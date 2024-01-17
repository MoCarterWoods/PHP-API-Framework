<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Login_model', 'dash');
        $this->load->model('Api_model', 'apimd');
    }

    public function show_Menu(){
        $sess = unserialize($this->input->post('session'));
        $result = $this->apimd->get_menu($sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_Edit_Ac(){
        $result = $this->apimd->get_account();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 
    

    public function showAccount($id = null) {
        $id=1;
        // ส่งพารามิเตอร์ $id ไปยังฟังก์ชัน get_Shipping_db ของ Model
        $result = $this->dash->get_account($id);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
    public function chk_login() {

        $data = unserialize($this->input->post("data"));
        //$sess = unserialize($this->input->post("session"));
        $result = $this->dash->chk_login_db($data['requestData']);
        echo json_encode($result);
       
    }

    public function logout() {

        $data = unserialize($this->input->post("data"));
        //$sess = unserialize($this->input->post("session"));
        $result = $this->dash->logout_db($data);
        echo json_encode($result);
       
    }

}
