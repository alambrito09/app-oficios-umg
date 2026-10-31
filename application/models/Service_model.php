<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create_from_quote($quote_id) {
        $quote = $this->db->get_where('quotes', array('id' => $quote_id))->row();
        if (!$quote || $quote->status !== 'aprobado') return FALSE;

        $data = array(
            'quote_id' => $quote_id,
            'client_id' => $this->db->get_where('service_requests', array('id' => $quote->request_id))->row()->client_id,
            'provider_id' => $quote->provider_id,
            'status' => 'programado'
        );
        $this->db->insert('services', $data);
        $service_id = $this->db->insert_id();

        $this->log_state_transition('service', $service_id, NULL, 'programado', $quote->provider_id, 'Servicio creado desde cotización aprobada');
        return $service_id;
    }

    public function get_by_id($id) {
        $this->db->select('s.*, q.total_amount, sr.title as request_title, u.full_name as client_name');
        $this->db->from('services s');
        $this->db->join('quotes q', 'q.id = s.quote_id');
        $this->db->join('service_requests sr', 'sr.id = q.request_id');
        $this->db->join('users u', 'u.id = s.client_id');
        $this->db->where('s.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_client($client_id) {
        $this->db->select(
            's.*, q.total_amount, t.name as trade_name, sr.title as request_title, ' .
            '(SELECT p.status FROM payments p WHERE p.service_id = s.id AND p.status = "completado" ORDER BY p.created_at DESC LIMIT 1) as payment_status',
            FALSE
        );
        $this->db->from('services s');
        $this->db->join('quotes q', 'q.id = s.quote_id');
        $this->db->join('service_requests sr', 'sr.id = q.request_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->where('s.client_id', $client_id);
        $this->db->order_by('s.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_provider($provider_id) {
        $this->db->select('s.*, q.total_amount, u.full_name as client_name, t.name as trade_name, sr.title as request_title');
        $this->db->from('services s');
        $this->db->join('quotes q', 'q.id = s.quote_id');
        $this->db->join('users u', 'u.id = s.client_id');
        $this->db->join('service_requests sr', 'sr.id = q.request_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->where('s.provider_id', $provider_id);
        $this->db->order_by('s.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function update_status($id, $status, $changed_by, $reason = NULL) {
        $service = $this->get_by_id($id);
        if (!$service) return FALSE;

        $old_state = $service->status;
        $data = array('status' => $status, 'updated_at' => date('Y-m-d H:i:s'));

        if ($status === 'en_ejecucion') {
            $data['started_at'] = date('Y-m-d H:i:s');
        }
        if ($status === 'completado') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }
        if ($status === 'cerrado') {
            $data['closed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->where('id', $id);
        $this->db->update('services', $data);

        $this->log_state_transition('service', $id, $old_state, $status, $changed_by, $reason);
        return TRUE;
    }

    public function update_progress($id, $notes, $changed_by) {
        $this->db->where('id', $id);
        return $this->db->update('services', array('progress_notes' => $notes));
    }

    private function log_state_transition($entity_type, $entity_id, $old_state, $new_state, $changed_by, $reason = NULL) {
        $this->db->insert('state_histories', array(
            'entity_type' => $entity_type,
            'entity_id' => $entity_id,
            'old_state' => $old_state,
            'new_state' => $new_state,
            'changed_by' => $changed_by,
            'reason' => $reason
        ));
    }
}
