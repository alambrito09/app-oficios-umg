<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rating_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function create($service_id, $client_id, $provider_id, $score, $comment) {
        // Verificar que el servicio esté completado o cerrado (solo servicios realizados)
        $service = $this->db->get_where('services', array('id' => $service_id))->row();
        if (!$service || !in_array($service->status, array('completado', 'cerrado'))) {
            return FALSE;
        }

        // Evitar doble calificación
        $existing = $this->db->get_where('ratings', array('service_id' => $service_id))->row();
        if ($existing) return FALSE;

        $this->db->insert('ratings', array(
            'service_id' => $service_id,
            'client_id' => $client_id,
            'provider_id' => $provider_id,
            'score' => $score,
            'comment' => $comment
        ));
        return $this->db->insert_id();
    }

    public function get_by_service($service_id) {
        $this->db->where('service_id', $service_id);
        return $this->db->get('ratings')->row();
    }

    public function get_by_provider($provider_id) {
        $this->db->select('r.*, u.full_name as client_name');
        $this->db->from('ratings r');
        $this->db->join('users u', 'u.id = r.client_id');
        $this->db->where('r.provider_id', $provider_id);
        return $this->db->get()->result();
    }

    public function get_average($provider_id) {
        $this->db->select('AVG(score) as avg, COUNT(*) as total');
        $this->db->where('provider_id', $provider_id);
        return $this->db->get('ratings')->row();
    }
}
