<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_request_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $this->db->insert('service_requests', $data);
        return $this->db->insert_id();
    }

    public function get_by_id($id) {
        $this->db->select('sr.*, u.full_name as client_name, t.name as trade_name, s.name as specialty_name');
        $this->db->from('service_requests sr');
        $this->db->join('users u', 'u.id = sr.client_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->join('specialties s', 's.id = sr.specialty_id', 'left');
        $this->db->where('sr.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_client($client_id) {
        $this->db->select('sr.*, t.name as trade_name, p.full_name as provider_name');
        $this->db->from('service_requests sr');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->join('users p', 'p.id = sr.assigned_provider_id', 'left');
        $this->db->where('sr.client_id', $client_id);
        $this->db->order_by('sr.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_provider($provider_id) {
        $this->db->select('sr.*, u.full_name as client_name, t.name as trade_name');
        $this->db->from('service_requests sr');
        $this->db->join('users u', 'u.id = sr.client_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->where('sr.assigned_provider_id', $provider_id);
        $this->db->order_by('sr.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_available_requests($trade_id, $provider_id = NULL) {
        $this->db->select('sr.*, u.full_name as client_name, t.name as trade_name');
        $this->db->from('service_requests sr');
        $this->db->join('users u', 'u.id = sr.client_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        if ($provider_id !== NULL) {
            $this->db->join('provider_trades pt', 'pt.trade_id = sr.trade_id');
            $this->db->where('pt.provider_id', $provider_id);
        } elseif ($trade_id !== NULL) {
            $this->db->where('sr.trade_id', $trade_id);
        }
        $this->db->where('sr.status', 'solicitado');
        $this->db->where('sr.assigned_provider_id IS NULL');
        $this->db->where('(sr.expires_at IS NULL OR sr.expires_at > NOW())');
        $this->db->order_by('sr.created_at', 'ASC');
        return $this->db->get()->result();
    }

    public function update_status($id, $status, $changed_by, $reason = NULL) {
        $request = $this->get_by_id($id);
        if (!$request) return FALSE;

        $old_state = $request->status;
        $this->db->where('id', $id);
        $this->db->update('service_requests', array('status' => $status, 'updated_at' => date('Y-m-d H:i:s')));

        $this->log_state_transition('service_request', $id, $old_state, $status, $changed_by, $reason);
        return TRUE;
    }

    public function assign_provider($request_id, $provider_id, $changed_by) {
        $this->db->trans_start();
        $this->db->where('id', $request_id);
        $this->db->where('status', 'solicitado');
        $this->db->where('assigned_provider_id IS NULL', NULL, FALSE);
        $this->db->where('(expires_at IS NULL OR expires_at > NOW())', NULL, FALSE);
        $this->db->update('service_requests', array(
            'assigned_provider_id' => $provider_id,
            'status' => 'confirmado',
            'updated_at' => date('Y-m-d H:i:s')
        ));
        if ($this->db->affected_rows() !== 1) {
            $this->db->trans_rollback();
            return FALSE;
        }
        $this->log_state_transition('service_request', $request_id, 'solicitado', 'confirmado', $changed_by, 'Asignado a proveedor');
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function search($trade_id, $location = NULL, $modality = NULL) {
        $this->db->select('sr.*, u.full_name as client_name, t.name as trade_name');
        $this->db->from('service_requests sr');
        $this->db->join('users u', 'u.id = sr.client_id');
        $this->db->join('trades t', 't.id = sr.trade_id');
        $this->db->where('sr.trade_id', $trade_id);
        $this->db->where_in('sr.status', array('solicitado', 'en_evaluacion'));
        if ($location) {
            $this->db->like('sr.location', $location);
        }
        if ($modality) {
            $this->db->where('sr.service_modality', $modality);
        }
        $this->db->order_by('sr.created_at', 'DESC');
        return $this->db->get()->result();
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

    public function count_by_status($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results('service_requests');
    }
}
