<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ManageSubmenu_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }



    public function show_main_menu()
    {
        $sql_show_main = "SELECT smm_id, smm_name FROM sys_main_menu";
        $query = $this->db->query($sql_show_main);
        $data = $query->result();

        return $data;
    }


    public function insert_sub_menu($data, $sess)
    {
        $id = $data["mainId"];
        $submenu = $data["SunMenuName"];
        $subcon = $data["SubMenuCon"];

        $sql_check_duplicate = "SELECT * FROM sys_sub_menu WHERE ssm_name = '$submenu'";
        $query_check_duplicate = $this->db->query($sql_check_duplicate);

        $sql_check_max = "SELECT IFNULL(MAX(ssm_order_no), 0) + 1 AS next_order_no FROM sys_sub_menu WHERE smm_id = '$id';";
        $query_max_no = $this->db->query($sql_check_max);

        if ($query_check_duplicate->num_rows() > 0) {
            return array('result' => 9); // มีข้อมูลซ้ำ
        } else {
            $next_order_no = $query_max_no->row()->next_order_no;

            $sql_insert = "INSERT INTO sys_sub_menu (ssm_name,smm_id , ssm_controller, ssm_order_no, ssm_status_flg, ssm_created_date, ssm_created_by)
            VALUES ('$submenu','$id' , '$subcon', '$next_order_no', 1, NOW(), '$sess')";

            $query = $this->db->query($sql_insert);

            if ($this->db->affected_rows() > 0) {
                return array('result' => 1); // Insert สำเร็จ
            } else {
                return array('result' => 0); // Insert ล้มเหลว
            }
        }
    }


    public function show_submenu($data)
    {
        if (isset($data["mainId"]) && !empty($data["mainId"])) {
            $id = $data["mainId"];

            $sql = "SELECT * FROM sys_sub_menu WHERE smm_id = '$id';";

            $query = $this->db->query($sql);
            $data = $query->result();

            // Loop through the result and replace null values with '-'
            foreach ($data as &$row) {
                foreach ($row as $key => $value) {
                    if ($value === null) {
                        $row->$key = '-';
                    }
                }
            }

            return $data;
        } else {
            // Handle the case where 'mainId' is not set or empty
            return null;
        }
    }


    public function show_show_smm($data)
    {
        $id = $data["id"];
        $sql_show_mmn = "SELECT * FROM sys_sub_menu WHERE ssm_id = '$id';";

        $query = $this->db->query($sql_show_mmn);
        $data = $query->row();

        if ($this->db->affected_rows() > 0) {
            return array('result' => true, 'data' => $data);
        } else {
            return array('result' => false);
        }
    }


    public function update_flg($data, $sess)
    {
        $stFlg = $data["newStatus"];
        $smId = $data["smId"];

        $sql = "UPDATE sys_sub_menu 
        SET ssm_status_flg = '$stFlg', ssm_updated_date = NOW(), ssm_updated_by = '$sess'
        WHERE ssm_id = '$smId';";

        $query = $this->db->query($sql);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }





// -----------------------------------------------------------------------------------------------------------------

    public function edit_sub_menu($data, $sess)
    {
        $smname = $data["SubName"];
        $sscon = $data["SubCon"];
        $ordno = $data["OrderNo"];
        $id = $data["subId"];

        $checkEdit = $this->checkEditMainMenu($data);

        if (!$checkEdit) {
            $sql = "UPDATE sys_sub_menu SET ssm_name = '$smname', ssm_controller = '$sscon', ssm_order_no = '$ordno', ssm_updated_date = NOW(), ssm_updated_by = '$sess' WHERE ssm_id = '$id'";
            $result = $this->db->query($sql);

            if ($this->db->affected_rows() > 0) {
                $this->orderNo($data);
                return array(
                    'result' => 1,
                    'massage' => 'edit main menu success'
                );
            } else {
                return array(
                    'result' => false,
                    'massage' => 'edit main menu failed'
                );
            }
        } else {
            return array(
                'result' => 2,
                'massage' => 'Duplicate value!!'
            );
        }
    }

    public function checkEditMainMenu($data)
    {
        $id = $data["subId"];
        $smname = $data["SubName"];

        $result = $this->db->query("SELECT * FROM sys_sub_menu WHERE ssm_name = '$smname' AND ssm_id != '$id'");


        if ($result->num_rows() > 0) {
            // มีข้อมูลที่ต้องการแก้ไข
            return true;
        } else {
            // ไม่พบข้อมูลที่ต้องการแก้ไข
            return false;
        }
    }

    public function orderNo($data)
    {
        $id = $data["subId"];
        $ordno = $data["OrderNo"];

        $result = $this->db->query("SELECT ssm_id,ssm_order_no FROM sys_sub_menu");
        $res = $result->result_array();
        $i = 1;
        $order = [];

        foreach ($res as $value) {
            if ($value["ssm_id"] != $id) {
                $i = $i == $ordno ? ++$i : $i;
                $result = $this->db->query("UPDATE sys_sub_menu SET ssm_order_no = '$i' WHERE ssm_id = '{$value['ssm_id']}'");
                $i++;
        
                // ตรวจสอบการอัปเดตข้อมูล
                if (!$result) {
                    return array('result' => 0);
                }
            } else {
                $order[] = [
                    'id' => $value["ssm_id"],
                    'order' => $ordno
                ];
            }
        }
        
    } 
}
