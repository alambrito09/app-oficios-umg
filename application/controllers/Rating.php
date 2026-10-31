<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rating extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('rating_model');
        $this->load->model('service_model');
        $this->load->library('form_validation');
    }

    public function create($service_id) {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes pueden calificar', 403);
        }

        $service = $this->service_model->get_by_id($service_id);
        if (!$service || (int) $service->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        if (!in_array($service->status, array('completado', 'cerrado'))) {
            $this->session->set_flashdata('error', 'Solo puede calificar servicios realizados.');
            redirect('client/history');
        }

        $existing = $this->rating_model->get_by_service($service_id);
        if ($existing) {
            $this->session->set_flashdata('error', 'Ya calificó este servicio.');
            redirect('client/history');
        }

        $this->form_validation->set_rules('score', 'Puntuación', 'required|integer|greater_than[0]|less_than[6]');
        $this->form_validation->set_rules('comment', 'Comentario', '');

        if ($this->form_validation->run() === FALSE) {
            $data['service'] = $service;
            $this->load->view('client/rate_service', $data);
            return;
        }

        $this->rating_model->create(
            $service_id,
            $this->session->userdata('user_id'),
            $service->provider_id,
            $this->input->post('score'),
            $this->input->post('comment')
        );

        $this->session->set_flashdata('success', 'Gracias por calificar el servicio.');
        redirect('client/history');
    }
}
