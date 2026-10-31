<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceRequest extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('service_request_model');
        $this->load->model('trade_model');
        $this->load->model('specialty_model');
        $this->load->model('availability_model');
        $this->load->library('form_validation');
    }

    public function create() {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes pueden crear solicitudes', 403);
        }

        $this->form_validation->set_rules('trade_id', 'Oficio', 'required|numeric');
        $this->form_validation->set_rules('specialty_id', 'Especialidad', 'numeric');
        $this->form_validation->set_rules('title', 'Título', 'required');
        $this->form_validation->set_rules('description', 'Descripción', 'required');
        $this->form_validation->set_rules('location', 'Ubicación', 'required');
        $this->form_validation->set_rules('modality', 'Modalidad', 'required|in_list[inmediata,programada,diagnostico,por_presupuesto]');
        $this->form_validation->set_rules('scheduled_date', 'Fecha programada', '');

        if ($this->form_validation->run() === FALSE) {
            $data['trades'] = $this->trade_model->get_all_active();
            $data['selected_trade_id'] = (int) $this->input->get('trade_id');
            $this->load->view('client/create_request', $data);
            return;
        }

        $expires = NULL;
        if ($this->input->post('modality') !== 'inmediata') {
            $expires = date('Y-m-d H:i:s', strtotime('+48 hours'));
        }

        $scheduled_date = $this->input->post('scheduled_date');
        if ($scheduled_date) {
            $scheduled_date = str_replace('T', ' ', $scheduled_date);
            if (strlen($scheduled_date) === 16) {
                $scheduled_date .= ':00';
            }
        }

        $data = array(
            'client_id' => $this->session->userdata('user_id'),
            'trade_id' => $this->input->post('trade_id'),
            'specialty_id' => $this->input->post('specialty_id') ?: NULL,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'location' => $this->input->post('location'),
            'service_modality' => $this->input->post('modality'),
            'scheduled_date' => $scheduled_date ?: NULL,
            'status' => 'solicitado',
            'photos' => json_encode($this->input->post('photos') ?: array()),
            'expires_at' => $expires
        );

        $request_id = $this->service_request_model->create($data);
        $this->session->set_flashdata('success', 'Solicitud creada exitosamente.');
        redirect('client/request/' . $request_id);
    }

    public function view($id) {
        $request = $this->service_request_model->get_by_id($id);
        if (!$request) {
            show_404();
        }

        // Solo el cliente dueño o el proveedor asignado pueden ver
        $user_id = $this->session->userdata('user_id');
        if ((int) $request->client_id !== (int) $user_id && (int) $request->assigned_provider_id !== (int) $user_id) {
            show_error('No autorizado para ver esta solicitud', 403);
        }

        $this->load->model('quote_model');
        $data['request'] = $request;
        $data['quote'] = $this->quote_model->get_by_request($id);
        $this->load->view('client/view_request', $data);
    }

    public function respond($id) {
        if ((int) $this->session->userdata('role_id') !== 2) {
            show_error('Solo proveedores pueden responder solicitudes', 403);
        }

        $request = $this->service_request_model->get_by_id($id);
        if (!$request) {
            show_404();
        }

        if ($request->status !== 'solicitado' || $request->assigned_provider_id !== NULL) {
            $this->session->set_flashdata('error', 'Esta solicitud ya no está disponible.');
            redirect('provider/requests');
            return;
        }

        $provider_trade = $this->db->get_where('provider_trades', array(
            'provider_id' => $this->session->userdata('user_id'),
            'trade_id' => $request->trade_id
        ))->row();
        if (!$provider_trade) {
            show_error('No está habilitado para responder solicitudes de este oficio', 403);
        }

        $this->form_validation->set_rules('action', 'Acción', 'required|in_list[accept,reject]');

        if ($this->form_validation->run() === FALSE) {
            $data['request'] = $request;
            $this->load->view('provider/respond_request', $data);
            return;
        }

        $action = $this->input->post('action');
        if ($action === 'accept') {
            // Regla de doble aceptación: un solo proveedor por solicitud
            if (!$this->service_request_model->assign_provider($id, $this->session->userdata('user_id'), $this->session->userdata('user_id'))) {
                $this->session->set_flashdata('error', 'Otro proveedor aceptó la solicitud o ya venció.');
                redirect('provider/requests');
                return;
            }
            $this->session->set_flashdata('success', 'Solicitud aceptada. Ahora puede preparar una cotización.');
            redirect('provider/dashboard');
        } else {
            $this->session->set_flashdata('info', 'Solicitud descartada para usted; continúa disponible para otros prestadores.');
            redirect('provider/requests');
        }
    }

    public function search() {
        $trade_id = $this->input->get('trade_id');
        $location = $this->input->get('location');
        $modality = $this->input->get('modality');

        $data['trades'] = $this->trade_model->get_all_active();
        $data['results'] = array();

        if ($trade_id) {
            $data['results'] = $this->service_request_model->search($trade_id, $location, $modality);
        }

        $this->load->view('client/search_results', $data);
    }
}
