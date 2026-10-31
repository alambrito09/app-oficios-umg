<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('service_model');
        $this->load->model('payment_model');
    }

    public function view($id) {
        $service = $this->service_model->get_by_id($id);
        if (!$service) {
            show_404();
        }

        $user_id = $this->session->userdata('user_id');
        if ((int) $service->client_id !== (int) $user_id && (int) $service->provider_id !== (int) $user_id) {
            show_error('No autorizado', 403);
        }

        $this->load->model('quote_model');
        $data['service'] = $service;
        $data['quote'] = $this->quote_model->get_by_id($service->quote_id);
        $data['payment'] = $this->payment_model->get_latest_for_service($id);
        $this->load->view('client/view_service', $data);
    }

    public function close($id) {
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $service = $this->service_model->get_by_id($id);
        if (!$service || (int) $service->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        if ($service->status !== 'completado') {
            $this->session->set_flashdata('error', 'Solo se pueden cerrar servicios completados.');
            redirect('client/history');
        }

        $this->service_model->update_status($id, 'cerrado', $this->session->userdata('user_id'), 'Cerrado por el cliente');
        $this->session->set_flashdata('success', 'Servicio cerrado.');
        redirect('client/history');
    }
}
