<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_ManagePerminG_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_group_name()
    {
        $sql = "SELECT * FROM sys_permission_group_app;";

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


    public function update_flg($data, $sess)
    {
        $stFlg = $data["newStatus"];
        $smId = $data["smId"];

        $sql = "UPDATE sys_permission_group_app 
        SET spga_status_flg = '$stFlg', spga_updated_date = NOW(), spga_updated_by = '$sess'
        WHERE spga_id = '$smId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function insert_permis_group($data, $sess) {
        $mpgname = $data["ManagePersGroup"];

    
        $sql_check_duplicate = "SELECT * FROM sys_permission_group_app WHERE spga_name = '$mpgname'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);
    
        // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $sql_insert = "INSERT INTO sys_permission_group_app (spga_name, spga_status_flg, spga_created_date, spga_created_by) 
            VALUES ('$mpgname', 1 , NOW(), '$sess')";
    
            $query = $this->db->query($sql_insert);
    
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }


    public function show_show_mpg($data)
    {
        $id = $data["id"];
        // return $id;
        // exit;

        $sql_show_mmn = "SELECT * FROM sys_permission_group_app WHERE spga_id = '$id';";

        $query = $this->db->query($sql_show_mmn);
        $data = $query->row();
        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }


    public function update_mpg_name($data, $sess)
    {
        $pergname = $data["ManagePergname"];
        $id = $data["mgpId"];

        $sql_update_nopass = "
            UPDATE sys_permission_group_app
            SET spga_name = '$pergname',
            spga_updated_date = NOW(),
            spga_updated_by = '$sess'
            WHERE spga_id = '$id';
        ";

        $query_nopass = $this->db->query($sql_update_nopass);

        if ($this->db->affected_rows() > 0) {
            return array('result' => 1);
        } else {
            return array('result' => 0);
        }
    }


}
