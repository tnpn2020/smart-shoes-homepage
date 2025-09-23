<?php
    class ProjectSettingModel extends gf{
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
        // 함 수 : 프로젝트 셋팅 조회하기
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_project_setting(){
            $sql = "select * from project_setting where idx=1";
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 프로젝트 셋팅 조회하기(php 내부에서 쓰는 함수)
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function get_project_setting(){
            $sql = "select * from project_setting where idx=1";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1" && count($result["value"]) == 1){ //조회가 성공이고 데이터 값이 있다면
                return $result["value"][0];    
            }else{
                return false;
            }
        }

        /********************************************************************* 
        // 함 수 : 프로젝트 셋팅하기
        // 설 명 : param["flag"]가 0이면 insert문 , 1이면 update문 실행
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function project_setting(){
            $param = $this->param;
            if($this->value_check(array("flag", "value"))){
                //프로젝트 셋팅이 안되어 있으면 insert문 실행
                if($param["flag"] == 0){
                    $sql = "insert into project_setting(category_count) values(";
                    $sql .= $this->null_check($param["value"]).")";
                    $result = $this->conn->db_insert($sql);
                    if($result["result"] == "1"){ //쿼리가 성공이면
                        $this->result = $result;
                        $this->result["message"] = "project_setting 등록 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "300";
                        $this->result["message"] = "project_setting 등록 실패";
                    }
                }else if($param["flag"] == 1){ //이미 등록되어 있으면 update문 실행
                    $sql = "update project_setting set category_count = ".$this->null_check($param["value"]);
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == "1"){ //쿼리가 성공이면
                        $this->result = $result;
                        $this->result["message"] = "project_setting 수정 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "301";
                        $this->result["message"] = "project_setting 수정 실패";
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : setting page 등록한 언어 list를 보여주기 위한 검색 함수
        // 설 명 : 
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function select_lang(){
            $sql = "select * from lang";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){ //쿼리가 성공이면
                $this->result = $result;
                $this->result["message"] = "lang table 검색 성공";
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "302";
                $this->result["message"] = "lang table 언어 검색 실패";
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 프로젝트 사용 언어 등록
        // 설 명 : 
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function register_lang(){
            $param = $this->param;
            if($this->value_check(array("lang_name", "icon"))){
                $sql = "insert into lang(name, icon_file_name) values(";
                $sql .= $this->null_check($param["lang_name"]).",";
                $sql .= $this->null_check($param["icon"]).")";
                $result = $this->conn->db_insert($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "프로젝트 사용 언어 등록 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "300";
                    $this->result["message"] = "프로젝트 사용 언어 등록 실패";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 프로젝트 사용 언어 삭제
        // 설 명 : 삭제시 삭제한 값 보다 큰 idx가 있으면 idx값들을 -1씩 해주고
                   auto_increment 초기값 변경시켜주기
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function delete_lang(){
            $param = $this->param;
            if($this->value_check(array("idx"))){
                $sql = "delete from lang where idx = ".$param["idx"];
                $result = $this->conn->db_delete($sql);
                if($result["result"] == "1"){ //쿼리가 성공이면
                    $this->result = $result;
                    $this->result["message"] = "프로젝트 사용 언어 삭제 성공";
                    //삭제에 성공하면 해당 idx 뒤에 있는 컬럼 idx 값 -1씩 해주기
                    $sql = "update lang set idx = idx - 1 where idx > ".$param["idx"];
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == "1"){ //쿼리가 성공이면
                        $this->result = $result;
                        $this->result["message"] = "lang table idx값 수정 성공";
                        //idx값 수정에 성공했으면 auto_increment 초기값 변경
                        $sql = "select count(*) as count from lang";
                        $result = $this->conn->db_select($sql);
                        if($result["result"] == "1"){ //쿼리가 성공이면
                            $this->result = $result;
                            $this->result["message"] = "lang table 마지막 idx값 검색 성공";
                            $sql = "alter table lang auto_increment = ".$result["value"][0]["count"];
                            $result = $this->conn->db_update($sql);
                            if($result["result"] == "1"){ //쿼리가 성공이면
                                $this->result = $result;
                                $this->result["message"] = "lang table auto_increment 값 수정 성공";
                            }else{
                                $this->result["result"] = "0";
                                $this->result["error_code"] = "301";
                                $this->result["message"] = "lang table auto_increment 값 수정 실패";
                            }
                        }else{
                            $this->result["result"] = "0";
                            $this->result["error_code"] = "302";
                            $this->result["message"] = "lang table 마지막 idx값 검색 실패";
                        }
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "301";
                        $this->result["message"] = "lang table idx값 수정 실패";
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "300";
                    $this->result["message"] = "프로젝트 사용 언어 삭제 실패";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 프로젝트 DB 생성하기
        // 설 명 : 쇼핑몰 Base Table이 없으면 자동으로 Table을 생성해주는 함수
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function create_project_table(){
            $schema = "shoppingmall";
            $this->create_admin($schema);
            $this->create_project_setting($schema);
            $this->create_lang($schema);
            $this->create_user_grade($schema);
            $this->create_user_grade_name($schema);
            $this->create_user($schema);
            $this->create_phone_certify($schema);
            $this->create_point($schema);
            $this->create_point_user_history($schema);
            $this->create_product($schema);
            $this->create_product_name($schema);
            $this->create_product_img($schema);
            $this->create_product_category_relation($schema);
            $this->create_main_category($schema);
            $this->create_main_category_name($schema);
            $this->create_category_1($schema);
            $this->create_category_1_name($schema);
            $this->create_category_2($schema);
            $this->create_category_2_name($schema);
            $this->create_category_3($schema);
            $this->create_category_3_name($schema);
            $this->create_option_1($schema);
            $this->create_option_1_name($schema);
            $this->create_option_2($schema);
            $this->create_option_2_name($schema);
            $this->create_option_3($schema);
            $this->create_option_3_name($schema);
            $this->create_option_4($schema);
            $this->create_option_4_name($schema);
            $this->create_purchase_order($schema);
            $this->create_order_product($schema);
            $this->create_order_product_name($schema);
            $this->create_coupon($schema);
            $this->create_coupon_name($schema);
            $this->create_coupon_relation($schema);
            $this->create_setting($schema);
            $this->create_complain_list($schema);
            $this->create_delivery_price($schema);
            $this->create_admin_email_alarm($schema);
            $this->create_admin_sms_alarm($schema);
            $this->create_icon($schema);
            $this->create_banner($schema);
            $this->create_shopping_basket($schema);
            $this->create_bookmark($schema);
            $this->create_product_qna($schema);
            $this->create_product_review($schema);
            $this->create_product_review_img($schema);
            $this->create_terms($schema);
            $this->create_terms_name($schema);
            $this->create_terms_agree($schema);
            $this->create_deposit_account($schema);
            $this->create_bank($schema);
            $this->create_1to1inquiry($schema);
            $this->create_1to1inquiry_img($schema);
            $this->create_admin_user_sms_email_setting($schema);
            $this->create_notice($schema);
            $this->create_notice_name($schema);
            $this->create_faq($schema);
            $this->create_faq_name($schema);
        }

        function create_admin($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'admin' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `admin` (
                        `idx` int(11) NOT NULL AUTO_INCREMENT,
                        `id` text,
                        `pw` text,
                        `name` text,
                        PRIMARY KEY (`idx`)
                      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_project_setting($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'project_setting' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `project_setting` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_count` varchar(45) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_lang($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'lang' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `lang` (
                        `idx` int(11) NOT NULL AUTO_INCREMENT,
                        `name` text,
                        `icon_file_name` text COMMENT '아이콘 파일 이름',
                        PRIMARY KEY (`idx`)
                      ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_user_grade($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'user_grade' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `user_grade` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `discount_rate` int(11) DEFAULT NULL COMMENT '할인율',
                      `max_price` int(11) DEFAULT NULL COMMENT '최대 할인금액',
                      `point_percent` int(11) DEFAULT NULL COMMENT '적립금 퍼센트',
                      `buy_count` int(11) DEFAULT NULL COMMENT '조건 구매횟수',
                      `buy_condition` int(11) DEFAULT NULL COMMENT '조건 금액',
                      `rank` int(11) DEFAULT NULL COMMENT '순위',
                      `is_use` int(11) DEFAULT NULL COMMENT '사용유무 0:미사용 1: 사용',
                      `delivery_free` int(11) DEFAULT NULL COMMENT '0 : 무료배송아님 1 : 무료배송 ',
                      `sequence` int(11) DEFAULT NULL COMMENT '순서 ',
                      `regdate` datetime DEFAULT NULL,
                      `update_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_user_grade_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'user_grade_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `user_grade_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_grade_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_user($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'user' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `user` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `pw` text COMMENT '비밀번호',
                      `post_code` text COMMENT '우편번호',
                      `address` text COMMENT '주소',
                      `detail_address` text COMMENT '상세주소',
                      `name` text COMMENT '이름',
                      `email` text COMMENT '이메일',
                      `phone` text COMMENT '핸드폰번호',
                      `state` int(11) unsigned DEFAULT '1' COMMENT '0: 휴면  1:정상 2:탈퇴',
                      `regdate` datetime DEFAULT NULL COMMENT '가입시기',
                      `dormant_regdate` datetime DEFAULT NULL COMMENT '휴면시기',
                      `leave_regdate` datetime DEFAULT NULL COMMENT '탈퇴시기',
                      `update_regdate` datetime DEFAULT NULL,
                      `pw_update_regdate` datetime DEFAULT NULL,
                      `user_grade_idx` int(11) DEFAULT NULL COMMENT '등급',
                      `email_agree` int(11) DEFAULT '0' COMMENT '이메일 수신 동의 1: 동의 0: 거부',
                      `sms_agree` int(11) DEFAULT '0' COMMENT '문자수신동의 1: 동의0:거부',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_phone_certify($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'phone_certify' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `phone_certify` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `code` text COMMENT '인증번호',
                      `key` text COMMENT '랜덤생성 key',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_point($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'point' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `point` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `point` int(11) DEFAULT NULL COMMENT '발급된 전체 포인트',
                      `state` int(11) DEFAULT NULL COMMENT '포인트 상태 0: 포인트없음 1: 포인트있음',
                      `remnant_point` int(11) DEFAULT NULL COMMENT '잔여포인트',
                      `regdate` datetime DEFAULT NULL COMMENT '포인트 적립 시간',
                      `expire_date` datetime DEFAULT NULL COMMENT '적립금 만료일자',
                      `point_title` text COMMENT '적립금 내용 ',
                      `return_point` int(11) DEFAULT '0',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_point_user_history($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'point_user_history' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `point_user_history` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `point_idx` int(11) DEFAULT NULL,
                      `use_point` int(11) DEFAULT NULL COMMENT '사용된 포인트',
                      `order_number` text COMMENT '사용된 주문번호',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_code` text COMMENT '제품코드',
                      `price` int(11) DEFAULT NULL COMMENT '가격',
                      `icon_idx` int(11) DEFAULT NULL COMMENT '아이콘 idx',
                      `is_stock` int(11) DEFAULT NULL COMMENT '재고 설정 on/off    0:off. 1:on',
                      `total_stock` int(11) DEFAULT NULL COMMENT '재고수량',
                      `is_discount` int(11) DEFAULT NULL COMMENT '할인 타입  0:없음 1:% discount_percent 참고, 2: 금액 discount_price 참고',
                      `discount_percent` int(11) DEFAULT NULL COMMENT '할인율',
                      `discount_price` int(11) DEFAULT NULL COMMENT '할인금액',
                      `shop_code` text COMMENT '매장코드',
                      `regdate` datetime DEFAULT NULL COMMENT '등록시간',
                      `modify_regdate` datetime DEFAULT NULL COMMENT '수정시간',
                      `is_detail_type` int(11) DEFAULT NULL COMMENT '상세페이지 type. 0:description 1:image',
                      `keyword` text COMMENT '검색 키워드 ',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) NOT NULL COMMENT 'product의 idx',
                      `product_name` text,
                      `thumnail_file` text COMMENT '썸네일 파일 이름   ',
                      `description` text COMMENT '상세페이지 내용\n',
                      `lang_idx` int(11) DEFAULT NULL COMMENT '언어idx',
                      `state` int(11) DEFAULT NULL COMMENT '1 :정상, 2: 품절, 3:숨김',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_img($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_img' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_img` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL COMMENT '제품 idx',
                      `file_name` text COMMENT '이미지 파일 이름',
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_category_relation($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_category_relation' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_category_relation` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL COMMENT '제품 idx',
                      `category_idx` int(11) DEFAULT NULL COMMENT '카테고리 idx',
                      `sequence` int(11) DEFAULT NULL COMMENT '해당 카테고리의 상품 순서',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_main_category($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'main_category' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `main_category` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `sequence` int(11) DEFAULT NULL COMMENT '순서 ',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_main_category_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'main_category_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `main_category_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_1($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_1' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_1` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `main_category_idx` int(11) DEFAULT NULL COMMENT '메인카테고리 idx',
                      `sequence` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_1_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_1_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_1_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_1_idx` int(11) DEFAULT NULL,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_2($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_2' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_2` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_1_idx` int(11) DEFAULT NULL,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `sequence` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_2_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_2_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_2_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_2_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `category_1_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_3($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_3' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_3` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_2_idx` int(11) DEFAULT NULL,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `category_1_idx` int(11) DEFAULT NULL,
                      `sequence` int(11) DEFAULT NULL COMMENT '카테고리 이름',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_category_3_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'category_3_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `category_3_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `category_3_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `main_category_idx` int(11) DEFAULT NULL,
                      `category_1_idx` int(11) DEFAULT NULL,
                      `category_2_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_1($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_1' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_1` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL,
                      `price` int(10) unsigned DEFAULT '0' COMMENT '옵션 가격 안쓸경우 0',
                      `is_stock` int(11) DEFAULT '0' COMMENT '재고 사용 유무',
                      `total_stock` int(11) DEFAULT '0' COMMENT '재고 사용시 재고',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_1_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_1_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_1_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `option_1_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_2($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_2' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_2` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL,
                      `option_1_idx` int(11) DEFAULT NULL,
                      `price` int(10) unsigned DEFAULT '0' COMMENT '옵션 가격',
                      `is_stock` int(11) DEFAULT '0' COMMENT '재고 사용 유무',
                      `total_stock` int(11) DEFAULT '0' COMMENT '재고 사용시 재고',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_2_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_2_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_2_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `option_2_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_3($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_3' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_3` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL COMMENT '제품 idx',
                      `option_2_idx` int(11) DEFAULT NULL COMMENT 'option_2 idx',
                      `price` int(11) unsigned DEFAULT '0' COMMENT '옵션 가격',
                      `is_stock` int(11) DEFAULT '0' COMMENT '재고 사용 유무',
                      `total_stock` int(11) DEFAULT '0' COMMENT '재고 사용시 재고',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_3_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_3_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_3_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `option_3_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_4($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_4' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_4` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_idx` int(11) DEFAULT NULL,
                      `option_3_idx` int(11) DEFAULT NULL,
                      `price` int(10) unsigned DEFAULT '0' COMMENT '옵션 가격',
                      `is_stock` int(11) DEFAULT '0' COMMENT '재고 사용 유무',
                      `total_stock` int(11) DEFAULT '0' COMMENT '재고 사용시 재고',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_option_4_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'option_4_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `option_4_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `option_4_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_purchase_order($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'purchase_order' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `purchase_order` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `order_number` text COMMENT '주문번호',
                      `user_idx` int(11) DEFAULT NULL COMMENT '주문한 계정의 idx 비회원일경우 null',
                      `user_type` int(11) DEFAULT NULL COMMENT '0: 비회원 1: 회원',
                      `imp_uid` text,
                      `order_full_info` text COMMENT '주문 전체 데이터 json',
                      `pay_type` text COMMENT '결제방식 card, v_bank, bank ...등등\n\ncard : 카드, account : 무통장, transfer : 계좌이체\n',
                      `receive_name` text COMMENT '받을사람 이름',
                      `post_code` text COMMENT '우편번호',
                      `address` text COMMENT '주소',
                      `detail_address` text COMMENT '상세주소',
                      `delivery_message` text COMMENT '배송메세지',
                      `receive_phone_number` text COMMENT '핸드폰 번호',
                      `total_price` int(11) DEFAULT NULL COMMENT '전체 가격',
                      `delivery_price` int(11) DEFAULT NULL COMMENT '배송료',
                      `use_point` int(11) DEFAULT NULL COMMENT '사용포인트(사용된 포인트 값)',
                      `use_coupon_idx` int(11) DEFAULT NULL COMMENT '사용된 쿠폰 idx(cupon_relation_idx)',
                      `use_sail_price` int(11) DEFAULT NULL COMMENT '할인된 총 가격',
                      `nonmember_passwrod` text COMMENT '비회원 주문시 비밀번호(회원이 입력함)',
                      `state` int(11) DEFAULT NULL COMMENT '상태 0: 입금대기, 1: 주문완료,2:배송준비중, 3:배송중, 4: 완료,5: 취소 ',
                      `deposit_regdate` datetime DEFAULT NULL COMMENT '입금완료 등록시간',
                      `delivery_ready_regdate` datetime DEFAULT NULL COMMENT '배송준비중 등록시간',
                      `on_delivery_regdate` datetime DEFAULT NULL COMMENT '배송중 등록시간',
                      `complete_regdate` datetime DEFAULT NULL COMMENT '완료(거래종료) 등록시간',
                      `cancel_regdate` datetime DEFAULT NULL COMMENT '취소 등록시간(해당 시간은 무통장 입금 대기에서 취소 했을경우)',
                      `regdate` datetime DEFAULT NULL COMMENT '주문 등록 시간',
                      `orderer_name` text,
                      `orderer_phone` text,
                      `orderer_email` text,
                      `cash_receipt_request` int(11) DEFAULT NULL COMMENT '0 : 없음 1 : 요청\n\n(현금영수증 발행요청)\n',
                      `cash_receipt_name` text COMMENT '현금영수증 이름\n',
                      `cash_receipt_number` text COMMENT '현금영수증 번호',
                      `cash_receipt_issue_kind` int(11) DEFAULT NULL COMMENT '현금영수증 발급 종류\n1 : 현금영수증 번호 2 : 휴대폰 번호\n',
                      `cash_receipt_trader_kind` int(11) DEFAULT NULL COMMENT '거래자 구분\n\n1 : 소득공제용 2 : 지출 증빙용 3: 문화비용\n',
                      `cash_receipt_regdate` datetime DEFAULT NULL,
                      `cash_receipt_state` int(11) DEFAULT NULL COMMENT '0 : 발급대상아님\n1: 미발급\n2: 발급완료',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_order_product($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'order_product' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `order_product` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `purchase_order_idx` int(11) DEFAULT NULL COMMENT 'purchase_order idx',
                      `product_code` text COMMENT '제품코드',
                      `price` int(11) DEFAULT NULL COMMENT '제품가격',
                      `is_discount` int(11) DEFAULT NULL COMMENT '0:없음 1:% 2:금액',
                      `discount_price` int(11) DEFAULT NULL COMMENT '할인되는 가격',
                      `shop_code` text COMMENT '매장코드',
                      `total_price` int(11) DEFAULT NULL COMMENT '총 제품 가격\n',
                      `state` int(11) DEFAULT NULL COMMENT ' 0: 입금대기, 1: 주문완료,2:배송준비중, 3:배송중, 4 :완료 , 5 : 취소,  6:환불요청, 7:환불 확인 8: 환불완료, 9:교환요청,10:교환중, 11:교환완료, 12:취소요청,  13 : 반품요청 14: 반품중 15:반품완료  ',
                      `product_idx` int(11) DEFAULT NULL,
                      `option_1_idx` int(11) DEFAULT NULL,
                      `option_2_idx` int(11) DEFAULT NULL,
                      `option_3_idx` int(11) DEFAULT NULL,
                      `option_4_idx` int(11) DEFAULT NULL,
                      `option_1_price` int(11) DEFAULT NULL,
                      `option_2_price` int(11) DEFAULT NULL,
                      `option_3_price` int(11) DEFAULT NULL,
                      `option_4_price` int(11) DEFAULT NULL,
                      `give_point` int(11) DEFAULT NULL COMMENT '지급 포인트 ',
                      `cash_receipt_issue_regdate` datetime DEFAULT NULL COMMENT '현금영수증 발급 일시\n',
                      `cash_receipt_cancel_regdate` datetime DEFAULT NULL COMMENT '현금 영수증 취소 일시 ',
                      `cash_receipt_regdate` datetime DEFAULT NULL,
                      `refund_request_regdate` datetime DEFAULT NULL COMMENT '환불요청 등록 시간',
                      `refund_check_regdate` datetime DEFAULT NULL COMMENT '환불확인 등록 시간',
                      `refund_complete_regdate` datetime DEFAULT NULL COMMENT '환불완료 등록 시간',
                      `cancel_complete_regdate` datetime DEFAULT NULL COMMENT '취소 완료 일시\n',
                      `cancel_request_regdate` datetime DEFAULT NULL,
                      `exchange_request_regdate` datetime DEFAULT NULL COMMENT '교환요청 등록시간',
                      `exchanging_regdate` datetime DEFAULT NULL COMMENT '교환중 등록 시간',
                      `exchange_complete_regdate` datetime DEFAULT NULL COMMENT '교환완료 등록 시간',
                      `return_request_regdate` datetime DEFAULT NULL,
                      `return_complete_regdate` datetime DEFAULT NULL,
                      `reason` text COMMENT '사유',
                      `cnt` int(11) DEFAULT '1',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_order_product_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'order_product_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `order_product_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `product_name` text,
                      `option_1_name` text,
                      `option_2_name` text,
                      `option_3_name` text,
                      `option_4_name` text,
                      `order_product_thumnail` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `order_product_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_coupon($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'coupon' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `coupon` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `discount_price` int(11) DEFAULT NULL COMMENT '쿠폰할인 금액\ndiscount_kind = 할인 종류가 percent일떄\n',
                      `discount_percent` int(11) DEFAULT NULL COMMENT '쿠폰 할인 퍼센트',
                      `max_discount_price` int(11) DEFAULT NULL COMMENT '최대 할인 가격',
                      `min_limited` int(11) DEFAULT '0' COMMENT '제한가격\nex) 15,000원 이',
                      `regdate` datetime DEFAULT NULL COMMENT '쿠폰 등록일\n',
                      `target` int(11) DEFAULT NULL COMMENT '1:회원전체, 2: 등급별',
                      `state` int(10) DEFAULT '1' COMMENT '1 : 생성 2 : 발급완료 3 :회수 ',
                      `user_grade_idx` int(11) DEFAULT NULL COMMENT '등급별 발급시',
                      `issue_kind` int(11) DEFAULT NULL COMMENT '1 : 다운로드 2: 자동발급 3 : 수동발급\n',
                      `issue_start_date` datetime DEFAULT NULL COMMENT '발급기간 시작일\n',
                      `issue_end_date` datetime DEFAULT NULL COMMENT '발급기간 종료일\n',
                      `use_start_date` datetime DEFAULT NULL COMMENT '사용 기한 시작일\n',
                      `use_end_date` datetime DEFAULT NULL COMMENT '사용기한 종료일\n',
                      `expiration_date` datetime DEFAULT NULL COMMENT '유효기간',
                      `delivery_coupon` int(11) DEFAULT NULL COMMENT '0 : 무료배송 쿠폰 아님 1 : 무료배송 쿠',
                      `discount_domain` int(11) DEFAULT NULL COMMENT ' 적용범위 1 : 전체 2: 카테고리',
                      `discount_kind` int(11) DEFAULT NULL COMMENT '1 : 원 2 : %',
                      `coupon_limit` int(11) DEFAULT NULL COMMENT '1 : 제한 없음 2 : 이미 할인 중인 상품 쿠폰 사용 불가 3 : 쿠폰 중복 사용 불가\n',
                      `main_category_idx` int(11) DEFAULT NULL,
                      `category_1_idx` int(11) DEFAULT NULL,
                      `category_2_idx` int(11) DEFAULT NULL,
                      `category_3_idx` int(11) DEFAULT NULL,
                      `retrieve_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_coupon_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'coupon_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `coupon_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `coupon_idx` int(11) DEFAULT NULL,
                      `name` text,
                      `img` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_coupon_relation($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'coupon_relation' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `coupon_relation` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `coupon_idx` int(11) DEFAULT NULL,
                      `regdate` datetime DEFAULT NULL COMMENT '발급 일시 ',
                      `is_use` int(11) DEFAULT NULL COMMENT '0: 미사용 1: 사용 2: 회수됨',
                      `use_date` datetime DEFAULT NULL COMMENT '사용 날짜',
                      `purchase_order_idx` int(11) DEFAULT NULL COMMENT '사용한 주문서',
                      `retrieve_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_setting($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'setting' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE  `shoppingmall`.`setting` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `coupon_limit` int(11) unsigned DEFAULT '2' COMMENT '0:불가, 1:무통장만 2:전체허용',
                      `discount_limit` int(11) unsigned DEFAULT '1' COMMENT '0: off, 1:on',
                      `auto_delivery_complete` int(11) unsigned DEFAULT '1' COMMENT '0: off, 1:on',
                      `auto_delivery_day` int(11) unsigned DEFAULT '3' COMMENT '자동배송완료 기간(3~7)',
                      `deposit_day` int(11) unsigned DEFAULT '5' COMMENT '입금기한(무통장) 1~5일까지만',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_complain_list($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'complain_list' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `complain_list` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `kind` int(11) DEFAULT NULL COMMENT '1:취소 2:환불 3:교환',
                      `code` int(11) DEFAULT NULL COMMENT '사유 내용',
                      `name` text,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_delivery_price($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'delivery_price' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `delivery_price` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `name` text,
                      `price` int(11) DEFAULT NULL COMMENT '배송료',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_admin_email_alarm($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'admin_email_alarm' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `admin_email_alarm` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `email` text COMMENT '알림 받을 관리자 이메일',
                      `is_join` int(11) NOT NULL DEFAULT '1' COMMENT '회원가입 알림 0 : off 1: on',
                      `is_order_complete` int(11) NOT NULL DEFAULT '1' COMMENT '주문들어옴 알림 0 : off 1: on',
                      `auto_deposit` int(11) NOT NULL DEFAULT '1' COMMENT '자동 입금확인 알림 0 : off 1: on',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_admin_sms_alarm($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'admin_sms_alarm' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `admin_sms_alarm` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `phone_number` text,
                      `is_join` int(11) NOT NULL DEFAULT '1' COMMENT '회원가입 알림 0 : off 1: on',
                      `is_order_complete` int(11) NOT NULL DEFAULT '1' COMMENT '주문들어옴 알림 0 : off 1: on',
                      `auto_deposit` int(11) NOT NULL DEFAULT '1' COMMENT '자동 입금확인 알림 0 : off 1: on',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_icon($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'icon' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `icon` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `name` text COMMENT '아이콘 이름',
                      `file_name` text COMMENT '파일이름',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_banner($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'banner' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `banner` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `name` text COMMENT '배너이름',
                      `file_name` text COMMENT '파일이름',
                      `kind` int(11) DEFAULT NULL COMMENT '종류 : 배너 위치에 따른 구분',
                      `lang_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_shopping_basket($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'shopping_basket' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `shopping_basket` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `product_idx` int(11) DEFAULT NULL,
                      `regdate` datetime DEFAULT NULL,
                      `count` int(11) DEFAULT NULL,
                      `option_1_idx` int(11) DEFAULT NULL,
                      `option_2_idx` int(11) DEFAULT NULL,
                      `option_3_idx` int(11) DEFAULT NULL,
                      `option_4_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_bookmark($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'bookmark' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `bookmark` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `product_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_qna($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_qna' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_qna` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `product_idx` int(11) DEFAULT NULL,
                      `title` text,
                      `content` text,
                      `answer` text,
                      `regdate` datetime DEFAULT NULL,
                      `answer_date` datetime DEFAULT NULL,
                      `password` text COMMENT '비밀글일 경우 패스워드',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_review($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_review' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_review` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `product_idx` int(11) DEFAULT NULL,
                      `review` text COMMENT 'Description 내용',
                      `grade_point` int(11) DEFAULT NULL COMMENT '평점(몇점 만점이 기준이냐에 따라 달라짐)',
                      `title` text,
                      `regdate` datetime DEFAULT NULL,
                      `purchase_order_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_product_review_img($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'product_review_img' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `product_review_img` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `review_idx` int(11) NOT NULL,
                      `img` text,
                      `regdate` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_terms($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'terms' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `terms` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `kind` int(11) DEFAULT NULL COMMENT '약관 종류   1: 개인정보처리방침……',
                      `regdate` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_terms_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'terms_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `terms_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `content` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `terms_idx` int(10) unsigned DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_terms_agree($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'terms_agree' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `terms_agree` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `user_idx` int(11) DEFAULT NULL,
                      `terms_idx` int(11) DEFAULT NULL,
                      `regdate` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원 이용약관 동의 테이블\n';";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_deposit_account($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'deposit_account' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `deposit_account` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `bank_idx` int(11) DEFAULT NULL COMMENT '은행 idx',
                      `account_number` text COMMENT '계좌번호',
                      `depositor` text COMMENT '예금주',
                      `regdate` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_bank($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'bank' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `bank` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `bank_name` text COMMENT '은행이름',
                      `code` text COMMENT '은행코드',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_1to1inquiry($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = '1to1inquiry' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `1to1inquiry` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `kind` int(11) DEFAULT NULL COMMENT '1 : 주문문의 2 : 주문변경 3 : 취소신청 4 : 교환신청 5 : 환불신청 6 :기타  ',
                      `user_idx` int(11) DEFAULT NULL,
                      `purchase_order_idx` int(11) DEFAULT NULL,
                      `title` text,
                      `content` text,
                      `answer` text,
                      `regdate` datetime DEFAULT NULL,
                      `answer_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_1to1inquiry_img($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = '1to1inquiry_img' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `1to1inquiry_img` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `inquiry_idx` int(11) DEFAULT NULL,
                      `img` text,
                      `regdate` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_admin_user_sms_email_setting($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'admin_user_sms_email_setting' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `admin_user_sms_email_setting` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `send_email` text,
                      `send_number` text,
                      `e_join` int(11) DEFAULT '0' COMMENT '회원가입 0 : off 1: on',
                      `e_order_complete` int(11) DEFAULT '0' COMMENT '주문완료 0 : off 1: on',
                      `e_deposit_complete` int(11) DEFAULT '0' COMMENT '입금완료 0 : off 1:on',
                      `e_shipment` int(11) DEFAULT '0' COMMENT '상품발송 0 : off 1: on',
                      `e_auto_deposit` int(11) DEFAULT '0' COMMENT '자동입금확인 0 : off 1 : on',
                      `e_deposit_request` int(11) DEFAULT '0' COMMENT '입금요청 0 : off 1 : on',
                      `e_question` int(11) DEFAULT '0' COMMENT '질문답변 0 :off 1: on ',
                      `s_join` int(11) DEFAULT '0',
                      `s_order_complete` int(11) DEFAULT '0',
                      `s_deposit_complete` int(11) DEFAULT '0',
                      `s_shipment` int(11) DEFAULT '0',
                      `s_auto_deposit` int(11) DEFAULT '0',
                      `s_deposit_request` int(11) DEFAULT '0',
                      `s_question` int(11) DEFAULT '0',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_notice($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'notice' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `notice` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `regdate` datetime DEFAULT NULL,
                      `update_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='공지사항 테이블';";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_notice_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'notice_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `notice_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `title` text,
                      `content` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `notice_idx` int(11) DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='언어별 공지사항 제목 및 내용';";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_faq($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'faq' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `faq` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `regdate` datetime DEFAULT NULL,
                      `update_date` datetime DEFAULT NULL,
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='faq';";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }

        function create_faq_name($schema){
            $table_check_sql = "SELECT EXISTS (
                SELECT 1 FROM Information_schema.tables 
                WHERE table_schema = '$schema' 
                AND table_name = 'faq_name' 
                ) AS flag;";

            $list = $this->conn->db_select($table_check_sql);
            if($list["error_code"]){
                $this->result["result"]="0";
                $this->result["error_code"]=$list["error_code"];
                $this->result["message"]=$list["error_msg"];
            }else{
                $list = $list["value"];
                if($list[0]["flag"] == "0"){ //테이블이 없으면 create
                    $create_sql = "CREATE TABLE `faq_name` (
                      `idx` int(11) NOT NULL AUTO_INCREMENT,
                      `title` text,
                      `content` text,
                      `lang_idx` int(11) DEFAULT NULL,
                      `faq_idx` int(11) DEFAULT NULL,
                      `kind` int(11) DEFAULT NULL COMMENT '1 : 주문/결제\n2 : 배송/반품\n3 : 회원\n4 : 기타\n',
                      PRIMARY KEY (`idx`)
                    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='언어별 faq';";
                    
                    $this->conn->db_create($create_sql);
                }
            }
        }
    }
?>