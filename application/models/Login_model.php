<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    // {empcoede : 124,password:123}
    public function chk_login_db($data) {
        $empcode = $data["empCode"];
        $password = md5($data["empPassword"]);


        $sql_chk_empCode = "SELECT sa_id,sa_emp_code,sa_status_flg FROM sys_account WHERE sa_emp_code = '$empcode'";     
        $query_chk_empCode = $this->db->query($sql_chk_empCode);

        
        // return $query_chk_empCode->num_rows() ;

        if ($query_chk_empCode->num_rows() > 0 ) { // $data_chk_empCode[0]->su_status_flg == 1
            $data_chk_empCode = $query_chk_empCode->result();

            if ($data_chk_empCode[0]->sa_status_flg == 1) {
                $sql_chk_login = "SELECT * FROM sys_account WHERE sa_emp_code = '$empcode' AND sa_emp_password = '$password'";
                
                $query_chk_login = $this->db->query($sql_chk_login);
                         
                
                if ($query_chk_login->num_rows() > 0) {
                    $query_login_data = $query_chk_login->row();
                

                    
                    $sql_chk_status_permis = "SELECT * FROM sys_account t1 JOIN sys_permission_group t2 ON t1.spg_id = t2.spg_id  AND t2.spg_status_flg = 1 WHERE t1.sa_emp_code = '$empcode' AND t1.sa_emp_password = '$password'";                 
                    $query_chk_status_permis = $this->db->query($sql_chk_status_permis);   
                    

                    // return    $sql_chk_status_permis;
                    // exit;         
                    if ($query_chk_status_permis->num_rows() > 0) {
                        $query_chk_status_permis = $query_chk_status_permis->row();
                        $sql_keep_login = "INSERT INTO log_active (sa_id,la_login,la_status_flg) VALUES($query_login_data->sa_id,NOW(),1)";
                        
                        $query_keep_login = $this->db->query($sql_keep_login);
        
                        $sql_log_login = "SELECT la_id FROM log_active WHERE sa_id = $query_login_data->sa_id  ORDER BY la_id DESC LIMIT 1";
                        
                        $query_log_login = $this->db->query($sql_log_login);
                        
                        $query_log_data = $query_log_login->row();      
                       
                        return array(
                            'result' => 1,  
                            'emp_id' => $query_login_data->sa_id, 
                            'emp_code' => $query_login_data->sa_emp_code,                                     
                            'emp_name' => $query_login_data->sa_fristname,
                            'permis_id' => $query_login_data->spg_id,
                            'permis_group' => $query_chk_status_permis->spg_name,
                            'log_login' =>  $query_log_data->la_id
                        );
                        
                    } else {
                        return array(
                            'result' => 7 //permis status = 0
                        );
                    }
                } else {
                    return array(
                        'result' => 0
                    );
                }
            } else {
                return array(
                        'result' => 8
                );
            }
        }
        else {
            return array(
                'result' => 9
            );
        }

    }


    public function logout_db($data) {
        $log_login_id = $data['log_login'];

        $sql_logout = "UPDATE log_active
        SET la_logout = NOW(), la_status_flg = 0
        WHERE la_id = $log_login_id;
        ";
        $query_logout = $this->db->query($sql_logout);

        if ($this->db->affected_rows() > 0) {
            return array(
                'result' => 1
            );
        } else {
            return array(
                'result' => 0
            );
        }
    }


    public function get_menu($data){
        $permis_id = $data;

        $sql_main_menu = "  SELECT t3.smm_id,t3.smm_name , t3.smm_icon
                            FROM sys_permission_detail t1 
                            INNER JOIN ( 
                                    SELECT t1.smm_id,t1.smm_name ,t2.ssm_id,t1.smm_order_no ,t1.smm_icon
                                    FROM sys_main_menu t1 
                                    INNER JOIN sys_sub_menu t2 ON t2.smm_id = t1.smm_id 
                                    WHERE t1.smm_status_flg = 1 
                                        AND t2.ssm_status_flg = 1 
                            ) AS t3 ON t3.ssm_id = t1.ssm_id 
                            WHERE t1.spg_id = 1 
                                AND t1.spd_status_flg = 1 
                            GROUP BY t3.smm_name,t3.smm_order_no,t3.smm_id ,t3.smm_icon
                            ORDER BY t3.smm_order_no ";

        $query_main_menu = $this->db->query($sql_main_menu);
        return $query_main_menu;
        exit;

        foreach ($query_main_menu->result() as $key_main_menu => $val_main_menu) {
            $sql_menu = "EXEC [dbo].[ST_sel_menu_demo] @permis = '".$permis_id."', @main_menu = '".$val_main_menu->smm_id."'  ";
            $query_menu = $this->db->query($sql_menu);

            $arr[] = array(
                'main_menu' => $val_main_menu,
                'sub_menu' => $query_menu->result()
            );
        }

        return $arr;
    }


}