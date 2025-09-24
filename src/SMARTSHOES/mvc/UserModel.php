<?php
    class UserModel extends gf{
        private $param;
        private $dir;
        private $conn;
        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->project_name = $array["project_name"];
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
            $this->session = $array["session"];
            
            $this->BillingSMS = new BillingSMSModel($array);
            $this->MailForm = new MailForm($array);

            $this->email_project = $array["email_project_name"];
            $this->send_email = $array["to_email"];
            $this->email_logo = $array["email_logo"];
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
        // 함수 설명 : 비회원 주문조회 확인
        //            
        // 만든이: 최진혁
        *********************************************************************/
        function nonmember_order_check(){
            $param = $this->param;
            if($this->value_check(array("order_name", "nonmember_pw", "order_number"))){
                $sql = "select * from purchase_order ";
                $sql .= "where order_number = ".$this->null_check($param["order_number"])." ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    if(count($result["value"]) == 0){
                        $this->result["result"] = 0;
                        $this->result["error_code"] = "5000";
                        $this->result["message"] = "없는 주문입니다.";
                    }else{
                        $order_detail = $result["value"][0];
                        if($order_detail["orderer_name"] != $param["order_name"]){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "5001";
                            $this->result["message"] = "주문자명이 잘못되었습니다.";
                        }else if($order_detail["nonmember_password"] != $param["nonmember_pw"]){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "5002";
                            $this->result["message"] = "비회원 주문 비밀번호가 잘못되었습니다.";
                        }else if($order_detail["state"] == 0 && ($order_detail["pay_type"] == "card" || $order_detail["pay_type"] == "transfer")){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "5000";
                            $this->result["message"] = "없는 주문입니다.";
                        }else{
                            $this->result = $result;
                            $this->result["order_number"]  = $param["order_number"];
                        }
                    }
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : 로그인 함수
        //            
        // 만든이: 조경민
        *********************************************************************/
        function login(){
            $param = $this->param;
            if($this->value_check(array("email", "pw"))){
                // 비밀번호 암호화 클래스 호출
                $keypw = new MyEncryption();
                $param["pw"] = $keypw->pw_encrypt($param["pw"]);
                
                // print_r($param);
                $sql = "select count(*) as count, idx, email, name, phone, state from user where email = ".$this->null_check($param["email"])." and pw = password(".$this->null_check($param["pw"]).")";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ 
                    $this->result = $result;
                    $this->result["message"] = "회원 정보 검색 성공";
                    $this->result["count"] = $result["value"][0]["count"];
                    if($result["value"][0]["state"] == 1){ //일반 로그인
                        $this->session->success_user_login($result["value"][0]["idx"]);
                        //로그인에 성공하면 user table login_count 증가
                        $sql = "update user set login_count = login_count + 1 where email = ".$this->null_check($param["email"])." and pw = password(".$this->null_check($param["pw"]).")";
                        $this->conn->s_transaction(); //트랜잭션 시작
                        $result = $this->conn->db_update($sql);


                        //로그인 성공시 현재 접속중인 회원의 브라우저 정보를 log DB에 저장
                        $visit_ip = $this->getRealClientIp(); //접속 아이피
                        $visit_date = date('Y-m-d H:i:s', time()); //접속 시간
                        $visit_info = $_SERVER["HTTP_USER_AGENT"]; //접속 브라우저 , 접속 기기 등
                        $visit_browser = $this->getBrowserInfo(); //브라우저 정보
                        $visit_os = $this->getOsInfo(); //OS 정보
                        $user_idx = $result["value"][0]["idx"]; //회원 idx

                        $sql = "insert into visit_log(user_idx, ip, visit_date, visit_info, visit_browser, visit_os) values(";
                        $sql .= $user_idx.","; //회원 idx
                        $sql .= $this->null_check($visit_ip).","; //접속 아이피
                        $sql .= $this->null_check($visit_date).","; //접속 시간
                        $sql .= $this->null_check($visit_info).","; //접속 정보
                        $sql .= $this->null_check($visit_browser).","; //접속 브라우저
                        $sql .= $this->null_check($visit_os).")"; //접속 운영체제
                        $this->conn->db_insert($sql);
                        
                        $this->conn->commit();
                    }else if($result["value"][0]["state"] == 0 && $result["value"][0]["idx"] != null){ //휴먼 회원
                        $this->result["count"] = -1;
                        $this->result["message"] = "휴먼 계정입니다.";
                    }else if($result["value"][0]["state"] == 2){ //탈퇴 회원
                        $this->result["count"] = -1;
                        $this->result["message"] = "탈퇴한 회원입니다.";
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "회원 정보 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : 로그아웃
        // 만든이: 조경민
        *********************************************************************/
        function logout(){
            $this->session->user_logout();
        }

        /********************************************************************* 
        // 함수 설명 : 아이디 찾기
        // 설명 : email을 입력 받아 해당 email의 사용자가 존재하면 id 반환
        // 만든이: 조경민
        *********************************************************************/
        function find_email(){
            $param = $this->param;
            if($this->value_check(array("phone", "certify_code", "certify_key"))){
                $sql = "select email from user where phone = ".$this->null_check($param["phone"]);
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ 
                    $this->result = $result;
                    $this->result["message"] = "회원 정보 검색 성공";
                    //검색에 성공하면 사용한 인증번호 삭제
                    $sql = "delete from phone_certify where code = ".$this->null_check($param["certify_code"])." and certify_key = ".$this->null_check($param["certify_key"]);
                    $result = $this->conn->db_delete($sql);
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "회원 정보 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : 아이디 찾기
        // 설명 : email을 입력 받아 해당 email의 사용자가 존재하면 id 반환
        // 만든이: 조경민
        *********************************************************************/
        function find_pw(){
            $param = $this->param;
            if($this->value_check(array("phone", "email", "certify_code", "certify_key"))){
                $sql = "select email, count(*) as count from user where phone = ".$this->null_check($param["phone"])." and email = ".$this->null_check($param["email"]);
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ 
                    $this->result = $result;
                    $this->result["message"] = "회원 정보 검색 성공";
                    $this->result["count"] = $result["value"][0]["count"];
                    //검색에 성공하면 사용한 인증번호 삭제
                    $sql = "delete from phone_certify where code = ".$this->null_check($param["certify_code"])." and certify_key = ".$this->null_check($param["certify_key"]);
                    $result = $this->conn->db_delete($sql);
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "회원 정보 검색 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : 메일로 임시비밀번호 전송 함수
        // 설명 : 비밀번호 찾기시 임시 비밀번호를 입력한 이메일로 전송
        // 만든이: 조경민
        *********************************************************************/
        function pw_send_email(){
            $param = $this->param;
            if($this->value_check(array("email"))){
                $temp_pw = $this->rand_generateRandomString(8);
                $keypw = new MyEncryption();
                $insert_temp_pw = $keypw->pw_encrypt($temp_pw);
                $sql = "update user set pw = password(".$this->null_check($insert_temp_pw).") where email = ".$this->null_check($param["email"]);
                $result = $this->conn->db_update($sql);
                if($result["result"] == "1"){ 
                    $this->result = $result;
                    $this->result["message"] = "회원 정보 수정 성공";

                    // 관리자에게 이메일 발신
                    $this->MailForm->send_email(array(
                        "type" => "find_pw",
                        "to_list" => array($param["email"]),
                        "temp_pw" => $temp_pw
                    ));
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "303";
                    $this->result["message"] = "회원 정보 수정 실패";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 회원가입 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function register_user(){
            $param = $this->param;
            if($this->value_check(array("pw", "name" , "phone" , "email", "post_code", "address", "detail_address", "terms_idx", "certify_key", "certify_code"))){
                //중복 체크를 통과한 아이디지만 회원가입 도중 같은 아아디로 회원가입 할 수 있으므로 다시 중복 체크
                $sql = "select count(email) as count from user where email = ".$this->null_check($param["email"]);
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "아이디 중복 확인 성공";
                    if($result["value"][0]["count"] == 0){
                        $sql = "select idx from user_grade where is_use = 1 order by sequence desc limit 0,1"; //사용중인 회원등급중 가장 낮은 등급의 idx를 가져와 회원가입시 등록해준다
                        $result = $this->conn->db_select($sql);
                        if(count($result["value"]) != 0){ //user_grade가 설정되어 있는 경우
                            // 비밀번호 암호화 클래스 호출
                            $keypw = new MyEncryption();
                            $param["pw"] = $keypw->pw_encrypt($param["pw"]);

                            $sql = "insert into user(pw, name, phone, email, post_code, address, detail_address, regdate,user_grade_idx, email_agree, sms_agree, birthday) values(";
                            $sql = $sql."password(".$this->null_check($param["pw"])."),";
                            $sql = $sql.$this->null_check($param["name"]).",";
                            $sql = $sql.$this->null_check($param["phone"]).",";
                            $sql = $sql.$this->null_check($param["email"]).",";
                            $sql = $sql.$this->null_check($param["post_code"]).",";
                            $sql = $sql.$this->null_check($param["address"]).",";
                            $sql = $sql.$this->null_check($param["detail_address"]).",";
                            $sql = $sql."now(),";
                            $sql = $sql.$result["value"][0]["idx"].",";
                            $sql = $sql.$param["email_agree"].",";
                            $sql = $sql.$param["sms_agree"].",";
                            $sql = $sql.$this->null_check($param["birthday"]).")";
                            $this->conn->s_transaction(); //트랜잭션 시작
                            $result = $this->conn->db_insert($sql);
                            if($result["result"] == "1"){ //쿼리가 성공이면
                                $this->result = $result;
                                $this->result["message"] = "회원 등록 성공";
                                $this->result["check"] = 1;
                                //회원 등록에 성공하면 등록된 user_idx로 terms_agree table 등록
                                $terms_idx = json_decode($param["terms_idx"]);
                                $sql = "insert into terms_agree(user_idx, terms_idx, regdate) values(";
                                for($i = 0; $i < count($terms_idx); $i++){
                                    $sql .= $result["value"].",";
                                    $sql .= $terms_idx[$i].",";
                                    if($i == count($terms_idx) - 1){
                                        $sql .= "now())";
                                    }else{
                                        $sql .= "now()), (";
                                    }
                                }
                                $terms_result = $this->conn->db_insert($sql);
                                
                                if($terms_result["result"] == "1"){ //쿼리가 성공이면
                                    $this->result = $terms_result;
                                    $this->result["message"] = "terms_agree table 등록 성공";
                                    //회원가입 모두 완료되면 phone_certify table에 회원가입에 사용된 인증번호 삭제 and point table에 3천 포인트 등록
                                    $sql = "insert into point(user_idx, point, state, remnant_point, regdate, point_title) values(";
                                    $sql .= $result["value"].",";
                                    $sql .= "3000,";
                                    $sql .= "1,";
                                    $sql .= "3000,";
                                    $sql .= "now(),";
                                    $sql .= "'회원가입 포인트')";
                                    $this->conn->db_insert($sql);
                                    
                                    //point_user_history table 등록 (유저의 포인트 내역)
                                    $sql = "insert into point_user_history(user_idx, use_content, kind, point, regdate) values(";
                                    $sql .= $result["value"].",";
                                    $sql .= "'회원가입 포인트',";
                                    $sql .= "2,";
                                    $sql .= "3000,";
                                    $sql .= "now())";
                                    $this->conn->db_insert($sql);
    
                                    $sql = "delete from phone_certify where code = ".$this->null_check($param["certify_code"])." and certify_key = ".$this->null_check($param["certify_key"]);
                                    $result = $this->conn->db_delete($sql);
                                    if($result["result"] == "1"){ //쿼리가 성공이면
                                        $this->result = $result;
                                        $this->result["message"] = "인증번호 삭제 성공";
                                        
                                        // if($param["email_agree"] == 1){ //문자 수신 동의
                                            
                                        // }

                                        if($param["sms_agree"] == 1){
                                            // 유저에게 문자 발신
                                            $this->BillingSMS->send_tracking(array(
                                                "send_type" => "kakao",
                                                "type" => "u_join",
                                                "receiver_list" => json_encode(array($param["phone"])),
                                            ), null);
                                        }
                                        

                                        // 관리자에게 문자 발신
                                        $this->BillingSMS->send_tracking(array(
                                            "send_type" => "kakao",
                                            "type" => "a_join",
                                            "name" => $param["name"],
                                        ), null);

                                        // 사용자에게 이메일 발신
                                        if($param["email_agree"] == 1){ //이메일 수신 동의
                                            $this->MailForm->send_email(array(
                                                "type" => "u_join",
                                                "name" => $param["name"],
                                                "email" => $param["email"],
                                                "to_list" => array(
                                                    $param["email"]
                                                ),
                                            ));
                                        }
                                        // 관리자에게 이메일 발신
                                        $this->MailForm->send_email(array(
                                            "type" => "a_join",
                                            "name" => $param["name"],
                                            "email" => $param["email"],
                                        ));

                                        $this->conn->commit();
                                    }else{
                                        $this->result["result"] = "0";
                                        $this->result["error_code"] = "303";
                                        $this->result["message"] = "인증 번호 삭제 실패";
                                    }
                                }else{
                                    $this->result["result"] = "0";
                                    $this->result["error_code"] = "300";
                                    $this->result["message"] = "terms_agree table 등록 실패";
                                }
                            }else{
                                $this->result["result"] = "0";
                                $this->result["error_code"] = "300";
                                $this->result["message"] = "회원 등록 실패";
                            }
                        }else{
                            //user grade가 설정되어 있지 않으면 관리자에게 문의
                            $this->result["result"] = "0";
                            $this->result["error_code"] = "302";
                            $this->result["message"] = "회원등갑 미설정";
                        }
                    }else{
                        $this->result["message"] = "회원가입 도중 같은 이메일로 가입한 회원이 있습니다.";
                        $this->result["check"] = 0;
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "아이디 중복 확인 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 회원정보수정에서 회원정보 수정 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function modify_user(){
            $param = $this->param;
            if($this->value_check(array("name", "phone", "post_code", "address", "detail_address"))){
                if($this->session->is_login_v2() == 1){
                    $user_idx = $this->session->get_user_idx();
                }else{
                    $this->result = $this->session->is_login_v2();
                    echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                    return;
                }

                //변경할 휴대폰 번호가 이미 있는 번호라면 변경 막기 ( 현재 회원의 전화번호는 제외하고 체크 )
                $sql = "select count(*) as count from user where phone = ".$this->null_check($param["phone"])." and idx != ".$user_idx;
                $result = $this->conn->db_select($sql);
                if($result["value"][0]["count"] >= 1){ //이미 가입된 번호면
                    $this->result = $result;
                    $this->result["message"] = "이미 가입된 전화번호입니다.";
                    $this->result["flag"] = 1;
                }else{
                    $sql = "update user set";
                    $sql .= " name = ".$this->null_check($param["name"]).",";
                    $sql .= " phone = ".$this->null_check($param["phone"]).",";
                    $sql .= " post_code = ".$this->null_check($param["post_code"]).",";
                    $sql .= " address = ".$this->null_check($param["address"]).",";
                    $sql .= " detail_address = ".$this->null_check($param["detail_address"]).",";
                    $sql .= " sms_agree = ".$param["sms_agree"].",";
                    $sql .= " email_agree = ".$param["email_agree"].",";
                    $sql .= " update_regdate = now()";
                    $sql .= " where idx = ".$user_idx;
                    $this->conn->s_transaction(); //트랜잭션 시작
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == "1"){ 
                        $this->result = $result;
                        $this->result["message"] = "회원 정보 수정 성공";
                        //수정에 성공하면 인증에 사용된 인증번호 삭제
                        if($param["certify_key"] != "0"){ //key 값이 0이라면 전화번호 인증을 안했으므로 실행 안함
                            $sql = "delete from phone_certify where code = ".$this->null_check($param["certify_code"])." and certify_key = ".$this->null_check($param["certify_key"]);
                            $this->conn->db_delete($sql);   
                        }
                        $this->conn->commit();
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "301";
                        $this->result["message"] = "회원 정보 수정 실패";
                    }
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 회원정보수정에서 비밀번호 수정 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function modify_password(){
            $param = $this->param;
            if($this->value_check(array("current_password", "new_password"))){
                if($this->session->is_login_v2() == 1){
                    $user_idx = $this->session->get_user_idx();
                }else{
                    $this->result = $this->session->is_login_v2();
                    echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                    return;
                }

                $keypw = new MyEncryption();
                $param["current_password"] = $keypw->pw_encrypt($param["current_password"]);
                //입력한 현재 비밀번호가 현재 로그인한 user의 비밀번호와 일치하는지 check
                $sql = "select count(pw) as count from user where pw = password(".$this->null_check($param["current_password"]).") and idx = ".$user_idx;
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ 
                    $this->result = $result;
                    $this->result["message"] = "회원 정보 검색 성공";
                    if($result["value"][0]["count"] == 1){ //입력한 비밀번호와 현재 로그인한 user의 비밀번호가 일치하면 새 비밀번호로 교체
                        $param["new_password"] = $keypw->pw_encrypt($param["new_password"]);
                        $sql = "update user set pw = password(".$this->null_check($param["new_password"])."),";
                        $sql .= " pw_update_regdate = now()";
                        $sql .= " where idx = ".$user_idx;
                        $result = $this->conn->db_update($sql);
                        if($result["result"] == "1"){ 
                            $this->result = $result;
                            $this->result["message"] = "회원 비밀번호 변경 성공";
                            $this->result["count"] = 1; //비밀번호 변경 여부 체크 변수
                        }else{
                            $this->result["result"] = "0";
                            $this->result["error_code"] = "301";
                            $this->result["message"] = "회원 비밀번호 변경 실패";
                        }
                    }else{
                        $this->result["count"] = 0; //비밀번호 변경 여부 체크 변수
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "회원 정보 검색 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 이메일 중복 체크 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function email_check(){
            $param = $this->param;
            if($this->value_check(array("email"))){
                $sql = "select count(email) as count from user where email = ".$this->null_check($param["email"]);
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "아이디 중복 확인 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "아이디 중복 확인 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : terms_name table에서 약관 내용을 가져와 회원가입 페이지
        //         약관에 내용을 넣어주는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_terms_text(){
            $param = $this->param;
            if($this->value_check(array("lang_idx"))){
                $sql = "select * from terms_name where lang_idx = ".$param["lang_idx"];
                $sql .= " order by terms_idx asc";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "약관 내용 검색 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "약관 내용 검색 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 마이페이지에서 회원정보수정에 들어갈때 한번더 비밀번호 확인
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function check_password(){
            $param = $this->param;
            if($this->value_check(array("pw"))){
                if($this->session->is_login_v2() == 1){
                    $user_idx = $this->session->get_user_idx();
                }else{
                    $this->result = $this->session->is_login_v2();
                    echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                    return;
                }

                $keypw = new MyEncryption();
                $param["pw"] = $keypw->pw_encrypt($param["pw"]);


                $sql = "select count(pw) as count from user where pw = password(".$this->null_check($param["pw"]).") and idx = ".$user_idx;
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "비밀번호 체크 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "비밀번호 체크 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 마이페이지에서 현재 로그인 한 회원정보를 가져오는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_user_info(){
            if($this->session->is_login_v2() == 1){
                $user_idx = $this->session->get_user_idx();
            }else{
                $this->result = $this->session->is_login_v2();
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            $sql = "select * from user where idx = ".$user_idx;
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){ //쿼리가 성공이면
                $this->result = $result;
                $this->result["message"] = "회원 정보 검색 성공";
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "302";
                $this->result["message"] = "회원 정보 검색 실패";
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 사용자가 입력한 인증번호를 체크하는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function certify_check(){
            $param = $this->param;
            if($this->value_check(array("certify_key", "code"))){
                $sql = "select count(*) as count from phone_certify where certify_key = ".$this->null_check($param["certify_key"])." and code = ".$this->null_check($param["code"]);
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "인증번호 확인 성공";
                    if($result["value"][0]["count"] == 1){ //인증번호 일치
                        $this->result["flag"] = 1;
                        $this->result["message"] = "휴대폰 인증이 확인되었습니다.";
                    }else{ //인증번호 불일치
                        $this->result["flag"] = 0;
                        $this->result["message"] = "인증번호가 틀렸습니다.";
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "인증번호 확인 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 재인증 요청시 기존의 인증코드 삭제
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function delete_certify_code(){
            $param = $this->param;
            if($this->value_check(array("certify_key"))){
                $sql = "delete from phone_certify where certify_key = ".$this->null_check($param["certify_key"]);
                $this->conn->s_transaction(); //트랜잭션 시작
                $result = $this->conn->db_delete($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->conn->commit();
                    $this->result = $result;
                    $this->result["message"] = "인증번호 삭제 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "303";
                    $this->result["message"] = "인증번호 삭제 실패";
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 회원가입시 핸드폰인증 sms보내기
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function send_sms(){
            $param = $this->param;
            if($this->value_check(array("phone_number"))){
                //핸드폰 번호로 인증된 계정이 있는지 확인
                $sql = "select count(*) as count from user where phone=".$this->null_check($param["phone_number"]);
                $result = $this->conn->db_select($sql);
                if($result["value"][0]["count"] != 0 && $param["type"] == 1){ //type이 1이 아니면 else로 이동
                    //핸드폰 번호로 가입된 계정이 있음
                    $this->result["result"] = "1";
                    $this->result["flag"]= "1"; //flag가 1이면 이미 가입된 번호
                    $this->result["message"]="이미 가입된 전화번호입니다.";
                }else if($result["value"][0]["count"] == 0 && $param["type"] == 2){ //아이디 or 비밀번호 찾기시 입력된 전화번호로 가입된 회원이 없을때
                    $this->result["result"] = "1";
                    $this->result["flag"]= "1"; //flag가 1이면 일치하는 회원정보 없음
                    $this->result["message"]="일치하는 회원정보가 없습니다.";
                }else{ //가입된 핸드폰 번호 계정이 없음
                    $certify_number = $this->rand_number();
                    $key  = $this->rand_generateRandomString(10);
                    $sql = "insert into phone_certify(code, certify_key) values(";
                    $sql .= $this->null_check($certify_number).",";
                    $sql .= $this->null_check($key).")";
                    $this->conn->s_transaction(); //트랜잭션 시작
                    $this->conn->db_insert($sql);
                    if($result["result"] == 1){
                        $this->result = $result;
                        $this->result["message"] = "phone_certify 테이블 등록 성공";
                        $this->result["value"] = array($key); //Test용
                        
                        // 사용자에게 문자 발신
                        $this->BillingSMS->send_tracking(array(
                            "send_type" => "kakao",
                            "type" => "phone_certify",
                            "certify_number" => $certify_number,
                            "receiver_list" => json_encode(array($param["phone_number"])),
                        ), null);
                        
                        $this->conn->commit();
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "300";
                        $this->result["message"] = "phone_certify 테이블 등록 실패";
                    }
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        /********************************************************************* 
        // 함 수 : 회원탈퇴 함수
        // 설 명 : 회원의 상태값 변경과 전화번호만 삭제하여 동일한 계정으로는 가입
        //         할 수 없도록 하고 같은 전화번호로는 재가입할 수 있도록 함
        // 만든이: 조경민
        *********************************************************************/
        function sign_out(){
            if($this->session->is_login_v2() == 1){
                $user_idx = $this->session->get_user_idx();
            }else{
                $this->result = $this->session->is_login_v2();
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            $sql = "update user set state = 2, phone = null where idx = ".$user_idx;
            $result = $this->conn->db_update($sql);
            if($result["result"] == "1"){ //쿼리가 성공이면
                $this->result = $result;
                $this->result["message"] = "회원 탈퇴 성공";
                $this->session->user_logout(); //탈퇴에 성공하면 session 제거
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "301";
                $this->result["message"] = "회원 탈퇴 실패";
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }

        //핸드폰번호 -하이픈 달기
        function format_phone($phone){
            $phone = preg_replace("/[^0-9]/", "", $phone);
            $length = strlen($phone);
        
            switch($length){
                case 11 :
                    return preg_replace("/([0-9]{3})([0-9]{4})([0-9]{4})/", "$1-$2-$3", $phone);
                    break;
                case 10:
                    return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);
                    break;
                default :
                    return $phone;
                    break;
            }
        }
        
        //랜덤 6자리 숫자 만들기
        function rand_number(){
            $char = '0123456789';
            $char_length = strlen($char);
            $randomString = "";
    
            for($i = 0; $i < 6; $i++){
                $randomString = $randomString.$char[rand(0, $char_length -1)];
            }
    
            return $randomString;
        }

        //made auth code..
        function rand_generateRandomString($leng){
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $char_length = strlen($char);
            $randomString = "";
            for($i = 0; $i < $leng; $i++){
                $randomString = $randomString.$char[rand(0, $char_length -1)];
            }
            return $randomString;
        }

        function test(){
            return;
            echo $_SERVER["HTTP_HOST"]; //접속 아이피
            echo $_SERVER["SERVER_SOFTWARE"]; //웹서버 정보 ( 아파치, PHP 버전 )
            echo date('Y-m-d H:i:s', time()); //접속 시간
            echo $_SERVER["HTTP_USER_AGENT"]; //접속 브라우저 , 접속 기기 등
        }

        /********************************************************************* 
        // 함 수 : 현재 접속중인 브라우저를 리턴해주는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function getBrowserInfo() {
            $userAgent = $_SERVER["HTTP_USER_AGENT"]; 
            if(preg_match('/MSIE/i',$userAgent) && !preg_match('/Opera/i',$u_agent)){
                $browser = 'Internet Explorer';
            }
            else if(preg_match('/Firefox/i',$userAgent)){
                $browser = 'Mozilla Firefox';
            }
            else if (preg_match('/Chrome/i',$userAgent)){
                $browser = 'Google Chrome';
            }
            else if(preg_match('/Safari/i',$userAgent)){
                $browser = 'Apple Safari';
            }
            elseif(preg_match('/Opera/i',$userAgent)){
                $browser = 'Opera';
            }
            elseif(preg_match('/Netscape/i',$userAgent)){
                $browser = 'Netscape';
            }
            else{
                $browser = "Other";
            }

            return $browser;
        }

        /********************************************************************* 
        // 함 수 : 현재 접속중인 OS를 리턴해주는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function getOsInfo(){
            $userAgent = $_SERVER["HTTP_USER_AGENT"]; 

            if (preg_match('/linux/i', $userAgent)){ 
                $os = 'linux';}
            elseif(preg_match('/macintosh|mac os x/i', $userAgent)){
                $os = 'mac';}
            elseif (preg_match('/windows|win32/i', $userAgent)){
                $os = 'windows';}
            else {
                $os = 'Other';

            }

            return $os;
        }

        /********************************************************************* 
        // 함 수 : 현재 접속중인 사용자의 IP를 리턴해주는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function getRealClientIp() {
            $ipaddress = '';
            if(isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            }else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            }else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            }else if(isset($_SERVER['HTTP_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            }else if(isset($_SERVER['REMOTE_ADDR'])) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            }else if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
            }else {
                $ipaddress = '알수없음';
            }  
            return $ipaddress;
        }

        
        /********************************************************************* 
        // 함 수 : 플러스 친구 쿠폰 등록
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function coupon_register(){
            $param = $this->param;
            if($this->value_check(array("coupon_number"))){
                if($this->session->is_login_v2() == 1){
                    $user_idx = $this->session->get_user_idx();
                }else{
                    $this->result = $this->session->is_login_v2();
                    echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                    return;
                }

                $sql = "select * from coupon_key where coupon_key = ".$this->null_check(trim($param["coupon_number"]))." ";
                $result= $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    if(count($result["value"]) == 0){
                        $this->result["result"] = 0;
                        $this->result["message"] = "없는 쿠폰 번호 입니다.";
                        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }
                $sql = "select * from coupon_key ";
                $sql .= "where user_idx = ".$user_idx." ";
                $sql .= "and coupon_idx = (select coupon_idx from coupon_name where lang_idx = 1 and name = ".$this->null_check($param["coupon_name"]).") ";
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    if(count($result["value"]) > 0){
                        $this->result["result"] = 0;
                        $this->result["message"] = "이미 쿠폰을 등록한 회원입니다.";
                        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                        return;
                    }else{
                        $sql = "select t1.idx from coupon as t1 ";
                        $sql .= "left join coupon_name as t2 ";
                        $sql .= "on t1.idx = t2.coupon_idx ";
                        $sql .= "where t2.lang_idx = 1 ";
                        $sql .= "and t2.name = ".$this->null_check($param["coupon_name"])." ";
                        $result = $this->conn->db_select($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                        }else{
                            $coupon_info = $result["value"];
                            $coupon_idx = $coupon_info[0]["idx"];
                            // 쿠폰을 등록한 회원이 아닐경우
                            $sql = "insert into coupon_relation(user_idx , coupon_idx, regdate, is_use) values (";
                            $sql .= $user_idx . ", ";
                            $sql .= $coupon_idx . ", now() , 0 ";
                            $sql .= ")";
                            $result = $this->conn->db_insert($sql);
                            if($result["result"] == 0){
                                $this->result = $result;
                            }else{
                                $sql = "update coupon_key set ";
                                $sql .= "user_idx = ".$user_idx." ";
                                $sql .= "where coupon_idx = ".$coupon_idx." ";
                                $sql .= "and coupon_key = ".$this->null_check($param["coupon_number"])." ";
                                $result = $this->conn->db_update($sql);
                                if($result["result"] == 0){
                                    $this->result = $result;    
                                }else{
                                    $this->result = $result;
                                }
                            }
                        }

                    }
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE); 
        }
    }
?>