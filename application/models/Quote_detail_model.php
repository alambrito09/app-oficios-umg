<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quote_detail_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add($quote_id, $item_type, $description, $quantity, $unit_price) {
        $total = $quantity * $unit_price;
        return $this->db->insert('quote_details', array(
            'quote_id' => $quote_id,
            'item_type' => $item_type,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $total
        ));
    }

    public function get_by_quote($quote_id) {
        $this->db->where('quote_id', $quote_id);
        return $this->db->get('quote_details')->result();
    }

    public function delete_by_quote($quote_id) {
        $this->db->where('quote_id', $quote_id);
        return $this->db->delete('quote_details');
    }

    public function get_totals_by_type($quote_id) {
        $this->db->select('item_type, SUM(total_price) as total');
        $this->db->where('quote_id', $quote_id);
        $this->db->group_by('item_type');
        return $this->db->get('quote_details')->result();
    }
}
