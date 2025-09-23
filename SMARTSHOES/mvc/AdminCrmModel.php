<?php
    class AdminCrmModel extends gf{
        private $param;
        private $dir;
        private $conn;

        function __construct($array){
            $this->array = $array;
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->file_manager = $array["file_manager"];
            $this->file_path = $array["file_path"]->get_path_php();
            $this->file_link = $array["file_path"]->get_link_php();
            
            //서브 path
            $this->file_path = array_merge($this->file_path,$array["sub_file_path"]->get_path_php());
            $this->file_link = array_merge($this->file_link,$array["sub_file_path"]->get_link_php());

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
                $this->result["message"] = $check_result["value"]." 가 없습니다.";
                return false;
            }
        }


        /********************************************************************* 
        // 함 수 : 회원(휴먼) 목록 조회
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_dormant_user_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);


                $like_sql = ""; //검색을 위한 like절 
                
                if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                    $word = $move_list["word"];
                    $search_kind = $move_list["search_kind"];

                    if($search_kind == "0"){ //이름검색
                        $like_sql = "and t1.name like '%".$word."%'";
                    }else if($search_kind == "1"){ //아이디 검색
                        $like_sql = "and t1.id like '%".$word."%'";
                    }else if($search_kind == "2"){ //등급 검색
                        $like_sql = "and t2.name like '%".$word."%'";
                    }else if($search_kind == "3"){ //이메일 검색
                        $like_sql = "and t1.email like '%".$word."%'";
                    }else if($search_kind == "4"){ //주소 검색
                        $like_sql = "and t1.address like '%".$word."%' or t1.detail_address like '%".$word."%'";
                    }else if($search_kind == "5"){ //연락처 검색
                        $like_sql = "and t1.phone like '%".$word."%'";
                    }
                }
                
                // sql문 최진혁 수정
                $sql = "select t1.*, t3.name as grade_name, (select count(idx) from purchase_order where user_idx = t1.idx and state = 4) as order_count, ";
                $sql .= "IF((select sum(total_price) from purchase_order where user_idx = t1.idx and state = 4), (select sum(total_price) from purchase_order where user_idx = t1.idx and state = 4), 0)  as total_price, ";
                $sql .= "IF((select sum(refund_price) from purchase_order where user_idx = t1.idx and state = 4), (select sum(refund_price) from purchase_order where user_idx = t1.idx and state = 4), 0)  as refund_price, ";
                $sql .= "t1.regdate ";
                $sql .= "from user as t1 ";
                $sql .= "left join user_grade as t2 ";
                $sql .= "on t1.user_grade_idx = t2.idx ";
                $sql .= "left join user_grade_name as t3 ";
                $sql .= "on t2.idx = t3.user_grade_idx ";
                $sql .= "where state = 0 ".$like_sql." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;
                // echo $sql;
                $this->result = $this->conn->db_select($sql);

                $sql = "select count(t1.idx) as total_count ";
                $sql .= "from user as t1 ";
                $sql .= "left join user_grade as t2 ";
                $sql .= "on t1.user_grade_idx = t2.idx ";
                $sql .= "left join user_grade_name as t3 ";
                $sql .= "on t2.idx = t3.user_grade_idx ";
                $sql .= "where state = 0 ".$like_sql." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by t1.regdate desc";
                $result = $this->conn->db_select($sql);
                if($result["result"]=="1"){ //전체 조회성공했으면
                    $this->result["total_count"]=$result["value"][0]["total_count"];
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 회원(탈퇴) 목록 조회
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_leave_user_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);
                $like_sql = ""; //검색을 위한 like절 
                
                if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                    $word = $move_list["word"];
                    $search_kind = $move_list["search_kind"];

                    if($search_kind == "0"){ //이름검색
                        $like_sql = "and t1.name like '%".$word."%'";
                    }else if($search_kind == "1"){ //아이디 검색
                        $like_sql = "and t1.id like '%".$word."%'";
                    }else if($search_kind == "2"){ //등급 검색
                        $like_sql = "and t2.name like '%".$word."%'";
                    }else if($search_kind == "3"){ //이메일 검색
                        $like_sql = "and t1.email like '%".$word."%'";
                    }else if($search_kind == "4"){ //주소 검색
                        $like_sql = "and t1.address like '%".$word."%' or t1.detail_address like '%".$word."%'";
                    }else if($search_kind == "5"){ //연락처 검색
                        $like_sql = "and t1.phone like '%".$word."%'";
                    }
                }
                // sql문 최진혁 수정
                $sql = "select t1.*, t3.name as grade_name, (select count(idx) from purchase_order where user_idx = t1.idx and state = 4) as order_count, ";
                $sql .= "IF((select sum(total_price) from purchase_order where user_idx = t1.idx and state = 4), (select sum(total_price) from purchase_order where user_idx = t1.idx and state = 4), 0)  as total_price, ";
                $sql .= "IF((select sum(refund_price) from purchase_order where user_idx = t1.idx and state = 4), (select sum(refund_price) from purchase_order where user_idx = t1.idx and state = 4), 0)  as refund_price, ";
                $sql .= "t1.regdate ";
                $sql .= "from user as t1 ";
                $sql .= "left join user_grade as t2 ";
                $sql .= "on t1.user_grade_idx = t2.idx ";
                $sql .= "left join user_grade_name as t3 ";
                $sql .= "on t2.idx = t3.user_grade_idx ";
                $sql .= "where state = 2 ".$like_sql." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $this->result = $this->conn->db_select($sql);

                $sql = "select count(t1.idx) as total_count ";
                $sql .= "from user as t1 ";
                $sql .= "left join user_grade as t2 ";
                $sql .= "on t1.user_grade_idx = t2.idx ";
                $sql .= "left join user_grade_name as t3 ";
                $sql .= "on t2.idx = t3.user_grade_idx ";
                $sql .= "where state = 2 ".$like_sql." ";
                $sql .= "and t3.lang_idx = 1 ";
                $sql .= "order by t1.regdate desc";
                $result = $this->conn->db_select($sql);
                if($result["result"]=="1"){ //전체 조회성공했으면
                    $this->result["total_count"]=$result["value"][0]["total_count"];
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 1대1문의 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        // tab = > all => 전체 , second => 답변완료, third => 미답변
        function user_1to1inquiry_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, t2.name, t2.email from 1to1inquiry as t1 ";
                $sql .= " left join user t2 on t1.user_idx = t2.idx";
                $sql .= " where t1.idx is not null ";
                // 탭별 where 절
                if(isset($move_list["tab"])){
                    if($move_list["tab"] != ""){
                        if($move_list["tab"] == "second"){
                            $sql .= "and answer is not null ";
                        }else if($move_list["tab"] == "third"){
                            $sql .= "and answer is null ";
                        }
                    }
                }
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "title"){
                            $sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "name"){
                            $sql .= " and t2.name like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "id"){
                            $sql .= " and t2.email like '%".$move_list["keyword"]."%' ";
                        }
                    }
                }
                // 분류 검색 부분
                if(isset($move_list["select_kind"])){
                    if($move_list["select_kind"] != "0"){
                        $sql .= " and t1.kind = ".$move_list["select_kind"]." ";
                    }
                }
                // 검색기간
                if(isset($move_list["start_date"])){
                    if($move_list["start_date"] != ""){
                        $sql .= " and t1.regdate >= ".$this->null_check($move_list["start_date"] . " 00:00:00")." ";
                        $sql .= " and t1.regdate <= ".$this->null_check($move_list["end_date"] . " 23:59:59")." ";
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

                $result = $this->conn->db_select($sql);

                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)


                    $inner_sql = " left join user t2 on t1.user_idx = t2.idx";
                    $inner_sql .= " where t1.idx is not null ";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "title"){
                                $inner_sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "name"){
                                $inner_sql .= " and t2.name like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "id"){
                                $inner_sql .= " and t2.email like '%".$move_list["keyword"]."%' ";
                            }
                        }
                    }
                    // 분류 검색 부분
                    if(isset($move_list["select_kind"])){
                        if($move_list["select_kind"] != "0"){
                            $inner_sql .= " and t1.kind = ".$move_list["select_kind"]." ";
                        }
                    }
                    // 검색기간
                    if(isset($move_list["start_date"])){
                        if($move_list["start_date"] != ""){
                            $inner_sql .= " and t1.regdate >= ".$this->null_check($move_list["start_date"] . " 00:00:00")." ";
                            $inner_sql .= " and t1.regdate <= ".$this->null_check($move_list["end_date"] . " 23:59:59")." ";
                        }
                    }

                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from 1to1inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as answer from 1to1inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and answer is not null ";
                    $sql .= ") R2 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as unanswer from 1to1inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and answer is null ";
                    $sql .= ") R3 ";
                    

                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        // 카운팅
                        // total_count =>페이징 처리를 위한 데이터
                        // total => 전체 탭 카운트
                        // answer => 답변완료 탭 카운트
                        // unanswer => 미답변 탭 카운트
                        if(isset($move_list["tab"])){
                            if($move_list["tab"] != ""){
                                if($move_list["tab"] == "second"){
                                    $this->result["total_count"] = $total_result["value"][0]["answer"];
                                }else if($move_list["tab"] == "third"){
                                    $this->result["total_count"] = $total_result["value"][0]["unanswer"];
                                }else{
                                    $this->result["total_count"] = $total_result["value"][0]["total"];
                                }
                            }else{
                                $this->result["total_count"] = $total_result["value"][0]["total"];
                            }
                        }else{
                            $this->result["total_count"] = $total_result["value"][0]["total"];
                        }
                        $this->result["total"] = $total_result["value"][0]["total"];
                        $this->result["answer_count"] = $total_result["value"][0]["answer"];
                        $this->result["unanswer_count"] = $total_result["value"][0]["unanswer"];

                    }
                    
                }
            }
            echo $this->jsonEncode($this->result);
        }
        
        /********************************************************************* 
        // 함 수 : 제품qna 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        // tab = > all => 전체 , second => 답변완료, third => 미답변
        function user_product_qna_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, t2.name as user_name, t2.email, ";
                $sql .= "(select product_name from product_name where product_idx = t1.product_idx and lang_idx = 1) as product_name ";
                $sql .= "from product_qna as t1 ";
                $sql .= "left join user as t2 ";
                $sql .= "on t1.user_idx = t2.idx ";
                $sql .= "where t1.idx is not null ";

                // $sql .= "where t3.lang_idx = 1";

                // 탭별 where 절
                if(isset($move_list["tab"])){
                    if($move_list["tab"] != ""){
                        if($move_list["tab"] == "second"){
                            $sql .= "and answer is not null ";
                        }else if($move_list["tab"] == "third"){
                            $sql .= "and answer is null ";
                        }
                    }
                }
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "title"){
                            $sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "name"){
                            $sql .= " and t2.name like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "id"){
                            $sql .= " and t2.email like '%".$move_list["keyword"]."%' ";
                        }
                    }
                }

                // 검색기간
                if(isset($move_list["start_date"])){
                    if($move_list["start_date"] != ""){
                        $sql .= " and t1.regdate >= ".$this->null_check($move_list["start_date"] . " 00:00:00")." ";
                        $sql .= " and t1.regdate <= ".$this->null_check($move_list["end_date"] . " 23:59:59")." ";
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;

            


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    $inner_sql = "left join user as t2 ";
                    $inner_sql .= "on t1.user_idx = t2.idx ";
                    $inner_sql .= "where t1.idx is not null ";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "title"){
                                $inner_sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "name"){
                                $inner_sql .= " and t2.name like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "id"){
                                $inner_sql .= " and t2.email like '%".$move_list["keyword"]."%' ";
                            }
                        }
                    }
                    // 검색기간
                    if(isset($move_list["start_date"])){
                        if($move_list["start_date"] != ""){
                            $inner_sql .= " and t1.regdate >= ".$this->null_check($move_list["start_date"] . " 00:00:00")." ";
                            $inner_sql .= " and t1.regdate <= ".$this->null_check($move_list["end_date"] . " 23:59:59")." ";
                        }
                    }
                    
                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from product_qna as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as answer from product_qna as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and answer is not null ";
                    $sql .= ") R2 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as unanswer from product_qna as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and answer is null ";
                    $sql .= ") R3 ";
                    
                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        // 카운팅
                        // total_count =>페이징 처리를 위한 데이터
                        // total => 전체 탭 카운트
                        // answer => 답변완료 탭 카운트
                        // unanswer => 미답변 탭 카운트
                        if(isset($move_list["tab"])){
                            if($move_list["tab"] != ""){
                                if($move_list["tab"] == "second"){
                                    $this->result["total_count"] = $total_result["value"][0]["answer"];
                                }else if($move_list["tab"] == "third"){
                                    $this->result["total_count"] = $total_result["value"][0]["unanswer"];
                                }else{
                                    $this->result["total_count"] = $total_result["value"][0]["total"];
                                }
                            }else{
                                $this->result["total_count"] = $total_result["value"][0]["total"];
                            }
                        }else{
                            $this->result["total_count"] = $total_result["value"][0]["total"];
                        }
                        $this->result["total"] = $total_result["value"][0]["total"];
                        $this->result["answer_count"] = $total_result["value"][0]["answer"];
                        $this->result["unanswer_count"] = $total_result["value"][0]["unanswer"];

                    }
                    
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : faq 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function faq_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, t2.title from faq as t1 ";
                $sql .= "left join faq_name as t2 ";
                $sql .= "on t1.idx = t2.faq_idx ";
                $sql .= "where t1.idx is not null ";
                $sql .= "and t2.lang_idx = 1 ";
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "title"){
                            $sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                        }
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by t1.regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    $inner_sql = "left join faq_name as t2 ";
                    $inner_sql .= "on t1.idx = t2.faq_idx ";
                    $inner_sql .= "where t1.idx is not null ";
                    $inner_sql .= "and t2.lang_idx = 1 ";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "title"){
                                $inner_sql .= " and t1.title like '%".$move_list["keyword"]."%' ";
                            }
                        }
                    }
                    // 검색기간
                    if(isset($move_list["start_date"])){
                        if($move_list["start_date"] != ""){
                            $inner_sql .= " and t1.regdate >= ".$this->null_check($move_list["start_date"] . " 00:00:00")." ";
                            $inner_sql .= " and t1.regdate <= ".$this->null_check($move_list["end_date"] . " 23:59:59")." ";
                        }
                    }
                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from faq as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1 ";
                    
                    $total_result = $this->conn->db_select($sql);
                    if($total_result["result"] == 0){
                        $this->result = $total_result;
                    }else{
                        $this->result = $result;
                        // 카운팅
                        $this->result["total_count"] = $total_result["value"][0]["total"];
                    }
                    
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /*************************************************************s******* 
        // 함 수 : faq 상세정보
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function faq_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql  = "select t1.idx, t2.* from faq as t1 ";
                $sql .= "left join faq_name as t2 ";
                $sql .= "on t1.idx = t2.faq_idx ";
                $sql .= "where t1.idx = ".$param["target"]." ";

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
        // 함 수 : faq 수정
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function faq_modify(){
            $param = $this->param;
            if($this->value_check(array("target","title", "content"))){
                // 제목
                $title = json_decode($param["title"] , true);
                // 내용
                $content = json_decode($param["content"], true);

                $sql = "select t2.content, t2.idx from faq as t1 ";
                $sql .= "left join faq_name as t2 ";
                $sql .= "on t1.idx = t2.faq_idx ";
                $sql .= "where t1.idx = ".$param["target"]." ";

                $this->conn->s_transaction();
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $origin_content = $result["value"];

                    $origin_array_img = array();
                    foreach($origin_content as $key => $value){
                        $origin_img = $this->file_manager->get_s3_image_array($value["content"], $this->file_link["faq_description_path"]);
                        $origin_array_img = array_merge($origin_array_img, $origin_img);
                    }
                    
                    // 에러나면 삭제할 description 이미지
                    $description_error_remove_img = array();
                    $now_img_array =array();
                    for($i = 0; $i<count($origin_content); $i++){
                        $description = $content[$i];
                        $description_result = $this->file_manager->convert_description($this->file_path["faq_description_path"],$this->file_link["faq_description_path"], $description); //이미지 저장 및 변환
                        $now_img =  $this->file_manager->get_s3_image_array($description_result["description"], $this->file_link["faq_description_path"]);
                        $now_img_array = array_merge($now_img_array, $now_img);
                        $description_error_remove_img = array_merge($description_error_remove_img, $description_result["error_file_array"]);

                        $sql = "update faq_name set ";
                        $sql .= "kind = ".$param["kind"]." , ";
                        $sql .= "title = ".$this->null_check($title[$i])." , ";
                        $sql .= "content = ".$this->null_check($description_result["description"])." ";
                        $sql .= "where idx = ".$origin_content[$i]["idx"]." ";
                        
                        
                        $this->conn->set_file_list($description_error_remove_img);
                        $this->conn->db_update($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                            return;
                        }
                    }

                    $sql = "update faq set ";
                    $sql .= "update_date = now() ";
                    $sql .= "where idx = ".$param["target"]." ";

                    $this->conn->set_file_list($description_error_remove_img);
                    $this->conn->db_update($sql);
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        // 업데이트 후 삭제할 이미지 파일 배열
                        $diff_array = array_diff($origin_array_img, $now_img_array);
                        $delete_file_arr = array();
                        foreach($diff_array as $key => $value){
                            $diff_file_path = $this->file_path["faq_description_path"].$value;
                            array_push($delete_file_arr, $diff_file_path);
                        }
                        $this->file_manager->delete_file($delete_file_arr);
                        $this->result = $result;
                        $this->conn->commit();
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : faq 등록
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function faq_register(){
            $param = $this->param;
            if($this->value_check(array("title", "content"))){
                // 제목
                $title = json_decode($param["title"] , true);
                // 내용
                $content = json_decode($param["content"], true);

                $sql = "insert into faq(regdate) values(";
                $sql .= "now() ";
                $sql .= ")";

                $this->conn->s_transaction();
                $result = $this->conn->db_insert($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 공지사항 idx
                    $faq_idx = $result["value"];
                    // description 삭제 이미지 배열(모두)
                    $description_remove_img = array();

                    $sql = "insert into faq_name(title, content, lang_idx, faq_idx, kind) values ";
                    for($i = 0; $i<count($title); $i++){
                        $description = $content[$i];
                        $description_result = $this->file_manager->convert_description($this->file_path["faq_description_path"],$this->file_link["faq_description_path"], $description); //이미지 저장 및 변환
                        $description_remove_img = array_merge($description_remove_img, $description_result["error_file_array"]);
                        $sql .= "(";
                        $sql .= $this->null_check($title[$i]) . ", ";
                        $sql .= $this->null_check($description_result["description"]) . ", ";
                        $sql .= ($i + 1) . ", ";
                        $sql .= $faq_idx . ", ";
                        $sql .= $param["kind"] . " ";
                        if($i != count($title) -1 ){
                            $sql .= "),";
                        }else{
                            $sql .= ")";
                        }
                    }
                    
                    $this->conn->set_file_list($description_remove_img);
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
        // 함 수 : faq 삭제
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function faq_remove(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $target = json_decode($param["target"], true);

                $sql = "select t2.content from faq as t1 ";
                $sql .= "left join faq_name as t2 ";
                $sql .= "on t1.idx = t2.faq_idx ";
                for($i = 0; $i<count($target); $i++){
                    if(count($target) == 1){
                        $sql .= "where t1.idx = ".$target[$i]." ";
                    }else{
                        if($i == 0){
                            $sql .= "where t1.idx in ( ".$target[$i] ." , ";
                        }else if($i == count($target) -1 ){
                            $sql .= " ".$target[$i] ." ) ";
                        }else{
                            $sql .= " ".$target[$i] ." , ";
                        }
                    }
                }

                $result = $this->conn->db_select($sql);
                
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $origin_content = $result["value"];
                    $origin_array_img = array();

                    foreach($origin_content as $key => $value){
                        $origin_img = $this->file_manager->get_s3_image_array($value["content"], $this->file_link["faq_description_path"]);
                        $origin_array_img = array_merge($origin_array_img, $origin_img);
                    }

                    if(count($target) > 0){
                        // faq 테이블 삭제
                        $sql = "delete from faq ";
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

                        $this->conn->s_transaction();
                        $result = $this->conn->db_delete($sql);
                        if($result["result"] == 0){
                            $this->result = $result;
                            $this->conn->rollback();
                        }else{
                            // faq_name 테이블 삭제
                            $sql = "delete from faq_name ";
                            for($i = 0; $i<count($target); $i++){
                                if(count($target) == 1){
                                    $sql .= "where faq_idx = ".$target[$i]." ";
                                }else{
                                    if($i == 0){
                                        $sql .= "where faq_idx in ( ".$target[$i]." , ";
                                    }else if($i == count($target) - 1){
                                        $sql .= " ".$target[$i]." ) ";
                                    }else{
                                        $sql .= " ".$target[$i]." , ";
                                    }
                                }
                            }

                            $result = $this->conn->db_delete($sql);
                            if($result["result"] == 0){
                                $this->result = $result;
                                $this->conn->rollback();
                            }else{
                                $delete_file_arr = array();
                                foreach($origin_array_img as $key => $value){
                                    $file_path = $this->file_path["faq_description_path"].$value;
                                    array_push($delete_file_arr,$file_path);
                                }
                                //이미지 삭제 코드
                                $this->file_manager->delete_file($delete_file_arr);
                                $this->result = $result;
                                $this->conn->commit();
                            }
                        }
                    }else{
                        $this->result["result"] = 0;
                        $this->result["error_code"] = 200;
                        $this->result["message"] = "선택된 faq가 없습니다.";
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 엑셀 다운로드
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function excel_download(){
            $param = $this->param;
            // 엑셀 클래스
            $objPHPExcel = new PHPExcel();
            // 엑셀 컬럼에 사용할 알파벳(26개)
            $alphabet = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

            $move_list = json_decode($param["move_list"], true);

            $like_sql = ""; //검색을 위한 like절
            
            if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                $word = $move_list["word"];
                $search_kind = $move_list["search_kind"];

                if($search_kind == "0"){ //이름검색
                    $like_sql = "and t1.name like '%".$word."%'";
                }else if($search_kind == "1"){ //아이디 검색
                    $like_sql = "and t1.id like '%".$word."%'";
                }
                // 최진혁 수정 -> 데이터 베이스 수정으로 이메일을 아이디로 대체함
                // else if($search_kind == "2"){ //등급 검색
                //     $like_sql = "and t2.name like '%".$word."%'";
                // }
                else if($search_kind == "3"){ //이메일 검색
                    $like_sql = "and t1.email like '%".$word."%'";
                }else if($search_kind == "4"){ //주소 검색
                    $like_sql = "and t1.address like '%".$word."%' or t1.detail_address like '%".$word."%'";
                }else if($search_kind == "5"){ //연락처 검색
                    $like_sql = "and t1.phone like '%".$word."%'";
                }
            }
            
            // tb => 테이블명, column_arr => 생성시킬 엑셀파일을 컬럼
            $sql = "select t1.name, t1.email, t2.name as grade_name, t1.phone, t1.login_count, (select count(idx) from purchase_order where user_idx = t1.idx) as order_count, ";
            $sql .= " IF((select sum(total_price) from purchase_order where user_idx = t1.idx), (select sum(total_price) from purchase_order where user_idx = t1.idx), 0)  as total_price, ";
            $sql .= " IF((select sum(refund_price) from purchase_order where user_idx = t1.idx), (select sum(refund_price) from purchase_order where user_idx = t1.idx), 0)  as refund_price, ";
            $sql .= " t1.regdate ";
            $sql .= "from user as t1 ";
            $sql .= "left join user_grade_name as t2 ";
            $sql .= "on t1.user_grade_idx = t2.user_grade_idx ";
            $sql .= "where t1.state = ".$param["state"]." ";
            $sql .= $like_sql;
            $sql .= "and t2.lang_idx = 1 ";

            // create_sql("insert", ["name","email"], "user", "idx=1 and idx=2");

            $result =$this->conn->db_select($sql);
            if($result["result"] == 0){
                $this->result = $result;
            }else{
                // 검색된 값
                $select_list = $result["value"];
                // Excel 문서 속성을 지정해주는 부분
                $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                            ->setLastModifiedBy("Maarten Balliauw")
                                            ->setTitle("Office 2007 XLSX Test Document")
                                            ->setSubject("Office 2007 XLSX Test Document")
                                            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                            ->setKeywords("office 2007 openxml php")
                                            ->setCategory("Test result file");

                $column_arr = array("번호","이름","이메일","등급","연락처","접속", "주문", "구매금액", "환불금액", "가입일");
                $key_arr = array("name","email","grade_name","phone","login_count","order_count","total_price","refund_price","regdate");

                // Excel파일의 각 셀의 타이틀 설정
                for($i = 0; $i<count($column_arr); $i++){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$i]."1", $column_arr[$i]);
                }

                // for문을 이용해 DB에서 가져온 데이터를 순차적으로 입력한다.
                // 변수 i값은 2부터 시작하도록 해야한다.
                for($i = 0; $i<count($select_list); $i++){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".($i+2), ($i+1));
                    for($j = 0; $j<count($key_arr); $j++){
                        if($key_arr[$j] == "total_price" || $key_arr[$j] == "refund_price"){
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$j+1].($i+2), number_format($select_list[$i][$key_arr[$j]]));
                        }else{
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$j+1].($i+2), $select_list[$i][$key_arr[$j]]);
                        }
                    }
                }

                // 파일의 저장 형식이 utf-8일 경우 한글 파일 이름은 깨지므로 euc-kr로 변환해서 준다
                $file_name = iconv("UTF-8", "EUC-KR", $param["file_name"]);
                
                // 유저 브라우저로 리다이렉트해준다.
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$file_name.'.xls"');
                header('Cache-Control: max-age=0');

                // Do your stuff here
                $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                // This line will force the file to download
                $writer->save('php://output');

            }
        }

        
        /********************************************************************* 
        // 함 수 : 1to1 질문 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function get_1to1_detail(){
            $param = $this->param;
            $sql = "select * from 1to1inquiry ";
            $sql = $sql." where idx = ".$param["inquiry_idx"];

            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 1to1 질문 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function save_1to1_answer(){
            $param = $this->param;
            $sql = "update 1to1inquiry set answer =".$this->null_check($param["answer"]);
            $sql = $sql.", answer_date = now() ";
            $sql = $sql."where idx = ".$param["inquiry_idx"];

            $this->result = $this->conn->db_update($sql);
            echo $this->jsonEncode($this->result);
        }
    }
?>
