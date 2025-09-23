<?php
    class Session{
        private $project_name;

        function __construct($name, $json = null){
            $this->project_name = $name."_";
            $this->param = $json;
        }

        function start_session(){
            
            if(!isset($_SESSION)){
                session_cache_expire(360); //세션이 유지될 시간(분)
                session_start();
            }
        }

        /********************************************************************* 
        // 함수 설명- seesion 설정
        // $key : session의 key값
        // $value : session의 value값
        // 만든이: 안정환 만든날 : 2019-05-29
        // 수정이:
        *********************************************************************/
        function create_session($key, $value){
            $this->start_session();
            $_SESSION[$key] = $value; 
        }

        /********************************************************************* 
        // 함수 설명- admin 로그인이 되어있는지 확인
        // return true, false
        // 만든이: 안정환 만든날 : 2019-05-29
        // 수정이:
        *********************************************************************/
        function is_admin_login(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){ //$admin이 있으면 로그인중인상태
                return true;
            }else{
                $login_error = array(
                    "result"=>"0",
                    "error_code"=>"501",
                    "message"=>"관리자 로그인을 하지 않았습니다",
                    "value"=>null,
                );
                echo json_encode($login_error,JSON_UNESCAPED_UNICODE);
                return false;
            }
        }

        /********************************************************************* 
        // 함수 설명- 관리자 로그인성공
        // $idx : 관리자 idx
        // 만든이: 안정환 만든날 : 2019-05-29
        // 수정이:
        *********************************************************************/
        function success_admin_login($idx){
            // echo $this->project_name."\n";
            $this->create_session($this->project_name."admin_idx",$idx);
        }

        /********************************************************************* 
        // 함수 설명- 관리자 역할(슈퍼관리자인지 하위관리자인지 구분 flag)
        // $idx : 관리자 idx
        // 만든이: 최진혁 : 만든날 2020-12-22
        // 수정이:
        *********************************************************************/
        function success_admin_flag($flag){
            // echo $this->project_name."\n";
            $this->create_session($this->project_name."admin_flag",$flag);
        }

        /********************************************************************* 
        // 함수 설명- 관리자 역할(슈퍼관리자인지 하위관리자인지 구분 flag)
        // 만든이: 최진혁 : 만든날 2020-12-22
        // 수정이:
        *********************************************************************/
        function get_admin_flag(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_flag"])){ //$admin_idx이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."admin_flag"];
            }else{
                return false;
            }
        }
        /********************************************************************* 
        // 함수 설명- 관리자 idx
        // $idx : 관리자 idx
        // 만든이: 최진혁 : 만든날 2020-12-22
        // 수정이:
        *********************************************************************/
        function get_seller_idx(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){ //$admin_idx이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."admin_idx"];
            }else{
                return false;
            }
        }

        /********************************************************************* 
        // 함수 설명- 관리자 로그아웃, 세션 해제
        // 만든이: 안정환 만든날 : 2019-05-29
        // 수정이:
        *********************************************************************/
        function admin_logout(){
            $this->start_session();
            unset( $_SESSION[$this->project_name.'admin_idx']);
            unset( $_SESSION[$this->project_name.'admin_flag']);
            $result = array(
                "result"=>"1",
                "error_code"=>"0",
                "message"=>"로그아웃 되었습니다.",
                "value"=>null,
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명- 로그인이 되어있는지 확인(moveController에서 쓸 함수)
        // return true, false
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function is_admin_login_php(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){ //$admin_idx이 있으면 로그인중인상태
                return true;
            }else{
                return false;
            }
        }

        /********************************************************************* 
        // 함수 설명- 세션 key 값 정보 전달
        // 만든이: 이재영
        // 수정이:
        *********************************************************************/
        function get_admin_info(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){ //$admin_idx이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."admin_idx"];
            }else{
                return false;
            }
        }


        /********************************************************************* 
        // 함수 설명- 로그인이 되어있는지 확인(model에서 사용할 함수)
        // return true, false
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function is_login(){
            $this->start_session();
            
            if(isset($_SESSION[$this->project_name."user_idx"])){
                return true;
            }else{
                $login_error = array(
                    "result"=>"0",
                    "error_code"=>"100",
                    "message"=>"로그인을 하지 않았습니다",
                    "value"=>null,
                );
                echo json_encode($login_error,JSON_UNESCAPED_UNICODE);
                return false;
            }
        }

        /********************************************************************* 
        // 함수 설명- 로그인이 되어있는지 확인(model에서 사용할 함수)
        // return true, false
        // 만든이: 최진혁 
        // 수정이:
        *********************************************************************/
        function is_login_v2(){
            if(isset($this->param["user_idx"])){
                return 1;
            }else{
                $this->start_session();
                if(isset($_SESSION[$this->project_name."user_idx"])){
                    return 1;
                }else{
                    $login_error = array(
                        "result"=>"0",
                        "error_code"=>"100",
                        "message"=>"로그인을 하지 않았습니다",
                        "value"=>null,
                    );
                    return $login_error;
                }
            }
        }

        /********************************************************************* 
        // 함수 설명- 로그인이 되어있는지 확인(moveController에서 쓸 함수)
        // return true, false
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function is_login_php(){
            if(isset($this->param["user_idx"])){
                return true;
            }
            $this->start_session();
            if(isset($_SESSION[$this->project_name."user_idx"])){ //$user_idx이 있으면 로그인중인상태
                return true;
            }else{
                return false;
            }
        }

        /********************************************************************* 
        // 함수 설명- 로그인 계정의 idx 가져오기(로그인된 상태가 아니면 0 return)
        // return true, false
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function get_user_idx(){
            if(isset($this->param["user_idx"])){
                return $this->param["user_idx"];
            }else{
                $this->start_session();
                if(isset($_SESSION[$this->project_name."user_idx"])){ //$admin이 있으면 로그인중인상태
                    return $_SESSION[$this->project_name."user_idx"];
                }else{
                    return 0;
                }
            }
        }

        /********************************************************************* 
        // 함수 설명- 사용자 로그인성공
        // $idx : 사용자 idx
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function success_user_login($idx){
            // echo $this->project_name."\n";
            $this->create_session($this->project_name."user_idx",$idx);
        }

        /********************************************************************* 
        // 함수 설명- 사용자 로그아웃, 세션 해제
        // 만든이: 안정환 
        // 수정이:
        *********************************************************************/
        function user_logout(){
            $this->start_session();
            unset( $_SESSION[$this->project_name.'user_idx']);
            echo 1;
        }



        // **************************************통합 문자 서비스 관련 세션 함수 ***********************************************

        
        /********************************************************************* 
        // 함수 설명- 로그인이 되어있는지 확인(model에서 사용할 함수)
        // return true, false
        // 만든이: 최진혁 
        // 수정이:
        *********************************************************************/
        function admin_login_check(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){
                return 1;
            }else{
                $login_error = array(
                    "result"=>"0",
                    "error_code"=>"100",
                    "message"=>"로그인을 하지 않았습니다",
                    "value"=>null,
                );
                return $login_error;
            }
        }

        /********************************************************************* 
        // 함수 설명- 로그인 계정의 idx 가져오기(로그인된 상태가 아니면 0 return)
        // return true, false
        // 만든이: 최진혁 
        *********************************************************************/
        function get_admin_idx(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_idx"])){ //$admin이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."admin_idx"];
            }else{
                return 0;
            }
        }
        /********************************************************************* 
        // 함수 설명- 로그인 계정의 idx 가져오기(로그인된 상태가 아니면 0 return)
        // return true, false
        // 만든이: 최진혁 
        *********************************************************************/
        function get_admin_role(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."admin_role"])){ //$admin이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."admin_role"];
            }else{
                return 0;
            }
        }
        /********************************************************************* 
        // return true, false
        // 만든이: 최진혁 
        *********************************************************************/
        function get_admin_agent_name(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."agent_name"])){ //$admin이 있으면 로그인중인상태
                return $_SESSION[$this->project_name."agent_name"];
            }else{
                return 0;
            }
        }

        
        /********************************************************************* 
        // 함수 설명- 관리자 로그인성공
        // $idx : 관리자 idx
        // 수정이:
        *********************************************************************/
        function success_sms_admin_login($idx, $agent_name, $role){
            // echo $this->project_name."\n";
            $this->create_session($this->project_name."admin_idx",$idx);
            $this->create_session($this->project_name."agent_name",$agent_name);
            $this->create_session($this->project_name."admin_role", $role);
        }

        /********************************************************************* 
        // 함수 설명- 수신자 세션 저장
        // $obj : save_list
        // 만든이: 최진혁 
        *********************************************************************/
        function receiver_list($obj){
            // echo $this->project_name."\n";
            // $this->start_session();
            // unset($_SESSION[$this->project_name."receover_list"]);
            $this->start_session();
            if(isset($_SESSION[$this->project_name."receiver_list"])){ 
                unset($_SESSION[$this->project_name."receover_list"]);
                $this->create_session($this->project_name."receiver_list",$obj);
            }else{
                $this->create_session($this->project_name."receiver_list",$obj);
            }
            
        }
        

        /********************************************************************* 
        // 함수 설명- 수신자 세션 저장 가져오기
        // 만든이: 최진혁 
        *********************************************************************/
        function get_receiver_list(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."receiver_list"])){ 
                return $_SESSION[$this->project_name."receiver_list"];
            }else{
                return 0;
            }
        }


        /********************************************************************* 
        // 함수 설명- 수신자 세션 해제
        *********************************************************************/
        function receiver_remove(){
            $this->start_session();
            if(isset($_SESSION[$this->project_name."receiver_list"])){ 
                unset( $_SESSION[$this->project_name.'receiver_list']);
            }
        }

        
        /********************************************************************* 
        // 함수 설명- 문자 통합 관리자 로그아웃, 세션 해제
        *********************************************************************/
        function sms_admin_logout(){
            $this->start_session();
            unset( $_SESSION[$this->project_name.'admin_idx']);
            unset( $_SESSION[$this->project_name.'agent_name']);
            unset( $_SESSION[$this->project_name.'admin_role']);
            $result = array(
                "result"=>"1",
                "error_code"=>"0",
                "message"=>"로그아웃 되었습니다.",
                "value"=>null,
            );
            echo json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        // ************************************************************************************************************************
    }
?>