<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EditAccount_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function get_account($sess) {
        $sql = "SELECT * FROM sys_account WHERE sa_id = '$sess'";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    

    public function update_user($data, $sess) {
        $empcode = $data["EmpCode"];
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
    
        $data_chk_user = $this->get_user_data($empcode);
    
        if ($data_chk_user) {
            $sql_update_nopass = "
                UPDATE sys_account
                SET sa_emp_code= '$empcode', 
                    sa_fristname= '$firstname',
                    sa_lastname= '$lastname',
                    sa_email= '$email',
                    sa_updated_date= NOW(),
                    sa_updated_by= '$sess'
                WHERE sa_emp_code= '$empcode';
            ";
    
            $query_nopass = $this->db->query($sql_update_nopass);
    
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1);
            } else {
                return array('result' => 0);
            }
        } 
    }
    
    private function get_user_data($empcode) {
        $sql_select = "
            SELECT *
            FROM sys_account
            WHERE sa_emp_code = '$empcode'
        ";
    
        $query_select = $this->db->query($sql_select);
        return $query_select->row();
    }
    
    

    // public function update_user($data, $sess) {
    //     $empcode = $data["EmpCode"];
    //     $password = ($data["EmpPassword"] != '') ? md5($data["EmpPassword"]) : NULL;
    //     $firstname = $data["EmpFirstName"];
    //     $lastname = $data["EmpLastName"];
    //     $email = $data["EmpEmail"];
    //     $permisgroup = $data["EmpPermission"];
    //     $plant = $data["EmpPlantCode"];
    
    //     $data_chk_user = $this->get_user_data($empcode);
    
    //     if ($data_chk_user->sa_emp_password == $password || $password === NULL) {
    //         $sql_update_nopass = "
    //             UPDATE sys_account
    //             SET sa_emp_code= '$empcode', 
    //                 sa_fristname= '$firstname',
    //                 sa_lastname= '$lastname',
    //                 sa_email= '$email',
    //                 spg_id= '$permisgroup',
    //                 mpc_id= '$plant',
    //                 sa_updated_date= NOW(),
    //                 sa_updated_by= '$sess'
    //             WHERE sa_emp_code= '$empcode';
    //         ";
    
    //         $query_nopass = $this->db->query($sql_update_nopass);
            
    //         if ($this->db->affected_rows() > 0) {
    //             return array('result' => 1);
    //         } else {
    //             return array('result' => 0);
    //         }
    //     } else {
    //         $sql_update = "
    //             UPDATE sys_account
    //             SET sa_emp_code= '$empcode', 
    //                 sa_emp_password= '$password',
    //                 sa_fristname= '$firstname',
    //                 sa_lastname= '$lastname',
    //                 sa_email= '$email',
    //                 spg_id= '$permisgroup',
    //                 mpc_id= '$plant',
    //                 sa_updated_date= NOW(),
    //                 sa_updated_by= '$sess'
    //             WHERE sa_emp_code= '$empcode';
    //         ";
    
    //         $query_update = $this->db->query($sql_update);
    
    //         if ($this->db->affected_rows() > 0) {
    //             return array('result' => 1); // อัปเดตสำเร็จ
    //         } else {
    //             return array('result' => 0); // ไม่สามารถอัปเดต
    //         }
    //     }
    // }
}