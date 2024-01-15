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
        $sql_group_per = "SELECT spg_id, spg_name FROM sys_permission_group";
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
        COALESCE(sys_permission_detail.spd_updated_date, '-') AS spd_updated_date,
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
        sys_permission_group.spg_id = '$perid';";

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


    public function drop_main()
    {
        $sql_main_menu = "SELECT smm_id, smm_name FROM sys_main_menu";
        $query = $this->db->query($sql_main_menu);
        $data = $query->result();

        return $data;
    }
    public function drop_sub()
    {
        $sql_sub_menu = "SELECT ssm_id, ssm_name FROM sys_sub_menu";
        $query = $this->db->query($sql_sub_menu);
        $data = $query->result();

        return $data;
    }
}
