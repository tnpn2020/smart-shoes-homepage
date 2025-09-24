<?php 
    class BillingSMSModel {
        private $param;
        private $conn;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->file_manager = $array["file_manager"];
            $this->file_path = $array["file_path"]->get_path_php();
            $this->file_link = $array["file_path"]->get_link_php();
            $this->project_name = $array["project_name"];
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );

            // 프로젝트 회사명
            $this->email_project_name = $array["email_project_name"];
            $this->send_id = $array["send_id"];
            $this->send_number = $array["send_number"];
            if($array["send_id"] == "tester"){
                $this->use_flag = "on";
            }
        }
        
        // company, send_number, text, receiver_list, user_id
        function send_tracking($param, $file){
            if(isset($this->use_flag)){
                if($this->use_flag == "off"){
                    return;
                }
            }
            
            $sql = "select phone_number from admin_sms_alarm ";
            $result = $this->conn->db_select($sql);
            if($result["result"] == 0){
                $this->result = $result;
            }else{
                $admin_phone_number = array();
                for($i = 0; $i<count($result["value"]); $i++){
                    array_push($admin_phone_number, $result["value"][$i]["phone_number"]);
                }
            }

            $sql = "select * from admin_user_sms_email_setting ";
            $user_sms_setting_result = $this->conn->db_select($sql);
            if($user_sms_setting_result["result"] == 0){
                return 0;
            }else{
                // 유저 수신 알림설정값
                $user_sms_setting = $user_sms_setting_result["value"][0];

                $sql = "select * from admin_sms_email_setting ";

                $admin_sms_setting_result = $this->conn->db_select($sql);
                if($admin_sms_setting_result["result"] == 0){
                    return 0;
                }else{
                    $admin_sms_setting = $admin_sms_setting_result["value"][0];

                    $param["user_id"] = $this->send_id;
                    $param["company"] = $this->email_project_name;
                    $param["send_number"] = $this->send_number;
                    $param["text"] = "";

                    // // 문자 타입
                    if($param["type"] == "u_join"){
                        // 회원가입시 유저에게 발신 내용
                        if($user_sms_setting["s_join"] == 0){
                            return 0;
                        }
                        $param["text"] = "♥매일매일 달라지는 피부, 닥터헤디슨♥\n\n";
                        $param["text"] .= "닥터헤디슨 공식 홈페이지에\n";
                        $param["text"] .= "회원가입 해주셔서 감사합니다.\n";
                        $param["text"] .= "닥터헤디슨 카카오톡 채널 추가하면\n\n";
                        $param["text"] .= "쿠폰 할인부터\n";
                        $param["text"] .= "다양한 이벤트 소식까지\n";
                        $param["text"] .= "가장 먼저 알림 받을 수 있어요!\n";
                        $param["text"] .= "그럼, 다양한 혜택 누리실 준비 되셨나요?\n\n";
                        $param["text"] .= "▶ 닥터헤디슨 카카오톡 채널 바로가기: http://pf.kakao.com/_stuJxb";
                        $param["template_key"] = "SJT_069985";
                        // $param["text"] = $param["company"] . " 회원가입을 축하드립니다.";
                        // $param["template_key"] = "SJT_068757";
                    }else if($param["type"] == "a_join"){
                        // 회원가입시 관리자에게 발신내용
                        // 필수 파라미터 -> $param["name"] : 회원 이름 필요
                        if($admin_sms_setting["s_join"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = $param["name"] . "님이 ".$param["company"]." 회원가입 하였습니다.";
                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068758";
                    }else if($param["type"] == "phone_certify"){
                        // 회원가입시 휴대폰 인증코드 문자
                        // 필수 파라미터 : $param["certify_number"] -> 휴대폰 인증코드;   
                        $param["text"] = "[".$param["certify_number"]."] ".$param["company"]." 핸드폰 인증코드";
                        $param["template_key"] = "SJT_068759";
                    }else if($param["type"] == "u_order_complete"){
                        // 주문 완료시 사용자가 받을 문자 메시지-> 유저에게  발송
                        // 필수 파라미터 : $param["order_number"] : 주문번호 , 
                        if($user_sms_setting["s_order_complete"] == 0){
                            return 0;
                        }
                        // $param["text"] = $param["company"] . " 주문이 완료되었습니다.\n";
                        // $param["text"] .= "주문번호 : " . $param["order_number"];
                        // $param["template_key"] = "SJT_068760";

                        $param["text"] = "♥오늘도 주문해 주셔서 감사합니다♥\n\n";
                        $param["text"] .= "주문이 완료되었습니다.\n";
                        $param["text"] .= "자세한 사항은 주문내역을 통해 확인할 수 있습니다.\n\n";
                        $param["text"] .= "▶ 상품명: ".$param["product_name"]."\n";
                        $param["text"] .= "▶ 스토어명: 닥터헤디슨\n";
                        $param["text"] .= "▶ 주문번호: ".$param["order_number"]."\n\n";
                        $param["text"] .= "닥터헤디슨의 다양한 제품을 통해 날마다 새로워지는 피부를 경험하세요.\n";
                        $param["text"] .= "▶ 닥터헤디슨 고객센터(080-624-0180)\n";
                        
                        $k_attach = array();
                        $k_attach["attachment"] = array();
                        $k_attach["attachment"]["button"] = array();
                        $button_description = array(
                            "name" => "주문조회하기",
                            "type" => "WL",
                            "url_mobile" => "https://www.drhedison.co.kr/?param=my_order_list&key=kakao",
                            "url_pc" => "https://www.drhedison.co.kr/?param=my_order_list&key=kakao",
                        );
                        array_push($k_attach["attachment"]["button"], $button_description);
                        $k_attach = json_encode($k_attach,JSON_UNESCAPED_UNICODE);
                        $k_attach = str_replace('\\/', '/', $k_attach);

                        $param["template_key"] = "SJT_069797";
                    }else if($param["type"] == "a_order_complete"){
                        // 주문 완료시 관리자가 받을 문자 메시지-> 관리자에게 발송
                        // 필수 파라미터 : $param["order_number"] : 주문번호 
                        if($admin_sms_setting["s_order_complete"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] . " 주문이 접수되었습니다.\n";
                        $param["text"] .= "주문번호 : " . $param["order_number"];
                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068761";
                    }else if($param["type"] == "my_order_cancel"){
                        // 사용자가 주문 취소 싱청시 - 관리자에게 문자 수신
                        // 필수 파라미터 : $param["order_number"] : 주문번호
                        if($admin_sms_setting["s_order_cancel"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "주문번호 " . $param["order_number"] . "의 상품 취소요청이 있습니다.\n";
                        $param["text"] .= "취소요청일시 : " . date("Y-m-d H:i:s");
                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068762";
                    }else if($param["type"] == "my_order_return"){
                        // 사용자가 주문 상품 반품 신청시 - 관리자에게 문자 수신
                        // 필수 파라미터 : $param["order_number"] : 주문번호
                        if($admin_sms_setting["s_order_return"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "주문번호 " . $param["order_number"] . "의 상품 반품요청이 있습니다.\n";
                        $param["text"] .= "반품요청일시 : " . date("Y-m-d H:i:s");
                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068763";
                    }else if($param["type"] == "my_order_exchange"){
                        // 사용자가 주문 상품 교환 신청시 - 관리자에게 문자 수신
                        // 필수 파라미터 : $param["order_number"] : 주문번호
                        if($admin_sms_setting["s_order_exchange"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "주문번호 " . $param["order_number"] . "의 상품 교환요청이 있습니다.\n";
                        $param["text"] .= "교환요청일시 : " . date("Y-m-d H:i:s");
                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068764";
                    }else if($param["type"] == "qna_register"){
                        // 사용자가 qna 등록 - 관리자에게 문자 수신
                        if($admin_sms_setting["s_qna"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "상품 Q&A가 등록되었습니다.\n";
                        $param["text"] .= "QNA 등록 일시 : " . date("Y-m-d H:i:s");

                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068766";
                    }else if($param["type"] == "1to1_register"){
                        // 사용자가 1:1문의 등록 - 관리자에게 문자 수신
                        if($admin_sms_setting["s_1to1"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "1대1 문의가 등록되었습니다.\n";
                        $param["text"] .= "1대1 문의 등록 일시 : " . date("Y-m-d H:i:s");

                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068768";
                    }else if($param["type"] == "review_register"){
                        // 사용자가 리뷰 등록 - 관리자에게 문자 수신
                        if($admin_sms_setting["s_review"] == 0){
                            return 0;
                        }
                        if(count($admin_phone_number) == 0){
                            return 0;
                        }
                        $param["text"] = "상품 리뷰가 등록되었습니다.\n";
                        $param["text"] .= "상품 리뷰 등록 일시 : " . date("Y-m-d H:i:s");

                        $param["receiver_list"] = json_encode($admin_phone_number);
                        $param["template_key"] = "SJT_068770";
                    }else if($param["type"] == "qna_answer_register"){
                        // 관리자가 상품 qna에 답변 등록 -> 사용자에게 문자 발송
                        if($user_sms_setting["s_question"] == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] ." 회원님이 남기신 Q&A에 답변이 등록되었습니다.\n";
                        $param["template_key"] = "SJT_068772";
                    }else if($param["type"] == "1to1_answer_register"){
                        // 관리자가 상품 1대1 문의 답변 등록 -> 사용자에게 문자 발송
                        if($user_sms_setting["s_question"] == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] ." 회원님이 남기신 1:1문의 답변이 등록되었습니다.\n";
                        $param["template_key"] = "SJT_068773";
                    }else if($param["type"] == "order_complete"){
                        // 해당주문이 배송완료 상태로 바뀜 -> 사용자에게 문자 발송
                        // 필수 파라미터 : $param["order_number"] -> 주문번호
                        if($user_sms_setting["s_delivery_complete"] == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] ." 상품이 배송완료 처리 되었습니다.\n";
                        $param["text"] .= "주문번호 : ".$param["order_number"]."\n";
                        $param["template_key"] = "SJT_068774";
                    }else if($param["type"] == "order_deposit"){
                        // 해당주문이 입금완료 상태로 바뀜 -> 사용자에게 문자 발송
                        // 필수 파라미터 : $param["order_number"] -> 주문번호
                        if($user_sms_setting["s_deposit_complete"] == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] ." 상품이 입금완료 상태로 변경되었습니다.\n";
                        $param["text"] .= "주문번호 : ".$param["order_number"]."\n";
                        $param["template_key"] = "SJT_068775";
                    }else if($param["type"] == "order_shipment"){
                        // 해당주문이 배송중 상태로 바뀜 -> 사용자에게 문자 발송
                        // 필수 파라미터 : $param["order_number"] -> 주문번호
                        if($user_sms_setting["s_shipment"] == 0){
                            return 0;
                        }
                        $param["text"] = $param["company"] ." 상품이 배송중 상태로 변경되었습니다.\n";
                        $param["text"] .= "주문번호 : ".$param["order_number"]."\n";
                        $param["template_key"] = "SJT_068776";
                    }else if($param["type"] == "u_account"){
                        // 주문 완료시 사용자가 받을 문자 메시지-> 유저에게  발송
                        // 필수 파라미터 : $param["order_number"] : 주문번호 , 
                        if($user_sms_setting["s_order_complete"] == 0){
                            return 0;
                        }
                        // $param["text"] = $param["company"] ." 상품이 배송중 상태로 변경되었습니다.\n";
                        // $param["text"] .= "주문번호 : ".$param["order_number"]."\n";
                        $param["text"] = "♥매일매일 달라지는 피부, 닥터헤디슨♥\n\n";
                        $param["text"] .= "[닥터헤디슨 입금안내]\n";
                        $param["text"] .= "오늘도 닥터헤디슨을 찾아주셔서 감사합니다.\n\n";
                        $param["text"] .= "▶ 입금은행: 부산은행\n";
                        $param["text"] .= "▶ 입금계좌: 113-2009-4246-04\n";
                        $param["text"] .= "▶ 예금주: 주식회사아이피아코스메틱\n";
                        $param["text"] .= "▶ 금액: ".number_format($param["amount"])."\n\n";
                        $param["text"] .= "입금 완료 후 상품 준비가 시작되는 점 참고해 주세요. 감사합니다.\n";
                        $param["template_key"] = "SJT_069798";
                    }
                    
                    if($param["send_type"] == "kakao"){
                        if(empty($k_attach)){
                            $result = $this->kakao_data_file_curl($param, $file, null);
                        }else{
                            $result = $this->kakao_data_file_curl($param, $file, $k_attach);
                        }
                    }else{
                        // 문자 발송
                        $result = $this->data_file_curl($param, $file);
                    }
                }
            }
            // echo $result;
        }

        
        // 파일 처리 curl
        function kakao_data_file_curl($json_data, $file, $k_attach){
            $url = "http://3.34.211.106/?ctl=SMS&param1=send_t_kakao";
            $header_data = array("Content-Type:multipart/form-data");

            $data = array(
                "ctl" => "SMS",
                "param1" => "send_t_kakao",
                "company" => $json_data["company"],
                "send_number" => $json_data["send_number"],
                "text" => $json_data["text"],
                "receiver_list" => $json_data["receiver_list"],
                "user_id" => $json_data["user_id"],
                "send_key" => "01b87e89e8b01ae567b4e7cd53163fd51242d3b1",
                "template_key" => $json_data["template_key"],
                // "file" => new CURLFile($file["tmp_name"][0]),
                "k_attach" => $k_attach,
            );
            $timeout = 60;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL , $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $str = curl_exec($curl);
            curl_close($curl);

            return $str;
        }

        // 파일 처리 curl
        function data_file_curl($json_data, $file){
            $url = "http://3.34.211.106/?ctl=SMS&param1=send_msg";
            // $url = "http://15.165.92.91/?ctl=SMS&param1=send_msg";

            $header_data = array("Content-Type:multipart/form-data");
            $data = array(
                "ctl" => "SMS",
                "param1" => "send_msg",
                "company" => $json_data["company"],
                "send_number" => $json_data["send_number"],
                "text" => $json_data["text"],
                "receiver_list" => $json_data["receiver_list"],
                "user_id" => $json_data["user_id"],
                // "file" => new CURLFile($file["tmp_name"][0]),
            );
            
            $timeout = 60;
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL , $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $str = curl_exec($curl);

            curl_close($curl);

            return $str;
        }

    }
?>