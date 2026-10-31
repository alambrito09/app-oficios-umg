<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Specialty_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_by_trade($trade_id) {
        $this->db->where('trade_id', $trade_id);
        $this->db->where('is_active', 1);
        return $this->db->get('specialties')->result();
    }

    public function get_all() {
        $this->db->join('trades t', 't.id = specialties.trade_id');
        return $this->db->get('specialties')->result();
    }
}
