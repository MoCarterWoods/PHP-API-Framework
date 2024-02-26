<? 
public function save_problem($data, $sess)
{

    $checkedValues = json_decode($data["CheckedValues"], true);
    $problem = $data["ProblemSel"];
    $detail = $data["ProblemDetail"];
    $id = $data["ist_Id"];



    $sql_update_problem = "UPDATE info_issue_ticket 
        SET mpc_id = '$problem', ist_updated_date = NOW(), ist_updated_by = '$sess' 
        WHERE ist_id = '$id'";
    $query_update_problem = $this->db->query($sql_update_problem);


    $sql_close_problem = "UPDATE info_problem_condition 
        SET ipc_status_flg = 0, ipc_updated_date = NOW(), ipc_updated_by = '$sess' 
        WHERE ist_id = '$id'";
    $query_close_problem = $this->db->query($sql_close_problem);


    $sql_insert_problem = "INSERT INTO info_problem_condition
        (ist_id, mpc_id, ipc_detail, ipc_status_flg, ipc_created_date, 
        ipc_created_by, ipc_updated_date, ipc_updated_by)
        VALUES ('$id', '$problem', '$detail', 3, NOW(), '$sess', NOW(), '$sess')";
    $query_insert_problem = $this->db->query($sql_insert_problem);


    if (!empty($checkedValues)) {
        foreach ($checkedValues as $checkedValue) {

            $mpc_id = $checkedValue;


            $sql_insert_problem_condition = "INSERT INTO info_problem_condition
                (ist_id, mpc_id, ipc_status_flg, ipc_created_date, ipc_created_by, 
                ipc_updated_date, ipc_updated_by)
                VALUES ('$id', '$mpc_id', 3, NOW(), '$sess', NOW(), '$sess')";


            $query_insert_problem_condition = $this->db->query($sql_insert_problem_condition);
        }
    }


    if ($this->db->affected_rows() > 0) {
        return array('result' => 1);
    } else {
        return array('result' => 0);
    }
}