<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_mainmenu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Mainmenu_model', 'main');
    }

    public function show_main_menu(){
        $result = $this->main->show_main_menu();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    public function insert_main_menu(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->main->insert_main_menu($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    public function upd_status_main_menu(){
        $data = unserialize($this->input->post('data'));
        $result = $this->main->update_flg($data);
       
        echo json_encode($result);
    }

    public function show_show_mmn(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->main->show_show_mmn($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function update_mmn(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->main->update_mmn($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }

}