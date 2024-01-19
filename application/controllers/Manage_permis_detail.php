<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_permis_detail extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ManagePermis_Detail_model', 'mang');
    }

    
    public function show_group (){
        $result = $this->mang->show_group();
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 

    public function show_tb(){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_tb($data);
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

    public function drop_main (){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->drop_main($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }
    
    public function drop_sub (){
        $data = unserialize($this->input->post('data'));
        $result = $this->mang->drop_sub($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    }


    public function insert_permiss(){
        $data = unserialize($this->input->post('data'));
        $sess = unserialize($this->input->post('session'));
        $result = $this->mang->insert_permiss($data, $sess);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


    
    public function show_show_edit(){
        $data = $this->input->post();
       
        // $data = unserialize($this->input->post('data'));
        $result = $this->mang->show_show_edit($data);
        // echo "<pre>";
        // print_r($result);
        echo json_encode($result);
    } 


}