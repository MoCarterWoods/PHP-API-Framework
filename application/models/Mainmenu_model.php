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
        $sql = "SELECT 
        smm_id,
        smm_name,
        smm_icon,
        smm_order_no,
        smm_status_flg,
        DATE_FORMAT(smm_updated_date, '%Y-%m-%d') as smm_updated_date,
        smm_updated_by
    FROM 
        sys_main_menu;";
        $query = $this->db->query($sql);
        $data = $query->result();

        foreach ($data as &$row) {
            foreach ($row as $key => $value) {
                if ($value === null) {
                    $row->$key = '-';
                }
            }
        }

        return $data;
    }

    public function insert_main_menu($data, $sess)
    {
        $mainmenu = $data["MainMenuName"];
        $icon = $data["MainMenuIcon"];

        $sql_check_duplicate = "SELECT * FROM sys_main_menu WHERE smm_name = '$mainmenu'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);

        $sql_check_max = "SELECT IFNULL(MAX(smm_order_no), 0) + 1 AS next_order_no FROM sys_main_menu;";
        $query_max_no = $this->db->query($sql_check_max);

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

    public function update_flg($data, $sess)
    {
        $stFlg = $data["newStatus"];
        $smId = $data["smId"];

        $sql = "UPDATE sys_main_menu 
        SET smm_status_flg = '$stFlg',smm_updated_date = NOW(),smm_updated_by = '$sess'
        WHERE smm_id = '$smId';";

        $query = $this->db->query($sql);
        return $this->db->affected_rows() > 0;
    }

    public function show_show_mmn($data)
    {
        $id = $data["id"];
        $sql_show_mmn = "SELECT * FROM sys_main_menu WHERE smm_id = '$id';";

        $query = $this->db->query($sql_show_mmn);
        $data = $query->row();

        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function update_mmn($data, $sess)
    {
        $id = $data["mmnId"];
        $ordno = $data["OrderNo"];

        $sql = "UPDATE sys_main_menu SET smm_order_no = $ordno  WHERE  smm_id = $id ";
        $query_update = $this->db->query($sql);

        $sql1 = "SELECT * FROM sys_main_menu WHERE smm_order_no >= $ordno  AND smm_id != $id ORDER BY smm_id ASC";
        $query1 = $this->db->query($sql1);
        $row1 = $query1->result_array();

        $sql2 = "SELECT COUNT(smm_id) AS c_smm_id FROM sys_main_menu";
        $query2 = $this->db->query($sql2);
        $row2 = $query2->result_array();
      
        $n = $row2[0]["c_smm_id"];
        $Neworder = count($row1);

        foreach ($row1 as $items) {
            $gid = $items["smm_id"];
            $oldOrder = $items["smm_order_no"];
            $Neworder =  $Neworder + 1;

            if ($gid) {
                $sqlUpdate = "UPDATE sys_main_menu SET smm_order_no = $Neworder  WHERE smm_id = $gid";
            }

            $queryUpdate = $this->db->query($sqlUpdate);
        }

        return $this->db->affected_rows() > 0 ? array('result' => 1) : array('result' => 0);
    }

    public function edit_main_menu($data, $sess)
    {
        $mmname = $data["MainMenuName"];
        $mmicon = $data["MainMenuIcon"];
        $ordno = $data["OrderNo"];
        $id = $data["mmnId"];

        $checkEdit = $this->checkEditMainMenu($data);

        if (!$checkEdit) {
            $sql = "UPDATE sys_main_menu SET smm_name = '$mmname', smm_icon = '$mmicon', smm_order_no = '$ordno', smm_updated_date = NOW(), smm_updated_by = '$sess' WHERE smm_id = '$id'";
            $result = $this->db->query($sql);

            if ($this->db->affected_rows() > 0) {
                $this->orderNo($data);
                return array(
                    'result' => 1,
                    'massage' => 'edit main menu success'
                );
            } else {
                return array(
                    'result' => false,
                    'massage' => 'edit main menu failed'
                );
            }
        } else {
            return array(
                'result' => 2,
                'massage' => 'Duplicate value!!'
            );
        }
    }

    public function checkEditMainMenu($data)
    {
        $id = $data["mmnId"];
        $mmname = $data["MainMenuName"];

        $result = $this->db->query("SELECT * FROM sys_main_menu WHERE smm_name = '$mmname' AND smm_id != '$id'");


        if ($result->num_rows() > 0) {
            // มีข้อมูลที่ต้องการแก้ไข
            return true;
        } else {
            // ไม่พบข้อมูลที่ต้องการแก้ไข
            return false;
        }
    }

    public function orderNo($data)
    {
        $id = $data["mmnId"];
        $ordno = $data["OrderNo"];

        $result = $this->db->query("SELECT smm_id,smm_order_no FROM sys_main_menu");
        $res = $result->result_array();
        $i = 1;
        $order = [];

        foreach ($res as $value) {
            if ($value["smm_id"] != $id) {
                $i = $i == $ordno ? ++$i : $i;
                $result = $this->db->query("UPDATE sys_main_menu SET smm_order_no = '$i' WHERE smm_id = '{$value['smm_id']}'");
                $i++;
        
                // ตรวจสอบการอัปเดตข้อมูล
                if (!$result) {
                    return array('result' => 0);
                }
            } else {
                $order[] = [
                    'id' => $value["smm_id"],
                    'order' => $ordno
                ];
            }
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

