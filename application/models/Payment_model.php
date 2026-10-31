<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

    public function get_latest_for_service($service_id) {
        return $this->db->where('service_id', $service_id)
            ->order_by('created_at', 'DESC')
            ->limit(1)
            ->get('payments')
            ->row();
    }

    public function record_simulated($service_id, $client_id, $payment_method) {
        $this->db->trans_begin();
        $service = $this->db->query(
            'SELECT id, client_id FROM services WHERE id = ? FOR UPDATE',
            array($service_id)
        )->row();

        if (!$service) {
            $this->db->trans_rollback();
            return 'not_found';
        }

        if ((int) $service->client_id !== (int) $client_id) {
            $this->db->trans_rollback();
            return 'not_owner';
        }

        $existing = $this->db->where('service_id', $service_id)
            ->where('status', 'completado')
            ->get('payments')
            ->row();
        if ($existing) {
            $this->db->trans_commit();
            return 'already_paid';
        }

        $quote = $this->db->select('q.total_amount')
            ->from('services s')
            ->join('quotes q', 'q.id = s.quote_id')
            ->where('s.id', $service_id)
            ->get()
            ->row();
        if (!$quote || (float) $quote->total_amount <= 0) {
            $this->db->trans_rollback();
            return 'invalid_amount';
        }

        $inserted = $this->db->insert('payments', array(
            'service_id' => $service_id,
            'amount' => $quote->total_amount,
            'status' => 'completado',
            'payment_method' => $payment_method,
            'transaction_id' => 'SIM-' . date('YmdHis') . '-' . bin2hex(random_bytes(5)),
            'paid_at' => date('Y-m-d H:i:s')
        ));

        if (!$inserted || !$this->db->trans_status()) {
            $this->db->trans_rollback();
            return 'error';
        }

        if (!$this->db->trans_commit()) {
            return 'error';
        }

        return 'paid';
    }
}
