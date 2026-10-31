<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quote_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $this->db->insert('quotes', $data);
        return $this->db->insert_id();
    }

    public function get_by_id($id) {
        $this->db->select('q.*, u.full_name as provider_name, sr.title as request_title');
        $this->db->from('quotes q');
        $this->db->join('users u', 'u.id = q.provider_id');
        $this->db->join('service_requests sr', 'sr.id = q.request_id');
        $this->db->where('q.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_request($request_id) {
        $this->db->where('request_id', $request_id);
        return $this->db->get('quotes')->row();
    }

    public function get_by_provider($provider_id) {
        $this->db->select('q.*, sr.title as request_title, u.full_name as client_name');
        $this->db->from('quotes q');
        $this->db->join('service_requests sr', 'sr.id = q.request_id');
        $this->db->join('users u', 'u.id = sr.client_id');
        $this->db->where('q.provider_id', $provider_id);
        $this->db->order_by('q.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('quotes', $data);
    }

    public function update_status($id, $status, $changed_by, $reason = NULL) {
        $quote = $this->get_by_id($id);
        if (!$quote) return FALSE;

        $old_state = $quote->status;
        $data = array('status' => $status, 'updated_at' => date('Y-m-d H:i:s'));
        if ($status === 'aprobado') {
            $data['approved_at'] = date('Y-m-d H:i:s');
            $data['approved_by'] = $changed_by;
        }
        if ($status === 'rechazado') {
            $data['rejection_reason'] = $reason;
        }
        $this->db->where('id', $id);
        $this->db->update('quotes', $data);

        $this->log_state_transition('quote', $id, $old_state, $status, $changed_by, $reason);
        return TRUE;
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('quotes');
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
