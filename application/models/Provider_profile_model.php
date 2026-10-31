<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Provider_profile_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_by_user($user_id) {
        $this->db->where('user_id', $user_id);
        return $this->db->get('provider_profiles')->row();
    }

    public function get_all_available() {
        $this->db->select('pp.*, u.full_name, u.username, u.email, u.phone');
        $this->db->from('provider_profiles pp');
        $this->db->join('users u', 'u.id = pp.user_id');
        $this->db->where('u.role_id', 2); // prestador
        $this->db->where('u.status', 'activo');
        $this->db->where('pp.availability_status', 'disponible');
        return $this->db->get()->result();
    }

    public function search($trade_id, $location = NULL) {
        $this->db->select('pp.*, u.id as provider_id, u.full_name, u.phone, pt.trade_id, t.name as trade_name');
        $this->db->from('provider_profiles pp');
        $this->db->join('users u', 'u.id = pp.user_id');
        $this->db->join('provider_trades pt', 'pt.provider_id = u.id');
        $this->db->join('trades t', 't.id = pt.trade_id');
        $this->db->where('u.role_id', 2);
        $this->db->where('u.status', 'activo');
        $this->db->where('pp.availability_status', 'disponible');
        $this->db->where('pt.trade_id', $trade_id);
        if ($location) {
            $this->db->like('pp.coverage_area', $location);
        }
        $this->db->order_by('pp.is_verified', 'DESC');

        return $this->db->get()->result();
    }

    public function create($data) {
        return $this->db->insert('provider_profiles', $data);
    }

    public function update($user_id, $data) {
        $this->db->where('user_id', $user_id);
        return $this->db->update('provider_profiles', $data);
    }

    public function set_verified($user_id, $verified = TRUE, $notes = NULL, $status = 'verificado') {
        $data = array(
            'is_verified' => $verified ? 1 : 0,
            'verification_status' => $status,
            'verification_notes' => $notes
        );
        $this->db->where('user_id', $user_id);
        return $this->db->update('provider_profiles', $data);
    }

    public function get_trades($provider_id) {
        $this->db->select('t.id, t.name, pt.is_primary');
        $this->db->from('provider_trades pt');
        $this->db->join('trades t', 't.id = pt.trade_id');
        $this->db->where('pt.provider_id', $provider_id);
        return $this->db->get()->result();
    }

    public function add_trade($provider_id, $trade_id, $is_primary = 0) {
        return $this->db->insert('provider_trades', array(
            'provider_id' => $provider_id,
            'trade_id' => $trade_id,
            'is_primary' => $is_primary
        ));
    }

    public function sync_trades($provider_id, $trade_ids) {
        $valid_ids = array();
        foreach ((array) $trade_ids as $trade_id) {
            if (filter_var($trade_id, FILTER_VALIDATE_INT) === FALSE || (int) $trade_id < 1) {
                continue;
            }

            $trade = $this->db->get_where('trades', array(
                'id' => (int) $trade_id,
                'is_active' => 1
            ))->row();
            if ($trade) {
                $valid_ids[] = (int) $trade_id;
            }
        }

        $this->db->trans_start();
        $this->db->delete('provider_trades', array('provider_id' => $provider_id));
        foreach (array_values(array_unique($valid_ids)) as $index => $trade_id) {
            $this->add_trade($provider_id, $trade_id, $index === 0 ? 1 : 0);
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
