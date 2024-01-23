<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Issue_Ticket extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Issue_Ticket_model', 'iss');
    }

    
    public function drop_job_type (){
        $result = $this->iss->drop_job_type();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
}