<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_Manage_menu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('App_ManageMenu_model', 'mang');
    }

    public function show_menu(){
        $result = $this->mang->show_menu();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    public function upstatus(){
        $sess = unserialize($this->input->post('session'));
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->update_flg($data,$sess);
       
        echo json_encode($result);
    }

    public function insert_menu(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->insert_menu($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }

    public function show_edit_menu(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_edit_menu($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function update_menu(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->update_menu($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }


}
