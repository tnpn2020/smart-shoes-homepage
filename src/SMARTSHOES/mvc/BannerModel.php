<?php
    class BannerModel extends gf{
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
        // 함수 설명 : 메인페이지의 모든 배너 정보를 가져오는 함수
        // 설명 : 
        // 만든이: 조경민
        *********************************************************************/
        function init_banner(){
            $param = $this->param;
            if($this->value_check(array( "lang_idx"))){
                $sql = "select link, m_file_name, pc_file_name, kind, title, content from banner where lang_idx = ".$param["lang_idx"]." and is_use = 1 order by sequence asc";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "배너 검색 성공";
                    //배너 검색에 성공했으면 배너를 종류별로 분류 하기
                    $banner_array = array(
                        "main" => [],
                        "sub_1" => [],
                        "sub_2" => [],
                        "sub_3" => [],
                    );
                    for($i = 0; $i < count($result["value"]); $i++){
                        if($result["value"][$i]["kind"] == 1){ //메인 배너인 경우
                            array_push($banner_array["main"], $result["value"][$i]);
                        }else if($result["value"][$i]["kind"] == 2){ //첫번째 서브 배너인 경우
                            array_push($banner_array["sub_1"], $result["value"][$i]);
                        }else if($result["value"][$i]["kind"] == 3){ //두번째 서브 배너인 경우
                            array_push($banner_array["sub_2"], $result["value"][$i]);
                        }else if($result["value"][$i]["kind"] == 4){ //세번째 서브 배너인 경우
                            array_push($banner_array["sub_3"], $result["value"][$i]);
                        }
                    }
                    $this->result["value"] = $banner_array;
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "배너 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : shop_view Banner 정보를 가져오는 함수
        // 설명 : 랜덤으로 하나의 데이터만 가져옴 ( 새로고침 할때마다 배너가 바뀌도록 하기 위해 )
        // 만든이: 조경민
        *********************************************************************/
        function shop_view_banner_init(){
            $param = $this->param;
            if($this->value_check(array( "lang_idx"))){
                $sql = "select pc_file_name, m_file_name, link from banner where kind = 4 and lang_idx = ".$param["lang_idx"];
                $sql .= " and is_use = 1 order by rand() limit 1";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "배너 검색 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "배너 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }
    }
?>