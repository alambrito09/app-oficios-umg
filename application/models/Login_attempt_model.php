<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_attempt_model extends CI_Model {

    public function is_locked($attempt_key) {
        $attempt = $this->db->get_where('login_attempts', array('attempt_key' => $attempt_key))->row();
        return $attempt && $attempt->locked_until && $attempt->locked_until > date('Y-m-d H:i:s');
    }

    public function record_failure($attempt_key) {
        $now = date('Y-m-d H:i:s');
        $attempt = $this->db->get_where('login_attempts', array('attempt_key' => $attempt_key))->row();

        if (!$attempt || strtotime($attempt->window_started_at) <= time() - 900) {
            $data = array(
                'attempt_key' => $attempt_key,
                'failed_attempts' => 1,
                'window_started_at' => $now,
                'locked_until' => NULL
            );
            if ($attempt) {
                $this->db->where('attempt_key', $attempt_key);
                return $this->db->update('login_attempts', $data);
            }
            return $this->db->insert('login_attempts', $data);
        }

        $failed_attempts = (int) $attempt->failed_attempts + 1;
        $data = array(
            'failed_attempts' => $failed_attempts,
            'locked_until' => $failed_attempts >= 5 ? date('Y-m-d H:i:s', time() + 900) : NULL
        );
        $this->db->where('attempt_key', $attempt_key);
        return $this->db->update('login_attempts', $data);
    }

    public function clear_attempts($attempt_key) {
        $this->db->where('attempt_key', $attempt_key);
        return $this->db->delete('login_attempts');
    }
}
