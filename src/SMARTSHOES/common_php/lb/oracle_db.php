<?php
	class oracle_db{
		private $conn;
		//생성자 db생성
		private $dataBase;
		function __construct(){
			// Create connection
			// $this->dataBase = $dataBase;
			// $this->conn = new \mysqli('lbcontents.cmy7whafp3m9.ap-northeast-2.rds.amazonaws.com', 'lbcontents', 'lbcontents12#', $dataBase);
			
			// // Check connection
			// if ($this->conn->connect_error) {
			// 	die("데이터베이스 연결실패: " . $this->conn->connect_error);
			// }
			$this->tns = "(DESCRIPTION=
				(ADDRESS=
				(PROTOCOL=TCP)
				(HOST=210.116.103.38)
				(PORT=2020)
				)
				(CONNECT_DATA=
				(SID=ORCL)
				)
			)";
			$user_id = "TG_IF";
			$user_password = "GngtoTg!@34";
			try{
				$this->conn = new PDO("oci:dbname=". $this->tns . ";charset=UTF8", $user_id, $user_password);
				// $statement = $this->conn->query("SELECT * FROM VW_STYLEINFO");
				// $result = $statement->execute();
				// $row = $statement->fetchALL(PDO::FETCH_ASSOC);
			}catch(PDOException $e){
				echo($e->getMessage());
			}
		}

		function db_select($sql){
			$statement = $this->conn->query($sql);
			// $result = $statement->execute();
			$row = $statement->fetchALL(PDO::FETCH_ASSOC);
			$result = array(
				"result" => 1,
				"value" => $row,
			);
			return $result;
		}

	}
?>