<?php
    class PopupModel extends gf{
        private $param;
        private $dir;
        private $conn;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->file_manager = $array["file_manager"];
            $this->file_path = $array["file_path"]->get_path_php();
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
            $this->session = $array["session"];
        }

        /********************************************************************* 
        // 함 수 : empty 체크
        // 설 명 : array("id","pw")
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
        // 함수 설명 : 메인페이지의 팝업 정보를 가져오는 함수
        // 설명 : 
        // 만든이: 조경민
        *********************************************************************/
        function init_popup(){
            $param = $this->param;
            if($this->value_check(array( "lang_idx"))){
                $sql = "select pc_file_name, link, idx from popup where is_use = 1 and lang_idx = ".$param["lang_idx"];
                $sql .= " order by sequence asc";
                
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "팝업 검색 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "팝업 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }
    }
?>