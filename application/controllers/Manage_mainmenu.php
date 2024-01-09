<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_mainmenu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mainmenu_model', 'main');
    }

    public function show_main_menu(){
        $result = $this->main->show_main_menu();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
}