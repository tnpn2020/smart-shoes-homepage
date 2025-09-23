<?php
    class AdminUserModel extends gf{
        private $param;
        private $dir;
        private $conn;
        private $session;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->session = $array["session"];
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
        // 함 수 : 관리자 로그인
        // 설 명 : id, pw 필수
        // 만든이: 안정환
        *********************************************************************/
        function login(){
            $param = $this->param;
            $param = $this->null_check_v3($param,array("id","pw"));
            if($this->value_check(array("id","pw"))){
                $sql = "select * from admin where id=".$param["id"]." and pw=".$param["pw"];
                $result = $this->conn->db_select($sql);
                if(count($result["value"]) == 0){
                    $this->result["result"]="0";
                    $this->result["error_code"]="404";
                    $this->result["message"]="아이디 또는 비밀번호가 틀렸습니다.";
                }else{
                    //계정이 있음, 세션 설정
                    $admin_data = $result["value"][0];
                    $session = $this->session;
                    $session->success_admin_login($admin_data["idx"]);
                    $this->result["result"]="1";
                    $this->result["message"]="로그인 성공";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 관리자 로그아웃
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function logout(){
            $session = $this->session;
            $session->admin_logout();
            $this->result["result"]="1";
            $this->result["message"]="로그아웃 성공";
        }


        /********************************************************************* 
        // 함 수 : 
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
    }
?>