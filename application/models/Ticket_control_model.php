<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_control_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_data()
    {
        $sql = "SELECT 
        t1.ist_id, 
        t1.ist_type, 
        t1.ist_pd, 
        t1.ist_line_cd, 
        t1.ist_area_other, 
        t1.ist_process, 
        t1.ist_tool, 
        t1.ist_job_no,
        t1.mjt_id, 
        t1.ist_request_by,
            t1.ist_status_flg, 
    
        t2.mjt_name_eng,
        t2.mjt_name_thai,
        t3.lmw_id,
        t4.swa_fristname,
        t4.swa_lastname,
            t4.swa_emp_code,
            t5.mts_name
    FROM 
        info_issue_ticket t1
    LEFT JOIN 
        mst_job_type t2 ON t1.mjt_id = t2.mjt_id
    LEFT JOIN 
        log_manage_worker t3 ON t1.ist_id = t3.ist_id
    LEFT JOIN 
        sys_worker_app t4 ON t3.lmw_worker = t4.swa_id
    LEFT JOIN
        mst_tooling_system t5 ON t1.ist_tool = t5.mts_id
    WHERE 
        t1.ist_status_flg IN (1, 5, 9)
    ORDER BY 
        t1.ist_job_no;
    ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }


    public function show_avatar()
    {
        $sql = "SELECT 
        t1.ist_id, 
        t2.lmw_id,
        t3.swa_fristname,
        t3.swa_lastname,
        t3.swa_emp_code
    FROM 
        info_issue_ticket t1
    LEFT JOIN 
        log_manage_worker t2 ON t1.ist_id = t2.ist_id
    LEFT JOIN 
        sys_worker_app t3 ON t2.lmw_worker = t3.swa_id
    WHERE 
        t1.ist_status_flg IN (1, 5, 9)
    ORDER BY 
        t1.ist_job_no;
    
    ";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
}
