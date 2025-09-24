<?php
    class AdminSettingModel extends gf{
        private $param;
        private $dir;
        private $conn;
        private $file_manager;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            // $this->file_manager = $array["filemanager"];
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
        // 함 수 : 쿠폰 세팅(discount_limit : 쿠폰, 적립금 중복설정)
        // 파라미터 : setting_bool : 바꿀 값
        // 만든이: 최진혁
        *********************************************************************/
        function coupon_point_setting(){
            $param = $this->param;
            if($this->value_check(array("setting_bool"))){
                $sql = "update setting set ";
                $sql .= "discount_limit = " . $param["setting_bool"] . " ";
                $sql .= "where idx = 1 ";

                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                    $this->result["setting_bool"] = $param["setting_bool"];
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 세팅 초기값
        // 파라미터 : 
        // 만든이: 최진혁
        *********************************************************************/
        function setting_init(){
            $param = $this->param;

            $sql = "select * from setting ";

            $result = $this->conn->db_select($sql);
            if($result["result"] == 0){
                $this->result = $result;
            }else{
                $this->result = $result;
            }
            echo $this->jsonEncode($this->result);
        }


                
    }
?>