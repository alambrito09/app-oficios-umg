<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quote extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        $this->load->model('quote_model');
        $this->load->model('quote_detail_model');
        $this->load->model('service_request_model');
        $this->load->model('service_model');
        $this->load->library('form_validation');
    }

    public function create($request_id) {
        if ((int) $this->session->userdata('role_id') !== 2) {
            show_error('Solo proveedores pueden crear cotizaciones', 403);
        }

        $request = $this->service_request_model->get_by_id($request_id);
        if (!$request || (int) $request->assigned_provider_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado para cotizar esta solicitud', 403);
        }

        $existing = $this->quote_model->get_by_request($request_id);
        if ($existing) {
            redirect('quote/edit/' . $existing->id);
        }

        $this->form_validation->set_rules('labor_cost', 'Mano de obra', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('materials_cost', 'Materiales', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('other_charges', 'Cargos adicionales', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('visit_cost', 'Costo de visita', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('valid_until', 'Válido hasta', '');

        if ($this->form_validation->run() === FALSE) {
            $data['request'] = $request;
            $this->load->view('provider/create_quote', $data);
            return;
        }

        $labor = (float) $this->input->post('labor_cost');
        $materials = (float) $this->input->post('materials_cost');
        $other = (float) $this->input->post('other_charges');
        $visit = (float) $this->input->post('visit_cost');
        $total = $labor + $materials + $other + $visit;

        $quote_id = $this->quote_model->create(array(
            'request_id' => $request_id,
            'provider_id' => $this->session->userdata('user_id'),
            'labor_cost' => $labor,
            'materials_cost' => $materials,
            'other_charges' => $other,
            'visit_cost' => $visit,
            'total_amount' => $total,
            'status' => 'enviado',
            'valid_until' => $this->input->post('valid_until') ?: NULL
        ));

        // Detalle de cotización (mano de obra, materiales, cargos separados)
        $this->quote_detail_model->add($quote_id, 'mano_obra', 'Mano de obra', 1, $labor);
        $this->quote_detail_model->add($quote_id, 'material', 'Materiales', 1, $materials);
        $this->quote_detail_model->add($quote_id, 'cargo_adicional', 'Cargos adicionales', 1, $other);
        $this->quote_detail_model->add($quote_id, 'cargo_adicional', 'Costo de visita', 1, $visit);

        // Actualizar estado de solicitud
        $this->service_request_model->update_status($request_id, 'pendiente_aprobacion', $this->session->userdata('user_id'), 'Cotización enviada');

        $this->session->set_flashdata('success', 'Cotización preparada y enviada.');
        redirect('provider/dashboard');
    }

    public function edit($id) {
        if ((int) $this->session->userdata('role_id') !== 2) {
            show_error('Solo proveedores pueden editar cotizaciones', 403);
        }

        $quote = $this->quote_model->get_by_id($id);
        if (!$quote || (int) $quote->provider_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        if ($quote->status !== 'borrador' && $quote->status !== 'enviado') {
            $this->session->set_flashdata('error', 'No se puede editar una cotización ya procesada.');
            redirect('provider/dashboard');
        }

        $this->form_validation->set_rules('labor_cost', 'Mano de obra', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('materials_cost', 'Materiales', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('other_charges', 'Cargos adicionales', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('visit_cost', 'Costo de visita', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $data['quote'] = $quote;
            $data['details'] = $this->quote_detail_model->get_by_quote($id);
            $this->load->view('provider/edit_quote', $data);
            return;
        }

        $labor = (float) $this->input->post('labor_cost');
        $materials = (float) $this->input->post('materials_cost');
        $other = (float) $this->input->post('other_charges');
        $visit = (float) $this->input->post('visit_cost');

        $this->quote_model->update($id, array(
            'labor_cost' => $labor,
            'materials_cost' => $materials,
            'other_charges' => $other,
            'visit_cost' => $visit,
            'total_amount' => $labor + $materials + $other + $visit,
            'status' => 'enviado'
        ));

        $this->quote_detail_model->delete_by_quote($id);
        $this->quote_detail_model->add($id, 'mano_obra', 'Mano de obra', 1, $labor);
        $this->quote_detail_model->add($id, 'material', 'Materiales', 1, $materials);
        $this->quote_detail_model->add($id, 'cargo_adicional', 'Cargos adicionales', 1, $other);
        $this->quote_detail_model->add($id, 'cargo_adicional', 'Costo de visita', 1, $visit);

        $this->session->set_flashdata('success', 'Cotización actualizada.');
        redirect('provider/dashboard');
    }

    public function approve($id) {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes pueden aprobar cotizaciones', 403);
        }
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $quote = $this->quote_model->get_by_id($id);
        if (!$quote) {
            show_404();
        }

        $request = $this->service_request_model->get_by_id($quote->request_id);
        if (!$request || (int) $request->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        if ($quote->status !== 'enviado') {
            $this->session->set_flashdata('error', 'Esta cotización ya no está disponible para aprobación.');
            redirect('client/history');
        }

        $this->quote_model->update_status($id, 'aprobado', $this->session->userdata('user_id'), 'Aprobada por el cliente');
        $this->service_request_model->update_status($quote->request_id, 'confirmado', $this->session->userdata('user_id'), 'Cotización aprobada');

        // Crear servicio
        $this->load->model('service_model');
        $service_id = $this->service_model->create_from_quote($id);

        $this->session->set_flashdata('success', 'Cotización aprobada. Servicio creado.');
        redirect('client/request/' . $quote->request_id);
    }

    public function reject($id) {
        if ((int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes pueden rechazar cotizaciones', 403);
        }
        if ($this->input->method(TRUE) !== 'POST') {
            show_error('Método no permitido', 405);
        }

        $quote = $this->quote_model->get_by_id($id);
        if (!$quote) {
            show_404();
        }

        $request = $this->service_request_model->get_by_id($quote->request_id);
        if (!$request || (int) $request->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        $this->quote_model->update_status($id, 'rechazado', $this->session->userdata('user_id'), 'Rechazada por el cliente');
        $this->service_request_model->update_status($quote->request_id, 'cancelado', $this->session->userdata('user_id'), 'Cotización rechazada');

        $this->session->set_flashdata('info', 'Cotización rechazada.');
        redirect('client/history');
    }
}
