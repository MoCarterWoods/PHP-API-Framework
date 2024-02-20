<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket_History_model extends CI_Model
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
        t1.ist_request_by,
        t2.mjt_name_eng,
        t2.mjt_name_thai,
        GROUP_CONCAT( DISTINCT t3.lmw_id ) AS lmw_id,
        GROUP_CONCAT( DISTINCT t4.swa_fristname ) AS swa_fristname,
        GROUP_CONCAT( DISTINCT t4.swa_emp_code ) AS swa_emp_code,
        t5.mts_name,
    CASE
            
            WHEN t1.ist_line_cd 
            OR t1.ist_area_other IS NULL THEN
                1 ELSE 3 
                END AS equipment_status,
            COALESCE ( t6.ipc_status_flg, 1 ) AS problem_status,
        CASE
                
                WHEN t1.mjt_id IS NULL THEN
                1 ELSE 3 
            END AS jopType_status,
            COALESCE ( t7.iim_status_flg, 1 ) AS inspection_status,
            COALESCE ( t8.it_status_flg, 1 ) AS troubleshooting_status,
            COALESCE ( t9.irp_status_flg, 1 ) AS rqPart_status,
            COALESCE ( t10.iap_status_flg, 1 ) AS analyze_status,
            COALESCE ( t11.ipr_status_flg, 1 ) AS prevention_status,
            COALESCE ( t12.ide_status_flg, 1 ) AS delivery_status,
            t1.ist_status_flg 
        FROM
            info_issue_ticket t1
            LEFT JOIN mst_job_type t2 ON t1.mjt_id = t2.mjt_id
            LEFT JOIN log_manage_worker t3 ON t1.ist_id = t3.ist_id
            LEFT JOIN sys_worker_app t4 ON t3.lmw_worker = t4.swa_id
            LEFT JOIN mst_tooling_system t5 ON t1.ist_tool = t5.mts_id
            LEFT JOIN info_problem_condition t6 ON t1.ist_id = t6.ist_id
            LEFT JOIN info_inspection_method t7 ON t1.ist_id = t7.ist_id
            LEFT JOIN info_troubleshooting t8 ON t1.ist_id = t8.ist_id
            LEFT JOIN info_required_parts t9 ON t1.ist_id = t9.ist_id
            LEFT JOIN info_analyze_problem t10 ON t1.ist_id = t10.ist_id
            LEFT JOIN info_prevention_recurrence t11 ON t1.ist_id = t11.ist_id
            LEFT JOIN info_delivery_equipment t12 ON t1.ist_id = t12.ist_id 
        WHERE
            t1.ist_status_flg IN ( 1, 3, 5, 7, 8 ) 
            AND (
            COALESCE ( t6.ipc_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t7.iim_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t8.it_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t9.irp_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t10.iap_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t11.ipr_status_flg, 1 ) IN ( 1, 3 )) 
            AND (
            COALESCE ( t12.ide_status_flg, 1 ) IN ( 1, 3 )) 
        GROUP BY
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
            t5.mts_name 
    ORDER BY
        t1.ist_job_no;";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }


}