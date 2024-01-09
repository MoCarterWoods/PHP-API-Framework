<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mainmenu_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_main_menu() {
        $sql = "SELECT * FROM sys_main_menu;";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
}