<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || !in_array((int) $this->session->userdata('role_id'), array(3, 4), TRUE)) {
            redirect('auth/login');
        }
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('trade_model');
        $this->load->model('provider_profile_model');
        $this->load->model('complaint_model');
        $this->load->model('service_request_model');
    }

    public function dashboard() {
        if ((int) $this->session->userdata('role_id') === 4) {
            redirect('admin/complaints');
            return;
        }
        $this->_require_administrator();

        $data['total_clients'] = $this->user_model->get_all(1);
        $data['total_providers'] = $this->user_model->get_all(2);
        $data['pending_requests'] = $this->service_request_model->count_by_status('solicitado');
        $data['pending_quotes'] = 0;
        $data['open_complaints'] = $this->complaint_model->count_by_status('abierto');
        $this->load->view('admin/dashboard', $data);
    }

    public function users() {
        $this->_require_administrator();
        $this->_load_users_page();
    }

    public function create_user() {
        $this->_require_administrator();
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $this->form_validation->set_rules('full_name', 'Nombre completo', 'required|max_length[150]');
        $this->form_validation->set_rules('email', 'Correo', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('username', 'Usuario', 'required|max_length[80]|is_unique[users.username]');
        $this->form_validation->set_rules('phone', 'Teléfono', 'max_length[30]');
        $this->form_validation->set_rules('role_id', 'Rol', 'required|integer');
        $this->form_validation->set_rules('password', 'Contraseña temporal', 'required|min_length[8]');

        if ($this->form_validation->run() === FALSE) {
            $this->_load_users_page();
            return;
        }

        $role = $this->role_model->get_by_id((int) $this->input->post('role_id'));
        if (!$role || !in_array($role->name, array('cliente', 'prestador'), TRUE)) {
            $this->session->set_flashdata('error', 'Solo se pueden crear cuentas de cliente o prestador.');
            redirect('admin/users');
            return;
        }

        $this->db->trans_begin();
        $created = $this->user_model->create(array(
            'username' => trim($this->input->post('username')),
            'email' => trim($this->input->post('email')),
            'password_hash' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'full_name' => trim($this->input->post('full_name')),
            'phone' => trim((string) $this->input->post('phone')) ?: NULL,
            'role_id' => (int) $role->id,
            'status' => 'activo'
        ));

        if ($created && $role->name === 'prestador') {
            $created = $this->provider_profile_model->create(array(
                'user_id' => $this->db->insert_id()
            ));
        }

        if (!$created || !$this->db->trans_status()) {
            $this->db->trans_rollback();
            log_message('error', 'No se pudo crear una cuenta desde el panel administrativo.');
            $this->session->set_flashdata('error', 'No se pudo crear el usuario. Revise que el correo y el nombre de usuario no estén registrados.');
            redirect('admin/users');
            return;
        }

        $this->db->trans_commit();
        $this->session->set_flashdata('success', 'Usuario creado y activado. Comparta la contraseña temporal de forma segura.');
        redirect('admin/users');
    }

    public function update_user_status($id) {
        $this->_require_administrator();
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $this->form_validation->set_rules('status', 'Estado', 'required|in_list[activo,inactivo,suspendido]');
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'El estado solicitado no es válido.');
            redirect('admin/users');
            return;
        }

        $user = $this->user_model->get_by_id($id);
        if (!$user) {
            show_404();
        }

        if ((int) $user->id === (int) $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'No puede cambiar el estado de su propia cuenta.');
            redirect('admin/users');
            return;
        }

        $new_status = $this->input->post('status');
        if ((int) $user->role_id === 3 && $user->status === 'activo' && $new_status !== 'activo') {
            $other_active_admins = $this->db->where('role_id', 3)
                ->where('status', 'activo')
                ->where('id !=', $user->id)
                ->count_all_results('users');
            if ($other_active_admins === 0) {
                $this->session->set_flashdata('error', 'Debe quedar al menos una cuenta administradora activa.');
                redirect('admin/users');
                return;
            }
        }

        $changes = array('status' => $new_status);
        if ($new_status !== 'activo') {
            $changes['reset_token'] = NULL;
            $changes['reset_expires'] = NULL;
        }

        if (!$this->user_model->update($user->id, $changes)) {
            log_message('error', 'No se pudo cambiar el estado del usuario ID ' . (int) $user->id . '.');
            $this->session->set_flashdata('error', 'No se pudo actualizar el estado del usuario.');
            redirect('admin/users');
            return;
        }

        if ((int) $user->role_id === 2 && $new_status !== 'activo') {
            $this->provider_profile_model->update($user->id, array('availability_status' => 'no_disponible'));
        }

        $this->session->set_flashdata('success', $new_status === 'activo'
            ? 'Cuenta activada. El prestador debe habilitar su disponibilidad nuevamente.'
            : 'Cuenta desactivada; el historial y los servicios asociados se conservaron.');
        redirect('admin/users');
    }

    public function trades() {
        $this->_require_administrator();
        $data['trades'] = $this->trade_model->get_all();
        $this->load->view('admin/trades', $data);
    }

    public function complaints() {
        if (!in_array((int) $this->session->userdata('role_id'), array(3, 4), TRUE)) {
            show_error('No autorizado', 403);
        }
        $data['complaints'] = $this->complaint_model->get_all();
        $this->load->view('admin/complaints', $data);
    }

    public function verify_provider($id) {
        $this->_require_administrator();

        $this->load->model('user_model');
        $user = $this->user_model->get_by_id($id);
        if (!$user || (int) $user->role_id !== 2) {
            show_404();
        }

        $this->form_validation->set_rules('verified', 'Estado de verificación', 'required|in_list[1,0]');
        $this->form_validation->set_rules('notes', 'Notas de verificación', '');

        if ($this->form_validation->run() === FALSE) {
            $this->load->model('provider_profile_model');
            $data['user'] = $user;
            $data['profile'] = $this->provider_profile_model->get_by_user($id);
            $this->load->view('admin/verify_provider', $data);
            return;
        }

        $this->provider_profile_model->set_verified(
            $id,
            $this->input->post('verified') === '1',
            $this->input->post('notes'),
            $this->input->post('verified') === '1' ? 'verificado' : 'rechazado'
        );

        $this->session->set_flashdata('success', 'Estado de verificación actualizado.');
        redirect('admin/users');
    }

    public function reports() {
        $this->_require_administrator();
        $data['requests_by_status'] = $this->db->select('status, COUNT(*) as total')
            ->group_by('status')->get('service_requests')->result();
        $data['complaints_by_status'] = $this->db->select('status, COUNT(*) as total')
            ->group_by('status')->get('complaints')->result();
        $data['top_providers'] = $this->db->select('provider_id, COUNT(*) as total_services')
            ->group_by('provider_id')->order_by('total_services', 'DESC')->limit(5)
            ->get('services')->result();
        $this->load->view('admin/reports', $data);
    }

    private function _require_administrator() {
        if ((int) $this->session->userdata('role_id') !== 3) {
            show_error('Solo administrador puede realizar esta acción', 403);
        }
    }

    private function _load_users_page() {
        $data['users'] = $this->user_model->get_all();
        $data['roles'] = array_values(array_filter($this->role_model->get_all(), function ($role) {
            return in_array($role->name, array('cliente', 'prestador'), TRUE);
        }));
        $this->load->view('admin/users', $data);
    }
}
