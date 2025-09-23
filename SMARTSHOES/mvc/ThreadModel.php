<?php
    class ThreadModel extends gf{
        private $param;
        private $dir;
        private $conn;
        private $file_manager;

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
            $this->project_name = $array["project_name"];
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
            if($check_result["result"]){//param 값 체크 비어있으면 실행 안함s
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
        // 함 수 : 쓰레드처럼 실행해야하는 함수
        // 설 명 : 이 함수에 해야하는 함수들을 호출할것(호출은 매일 일정시간에 호출해줌)
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function thread_call(){
            $year = date("Y"); //년도 4자리
            $month = date("m"); //월 2자리
            $day = date("d"); //일 2자리


            //매일 실행하는 쓰레드 
            $sql = "select * from thread_time where kind=0 order by datetime desc LIMIT 1;"; //매일 실행 최근 실행 날짜 조회
            $result = $this->conn->db_select($sql);
            $insert_datetime_flag = false;
            if(count($result["value"]) == 0){ //한번도 실행한적없음으로 실행
                $this->every_day_call();
                $insert_datetime_flag = true;
                echo "every_day_call [실행 완료]";
            }else{
                $dayString = $result["value"][0]["datetime"];
                $last_date = new DateTime($dayString);
                $now_date = new DateTime((date("Y-m-d")));
                $diff_day = date_diff($last_date,$now_date);
                $diff_days = $diff_day->days;
                if($diff_days != 0){ //오늘 실행되지 않았다면 실행
                    $this->every_day_call();
                    $insert_datetime_flag = true;
                    echo "every_day_call [실행 완료]";
                }else{
                    echo "every_day_call [이미 실행된 날짜입니다]";
                }
            }

            if($insert_datetime_flag == true){
                $db = new db($this->project_name); // DB변경시 여기 수정
                $this->conn = new AppDB($db, $this->file_manager);
                $sql = "insert into thread_time(kind,datetime) values(0,".$this->null_check($year."-".$month."-".$day." 00:00:00").");";
                $this->conn->db_insert($sql);
            }

            //매월 실행하는 쓰레드
            $insert_datetime_flag = false; // flag초기화
            if(true){ //테스트코드
            // if($day == "01"){ //1일이면 이번달 실행 했는지 확인 후 실행
                $sql = "select * from thread_time where kind=1 order by datetime desc LIMIT 1;"; //매일 실행 최근 실행 날짜 조회
                $result = $this->conn->db_select($sql);
                if(count($result["value"]) == 0){ //한번도 실행한적없음으로 실행
                    $this->month_call();
                    $insert_datetime_flag = true;
                    echo "month_call [실행 완료]";
                }else{
                    $dayString = $result["value"][0]["datetime"];
                    $last_date = new DateTime($dayString);
                    $now_date = new DateTime((date("Y-m-d")));
                    $last_year = $last_date->format('Y');
                    $now_year = $now_date->format('Y');
                    $last_month = $last_date->format('m');
                    $now_month = $now_date->format('m');
                    if($last_year == $now_year){ //년도가 같으면 월 비교해서 실행
                        if($last_month < $now_month){ //이전에 실행한 월보다 현재가 높다면 실행
                            //월 실행
                            $this->month_call();
                            $insert_datetime_flag = true;
                            echo "month_call 실행";
                        }else{
                            echo "month_call [이미 실행된 달입니다 ".$now_month."월 ]";    
                            
                        }
                    }else{ //년도가 바뀜
                        $this->month_call();
                        $insert_datetime_flag = true;
                        echo "month_call 실행";
                    }
                }
                if($insert_datetime_flag == true){
                    $db = new db($this->project_name); // DB변경시 여기 수정
                    $this->conn = new AppDB($db, $this->file_manager);
                    $sql = "insert into thread_time(kind,datetime) values(1,".$this->null_check($year."-".$month."-".$day." 00:00:00").");";
                    $this->conn->db_insert($sql);
                }
            }else{
                echo "month_call [1일이 아닙니다]";
            }

            
            // $hour = date("H");
            // $minute = date("i");
            // $second = date("s");
            
            
            // if($day == "01"){ //1일이면 해야하는 메소드 호출
            //     //등급 관련 코드 호출 
            //     $this->grade_check(); //등급 변경
            //     //등급별 쿠폰 발급
            //     $this->grade_coupon_issue(); //등급별 할인 쿠폰 발급
            //     //생일자 쿠폰 발급
            //     $this->birthday_coupon_issue($month);
            // }
        }


        //매월 실행해야하는것 (매달1일)
        function month_call(){
            $month = date("m"); //월 2자리
            //등급 관련 코드 호출 
            $this->grade_check(); //등급 변경
            //등급별 쿠폰 발급
            $db = new db($this->project_name); // DB변경시 여기 수정
            $this->conn = new AppDB($db, $this->file_manager);
            $this->grade_coupon_issue(); //등급별 할인 쿠폰 발급

            //생일자 쿠폰 발급
            $db = new db($this->project_name); // DB변경시 여기 수정
            $this->conn = new AppDB($db, $this->file_manager);
            $this->birthday_coupon_issue($month);

            //인스타 토큰 갱신 ( 아이피아만 적용 )
            $db = new db($this->project_name); // DB변경시 여기 수정
            $this->conn = new AppDB($db, $this->file_manager);
            $this->refresh_insta_token();
        }

        //매일 실행해야하는것
        function every_day_call(){
            //무조건 실행해야하는 메소드는 여기서 호출
            $this->account_delivery_check();//무통장 입금 대기중인 상태에서 취소 상태로 변경 메소드, 택배 발송후 완료 처리 변경 메소드
            $this->coupon_expired();//쿠폰 유효기간 만료된 쿠폰 만료처리(호출된 날짜 기준 이전이 만료날짜인 쿠폰을 만료처리)
            $this->reset_n_basket();//하루마다 비회원 장바구니 초기화 함수
            
            // $this->result["result"] = 1;
            // echo $this->jsonEncode($this->result);



            // 사용기간이 만료된 플러스 친구 쿠폰 회수처리
            $this->expire_kakao_coupon();
        }


        /********************************************************************* 
        // 함 수 : 등급 변경
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function grade_check(){
            $grade_sql = "select * from user_grade order by sequence desc;";
            $grade_result = $this->conn->db_select($grade_sql);
            // print_r($grade_result["value"]);
            $grade_data = $grade_result["value"];
            
            //트랜잭션 시작
            $this->conn->s_transaction();
            for($i=0; $i<count($grade_data);$i++){//하위등급부터 차례대로 체크해서 등급업을 시킨다.
                $grade_idx = $grade_data[$i]["idx"]; //변경할 등급의 idx

                $now_condition = $grade_data[$i]["buy_condition"];//현재조건
                $next_condition = null; //상위조건
                if(isset($grade_data[$i+1])){ //다음 등급 조건이 있다면
                    $next_condition = $grade_data[$i+1]["buy_condition"];
                }
                $in_sql = null;
                if($next_condition != null){ //다음 등급 조건이 있다면 현재 등급 조건 이상 다음등급조건 미만의 인원을 update시킨다
                    $in_sql = "select user_idx from purchase_order ";
                    $in_sql = $in_sql."where regdate > LAST_DAY(NOW() - interval 2 month) and regdate < (LAST_DAY(NOW() - interval 1 month) + interval 1 DAY) and user_type=1 and state = 4 group by user_idx ";
                    $in_sql = $in_sql."HAVING (sum(total_price)-sum(refund_price)) >= ".$now_condition." and (sum(total_price)-sum(refund_price)) < ".$next_condition;
                }else{ //다음등급이 없다면 무조건 이것 이상이면 등급업(마지막 등급일경우 실행되겟죠잉)
                    $in_sql = "select user_idx from purchase_order ";
                    $in_sql = $in_sql."where regdate > LAST_DAY(NOW() - interval 2 month) and regdate < (LAST_DAY(NOW() - interval 1 month) + interval 1 DAY) and user_type=1 and state = 4 group by user_idx ";
                    $in_sql = $in_sql."HAVING (sum(total_price)-sum(refund_price)) >= ".$now_condition;
                }
                
                //해당하는 사용자 등급 변경
                $update_sql = "update user set user_grade_idx=".$grade_idx." where idx in(".$in_sql.");";
                $this->conn->db_update($update_sql);
            }


            //아이피아의 특이 사항
            //최상위 등급중에 저번달에 한번도 구매하지않은 사람은 등급 다운
            $update_sql = "update user set user_grade_idx=".$grade_data[count($grade_data)-2]["idx"];
            $in_sql = "SELECT idx FROM user WHERE user_grade_idx=".$grade_data[count($grade_data)-1]["idx"]." and NOT EXISTS ( SELECT user_idx FROM purchase_order WHERE user.idx = purchase_order.user_idx and regdate > LAST_DAY(NOW() - interval 2 month) and regdate < (LAST_DAY(NOW() - interval 1 month) + interval 1 DAY) and user_type=1 and state = 4)";

            $update_sql = $update_sql." where idx in (SELECT idx FROM(".$in_sql.") tmp);";
            // echo $update_sql;
            $this->conn->db_update($update_sql);

            //아이피아 특이 사항
            //프리미엄 등급 계정중 20000만원 이하인 고객들을 패밀리로 등급다운 쿼리
            //신규 프리미엄등급 계정은 30만원 이상을 구매해서 등급업이 된것임으로 이 쿼리에 걸리지 않는다.
            $update_sql = "update user set user_grade_idx=".$grade_data[count($grade_data)-2]["idx"];
            $in_sql = "select user_idx from purchase_order as t1 left join user as t2 on t1.user_idx = t2.idx
            where t2.user_grade_idx=".$grade_data[count($grade_data)-1]["idx"]." and t1.regdate > LAST_DAY(NOW() - interval 2 month) and t1.regdate < (LAST_DAY(NOW() - interval 1 month) + interval 1 DAY) and t1.user_type=1 and t1.state = 4 group by t1.user_idx
            HAVING (sum(total_price)-sum(refund_price)) < 200000";
            $update_sql = $update_sql." where idx in (SELECT idx FROM(".$in_sql.") tmp);";
            $this->conn->db_update($update_sql);

            $this->conn->commit();
        }

        /********************************************************************* 
        // 함 수 : 무통장 처리 및 배송완료 처리
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function account_delivery_check(){
            $setting_sql = "select * from setting;";
            $setting_result = $this->conn->db_select($setting_sql);
            $setting_data = $setting_result["value"][0]; //서버 설정 데이터

            // print_r($setting_data);
            if($setting_data["auto_delivery_complete"] == 1){ //자동 배송 완료 처리일 경우
                // order_product 업데이트
                $update_sql = "update order_product set state=4, complete_regdate=now() where purchase_order_idx in (select idx from purchase_order where state=3 and (TO_DAYS(now()) - TO_DAYS(on_delivery_regdate)) >= ".$setting_data["auto_delivery_day"].");";
                $this->conn->db_update($update_sql);
                //주문서 업데이트
                $update_sql = "update purchase_order set state=4, complete_regdate=now() where state=3 and (TO_DAYS(now()) - TO_DAYS(on_delivery_regdate)) >= ".$setting_data["auto_delivery_day"].";";
                $this->conn->db_update($update_sql);
            }
            // order_product 업데이트
            $update_sql = "update order_product set state=5, complete_regdate=now() where purchase_order_idx in (select idx from purchase_order where state=0 and pay_type = 'account' and (TO_DAYS(now()) - TO_DAYS(regdate)) >= ".$setting_data["deposit_day"].");";
            $this->conn->db_update($update_sql);
            //입금대기 기간 초과된 주문서 취소처리
            $update_sql = "update purchase_order set state=5 where state=0 and pay_type = 'account' and (TO_DAYS(now()) - TO_DAYS(regdate)) >= ".$setting_data["deposit_day"].";";
            $this->conn->db_update($update_sql);
        }


        

        /********************************************************************* 
        // 함 수 : 생일 쿠폰발급
        // 설 명 : 
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function birthday_coupon_issue($month){
            $i = 0;
            $welcome_coupon_idx = null;
            $family_coupon_idx = null;
            $premium_coupon_idx = null;

            while($i<3){
                $percent = "20"; //할인율(생일은 무조건 20%)
                if($i==1){ //패밀리
                    $percent = "30";
                }else if($i==2){ //프리미엄
                    $percent = "40";
                }
                $max_discount_price = "1000000"; //최대 금액(임시로 백만원으로)
                $sql = "insert into coupon(discount_price,max_discount_price,min_limited,regdate,target,issue_kind,use_start_date,use_end_date,delivery_coupon,discount_domain,discount_kind,coupon_limit,discount_kind_state)";
                $sql = $sql." VALUES(".$percent.",".$max_discount_price.",0,now(),0,4,now(),DATE_ADD(NOW(), INTERVAL 30 DAY),0,1,2,2,1);";

                $result_coupon = $this->conn->db_insert($sql); //쿠폰 생성
                if($i==0){ //웰컴
                    $welcome_coupon_idx = $result_coupon["value"]; //생성된 쿠폰 idx
                }else if($i==1){ //패밀리
                    $family_coupon_idx = $result_coupon["value"]; //생성된 쿠폰 idx
                }else if($i==2){ //프리미엄
                    $premium_coupon_idx = $result_coupon["value"]; //생성된 쿠폰 idx
                }
                $i++;
            }
            
            $this->conn->s_transaction();
            $k=0;
            while($k<3){
                $coupon_idx = null;
                $grade_idx = null;
                if($k==0){ //웰컴
                    $grade_idx = 4;
                    $coupon_idx = $welcome_coupon_idx;
                }else if($k==1){ //패밀리
                    $grade_idx = 3;
                    $coupon_idx = $family_coupon_idx;
                }else{ //프리미엄
                    $grade_idx = 2;
                    $coupon_idx = $premium_coupon_idx;
                }
                //쿠폰 이름 insert
                //사용하는 언어에 맞춰서 쿠폰이름을 생성해야함으로 언어 조회
                $select_lang_sql = "select * from lang";
                $result_lang = $this->conn->db_select($select_lang_sql);
                $lang_data = $result_lang["value"]; //언어 데이터

                //언어데이터를 가지고 생성한 쿠폰의 이름을 넣는다.
                $coupon_name_sql = "insert into coupon_name(coupon_idx,name,lang_idx) VALUES ";
                for($i=0; $i<count($lang_data); $i++){
                    if($i != 0){ //첫번째 데이터가 아니면 앞에 쉼표 붙이기
                        $coupon_name_sql = $coupon_name_sql.",";
                    }
                    $coupon_name = "생일 쿠폰";
                    if($lang_data[$i]["idx"] != 1){ //한국어가 아니면 영어로 입력
                        $coupon_name = "Birthday Discount Coupon";
                    }
                    $coupon_name_sql = $coupon_name_sql."(".$coupon_idx.",".$this->null_check($coupon_name).",".$lang_data[$i]["idx"].")";
                }
                $this->conn->db_insert($coupon_name_sql);

                //해당되는 생일자 계정에 쿠폰 발급 쿼리 실행
                $coupon_relation_sql = "insert into coupon_relation(user_idx, coupon_idx, regdate, is_use) select idx,".$coupon_idx.",now(),0 from user where user_grade_idx=".$grade_idx." and SUBSTRING(birthday,3,2) = '".$month."';";
                $this->conn->db_insert($coupon_relation_sql);
                $k++;
            }
            $this->conn->commit();
        }


        /********************************************************************* 
        // 함 수 : 등급별 쿠폰발급
        // 설 명 : 아이피에 맞춰서 코딩한것임(추후에 자동코드 만들거나 해야함)
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function grade_coupon_issue(){
            $grade_sql = "select * from user_grade order by sequence desc;";
            $grade_result = $this->conn->db_select($grade_sql);
            $grade_data = $grade_result["value"];

            $this->conn->s_transaction();
            for($i=1; $i<count($grade_data); $i++){ //등급별 쿠폰 발급(최하위 등급은 발급할게 없어서 1부터 for문 시작)
                $percent = $grade_data[$i]["month_coupon_percent"]; //등급별 발급할 쿠폰의 할인율
                $max_discount_price = "1000000"; //최대 금액(임시로 백만원으로)
                $sql = "insert into coupon(discount_price,max_discount_price,min_limited,regdate,target,issue_kind,use_start_date,use_end_date,delivery_coupon,discount_domain,discount_kind,coupon_limit,discount_kind_state)";
                $sql = $sql." VALUES(".$percent.",".$max_discount_price.",0,now(),0,4,now(),DATE_ADD(NOW(), INTERVAL 30 DAY),0,1,2,2,1);";
                
                $result_coupon = $this->conn->db_insert($sql); //쿠폰 생성
                $coupon_idx = $result_coupon["value"]; //생성된 쿠폰 idx
                
                

                //쿠폰 이름 insert
                //사용하는 언어에 맞춰서 쿠폰이름을 생성해야함으로 언어 조회
                $select_lang_sql = "select * from lang";
                $result_lang = $this->conn->db_select($select_lang_sql);
                $lang_data = $result_lang["value"]; //언어 데이터

                //언어데이터를 가지고 생성한 쿠폰의 이름을 넣는다.
                $coupon_name_sql = "insert into coupon_name(coupon_idx,name,lang_idx) VALUES ";
                for($k=0; $k<count($lang_data); $k++){
                    if($k != 0){ //첫번째 데이터가 아니면 앞에 쉼표 붙이기
                        $coupon_name_sql = $coupon_name_sql.",";
                    }
                    $coupon_name = "등급 할인 쿠폰";
                    if($lang_data[$k]["idx"] != 1){ //한국어가 아니면 영어로 입력
                        $coupon_name = "Grade Discount Coupon";
                    }
                    $coupon_name_sql = $coupon_name_sql."(".$coupon_idx.",".$this->null_check($coupon_name).",".$lang_data[$k]["idx"].")";
                }
                $coupon_name_sql = $coupon_name_sql.";";
                $coupon_result = $this->conn->db_insert($coupon_name_sql);
                

                //해당되는 등급 계정에 쿠폰 발급 쿼리 실행
                $coupon_relation_sql = "insert into coupon_relation(user_idx, coupon_idx, regdate, is_use) select idx,".$coupon_idx.",now(),0 from user where user_grade_idx=".$grade_data[$i]["idx"].";";
                $coupon_relation_result = $this->conn->db_insert($coupon_relation_sql);
                
            }
            $this->conn->commit();
        }

        /********************************************************************* 
        // 함 수 : 쿠폰 유효기간 확인
        // 설 명 : 유효기간이 지난 쿠폰은 회수처리
        // 예 시 : 
        // 만든이: 안정환
        *********************************************************************/
        function coupon_expired(){
            $sql = "update coupon_relation set is_use=3, retrieve_date=now() where coupon_idx in (select idx from coupon where use_end_date < curdate());";
            $this->conn->db_update($sql);
        }


        /********************************************************************* 
        // 함 수 : 비회원 장바구니 삭제 함수
        // 설 명 : 하루가 지나면 비회원 장바구니를 비워주는 함수
        // 예 시 : 
        // 만든이: 조경민
        *********************************************************************/
        function reset_n_basket(){
            $sql = "delete from n_shopping_basket";
            $this->conn->db_delete($sql);
        }


        /********************************************************************* 
        // 함수 설명 : 인스타 토큰 유효기간 갱신
        // 설명 : 
        // 만든이: 조경민
        *********************************************************************/
        function refresh_insta_token(){
            //갱신시킬 토큰을 가져와서 curl 전송에 사용
            $sql = "select * from instar_token";
            $result = $this->conn->db_select($sql);
            //토큰이 있는 경우만 실행
            if(count($result["value"]) > 0){
                $url = "https://graph.instagram.com/refresh_access_token/";
                $queryParams = '?' . urlencode('grant_type').'=ig_refresh_token';
                $queryParams .= '&' . urlencode('access_token').'='.urlencode($result["value"][0]["token"]); 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url.$queryParams);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_TIMEOUT , "2");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//로컬호스트에서 동작 시키려고 코드추가
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//로컬호스트에서 동작 시키려고 코드추가
                $response = curl_exec($ch);
                curl_close($ch);
                
                $data = json_decode($response);
                $access_token = $data->access_token; //새로 갱신받은 토큰
                //새로 갱신받은 토큰으로 DB에 있는 토큰 업데이트
                $sql = "update instar_token set token = ".$this->null_check($access_token);
                $this->conn->db_update($sql);
            }
        }

        /********************************************************************* 
        // 함수 설명 : 만료처리된 카카오 플러스 친구 쿠폰
        // 설명 : 
        // 만든이: 조경민
        *********************************************************************/
        function expire_kakao_coupon(){
            $cur_time = date("Y-m-d");
            $sql = "select idx from coupon ";
            $sql .= "where use_start_date != '0000-00-00 00:00:00' ";
            $sql .= "and use_end_date < curdate() ";
            $sql .= "and state = 1 ";
            $sql .= "and issue_kind = 6 ";

            $couponResult = $this->conn->db_select($sql);
            if($couponResult["result"] == 0){
                $this->result = $couponResult;
            }else{
                if(count($couponResult["value"]) > 0){
                    // 만료시킬 쿠폰이 있을 경우
                    $couponArray = $couponResult["value"];
                    $sql = "update coupon_relation set ";
                    $sql .= "retrieve_date = now() , ";
                    $sql .= "is_use = 3 ";
                    $sql .= "where idx is not null and ";
                    for($i = 0; $i<count($couponArray); $i++){
                        if(count($couponArray) == 1){
                            $sql .= "coupon_idx = ".$couponArray[$i]["idx"]." ";
                        }else{
                            if($i == 0){
                                $sql .= "coupon_idx in (".$couponArray[$i]["idx"].", ";
                            }else if($i == count($couponArray) - 1){
                                $sql .= " ".$couponArray[$i]["idx"].") ";
                            }else{
                                $sql .= " ".$couponArray[$i]["idx"].", ";
                            }
                        }
                    }
                    $sql .= "and is_use = 0 ";
                    $this->conn->db_update($sql);

                    $sql = "update coupon set ";
                    $sql .= "retrieve_date = now(), ";
                    $sql .= "state = 3 ";
                    $sql .= "where idx is not null and ";
                    for($i = 0; $i<count($couponArray); $i++){
                        if(count($couponArray) == 1){
                            $sql .= "idx = ".$couponArray[$i]["idx"]." ";
                        }else{
                            if($i == 0){
                                $sql .= "idx in (".$couponArray[$i]["idx"].", ";
                            }else if($i == count($couponArray) - 1){
                                $sql .= " ".$couponArray[$i]["idx"].") ";
                            }else{
                                $sql .= " ".$couponArray[$i]["idx"].", ";
                            }
                        }
                    }
                    $sql .= "and state = 1 ";
                    $this->conn->db_update($sql);
                }
            }
        }

    }
?>