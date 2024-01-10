<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mainmenu_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_main_menu()
{
    $sql = "SELECT * FROM sys_main_menu;";

    $query = $this->db->query($sql);
    $data = $query->result();

    // Loop through the result and replace null values with '-'
    foreach ($data as &$row) {
        foreach ($row as $key => $value) {
            if ($value === null) {
                $row->$key = '-';
            }
        }
    }

    return $data;
}

public function insert_main_menu($data, $sess) {
    $mainmenu = $data["MainMenuName"];
    $icon = $data["MainMenuIcon"];

    $sql_check_duplicate = "SELECT * FROM sys_main_menu WHERE smm_name = '$mainmenu'";
    $query_check_duplicate = $this->db->query($sql_check_duplicate);

    $sql_check_max = "SELECT IFNULL(MAX(smm_order_no), 0) + 1 AS next_order_no FROM sys_main_menu;";
    $query_max_no = $this->db->query($sql_check_max);

    // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
    if ($query_check_duplicate->num_rows() > 0) {
        return array('result' => 9); // มีข้อมูลซ้ำ
    } else {
        $next_order_no = $query_max_no->row()->next_order_no;

        $sql_insert = "INSERT INTO sys_main_menu (smm_name, smm_icon, smm_order_no, smm_status_flg, smm_created_date, smm_created_by)
        VALUES ('$mainmenu', '$icon', '$next_order_no', 1, NOW(), '$sess')";

        $query = $this->db->query($sql_insert);

        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // Insert สำเร็จ
        } else {
            return array('result' => 0); // Insert ล้มเหลว
        }
    }
}



    public function update_flg($data,$sess)
    {
        $stFlg = $data["newStatus"];
        $smId = $data["smId"];

        $sql = "UPDATE sys_main_menu 
        SET smm_status_flg = '$stFlg',smm_updated_date = NOW(),smm_updated_by = '$sess'
        WHERE smm_id = '$smId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function show_show_mmn($data) {
        $id = $data["id"];
        // return $id;
        // exit;
        
        $sql_show_mmn = "SELECT * FROM sys_main_menu WHERE smm_id = '$id';";

        $query = $this->db->query($sql_show_mmn);
        $data = $query->row();
        if ($this->db->affected_rows()>0) {
            return array('result'=> true,'data'=>$data);
        }
        else{
            return array('result' => false);
        }
    }

public function update_mmn($data, $sess){
        $id = $data["mmnId"];
        $mmn = $data["MainMenuName"];
        $mmi = $data["MainMenuIcon"];
        $ordno = $data["OrderNo"];


        $sql = "update sys_main_menu set smm_order_no = $ordno  where  smm_id = $id ";
        $query_update = $this->db->query($sql);
        $sql1 = "select * from sys_main_menu where smm_order_no >= $ordno  and smm_id != $id order by smm_id asc";
        $query1 = $this->db->query($sql1);
        $row1 = $query1->result_array();

        

        $sql2 = "select count(smm_id) As c_smm_id from sys_main_menu";
        $query2 = $this->db->query($sql2);
        $row2 = $query2->result_array();
      
        $n = $row2[0]["c_smm_id"]; //4
        $Neworder = count($row1); //3
            foreach ($row1 as $items){
                $gid = $items["smm_id"];
                $oldOrder = $items["smm_order_no"];
                $Neworder =  $Neworder + 1;
                if ($gid){
                    $sqlUpdate = "update sys_main_menu set smm_order_no = $Neworder  where smm_id = $gid";
                }else{
                }
                $queryUpdate = $this->db->query($sqlUpdate);
            }
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // อัปเดตสำเร็จ
            } else {
                return array('result' => 0); // ไม่สามารถอัปเดต
            }
        }
}

    // public function update_mmn($data, $sess) {
    //     $mmn = $data["MainMenuName"];
    //     $mmi = $data["MainMenuIcon"];
    //     $ordno = $data["OrderNo"];
    
    //     $data_chk_user = $this->get_mmn_data($empcode);
    
    //         if ($this->db->affected_rows() > 0) {
    //             return array('result' => 1);
    //         } else {
    //             return array('result' => 0);
            
    //         } else {
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
    
    // private function get_mmn_data($mmn) {
    //     $sql_select = "
    //         SELECT *
    //         FROM sys_main_menu
    //         WHERE smm_name = '$mmn'
    //     ";
    
    //     $query_select = $this->db->query($sql_select);
    //     return $query_select->row();
    // }

