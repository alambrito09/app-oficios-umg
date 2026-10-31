<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller {

    private $payment_methods = array(
        'efectivo_simulado' => 'Efectivo (demostración)',
        'transferencia_simulada' => 'Transferencia (demostración)',
        'tarjeta_simulada' => 'Tarjeta (demostración)'
    );

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id') || (int) $this->session->userdata('role_id') !== 1) {
            show_error('Solo clientes pueden registrar pagos', 403);
        }
        $this->load->model('service_model');
        $this->load->model('payment_model');
        $this->load->library('form_validation');
    }

    public function checkout($service_id) {
        $service = $this->service_model->get_by_id($service_id);
        if (!$service || (int) $service->client_id !== (int) $this->session->userdata('user_id')) {
            show_error('No autorizado', 403);
        }

        $payment = $this->payment_model->get_latest_for_service($service_id);
        if ($payment && $payment->status === 'completado') {
            $this->session->set_flashdata('info', 'Este servicio ya tiene un pago registrado.');
            redirect('service/view/' . (int) $service_id);
            return;
        }

        if ($this->input->method(TRUE) === 'POST') {
            $allowed_methods = implode(',', array_keys($this->payment_methods));
            $this->form_validation->set_rules('payment_method', 'Método de pago', 'required|in_list[' . $allowed_methods . ']');

            if ($this->form_validation->run() !== FALSE) {
                $result = $this->payment_model->record_simulated(
                    (int) $service_id,
                    (int) $this->session->userdata('user_id'),
                    $this->input->post('payment_method')
                );

                if ($result === 'paid') {
                    $this->session->set_flashdata('success', 'Pago simulado registrado. No se realizó ningún cobro real.');
                    redirect('service/view/' . (int) $service_id);
                    return;
                }
                if ($result === 'already_paid') {
                    $this->session->set_flashdata('info', 'Este servicio ya tiene un pago registrado.');
                    redirect('service/view/' . (int) $service_id);
                    return;
                }
                if ($result === 'not_found' || $result === 'not_owner') {
                    show_error('No autorizado', 403);
                }
                if ($result === 'invalid_amount') {
                    $this->session->set_flashdata('error', 'No se puede registrar un pago con un total inválido.');
                    redirect('service/view/' . (int) $service_id);
                    return;
                }

                log_message('error', 'No se pudo registrar el pago simulado del servicio ID ' . (int) $service_id . '.');
                $this->session->set_flashdata('error', 'No se pudo registrar el pago simulado. Intente nuevamente.');
                redirect('service/view/' . (int) $service_id);
                return;
            }
        }

        $data['service'] = $service;
        $data['payment_methods'] = $this->payment_methods;
        $this->load->view('client/payment', $data);
    }
}
