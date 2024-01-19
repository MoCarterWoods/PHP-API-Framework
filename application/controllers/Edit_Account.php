<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit_Account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('EditAccount_model', 'edit');
    }

    public function show_Edit_Ac(){
        $sess = unserialize($this->input->post('session'));
        $result = $this->edit->get_account($sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    public function update_user(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->edit->update_user($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
}










