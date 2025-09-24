<?php
    class AdminMenu4Model extends gf{
        private $param;
        private $dir;
        private $conn;
        private $file_manager;
        private $array;
        private $file_path;
        private $project_name;

        function __construct($array){
            $this->array = $array;
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
        // 함 수 : 이벤트 파일 다운로드함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function file_download(){
            $param = $this->param;
            // print_r($param);
            // exit;
            $orign = $param;
            $column = array('realname', 'url');
            $check_parameter = $this->column_check($orign, $column);
            if ($check_parameter) {
                include_once($this->down_path . "Download.php");
                if (new Download($param["download_type"], $param["url"], $param["realname"], $this->down_path)) {
                    // $this->result["result"] = "0";
                    // $this->result["error_code"] = "1";
                    // $this->result["message"] = "DB오류 관리자에게 문의해주세요";
                    // echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                }
            } else {
                $this->result["result"] = "0";
                $this->result["error_code"] = "1";
                $this->result["message"] = "DB오류 관리자에게 문의해주세요";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }


        /********************************************************************* 
         // 함 수 : 제품문의 상세내용
         // 설 명 : 
         // 만든이: 김경훈
         *********************************************************************/
        function request_product_inquiry_detail() {
            $param = $this->param;
            $sql = "select * from product_inquiry";
            $sql .= " where idx = " .$this->null_check($param["target"]);

            $this->result = $this->conn->db_select($sql);

            $sql = "select * from product_inquiry_file";
            $sql .= " where product_inquiry_idx = " .$this->null_check($param["target"]);
            $result = $this->conn->db_select($sql);
            $this->result["value"][0]["file"] = $result["value"];
            
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 제품문의 리스트
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function product_inquiry_list() {
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select * from product_inquiry ";
                $sql .= "where idx is not null ";
                // 탭별 where 절
                if(isset($move_list["tab"])){
                    if($move_list["tab"] != ""){
                        if($move_list["tab"] == "second"){
                            $sql .= "and type = 1 ";
                        }else if($move_list["tab"] == "third"){
                            $sql .= "and type = 0 ";
                        }
                    }
                }
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "title"){
                            $sql .= " and title like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "name"){
                            $sql .= " and name like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "email"){
                            $sql .= " and email like '%".$move_list["keyword"]."%' ";
                        }
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    $inner_sql = "where idx is not null ";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "title"){
                                $inner_sql .= " and title like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "name"){
                                $inner_sql .= " and name like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "email"){
                                $inner_sql .= " and email like '%".$move_list["keyword"]."%' ";
                            }
                        }
                    }

                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from product_inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as answer from product_inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and type = 1 ";
                    $sql .= ") R2 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as unanswer from product_inquiry as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and type = 0 ";
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
         // 함 수 : 제품문의 답변여부 수정
         // 설 명 : 
         // 만든이: 김경훈
         *********************************************************************/
        function request_product_inquiry_is_answer_change(){
            $param = $this->param;
            if($this->value_check(array("target", "is_answer"))){
                $sql = "UPDATE product_inquiry SET type=".$param["is_answer"]." where idx=".$param["target"];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        

        /********************************************************************* 
         // 함 수 : 기술지원 상세내용
         // 설 명 : 
         // 만든이: 김경훈
         *********************************************************************/
        function request_technique_aid_detail() {
            $param = $this->param;
            $sql = "select * from technique_aid";
            $sql .= " where idx = " .$this->null_check($param["target"]);

            $this->result = $this->conn->db_select($sql);

            $sql = "select * from technique_aid_file";
            $sql .= " where technique_aid_idx = " .$this->null_check($param["target"]);
            $result = $this->conn->db_select($sql);
            $this->result["value"][0]["file"] = $result["value"];
            
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 기술지원 리스트
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function technique_aid_list() {
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select * from technique_aid ";
                $sql .= "where idx is not null ";
                // 탭별 where 절
                if(isset($move_list["tab"])){
                    if($move_list["tab"] != ""){
                        if($move_list["tab"] == "second"){
                            $sql .= "and type = 1 ";
                        }else if($move_list["tab"] == "third"){
                            $sql .= "and type = 0 ";
                        }
                    }
                }
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "title"){
                            $sql .= " and title like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "name"){
                            $sql .= " and name like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == "email"){
                            $sql .= " and email like '%".$move_list["keyword"]."%' ";
                        }
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by regdate desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    $inner_sql = "where idx is not null ";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "title"){
                                $inner_sql .= " and title like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "name"){
                                $inner_sql .= " and name like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == "email"){
                                $inner_sql .= " and email like '%".$move_list["keyword"]."%' ";
                            }
                        }
                    }

                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from technique_aid as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as answer from technique_aid as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and type = 1 ";
                    $sql .= ") R2 ,";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as unanswer from technique_aid as t1 ";
                    $sql .= $inner_sql;
                    $sql .= "and type = 0 ";
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
         // 함 수 : 기술지원 답변여부 수정
         // 설 명 : 
         // 만든이: 김경훈
         *********************************************************************/
        function request_technique_aid_is_answer_change(){
            $param = $this->param;
            if($this->value_check(array("target", "is_answer"))){
                $sql = "UPDATE technique_aid SET type=".$param["is_answer"]." where idx=".$param["target"];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 공지사항 리스트
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function notice_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.*, t2.title from notice as t1 ";
                $sql .= "left join notice_name as t2 ";
                $sql .= "on t1.idx = t2.notice_idx ";
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
                    $inner_sql = "left join notice_name as t2 ";
                    $inner_sql .= "on t1.idx = t2.notice_idx ";
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
                    $sql .= "select count(t1.idx) as total from notice as t1 ";
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
        // 함 수 : 공지사항 상세정보
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function notice_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql  = "select t1.idx, t2.* from notice as t1 ";
                $sql .= "left join notice_name as t2 ";
                $sql .= "on t1.idx = t2.notice_idx ";
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
        // 함 수 : 공지사항 수정
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function notice_modify(){
            $param = $this->param;
            if($this->value_check(array("target","title", "content"))){
                // 제목
                $title = json_decode($param["title"] , true);
                // 내용
                $content = json_decode($param["content"], true);

                $sql = "select t2.content, t2.idx from notice as t1 ";
                $sql .= "left join notice_name as t2 ";
                $sql .= "on t1.idx = t2.notice_idx ";
                $sql .= "where t1.idx = ".$param["target"]." ";

                $this->conn->s_transaction();
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    $origin_content = $result["value"];

                    $origin_array_img = array();
                    foreach($origin_content as $key => $value){
                        $origin_img = $this->file_manager->get_s3_image_array($value["content"], $this->file_link["notice_description_path"]);
                        $origin_array_img = array_merge($origin_array_img, $origin_img);
                    }
                    
                    // 에러나면 삭제할 description 이미지
                    $description_error_remove_img = array();
                    $now_img_array =array();
                    for($i = 0; $i<count($origin_content); $i++){
                        $description = $content[$i];
                        $description_result = $this->file_manager->convert_description($this->file_path["notice_description_path"],$this->file_link["notice_description_path"], $description); //이미지 저장 및 변환
                        $now_img =  $this->file_manager->get_s3_image_array($description_result["description"], $this->file_link["notice_description_path"]);
                        $now_img_array = array_merge($now_img_array, $now_img);
                        $description_error_remove_img = array_merge($description_error_remove_img, $description_result["error_file_array"]);

                        $sql = "update notice_name set ";
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

                    $sql = "update notice set ";
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
                            $diff_file_path = $this->file_path["notice_description_path"].$value;
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
        // 함 수 : 공지사항 등록
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function notice_register(){
            $param = $this->param;
            if($this->value_check(array("title", "content"))){
                // 제목
                $title = json_decode($param["title"] , true);
                // 내용
                $content = json_decode($param["content"], true);

                $sql = "insert into notice(regdate) values(";
                $sql .= "now() ";
                $sql .= ")";

                $this->conn->s_transaction();
                $result = $this->conn->db_insert($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 공지사항 idx
                    $notice_idx = $result["value"];
                    // description 삭제 이미지 배열(모두)
                    $description_remove_img = array();

                    $sql = "insert into notice_name(title, content, lang_idx, notice_idx) values ";
                    for($i = 0; $i<count($title); $i++){
                        $description = $content[$i];
                        $description_result = $this->file_manager->convert_description($this->file_path["notice_description_path"],$this->file_link["notice_description_path"], $description); //이미지 저장 및 변환
                        $description_remove_img = array_merge($description_remove_img, $description_result["error_file_array"]);
                        $sql .= "(";
                        $sql .= $this->null_check($title[$i]) . ", ";
                        $sql .= $this->null_check($description_result["description"]) . ", ";
                        $sql .= ($i + 1) . ", ";
                        $sql .= $notice_idx . " ";
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
        // 함 수 : 공지사항 삭제
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function notice_remove(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $target = json_decode($param["target"], true);

                $sql = "select t2.content from notice as t1 ";
                $sql .= "left join notice_name as t2 ";
                $sql .= "on t1.idx = t2.notice_idx ";
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
                        $origin_img = $this->file_manager->get_s3_image_array($value["content"], $this->file_link["notice_description_path"]);
                        $origin_array_img = array_merge($origin_array_img, $origin_img);
                    }

                    if(count($target) > 0){
                        // notice 테이블 삭제
                        $sql = "delete from notice ";
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
                            // notice_name 테이블 삭제
                            $sql = "delete from notice_name ";
                            for($i = 0; $i<count($target); $i++){
                                if(count($target) == 1){
                                    $sql .= "where notice_idx = ".$target[$i]." ";
                                }else{
                                    if($i == 0){
                                        $sql .= "where notice_idx in ( ".$target[$i]." , ";
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
                                //이미지 삭제 코드
                                $delete_file_arr = array();
                                foreach($origin_array_img as $key => $value){
                                    $file_path = $this->file_path["notice_description_path"].$value;
                                    array_push($delete_file_arr,$file_path);
                                }
                                $this->file_manager->delete_file($delete_file_arr);
                                $this->result = $result;
                                $this->conn->commit();
                            }
                        }
                    }else{
                        $this->result["result"] = 0;
                        $this->result["error_code"] = 200;
                        $this->result["message"] = "선택된 공지사항이없습니다.";
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

    }
?>