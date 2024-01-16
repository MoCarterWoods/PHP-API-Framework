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
    public function get_menu() {
        $sql = "SELECT sys_main_menu.smm_id, 
        sys_main_menu.smm_name, 
        sys_main_menu.smm_icon, 
        sys_main_menu.smm_order_no, 
        sys_main_menu.smm_status_flg ,
        sys_sub_menu.smm_id , 
        sys_sub_menu.ssm_controller,
        sys_sub_menu.ssm_name
        FROM sys_main_menu
        INNER JOIN sys_sub_menu
        ON sys_main_menu.smm_id = sys_sub_menu.smm_id;";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
}
