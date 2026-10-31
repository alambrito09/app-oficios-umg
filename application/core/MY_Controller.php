<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return;
        }

        $account = $this->db->select('status')
            ->get_where('users', array('id' => $user_id))
            ->row();

        if (!$account || $account->status !== 'activo') {
            $this->session->sess_destroy();
            redirect('auth/login');
        }
    }
}
