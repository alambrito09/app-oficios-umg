<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Availability_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_by_provider($provider_id) {
        $this->db->where('provider_id', $provider_id);
        return $this->db->get('availabilities')->result();
    }

    public function save($provider_id, $availabilities) {
        if (!is_array($availabilities)) {
            return FALSE;
        }

        foreach ($availabilities as $availability) {
            if (!isset($availability['day_of_week'], $availability['start_time'], $availability['end_time'])
                || (int) $availability['day_of_week'] < 0 || (int) $availability['day_of_week'] > 6
                || $availability['start_time'] >= $availability['end_time']) {
                return FALSE;
            }
        }

        $this->db->trans_start();
        $this->db->where('provider_id', $provider_id);
        $this->db->delete('availabilities');
        foreach ($availabilities as $a) {
            $this->db->insert('availabilities', array(
                'provider_id' => $provider_id,
                'day_of_week' => $a['day_of_week'],
                'start_time' => $a['start_time'],
                'end_time' => $a['end_time'],
                'is_available' => isset($a['is_available']) ? 1 : 0
            ));
        }
        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_available_providers($trade_id, $day_of_week, $time) {
        $this->db->select('u.id, u.full_name, pp.coverage_area, pp.is_verified');
        $this->db->from('availabilities a');
        $this->db->join('users u', 'u.id = a.provider_id');
        $this->db->join('provider_profiles pp', 'pp.user_id = u.id');
        $this->db->join('provider_trades pt', 'pt.provider_id = u.id');
        $this->db->where('u.role_id', 2);
        $this->db->where('u.status', 'activo');
        $this->db->where('a.day_of_week', $day_of_week);
        $this->db->where('a.start_time <=', $time);
        $this->db->where('a.end_time >=', $time);
        $this->db->where('a.is_available', 1);
        $this->db->where('pt.trade_id', $trade_id);
        $this->db->group_by('u.id');
        return $this->db->get()->result();
    }
}
