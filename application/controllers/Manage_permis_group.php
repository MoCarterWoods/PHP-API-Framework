<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_permis_group extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ManagePerminG_model', 'mang');
    }

    public function show_group_name(){
        $result = $this->mang->show_group_name();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    public function insert_permis_group(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->insert_permis_group($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 



    public function update_flg(){
        $sess = unserialize($this->input->post('session'));
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->update_flg($data,$sess);
       
        echo json_encode($result);
    }

    public function show_show_mpg(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_show_mpg($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function update_mpg_name(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->update_mpg_name($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
}