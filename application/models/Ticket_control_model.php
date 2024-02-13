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
        t1.ist_request_by,
        t2.mjt_name_eng,
        t2.mjt_name_thai,
        GROUP_CONCAT( DISTINCT t3.lmw_id ) AS lmw_id,
        GROUP_CONCAT( DISTINCT t4.swa_fristname ) AS swa_fristname,
        GROUP_CONCAT( DISTINCT t4.swa_emp_code ) AS swa_emp_code,
        t5.mts_name,
        CASE
         WHEN t1.ist_line_cd OR t1.ist_area_other IS NULL THEN
         1 ELSE 3
        END AS equipment_status,
        t6.ipc_status_flg AS ploblem_status,
        CASE
         WHEN t1.mjt_id  IS NULL THEN
         1 ELSE 3
        END AS jopType_status,
        t7.iim_status_flg AS inspection_status,
        t8.it_status_flg AS troubleshooting_status,
        t9.irp_status_flg AS rqPart_status,
        t10.iap_status_flg AS analyze_status,
        t11.ipr_status_flg AS prevention_status,
        t12.ide_status_flg AS delivery_status,
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

    public function accept_ticket($data, $sess)
    {
        $stAccept = $data["newStatus"];
        $acId = $data["acId"];

        $sql = "UPDATE info_issue_ticket 
        SET ist_status_flg = '$stAccept', ist_accept_by = '$sess', ist_updated_date = NOW(), ist_updated_by = '$sess'
        WHERE ist_id = '$acId';";

        $query = $this->db->query($sql);
        return $this->db->affected_rows() > 0;
    }


    public function cancel_ticket($data, $sess)
    {
        $DtCancle = $data["DetailCancle"];
        $stCancle = $data["newStatus"];
        $ccID = $data["ccID"];

        $sql = "UPDATE info_issue_ticket 
                SET ist_status_flg = '$stCancle', ist_cancle_detail = '$DtCancle', ist_updated_date = NOW(), ist_updated_by = '$sess'
                WHERE ist_id = '$ccID';";

        $query = $this->db->query($sql);
        return $this->db->affected_rows() > 0 ? 1 : 2;
    }

    public function show_equipment($data)
    {
        $id = $data["ist_Id"];
        $sql_show_eq = "SELECT 
        ist_pd,
        ist_line_cd,
        ist_area_other,
        ist_process,
        ist_tool,
        ist_maker,
        ist_model
         
        FROM info_issue_ticket 
        WHERE ist_id = '$id';";

        $query = $this->db->query($sql_show_eq);
        $data = $query->row();

        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function show_problem($data)
    {
        $id = $data["ist_Id"];

        $sql_show_pb = "SELECT 
        t1.ist_id,
        t2.ipc_id,
        t2.mpc_id,
        t2.ipc_detail,
        t2.ipc_pic_1,
        t2.ipc_pic_2,
        t2.ipc_pic_3,
        t2.ipc_path,
        CASE WHEN t2.mpc_id IN (7, 8, 9) THEN 'checked' ELSE NULL END AS checked
    FROM 
        info_issue_ticket t1
    LEFT JOIN
        info_problem_condition t2 ON t1.ist_id = t2.ist_id
    WHERE
        t1.ist_id = '$id' AND (t2.ipc_status_flg = 1 OR t2.ipc_status_flg = 3)";


        $query = $this->db->query($sql_show_pb, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }


    public function save_problem($data, $sess)
    {
        $problem = $data["ProblemSel"];
        $detail = $data["ProblemDetail"];
        $chk1 = $data["PbCheck1"];
        $chk2 = $data["PbCheck2"];
        $chk3 = $data["PbCheck3"];
        $id = $data["ist_Id"];
        
        $sql_update_problem = "UPDATE info_issue_ticket 
            SET mpc_id = '$problem' , ist_updated_date = NOW() , ist_updated_by = '$sess' WHERE ist_id = '$id'";
    
        $query_update_problem = $this->db->query($sql_update_problem);
    
        $sql_close_problem = "UPDATE info_problem_condition 
            SET ipc_status_flg = 0 , 
            ipc_updated_date = NOW() , 
            ipc_updated_by = '$sess' 
            WHERE ist_id = '$id'";
        $query_close_problem = $this->db->query($sql_close_problem);
    
        $sql_insert_problem = "INSERT INTO info_problem_condition
            (ist_id, 
            mpc_id, 
            ipc_detail, 
            ipc_status_flg, 
            ipc_created_date, 
            ipc_created_by,
            ipc_updated_date,
            ipc_updated_by
            )
            VALUES
            ('$id', '$problem', '$detail', 3, NOW(), '$sess', NOW(), '$sess')";
        $query_insert_problem = $this->db->query($sql_insert_problem);
    
        if ($chk1 !== '') {
            $sql_insert_probelm_1 = "INSERT INTO info_problem_condition
                (ist_id, 
                mpc_id, 
                ipc_status_flg, 
                ipc_created_date, 
                ipc_created_by,
                ipc_updated_date,
                ipc_updated_by
                )
                VALUES
                ('$id', '$chk1', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_probelm_1 = $this->db->query($sql_insert_probelm_1);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($chk2 !== '') {
            $sql_insert_probelm_2 = "INSERT INTO info_problem_condition
                (ist_id, 
                mpc_id, 
                ipc_status_flg, 
                ipc_created_date, 
                ipc_created_by,
                ipc_updated_date,
                ipc_updated_by
                )
                VALUES
                ('$id', '$chk2', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_probelm_2 = $this->db->query($sql_insert_probelm_2);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($chk3 !== '') {
            $sql_insert_probelm_3 = "INSERT INTO info_problem_condition
                (ist_id, 
                mpc_id, 
                ipc_status_flg, 
                ipc_created_date, 
                ipc_created_by,
                ipc_updated_date,
                ipc_updated_by
                )
                VALUES
                ('$id', '$chk3', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_probelm_3 = $this->db->query($sql_insert_probelm_3);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
        } else {
            return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
        }
        // หากไม่มีการอัพเดทใด ๆ สำเร็จ
        return array('result' => 0);
    }
    





    public function show_jobtype($data)
    {
        $id = $data["ist_Id"];

        $sql_show_jt = "SELECT

        ist_id,
        mjt_id
        FROM
        info_issue_ticket
        WHERE
        ist_id = '$id'";

        $query = $this->db->query($sql_show_jt, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function save_jobtype($data, $sess)
    {
        $id = $data["ist_Id"];
        
        $radioJT = array(
            "Jtradio1" => $data["Jtradio1"],
            "Jtradio2" => $data["Jtradio2"],
            "Jtradio3" => $data["Jtradio3"],
            "Jtradio4" => $data["Jtradio4"]
        );
    
        foreach ($radioJT as $key => $value) {
            if ($value !== '') {
                $sql = "UPDATE info_issue_ticket
                    SET mjt_id = ? ,ist_updated_date = NOW() , ist_updated_by = ?
                    WHERE ist_id = ?";
    
                $query_insert_jobtype = $this->db->query($sql, array($value, $sess, $id));
    
                if ($this->db->affected_rows() > 0) {
                    return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
                } else {
                    return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
                }
            }
        }
    
        // หากไม่มีการอัพเดทใด ๆ สำเร็จ
        return array('result' => 0);
    }
    


    public function show_inspection($data)
    {
        $id = $data["ist_Id"];

        $sql_show_insp = "SELECT 
        t1.ist_id,
        t2.iim_id,
        t2.mim_id,
        t2.iim_detail,
        t2.iim_pic_1,
        t2.iim_pic_2,
        t2.iim_pic_3,
        t2.iim_path,
        CASE WHEN t2.mim_id IN (6, 7, 8) THEN 'checked' ELSE NULL END AS checked
    FROM 
        info_issue_ticket t1
    LEFT JOIN
    info_inspection_method t2 ON t1.ist_id = t2.ist_id
    WHERE
        t1.ist_id = '$id' AND (t2.iim_status_flg = 1 OR t2.iim_status_flg = 3)";


        $query = $this->db->query($sql_show_insp, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }


    public function save_inspection($data, $sess)
    {
        $inspec = $data["InspecSel"];
        $detail = $data["InspecDetail"];
        $chk1 = $data["InsCheck1"];
        $chk2 = $data["InsCheck2"];
        $chk3 = $data["InsCheck3"];
        $id = $data["ist_Id"];
        
        $sql_update_inspec = "UPDATE info_issue_ticket 
            SET mim_id = '$inspec' , ist_updated_date = NOW() , ist_updated_by = '$sess' WHERE ist_id = '$id'";
    
        $query_update_inspec = $this->db->query($sql_update_inspec);
    
        $sql_close_inspec = "UPDATE info_inspection_method 
            SET iim_status_flg = 0 , 
            iim_updated_date = NOW() , 
            iim_updated_by = '$sess' 
            WHERE ist_id = '$id'";
        $query_close_inspec = $this->db->query($sql_close_inspec);
    
        $sql_insert_inspec = "INSERT INTO info_inspection_method
            (ist_id, 
            mim_id, 
            iim_detail, 
            iim_status_flg, 
            iim_created_date, 
            iim_created_by,
            iim_updated_date,
            iim_updated_by
            )
            VALUES
            ('$id', '$inspec', '$detail', 3, NOW(), '$sess', NOW(), '$sess')";
        $query_insert_inspec = $this->db->query($sql_insert_inspec);
    
        if ($chk1 !== '') {
            $sql_insert_inspec_1 = "INSERT INTO info_inspection_method
            (ist_id, 
            mim_id, 
            iim_detail, 
            iim_status_flg, 
            iim_created_date, 
            iim_created_by,
            iim_updated_date,
            iim_updated_by
            )
                VALUES
                ('$id', '$chk1', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_inspec_1 = $this->db->query($sql_insert_inspec_1);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($chk2 !== '') {
            $sql_insert_inspec_2 = "INSERT INTO info_inspection_method
            (ist_id, 
            mim_id, 
            iim_detail, 
            iim_status_flg, 
            iim_created_date, 
            iim_created_by,
            iim_updated_date,
            iim_updated_by
            )
                VALUES
                ('$id', '$chk2', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_inspec_2 = $this->db->query($sql_insert_inspec_2);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($chk3 !== '') {
            $sql_insert_inspec_3 = "INSERT INTO info_inspection_method
            (ist_id, 
            mim_id, 
            iim_detail, 
            iim_status_flg, 
            iim_created_date, 
            iim_created_by,
            iim_updated_date,
            iim_updated_by
            )
                VALUES
                ('$id', '$chk3', 3, NOW(), '$sess', NOW(), '$sess')";
    
            $query_insert_inspec_3 = $this->db->query($sql_insert_inspec_3);
    
            if ($this->db->affected_rows() > 0) {
    
            } else {
    
            }
        }
    
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
        } else {
            return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
        }
        // หากไม่มีการอัพเดทใด ๆ สำเร็จ
        return array('result' => 0);
    }

    public function show_analyze($data)
    {
        $id = $data["ist_Id"];

        $sql_show_pb = "SELECT 
        t1.ist_id,
        t2.iap_id,
        t2.map_id,
        t2.iap_detail,
        t2.iap_pic1,
        t2.iap_pic2,
        t2.iap_pic3,
        t2.iap_path,
        CASE WHEN t2.map_id IN (1, 2, 3, 4, 5,6,7,8,9,10,11) THEN 'checked' ELSE NULL END AS checked
    FROM 
        info_issue_ticket t1
    LEFT JOIN
    info_analyze_problem t2 ON t1.ist_id = t2.ist_id
    WHERE
        t1.ist_id = '$id' AND (t2.iap_status_flg = 1 OR t2.iap_status_flg = 3)";


        $query = $this->db->query($sql_show_pb, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function save_analyze($data, $sess)
    {
        $analyzdetail = $data["Mdetail"];
        $id = $data["ist_Id"];
    
        // Clear previous selections
        $this->db->query("UPDATE info_analyze_problem 
                            SET iap_status_flg = 0 , 
                            iap_updated_date = NOW() , 
                            iap_updated_by = '$sess' 
                            WHERE ist_id = '$id'");
    
        // Insert new analyze detail
        $this->db->query("INSERT INTO info_analyze_problem
                            (ist_id, 
                            iap_detail, 
                            iap_status_flg, 
                            iap_created_date, 
                            iap_created_by,
                            iap_updated_date,
                            iap_updated_by
                            )
                            VALUES
                            ('$id','$analyzdetail', 3, NOW(), '$sess', NOW(), '$sess')");
    
        // Insert checkboxes
        $checkboxes = array(
            "Checkval1", "Checkval2", "Checkval3", "Checkval4", "Checkval5",
            "Checkval6", "Checkval7", "Checkval8", "Checkval9", "Checkval10", "Checkval11"
        );
        foreach ($checkboxes as $checkbox) {
            if (!empty($data[$checkbox])) {
                $map_id = $data[$checkbox];
                if ($checkbox === "Checkval11") {
                    // If it's Checkval11, use Detailcheck11 for iap_detail
                    $iap_detail = $data["Detailcheck11"];
                    $this->db->query("INSERT INTO info_analyze_problem (
                                        ist_id,
                                        map_id,
                                        iap_detail,
                                        iap_status_flg,
                                        iap_created_date,
                                        iap_created_by
                                        ) VALUES ('$id', $map_id, '$iap_detail', 3, NOW(), '$sess')");
                } else {
                    $this->db->query("INSERT INTO info_analyze_problem (
                                        ist_id,
                                        map_id,
                                        iap_status_flg,
                                        iap_created_date,
                                        iap_created_by
                                        ) VALUES ('$id', $map_id, 3, NOW(), '$sess')");
                }
            }
        }
    
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
        } else {
            return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
        }
        // หากไม่มีการอัพเดทใด ๆ สำเร็จ
        return array('result' => 0);
    }
    

}
