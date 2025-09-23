<?php
    class Error_log extends gf{
        private $conn;
        function __construct($db){
            $this->conn = $db;
            $this->result = array(
                "result"=>null,
                "error_code"=>null,
                "message"=>null,
                "value"=>null,
            );
        }

        /********************************************************************* 
        // 설 명 : 에러 추가
        // 만든이: 안정환
        // 담당자: 
        *********************************************************************/
        function error_add($data, $error_kind){
            $sql = "insert into error_logs (data, error_kind, datetime) values(";
            $sql = $sql.$this->null_check($data).",";
            $sql = $sql.$this->null_check($error_kind).",";
            $sql = $sql."now()".")";
            $this->conn->get_result($sql);
        }

    }
?>