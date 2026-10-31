<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('login_attempt_model');
        $this->load->library('form_validation');
    }

    public function login() {
        if ($this->session->userdata('user_id')) {
            $user = $this->user_model->get_by_id($this->session->userdata('user_id'));
            if ($user && $user->status === 'activo') {
                redirect($this->_dashboard_by_role($this->session->userdata('role_id')));
            }
            $this->session->sess_destroy();
        }

        $this->form_validation->set_rules('username', 'Usuario', 'required');
        $this->form_validation->set_rules('password', 'Contraseña', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login');
            return;
        }

        $attempt_key = hash('sha256', $this->input->ip_address());
        if ($this->login_attempt_model->is_locked($attempt_key)) {
            $this->session->set_flashdata('error', 'Demasiados intentos. Espere 15 minutos antes de volver a intentarlo.');
            redirect('auth/login');
        }

        $user = $this->user_model->get_by_username($this->input->post('username'));
        if ($user && password_verify($this->input->post('password'), $user->password_hash)) {
            $this->login_attempt_model->clear_attempts($attempt_key);
            if ($user->status !== 'activo') {
                $this->session->set_flashdata('error', 'Su cuenta está inactiva o suspendida.');
                $this->load->view('auth/login');
                return;
            }
            $this->session->set_userdata(array(
                'user_id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role_name,
                'logged_in' => TRUE
            ));
            $this->session->sess_regenerate(TRUE);
            redirect($this->_dashboard_by_role($user->role_id));
        } else {
            $this->login_attempt_model->record_failure($attempt_key);
            $this->session->set_flashdata('error', 'Usuario o contraseña incorrectos.');
            $this->load->view('auth/login');
        }
    }

    public function register() {
        $this->form_validation->set_rules('full_name', 'Nombre completo', 'required');
        $this->form_validation->set_rules('email', 'Correo', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('username', 'Usuario', 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Rol', 'required|in_list[cliente,prestador]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/register');
            return;
        }

        $role = $this->db->get_where('roles', array('name' => $this->input->post('role')))->row();
        if (!$role) {
            $this->session->set_flashdata('error', 'Rol no válido.');
            redirect('auth/register');
        }

        $data = array(
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'full_name' => $this->input->post('full_name'),
            'phone' => $this->input->post('phone'),
            'role_id' => $role->id,
            'status' => 'activo'
        );
        $this->user_model->create($data);

        $this->session->set_flashdata('success', 'Registro exitoso. Ya puede iniciar sesión.');
        redirect('auth/login');
    }

    public function recover() {
        $this->form_validation->set_rules('email', 'Correo', 'required|valid_email');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/recover');
            return;
        }

        $user = $this->user_model->get_by_email($this->input->post('email'));
        $smtp_host = trim((string) getenv('SMTP_HOST'));
        $smtp_from = trim((string) getenv('SMTP_FROM_EMAIL'));
        if ($user && $smtp_host !== '' && $smtp_from !== '') {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            if ($this->user_model->set_reset_token($user->email, hash('sha256', $token), $expires)) {
                $this->load->library('email');
                $email_config = array(
                    'protocol' => 'smtp',
                    'smtp_host' => $smtp_host,
                    'smtp_port' => (int) (getenv('SMTP_PORT') ?: 587),
                    'smtp_user' => getenv('SMTP_USER') ?: '',
                    'smtp_pass' => getenv('SMTP_PASSWORD') ?: '',
                    'smtp_crypto' => getenv('SMTP_CRYPTO') ?: 'tls',
                    'smtp_timeout' => 10,
                    'mailtype' => 'text',
                    'charset' => 'UTF-8',
                    'newline' => "\r\n",
                    'crlf' => "\r\n"
                );
                $this->email->initialize($email_config);
                $this->email->from($smtp_from, getenv('SMTP_FROM_NAME') ?: 'App Oficios');
                $this->email->to($user->email);
                $this->email->subject('Recuperación de cuenta — App Oficios');
                $this->email->message(
                    "Se solicitó restablecer la contraseña de su cuenta. Abra este enlace dentro de una hora:\n\n" .
                    site_url('auth/reset') . '#' . $token .
                    "\n\nSi usted no hizo esta solicitud, ignore este mensaje."
                );
                if (!$this->email->send()) {
                    $this->user_model->clear_reset_token($user->id);
                    log_message('error', 'No se pudo enviar el correo de recuperación para el usuario ID ' . (int) $user->id . '.');
                }
            } else {
                log_message('error', 'No se pudo guardar el token de recuperación para el usuario ID ' . (int) $user->id . '.');
            }
        }

        $this->session->set_flashdata('success', 'Si el correo corresponde a una cuenta y el servicio de correo está configurado, recibirá instrucciones para recuperar el acceso.');
        redirect('auth/login');
    }

    public function reset() {
        $this->output->set_header('Referrer-Policy: no-referrer');
        $token = $this->input->method(TRUE) === 'POST' ? $this->input->post('token') : NULL;
        if (!$token) {
            if ($this->input->method(TRUE) === 'POST') {
                $this->session->set_flashdata('error', 'El enlace de recuperación no es válido.');
                redirect('auth/login');
                return;
            }
            $this->load->view('auth/reset', array('token' => ''));
            return;
        }

        if ($this->input->method(TRUE) !== 'POST') {
            redirect('auth/login');
            return;
        }

        $user = $this->user_model->verify_reset_token($token);
        if (!$user) {
            $this->session->set_flashdata('error', 'Token inválido o expirado.');
            redirect('auth/login');
        }

        $this->form_validation->set_rules('password', 'Nueva contraseña', 'required|min_length[6]');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/reset', array('token' => $token));
            return;
        }

        $this->user_model->change_password($user->id, password_hash($this->input->post('password'), PASSWORD_DEFAULT));
        $this->user_model->clear_reset_token($user->id);
        $this->session->set_flashdata('success', 'Contraseña actualizada. Puede iniciar sesión.');
        redirect('auth/login');
    }

    public function logout() {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    private function _dashboard_by_role($role_id) {
        switch ($role_id) {
            case 1: return 'client/dashboard';
            case 2: return 'provider/dashboard';
            case 3: return 'admin/dashboard';
            case 4: return 'admin/dashboard';
            default: return 'auth/login';
        }
    }

    public function provider_profile_setup() {
        if (!$this->session->userdata('user_id') || (int) $this->session->userdata('role_id') !== 2) {
            redirect('auth/login');
        }

        $this->load->model('trade_model');
        $this->load->model('provider_profile_model');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('experience_years', 'Años de experiencia', 'numeric');
        $this->form_validation->set_rules('bio', 'Biografía', 'required');
        $this->form_validation->set_rules('coverage_area', 'Área de cobertura', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['trades'] = $this->trade_model->get_all_active();
            $data['profile'] = $this->provider_profile_model->get_by_user($this->session->userdata('user_id'));
            $data['selected_trade_ids'] = array_map(
                'intval',
                array_column($this->provider_profile_model->get_trades($this->session->userdata('user_id')), 'id')
            );
            $this->load->view('provider/profile_setup', $data);
            return;
        }

        $profile_data = array(
            'user_id' => $this->session->userdata('user_id'),
            'experience_years' => $this->input->post('experience_years'),
            'bio' => $this->input->post('bio'),
            'coverage_area' => $this->input->post('coverage_area'),
            'availability_status' => 'no_disponible'
        );
        $existing = $this->provider_profile_model->get_by_user($this->session->userdata('user_id'));
        if ($existing) {
            $this->provider_profile_model->update($this->session->userdata('user_id'), $profile_data);
        } else {
            $this->provider_profile_model->create($profile_data);
        }

        $this->provider_profile_model->sync_trades(
            $this->session->userdata('user_id'),
            $this->input->post('trade_ids') ?: array()
        );

        $this->session->set_flashdata('success', 'Perfil de proveedor guardado.');
        redirect('provider/dashboard');
    }
}
