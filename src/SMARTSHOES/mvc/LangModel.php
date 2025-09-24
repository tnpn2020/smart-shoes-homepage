<?php
    class LangModel extends gf{
        private $param;
        private $dir;
        private $conn;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
        }

        /********************************************************************* 
        // 함 수 : empty 체크
        // 설 명 : array("id","pw")
        // 예 시 : if($this->value_check(array("id","pw"))){}
        // 만든이: 안정환
        *********************************************************************/
        function value_check($check_value_array){
            $object = array(
                "param"=>$this->param,
                "array"=>$check_value_array
            );
            $check_result = $this->empty_check($object);
            if($check_result["result"]){//param 값 체크 비어있으면 실행 안함
                if($check_result["value_empty"]){//필수 값이 비었을 경우
                    $this->result["result"]="0";
                    $this->result["error_code"]="101";
                    $this->result["message"]=$check_result["value_key"]."가 비어있습니다.";
                    return false;
                }else{
                    return true;
                }
            }else{
                $this->result["result"]="0";
                $this->result["error_code"]="100";
                $this->result["message"]=$check_result["value"]." 가 없습니다.";
                return false;
            }
        }

        /********************************************************************* 
        // 함 수 : 등록된 언어 조회
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_lang_list(){
            $sql = "select * from lang";
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 등록된 언어 조회(php용)
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function get_lang_list(){
            $sql = "select * from lang";
            $result = $this->conn->db_select($sql);
            if(count($result["value"]) > 0){
                return $result["value"];
            }else{
                //설정 언어가 없음
                return false;
            }
        }
    }
?>