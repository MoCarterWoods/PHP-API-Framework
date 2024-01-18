<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_ManageMenu_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function show_menu() {
        $sql = "SELECT 
        sma_id,
        sma_name,
        sma_pic,
        sma_path,
        sma_order_no,
        sma_status_flg,
        DATE_FORMAT (sma_created_date, '%Y-%m-%d') as sma_created_date,
        sma_created_by,
        sma_route
        FROM 
        sys_menu_app;";
    
        $query = $this->db->query($sql);
        $data = $query->result();
        return $data;
    }
    

    public function update_flg($data,$sess){
        $stFlg = $data["newStatus"];
        $saId = $data["saId"];
    
        $sql = "UPDATE sys_menu_app 
        SET sma_status_flg = '$stFlg',sma_updated_date = NOW(),sma_updated_by = '$sess'
        WHERE sma_id = '$saId';";
        
        $query=$this->db->query($sql);
        if($this->db->affected_rows()>0){
            return true;
        }else{
            return false;
    
        }
        }


        public function insert_menu($data, $sess) {
            $name = $data["MenuName"];
            $pic = $data["MenuPic"];
            $part = $data["MenuPart"];
            $rout = $data["MenuRout"];
        
            $sql_check_duplicate = "SELECT * FROM sys_menu_app WHERE sma_name = '$name'";
            $query_check_duplicate = $this->db->query($sql_check_duplicate);
        
            $sql_check_max = "SELECT IFNULL(MAX(sma_order_no), 0) + 1 AS next_order_no FROM sys_menu_app;";
        $query_max_no = $this->db->query($sql_check_max);

        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $next_order_no = $query_max_no->row()->next_order_no;

            $sql_insert = "INSERT INTO sys_menu_app 
            (sma_name, sma_pic, sma_path, sma_order_no, sma_status_flg, sma_created_date, sma_created_by, sma_route)
          VALUES 
            ('$name', '$pic', '$part', '$next_order_no', 1, NOW(), '$sess', '$rout');
          ";

            $query = $this->db->query($sql_insert);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
        }
    

        public function show_edit_menu($data) {
            $id = $data["id"];
            // return $id;
            // exit;
            
            $sql_show_mn = "SELECT * FROM sys_menu_app WHERE sma_id = '$id';";
    
            $query = $this->db->query($sql_show_mn);
            $data = $query->row();
            if ($this->db->affected_rows()>0) {
                return array('result'=> true,'data'=>$data);
            }
            else{
                return array('result' => false);
            }
        }




        public function update_menu($data, $sess) {
            $accId = $data["accId"];
            $menuname = $data["MenuName"];
            $menupic = $data["MenuPic"];
            $menupart = $data["MenuPart"];
            $menuroute = $data["MenuRout"];
        

                $sql_update = "
                UPDATE sys_menu_app
                SET sma_name= '$menuname', 
                sma_pic= '$menupic',
                sma_path= '$menupart',
                sma_updated_date= NOW(),
                sma_updated_by = '$sess',
                sma_route= '$menuroute'
                WHERE sma_id= $accId;
                ";
        
                $query_update = $this->db->query($sql_update);
        
                if ($this->db->affected_rows() > 0) {
                    return array('result' => 1); // อัปเดตสำเร็จ
                } else {
                    return array('result' => 0); // ไม่สามารถอัปเดต
                }
            }

}
    