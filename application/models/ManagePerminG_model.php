<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ManagePerminG_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_group_name()
{
    $sql = "SELECT * FROM sys_permission_group;";

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

public function update_flg($data,$sess)
    {
        $stFlg = $data["newStatus"];
        $smId = $data["smId"];

        $sql = "UPDATE sys_permission_group 
        SET spg_status_flg = '$stFlg', spg_updated_date = NOW(), spg_updated_by = '$sess'
        WHERE spg_id = '$smId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function show_show_mpg($data) {
        $id = $data["id"];
        // return $id;
        // exit;
        
        $sql_show_mmn = "SELECT * FROM sys_permission_group WHERE spg_id = '$id';";

        $query = $this->db->query($sql_show_mmn);
        $data = $query->row();
        if ($this->db->affected_rows()>0) {
            return array('result'=> true,'data'=>$data);
        }
        else{
            return array('result' => false);
        }
    }

    public function update_mpg_name($data, $sess) {
        $pergname = $data["ManagePergname"];
        $id = $data["mgpId"];
    
        $sql_update_nopass = "
            UPDATE sys_permission_group
            SET spg_name = '$pergname',
            spg_updated_date = NOW(),
            spg_updated_by = '$sess'
            WHERE spg_id = '$id';
        ";
    
        $query_nopass = $this->db->query($sql_update_nopass);
    
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1);
        } else {
            return array('result' => 0);
        }
    }
    

}