<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ticket_History extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Ticket_History_model', 'tkh');
    }

    public function show_data(){
        $result = $this->tkh->show_data();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_data_list()
    {
        $inpStartDate = $this->input->post('inpStartDate');
        $inpEndDate = $this->input->post('inpEndDate');
        $data = array(
            'inpStartDate' => $inpStartDate,
            'inpEndDate' => $inpEndDate
        );
        $result = $this->tkh->show_data_list($data);
        echo json_encode($result);
    }

    
}