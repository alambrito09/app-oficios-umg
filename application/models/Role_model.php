<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {
        return $this->db->get('roles')->result();
    }

    public function get_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('roles')->row();
    }

    public function get_by_name($name) {
        $this->db->where('name', $name);
        return $this->db->get('roles')->row();
    }
}
