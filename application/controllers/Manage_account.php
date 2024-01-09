<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_account extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Manage_model', 'mang');
    }

    public function show_user(){
        $result = $this->mang->show_user();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_drop_down (){
        $result = $this->mang->show_drop_down();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function insert_user(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->insert_user($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function update_status(){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->update_status($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
    
    public function upstatus(){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->update_flg($data);
       
        echo json_encode($result);
    }
    
    public function show_show_acc(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_show_acc($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_update_acc(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->insert_user($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }  
    public function update_user(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->update_user($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }

    public function show_upd_User(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->show_upd_User($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }  
}
