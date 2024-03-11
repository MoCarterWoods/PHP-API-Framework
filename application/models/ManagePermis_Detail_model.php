<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ManagePermis_Detail_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }



    public function show_group()
    {
        $sql_group_per = "SELECT spg_id, spg_name FROM sys_permission_group WHERE spg_status_flg = 1;";
        $query = $this->db->query($sql_group_per);
        $data = $query->result();

        return $data;
    }


    public function show_tb($data)
    {
        $perid = $data["permisId"];



        $sql = "SELECT
        sys_permission_detail.spd_id,
        sys_permission_group.spg_id,
        sys_permission_detail.ssm_id,
        sys_permission_detail.spd_status_flg,
        sys_permission_detail.spd_created_date,
        sys_permission_detail.spd_created_by,
        IFNULL(sys_permission_detail.spd_updated_date, '-') AS spd_updated_date,
        COALESCE(sys_permission_detail.spd_updated_by, '-') AS spd_updated_by,
        sys_sub_menu.ssm_name,
        sys_main_menu.smm_name
    FROM
        sys_permission_detail
    JOIN
        sys_permission_group ON sys_permission_detail.spg_id = sys_permission_group.spg_id
    JOIN
        sys_sub_menu ON sys_permission_detail.ssm_id = sys_sub_menu.ssm_id
    JOIN
        sys_main_menu ON sys_sub_menu.smm_id = sys_main_menu.smm_id
    WHERE
        sys_permission_group.spg_id = '$perid';
    ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }


    public function update_flg($data, $sess)
    {
        $stFlg = $data["newStatus"];
        $detailId = $data["detailId"];

        $sql = "UPDATE sys_permission_detail 
        SET spd_status_flg = '$stFlg', spd_updated_date = NOW(), spd_updated_by = '$sess'
        WHERE spd_id = '$detailId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function drop_main($data)
    {
        $perid = $data["permisId"];

        $sql_main_menu = "SELECT DISTINCT sys_main_menu.smm_id, sys_main_menu.smm_name
        FROM sys_main_menu
        LEFT JOIN sys_sub_menu ON sys_sub_menu.smm_id = sys_main_menu.smm_id
        LEFT JOIN sys_permission_detail ON sys_sub_menu.ssm_id = sys_permission_detail.ssm_id AND sys_permission_detail.spg_id = '$perid'
        WHERE sys_permission_detail.ssm_id IS NULL;";
        $query = $this->db->query($sql_main_menu);
        $data = $query->result();

        return $data;
    }
    public function drop_sub($data)
    {
        $perid = $data["permisId"];
        $mainid = $data["mainId"];



        $sql_sub_menu = "SELECT 
        sys_sub_menu.ssm_id,
        sys_sub_menu.ssm_name
    FROM sys_sub_menu
    LEFT JOIN sys_permission_detail ON sys_sub_menu.ssm_id = sys_permission_detail.ssm_id AND sys_permission_detail.spg_id = '$perid'
    WHERE sys_sub_menu.smm_id = '$mainid'
        AND sys_permission_detail.ssm_id IS NULL;
    ";
        $query = $this->db->query($sql_sub_menu);
        $data = $query->result();

        return $data;
    }


    public function insert_permiss($data, $sess) {
        $permisID = $data["PermisID"];
        $menuGroup = $data["MenuGroup"];
        $subMenu = $data["SubMenu"];
    
        // Check if data exists in sys_sub_menu for the given condition
        $sql_check_sub_menu = "SELECT ssm_id 
                                FROM sys_sub_menu 
                                LEFT JOIN sys_main_menu ON sys_main_menu.smm_id = sys_sub_menu.smm_id  
                                WHERE sys_main_menu.smm_id = '$menuGroup'";
    
        $query_check_sub_menu = $this->db->query($sql_check_sub_menu);
    
        // If no data exists, return an error result
        if ($query_check_sub_menu->num_rows() == 0) {
            return array('result' => 2); // No data found in sys_sub_menu
        }
    
        // Check for duplicate entries in the sys_permission_detail table
        $sql_check_duplicate = "SELECT * FROM sys_permission_detail WHERE spg_id = '$permisID' AND ssm_id = '$subMenu'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);
    
        // If duplicate entries are found, return an error result
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // Duplicate data found
        } else {
            // If no duplicate entries are found, insert a new record
            $sql_insert = "INSERT INTO sys_permission_detail 
            (spg_id, 
            ssm_id, 
            spd_status_flg, 
            spd_created_date, 
            spd_created_by) 
            VALUES ('$permisID ', '$subMenu', 1, NOW() , '$sess')";
    
            $query = $this->db->query($sql_insert);
    
            // Check if the insert operation was successful
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Successful insert
            } else {
                return array('result' => 0); // Failed insert
            }
        }
    }
    


    public function show_show_edit($data)
    {
        $id = $data["id"];


        
        $sql_show_edit = "SELECT 
        sys_main_menu.smm_name,
        sys_main_menu.smm_id,
        sys_sub_menu.ssm_name,
        sys_sub_menu.ssm_id
         FROM sys_permission_detail
        JOIN
                sys_sub_menu ON sys_permission_detail.ssm_id = sys_sub_menu.ssm_id
        JOIN
                sys_main_menu ON sys_sub_menu.smm_id = sys_main_menu.smm_id
            WHERE spd_id = '$id';";

        $query = $this->db->query($sql_show_edit);
        $data = $query->row();

        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }
}
