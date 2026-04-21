<?php

class Database{

	public $conn;

	public function __construct(){
		$this->conn = mysqli_connect("127.0.0.1", "root", "", "saint_vincent_farm");
		if (!$this->conn) {
			echo "Not Connected";
		}
	}


}


// $obj = new Connection();
// $obj->conn();

?>