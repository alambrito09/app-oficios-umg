<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trade_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_active() {
        $this->db->where('is_active', 1);
        return $this->db->get('trades')->result();
    }

    public function get_all() {
        return $this->db->get('trades')->result();
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('trades')->row();
    }

    public function create($data) {
        return $this->db->insert('trades', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('trades', $data);
    }
}
