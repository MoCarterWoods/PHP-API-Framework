<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_control extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Ticket_control_model', 'tkc');
    }

    public function show_data(){
        $result = $this->tkc->show_data();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_avatar (){
        $result = $this->tkc->show_avatar();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

}
