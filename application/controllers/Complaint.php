<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('complaint_model');
        $this->load->model('service_request_model');
        $this->load->library('form_validation');
    }

    public function client_list() {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes', 403);
        }
        $data['complaints'] = $this->complaint_model->get_by_client($this->session->userdata('user_id'));
        $this->load->view('client/complaints', $data);
    }

    public function provider_list() {
        if ((int) $this->session->userdata('role_id') !== 2) {
            show_error('Solo proveedores', 403);
        }
        $data['complaints'] = $this->complaint_model->get_by_provider($this->session->userdata('user_id'));
        $this->load->view('provider/complaints', $data);
    }

    public function create($request_id) {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes', 403);
        }

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

    public function resolve($id) {
        $role_id = (int) $this->session->userdata('role_id');
        if (!in_array($role_id, array(2, 3, 4), TRUE)) {
            show_error('No autorizado', 403);
        }

        $complaint = $this->complaint_model->get_by_id($id);
        if (!$complaint) {
            show_404();
        }
        if ($role_id === 2 && (int) $complaint->provider_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado para resolver este reclamo', 403);
        }
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $this->form_validation->set_rules('status', 'Estado', 'required|in_list[resuelto,cerrado]');
        $this->form_validation->set_rules('resolution', 'Resolución', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['complaint'] = $complaint;
            $this->load->view('admin/resolve_complaint', $data);
            return;
        }

        $this->complaint_model->update_status(
            $id,
            $this->input->post('status'),
            $this->session->userdata('user_id'),
            $this->input->post('resolution')
        );

        $this->session->set_flashdata('success', 'Reclamo actualizado.');
        redirect('admin/complaints');
    }
}
