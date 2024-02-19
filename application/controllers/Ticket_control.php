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

    public function accept_ticket(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->accept_ticket($data,$sess);
       
        echo json_encode($result);
    }

    public function cancel_ticket(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->cancel_ticket($data,$sess);
       
        echo json_encode($result);
    }

    public function show_equipment(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_equipment($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_problem(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_problem($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function chkBox_problem (){
        $result = $this->tkc->chkBox_problem();

        echo json_encode($result);
    } 

    public function save_problem(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->save_problem($data,$sess);
       
        echo json_encode($result);
    }

    public function show_jobtype(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_jobtype($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function radio_jobtype (){
        $result = $this->tkc->radio_jobtype();

        echo json_encode($result);
    } 
    public function save_jobtype(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->save_jobtype($data,$sess);
       
        echo json_encode($result);
    }


    public function show_inspection(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_inspection($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function chkBox_inspection (){
        $result = $this->tkc->chkBox_inspection();

        echo json_encode($result);
    } 

        public function save_inspection(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->save_inspection($data,$sess);
       
        echo json_encode($result);
    }

    public function show_analyze(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_analyze($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
    public function save_analyze(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->save_analyze($data,$sess);
       
        echo json_encode($result);
    }

    public function show_delivery(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->tkc->show_delivery($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function save_delivery(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->tkc->save_delivery($data,$sess);
       
        echo json_encode($result);
    }
}
