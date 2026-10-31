<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || (int) $this->session->userdata('role_id') !== 1) {
            redirect('auth/login');
        }
        $this->load->model('service_request_model');
        $this->load->model('quote_model');
        $this->load->model('service_model');
        $this->load->model('rating_model');
        $this->load->model('complaint_model');
        $this->load->model('trade_model');
    }

    public function dashboard() {
        $id = $this->session->userdata('user_id');
        $data['requests'] = $this->service_request_model->get_by_client($id);
        $data['services'] = $this->service_model->get_by_client($id);
        $data['pending_quotes'] = array();
        foreach ($data['requests'] as $r) {
            $q = $this->quote_model->get_by_request($r->id);
            if ($q && $q->status === 'enviado') {
                $data['pending_quotes'][] = $q;
            }
        }
        $this->load->view('client/dashboard', $data);
    }

    public function search() {
        $data['trades'] = $this->trade_model->get_all_active();
        $this->load->view('client/search', $data);
    }

    public function results() {
        $trade_id = $this->input->get('trade_id');
        $location = $this->input->get('location');

        $data['trades'] = $this->trade_model->get_all_active();
        $data['results'] = array();

        if ($trade_id) {
            $this->load->model('provider_profile_model');
            $data['results'] = $this->provider_profile_model->search($trade_id, $location);
        }

        $this->load->view('client/search_results', $data);
    }

    public function profile($id) {
        $this->load->model('user_model');
        $this->load->model('provider_profile_model');
        $data['provider'] = $this->user_model->get_by_id($id);
        $data['profile'] = $this->provider_profile_model->get_by_user($id);
        if (!$data['provider'] || (int) $data['provider']->role_id !== 2 || !$data['profile']) {
            show_404();
        }
        $data['trades'] = $this->provider_profile_model->get_trades($id);
        $data['ratings'] = $this->rating_model->get_by_provider($id);
        $this->load->view('client/view_provider', $data);
    }

    public function history() {
        $id = $this->session->userdata('user_id');
        $data['requests'] = $this->service_request_model->get_by_client($id);
        $data['services'] = $this->service_model->get_by_client($id);
        $data['complaints'] = $this->complaint_model->get_by_client($id);
        $this->load->view('client/history', $data);
    }

    public function rate($service_id) {
        $this->load->model('service_model');
        $service = $this->service_model->get_by_id($service_id);
        if (!$service || (int) $service->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
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

    public function create_complaint($request_id) {
        $this->load->model('service_request_model');
        $request = $this->service_request_model->get_by_id($request_id);
        if (!$request || (int) $request->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        $this->form_validation->set_rules('title', 'Título', 'required');
        $this->form_validation->set_rules('description', 'Descripción', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['request'] = $request;
            $this->load->view('client/create_complaint', $data);
            return;
        }

        $this->complaint_model->create(array(
            'request_id' => $request_id,
            'client_id' => $this->session->userdata('user_id'),
            'provider_id' => $request->assigned_provider_id,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'evidence' => $this->input->post('evidence') ?: NULL,
            'status' => 'abierto'
        ));

        $this->session->set_flashdata('success', 'Reclamo registrado.');
        redirect('client/history');
    }
}
