<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_user() {
        $sql = "SELECT sys_account.sa_id, sys_account.sa_fristname, sys_account.sa_lastname
        , sys_account.sa_emp_code, sys_account.spg_id , sys_account.sa_email ,sys_account.sa_created_date,sys_account.sa_status_flg ,
        sys_permission_group.spg_name
        FROM sys_account
        INNER JOIN sys_permission_group
        ON sys_account.spg_id = sys_permission_group.spg_id;";

        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }

    public function show_drop_down() {
    $sql1 = "SELECT spg_id,spg_name From sys_permission_group";
        $query = $this->db->query($sql1);
       
        foreach ($query->result() as $key => $value) {
            $arr['permission'][] = $value;
        }
        $sql2 = "SELECT mpc_id,mpc_code,mpc_name From mst_plant_code";
        $query = $this->db->query($sql2);
       
        foreach ($query->result() as $key => $value) {
            $arr['plantcode'][] = $value;
        }
        
        return $arr;
    }



    public function insert_user($data, $sess) {
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];
    
        $sql_check_duplicate = "SELECT * FROM sys_account WHERE sa_emp_code = '$empcode'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);
    
        // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $sql_insert = "INSERT INTO sys_account (sa_emp_code, sa_emp_password, sa_fristname, sa_lastname, sa_email, spg_id, mpc_id, sa_created_by, sa_created_date, sa_status_flg) 
                           VALUES ('$empcode', '$password', '$firstname', '$lastname', '$email', '$permisgroup', '$plant', '$sess', NOW(), 1)";
    
            $query = $this->db->query($sql_insert);
    
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }
    
    
    public function show_show_acc($data) {
        $id = $data["id"];
        // return $id;
        // exit;
        
        $sql_show_acc = "SELECT * FROM sys_account WHERE sa_id = '$id';";

        $query = $this->db->query($sql_show_acc);
        $data = $query->row();
        if ($this->db->affected_rows()>0) {
            return array('result'=> true,'data'=>$data);
        }
        else{
            return array('result' => false);
        }
    }

    public function update_status($data) {
        $id = $data["saId"];
        $stt = $data["newStatus"];


        // return $data;
        // exit;
        $sql_show_acc = "UPDATE sys_account
        SET sa_status_flg = '$stt'
        WHERE sa_id = '$id';
        ";
        
        $query = $this->db->query($sql_show_acc);
        if ($this->db->affected_rows()>0) {
            return array('result' => 1);
        }
        else{
            return array('result' => 0);
        }
    }


public function update_flg($data){
    $stFlg = $data["newStatus"];
    $saId = $data["saId"];

    $sql = "UPDATE sys_account 
    SET sa_status_flg = '$stFlg'
    WHERE sa_id = '$saId';";
    
    $query=$this->db->query($sql);
    if($this->db->affected_rows()>0){
        return true;
    }else{
        return false;

    }
    }


    public function show_update_acc($data, $sess) {
        $id = $data["EmpId"];
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $permisgroup = $data["EmpPermission"];
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $flag = $data["EmpFlag"];
        $plant = $data["EmpPlantCode"];

        // return $data;
        // exit;
        $sql_show_acc = "UPDATE sys_account
        SET sa_emp_code = '$empcode', 
        sa_emp_password = '$password', 
        spg_id = '$permisgroup', 
        sa_fristname = '$firstname',
        sa_lastname = '$lastname',
        sa_email = '$email',
        sa_status_flg = '$flag',
        sa_updated_date = NOW(),
        sa_updated_by = '$sess',
        mpc_id = '$plant'
        WHERE sa_id = '$id';
        ";
        
        $query = $this->db->query($sql_show_acc);
        if ($this->db->affected_rows()>0) {
            return array('result' => 1);
        }
        else{
            return array('result' => 0);
        }
    }
    

    public function update_user($data, $sess) {
        $empcode = $data["EmpCode"];
        $password = ($data["EmpPassword"] != '') ? md5($data["EmpPassword"]) : NULL;
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];
    
        $data_chk_user = $this->get_user_data($empcode);
    
        if ($data_chk_user->sa_emp_password == $password || $password === NULL) {
            $sql_update_nopass = "
                UPDATE sys_account
                SET sa_emp_code= '$empcode', 
                    sa_fristname= '$firstname',
                    sa_lastname= '$lastname',
                    sa_email= '$email',
                    spg_id= '$permisgroup',
                    mpc_id= '$plant',
                    sa_updated_date= NOW(),
                    sa_updated_by= '$sess'
                WHERE sa_emp_code= '$empcode';
            ";
    
            $query_nopass = $this->db->query($sql_update_nopass);
            
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1);
            } else {
                return array('result' => 0);
            }
        } else {
            $sql_update = "
                UPDATE sys_account
                SET sa_emp_code= '$empcode', 
                    sa_emp_password= '$password',
                    sa_fristname= '$firstname',
                    sa_lastname= '$lastname',
                    sa_email= '$email',
                    spg_id= '$permisgroup',
                    mpc_id= '$plant',
                    sa_updated_date= NOW(),
                    sa_updated_by= '$sess'
                WHERE sa_emp_code= '$empcode';
            ";
    
            $query_update = $this->db->query($sql_update);
    
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // อัปเดตสำเร็จ
            } else {
                return array('result' => 0); // ไม่สามารถอัปเดต
            }
        }
    }
    
    private function get_user_data($empcode) {
        $sql_select = "
            SELECT *
            FROM sys_account
            WHERE sa_emp_code = '$empcode'
        ";
    
        $query_select = $this->db->query($sql_select);
        return $query_select->row();
    }


    public function show_upd_User($data, $sess) {
        $empcode = $data["EmpCode"];
        $password = md5($data["EmpPassword"]);
        $firstname = $data["EmpFirstName"];
        $lastname = $data["EmpLastName"];
        $email = $data["EmpEmail"];
        $permisgroup = $data["EmpPermission"];
        $plant = $data["EmpPlantCode"];
    
        $sql_check_duplicate = "SELECT * FROM sys_account WHERE sa_emp_code = '$empcode'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);
    
        // ใช้ num_rows() เพื่อนับจำนวนแถวที่ถูกพบ
        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $sql_insert = "INSERT INTO sys_account (sa_emp_code, sa_emp_password, sa_fristname, sa_lastname, sa_email, spg_id, mpc_id, sa_created_by, sa_created_date, sa_status_flg) 
                           VALUES ('$empcode', '$password', '$firstname', '$lastname', '$email', '$permisgroup', '$plant', '$sess', NOW(), 1)";
    
            $query = $this->db->query($sql_insert);
    
            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }
    
}
    