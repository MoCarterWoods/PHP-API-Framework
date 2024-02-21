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

    public function save_equipment($data, $sess)
    {
        $areapd = $data["AreaPd"];
        $arealine = $data["AreaLine"];
        $areaother = $data["AreaOther"];
        $processfun = $data["ProcFunc"];
        $toolsys = $data["ToolSys"];
        $maker = $data["Maker"];
        $model = $data["Model"];
        $id = $data["ist_Id"];

        $sql_update_equip = "UPDATE info_issue_ticket 
        SET ist_pd = '$areapd',
        ist_line_cd = '$arealine',
        ist_area_other = '$areaother',
        ist_process = '$processfun',
        ist_tool = '$toolsys',
        ist_maker = '$maker',
        ist_model = '$model',
        ist_updated_date = NOW(),
        ist_updated_by = '$sess' 
        WHERE
            ist_id = '$id';";

        $query_update_equip = $this->db->query($sql_update_equip);



        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
        } else {
            return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
        }
        // หากไม่มีการอัพเดทใด ๆ สำเร็จ
        return array('result' => 0);
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

        $sql_show_image = "SELECT
        ipc_pic_1,
        ipc_pic_2,
        ipc_pic_3
    FROM
        info_problem_condition 
    WHERE
        ist_id = '$id' 
        AND ipc_status_flg != 0  LIMIT 1";

        $query_image = $this->db->query($sql_show_image, array($id));
        $data_image = $query_image->result();


        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data, 'data_image' => $data_image);
        } else {
            return array('result' => false);
        }
    }

    public function chkBox_problem()
    {
        $sql_pb = "SELECT 
        mpc_id,
        mpc_name_eng,
        mpc_name_thai,
        mpc_detail
    FROM 
        mst_problem_condition 
    WHERE 
        mpc_type = 5 AND mpc_status_flg = 1;";
        $query = $this->db->query($sql_pb);
        $data = $query->result();

        return $data;
    }

    public function radio_jobtype()
    {
        $sql_jbt = "SELECT 
        mjt_id,
        mjt_name_eng,
        mjt_name_thai,
        mjt_status_flg
    FROM 
        mst_job_type 
    WHERE 
        mjt_status_flg = 1;";
        $query = $this->db->query($sql_jbt);
        $data = $query->result();

        return $data;
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
        mim_id,
        mim_name_eng,
        mim_name_thai,
        mim_status_flg,
        mim_detail
    FROM 
        mst_inspection_method 
    WHERE 
        mim_type = 1 AND mim_status_flg = 1;";


        $query = $this->db->query($sql_show_insp, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function chkBox_inspection()
    {
        $sql_inspec = "SELECT 
        mim_id,
        mim_name_eng,
        mim_name_thai,
        mim_status_flg,
        mim_detail
    FROM 
        mst_inspection_method 
    WHERE 
        mim_type = 5 AND mim_status_flg = 1;";
        $query = $this->db->query($sql_inspec);
        $data = $query->result();

        return $data;
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

    public function show_required_parts($data)
    {
        $id = $data["ist_Id"];

        $sql_show_pb = "SELECT 
        t1.ist_id,
        t2.irp_id,
        t2.irp_name,
        t2.irp_maker,
        t2.irp_model,
        t2.irp_type,
        t2.irp_qty,
        DATE_FORMAT(t2.irp_withdraw_time, '%Y-%m-%d') AS irp_withdraw_time,
        t2.irp_withdraw_qty,
        DATE_FORMAT(t2.irp_order_time, '%Y-%m-%d') AS irp_order_time,
        t2.irp_order_qty,
        DATE_FORMAT(t2.irp_received_time, '%Y-%m-%d') AS irp_received_time,
        t2.irp_received_qty,
        t2.irp_status_flg
    FROM 
        info_issue_ticket t1
    LEFT JOIN
        info_required_parts t2 ON t1.ist_id = t2.ist_id
    WHERE
        t1.ist_id = '$id'
        AND (
            t2.irp_status_flg = 1 
            OR t2.irp_status_flg = 3
        )
    ";


        $query = $this->db->query($sql_show_pb);
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }

    public function save_required($data, $sess)
    {
        $id = $data["ist_Id"];

        $sql_close_req = "UPDATE info_required_parts 
            SET irp_status_flg = 0 , 
            irp_updated_date = NOW() , 
            irp_updated_by = '$sess' 
            WHERE ist_id = '$id'";
        $query_close_req = $this->db->query($sql_close_req);

        $partRqArray = json_decode($data['rowDataArray'], true);
        for ($i = 0; $i < count($partRqArray); $i++) {
            $data = $partRqArray[$i];
            $maker = $data['Maker'];
            $model = $data['Model'];
            $name = $data['Name'];
            $order = $data['Order'];
            $orderQty = $data['OrderQty'];
            $qty = $data['Qty'];
            $received = $data['Received'];
            $receivedQty = $data['ReceivedQty'];
            $stock = $data['Stock'];
            $stockQty = $data['StockQty'];
            $type = $data['Type'];

            // ทำการ query
            $sql_insert_req = "INSERT INTO info_required_parts (
            ist_id,
            irp_name,
            irp_maker,
            irp_model,
            irp_type,
            irp_qty,
            irp_withdraw_time,
            irp_withdraw_qty,
            irp_order_time,
            irp_order_qty,
            irp_received_time,
            irp_received_qty,
            irp_status_flg,
            irp_created_date,
            irp_created_by 
        )
        VALUES
            (
                '$id',
                '$name',
                '$maker',
                '$model',
                '$type',
                '$qty',
                '$stock',
                '$stockQty',
                '$order',
                '$orderQty',
                '$received',
                '$receivedQty',
                3,
                NOW(),
                '$sess' 
            )";

            // ทำการ query
            $query_insert_req = $this->db->query($sql_insert_req);

            // ตรวจสอบว่า query สำเร็จหรือไม่
            if ($this->db->affected_rows() > 0) {
                $reqPart_status = 1;
            } else {
                $reqPart_status = 0;
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
        t2.iap_path
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


    public function show_prevention($data)
    {
        $id = $data["ist_Id"];

        $sql_show_preven = "SELECT
        t1.ist_id,
        t2.ipr_id,
        t2.ipr_suggestions,
        t2.ipr_operated,
        DATE_FORMAT( t2.ipr_schedule, '%Y-%m-%d' ) AS ipr_schedule,
        t2.ipr_status_flg 
    FROM
        info_issue_ticket t1
        LEFT JOIN info_prevention_recurrence t2 ON t1.ist_id = t2.ist_id 
    WHERE
        t1.ist_id = '$id' 
        AND (
            t2.ipr_status_flg = 1 
        OR t2.ipr_status_flg = 3 
        )";


        $query = $this->db->query($sql_show_preven);
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }



    public function show_delivery($data)
    {
        $id = $data["ist_Id"];

        $sql_show_pb = "SELECT 
        t1.ist_id,
        t2.ide_id,
        t2.mde_id,
        t2.ide_detail,
        t2.ide_status_flg
    FROM 
        info_issue_ticket t1
    LEFT JOIN
    info_delivery_equipment t2 ON t1.ist_id = t2.ist_id
    WHERE
        t1.ist_id = '$id' AND (t2.ide_status_flg = 1 OR t2.ide_status_flg = 3)";


        $query = $this->db->query($sql_show_pb, array($id));
        $data = $query->result();

        if ($query->num_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }
    // ========================================================================================

    public function save_delivery($data, $sess)
    {
        $id = $data["ist_Id"];

        // Clear previous selections
        $this->db->query("UPDATE info_delivery_equipment 
        SET ide_status_flg = 0 , 
        ide_updated_date = NOW() , 
        ide_updated_by = '$sess' 
        WHERE ist_id = '$id'");


        $checkboxes = array(
            "Checkval1", "Checkval2", "Checkval3", "Checkval4", "Checkval5",
            "Checkval6"
        );
        foreach ($checkboxes as $checkbox) {
            if (!empty($data[$checkbox])) {
                $mde_id = $data[$checkbox];
                if ($checkbox === "Checkval1") {
                    // If it's Checkval11, use Detailcheck11 for iap_detail
                    $ide_detail = $data["Detailcheck11"];
                    $this->db->query("INSERT INTO info_delivery_equipment (
                                        ist_id,
                                        mde_id,
                                        ide_detail,
                                        ide_status_flg,
                                        ide_created_date,
                                        ide_created_by
                                        ) VALUES ('$id', $mde_id, '$ide_detail', 3, NOW(), '$sess')");
                } else {
                    $this->db->query("INSERT INTO info_delivery_equipment (
                                        ist_id,
                                        mde_id,
                                        ide_status_flg,
                                        ide_created_date,
                                        ide_created_by
                                        ) VALUES ('$id', $mde_id, 3, NOW(), '$sess')");
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

    public function drop_worker($data)
    {
        $id = $data["ist_Id"];

        $sql_worker = "SELECT
        t1.swa_id,
        t1.swa_emp_code
    FROM
        sys_worker_app t1
    LEFT JOIN
        log_manage_worker t2 ON t1.swa_id = t2.lmw_worker 
    WHERE
        t2.ist_id = '$id' AND t2.lmw_status_flg != 0;";
        $query = $this->db->query($sql_worker);
        $data = $query->result();

        return $data;
    }


    public function all_worker()
    {

        $sql_worker = "SELECT * FROM sys_worker_app WHERE swa_status_flg =1;";
        $query = $this->db->query($sql_worker);
        $data = $query->result();

        return $data;
    }

    public function save_worker($data, $sess)
    {
        $id = $data["ist_Id"];

        // ทำการอัปเดตข้อมูลในตาราง log_manage_worker โดยเปลี่ยน lmw_status_flg เป็น 0
        $update_sql = "UPDATE log_manage_worker 
        SET lmw_status_flg = 0,
        lmw_updated_date = NOW(),
        lmw_updated_by = '$sess' 
        WHERE
            ist_id = '$id'";
        $this->db->query($update_sql);

        // ทำการเตรียมข้อมูลสำหรับการเพิ่มข้อมูลใหม่
        $swa_emp_codes = array();
        if (isset($data['valuesOnly']) && is_array($data['valuesOnly'])) {
            foreach ($data['valuesOnly'] as $item) {
                $swa_emp_codes[] = $item['value'];
            }
        }

        // เตรียมค่าสำหรับใส่ลงใน VALUES ของคำสั่ง SQL INSERT
        $values = array();
        foreach ($swa_emp_codes as $swa_emp_code) {
            $values[] = "('$id', '$swa_emp_code', 3, '$sess', NOW())";
        }

        $values_str = implode(", ", $values);

        // ทำการเพิ่มข้อมูลใหม่ลงในตาราง log_manage_worker
        $insert_sql = "INSERT INTO log_manage_worker (ist_id, lmw_worker, lmw_status_flg, lmw_created_by, lmw_created_date) 
                       VALUES $values_str";
        $this->db->query($insert_sql);

        // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
        if ($this->db->affected_rows() > 0) {
            return array('result' => 1); // ส่งค่ากลับว่าการทำงานเสร็จสมบูรณ์
        } else {
            return array('result' => 0); // ส่งค่ากลับว่าไม่มีการอัพเดทหรือไม่สามารถอัพเดทได้
        }
    }
}
