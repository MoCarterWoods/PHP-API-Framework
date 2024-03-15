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
        t1.ist_status_flg 
        FROM
            info_issue_ticket t1
            LEFT JOIN mst_job_type t2 ON t1.mjt_id = t2.mjt_id
            LEFT JOIN log_manage_worker t3 ON t1.ist_id = t3.ist_id
            LEFT JOIN sys_worker_app t4 ON t3.lmw_worker = t4.swa_id
            LEFT JOIN mst_tooling_system t5 ON t1.ist_tool = t5.mts_id
        WHERE
            t1.ist_status_flg = 9 
            AND DATE(t1.ist_approved_date) = CURDATE()  
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
    public function show_data_list($data)
    {
        $beginDate = $data['inpStartDate'];
        $endDate = $data['inpEndDate'];
        $sql = "SELECT
            t1.ist_id,
            t1.ist_type,
            t1.ist_pd,
            t1.ist_line_cd,
            t1.ist_area_other,
            DATE_FORMAT(t1.ist_date, '%b %d, %Y') AS ist_date,
            t1.ist_process,
            t1.ist_request_by,
            t2.mjt_name_eng,
            t2.mjt_name_thai,
            t1.ist_status_flg,
            GROUP_CONCAT( DISTINCT t3.lmw_id ) AS lmw_id,
            GROUP_CONCAT( DISTINCT t4.swa_fristname ) AS swa_fristname,
            GROUP_CONCAT( DISTINCT t4.swa_emp_code ) AS swa_emp_code,
            t5.mts_name
        FROM
            info_issue_ticket t1
            LEFT JOIN mst_job_type t2 ON t1.mjt_id = t2.mjt_id
            LEFT JOIN log_manage_worker t3 ON t1.ist_id = t3.ist_id
            LEFT JOIN sys_worker_app t4 ON t3.lmw_worker = t4.swa_id
            LEFT JOIN mst_tooling_system t5 ON t1.ist_tool = t5.mts_id
            LEFT JOIN info_problem_condition t6 ON t1.ist_id = t6.ist_id
        WHERE
            t1.ist_status_flg = 9 AND
            DATE(t1.ist_date) BETWEEN '$beginDate' AND '$endDate'
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


    public function show_pdf($ist_Id)
    {


        $sql_show_info = "SELECT
        t1.ist_id,
        t1.ist_pd,
        t1.ist_line_cd,
        t1.ist_area_other,
        t1.ist_process,
        t2.mts_name,
        t1.ist_maker,
        t1.ist_model,
        t1.ist_job_no,
        t1.ist_date,
        t1.ist_start_date,
        t1.ist_end_date,
        t1.ist_approved_date,
        t1.ist_approved_by,
        t1.mjt_id
        FROM
            info_issue_ticket t1
            LEFT JOIN mst_tooling_system t2 ON t1.ist_tool = t2.mts_id 
        WHERE
            t1.ist_id = '$ist_Id' 
        GROUP BY
            t1.ist_id";


        $query = $this->db->query($sql_show_info, array($ist_Id));
        $info_issue = $query->result();


        $sql_show_problem = "SELECT
        t1.ist_id,
        t1.mpc_id,
        GROUP_CONCAT( DISTINCT t2.mpc_id ) AS mpc_id,
        t2.ipc_detail,
        t2.ipc_pic_1,
        t2.ipc_pic_2,
        t2.ipc_pic_3,
        t3.mpc_name_eng,
        t3.mpc_name_thai
        FROM
            info_issue_ticket t1
        LEFT JOIN info_problem_condition t2 ON t1.ist_id = t2.ist_id 
            AND t2.ipc_status_flg = 3
        LEFT JOIN mst_problem_condition t3 ON t1.mpc_id = t3.mpc_id
        WHERE
            t1.ist_id = '$ist_Id' 
        GROUP BY
            t1.ist_id";

        $query_problem = $this->db->query($sql_show_problem, array($ist_Id));
        $data_problem = $query_problem->result();

        $sql_show_inspection = "SELECT
        t1.ist_id,
        t1.mim_id,
        GROUP_CONCAT( DISTINCT t2.mim_id ) AS mim_id,
        t2.iim_detail,
        t2.iim_pic_1,
        t2.iim_pic_2,
        t2.iim_pic_3,
        t3.mim_name_eng,
        t3.mim_name_thai
        
        FROM
            info_issue_ticket t1
        LEFT JOIN info_inspection_method t2 ON t1.ist_id = t2.ist_id
            AND t2.iim_status_flg = 3
            
        LEFT JOIN mst_inspection_method t3 ON t1.mpc_id = t3.mim_id
        WHERE
            t1.ist_id = '$ist_Id' 
        GROUP BY
            t1.ist_id";

        $query_inspection = $this->db->query($sql_show_inspection, array($ist_Id));
        $data_inspection = $query_inspection->result();


        $sql_show_trouble = "SELECT
        t1.ist_id,
        t1.mt_id,
        GROUP_CONCAT( DISTINCT t2.mt_id ) AS mt_id,
        GROUP_CONCAT( DISTINCT t2.it_detail ) AS it_detail,
        t2.it_pic_1,
        t2.it_pic_2,
        t2.it_pic_3,
        t3.mt_name_eng,
        t3.mt_name_thai
        
        FROM
            info_issue_ticket t1
        LEFT JOIN info_troubleshooting t2 ON t1.ist_id = t2.ist_id
            AND t2.it_status_flg = 3
            
        LEFT JOIN mst_troubleshooting t3 ON t1.mt_id = t3.mt_id
        WHERE
            t1.ist_id = '$ist_Id' 
        GROUP BY
            t1.ist_id";

        $query_trouble = $this->db->query($sql_show_trouble, array($ist_Id));
        $data_trouble = $query_trouble->result();

        $sql_show_req_part = "SELECT
        t3.mts_name,
        t2.irp_maker,
        t2.irp_model,
        t4.mtr_name,
        t2.irp_qty,
        DATE_FORMAT(t2.irp_withdraw_time, '%d/%m/%Y') AS irp_withdraw_time,
        t2.irp_withdraw_qty,
        DATE_FORMAT(t2.irp_order_time, '%d/%m/%Y') AS irp_order_time,
        t2.irp_order_qty,
        DATE_FORMAT(t2.irp_received_time, '%d/%m/%Y') AS irp_received_time,
        t2.irp_received_qty
                
        FROM
        info_issue_ticket t1
        LEFT JOIN info_required_parts t2 ON t1.ist_id = t2.ist_id
        AND t2.irp_status_flg = 3
        LEFT JOIN mst_tooling_system t3 ON t2.irp_name = t3.mts_id
        LEFT JOIN mst_type_request t4 ON t2.irp_type = t4.mtr_id
        WHERE
        t1.ist_id = '$ist_Id' ";

        $query_req_part = $this->db->query($sql_show_req_part, array($ist_Id));
        $data_req_part = $query_req_part->result();


        $sql_show_analyz = "SELECT
        t2.map_id,
        t2.iap_detail,
        t2.iap_pic1,
        t2.iap_pic2,
        t2.iap_pic3
        
        FROM
            info_issue_ticket t1
            LEFT JOIN info_analyze_problem t2 ON t1.ist_id = t2.ist_id 
            AND t2.iap_status_flg = 3 
        WHERE
            t1.ist_id = '$ist_Id' ";

        $query_analyz = $this->db->query($sql_show_analyz, array($ist_Id));
        $data_analyz = $query_analyz->result();

        $sql_show_prevention = "SELECT
        t2.ipr_suggestions,
        t2.ipr_operated,
        DATE_FORMAT(t2.ipr_schedule, '%d/%m/%Y') AS ipr_schedule
                
        FROM
        info_issue_ticket t1
        LEFT JOIN info_prevention_recurrence t2 ON t1.ist_id = t2.ist_id
        AND t2.ipr_status_flg = 3
        WHERE
        t1.ist_id = '$ist_Id'";

        $query_prevention = $this->db->query($sql_show_prevention, array($ist_Id));
        $data_prevention = $query_prevention->result();

        $sql_show_delivery = "SELECT
        t2.mde_id,
        t2.ide_detail
        FROM
        info_issue_ticket t1
        LEFT JOIN info_delivery_equipment t2 ON t1.ist_id = t2.ist_id 
        AND t2.ide_status_flg = 3 
        WHERE
        t1.ist_id = '$ist_Id'";

        $query_delivery = $this->db->query($sql_show_delivery, array($ist_Id));
        $data_delivery = $query_delivery->result();


        if ($query->num_rows() > 0) {
            return array('result' => true, 'info_issue' => $info_issue, 'data_problem' => $data_problem, 'data_inspection' => $data_inspection, 'data_trouble' => $data_trouble, 'data_req_part' => $data_req_part, 'data_analyz' => $data_analyz, 'data_prevention' => $data_prevention, 'data_delivery' => $data_delivery);
        } else {
            return array('result' => false);
        }
    }

}