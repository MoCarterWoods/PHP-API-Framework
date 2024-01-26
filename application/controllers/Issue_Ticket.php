<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Issue_Ticket extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Issue_Ticket_model', 'iss');
    }

    
    public function drop_job_type (){
        $result = $this->iss->drop_job_type();

        echo json_encode($result);
    } 


    public function drop_tool (){
        $result = $this->iss->drop_tool();

        echo json_encode($result);
    } 

    public function drop_problem (){
        $result = $this->iss->drop_problem();

        echo json_encode($result);
    } 

    public function drop_inspec_method (){
        $result = $this->iss->drop_inspec_method();

        echo json_encode($result);
    } 

    public function drop_trouble (){
        $result = $this->iss->drop_trouble();

        echo json_encode($result);
    } 



    public function upload_file() {
    $targetDirectory = '127.0.0.1/ticket/assets/img/ProblemCondition/';
    $targetFile = $targetDirectory . basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    echo 'File uploaded successfully.';
    } else {
    echo 'Error uploading file.';
    }
    }


    public function save_issue(){

        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));

        $result = $this->iss->save_issue($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
}