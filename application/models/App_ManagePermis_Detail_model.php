<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_ManagePermis_Detail_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }



    public function show_group()
    {
        $sql_group_per = "SELECT spga_id, spga_name FROM sys_permission_group_app";
        $query = $this->db->query($sql_group_per);
        $data = $query->result();

        return $data;
    }


    public function show_tb($data)
    {
        $perid = $data["permisId"];



        $sql = "SELECT
        sys_permission_detail_app.spda_id,
        sys_permission_detail_app.spga_id,
        sys_permission_detail_app.sma_id,
        sys_permission_detail_app.spda_status_flg,
        sys_permission_detail_app.spda_created_date,
        sys_permission_detail_app.spda_created_by,
        IFNULL(sys_permission_detail_app.spda_updated_date, '-') AS spda_updated_date,
        COALESCE(sys_permission_detail_app.spda_updated_by, '-') AS spda_updated_by,
        sys_menu_app.sma_name
    FROM
        sys_permission_detail_app
    JOIN
        sys_permission_group_app ON sys_permission_detail_app.spga_id = sys_permission_group_app.spga_id
    JOIN
        sys_menu_app ON sys_permission_detail_app.sma_id = sys_menu_app.sma_id
    WHERE
        sys_permission_group_app.spga_id = '$perid';
    ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function drop_menu($data)
    {
        $perid = $data["permisId"];

        $sql_main_menu = "SELECT ma.sma_id, ma.sma_name
        FROM sys_menu_app ma
        LEFT JOIN sys_permission_detail_app pd ON ma.sma_id = pd.sma_id AND pd.spga_id = '$perid'
        WHERE pd.sma_id IS NULL;";
        $query = $this->db->query($sql_main_menu);
        $data = $query->result();

        return $data;
    }

    public function update_flg($data, $sess)
    {
        $stFlg = $data["newStatus"];
        $id = $data["smId"];

        $sql = "UPDATE sys_permission_detail_app 
        SET spda_status_flg = '$stFlg',
        spda_updated_date = NOW(),
        spda_updated_by = '$sess' 
        WHERE
        spda_id = '$id';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

}
