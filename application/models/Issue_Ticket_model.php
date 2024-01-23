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

}