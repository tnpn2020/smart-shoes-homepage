<?php
	class db{
		private $conn;
		
		//생성자 db생성
		private $dataBase;
		function __construct($dataBase){
			// Create connection
			$this->dataBase = $dataBase;
			
			$this->conn = new \mysqli('address', 'id', 'pw', $dataBase);
			$this->conn->set_charset("utf8");
			

			// Check connection
			if ($this->conn->connect_error) {
				die("데이터베이스 연결실패: " . $this->conn->connect_error);
			}
		}

		function get_result($sql){
			$result = $this->conn->query($sql);
			return $result;
		}

		function get_query($sql){//최종본 에러코드와 함께 전달
			$result = $this->conn->query($sql);
			$array = array(
				"error_code"=>$this->conn->error,
				"error_msg"=>$this->conn->error,
				"result"=>$result
			);
			return $array;
		}

		public function get_conn(){
			return $this->conn;
		}

		public function s_transaction(){
			$this->conn->query("start transaction");
		}

		public function commit(){
			$this->conn->query("commit");
		}

		public function begin(){
			$this->conn->query("begin");
		}

		public function rollback(){
			$this->conn->query("rollback");
		}

		public function close(){//fianl 변경 불가능
			$this->conn->close();
		}

		public function get_schema(){ //테이블 스키마 가져오기
			return $this->dataBase;
		}
	}
?>