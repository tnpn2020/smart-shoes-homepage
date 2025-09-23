<?php
    class AdminPopupModel extends gf{
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
                    $this->result["messagse"]=$check_result["value_key"]."가 비어있습니다.";
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
        // 함 수 : 유저 데이터 내용 조회
        // 파라미터 : user_idx 
        // 만든이: 최진혁
        *********************************************************************/
        function user_detail(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $sql = "select * from user ";
                $sql = $sql . "where idx = ".$param["user_idx"]." ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 유저 문자 이메일 수신관리
        // 파라미터 : user_idx , 현재 flag(email_agree, sms_agree), type(0 : sms, 1 : email);
        // 만든이: 최진혁
        *********************************************************************/
        function user_r_agree(){
            $param = $this->param;
            // agree_type = sms, email  / agree_flag = 0 : 수신안함, 1: 수신함
            if($this->value_check(array("user_idx", "agree_type","agree_flag"))){
                $sql = "update user set ";
                if($param["agree_type"] == "sms"){
                    $sql .= "sms_agree = ".$param["agree_flag"]." ";
                }else{
                    $sql .= "email_agree = ".$param["agree_flag"]." ";
                }
                $sql .= "where idx = ".$param["user_idx"]."";

                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 회원정보 저장
        // 파라미터 : user_idx , ajaxformpost 로들어옴  elem : form_elem , email : email id+name;
        // 만든이: 최진혁
        *********************************************************************/
        function save_user(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $sql = "update user set ";
                $sql .= "name = ".$this->null_check($param["user_name"]).", ";
                $sql .= "phone = ".$this->null_check($param["phone"]).", ";
                if($param["user_new_pw"] != ""){
                    $sql .= "pw = ".$this->null_check($param["user_new_pw"]).", ";
                    $sql .= "pw_update_regdate = now(), ";
                }
                // $sql .= "email = ".$this->null_check($param["email"]).", ";
                $sql .= "post_code = ".$this->null_check($param["post_code"]).", ";
                $sql .= "address = ".$this->null_check($param["address"]).", ";
                $sql .= "detail_address = ".$this->null_check($param["detail_address"]).", ";
                $sql .= "update_regdate = now() ";
                $sql .= "where idx = ".$param["user_idx"]."";

                $this->conn->s_transaction();
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                    $this->conn->rollback();
                }else{
                    $this->result = $result;
                    $this->conn->commit();
                }
            }
            echo $this->jsonEncode($this->result);
        }

        // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  회원 qna 페이지 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        /********************************************************************* 
        // 함 수 : 회원 상품qna 리스트 몇번째 데이터인지
        // 파라미터 : user_idx, target
        // 만든이: 최진혁
        *********************************************************************/
        function user_product_index(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "target"))){
                $sql = "select count(idx) as num from product_qna ";
                $sql .= "where user_idx = ".$param["user_idx"]." ";
                $sql .= "and idx >= ".$param["target"]." ";
                $sql .= "order by regdate desc ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 회원 qna 리스트
        // 파라미터 : user_idx, page_size, page
        // 만든이: 최진혁
        *********************************************************************/

        function user_qna_list(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, ";
                $sql .= "(select product_name from product_name where product_idx = t1.product_idx and lang_idx = 1) as product_name ";
                $sql .= "from product_qna as t1 ";
                $sql .= "where t1.user_idx = ".$param["user_idx"]." ";
                $sql .= " limit ".$page_size*($page-1).",".$page_size;

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql = "select count(idx) as total_count from product_qna ";
                    $sql .= "where user_idx = ".$param["user_idx"]." ";

                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        $this->result["total_count"] = $total_result["value"][0]["total_count"];
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 회원 qna 답변 추가
        // 파라미터 : qna_idx, answer 내용
        // 만든이: 최진혁
        *********************************************************************/
        function qna_answer_register(){
            $param = $this->param;
            if($this->value_check(array("qna_idx", "answer"))){
                $sql = "update product_qna set ";
                $sql .= "answer = ".$this->null_check($param["answer"]).", ";
                $sql .= "answer_date = now() ";
                $sql .= "where idx = ".$param["qna_idx"]." ";
                
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                    //수정 : 조경민
                    //답변 등록에 성공하면 관리자페이지 문자 or 이메일 발신 여부를 체크하고
                    //상품 Q&A를 남긴 사용자의 문자 or 이메일 수신동의를 체크한 후
                    //이메일 or 문자를 보내줌
                    $sql = "select s_question, e_question from admin_user_sms_email_setting"; //관리자가 설정한 답변 등록시 문자 or 이메일 발신 체크
                    $admin_check_result = $this->conn->db_select($sql);

                    $sql = "select email_agree, sms_agree, email, phone from user where idx = ".$param["user_idx"]; //관리자가 설정한 답변 등록시 문자 or 이메일 발신 체크
                    $user_check_result = $this->conn->db_select($sql);

                    if($user_check_result["value"][0]["sms_agree"] == 1){ //사용자가 문자 수신 동의를 했으면 실행
                        // 사용자에게 문자 발신
                        $this->BillingSMS->send_tracking(array(
                            "send_type" => "kakao",
                            "type" => "qna_answer_register",
                            "receiver_list" => json_encode(array($user_check_result["value"][0]["phone"])),
                        ), null);
                    }

                    if($user_check_result["value"][0]["email_agree"] == 1){ //사용자가 이메일 수신 동의를 했으면 실행
                        $this->MailForm->send_email(array(
                            "type" => "qna_answer",
                            "target" => $param["qna_idx"],
                            "to_list" => array(
                                $user_check_result["value"][0]["email"]
                            ),
                        ));
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }
         // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  회원 1to1 페이지 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        /********************************************************************* 
        // 함 수 : 회원 1to1 리스트 몇번째 데이터인지?
        // 파라미터 : user_idx, target
        // 만든이: 최진혁
        *********************************************************************/
        function user_1to1_index(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "target"))){
                $sql = "select count(idx) as num from 1to1inquiry ";
                $sql .= "where user_idx = ".$param["user_idx"]." ";
                $sql .= "and idx >= ".$param["target"]." ";
                $sql .= "order by regdate desc ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 회원 1to1 리스트
        // 파라미터 : user_idx, page_size, page
        // 만든이: 최진혁
        *********************************************************************/
        function user_1to1(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, t2.order_number from 1to1inquiry as t1 ";
                $sql .= "left join purchase_order as t2 ";
                $sql .= "on t1.purchase_order_idx = t2.idx ";
                $sql .= "where t1.user_idx = ".$param["user_idx"]." ";
                $sql .= "order by idx desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql = "select count(idx) as total_count from 1to1inquiry ";
                    $sql .= "where user_idx = ".$param['user_idx']."";
                    
                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        $this->result["total_count"] = $total_result["value"][0]["total_count"];
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 1대1 문의 답변추가
        // 파라미터 : target, answer 내용
        // 만든이: 최진혁
        *********************************************************************/
        function inquiry_answer_register(){
            $param = $this->param;
            if($this->value_check(array("target", "answer", "user_idx"))){
                $sql = "update 1to1inquiry set ";
                $sql .= "answer = ".$this->null_check($param["answer"]).", ";
                $sql .= "answer_date = now() ";
                $sql .= "where idx = ".$param["target"]." ";
                
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                    //수정 : 조경민
                    //답변 등록에 성공하면 관리자페이지 문자 or 이메일 발신 여부를 체크하고
                    //1 : 1 문의를 남긴 사용자의 문자 or 이메일 수신동의를 체크한 후
                    //이메일 or 문자를 보내줌
                    $sql = "select s_question, e_question from admin_user_sms_email_setting"; //관리자가 설정한 답변 등록시 문자 or 이메일 발신 체크
                    $admin_check_result = $this->conn->db_select($sql);

                    $sql = "select email_agree, sms_agree, email, phone from user where idx = ".$param["user_idx"]; //관리자가 설정한 답변 등록시 문자 or 이메일 발신 체크
                    $user_check_result = $this->conn->db_select($sql);

                    if($user_check_result["value"][0]["sms_agree"] == 1){ //사용자가 문자 수신 동의를 했으면 실행
                        // 사용자에게 문자 발신
                        $this->BillingSMS->send_tracking(array(
                            "send_type" => "kakao",
                            "type" => "1to1_answer_register",
                            "receiver_list" => json_encode(array($user_check_result["value"][0]["phone"])),
                        ), null);
                    }
                    if($user_check_result["value"][0]["email_agree"] == 1){ //사용자가 이메일 수신 동의를 했으면 실행
                        $this->MailForm->send_email(array(
                            "type" => "1to1_answer",
                            "target" => $param["target"],
                            "to_list" => array(
                                $user_check_result["value"][0]["email"]
                            ),
                        ));
                    }

                }

            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 1대1 문의 첨부이미지 조회
        // 파라미터 : target
        // 만든이: 최진혁
        *********************************************************************/
        function inquiry_img(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql ="select * from 1to1inquiry_img where inquiry_idx = ".$param["target"]."";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }

          // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  리뷰 조회 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
         /********************************************************************* 
        // 함 수 : 회원 상품 후기 리스트 몇번째 데이터인지
        // 파라미터 : user_idx, target
        // 만든이: 최진혁
        *********************************************************************/
        function user_review_index(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "target"))){
                $sql = "select count(idx) as num from product_review ";
                $sql .= "where user_idx = ".$param["user_idx"]." ";
                $sql .= "and idx >= ".$param["target"]." ";
                $sql .= "order by regdate desc ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 회원 리뷰 조회
        // 파라미터 : user_idx, page_size, page
        // 만든이: 최진혁
        *********************************************************************/
        function review_list(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql ="select t1.* , t3.product_name from product_review as t1 ";
                $sql .= "left join product as t2 ";
                $sql .= "on t1.product_idx = t2.idx ";
                $sql .= "left join product_name as t3 ";
                $sql .= "on t2.idx = t3.product_idx ";
                $sql .= "where t1.user_idx = ".$param["user_idx"]." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql ="select count(t1.idx) as total from product_review as t1 ";
                    $sql .= "left join product as t2 ";
                    $sql .= "on t1.product_idx = t2.idx ";
                    $sql .= "where t1.user_idx = ".$param["user_idx"]."";

                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        $this->result["total_count"] = $total_result["value"][0]["total"];
                    }

                    
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 :리뷰 첨부이미지 조회
        // 파라미터 : target
        // 만든이: 최진혁
        *********************************************************************/
        function review_img(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql ="select * from product_review_img where review_idx = ".$param["target"]."";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : qna 첨부이미지 조회
        // 파라미터 : target
        // 만든이: 최진혁
        *********************************************************************/
        function qna_img(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql ="select * from product_qna_img where qna_idx = ".$param["target"]."";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }


        // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  주문 조회 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        /********************************************************************* 
        // 함 수 : 회원 주문 조회
        // 파라미터 : user_idx, page_size, page
        // 만든이: 최진혁
        *********************************************************************/
        function user_purchase_order_list(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){  
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);
                
                $sql  ="select * from purchase_order ";
                $sql .= "where user_idx = ".$param["user_idx"]." ";
                $sql .= " and (state, pay_type) not in ((0, 'card'),(0, 'transfer')) ";
                $sql .= "order by regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $select_list = $result["value"];
                
                    $sql  ="select count(idx) as total from purchase_order ";
                    $sql .= "where user_idx = ".$param["user_idx"]." ";
                    $sql .= " and (state, pay_type) not in ((0, 'card'),(0, 'transfer')) ";
                    $sql .= "order by regdate desc ";
                    $total_result = $this->conn->db_select($sql);
                    if($result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $sql = "select t1.idx, t1.price, sum(t1.cnt) as product_count, t1.purchase_order_idx, t2.product_name, t2.order_product_thumnail as product_thumnail, ";
                        $sql .= "t1.option_1_idx, t2.option_1_name,t1.option_2_idx, t2.option_2_name, t1.option_3_idx, t2.option_3_name, t1.option_4_idx, t2.option_4_name from order_product as t1 ";
                        $sql .= "left join order_product_name as t2 ";
                        $sql .= "on t1.idx = t2.order_product_idx ";
                        for($i = 0; $i<count($select_list); $i++){
                            if(count($select_list) == 1){
                                $sql .= "where t1.purchase_order_idx = ".$select_list[$i]["idx"]." ";
                            }else{
                                if($i == 0){
                                    $sql .= "where t1.purchase_order_idx in ( ".$select_list[$i]["idx"]." , ";
                                }else if($i == count($select_list) - 1 ){
                                    $sql .= " ".$select_list[$i]["idx"]." ) ";
                                }else{
                                    $sql .= " ".$select_list[$i]["idx"]." , ";
                                }
                            }
                        }
                        $sql .= "and t2.lang_idx = 1 ";
                        
                        $sql .= "group by t1.purchase_order_idx, t1.product_idx, t1.option_1_idx, t1.option_2_idx, t1.option_3_idx, t1.option_4_idx ";
                        $product_result = $this->conn->db_select($sql);
                        if($product_result["result"] == 0){
                            $this->result = $product_result;
                        }else{
                            $product_select_list = $product_result["value"];
                            for($i = 0; $i<count($select_list); $i++){
                                $select_list[$i]["products"] = array();
                                for($j = 0; $j<count($product_select_list); $j++){
                                    if($select_list[$i]["idx"] == $product_select_list[$j]["purchase_order_idx"]){
                                        array_push($select_list[$i]["products"], $product_select_list[$j]);
                                    }
                                }
                            }

                            // print_r($select_list);
                            $this->result = $result;
                            $this->result["value"] = $select_list;
                            $this->result["total_count"] = $total_result["value"][0]["total"];                            
                        }

                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 회원 주문 조회
        // 파라미터 : target(purchase_order_idx)
        // 만든이: 최진혁
        *********************************************************************/
        function order_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))){  
                $sql = "select * from purchase_order ";
                $sql .= "where idx = ".$param["target"]." ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql = "select t1.idx, t1.state, t1.price, t1.is_discount, t1.discount_price, t2.product_name, t1.invoice_number, ";
                    $sql .= "t1.option_1_price, t1.option_2_price, t1.option_3_price, t1.option_4_price, ";
                    $sql .= "t2.order_product_thumnail as product_thumnail, t2.option_1_name, t2.option_2_name, t2.option_3_name, t2.option_4_name ";
                    $sql .= "from order_product as t1 ";
                    $sql .= "left join order_product_name as t2 ";
                    $sql .= "on t1.idx = t2.order_product_idx ";
                    $sql .= "where t1.purchase_order_idx = ".$param["target"]." ";
                    $sql .= "and t2.lang_idx = 1 ";

                    $product_result = $this->conn->db_select($sql);
                    if($product_result["result"] == 0){
                        $this->result = $product_result;
                    }else{

                        $sql = "select sum(cnt) as cnt, state from order_product ";
                        $sql .= "where purchase_order_idx = ".$param["target"]." ";
                        $sql .= "group by state ";

                        $state_total_result = $this->conn->db_select($sql);
                        if($state_total_result["result"] == 0){
                            $this->result = $state_total_result;
                        }else{
                            // 송장번호 조회
                            $sql = "select invoice_number, courier_name from order_product ";
                            $sql .= "where purchase_order_idx = ".$param["target"]." ";
                            $sql .= "group by invoice_number ";


                            $invoice_result = $this->conn->db_select($sql);
                            if($invoice_result["result"] == 0){
                                $this->result = $invoice_result;
                            }else{
                                $this->result = $result;
                                $this->result["product"] = $product_result["value"];
                                $this->result["state_total"] = $state_total_result["value"];
                                $this->result["invoice_number"] = $invoice_result["value"];
                            }
                        }
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  적립금 조회 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        /********************************************************************* 
        // 함 수 : 적립금 조회
        // 파라미터 : user_idx, page_size, page
        // 만든이: 최진혁
        *********************************************************************/
        function point_list(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){  
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select * from point as t1 ";
                $sql .= "where t1.user_idx = ".$param["user_idx"]." ";
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $result  = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $select_list = $result["value"];

                    $sql = "select count(idx) as total_count from point ";
                    $sql .= "where user_idx = ".$param["user_idx"]."";
                    
                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        $this->result["total_count"] = $total_result["value"][0]["total_count"];
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 적립금 추가
        // 파라미터 : user_idx, point_reason, point, expire_date, unlimited_flag
        // 만든이: 최진혁
        *********************************************************************/
        function point_add(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "point_reason", "point"))){
                $sql = "insert into point(user_idx, point, state,  remnant_point, regdate, expire_date, point_title) values(";
                $sql .= $param["user_idx"] . " , ";
                $sql .= $param["point"] . " , 1 , ";
                $sql .= $param["point"] . " , now() , ";
                if($param["unlimited_flag"] == 1){
                    $sql .= " '0000-00-00' , ";
                }else{
                    $sql .= $this->null_check($param["expire_date"]) . " , ";
                }
                $sql .= $this->null_check($param["point_reason"]) . " ";
                $sql .= ")";

                $this->conn->s_transaction();
                $result = $this->conn->db_insert($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $point_idx = $result["value"];

                    // kind = 2 일떄 적립
                    $sql = "insert into point_user_history(user_idx, kind, use_content, point, regdate) value(";
                    $sql .= $param["user_idx"] . " , 2 , ";
                    $sql .= $this->null_check($param["point_reason"]). ", ";
                    $sql .= $param["point"] . ", now() ";
                    $sql .= ")";


                    $result = $this->conn->db_insert($sql);
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        $this->result = $result;
                        $this->conn->commit();
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 적립금 반환
        // 파라미터 : user_idx, target(arr)
        // 만든이: 최진혁
        *********************************************************************/
        function point_return(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "target"))){  
                $target = json_decode($param["target"], true);

                $total_return_point = 0;


                $sql = "update point set ";
                $sql .= "return_point = remnant_point , ";
                $sql .= "remnant_point = 0 ";
                $sql .= "where user_idx = ".$param['user_idx']." ";
                for($i = 0; $i<count($target); $i++){
                    $now_target = json_decode($target[$i], true);
                    $total_return_point = (int)$total_return_point + (int)$now_target["remnant_point"];
                    if(count($target) == 1){
                        $sql .= "and idx = ".$now_target["idx"]." ";
                    }else{
                        if($i == 0){
                            $sql .= "and idx in ( ".$now_target["idx"]." , ";
                        }else if($i == count($target) -1 ){
                            $sql .= " ".$now_target["idx"]." ) ";
                        }else{
                            $sql .= " ".$now_target["idx"]." , ";
                        }
                    }
                }

                $this->conn->s_transaction();
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 적립금 회수일때 kind = 3 
                    $sql = "insert into point_user_history(user_idx, kind, point, use_content, regdate) values";
                    $sql .= "(";
                    $sql .= $param["user_idx"].", 3, ";
                    $sql .= $total_return_point . ", '적립금 반환' , now() )";
                    

                    $result = $this->conn->db_insert($sql);
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        $this->result = $result;
                        $this->conn->commit();
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }
        // // // // // // // // // // // // // // // // // // // // // // // // // //
        // // // // // // //  쿠폰 조회 // // // // // // // // // // // // // 
        // // // // // // // // // // // // // // // // // // // // // // // // // // 
        /********************************************************************* 
        // 함 수 : 쿠폰 조회
        // 파라미터 : user_idx
        // 만든이: 최진혁
        *********************************************************************/
        function coupon_list(){
            $param = $this->param;
            if($this->value_check(array("user_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t3.name, t1.discount_price,t1.discount_kind, t1.max_discount_price, t1.target, t1.state, t1.use_end_date, t1.user_grade_idx, t1.min_limited, ";
                $sql .= "t2.user_idx, t2.idx, t2.regdate, t2.is_use, t2.use_date, t2.purchase_order_idx, t2.retrieve_date from coupon as t1 ";
                $sql .= "left join coupon_relation as t2 ";
                $sql .= "on t1.idx = t2.coupon_idx ";
                $sql .= "left join coupon_name as t3 ";
                $sql .= "on t1.idx = t3.coupon_idx ";
                $sql .= "where t2.user_idx = ".$param["user_idx"]." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by t2.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql = "select count(t2.idx) as total_count from coupon as t1 ";
                    $sql .= "left join coupon_relation as t2 ";
                    $sql .= "on t1.idx = t2.coupon_idx ";
                    $sql .= "where t2.user_idx = ".$param["user_idx"]." ";

                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"]== 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        $this->result["total_count"] = $total_result["value"][0]["total_count"];
                    }
                }
            }       
            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 쿠폰 회수
        // 파라미터 : user_idx, check_arr
        // 만든이: 최진혁
        *********************************************************************/
        function coupon_retrieve(){
            $param = $this->param;
            if($this->value_check(array("user_idx", "check_arr"))){
                $check_arr = json_decode($param["check_arr"], true);

                $sql = "update coupon_relation set ";
                $sql .= " is_use = 2 , ";
                $sql .= " retrieve_date = now() ";
                for($i = 0; $i<count($check_arr); $i++){
                    if(count($check_arr) == 1){
                        $sql .= "where idx = ".$check_arr[$i]." ";
                    }else{
                        if($i == 0){
                            $sql .= "where idx in ( ".$check_arr[$i]." , ";
                        }else if($i == count($check_arr) -1 ){
                            $sql .= " ".$check_arr[$i]." ) ";
                        }else{
                            $sql .= " ".$check_arr[$i]." , ";
                        }
                    }
                }
                
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 쿠폰 회수
        // 파라미터 : user_idx, check_arr
        // 만든이: 최진혁
        *********************************************************************/
        function use_coupon_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql = "select * from coupon as t1 ";
                $sql .= "left join coupon_name as t2 ";
                $sql .= "on t1.idx = t2.coupon_idx ";
                $sql .= "where t2.lang_idx = 1 ";
                $sql .= "and t1.idx = ".$param["target"]." ";
                
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
                
            }
            echo $this->jsonEncode($this->result);
        }


        
        /********************************************************************* 
        // 함 수 : 팝업 주문상세페이지에 상품 상태 리스트 보기
        // 파라미터 : target, list_type
        // 만든이: 최진혁
        *********************************************************************/
        function state_order_product_list(){
            $param = $this->param;
            if($this->value_check(array("target","list_type"))){
                $sql = "select t1.idx, t1.price, t1.purchase_order_idx, t2.product_name, t2.order_product_thumnail as product_thumnail, ";
                $sql .= "t1.option_1_idx, t2.option_1_name,t1.option_2_idx, t2.option_2_name, t1.option_3_idx, t2.option_3_name, t1.option_4_idx, t2.option_4_name, ";
                $sql .= "t1.regdate, t1.deposit_regdate, t1.delivery_ready_regdate, t1.on_delivery_regdate, t1.complete_regdate, ";
                $sql .= "IF(isnull(t1.option_1_price) , 0, t1.option_1_price) as option_1_price, ";
                $sql .= "IF(isnull(t1.option_2_price) , 0, t1.option_2_price) as option_2_price , ";
                $sql .= "IF(isnull(t1.option_3_price) , 0, t1.option_3_price) as option_3_price , ";
                $sql .= "IF(isnull(t1.option_4_price) , 0, t1.option_4_price) as option_4_price  ";
                $sql .= "from order_product as t1 ";
                $sql .= "left join order_product_name as t2 ";
                $sql .= "on t1.idx = t2.order_product_idx ";
                $sql .= "where t2.lang_idx = 1 ";
                $sql .= "and t1.purchase_order_idx = ".$param["target"]."";
                
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }

            }
            echo $this->jsonEncode($this->result);
        }

        
        
        /********************************************************************* 
        // 함 수 : 팝업 주문상세페이지에 상품상태 변경
        // 파라미터 : state_type
        // 만든이: 최진혁
        *********************************************************************/
        function change_order_product_state(){
            $param = $this->param;
            if($this->value_check(array("state_type", "target", "purchase_order_idx"))){
                $target = json_decode($param["target"], true);

                $sql = "select * from purchase_order ";
                $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                $this->conn->s_transaction();
                $result = $this->conn->db_select($sql);
                if($result["result"] ==0){
                    $this->result = $result;
                }else{
                    $order_detail = $result["value"][0];
                    // 적림금 처리 부분
                    // if($order_detail["give_point_flag"] == 0){
                    //     $order_detail["give_point"] = 0;
                    // }

                    $sql = "select t1.price, t1.is_discount, t1.discount_price, t1.option_1_price, t1.option_2_price, t1.option_3_price, t1.option_4_price, sum(t1.cnt) as cnt, ";
                    $sql .= "t2.product_name, t2.option_1_name, t2.option_2_name, t2.option_3_name, t2.option_4_name, t2.order_product_thumnail, t1.state ";
                    $sql .= "from order_product as t1 ";
                    $sql .= "left join order_product_name as t2 ";
                    $sql .= "on t1.idx = t2.order_product_idx ";
                    $sql .= "where t1.purchase_order_idx = ".$param["purchase_order_idx"]." ";
                    $sql .= "and t2.lang_idx = 1 ";
                    for($i = 0; $i<count($target); $i++){
                        if(count($target) == 1){
                            $sql .= "and t1.idx = ".$target[$i]." ";
                        }else{
                            if($i == 0){
                                $sql .= "and t1.idx in ( ".$target[$i]." , ";
                            }else if($i == count($target) - 1){
                                $sql .= "  ".$target[$i]." ) ";
                            }else{
                                $sql .= "  ".$target[$i]." , ";
                            }
                        }
                    }
                    $sql .= "group by t1.product_idx, t1.option_1_idx, t1.option_2_idx, t1.option_3_idx, t1.option_4_idx ";

                    $product_result =$this->conn->db_select($sql);
                    if($product_result["result"] == 0){
                        $this->result = $product_result;
                        $this->conn->rollback();
                        echo $this->jsonEncode($this->result);
                        return;
                    }

                    $product_list = $product_result["value"];


                    $this_purchase_idx = $result["value"][0]["state"];
                    //  0 : 미입금 1 : 입금완료 2 : 배송준비중 3 : 배송중 4 : 배송완료 5 : 취소


                    $sql = "update order_product set ";
                    if($param["state_type"] == "unpaid"){
                        // 미입금
                        $sql .= "state = 0 , ";
                        $sql .= "regdate = now() ";
                    }else if($param["state_type"] == "deposit"){
                        // 입금
                        $sql .= "state = 1 , ";
                        $sql .= "deposit_regdate = now() ";
                    }else if($param["state_type"] == "delivery_ready"){
                        // 배송준비중
                        $sql .= "state = 2 , ";
                        $sql .= "delivery_ready_regdate = now() ";
                    }else if($param["state_type"] == "on_delivery"){
                        // 배송중
                        $sql .= "state = 3 , ";
                        $sql .= "on_delivery_regdate = now() ";
                    }else if($param["state_type"] == "complete"){
                        
                        // 배송완료
                        $sql .= "state = 4 , ";
                        $sql .= "complete_regdate = now() ";
                    }


                    for($i =0 ;$i<count($target); $i++){
                        if(count($target) == 1){
                            $sql .= "where idx = ".$target[$i]." ";
                        }else{
                            if($i == 0){
                                $sql .= "where idx in ( ".$target[$i]." ,  ";
                            }else if($i == count($target) - 1){
                                $sql .= " ".$target[$i]." ) ";
                            }else{
                                $sql .= " ".$target[$i]." , ";
                            }
                        }
                    }
                    
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        
                        $change_purchase_flag = false;
                        if($param["state_type"] == "unpaid"){
                            // 미입금
                            if((int)$this_purchase_idx < 0){
                                $this_purchase_idx = 0;
                                $change_purchase_flag = true;
                            }
                        }else if($param["state_type"] == "deposit"){
                            // 입금
                            if((int)$this_purchase_idx < 1){
                                $this_purchase_idx = 1;
                                $change_purchase_flag = true;
                            }
                        }else if($param["state_type"] == "delivery_ready"){
                            // 배송준비중
                            if((int)$this_purchase_idx < 2){
                                $this_purchase_idx = 2;
                                $change_purchase_flag = true;
                            }
                        }else if($param["state_type"] == "on_delivery"){
                            // 배송중
                            if((int)$this_purchase_idx < 3){
                                $this_purchase_idx = 3;
                                $change_purchase_flag = true;
                            }
                        }else if($param["state_type"] == "complete"){
                            // 배송완료
                            if((int)$this_purchase_idx < 4){
                                $this_purchase_idx = 4;
                                $change_purchase_flag = true;
                            }
                        }

                        // 관리자가 정한 문자 이메일 수신 여부 설정
                        $sql = "select * from admin_user_sms_email_setting ";
                        $sms_email_result = $this->conn->db_select($sql);
                        if($sms_email_result["result"] == 0){
                            $this->result = $sms_email_result;
                            $this->conn->rollback();
                            echo $this->jsonEncode($this->result);
                            return;
                        }
                        // 문자 이메일 세팅
                        $sms_email_setting = $sms_email_result["value"][0];
                        if($order_detail["user_type"] == 1){
                            // 유저 타입이 회원일경우  sms_email_setting에 유저 이메일 문자 셋팅 추가
                            $sql = "select email_agree, sms_agree from user ";
                            $sql .= "where idx = ".$order_detail["user_idx"]." ";

                            $user_result = $this->conn->db_select($sql);
                            if($result["result"] == 0){
                                $this->result = $user_result;
                                $this->conn->rollback();
                                echo $this->jsonEncode($this->result);
                                return;
                            }

                            $user_detail = $user_result["value"][0];
                            $sms_email_setting["user_email_agree"] = $user_detail["email_agree"];
                            $sms_email_setting["user_sms_agree"] = $user_detail["sms_agree"];
                        }


                        if($change_purchase_flag == true){
                            // 주문서 상태가 변경되었을 경우에만 이메일 전송
                            // 주문서 상태값을 변경해야될때
                            $sql = "update purchase_order set ";
                            if($param["state_type"] == "unpaid"){
                                // 미입금
                                $sql .= "state = 0 , ";
                                $sql .= "regdate = now() ";
                            }else if($param["state_type"] == "deposit"){
                                // 입금
                                $sql .= "state = 1 , ";
                                $sql .= "deposit_regdate = now() ";
                                
                            }else if($param["state_type"] == "delivery_ready"){
                                // 배송준비중
                                $sql .= "state = 2 , ";
                                $sql .= "delivery_ready_regdate = now() ";
                            }else if($param["state_type"] == "complete"){
                                
                                // 배송완료
                                $sql .= "state = 4 , ";
                                $sql .= "complete_regdate = now() ";
                                
                            }

                            $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                            $result = $this->conn->db_update($sql);
                            if($result["result"] == 0){
                                $this->result = $result;
                            }else{
                                if($param["state_type"] == "complete"){
                                    // 일년 뒤의 날짜
                                    $year_after_date = date("Y-m-d H:i:s", strtotime("+1 year", time()));
                                    // 주문서 상태값을 변경할 당시에만 포인트 적립(회원일 경우에만)
                                    // 주문서에서 포인트가 지급된상태가 아닌경우에만 포인트 지급
                                    if($order_detail["user_type"] == 1 && $order_detail["give_point_flag"] == 0){
                                        if($order_detail["give_point"] != "" && $order_detail["give_point"] != 0){
                                            // 결제 후 적립금액이 없거나 0원이 아닌경우
                                            $sql = "insert into point(user_idx, point, state, remnant_point, regdate, expire_date, point_title) values(";
                                            $sql .= $order_detail["user_idx"] . " , ";
                                            $sql .= $order_detail["give_point"] . " , 1 , ";
                                            $sql .= $order_detail["give_point"] . ", now() ,";
                                            $sql .= $this->null_check($year_after_date) . " , ";
                                            $sql .= "'결제 적립금 지급' ) ";
                                            $result = $this->conn->db_insert($sql);
                                            if($result["result"] == 0){
                                                $this->result = $result;
                                            }else{
                                                // 포인트 적립내역 insert
                                                $sql = "insert into point_user_history(user_idx, use_content, kind, point, order_number, regdate) values(";
                                                $sql .= $order_detail["user_idx"]. " , ";
                                                $sql .= "'결제 적립금 지급' , 2 , ";
                                                $sql .= $order_detail["give_point"] . " , ";
                                                $sql .= $this->null_check($order_detail["order_number"]) . " ,  now()";
                                                $sql .= ")";
                                                
                                                $result = $this->conn->db_insert($sql);
                                                if($result["result"] == 0){
                                                    $this->result = $result;
                                                }else{
                                                    //  주문서 포인트 발급된 상태로 변경 give_point_flag = 1
                                                    $sql = "update purchase_order set ";
                                                    $sql .= "give_point_flag = 1 ";
                                                    $sql .= "where idx = ".$param["purchase_order_idx"]." ";
                                                    $result = $this->conn->db_update($sql);
                                                    if($result["result"] == 0){
                                                        $this->result = $result;
                                                    }else{
                                                        $this->result = $result;
                                                        
                                                        if($param["state_type"] == "complete"){
                                                            if($sms_email_setting["user_email_agree"] == 1){
                                                               
                                                                // 사용자에게 이메일 발신
                                                                $this->MailForm->send_email(array(
                                                                    "type" => "u_delivery_complete",
                                                                    "to_list" => array($order_detail["orderer_email"]),
                                                                    "order" => $order_detail,
                                                                    "product" => $product_list,
                                                                    "regdate" => date("Y-m-d H:i:s")
                                                                ));
                                                            }
                                                            if($sms_email_setting["user_sms_agree"] == 1){
                                                                // 유저에게 문자 발신
                                                                $this->BillingSMS->send_tracking(array(
                                                                    "send_type" => "kakao",
                                                                    "type" => "order_complete",
                                                                    "order_number" => $order_detail["order_number"],
                                                                    "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                                ), null);
                                                            }
                                                        }
                                                        $this->conn->commit();
                                                    }
                                                }
                                            }
                                        }else{
                                            //  주문서 포인트 발급된 상태로 변경 give_point_flag = 1
                                            $sql = "update purchase_order set ";
                                            $sql .= "give_point_flag = 1 ";
                                            $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                                            $result = $this->conn->db_update($sql);
                                            if($result["result"] == 0){
                                                $this->result = $result;
                                            }else{
                                                $this->result = $result;
                                                
                                                if($param["state_type"] == "complete"){
                                                    if($sms_email_setting["user_email_agree"] == 1){

                                                         // 사용자에게 이메일 발신
                                                         $this->MailForm->send_email(array(
                                                            "type" => "u_delivery_complete",
                                                            "to_list" => array($order_detail["orderer_email"]),
                                                            "order" => $order_detail,
                                                            "product" => $product_list,
                                                            "regdate" => date("Y-m-d H:i:s")
                                                        ));
                                                    }

                                                    if($sms_email_setting["user_sms_agree"] == 1){

                                                        // 유저에게 문자 발신
                                                        $this->BillingSMS->send_tracking(array(
                                                            "send_type" => "kakao",
                                                            "type" => "order_complete",
                                                            "order_number" => $order_detail["order_number"],
                                                            "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                        ), null);
                                                    }
                                                }
                                                $this->conn->commit();
                                            }
                                            
                                        }
                                    }else{
                                        // 비회원 주문일 경우
                                        $this->result = $result;
                                        
                                        if($param["state_type"] == "complete"){
                                            // 유저에게 문자 발신
                                            $this->BillingSMS->send_tracking(array(
                                                "send_type" => "kakao",
                                                "type" => "order_complete",
                                                "order_number" => $order_detail["order_number"],
                                                "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                            ), null);

                                            // 사용자에게 이메일 발신
                                            $this->MailForm->send_email(array(
                                                "type" => "u_delivery_complete",
                                                "to_list" => array($order_detail["orderer_email"]),
                                                "order" => $order_detail,
                                                "product" => $product_list,
                                                "regdate" => date("Y-m-d H:i:s")
                                            ));
                                        }
                                        $this->conn->commit();
                                    }
                                }else{
                                    // 배송완료가 아닌경우
                                    $this->result = $result;
                                    
                                    if($param["state_type"] == "deposit"){
                                        // 입금완료
                                        if($sms_email_setting["s_deposit_complete"] == 1){
                                            if($order_detail["user_type"] == 1){
                                                if($sms_email_setting["user_sms_agree"] == 1){

                                                    // 유저에게 문자 발신
                                                    $this->BillingSMS->send_tracking(array(
                                                        "send_type" => "kakao",
                                                        "type" => "order_deposit",
                                                        "order_number" => $order_detail["order_number"],
                                                        "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                    ), null);
                                                }
                                            }else{
                                                // 유저에게 문자 발신
                                                $this->BillingSMS->send_tracking(array(
                                                    "send_type" => "kakao",
                                                    "type" => "order_deposit",
                                                    "order_number" => $order_detail["order_number"],
                                                    "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                ), null);
                                            }
                                        }

                                        if($order_detail["user_type"] == 1){
                                            if($sms_email_setting["user_email_agree"] == 1){
                                                 // 사용자에게 이메일 발신
                                                $this->MailForm->send_email(array(
                                                    "type" => "u_order_deposit",
                                                    "to_list" => array($order_detail["orderer_email"]),
                                                    "order" => $order_detail,
                                                    "product" => $product_list,
                                                    "regdate" => date("Y-m-d H:i:s")
                                                ));
                                            }
                                        }else{
                                             // 사용자에게 이메일 발신
                                             $this->MailForm->send_email(array(
                                                "type" => "u_order_deposit",
                                                "to_list" => array($order_detail["orderer_email"]),
                                                "order" => $order_detail,
                                                "product" => $product_list,
                                                "regdate" => date("Y-m-d H:i:s")
                                            ));
                                        }
                                    }else if($param["state_type"] == "complete"){
                                        // 완료
                                        if($order_detail["user_type"] == 1){
                                            if($sms_email_setting["user_sms_agree"] == 1){
                                                // 유저에게 문자 발신
                                                $this->BillingSMS->send_tracking(array(
                                                    "send_type" => "kakao",
                                                    "type" => "order_complete",
                                                    "order_number" => $order_detail["order_number"],
                                                    "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                ), null);
                                            }
                                        }else{
                                            // 유저에게 문자 발신
                                            $this->BillingSMS->send_tracking(array(
                                                "send_type" => "kakao",
                                                "type" => "order_complete",
                                                "order_number" => $order_detail["order_number"],
                                                "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                            ), null);
                                        }
                                        if($order_detail["user_type"] == 1){
                                            if($sms_email_setting["user_email_agree"] == 1){
                                                // 사용자에게 이메일 발신
                                                $this->MailForm->send_email(array(
                                                    "type" => "u_delivery_complete",
                                                    "to_list" => array($order_detail["orderer_email"]),
                                                    "order" => $order_detail,
                                                    "product" => $product_list,
                                                    "regdate" => date("Y-m-d H:i:s")
                                                )); 
                                            }
                                        }else{
                                            // 사용자에게 이메일 발신
                                            $this->MailForm->send_email(array(
                                                "type" => "u_delivery_complete",
                                                "to_list" => array($order_detail["orderer_email"]),
                                                "order" => $order_detail,
                                                "product" => $product_list,
                                                "regdate" => date("Y-m-d H:i:s")
                                            )); 
                                        }
                                    }
                                    $this->conn->commit();
                                }
                            }
                        }else{
                            $this->result = $result;
                            $this->conn->commit();
                        }
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }


        // 재고 되돌리기
        function stock_return($product_array){
            for($i = 0; $i<count($product_array); $i++){
                $obj = $product_array[$i];
                if($obj["product_is_stock"] == 1){
                    // 단일 재고일 경우
                    $sql = "update product set ";
                    $sql .= "total_stock = total_stock + 1 ";
                    $sql .= "where idx = ".$obj["product_idx"]." ";
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == 0){
                        return $result;
                    }
                }else{
                    if($obj["product_key"] == "option_1"){
                        if($obj["option_1_is_stock"] == 1){
                            // 재고를 사용할 경우
                            $sql = "update option_1 set ";
                            $sql .= "total_stock = total_stock + 1 ";
                            $sql .= "where idx = ".$obj["option_1_idx"]." ";
                            $result = $this->conn->db_update($sql);
                            if($result["result"] == 0){
                                return $result;
                            }       
                        }
                    }else if($obj["product_key"] == "option_2"){
                        if($obj["option_2_is_stock"] == 1){
                            // 재고를 사용할 경우
                            $sql = "update option_2 set ";
                            $sql .= "total_stock = total_stock + 1 ";
                            $sql .= "where idx = ".$obj["option_2_idx"]." ";
                            $result = $this->conn->db_update($sql);
                            if($result["result"] == 0){
                                return $result;
                            }       
                        }
                    }else if($obj["product_key"] == "option_3"){
                        if($obj["option_3_is_stock"] == 1){
                            // 재고를 사용할 경우
                            $sql = "update option_3 set ";
                            $sql .= "total_stock = total_stock + 1 ";
                            $sql .= "where idx = ".$obj["option_3_idx"]." ";
                            $result = $this->conn->db_update($sql);
                            if($result["result"] == 0){
                                return $result;
                            }       
                        }
                    }else if($obj["product_key"] == "option_4"){
                        if($obj["option_4_is_stock"] == 1){
                            // 재고를 사용할 경우
                            $sql = "update option_4 set ";
                            $sql .= "total_stock = total_stock + 1 ";
                            $sql .= "where idx = ".$obj["option_4_idx"]." ";
                            $result = $this->conn->db_update($sql);
                            if($result["result"] == 0){
                                return $result;
                            }       
                        }
                    }
                }
            }

            // 재고를 반환한 상품들은 stock_return 컬럼을 1로 바꿔준다
            $sql = "update order_product set ";
            $sql .= "stock_return = 1 ";
            for($i = 0; $i<count($product_array); $i++){
                if(count($product_array) == 1){
                    $sql .= "where idx = ".$product_array[$i]["idx"]." ";
                }else{
                    if($i == 0){
                        $sql .= "where idx in ( ".$product_array[$i]["idx"]." , ";
                    }else if($i == count($product_array) - 1 ){
                        $sql .= " ".$product_array[$i]["idx"]." ) ";
                    }else{
                        $sql .= " ".$product_array[$i]["idx"]." , ";
                    }
                }
            }

            $result = $this->conn->db_update($sql);
            if($result["result"] == 0){
                return $result;
            }

            return 1;
        }

        /********************************************************************* 
        // 함 수 : 팝업 주문상세페이지에 상품처리상태 변경
        // 파라미터 : state_type
        // 만든이: 최진혁
        *********************************************************************/
        function order_product_process_state(){
            $param = $this->param;
            if($this->value_check(array("state_type", "target", "purchase_order_idx"))){
                $target = json_decode($param["target"], true);
                
                $sql = "select * from purchase_order ";
                $sql .= "where idx = ".$param['purchase_order_idx']." ";
                
                $this->conn->s_transaction();
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 주문서 전체내용
                    $order_detail = $result["value"][0];

                    // 사용된 포인트(반환될 금액)
                    $use_point = $order_detail["use_point"];
                    // 총 결제 금액
                    $total_price = $order_detail["total_price"];
                    // 배송비
                    if($order_detail["delivery_price"] == ""){
                        $delivery_price = 0;
                    }else{
                        $delivery_price = $order_detail["delivery_price"];
                    }
                    // 이미 환불된 금액
                    $already_refund_price = $order_detail["refund_price"];
                    // 결제타입 -> 무통장입금일시 환불로직 없음(무통장입금일시 뷰페이지에서 환불되었는지 확인)
                    $pay_type = $order_detail["pay_type"];
                    // 주문취소와  상품 반품 로직
                    if($param["state_type"] == "cancel"){
                        // 주문취소일때 -> 배송하지 않은 상태에서만 가능하기때문에 배송비까지 모두 환불 사용된 적립금을 반환
                        if((int)($order_detail["state"]) > 1 && (int)$order_detail["state"] != 5){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "7010";
                            $this->result["message"] = "현재 주문서는 배송이 진행된 주문이기때문에 주문취소가 불가능합니다.";
                            $this->conn->rollback();
                        }else if((int)$order_detail["state"] == 5){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "7010";
                            $this->result["message"] = "이미 주문 취소된 주문서입니다.";
                            $this->conn->rollback();
                        }else{
                            // 취소요청된 상품 조회
                            $sql = "select idx, state, product_idx, option_1_idx, option_2_idx, option_3_idx, option_4_idx, product_key, product_is_stock, option_1_is_stock, option_2_is_stock, option_3_is_stock, option_4_is_stock from order_product ";
                            $sql .= "where purchase_order_idx = ".$param["purchase_order_idx"]." ";
                            $result = $this->conn->db_select($sql);
                            if($result["result"] == 0){
                                $this->result["result"] = $result;
                            }else{
                                $cancel_list = $result["value"];
                                
                                for($i = 0; $i<count($cancel_list); $i++){
                                    if($cancel_list[$i]["state"] == "0"){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7060";
                                        $this->result["message"] = "입금대기 상태인 상품이 있어 주문취소가 불가능합니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else if($cancel_list[$i]["state"] == "8"){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7061";
                                        $this->result["message"] = "이미 환불된 상품이 있어 주문취소가 불가능합니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else if($cancel_list[$i]["state"] == "15"){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7062";
                                        $this->result["message"] = "이미 반품된 상품이 있어 주문취소가 불가능합니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }
                                }

                                $sql = "update purchase_order set ";
                                $sql .= "state = 5 , ";
                                $sql .= "refund_price = total_price ";
                                // $sql .= "cancel_complete_regdate = now() ";
                                $sql .= "where idx = ".$param["purchase_order_idx"]." ";
    
                                $result = $this->conn->db_update($sql);
                                if($result["result"] == 0){
                                    $this->result = $result;
                                }else{
                                    $sql = "update order_product set ";
                                    $sql .= "state = 5 , ";
                                    $sql .= "cancel_complete_regdate = now() ";
                                    $sql .= "where purchase_order_idx = ".$param["purchase_order_idx"]." ";
                                    $result = $this->conn->db_update($sql);
                                    if($result["result"] == 0){
                                        $this->result = $result;
                                    }else{
                                        // 재고 되돌리기
                                        $stock_result = $this->stock_return($cancel_list);
                                        if($stock_result != 1){
                                            $this->result = $stock_result;
                                            echo $this->jsonEncode($this->result);
                                            return;
                                        }else{
                                            if($pay_type == "account"){
                                                $this->result = $result;
                                                $this->conn->commit();
                                            }else{
                                                // 아임포트 환불로직
                                                $import = new PaymentModel();
                                                // 환불할금액 = 총 결제금액
                                                $amount = $total_price;
                                                $result = $import->payment_refund($order_detail["imp_uid"], $order_detail["order_number"], "주문취소");
                                                if($result["code"] == 0){
                                                    $this->result["result"] = 1;
                                                    $this->conn->commit();
                                                    
                                                }else{
                                                    $this->result["result"] = 0;
                                                    $this->result["error_codes"] = "7020";
                                                    $this->result["message"] = "아임포트 환불실패";
                                                    $this->conn->rollback();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else if($param["state_type"] == "refund" || $param["state_type"] == "return"){
                        // 요청 기능이 환불 또는 반품일 경우
                        // 1. 환불가능한 금액인지 확인 (환불가능 금액 = 실 결제금액 - 배송비 - 이미 환불된 금액)
                        $possible_refund_price = (int)$total_price - (int)$delivery_price - (int)$already_refund_price;

                        // 환불할 상품 조회
                        $sql = "select * from order_product ";
                        for($i = 0; $i<count($target); $i++){
                            if(count($target) == 1){
                                $sql .= "where idx = ".$target[$i]." ";
                            }else{
                                if($i == 0){
                                    $sql .= "where idx in ( ".$target[$i]." , ";
                                }else if($i == count($target) - 1){
                                    $sql .= " ".$target[$i]." ) ";
                                }else{
                                    $sql .= " ".$target[$i]." , ";
                                }
                            }

                        }

                        

                        $result = $this->conn->db_select($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                        }else{
                            // 환불할 상품리스트
                            $refund_list = $result["value"];
                            

                            // 주문서가 환불가능한 상태인지 체크
                            if((int)($order_detail["state"]) < 1 && (int)$order_detail["state"] != 5){
                                $this->result["result"] = 0;
                                $this->result["error_code"] = "7010";
                                $this->result["message"] = "입금되지 않은 주문서입니다.";
                                $this->conn->rollback();
                            }else if((int)$order_detail["state"] == 5){
                                $this->result["result"] = 0;
                                $this->result["error_code"] = "7010";
                                $this->result["message"] = "이미 주문이 취소된 주문서입니다.";
                                $this->conn->rollback();
                            }else{

                                for($i =0; $i<count($refund_list); $i++){
                                    if($refund_list[$i]["state"] == 0){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7051";
                                        $this->result["message"] = "입금대기 상태인 상품이있습니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else if($refund_list[$i]["state"] == 8){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7052";
                                        $this->result["message"] = "환불완료 상태인 상품이있습니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else if($refund_list[$i]["state"] == 5){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7053";
                                        $this->result["message"] = "취소 상태인 상품이있습니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else if($refund_list[$i]["state"] == 15){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7054";
                                        $this->result["message"] = "반품완료 상태인 상품이있습니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }
                                }
                                

                                // 환불할 금액
                                $refund_price = 0;
                                for($i =0; $i<count($refund_list); $i++){
                                    $price = 0;
                                    if($refund_list[$i]["is_discount"] == 1){
                                        $price = $refund_list[$i]["discount_price"];
                                    }else{
                                        $price = $refund_list[$i]["price"];
                                    }
                                    $cur_refund_price = (int)$price + (int)$refund_list[$i]["option_1_price"]  + (int)$refund_list[$i]["option_2_price"]  + (int)$refund_list[$i]["option_3_price"]  + (int)$refund_list[$i]["option_4_price"];
                                    $refund_price = (int)$refund_price + (int)$cur_refund_price;
                                }

                                // 환불가능한 금액보다 환불할 금액이 클시 자동환불 불가능하고 수동환불로 넘어감
                                if((int)$refund_price > (int)$possible_refund_price){
                                    $this->result["result"] = 0;
                                    $this->result["error_code"] = "7040";
                                    $this->result["message"] = "환불금액이 환불가능금액보다 큽니다.";
                                    $this->conn->rollback();
                                }else{
                                    // 환불가능함 환불로직 
                                    // 환불할 금액 
                                    $amount = $refund_price;
                                    
                                    $sql = "update order_product set ";
                                    if($param["state_type"] == "refund"){
                                        // 환불타입이 환불일경
                                        $sql .= "state = 8 , ";
                                        $sql .= "refund_complete_regdate = now() ";
                                    }else if($param["state_type"] == "return"){
                                        // 환불타입이 반품일 경우
                                        $sql .= "state =  15, ";
                                        $sql .= "return_complete_regdate = now() ";
                                    }
                                    for($i = 0; $i<count($target); $i++){
                                        if(count($target) == 1){
                                            $sql .= "where idx = ".$target[$i]." ";
                                        }else{
                                            if($i == 0){
                                                $sql .= "where idx in ( ".$target[$i]." , ";
                                            }else if($i == count($target) - 1){
                                                $sql .= " ".$target[$i]." ) ";
                                            }else{
                                                $sql .= " ".$target[$i]." , ";
                                            }
                                        }
                                    }

                                    

                                    $result = $this->conn->db_update($sql);
                                    if($result["result"] == 0){
                                        $this->result = $result;
                                    }else{
                                        $sql = "update purchase_order set ";
                                        $sql .= "refund_price = refund_price + ".$amount." ";
                                        $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                                        $result = $this->conn->db_update($sql);
                                        if($result["result"] == 0){
                                            $this->result = $result;
                                        }else{
                                            // 재고 되돌리기
                                            $stock_result = $this->stock_return($refund_list);
                                            if($stock_result != 1){
                                                $this->result = $stock_result;
                                                echo $this->jsonEncode($this->result);
                                                return;
                                            }else{
                                                if($order_detail["user_type"] == "1"){
                                                    // user_type == 1 이면 회원 0 이면 비회원
                                                    // 현재 결제 금액 : 총 결제 금액 - 배송비 - 환불된 금액
                                                    $cur_price = (int)$order_detail["total_price"] - (int)$order_detail["delivery_price"] - (int)$order_detail["refund_price"] - (int)$amount;
    
                                                    $sql = "select * from user as t1 ";
                                                    $sql .= "left join user_grade as t2 ";
                                                    $sql .= "on t1.user_grade_idx = t2.idx ";
                                                    $sql .= "where t1.idx = ".$order_detail["user_idx"]." ";
    
                                                    $result = $this->conn->db_select($sql);
                                                    if($result["result"] == 0){
                                                        $this->result = $result;
                                                    }else{
                                                        // 유저 정보
                                                        $user_detail = $result["value"][0];
                                                        $point_percent = $user_detail["point_percent"];
                                                        $origin_give_point = $order_detail["give_point"];
    
                                                        // 적립될 포인트 (현재 총 결제금액에 결제당시 적립퍼센테이지)
                                                        $point = ((int)$cur_price/100) * (int)$order_detail["point_percent"];
                                                        $point = ceil($point);
    
                                                        // 현재 포인트에서 차감해야함
                                                        $decrease_give_point = (int)$origin_give_point - (int)$point;
    
                                                        // 적립금 변경
                                                        $sql = "update purchase_order set ";
                                                        $sql .= "give_point = ".$point." ";
                                                        $sql .= "where idx = ".$param["purchase_order_idx"]." ";
    
                                                        $result = $this->conn->db_update($sql);
                                                        if($result["result"] == 0){
                                                            $this->result = $result;
                                                        }else{
                                                            if($order_detail["give_point_flag"] == 0 || $decrease_give_point == 0){
                                                                // 적립여부 0이면 미지급 1이면 지급 
                                                                // 적립여부가 0이거나 차감 포인트가 0인경우
                                                                if($pay_type == "account"){
                                                                    $this->result = $result;
                                                                    $this->conn->commit();
                                                                }else{
                                                                    // 비회원이면 아임포트 환불
                                                                    $reason = "";
                                                                    if($param["state_type"] == "return"){
                                                                        if($refund_list[0]["return_reason"] == ""){
                                                                            $reason = "관리자가 반품처리";
                                                                        }else{
                                                                            $reason = $refund_list[0]["return_reason"];
                                                                        }
                                                                    }else{
                                                                        $reason = "관리자가 환불처리";
                                                                    }
                                                                    // 아임포트 환불로직
                                                                    $import = new PaymentModel();
                                                                    $result = $import->payment_part_refund($order_detail["imp_uid"], $order_detail["order_number"], $reason, $amount);
                                                                    if($result["code"] == 0){
                                                                        $this->result["result"] = 1;
                                                                        $this->conn->commit();
                                                                    }else{
                                                                        $this->result["result"] = 0;
                                                                        $this->result["error_codes"] = "7020";
                                                                        $this->result["message"] = "아임포트 환불실패";
                                                                        $this->conn->rollback();
                                                                    }
                                                                }
                                                            }else{
                                                                // 포인트 반환해야할때
                                                                $year_ago_date = date("Y-m-d H:i:s", strtotime("-1 year", time()));
                                                                // 포인트 테이블 조회 1년전부터
                                                                $sql = "select idx, user_idx, state, remnant_point from point ";
                                                                $sql .= "where user_idx = ".$order_detail["user_idx"]." ";
                                                                $sql .= "and (regdate > ".$this->null_check($year_ago_date)." or expire_date = '0000-00-00 00:00:00') ";
                                                                $sql .= "and state = 1 ";
                                                                $sql .= "order by regdate asc ";
    
                                                                $point_result = $this->conn->db_select($sql);
                                                                if($point_result["result"] == 0){
                                                                    $this->result = $point_result;
                                                                }else{
                                                                    $point_list = $point_result["value"];
                                                                    $cur_decrease_give_point = $decrease_give_point;
    
                                                                    for($i =0; $i<count($point_list); $i++){
                                                                        // 현재 데이터 잔여포인트
                                                                        $remnant_point = $point_list[$i]["remnant_point"];
                                                                        if((int)$remnant_point >= (int)$cur_decrease_give_point){
                                                                            // 디비상 남은 포인트가 현재 차감 포인트 보다 적으면 현재 사용할 포인트를 차감
                                                                            $sql = "update point set ";
                                                                            $sql .= "remnant_point = remnant_point - ".$cur_decrease_give_point.", ";
                                                                            $sql .= "return_point = return_point + ".$cur_decrease_give_point." ";
                                                                            if((int)$remnant_point == (int)$cur_decrease_give_point){
                                                                                $sql .= ", state = 0 ";
                                                                            }
                                                                            $sql .= "where idx = ".$point_list[$i]["idx"]." ";
                                                                            
                                                                            $point_update_result = $this->conn->db_update($sql);
                                                                            if($point_update_result["result"] == 0){
                                                                                $this->result = $point_update_result;
                                                                                echo $this->jsonEncode($this->result);
                                                                                return;
                                                                            }else{
                                                                                break;
                                                                            }
                                                                        }
                                                                        else if((int)$remnant_point < (int)$cur_decrease_give_point){
                                                                            // 디비상 남은 포인트가 현재 차감 포인트가 적으면 디비상 남은 포인트를 차감
                                                                            $cur_decrease_give_point = (int)$cur_decrease_give_point - (int)$remnant_point;
    
                                                                            $sql = "update point set ";
                                                                            $sql .= "remnant_point = remnant_point - ".$remnant_point.", ";
                                                                            $sql .= "return_point = return_point + ".$remnant_point.", ";
                                                                            $sql .= "state = 0 ";
                                                                            $sql .= "where idx = ".$point_list[$i]["idx"]." ";
    
                                                                            $point_update_result = $this->conn->db_update($sql);
                                                                            if($point_update_result["result"] == 0){
                                                                                $this->result = $point_update_result;
                                                                                echo $this->jsonEncode($this->result);
                                                                                return;
                                                                            }
                                                                        }
                                                                    }

                                                                    // 유저 적립금 기록에 남김
                                                                    $sql = "insert into point_user_history (kind, point, user_idx, use_content, order_number, regdate) values (";
                                                                    $sql .= "3, ";
                                                                    $sql .= $decrease_give_point . ", ";
                                                                    $sql .= $order_detail["user_idx"] . ", ";
                                                                    if($param["state_type"] == "refund"){
                                                                        $sql .= " '환불로 인한 적립금 회수' , ";
                                                                    }else if($param["state_type"] == "return"){
                                                                        $sql .= " '반품으로 인한 적립금 회수' , ";
                                                                    }
                                                                    $sql .= $this->null_check($order_detail["order_number"]) . ", now() ";
                                                                    $sql .= ")";
    
                                                                    $point_history_result = $this->conn->db_insert($sql);
                                                                    if($point_history_result["result"] == 0){
                                                                        $this->result = $point_history_result;
                                                                    }else{
                                                                        if($pay_type == "account"){
                                                                            $this->result["result"] = $point_history_result;
                                                                            $this->conn->commit();
                                                                        }else{
                                                                            // 비회원이면 아임포트 환불
                                                                            $reason = "";
                                                                            if($param["state_type"] == "return"){
                                                                                if($refund_list[0]["return_reason"] == ""){
                                                                                    $reason = "관리자가 반품처리";
                                                                                }else{
                                                                                    $reason = $refund_list[0]["return_reason"];
                                                                                }
                                                                            }else{
                                                                                $reason = "관리자가 환불처리";
                                                                            }
                                                                            // 아임포트 환불로직
                                                                            $import = new PaymentModel();
                                                                            $result = $import->payment_part_refund($order_detail["imp_uid"], $order_detail["order_number"], $reason, $amount);
                                                                            if($result["code"] == 0){
                                                                                $this->result["result"] = 1;
                                                                                $this->conn->commit();
                                                                            }else{
                                                                                $this->result["result"] = 0;
                                                                                $this->result["error_codes"] = "7020";
                                                                                $this->result["message"] = "아임포트 환불실패";
                                                                                $this->conn->rollback();
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }else{
                                                    if($pay_type == "account"){
                                                        $this->result = $result;
                                                        $this->conn->commit();
                                                    }else{
                                                        // 비회원이면 아임포트 환불
                                                        $reason = "";
                                                        if($param["state_type"] == "return"){
                                                            if($refund_list[0]["return_reason"] == ""){
                                                                $reason = "관리자가 반품처리";
                                                            }else{
                                                                $reason = $refund_list[0]["return_reason"];
                                                            }
                                                        }else{
                                                            $reason = "관리자가 환불처리";
                                                        }
                                                        // 아임포트 환불로직
                                                        $import = new PaymentModel();
                                                        $result = $import->payment_part_refund($order_detail["imp_uid"], $order_detail["order_number"], $reason, $amount);
                                                        if($result["code"] == 0){
                                                            $this->result["result"] = 1;
                                                            $this->conn->commit();
                                                        }else{
                                                            $this->result["result"] = 0;
                                                            $this->result["error_codes"] = "7020";
                                                            $this->result["message"] = "아임포트 환불실패";
                                                            $this->conn->rollback();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }else if($param["state_type"] == "exchange"){
                        //교환일때 -> 배송완료 상태에서만 교환상태로 전환가능
                        if((int)$order_detail["state"] != 4){
                            $this->result["result"] = 0;
                            $this->result["error_code"] = "7013";
                            $this->result["message"] = "현재 주문서는 배송완료된 주문이 아닙니다.";
                            $this->conn->rollback();
                        }else{
                            // 교환요청된 상품 조회
                            $sql = "select * from order_product ";
                            for($i = 0; $i<count($target); $i++){
                                if(count($target)  == 1){
                                    $sql .= "where idx = ".$target[$i]." ";
                                }else{
                                    if($i == 0){
                                        $sql .= "where idx in ( ".$target[$i]." , ";
                                    }else if($i == count($target) - 1 ){
                                        $sql .= " ".$target[$i]." ) ";
                                    }else{
                                        $sql .= " ".$target[$i]." , ";
                                    }
                                }
                            }
                            // $sql .= "where purchase_order_idx = ".$param["purchase_order_idx"]." ";
                            
                            $result = $this->conn->db_select($sql);
                            if($result["result"] == 0){
                                $this->result = $result;
                            }else{
                                $exchange_list = $result["value"];

                                for($i = 0; $i<count($exchange_list); $i++){
                                    if($exchange_list[$i]["state"] != "5"){
                                        $this->result["result"] = 0;
                                        $this->result["error_code"] = "7065";
                                        $this->result["message"] = "선택한 상품중 배송완료상태가 아닌 상품이있습니다.";
                                        $this->conn->rollback();
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }
                                }

                                // 교환완료 상태로 바꿈
                                $sql = "update  order_product set ";
                                $sql .= "state = 11 , ";
                                $sql .= "exchange_complete_regdate = now() ";
                                for($i = 0; $i<count($target); $i++){
                                    if(count($target)  == 1){
                                        $sql .= "where idx = ".$target[$i]." ";
                                    }else{
                                        if($i == 0){
                                            $sql .= "where idx in ( ".$target[$i]." , ";
                                        }else if($i == count($target) - 1 ){
                                            $sql .= " ".$target[$i]." ) ";
                                        }else{
                                            $sql .= " ".$target[$i]." , ";
                                        }
                                    }
                                }

                                $result = $this->conn->db_update($sql);
                                if($result["result"] == 0){
                                    $this->result = $result;
                                }else{
                                    // 재고 되돌리기
                                    $stock_result = $this->stock_return($exchange_list);
                                    if($stock_result != 1){
                                        $this->result = $stock_result;
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else{
                                        $this->result = $result;
                                        $this->conn->commit();
                                    }
                                }
                            }
                        }
                    }else{
                        $this->result["result"] = 0;
                        $this->result["error_code"] = 7090;
                        $this->result["message"] = "바뀔 상태값이 없습니다.";
                        $this->conn->rollback();
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 수동환불 상풀리스트 및 가격정보
        // 파라미터 :  
        // 만든이: 최진혁
        *********************************************************************/
        function order_refund_list(){
            $param = $this->param;
            if($this->value_check(array("state_type", "target", "purchase_order_idx"))){
                $target = json_decode($param["target"], true);

                $sql = "select * from purchase_order ";
                $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $sql = "select sum(t1.cnt) as cnt, t1.discount_price, t1.price, t1.is_discount, ";
                    $sql .= "t2.option_1_name , t2.option_2_name, t2.option_3_name, t2.option_4_name, t2.product_name, ";
                    $sql .= "t1.option_1_price, t1.option_2_price, t1.option_3_price, t1.option_4_price ";
                    $sql .= " from order_product as t1 ";
                    $sql .= "left join order_product_name as t2 ";
                    $sql .= "on t1.idx = t2.order_product_idx ";
                    $sql .= "where t2.lang_idx = 1 ";
                    for($i = 0; $i<count($target); $i++){
                        if(count($target) == 1){
                            $sql .= "and t1.idx = ".$target[$i]." ";
                        }else{ 
                            if($i == 0){
                                $sql .= "and t1.idx in ( ".$target[$i]." , ";
                            }else if($i == count($target) - 1 ){
                                $sql .= " ".$target[$i]." ) ";
                            }else{
                                $sql .= " ".$target[$i]." , ";
                            }
                        }
                    }
                    $sql .= "group by t1.product_idx, t1.option_1_idx, t1.option_2_idx, t1.option_3_idx, t1.option_4_idx ";
    
                    $product_result = $this->conn->db_select($sql);
                    if($product_result["result"] == 0){
                        $this->result = $product_result;
                    }else{
                        $this->result = $result;
                        $this->result["products"] = $product_result["value"];
                    }
                }

            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 수동환불
        // 파라미터 :  
        // 만든이: 최진혁
        *********************************************************************/
        function menual_refund(){
            $param = $this->param;
            if($this->value_check(array("target", "purchase_order_idx"))){
                $target = json_decode($param["target"], true);

                // 주문서에 refund_price 추가하기
                $sql = "update purchase_order set ";
                $sql .= "refund_price = refund_price + ".$param["refund_price"]." ";
                $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                $this->conn->s_transaction();
                $result = $this->conn->db_update($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 상품리스트 상태 변경
                    $sql = "update order_product set ";
                    if($param["state_type"] == "refund"){
                        $sql .= "refund_complete_regdate = now() , ";
                        $sql .= "state = 8 ";
                    }else if($param["state_type"] == "return"){
                        $sql .= "return_complete_regdate = now() , ";
                        $sql .= "state = 15 ";
                    }
                    for($i =0 ;$i<count($target); $i++){
                        if(count($target) == 1){
                            $sql .= "where idx = ".$target[$i]." ";
                        }else{
                            if($i == 0){
                                $sql .= "where idx in ( ".$target[$i]." , ";
                            }else if($i == count($target) -1 ){
                                $sql .= " ".$target[$i]." ) ";
                            }else{
                                $sql .= " ".$target[$i]." , ";
                            }
                        }
                    }
                    
                    $result = $this->conn->db_update($sql);
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        //주문서 상세내용 조횐
                        $sql = "select * from purchase_order ";
                        $sql .= "where idx = ".$param["purchase_order_idx"]." ";

                        $result = $this->conn->db_select($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                        }else{
                            $return_give_point = $param["return_give_point"];
                            $return_use_point = $param["return_use_point"];
                            // 해당 주문서 데이터
                            $order_detail = $result["value"][0];

                            if($order_detail["user_type"] == 0 && $return_use_point != 0){
                                // 비회원 주문인경우 - 사용 적립금복구가 불가능
                                $this->result["result"] = 0;
                                $this->result["error_code"] = 7081;
                                $this->result["message"] = "비회원 주문인 경우 사용적립금 복구가 불가능합니다.";
                                echo $this->jsonEncode($this->result);
                                return;
                            }

                            if($order_detail["user_type"] == 0 && $return_give_point != 0){
                                $this->result["result"] = 0;
                                $this->result["error_code"] = 7082;
                                $this->result["message"] = "비회원 주문인 경우 적립금반환이 불가능합니다.";
                                $this->conn->rollback();
                                echo $this->jsonEncode($this->result);
                                return;
                            }


                            if($order_detail["user_type"] != 0){
                                //회원 주문이면

                                // 회원인경우 -> 포인트 반납 후 환불 로직
                                
                                $year_after_date = date("Y-m-d H:i:s", strtotime("+1 year", time()));
                                $year_ago_date = date("Y-m-d H:i:s", strtotime("-s1 year", time()));

                                if($return_use_point != 0){
                                    // 사용 포인트 반환금액이 있을경우 사용자에게 포인트 적립
                                    $sql = "insert into point(user_idx, point, state, remnant_point, regdate, expire_date, point_title) values(";
                                    $sql .= $order_detail["user_idx"] . ",";
                                    $sql .= $return_use_point . ", 1 , 0 , ";
                                    $sql .= "now(), ". $this->null_check($year_after_date) . " , ";
                                    $sql .= "'사용적립금 반환'";
                                    $sql .= ")";
                                    $result = $this->conn->db_insert($sql);
                                    if($result["result"] == 0){
                                        $this->result = $result;
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }
                                }    
                                if($return_give_point != 0){
                                    // 결제후 적립포인트 반환 사용자 포인트에서 뺏아옴
                                    // 포인트 테이블 조회 1년전부터
                                    $sql = "select idx, user_idx, state, remnant_point from point ";
                                    $sql .= "where user_idx = ".$order_detail["user_idx"]." ";
                                    $sql .= "and (regdate > ".$this->null_check($year_ago_date)." or expire_date = '0000-00-00 00:00:00') ";
                                    $sql .= "and state = 1 ";
                                    $sql .= "order by regdate asc ";

                                    $point_result = $this->conn->db_select($sql);
                                    if($point_result["result"] == 0){
                                        $this->result = $point_result;
                                        echo $this->jsonEncode($this->result);
                                        return;
                                    }else{
                                        $point_list = $point_result["value"];
                                        $cur_return_give_point = $return_give_point;

                                        for($i =0; $i<count($point_list); $i++){
                                            // 현재 데이터 잔여포인트
                                            $remnant_point = $point_list[$i]["remnant_point"];
                                            if((int)$remnant_point >= (int)$cur_return_give_point){
                                                // 디비상 남은 포인트가 현재 차감 포인트 보다 적으면 현재 사용할 포인트를 차감
                                                $sql = "update point set ";
                                                $sql .= "remnant_point = remnant_point - ".$cur_return_give_point.", ";
                                                $sql .= "return_point = return_point + ".$cur_return_give_point." ";
                                                if((int)$remnant_point == (int)$cur_return_give_point){
                                                    $sql .= ", state = 0 ";
                                                }
                                                $sql .= "where idx = ".$point_list[$i]["idx"]." ";
                                                
                                                $point_update_result = $this->conn->db_update($sql);
                                                if($point_update_result["result"] == 0){
                                                    $this->result = $point_update_result;
                                                    echo $this->jsonEncode($this->result);
                                                    return;
                                                }else{
                                                    break;
                                                }
                                            }
                                            else if((int)$remnant_point < (int)$cur_return_give_point){
                                                // 디비상 남은 포인트가 현재 차감 포인트가 적으면 디비상 남은 포인트를 차감
                                                $cur_return_give_point = (int)$cur_return_give_point - (int)$remnant_point;

                                                $sql = "update point set ";
                                                $sql .= "remnant_point = remnant_point - ".$remnant_point.", ";
                                                $sql .= "return_point = return_point + ".$remnant_point.", ";
                                                $sql .= "state = 0 ";
                                                $sql .= "where idx = ".$point_list[$i]["idx"]." ";

                                                $point_update_result = $this->conn->db_update($sql);
                                                if($point_update_result["result"] == 0){
                                                    $this->result = $point_update_result;
                                                    echo $this->jsonEncode($this->result);
                                                    return;
                                                }
                                            }
                                        }

                                        // 유저 적립금 기록에 남김
                                        $sql = "insert into point_user_history (kind, point, user_idx, use_content, order_number, regdate) values (";
                                        $sql .= "3, ";
                                        $sql .= $return_give_point . ", ";
                                        $sql .= $order_detail["user_idx"] . ", ";
                                        $sql .= " '환불로인한 적립금 회수' , ";
                                        $sql .= $this->null_check($order_detail["order_number"]) . ", now() ";
                                        $sql .= ")";

                                        $result = $this->conn->db_insert($sql);
                                        if($result["result"] == 0){
                                            $this->result = $result;
                                            echo $this->jsonEncode($this->result);
                                            return;
                                        }
                                    }
                                }
                            }

                            // 재고 되돌리는 것 -> 이전에 취소, 환불, 반품, 교환된 상품은 재고 복구되지않아야함
                            $sql = "select * from order_product ";
                            for($i =0 ;$i<count($target); $i++){
                                if(count($target) == 1){
                                    $sql .= "where idx = ".$target[$i]." ";
                                }else{
                                    if($i == 0){
                                        $sql .= "where idx in ( ".$target[$i]." , ";
                                    }else if($i == count($target) -1 ){
                                        $sql .= " ".$target[$i]." ) ";
                                    }else{
                                        $sql .= " ".$target[$i]." , ";
                                    }
                                }
                            }
                            $sql .= "and stock_return = 0 ";

                            // 재고가 복구되지않은 상품들 리턴
                            $order_product_result = $this->conn->db_select($sql);
                            if($order_product_result["result"] == 0){
                                $this->result = $order_product_result;
                                echo $this->jsonEncode($this->result);
                                return;
                            }else{
                                // 재고가 복귀되지않은 상품 리스트
                                $order_product_list = $order_product_result["value"];

                                // 재고 되돌리기
                                $stock_result = $this->stock_return($order_product_list);
                                if($stock_result != 1){
                                    $this->result = $stock_result;
                                    echo $this->jsonEncode($this->result);
                                    return;
                                }
                            }

                            // 환불할 금액이 없으면 바로 환불하지 않고 바로 프로세스 끝내기
                            if($param["refund_price"] == 0){
                                $this->result = $result;
                                $this->conn->commit();
                            }else{
                                // 지불 방식 account이면 무통장입금
                                $pay_type = $order_detail["pay_type"];
                                $possible_refund_price = $param["possible_refund_price"];

                                if((int)$param["refund_price"] > (int)$possible_refund_price){
                                    $this->result["result"] = 0;
                                    $this->result["error_code"] = 7084;
                                    $this->result["message"] = "환불가능한 금액보다 환불할 금액이 큽니다.";
                                    $this->conn->rollback();
                                }else{

                                    if($pay_type == "account"){
                                        $this->result = $result;
                                        $this->conn->commit();
                                    }else{
                                        if($param["state_type"] == "refund"){
                                            $reason = "관리자가 환불";
                                        }else if($param["state_type"] == "return"){
                                            $reason = "관리자가 반품";
                                        }
                                        if($param["reason"] != ""){
                                            $reason = $param["reason"];
                                        }
                                        $amount = $param["refund_price"];
    
                                        // 아임포트 환불로직
                                        $import = new PaymentModel();
                                        $result = $import->payment_part_refund($order_detail["imp_uid"], $order_detail["order_number"], $reason, $amount);
                                        if($result["code"] == 0){
                                            $this->result["result"] = 1;
                                            $this->conn->commit();
                                        }else{
                                            $this->result["result"] = 0;
                                            $this->result["error_codes"] = "7020";
                                            $this->result["message"] = "아임포트 환불실패";
                                            $this->conn->rollback();
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 송장번호 입력후 배송중상태로 변경
        // 파라미터 : 
        // 만든이: 최진혁
        *********************************************************************/
        function add_invoice_number(){
            $param = $this->param;
            if($this->value_check(array("invoice_number", "courier_name", "target"))){
                $target = json_decode($param["target"] , true);

                $sql = "select * from purchase_order ";
                $sql .= "where idx = ".$param["purchase_order_idx"]." ";


                $this->conn->s_transaction();
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $cur_order_state = $result["value"][0]["state"];
                    $order_detail = $result["value"][0];


                    $sql = "select t1.price, t1.is_discount, t1.discount_price, t1.option_1_price, t1.option_2_price, t1.option_3_price, t1.option_4_price, sum(t1.cnt) as cnt, ";
                    $sql .= "t2.product_name, t2.option_1_name, t2.option_2_name, t2.option_3_name, t2.option_4_name, t2.order_product_thumnail, t1.state ";
                    $sql .= "from order_product as t1 ";
                    $sql .= "left join order_product_name as t2 ";
                    $sql .= "on t1.idx = t2.order_product_idx ";
                    $sql .= "where t1.purchase_order_idx = ".$param["purchase_order_idx"]." ";
                    $sql .= "and t2.lang_idx = 1 ";
                    for($i = 0; $i<count($target); $i++){
                        if(count($target) == 1){
                            $sql .= "and t1.idx = ".$target[$i]." ";
                        }else{
                            if($i == 0){
                                $sql .= "and t1.idx in ( ".$target[$i]." , ";
                            }else if($i == count($target) - 1){
                                $sql .= "  ".$target[$i]." ) ";
                            }else{
                                $sql .= "  ".$target[$i]." , ";
                            }
                        }
                    }$sql .= "group by t1.product_idx, t1.option_1_idx, t1.option_2_idx, t1.option_3_idx, t1.option_4_idx ";

                    $product_result = $this->conn->db_select($sql);
                    if($product_result["result"] == 0){
                        $this->result =$result;
                    }else{
                        $product_list = $product_result["value"];

                        
                        $sql = "update order_product set ";
                        $sql .= "courier_name = ".$this->null_check($param["courier_name"])." , ";
                        $sql .= "invoice_number = ".$this->null_check($param["invoice_number"])." , ";
                        $sql .= "state = 3 , ";
                        $sql .= "on_delivery_regdate = now() ";
                        for($i = 0; $i<count($target); $i++){
                            if(count($target) == 1){
                                $sql .= "where idx = ".$target[$i]." ";
                            }else{
                                if($i == 0){
                                    $sql .= "where idx in ( ".$target[$i]." , ";
                                }else if($i == count($target) - 1){
                                    $sql .= " ".$target[$i]." ) ";
                                }else{
                                    $sql .= " ".$target[$i]." , ";
                                }
                            }
                        }

                        $result = $this->conn->db_update($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                        }else{
                            if($cur_order_state < 3){
                                $sql = "update purchase_order set ";
                                $sql .= "state = 3 , ";
                                $sql .= "on_delivery_regdate = now() ";
                                $sql .= "where idx = ".$param["purchase_order_idx"]."";

                                $result = $this->conn->db_update($sql);
                                if($result["result"] == 0){
                                    $this->result = $result;
                                }else{
                                   

                                    // 관리자가 정한 문자 이메일 수신 여부 설정
                                    $sql = "select * from admin_user_sms_email_setting ";
                                    $sms_email_result = $this->conn->db_select($sql);
                                    if($sms_email_result["result"] == 0){
                                        $this->result = $sms_email_result;
                                    }else{
                                        // 문자 이메일 세팅
                                        $sms_email_setting = $sms_email_result["value"][0];
                                        if($order_detail["user_type"] == 1){
                                            // 유저 타입이 회원일경우  sms_email_setting에 유저 이메일 문자 셋팅 추가
                                            $sql = "select email_agree, sms_agree from user ";
                                            $sql .= "where idx = ".$order_detail["user_idx"]." ";

                                            $user_result = $this->conn->db_select($sql);
                                            if($result["result"] == 0){
                                                $this->result = $user_result;
                                                echo $this->jsonEncode($this->result);
                                                return;
                                            }
                                            $user_detail = $user_result["value"][0];    
                                            $sms_email_setting["user_email_agree"] = $user_detail["email_agree"];
                                            $sms_email_setting["user_sms_agree"] = $user_detail["sms_agree"];
                                        }
                                        $this->result = $result;

                                        //송장번호 수정시 문자 , 이메일을 보내지 않도록 하기 위해 추가 ( 조경민 )
                                        if($param["flag"] == "register"){
                                            if($sms_email_setting["s_shipment"] == 1){
                                                //문자 보내는 코드 작성
                                                if($order_detail["user_type"] == 1){
                                                    if($sms_email_setting["user_sms_agree"] == 1){
                                                        // 유저에게 문자 발신
                                                        $this->BillingSMS->send_tracking(array(
                                                            "send_type" => "kakao",
                                                            "type" => "order_shipment",
                                                            "order_number" => $order_detail["order_number"],
                                                            "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                        ), null);
                                                    }
                                                }else{
                                                    // 유저에게 문자 발신
                                                    $this->BillingSMS->send_tracking(array(
                                                        "send_type" => "kakao",
                                                        "type" => "order_shipment",
                                                        "order_number" => $order_detail["order_number"],
                                                        "receiver_list" => json_encode(array($order_detail["orderer_phone_number"])),
                                                    ), null);
                                                }
                                            }
    
    
                                            if($order_detail["user_type"] == 1){
                                                if($sms_email_setting["user_email_agree"] ==1){
                                                    // 사용자에게 이메일 발신
                                                    $this->MailForm->send_email(array(
                                                        "type" => "u_shipment",
                                                        "to_list" => array($order_detail["orderer_email"]),
                                                        "order" => $order_detail,
                                                        "product" => $product_list,
                                                        "regdate" => date("Y-m-d H:i:s")
                                                    ));
                                                }
                                            }else{
                                                // 사용자에게 이메일 발신
                                                $this->MailForm->send_email(array(
                                                    "type" => "u_shipment",
                                                    "to_list" => array($order_detail["orderer_email"]),
                                                    "order" => $order_detail,
                                                    "product" => $product_list,
                                                    "regdate" => date("Y-m-d H:i:s")
                                                ));
                                            }
                                        }
                                        
                                        $this->conn->commit();
                                    }
                                }
                            }else{
                                $this->result = $result;
                                $this->conn->commit();
                            }
                        }
                    }

                }
            }
            echo $this->jsonEncode($this->result);
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

        /********************************************************************* 
        // 함 수 : 해당 송장번호의 상품리스트
        // 파라미터 : 
        // 만든이: 최진혁
        *********************************************************************/
        function invoice_list(){
            $param = $this->param;
            if($this->value_check(array("invoice_number", "purchase_order_idx"))){
                $sql = "select t1.price, t1.is_discount, t1.discount_price, t1.option_1_price, t1.option_2_price, t1.option_3_price, t1.option_4_price, ";
                $sql .= "t2.product_name, t2.option_1_name, t2.option_2_name, t2.option_3_name, t2.option_4_name, t1.regdate, t2.order_product_thumnail as product_thumnail, order_product_idx ";
                $sql .= "from order_product as t1 ";
                $sql .= "left join order_product_name as t2 ";
                $sql .= "on t1.idx = t2.order_product_idx ";
                $sql .= "where t1.purchase_order_idx = ".$param["purchase_order_idx"]." ";
                $sql .= "and t1.invoice_number = ".$this->null_check($param["invoice_number"])." ";
                $sql .= "and t2.lang_idx = 1 ";
                
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
            
        }
    }
?>
