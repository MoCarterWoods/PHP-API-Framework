<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_submenu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ManageSubmenu_model', 'mang');
    }

    
    public function show_main_menu (){
        $result = $this->mang->show_main_menu();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_submenu(){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_submenu($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
}


