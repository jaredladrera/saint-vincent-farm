<?php
 include './../config/pdo_connection.php';

 class DataOperation extends Connect{

	public $error;

	public function insertAny($tbl_name, $data, $status){
		$sql = '';  
		$sql .= "INSERT INTO ".$tbl_name;
		$sql .= "(".implode(",", array_keys($data)).") VALUES ";
		$sql .= "('".implode("','", array_values($data))."')";

		$stm = $this->connection->prepare($sql);
        
        if($stm->execute()) {
            exit($status);
        } else {
            exit($sql);
        }

	}

	function updateAny($tbl_name, $data, $id){

			$sql = '';
			$sql2 = '';
			$sql1 ="UPDATE ".$tbl_name." SET ";

			foreach ($data as $key => $value) {
			$sql2 .= $key." = '".$value."' , ";
			}
			
			$sql3=rtrim($sql2,", ");
			$sql .=$sql1.$sql3." WHERE id = ".$id;
			// $query = mysqli_query($this->conn, $sql);
			$query = $this->connection->prepare($sql);
		if ($query->execute()) {
			exit("Successfully Updated!!!");
		}else{
			exit($sql);
		}

	}

	// public function required_validation($fields){
	// 		$count = 0;

	// 		foreach ($fields as $key => $value) {
	// 			if (empty($value)) {
	// 				$count = $count + 1;
	// 			 return	$this->error = '<p class="text-danger">'.$key.' is required!</p>';				
	// 		}
	// 	}
	// }


	// function getUser($userid){

	// 	$query = "SELECT * FROM user_information where id = ".$userid;
	// 	$stmt = $this->conn->prepare($query);

    //     $result = $stmt->fetch();

	// 	$userdata = $result->fetch_array();
	// 	$username = $userdata['username'];
	// 	return $username;

	// 	//return $userid;

	// }



	// function delete_row($tbl_name, $id, $status){
	// 	$sql = $this->conn->query("DELETE FROM ".$tbl_name." where id = '$id'");
	// 	return $status;
	// }

	// function getBrgy($brgy_id){
	// 	$brgy_name = $this->conn->query("SELECT barangay_name from barangay where id = '$brgy_id'");
	// 	return $brgy_name;
	// }


}//end of class


?>