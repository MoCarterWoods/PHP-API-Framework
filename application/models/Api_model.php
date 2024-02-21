<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_account($id) {
        $sql = "SELECT * FROM sys_account WHERE sa_id = $id";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    public function chk_login_db($data) {       
        return $data;
    }
    public function get_menu($sess) {
        $sql = "CALL get_menu($sess)";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
}
