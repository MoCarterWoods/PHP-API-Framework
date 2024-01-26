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


    public function save_issue($data, $sess)
{
    $code = $this->get_reqCode();
    $areapd = $data["AreaPd"];
    $arealine = $data["AreaLine"];
    $areaother = $data["AreaOther"];
    $processfun = $data["ProcFunc"];
    $toolsys = $data["ToolSys"];
    $maker = $data["Maker"];
    $model = $data["Model"];
    $jobtype = $data["JobType"];
    $prodcon = $data["ProbCon"];
    $prodcondetail = $data["ProbConDetail"];
    $prodconpic1 = $data["ProbConPic1"];
    $prodconpic2 = $data["ProbConPic2"];
    $prodconpic3 = $data["ProbConPic3"];
    $ispec = $data["InspecMethod"];
    $ispecdetail = $data["InspecDetail"];
    $ispecpic1 = $data["InspecPic1"];
    $ispecpic2 = $data["InspecPic2"];
    $ispecpic3 = $data["InspecPic3"];
    $trouble = $data["Trouble"];
    $troubledetail = $data["TroubleDetail"];
    $troublepic1 = $data["TroublePic1"];
    $troublepic2 = $data["TroublePic2"];
    $troublepic3 = $data["TroublePic3"];

    $troublepic3 = $data["DetailAnalyze"];
    $troublepic3 = $data["analyzeCheckval1"];
    $troublepic3 = $data["analyzeCheckval2"];
    $troublepic3 = $data["analyzeCheckval3"];
    $troublepic3 = $data["analyzeCheckval4"];
    $troublepic3 = $data["analyzeCheckval5"];
    $troublepic3 = $data["analyzeCheckval6"];

    $sql_insert_issue_ticket = "INSERT INTO info_issue_ticket (
        ist_type,
        ist_pd,
        ist_line_cd,
        ist_area_other,
        ist_process,
        ist_tool,
        ist_maker,
        ist_model,
        ist_job_no,
        ist_date,
        mjt_id,
        mpc_id,
        mim_id,
        mt_id,
        ist_request_by,
        ist_request_time,
        ist_status_flg,
        ist_created_date,
        ist_created_by) 
        VALUES (2,'$areapd','$arealine','$areaother','$processfun','$toolsys','$maker','$model','$code',NOW(),'$jobtype','$prodcon','$ispec','$trouble','$sess',NOW(),1,NOW(),'$sess')";

    $query_insert_issue_ticket = $this->db->query($sql_insert_issue_ticket);

    if ($this->db->affected_rows() > 0) {
        // ดึง ist_id ที่เพิ่งถูกสร้างขึ้น
        $sql_select_ist_id = "SELECT ist_id FROM info_issue_ticket WHERE ist_created_by = '$sess' ORDER BY ist_id DESC LIMIT 1";
        $query_select_ist_id = $this->db->query($sql_select_ist_id);

        if ($query_select_ist_id->num_rows() > 0) {
            $row = $query_select_ist_id->row();
            $ist_id = $row->ist_id;

            // INSERT INTO info_problem_condition
            $sql_insert_problem_condition = "INSERT INTO info_problem_condition (
                ist_id,
                mpc_id,
                ipc_detail,
                ipc_pic_1,
                ipc_pic_2,
                ipc_pfc_3,
                ipc__path,
                ipc_status_flg,
                ipc_created_date,
                ipc_created_by
            ) VALUES ('$ist_id','$prodcon','$prodcondetail','$prodconpic1','$prodconpic2','$prodconpic3','ipc__path',1,NOW(),'$sess')";

            $query_insert_problem_condition = $this->db->query($sql_insert_problem_condition);


            $sql_insert_troubleshooting = "INSERT INTO info_troubleshooting (ist_id,
            mt_id,
            it_detail,
            it_pic_1,
            it_pic_2,
            it_pfc_3,
            it_path,
            it_status_flg,
            it_created_date,
            it_created_by
            ) VALUES ('$ist_id','$trouble','$troubledetail','$troublepic1','$troublepic2','$troublepic3','it_path',1,NOW(),'$sess')";

            $query_insert_troubleshooting = $this->db->query($sql_insert_troubleshooting);

            if ($this->db->affected_rows() > 0) {
                // INSERT INTO info_inspection_method
                $sql_insert_inspection_method = "INSERT INTO info_inspection_method (
                    ist_id,
                    mim_id,
                    iim_detail,
                    iim_pic_1,
                    iim_pic_2,
                    iim_pfc_3,
                    iim_path,
                    iim_status_flg,
                    iim_created_date,
                    iim_created_by
                ) VALUES ('$ist_id','$ispec','$ispecdetail','$ispecpic1','$ispecpic2','$ispecpic3','iim_path',1,NOW(),'$sess')";

                $query_insert_inspection_method = $this->db->query($sql_insert_inspection_method);

                if ($this->db->affected_rows() > 0) {
                    return array('result' => 1, 'ist_id' => $ist_id); // Insert สำเร็จ
                } else {
                    return array('result' => 0, 'message' => 'Insert ล้มเหลวในตาราง info_inspection_method');
                }
            } else {
                return array('result' => 0, 'message' => 'Insert ล้มเหลวในตาราง info_problem_condition');
            }
        } else {
            return array('result' => 0, 'message' => 'ไม่สามารถดึง ist_id ได้');
        }
    } else {
        return array('result' => 0, 'message' => 'Insert ล้มเหลวในตาราง info_issue_ticket');
    }
}



        public function get_reqCode(){
            $month = date('Ym');
            $sql = "SELECT * FROM info_issue_ticket WHERE ist_job_no like '$month%'";
            $query = $this->db->query($sql);
            $result = $query->result_array();
            $row = count($result);
            $code = $month;
            if($row != 0){
                $row++;
                if($row>=1000){$code .= $row;}
                else if($row>=100){$code .= "0".$row;}
                else if($row>=10){$code .= "00".$row;}
                else if($row>=1){$code .= "000".$row;}
            }else{
                $code .= "0001";
            }
            return $code;
        }

    
}