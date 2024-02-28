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


    public function drop_type (){
        $result = $this->iss->drop_type();

        echo json_encode($result);
    } 

    public function drop_problem (){
        $selectedValue = $this->input->get('selectedValue');
    
        $result = $this->iss->drop_problem($selectedValue);
    
        echo json_encode($result);
    }
    

    public function chkBox_problem (){
        $result = $this->iss->chkBox_problem();

        echo json_encode($result);
    } 

    public function radio_jobtype (){
        $result = $this->iss->radio_jobtype();

        echo json_encode($result);
    } 

    public function drop_inspec_method (){

        $selectedValue = $this->input->get('selectedValue');

        $result = $this->iss->drop_inspec_method($selectedValue);

        echo json_encode($result);
    } 

    public function chkBox_inspection (){
        $result = $this->iss->chkBox_inspection();

        echo json_encode($result);
    } 

    public function drop_trouble (){
        $selectedValue = $this->input->get('selectedValue');

        $result = $this->iss->drop_trouble($selectedValue);

        echo json_encode($result);
    } 

    public function chkBox_trouble1 (){
        $result = $this->iss->chkBox_trouble1();

        echo json_encode($result);
    } 

    public function chkBox_trouble2 (){
        $result = $this->iss->chkBox_trouble2();

        echo json_encode($result);
    } 

    public function chkBox_analysis (){
        $result = $this->iss->chkBox_analysis();

        echo json_encode($result);
    } 

    public function chkBox_delivery (){
        $result = $this->iss->chkBox_delivery();

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
        echo json_encode($result);
    }


    public function jobtype_id (){
        $id = $this->input->get('ist_Id');
        $result = $this->iss->jobtype_id($id);

        echo json_encode($result);
    } 
    
}