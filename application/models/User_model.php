<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all($role_id = NULL) {
        $this->db->select('u.id, u.role_id, u.username, u.email, u.full_name, u.phone, u.status, u.created_at, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id');
        if ($role_id) {
            $this->db->where('u.role_id', $role_id);
        }
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        $this->db->select('u.*, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id');
        $this->db->where('u.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_username($username) {
        $this->db->select('u.*, r.name as role_name');
        $this->db->from('users u');
        $this->db->join('roles r', 'r.id = u.role_id');
        $this->db->where('u.username', $username);
        return $this->db->get()->row();
    }

    public function get_by_email($email) {
        $this->db->where('email', $email);
        return $this->db->get('users')->row();
    }

    public function create($data) {
        return $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    public function change_password($id, $hash) {
        $this->db->where('id', $id);
        return $this->db->update('users', array('password_hash' => $hash));
    }

    public function set_reset_token($email, $token, $expires) {
        $this->db->where('email', $email);
        return $this->db->update('users', array(
            'reset_token' => $token,
            'reset_expires' => $expires
        ));
    }

    public function verify_reset_token($token) {
        $this->db->where('reset_token', hash('sha256', $token));
        $this->db->where('reset_expires >', date('Y-m-d H:i:s'));
        return $this->db->get('users')->row();
    }

    public function clear_reset_token($id) {
        $this->db->where('id', $id);
        return $this->db->update('users', array('reset_token' => NULL, 'reset_expires' => NULL));
    }
}
