<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Provider extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || (int) $this->session->userdata('role_id') !== 2) {
            redirect('auth/login');
        }
        $this->load->model('service_request_model');
        $this->load->model('quote_model');
        $this->load->model('service_model');
        $this->load->model('provider_profile_model');
        $this->load->model('complaint_model');
        $this->load->library('form_validation');
    }

    public function dashboard() {
        $id = $this->session->userdata('user_id');
        $data['available_requests'] = $this->service_request_model->get_available_requests(NULL, $id);
        $data['my_requests'] = $this->service_request_model->get_by_provider($id);
        $data['quotes'] = $this->quote_model->get_by_provider($id);
        $data['services'] = $this->service_model->get_by_provider($id);
        $data['profile'] = $this->provider_profile_model->get_by_user($id);
        $this->load->view('provider/dashboard', $data);
    }

    public function requests() {
        $id = $this->session->userdata('user_id');
        $data['available'] = $this->service_request_model->get_available_requests(NULL, $id);
        $data['assigned'] = $this->service_request_model->get_by_provider($id);
        $this->load->view('provider/requests', $data);
    }

    public function profile() {
        $id = $this->session->userdata('user_id');
        $data['profile'] = $this->provider_profile_model->get_by_user($id);
        $data['trades'] = $this->provider_profile_model->get_trades($id);
        $this->load->model('availability_model');
        $data['availabilities'] = $this->availability_model->get_by_provider($id);
        $this->load->view('provider/profile', $data);
    }

    public function services() {
        $id = $this->session->userdata('user_id');
        $data['services'] = $this->service_model->get_by_provider($id);
        $this->load->view('provider/services', $data);
    }

    public function progress($id) {
        $service = $this->service_model->get_by_id($id);
        if (!$service || (int) $service->provider_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        $this->form_validation->set_rules('notes', 'Notas de avance', 'required');
        $this->form_validation->set_rules('status', 'Estado del servicio', 'required|in_list[programado,en_ejecucion,completado]');

        if ($this->form_validation->run() === FALSE) {
            $data['service'] = $service;
            $this->load->view('provider/update_progress', $data);
            return;
        }

        $next_status = $this->input->post('status');
        $allowed_transition = (
            $service->status === 'programado' && $next_status === 'en_ejecucion'
        ) || (
            $service->status === 'en_ejecucion' && in_array($next_status, array('en_ejecucion', 'completado'), TRUE)
        );
        if (!$allowed_transition) {
            $this->session->set_flashdata('error', 'La transición de estado solicitada no es válida.');
            redirect('provider/services');
            return;
        }

        $updated = $this->service_model->update_progress($id, $this->input->post('notes'), $this->session->userdata('user_id'));
        if ($updated && $next_status !== $service->status) {
            $updated = $this->service_model->update_status($id, $next_status, $this->session->userdata('user_id'), 'Estado actualizado por el prestador');
        }
        if (!$updated) {
            $this->session->set_flashdata('error', 'No se pudo actualizar el servicio.');
            redirect('provider/services');
            return;
        }

        $this->session->set_flashdata('success', 'Avance registrado.');
        redirect('provider/services');
    }

    public function set_availability() {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $days = $this->input->post('days');
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');
        $time_pattern = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';
        if (!is_array($days) || !preg_match($time_pattern, (string) $start_time)
            || !preg_match($time_pattern, (string) $end_time) || $start_time >= $end_time) {
            $this->session->set_flashdata('error', 'Seleccione días y un horario válido.');
            redirect('provider/profile');
            return;
        }

        $avail = array();
        foreach (array_unique($days) as $day) {
            if ($day === '') {
                continue;
            }
            if (filter_var($day, FILTER_VALIDATE_INT) === FALSE || (int) $day < 0 || (int) $day > 6) {
                $this->session->set_flashdata('error', 'Día de disponibilidad no válido.');
                redirect('provider/profile');
                return;
            }
            $avail[] = array(
                'day_of_week' => (int) $day,
                'start_time' => $start_time . ':00',
                'end_time' => $end_time . ':00',
                'is_available' => 1
            );
        }

        $this->load->model('availability_model');
        if (!$this->availability_model->save($this->session->userdata('user_id'), $avail)) {
            $this->session->set_flashdata('error', 'No se pudo guardar la disponibilidad.');
            redirect('provider/profile');
            return;
        }

        $this->load->model('provider_profile_model');
        $status = empty($avail) ? 'no_disponible' : 'disponible';
        if (!$this->provider_profile_model->update($this->session->userdata('user_id'), array('availability_status' => $status))) {
            $this->session->set_flashdata('error', 'Se guardaron los horarios, pero no se pudo actualizar el estado del perfil.');
            redirect('provider/profile');
            return;
        }

        $this->session->set_flashdata('success', 'Disponibilidad actualizada.');
        redirect('provider/profile');
    }
}
