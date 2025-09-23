<?php
	class MailForm extends gf{
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

            $this->mail_model = new MailModel();
            
            // 이메일 발신 업체(메시지 내용에 들어가거나 제목에 들어감)
            $this->email_project = "아이피아코스메틱";

            // 발신이메일
            $this->send_email = "ipia.drhedison@gmail.com";

            // 이메일 로고 링크
            $this->email_logo = "https://s3.ap-northeast-2.amazonaws.com/lbplatform/images/TEMPORARY/156017985577744.png";

            // 로그인 url
            $this->login_url = "http://ipia.glserver.co.kr/?param=login&tab=member";
           
            // 상품 qna url
            $this->qna_url = "http://ipia.glserver.co.kr/?param=cscenter&tab=question";

             // 관리자페이지 url
             $this->admin_url = "http://ipia.glserver.co.kr/?ctl=move&param=adm&param1=menu1_alarm";
        }

        function send_email($param){
            // 타입 키가 들어오지않으면 리턴
            if(!isset($param["type"])){
                return 0;
            }

            $param["set_from"] = $this->send_email;
            $param["project"] = $this->project_name;
            $param["from_name"] = $this->project_name;
            

            $user_setting = "";
            $admin_setting = "";
            // 관리자 이메일 수신여부 조회
            $sql = "select * from admin_sms_email_setting ";
            $admin_result = $this->conn->db_select($sql);
            if($admin_result["result"] == 0){
                return 0;
            }else{
                if(count($admin_result["value"]) == 0){
                    return 0;
                }else{
                    // 관리자 수신여부가 있을경우
                    $admin_setting = $admin_result["value"][0];
                }
            }

            // 사용자 이메일 수신여부 조회
            $sql = "select * from admin_user_sms_email_setting ";
            $user_result = $this->conn->db_select($sql);
            if($user_result["result"] == 0){
                return 0;
            }else{
                if(count($user_result["value"]) == 0){
                    return 0;
                }else{
                    // 사용자 수신여부가 있을경우
                    $user_setting = $user_result["value"][0];
                }
            }

            $admin_email_arr = array();

            // 관리자 수신 이메일 조회 없으면 관리자 이메일 발송 하지 않음.
            $sql = "select * from admin_email_alarm ";
            $admin_email_result = $this->conn->db_select($sql);
            if($admin_email_result["result"] == 0){
                return 0;
            }else{
                for($i = 0; $i<count($admin_email_result["value"]); $i++){
                    array_push($admin_email_arr, $admin_email_result["value"][$i]["email"]);
                }
            }

            if($param["type"] == "u_join"){
                // 회원가입시 사용자에게 발신하는 이메일
                // 필수 파라미터 $param["to_list"] :배열 , $param["name"], $param["email"]
                if($user_setting["e_join"] == 0){
                    return 0;
                }
                
                $param["title"] = $this->email_project . " 회원가입을 축하합니다.";
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "a_join"){
                // 회원가입시 관리자에게 발신하는 이메일
                // 필수 파라미터 
                if($admin_setting["e_join"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }
                
                $param["title"] = $this->email_project . " 회원가입한 사용자가 있습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "u_order_complete"){
                // 주문완료시 사용자가 받는 이메일
                // 필수 파라미터 order : array , product : array
                if($user_setting["e_order_complete"] == 0){
                    return 0;
                }
                $param["title"] = $this->email_project . " 주문내역입니다.";
                $param["body"] = $this->get_mail_body($param);
                
            }else if($param["type"] == "a_order_complete"){
                // 주문완료시 관리자에게 이메일 발신
                if($admin_setting["e_order_complete"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }
                $param["title"] = $this->email_project . " 주문내역입니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "u_order_deposit"){
                // 관리자가 입금완료 처리시 사용자에게 이메일 발신
                if($user_setting["e_deposit_complete"] == 0){
                    return 0;
                }
                $param["title"] = $this->email_project . " 주문이 입금완료 되었습니다.";
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "u_shipment"){
                // 관리자가 배송중 상태로 변경시 사용자에게 이메일 발신
                if($user_setting["e_shipment"] == 0){
                    return 0;
                }
                $param["title"] = $this->email_project . " 주문이 배송중 상태로 변경되었습니다.";
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "u_delivery_complete"){
                // 관리자가 배송완료 처리시 사용자에게 이메일 발신
                if($user_setting["e_delivery_complete"] == 0){
                    return 0;
                }
                $param["title"] = $this->email_project . " 주문이 배송완료 되었습니다.";
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "qna_answer"){
                // 관리자가 qna 답변시 사용자에게 이메일 발신
                if($user_setting["e_question"] == 0){
                    return 0;
                }
                //필요한 정보 : 회원 이름 , 답변 내용 , 답변 등록일 , 제목 , 내용 , 문의 등록일
                $sql = "select * from product_qna as t1 left join user as t2 on t1.user_idx = t2.idx where t1.idx = ".$param["target"];
                $qna_result = $this->conn->db_select($sql);
                if($qna_result["result"] == 0){
                    return 0;
                }else{
                    if(count($qna_result["value"]) == 0){
                        return 0;
                    }else{
                        $param["qna_data"] = $qna_result["value"][0];
                    }
                }
                $param["title"] = $this->email_project . " Q&A 답변입니다.";
                $param["body"] = $this->get_mail_body($param);
                
            }else if($param["type"] == "1to1_answer"){
                // 관리자가 1대1 문의 답변 등록시 사용자에게 이메일 발신
                if($user_setting["e_question"] == 0){
                    return 0;
                }

                //필요한 정보 : 회원 이름 , 답변 내용 , 답변 등록일 , 문의 유형 , 제목 , 내용 , 문의 등록일
                $sql = "select t1.*, t2.name from 1to1inquiry as t1 left join user as t2 on t1.user_idx = t2.idx where t1.idx = ".$param["target"];
                $inquiry_result = $this->conn->db_select($sql);
                if($inquiry_result["result"] == 0){
                    return 0;
                }else{
                    if(count($inquiry_result["value"]) == 0){
                        return 0;
                    }else{
                        $kind_arr = ["", "구매/결제", "주문문의", "취소신청", "교환/반품 신청", "환불신청", "배송", "계정", "기타"];
                        $param["inquiry_data"] = $inquiry_result["value"][0];
                        $param["inquiry_data"]["kind"] = $kind_arr[$param["inquiry_data"]["kind"]];
                    }
                }
                $param["title"] = $this->email_project . " 1대1 문의 답변입니다.";
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "admin_qna"){
                // 사용자가 qna 등록시 관리자에게 이메일 발신
                // 필수 파라미터  등록일, 제목
                if($admin_setting["e_qna"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }

                $param["title"] = $this->email_project . " 사용자가 Q&A를 등록하였습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "admin_1to1"){
                // 사용자가 1대1문의 등록시 관리자에게 이메일 발신
                // 필수 파라미터  등록일, 제목
                if($admin_setting["e_1to1"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }

                $param["title"] = $this->email_project . " 사용자가 1대1 문의를 등록하였습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);

            }else if($param["type"] == "admin_review"){
                // 사용자가 상품후기 등록시 관리자에게 이메일 발신
                // 필수 파라미터  등록일, 후기내용
                if($admin_setting["e_review"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }

                $param["title"] = $this->email_project . " 사용자가 상품 후기를 등록하였습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "order_cancel_req"){
                // 사용자가 주문 취소 요청시 관리자에게 이메일 전송
                // 필수 파라미터 취소날짜, 주문번호
                if($admin_setting["e_order_cancel"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }
                $param["title"] = $this->email_project . " 주문 취소요청이 있습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "order_return_req"){
                // 사용자가 주문 반품 요청시 관리자에게 이메일 전송
                // 필수 파라미터 반품날짜, 주문번호
                if($admin_setting["e_order_return"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }

                $param["title"] = $this->email_project . " 주문 반품요청이 있습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "order_exchange_req"){
                // 사용자가 주문 반품 요청시 관리자에게 이메일 전송
                // 필수 파라미터 반품날짜, 주문번호
                if($admin_setting["e_order_return"] == 0){
                    return 0;
                }
                if(count($admin_email_arr) == 0){
                    return 0;    
                }
                $param["title"] = $this->email_project . " 주문 교환요청이 있습니다.";
                $param["to_list"] = $admin_email_arr;
                $param["body"] = $this->get_mail_body($param);
            }else if($param["type"] == "find_pw"){
                $param["title"] = $this->email_project . " 임시 비밀번호가 발급되었습니다.";
                $param["body"] = $this->get_mail_body($param);
            }

            // 메일발송
            $this->mail_model->send_mail($param);
        }

        function get_mail_body($param){
            if($param["type"] == "u_join"){
                $mail_body = '
                <body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                안녕하세요. '.$this->email_project.'입니다.<br/>
                                '.$param["name"].' 고객님의 회원가입을 축하드립니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <span style="word-break:keep-all; display: block; margin:0 0 24px 0; padding:0;">
                                    소중한 개인정보보호를 위해 사용하는 아이디 및 비밀번호는 타인에게 알려지지 않도록 주의하시기 바랍니다.
                                </span>
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">아이디</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["email"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->login_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">바로 로그인하기</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                    </div>
                </body>
                ';
            }else if($param["type"] == "a_join"){
                $mail_body = '
                <body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="#"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                '.$param["name"].' 고객님이 회원가입 하였습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">아이디</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["email"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자 페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                    </div>
                </body>
                ';
            }else if($param["type"] == "u_order_complete"){
                $mail_body = '
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                    <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                        <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                            <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                        </div>
                        <!-- 로고 영역입니다. -->
                        <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["order"]["orderer_name"].'</span> 회원님의<br/> <span style="font-weight: 600;">주문하신 내역</span>입니다.</p>
                        <div class="email_inner">
                            <!-- 결제내역  table start -->
                            <div class="email_table" style="border: 0; margin: 0 0 32px 0; padding:20px 16px; background-color:#f6f6f6;">
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; font-size:16px;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 8px 0;">이름</td>
                                            <td style="width:100%; padding: 8px 0;">'.$param["order"]["orderer_name"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문번호</td>
                                            <td style=" padding: 8px 0;">'.$param["order"]["order_number"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문일자</td>
                                            <td style=" padding: 8px 0;">'.$param["order"]["regdate"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div  class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">결제정보</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb; table-layout: fixed;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">결제금액</td>
                                            <td style="width:100%;border-bottom: 1px solid #dbdbdb; padding: 16px 10px; text-align: right;"><span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                        </tr>
                                        <tr >
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb">결제수단</td>
                                            <td style="width:100%; padding: 16px 10px; text-align: right; border-bottom: 1px solid #dbdbdb">';
                                            if($param["order"]["pay_type"] == "card"){
                                                $mail_body .= "카드결제";
                                            }else if($param["order"]["pay_type"] == "account"){
                                                $mail_body .= "무통장입금";
                                            }else if($param["order"]["pay_type"] == "transfer"){
                                                $mail_body .= "실시간계좌이체";
                                            }
                            $mail_body .= '</td>
                                        </tr>
                                        <tr >
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px;">결제일시</td>
                                            <td style="width:100%; padding: 16px 10px; text-align: right;">'.$param["order"]["regdate"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">배송지 정보</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수령인</td>
                                            <td style=" width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_name"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">휴대전화</td>
                                            <td style="width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.substr_replace($param["order"]["receive_phone_number"],"***", strlen($param["order"]["receive_phone_number"]) - 3, 3 ).'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">주소</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_address"]." ".$param["order"]["receive_detail_address"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">배송메시지</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["delivery_message"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="email_table" style="border: 0; margin: 0 0 24px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">주문상품</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                    <tbody>';
                                    for($i=0; $i<count($param["product"]); $i++){
                                        $product = $param["product"][$i];
                                        $mail_body .= 
                                        '<tr>
                                            <td colspan="2" style=" width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: left;">
                                                <span style=" font-size:1.175em; display:block; font-weight: 600;">'.$product["product_name"].'</span>
                                                <span style="display:block;">';
                                                if($product["key"] == "product"){
                                                    $mail_body .= "옵션없음";
                                                }else if($product["key"] == "option_1"){
                                                    $mail_body .= $product["option_1_name"];
                                                }else if($product["key"] == "option_2"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"];
                                                }else if($product["key"] == "option_3"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"];
                                                }else if($product["key"] == "option_4"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"] . " > " . $product["option_4_name"];
                                                }
                                                $mail_body .= '</span><!-- 옵션 자체가 없으면 삭제 / 옵션 선택 안함이면 옵션 선택안함-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수량</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$product["cnt"].'</td>
                                        </tr>';
                                    }
                                    $mail_body .= 
                                        '<tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">최종결제금액</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right; font-size:1.5em; color:#ff5d00; font-weight: 600;"></span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                            <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 입금이 확인된 이후에 주문상품의 배송이 이루어지며, 택배로 상품을 배송합니다.</span>
                        </div>
                    </main>
                    <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                        <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                    </footer>
                </div>
                ';
            }else if($param["type"] == "a_order_complete"){
                $mail_body = '
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                    <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                        <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                            <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                        </div>
                        <!-- 로고 영역입니다. -->
                        <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["order"]["orderer_name"].'</span> 회원님의<br/> <span style="font-weight: 600;">주문하신 내역</span>입니다.</p>
                        <div class="email_inner">
                            <!-- 결제내역  table start -->
                            <div class="email_table" style="border: 0; margin: 0 0 32px 0; padding:20px 16px; background-color:#f6f6f6;">
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; font-size:16px;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 8px 0;">이름</td>
                                            <td style="width:100%; padding: 8px 0;">'.$param["order"]["orderer_name"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문번호</td>
                                            <td style=" padding: 8px 0;">'.$param["order"]["order_number"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문일자</td>
                                            <td style=" padding: 8px 0;">'.$param["order"]["regdate"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div  class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">결제정보</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb; table-layout: fixed;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">결제금액</td>
                                            <td style="width:100%;border-bottom: 1px solid #dbdbdb; padding: 16px 10px; text-align: right;"><span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                        </tr>
                                        <tr >
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb">결제수단</td>
                                            <td style="width:100%; padding: 16px 10px; text-align: right; border-bottom: 1px solid #dbdbdb">';
                                            if($param["order"]["pay_type"] == "card"){
                                                $mail_body .= "카드결제";
                                            }else if($param["order"]["pay_type"] == "account"){
                                                $mail_body .= "무통장입금";
                                            }else if($param["order"]["pay_type"] == "transfer"){
                                                $mail_body .= "실시간계좌이체";
                                            }
                            $mail_body .= '</td>
                                        </tr>
                                        <tr >
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px;">결제일시</td>
                                            <td style="width:100%; padding: 16px 10px; text-align: right;">'.$param["order"]["regdate"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">배송지 정보</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                    <tbody>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수령인</td>
                                            <td style=" width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_name"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600;  padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">휴대전화</td>
                                            <td style="width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.substr_replace($param["order"]["receive_phone_number"],"***", strlen($param["order"]["receive_phone_number"]) - 3, 3 ).'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">주소</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_address"]." ".$param["order"]["receive_detail_address"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">배송메시지</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["delivery_message"].'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="email_table" style="border: 0; margin: 0 0 24px 0; padding: 0;">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">주문상품</p>
                                <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                    <tbody>';
                                    for($i=0; $i<count($param["product"]); $i++){
                                        $product = $param["product"][$i];
                                        $mail_body .= 
                                        '<tr>
                                            <td colspan="2" style=" width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: left;">
                                                <span style=" font-size:1.175em; display:block; font-weight: 600;">'.$product["product_name"].'</span>
                                                <span style="display:block;">';
                                                if($product["key"] == "product"){
                                                    $mail_body .= "옵션없음";
                                                }else if($product["key"] == "option_1"){
                                                    $mail_body .= $product["option_1_name"];
                                                }else if($product["key"] == "option_2"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"];
                                                }else if($product["key"] == "option_3"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"];
                                                }else if($product["key"] == "option_4"){
                                                    $mail_body .= $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"] . " > " . $product["option_4_name"];
                                                }
                                                $mail_body .= '</span><!-- 옵션 자체가 없으면 삭제 / 옵션 선택 안함이면 옵션 선택안함-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수량</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$product["cnt"].'</td>
                                        </tr>';
                                    }
                                    $mail_body .= 
                                        '<tr>
                                            <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">최종결제금액</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right; font-size:1.5em; color:#ff5d00; font-weight: 600;"></span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                            <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 입금이 확인된 이후에 주문상품의 배송이 이루어지며, 택배로 상품을 배송합니다.</span>
                        </div>
                    </main>
                    <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                        <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                    </footer>
                </div>
                ';
            }else if($param["type"] == "u_order_deposit"){
                // 입금완료상태로 변경시 이메일
                $mail_body = '
                <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                    <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                        <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                    </div>
                    <!-- 로고 영역입니다. -->
                    <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["order"]["orderer_name"].'</span> 회원님의<br/> <span style="font-weight: 600;">주문상품</span>이 입급완료 되었습니다.</p>
                    <div class="email_inner">
                        <!-- 결제내역  table start -->
                        <div class="email_table" style="border: 0; margin: 0 0 32px 0; padding:20px 16px; background-color:#f6f6f6;">
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; font-size:16px;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 8px 0;">이름</td>
                                        <td style="width:100%; padding: 8px 0;">'.$param["order"]["orderer_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문번호</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["order_number"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문일자</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div  class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">결제정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb; table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">결제금액</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb; padding: 16px 10px; text-align: right;"><span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb">결제수단</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right; border-bottom: 1px solid #dbdbdb">';
                                        if($param["order"]["pay_type"] == "card"){
                                            $mail_body .= "카드결제";
                                        }else if($param["order"]["pay_type"] == "account"){
                                            $mail_body .= "무통장입금";
                                        }else if($param["order"]["pay_type"] == "transfer"){
                                            $mail_body .= "실시간계좌이체";
                                        }
                        $mail_body .= '</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px;">결제일시</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">배송지 정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수령인</td>
                                        <td style=" width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">휴대전화</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.substr_replace($param["order"]["receive_phone_number"],"***", strlen($param["order"]["receive_phone_number"]) - 3, 3 ).'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">주소</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_address"]." ".$param["order"]["receive_detail_address"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">배송메시지</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["delivery_message"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 24px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">주문상품</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>';
                                for($i=0; $i<count($param["product"]); $i++){
                                    $product = $param["product"][$i];
                                    $mail_body .= 
                                    '<tr>
                                        <td colspan="2" style=" width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: left;">
                                            <span style=" font-size:1.175em; display:block; font-weight: 600;">'.$product["product_name"].'</span>
                                            <span style="display:block;">';
                                            $option_name = "";
                                            if($product["option_1_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"];
                                            }else if($product["option_2_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"];
                                            }else if($product["option_3_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"];
                                            }else if($product["option_4_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"] . " > " . $product["option_4_name"];
                                            }
                                            $mail_body .= $option_name;
                                            $mail_body .= '</span><!-- 옵션 자체가 없으면 삭제 / 옵션 선택 안함이면 옵션 선택안함-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수량</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$product["cnt"].'</td>
                                    </tr>
                                    <tr>';
                                }
                                $mail_body .= 
                                    '<tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">최종결제금액</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right; font-size:1.5em; color:#ff5d00; font-weight: 600;"></span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                    </div>
                </main>
                <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                    <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                </footer>
                </div>
                ';
            }else if($param["type"] == "u_shipment"){


                $mail_body = '
                <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                    <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                        <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                    </div>
                    <!-- 로고 영역입니다. -->
                    <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["order"]["orderer_name"].'</span> 회원님의<br/> <span style="font-weight: 600;">주문상품</span>이 배송되었습니다.</p>                    <div class="email_inner">
                        <!-- 결제내역  table start -->
                        <div class="email_table" style="border: 0; margin: 0 0 32px 0; padding:20px 16px; background-color:#f6f6f6;">
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; font-size:16px;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 8px 0;">이름</td>
                                        <td style="width:100%; padding: 8px 0;">'.$param["order"]["orderer_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문번호</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["order_number"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문일자</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div  class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">결제정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb; table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">결제금액</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb; padding: 16px 10px; text-align: right;"><span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb">결제수단</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right; border-bottom: 1px solid #dbdbdb">';
                                        if($param["order"]["pay_type"] == "card"){
                                            $mail_body .= "카드결제";
                                        }else if($param["order"]["pay_type"] == "account"){
                                            $mail_body .= "무통장입금";
                                        }else if($param["order"]["pay_type"] == "transfer"){
                                            $mail_body .= "실시간계좌이체";
                                        }
                        $mail_body .= '</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px;">결제일시</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">배송지 정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수령인</td>
                                        <td style=" width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">휴대전화</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.substr_replace($param["order"]["receive_phone_number"],"***", strlen($param["order"]["receive_phone_number"]) - 3, 3 ).'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">주소</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_address"]." ".$param["order"]["receive_detail_address"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">배송메시지</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["delivery_message"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 24px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">주문상품</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>';
                                for($i=0; $i<count($param["product"]); $i++){
                                    $product = $param["product"][$i];
                                    $mail_body .= 
                                    '<tr>
                                        <td colspan="2" style=" width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: left;">
                                            <span style=" font-size:1.175em; display:block; font-weight: 600;">'.$product["product_name"].'</span>
                                            <span style="display:block;">';
                                            $option_name = "";
                                            if($product["option_1_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"];
                                            }else if($product["option_2_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"];
                                            }else if($product["option_3_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"];
                                            }else if($product["option_4_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"] . " > " . $product["option_4_name"];
                                            }
                                            $mail_body .= $option_name;
                                            $mail_body .= '</span><!-- 옵션 자체가 없으면 삭제 / 옵션 선택 안함이면 옵션 선택안함-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수량</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$product["cnt"].'</td>
                                    </tr>
                                    <tr>';
                                }
                                $mail_body .= 
                                    '<tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">최종결제금액</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right; font-size:1.5em; color:#ff5d00; font-weight: 600;"></span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                    </div>
                </main>
                <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                    <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                </footer>
                </div>
                ';
            }else if($param["type"] == "u_delivery_complete"){
             // 배송완료시 이메일
                $mail_body = '
                <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                    <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                        <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                    </div>
                    <!-- 로고 영역입니다. -->
                    <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["order"]["orderer_name"].'</span> 회원님의<br/> <span style="font-weight: 600;">주문이 배송완료되었습니다.</span></p>
                    <div class="email_inner">
                        <!-- 결제내역  table start -->
                        <div class="email_table" style="border: 0; margin: 0 0 32px 0; padding:20px 16px; background-color:#f6f6f6;">
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; font-size:16px;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 8px 0;">이름</td>
                                        <td style="width:100%; padding: 8px 0;">'.$param["order"]["orderer_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문번호</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["order_number"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 8px 0;">주문일자</td>
                                        <td style=" padding: 8px 0;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div  class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">결제정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb; table-layout: fixed;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">결제금액</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb; padding: 16px 10px; text-align: right;"><span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb">결제수단</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right; border-bottom: 1px solid #dbdbdb">';
                                        if($param["order"]["pay_type"] == "card"){
                                            $mail_body .= "카드결제";
                                        }else if($param["order"]["pay_type"] == "account"){
                                            $mail_body .= "무통장입금";
                                        }else if($param["order"]["pay_type"] == "transfer"){
                                            $mail_body .= "실시간계좌이체";
                                        }
                        $mail_body .= '</td>
                                    </tr>
                                    <tr >
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px;">결제일시</td>
                                        <td style="width:100%; padding: 16px 10px; text-align: right;">'.$param["order"]["regdate"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 48px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">배송지 정보</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수령인</td>
                                        <td style=" width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_name"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600;  padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">휴대전화</td>
                                        <td style="width:100%;border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.substr_replace($param["order"]["receive_phone_number"],"***", strlen($param["order"]["receive_phone_number"]) - 3, 3 ).'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">주소</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["receive_address"]." ".$param["order"]["receive_detail_address"].'</td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">배송메시지</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$param["order"]["delivery_message"].'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="email_table" style="border: 0; margin: 0 0 24px 0; padding: 0;">
                            <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">주문상품</p>
                            <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin:0; border-bottom: 1px solid #dbdbdb;">
                                <tbody>';
                                for($i=0; $i<count($param["product"]); $i++){
                                    $product = $param["product"][$i];
                                    $mail_body .= 
                                    '<tr>
                                        <td colspan="2" style=" width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: left;">
                                            <span style=" font-size:1.175em; display:block; font-weight: 600;">'.$product["product_name"].'</span>
                                            <span style="display:block;">';
                                            $option_name = "";
                                            if($product["option_1_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"];
                                            }else if($product["option_2_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"];
                                            }else if($product["option_3_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"];
                                            }else if($product["option_4_name"] != ""){
                                                $option_name = $option_name . " > " . $product["option_1_name"] . " > " . $product["option_2_name"] . " > " . $product["option_3_name"] . " > " . $product["option_4_name"];
                                            }
                                            $mail_body .= $option_name;
                                            $mail_body .= '</span><!-- 옵션 자체가 없으면 삭제 / 옵션 선택 안함이면 옵션 선택안함-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">수량</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right;">'.$product["cnt"].'</td>
                                    </tr>
                                    <tr>';
                                }
                                $mail_body .= 
                                    '<tr>
                                        <td style="min-width:88px; font-weight: 600; padding: 16px 10px; border-bottom: 1px solid #dbdbdb;">최종결제금액</td>
                                        <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 16px 10px; text-align: right; font-size:1.5em; color:#ff5d00; font-weight: 600;"></span>'.number_format($param["order"]["total_price"]).'</span>원</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                    </div>
                </main>
                <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                    <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                </footer>
            </div>
            ';   
            }else if($param["type"] == "qna_answer"){

                $mail_body = '
                <body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 36px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["qna_data"]["name"].'</span> 회원님의<br/> <span style="font-weight: 600;">상품 문의에 대한 답변</span>이 등록되었습니다.</p>
                            <div class="email_inner">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">답변내용<span style="float:right; line-height: 2; font-size:0.85em; font-weight:normal;">'.$param["qna_data"]["answer_date"].'</span></p>
                                <!-- 답변 --> 
                                <div class="email_table" style="margin-bottom:24px;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tbody>
                                            <tr style="background-color:#f6f6f6;">
                                                <td style="min-width:64px; font-weight: 600;  padding: 24px 16px; vertical-align: top;">답변내용</td>
                                                <td style="width:100%; padding: 24px 10px;">
                                                    '.$param["qna_data"]["answer"].'
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="email_inner">
                                    <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">문의내용<span style="float:right; line-height: 2; font-size:0.85em; font-weight:normal;">'.$param["qna_data"]["regdate"].'</span></p>
                                    <!-- 문의 --> 
                                    <div class="email_table" style="margin:0 0 24px 0;">
                                        <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                            <tbody>
                                                <tr>
                                                    <td style="min-width:64px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb; vertical-align: top;">제목</td>
                                                    <td style="width:100%; border-bottom: 1px solid #dbdbdb; padding: 24px 10px;">
                                                    '.$param["qna_data"]["title"].'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="min-width:64px; font-weight: 600;  padding: 24px 10px; border-bottom: 1px solid #dbdbdb; vertical-align: top;">내용</td>
                                                    <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px;">
                                                    '.$param["qna_data"]["content"].'
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 상품 문의 답변은 <a href="'.$this->qna_url.'" style="color: #626262; font-weight: 600;">C/S CENTER > Q & A</a>를 통해서도 확인이 가능합니다.</span>
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                    </div>
                </body>';
            }else if($param["type"] == "1to1_answer"){
                $mail_body = '
                <body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 36px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;"><span style="font-weight:600; color:#626262;">'.$param["inquiry_data"]["name"].'</span> 회원님의<br/> <span style="font-weight: 600;">1:1 문의에 대한 답변</span>이 등록되었습니다.</p>
                            <div class="email_inner">
                                <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">답변내용<span style="float:right; line-height: 2; font-size:0.85em; font-weight:normal;">'.$param["inquiry_data"]["answer_date"].'</span></p>
                                <!-- 답변 --> 
                                <div class="email_table" style="margin-bottom:24px;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tbody>
                                            <tr style="background-color:#f6f6f6;">
                                                <td style="min-width:64px; font-weight: 600;  padding: 24px 16px; vertical-align: top;">답변내용</td>
                                                <td style="width:100%; padding: 24px 10px;">
                                                    '.$param["inquiry_data"]["answer"].'
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="email_inner">
                                    <p style="font-size:1.175em; margin: 0; padding:0 0 8px 0; border-bottom: 1px solid #626262; font-weight: 600;">문의내용<span style="float:right; line-height: 2; font-size:0.85em; font-weight:normal;">'.$param["inquiry_data"]["regdate"].'</span></p>
                                    <!-- 문의 --> 
                                    <div class="email_table" style="margin:0 0 24px 0;">
                                        <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                            <tbody>
                                                <tr>
                                                    <td style="min-width:64px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb; vertical-align: top;">문의유형</td>
                                                    <td style="width:100%; border-bottom: 1px solid #dbdbdb; padding: 24px 10px;">
                                                    '.$param["inquiry_data"]["kind"].'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="min-width:64px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb; vertical-align: top;">제목</td>
                                                    <td style="width:100%; border-bottom: 1px solid #dbdbdb; padding: 24px 10px;">
                                                    '.$param["inquiry_data"]["title"].'
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="min-width:64px; font-weight: 600;  padding: 24px 10px; border-bottom: 1px solid #dbdbdb; vertical-align: top;">내용</td>
                                                    <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px;">
                                                    '.$param["inquiry_data"]["content"].'
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 1:1 문의 답변은 <a href="'.$this->login_url.'" style="color: #626262; font-weight: 600;">마이페이지 > 1:1 문의</a>를 통해서도 확인이 가능합니다.</span>
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                    </div>
                </body>';
            }else if($param["type"] == "admin_qna"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                사용자가 Q&A를 등록하였습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">제목</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["qna_title"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">등록일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "admin_1to1"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; fnot-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                사용자가 1대1 문의를 등록하였습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">제목</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["inquiry_title"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">등록일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "admin_review"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; fnot-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                사용자가 상품후기를 등록하였습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">내용</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["review_content"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">등록일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "order_cancel_req"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; fnot-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                주문 취소요청이 있습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">주문번호</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["order_number"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">요청일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "order_return_req"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; fnot-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                주문 반품요청이 있습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">주문번호</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["order_number"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">요청일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "order_exchange_req"){
                $mail_body = '<body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->admin_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <!-- 로고 영역입니다. -->
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; fnot-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                주문 교환요청이 있습니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <!-- 상담 내역 table start -->
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">주문번호</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["order_number"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">요청일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["regdate"].'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->admin_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">관리자페이지 이동</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                <!-- 상담 내역 table end -->
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                        <!-- footer end -->
                    </div>
                </body>';
            }else if($param["type"] == "find_pw"){
                $mail_body = '
                <body style="margin:0; padding:0; font-size:14px; padding:40px; line-height: 1.5; background-color: #fff;">
                    <div class="wrap" style="max-width:600px; margin:0 auto; position: relative; border-top:3px solid #232323;">
                        <main class="email_con" style="width:100%; box-sizing: border-box; overflow: hidden; margin: 0 auto; padding: 32px 0;">
                            <div class="logo_wrap" style="padding: 0px 0 32px 0; margin: 0; width: 80px;">
                                <a href="'.$this->login_url.'"><img src="'.$this->email_logo.'" alt="'.$this->email_project.'" style="border:0; display: block;"/></a>
                            </div>
                            <p style="margin:0; padding:0; width: 100%; padding: 0 0 24px 0; font-size: 1.375em; font-weight: 500; word-break: keep-all; letter-spacing: -.8px;">
                                이 메일은 비밀번호 확인을 위한 메일입니다.<br/>
                                임시 비밀번호로 <strong>로그인 후 비밀번호를 수정</strong>하시기 바랍니다.
                            </p>
                            <div class="email_inner" style="margin:0; padding:0;">
                                <span style="word-break:keep-all; display: block; margin:0 0 24px 0; padding:0;">
                                    소중한 개인정보보호를 위해 사용하는 아이디 및 비밀번호는 타인에게 알려지지 않도록 주의하시기 바랍니다.
                                </span>
                                <div class="email_table" style="width: 100%; margin: 0; padding: 0; border-top:1px solid #626262;">
                                    <table style="border: 0; border-collapse:collapse; border-spacing:0; padding: 0; margin: 0; border-bottom: 1px solid #dbdbdb;">
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">임시비밀번호</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.$param["temp_pw"].'</td>
                                        </tr>
                                        <tr>
                                            <td style="width:88px; min-width:88px; font-weight: 600; padding: 24px 10px; border-bottom: 1px solid #dbdbdb;">발급일시</td>
                                            <td style="width:100%; border-bottom: 1px solid #dbdbdb;  padding: 24px 10px; text-align: right;">'.date("Y-m-d h:i:s").'</td>
                                        </tr>
                                    </table>
                                    <div class="button_wrap" style="width: 100%; text-align: center; margin: 24px 0; padding: 0;">
                                        <a href="'.$this->login_url.'" style="display: inline-block; padding:16px 24px; color: white; font-weight: 600; text-decoration: none; background-color:#626262;">바로 로그인하기</a>
                                    </div>
                                    <span style="margin: 0; font-size: 0.95em; display:block; letter-spacing: -.8px; color:#626262;">* 본 메일은 발신 전용입니다.</span>
                                </div>
                            </div>
                        </main>
                        <footer style="width:100%; background-color:#232323; overflow:hidden; margin: 0 auto; padding:24px 20px;  box-sizing: border-box;">
                            <span style="font-size:.85em; color: white; width: 100%; text-align: center; display:block; padding:0;">COPYRIGHT © '.$this->email_project.'. ALL RIGHTS RESERVED.</span>
                        </footer>
                    </div>
                </body>';

            }
            return $mail_body;
        }
	}
?>
