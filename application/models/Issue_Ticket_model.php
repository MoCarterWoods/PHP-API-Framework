<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Issue_Ticket_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }



    public function drop_job_type()
    {
        $sql_job_type = "SELECT 
        mjt_id,
        mjt_name_eng,
        mjt_name_thai
        FROM mst_job_type
        WHERE mjt_status_flg = 1";
        $query = $this->db->query($sql_job_type);
        $data = $query->result();

        return $data;
    }

    public function drop_tool()
    {
        $sql_tool = "SELECT mts_id,
        mts_name,
        mts_maker,
        mts_model
        FROM mst_tooling_system WHERE mts_status_flg =1";
        $query = $this->db->query($sql_tool);
        $data = $query->result();

        return $data;
    }


    public function drop_problem()
    {
        $sql_tool = "SELECT mpc_id,
        mpc_name_eng,
        mpc_name_thai,
        mpc_detail
        FROM mst_problem_condition WHERE mpc_status_flg =1";
        $query = $this->db->query($sql_tool);
        $data = $query->result();

        return $data;
    }

    public function drop_inspec_method()
    {
        $sql_inspec = "SELECT mim_id,
        mim_name_eng,
        mim_name_thai,
        mim_detail
        FROM mst_inspection_method WHERE mim_status_flg =1";
        $query = $this->db->query($sql_inspec);
        $data = $query->result();

        return $data;
    }

    public function drop_trouble()
    {
        $sql_inspec = "SELECT mt_id,
        mt_name_eng,
        mt_name_thai,
        mt_detail
        FROM mst_troubleshooting WHERE mt_status_flg =1";
        $query = $this->db->query($sql_inspec);
        $data = $query->result();

        return $data;
    }

}