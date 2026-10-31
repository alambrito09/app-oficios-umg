<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaint_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $this->db->insert('complaints', $data);
        return $this->db->insert_id();
    }

    public function get_by_id($id) {
        $this->db->select('c.*, u.full_name as client_name, p.full_name as provider_name');
        $this->db->from('complaints c');
        $this->db->join('users u', 'u.id = c.client_id');
        $this->db->join('users p', 'p.id = c.provider_id', 'left');
        $this->db->where('c.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_client($client_id) {
        $this->db->where('client_id', $client_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('complaints')->result();
    }

    public function get_by_provider($provider_id) {
        $this->db->where('provider_id', $provider_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('complaints')->result();
    }

    public function get_all() {
        $this->db->select('c.*, u.full_name as client_name, p.full_name as provider_name');
        $this->db->from('complaints c');
        $this->db->join('users u', 'u.id = c.client_id');
        $this->db->join('users p', 'p.id = c.provider_id', 'left');
        $this->db->order_by('c.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function update_status($id, $status, $resolved_by, $resolution = NULL) {
        $data = array('status' => $status, 'updated_at' => date('Y-m-d H:i:s'));
        if ($status === 'resuelto' || $status === 'cerrado') {
            $data['resolved_at'] = date('Y-m-d H:i:s');
            $data['resolved_by'] = $resolved_by;
            if ($resolution) $data['resolution'] = $resolution;
        }
        $this->db->where('id', $id);
        return $this->db->update('complaints', $data);
    }

    public function count_by_status($status) {
        $this->db->where('status', $status);
        return $this->db->count_all_results('complaints');
    }
}
