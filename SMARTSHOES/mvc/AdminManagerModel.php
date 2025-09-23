<?php
    class AdminManagerModel extends gf{
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
        // 함 수 : 회원등록
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_user(){
            $param = $this->param;
            $c_email = 0;
            $c_phone = 0;
            if(isset($param["c_email"])){
               $c_email = 1;
            }
            if(isset($param["c_phone"])){
                $c_phone = 1;
            }
            if($this->value_check(array("name"))){
                $sql = "insert into user(name, number, branch, phone, c_phone, email1, email2, c_email, birth, postcode, address, detail_address, job, path, memo, regdate, type) ";
                $sql = $sql."values(";
                $sql = $sql.$this->null_check($param["name"])." , ";
                $sql = $sql.$this->null_check($param["number"])." , ";
                $sql = $sql.$this->null_check($param["branch"])." , ";
                $sql = $sql.$this->null_check($param["phone"])." , ";
                $sql = $sql.$c_phone." , ";
                $sql = $sql.$this->null_check($param["email1"])." , ";
                $sql = $sql.$this->null_check($param["email2"])." , ";
                $sql = $sql.$c_email." , ";
                $sql = $sql.$this->null_check($param["birth"])." , ";
                $sql = $sql.$this->null_check($param["postcode"])." , ";
                $sql = $sql.$this->null_check($param["address"])." , ";
                $sql = $sql.$this->null_check($param["detail_address"])." , ";
                $sql = $sql.$this->null_check($param["job"])." , ";
                $sql = $sql.$this->null_check($param["path"])." , ";
                $sql = $sql.$this->null_check($param["memo"])." , ";
                $sql = $sql.$this->null_check($param["regdate"])." , ";
                $sql = $sql.$this->null_check($param["type"]).")";
                
                $this->result = $this->conn->db_insert($sql);
            }
            echo $this->jsonEncode($this->result);           
        }

        /********************************************************************* 
        // 함 수 : 회원업데이트
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function update_user(){
            $param = $this->param;
            $c_email = 0;
            $c_phone = 0;
            if(isset($param["c_email"])){
               $c_email = 1;
            }
            if(isset($param["c_phone"])){
                $c_phone = 1;
            }
            if($this->value_check(array("name"))){
                $sql = "update user set ";
                $sql = $sql." name = ".$this->null_check($param["name"])." , ";
                $sql = $sql." number = ".$this->null_check($param["number"])." , ";
                $sql = $sql." branch = ".$this->null_check($param["branch"])." , ";
                $sql = $sql." phone = ".$this->null_check($param["phone"])." , ";
                $sql = $sql." c_phone = ".$c_phone." , ";
                $sql = $sql." email1 = ".$this->null_check($param["email1"])." , ";
                $sql = $sql." email2 = ".$this->null_check($param["email2"])." , ";
                $sql = $sql." c_email = ".$c_email." , ";
                $sql = $sql." birth = ".$this->null_check($param["birth"])." , ";
                $sql = $sql." postcode = ".$this->null_check($param["postcode"])." , ";
                $sql = $sql." address = ".$this->null_check($param["address"])." , ";
                $sql = $sql." detail_address = ".$this->null_check($param["detail_address"])." , ";
                $sql = $sql." job = ".$this->null_check($param["job"])." , ";
                $sql = $sql." path = ".$this->null_check($param["path"])." , ";
                $sql = $sql." memo = ".$this->null_check($param["memo"])." , ";
                $sql = $sql." regdate = ".$this->null_check($param["regdate"])." , ";
                $sql = $sql." type = ".$this->null_check($param["type"]);
                $sql = $sql." where idx = ".$param["idx"];

                
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 회원 정보 
        // 설 명 : idx값으로 회원정보 가져옴
        // 만든이: 박준기
        *********************************************************************/
        function request_user_info(){
            $param = $this->param;
            if($this->value_check(array("idx"))){
                $sql = "select * from user where idx = ".$param["idx"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);                       
        }


        /********************************************************************* 
        // 함 수 : 회원등록
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_user_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $like_sql = "where is_delete = 1 "; //검색을 위한 like절
                
                if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                    $word = $move_list["word"];
                    $search_kind = $move_list["search_kind"];

                    if($search_kind == "0"){ //이름검색
                        $like_sql .= " and name like '%".$word."%'";
                    }else if($search_kind == "1"){ //회원번호 검색
                        $like_sql .= " and number like '%".$word."%'";
                    }
                }


                $branch_sql = "";

                if(isset($move_list["branch"])){ //검색 키워드와 검색종류가 있을경우
                    $word = $move_list["word"];
                    $branch = $move_list["branch"];
                    if($branch == 0){
                        $branch_sql = "";
                    }else{
                        $branch_sql = " where branch = ".$branch;
                    }
                    
                }


                $sql = "";
                $birth_sql="";
                if(isset($move_list["tab"])){ //탭값 있을경우
                    $tab = $move_list["tab"];

                    if($tab == "add"){

                    }else if($tab == "end"){
                        $like_sql = $like_sql." and e_date between curdate() AND DATE_ADD(CURDATE(), INTERVAL +10 DAY) ";
                    }else if($tab == "birth"){
                        $sql = "select * from (";
                        $birth_sql = ")bb where year(birth) = year(now()) and month(birth) = month(now()) ";
                    }
                
                }

                $sql = $sql."select t.*, tt.count as class_count, tt.e_date as e_date from (select * from user".$branch_sql.") t";
                $sql = $sql." left outer join (select cu.user_idx, count(cr.idx) as count, max(e_date) as e_date from class_user as cu left join class_relation as cr";
                $sql = $sql." on cr.class_user_idx = cu.idx where is_delete = 1 group by cu.user_idx)tt";
                $sql = $sql." on t.idx = tt.user_idx";
                $sql .= " ".$like_sql." ";
                $sql .= " order by regdate desc, idx desc limit ".$page_size*($page-1).",".$page_size;
                $sql = $sql.$birth_sql;


                $this->result = $this->conn->db_select($sql);


                $sql = "select t.*, tt.count as class_count, tt.e_date as e_date from (select * from user".$branch_sql.") t";
                $sql = $sql." left outer join (select cu.user_idx, count(cr.idx) as count, max(e_date) as e_date from class_user as cu left join class_relation as cr";
                $sql = $sql." on cr.class_user_idx = cu.idx where is_delete = 1 group by cu.user_idx)tt";
                $sql = $sql." on t.idx = tt.user_idx";
                $sql .= " ".$like_sql." ";
                
                $sql = "select count(ttt.name) as total_count from(".$sql.")ttt";

                $result = $this->conn->db_select($sql);

                if($result["result"]=="1"){
                    $this->result["total_count"]=$result["value"][0]["total_count"];
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 지점 가져오기
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function get_branch_list(){
            $param = $this->param;

            $sql = "select * from branch";
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 지점 가져오기
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function get_branch(){
            $param = $this->param;

            if($this->value_check(array("branch"))){
                $sql = "select branch_name from branch where idx = ".$param["branch"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 지점별 class 정보 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function get_class_list(){
            $param = $this->param;

            if($this->value_check(array("branch"))){
                $sql = "select * from class where branch_idx = ".$param["branch"]." and is_delete =1";
                $sql = $sql." order by case when day like '월%' then 1";
                $sql = $sql." when day like '화%' then 2";
                $sql = $sql." when day like '수%' then 3";
                $sql = $sql." when day like '목%' then 4";
                $sql = $sql." when day like '금%' then 5";
                $sql = $sql." when day like '토%' then 6";
                $sql = $sql." when day like '일%' then 7 end asc;";
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 지점별 class 정보 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function get_all_class_list(){
            $param = $this->param;
            
            $page_size = (int)$param["page_size"];
            $page = (int)$param["move_page"];
            $move_list = json_decode($param["move_list"], true);
            $like_sql = "where class.is_delete = '1' ";
            

            if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                $word = $move_list["word"];
                $search_kind = $move_list["search_kind"];

                if($search_kind == "0"){ //강의명
                    $like_sql .= " and class.title like '%".$word."%'";
                }else if($search_kind == "1"){ //강사명
                    $like_sql .= " and class.teacher like '%".$word."%'";
                }
            }
            if(isset($move_list["grade"])){ //검색 키워드와 검색종류가 있을경우
                $s_grade = $move_list["grade"];
                if($s_grade == "전체"){
                }else{
                    $like_sql .= " and class.grade = '".$s_grade."'";
                }   
            }

            if(isset($move_list["branch_idx"])){ //검색 키워드와 검색종류가 있을경우
                $branch_idx = $move_list["branch_idx"];
                if($branch_idx == "0"){
                }else{
                    $like_sql .= " and class.branch_idx = '".$branch_idx."'";
                }   
            }

            $sql = "select class.*, date_format(class.time,'%H:%i') as class_time ,branch.branch_name from class left join branch on class.branch_idx = branch.idx ";
            $sql .= " ".$like_sql." ";
            $sql = $sql." order by case when day like '월%' then 1";
            $sql = $sql." when day like '화%' then 2";
            $sql = $sql." when day like '수%' then 3";
            $sql = $sql." when day like '목%' then 4";
            $sql = $sql." when day like '금%' then 5";
            $sql = $sql." when day like '토%' then 6";
            $sql = $sql." when day like '일%' then 7 end asc ";
            $sql .= " limit ".$page_size*($page-1).",".$page_size; 

            $this->result = $this->conn->db_select($sql);
            
            $sql = "select count(idx) as total_count from(".$sql.")t";
            
            $result = $this->conn->db_select($sql);
            if($result["result"]=="1"){
                $this->result["total_count"]=$result["value"][0]["total_count"];
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 회원별 수업 등록시 특정 수업정보 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_class_info(){
            $param = $this->param;

            if($this->value_check(array("idx"))){
                $sql = "select * from class where idx = ".$param["idx"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 유저 삭제(상태값만 변경)
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function delete_user(){
            $param = $this->param;
            
            if($this->value_check(array("idx"))){
                $sql = "update user set is_delete = 0 where idx =".$param["idx"];
                $this->result = $this->conn->db_update($sql);
            }

            echo $this->jsonEncode($this->result);

        }

        
        /********************************************************************* 
        // 함 수 : 회원별 강의 추가
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_class(){
            $param = $this->param;

            $cash = 0;
            $etc = 0;
            $credit = 0;

            if($param["cash"]!=null){
                $cash = $this->null_check($param["cash"]);
            }
            if($param["etc"]!=null){
                $etc = $this->null_check($param["etc"]);
            }
            if($param["credit"]!=null){
                $credit = $this->null_check($param["credit"]);
            }

            if($this->value_check(array("s_date", "e_date", "idx"))){
                $sql = "insert into class_user(kind, reg_type, s_date, e_date, cash, credit, etc, register, memo, user_idx, regdate, branch_idx) ";
                $sql = $sql."values(".$this->null_check($param["kind"]).",".$this->null_check($param["reg_type"]).",".$this->null_check($param["s_date"]).",".$this->null_check($param["e_date"]).",";
                $sql = $sql.$cash.",".$credit.",".$etc.",".$this->null_check($param["register"]).",".$this->null_check($param["memo"]).",".$this->null_check($param["idx"]).", ".$this->null_check($param["regdate"]).",".$this->null_check($param["branch_idx"]).")";
                
                $this->conn->s_transaction();

                $result = $this->conn->db_insert($sql);
                $class_user_idx = $result["value"];

                if($result["result"]==1){
                    // //수업 추가하기
                    $add_categort_list = json_decode($param["add_category_list"]); //추가해야할 category_idx 배열
                    $product_category_relation_sql = "insert into class_relation(class_user_idx, class_idx, sequence) values";
                    // print_r($add_categort_list);
                    if(count($add_categort_list) > 0){ //카테고리를 넣어야한다면
                        for($i=0; $i<count($add_categort_list); $i++){
                            if($i == 0){ //처음이면 앞에 ,필요없음
                                $product_category_relation_sql = $product_category_relation_sql. "(";
                                $product_category_relation_sql = $product_category_relation_sql.$this->null_check($class_user_idx);
                                $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                                $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                            }else{
                                $product_category_relation_sql = $product_category_relation_sql. ",(";
                                $product_category_relation_sql = $product_category_relation_sql.$this->null_check($class_user_idx);
                                $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                                $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                            }
                        }
                        $this->conn->db_insert($product_category_relation_sql); //카테고리 릴레이션 insert
                    }
                    $this->conn->commit();
                }else{
                    $this->conn->rollback();
                    $result = $result["result"] == "0";
                    $result = $result["message"] == "인서트 오류1";
                }
                $this->result = $result;
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 수업 삭제
        // 설 명 : is_delete만 0로 변경
        // 만든이: 박준기
        *********************************************************************/
        function delete_class(){
            $param = $this->param;
            $sql = "update class_user set is_delete = 0 where idx =".$param["idx"];
            $this->result = $this->conn->db_update($sql);
            echo $this->jsonEncode($this->result);

        }

        /********************************************************************* 
        // 함 수 : 수업 환불 / 환불 취소
        // 설 명 : is_delete만 2, 1로 변경
        // 만든이: 박준기
        *********************************************************************/
        function refund_class(){
            $param = $this->param;
            $sql = "update class_user set is_delete = ".$param["state"]." where idx =".$param["idx"];
            $this->result = $this->conn->db_update($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 회원별 강의 수정
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function modify_class(){
            $param = $this->param;

            $cash = 0;
            $etc = 0;
            $credit = 0;

            if($param["cash"]!=null){
                $cash = $this->null_check($param["cash"]);
            }
            if($param["etc"]!=null){
                $etc = $this->null_check($param["etc"]);
            }
            if($param["credit"]!=null){
                $credit = $this->null_check($param["credit"]);
            }

            if($this->value_check(array("s_date", "e_date", "idx"))){
                $sql = "update class_user set ";
                $sql = $sql."kind=".$this->null_check($param["kind"]).",";
                $sql = $sql."reg_type=".$this->null_check($param["reg_type"]).",";
                $sql = $sql."s_date=".$this->null_check($param["s_date"]).",";
                $sql = $sql."e_date=".$this->null_check($param["e_date"]).",";
                $sql = $sql."cash=".$cash.",";
                $sql = $sql."credit=".$credit.",";
                $sql = $sql."etc=".$etc.",";
                $sql = $sql."register=".$this->null_check($param["register"]).",";
                $sql = $sql."memo=".$this->null_check($param["memo"]).",";
                $sql = $sql."regdate=".$this->null_check($param["regdate"]);
                $sql = $sql." where idx = ".$param["idx"];
           
                $this->conn->s_transaction();
                $result = $this->conn->db_update($sql);

                $class_user_idx = $param["idx"];
                if($result["result"]==1){
                    // //수업 추가하기
                    $add_categort_list = json_decode($param["add_category_list"]); //추가해야할 category_idx 배열
                    $product_category_relation_sql = "insert into class_relation(class_user_idx, class_idx, sequence) values";
                    if(count($add_categort_list) > 0){ //카테고리를 넣어야한다면
                        for($i=0; $i<count($add_categort_list); $i++){
                            if($i == 0){ //처음이면 앞에 ,필요없음
                                $product_category_relation_sql = $product_category_relation_sql. "(";
                                $product_category_relation_sql = $product_category_relation_sql.$this->null_check($class_user_idx);
                                $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                                $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                            }else{
                                $product_category_relation_sql = $product_category_relation_sql. ",(";
                                $product_category_relation_sql = $product_category_relation_sql.$this->null_check($class_user_idx);
                                $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                                $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                            }
                        }

                        $this->conn->db_insert($product_category_relation_sql); //카테고리 릴레이션 insert
                    }
                    //삭제해야할 카테고리가 있다면 삭제
                    $delete_relation_idx_array = json_decode($param["delete_relation_idx"]); //삭제해야할 product_category_relation idx들을 담은 배열
                    if(count($delete_relation_idx_array) > 0){ //삭제할 카테고리가 있다면
                        $category_delete_sql = "delete from class_relation where ";
                        for($i=0; $i<count($delete_relation_idx_array); $i++){
                            if($i == 0){
                                $category_delete_sql = $category_delete_sql." idx=".$delete_relation_idx_array[$i];
                            }else{
                                $category_delete_sql = $category_delete_sql." or idx=".$delete_relation_idx_array[$i];
                            }
                        }
                        //delete sql 실행
                        // echo "카테고리 delete 실행";
                        $this->conn->db_delete($category_delete_sql);
                    }

                    $this->conn->commit();
                }else{
                    $this->conn->rollback();
                    $result = $result["result"] == "0";
                    $result = $result["message"] == "인서트 오류1";
                }
                $this->result = $result;
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 특정 수업 정보
        // 설 명 : class_user idx로 검색
        // 만든이: 박준기
        *********************************************************************/
        function get_class_info(){
            $param = $this->param;
            if($this->value_check(array("idx"))){
                $sql = "select user.idx as user_idx, cr.idx as relation_idx, cu.idx, cu.kind, cu.reg_type, cu.s_date, cu.e_date, cu.cash, cu.credit, cu.etc, cu.register, cu.memo, date_format(cu.regdate,'%Y-%m-%d') as regdate, cu.is_delete, branch.branch_name, branch.idx as branch_idx, cr.class_idx, class.title ";
                $sql = $sql." from class_user as cu left join user on cu.user_idx = user.idx ";
                $sql = $sql." left join class_relation as cr on cr.class_user_idx = cu.idx left join branch on branch.idx = user.branch left join class on class.idx = cr.class_idx ";
                $sql = $sql." where cu.idx = ".$param["idx"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }






        /********************************************************************* 
        // 함 수 : 유저 삭제(상태값만 변경)
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function user_class_list(){
            $param = $this->param;
            
            $page_size = (int)$param["page_size"];
            $page = (int)$param["move_page"];
            $move_list = json_decode($param["move_list"], true);

            $sql = "select cu.*, GROUP_CONCAT(class.teacher SEPARATOR ' / ') as teacher, GROUP_CONCAT(class.title SEPARATOR ' / ') as class_name,  GROUP_CONCAT(class.day SEPARATOR ' / ') as day,  GROUP_CONCAT(class.grade SEPARATOR ' / ') as grade,  GROUP_CONCAT(date_format(class.time,'%H:%i') SEPARATOR ' / ') as time ";
            $sql .= " from class_user as cu left join class_relation as cr on cu.idx = cr.class_user_idx ";
            $sql .= " left join class on cr.class_idx = class.idx ";
            $sql .= " where user_idx = ".$move_list["idx"]." and cu.is_delete != 0";
            $sql .= " group by idx order by regdate asc limit ".$page_size*($page-1).",".$page_size; 

            $this->result = $this->conn->db_select($sql);
            $sql = "select cu.idx as idx ";
            $sql .= " from class_user as cu left join class_relation as cr on cu.idx = cr.class_user_idx ";
            $sql .= " left join class on cr.class_idx = class.idx ";
            $sql .= " where user_idx = ".$move_list["idx"];
            $sql .= " group by idx order by regdate desc";
            $sql = "select count(idx) as total_count from (".$sql.")t";
            
            $result = $this->conn->db_select($sql);
            if($result["result"]=="1"){
                $this->result["total_count"]=$result["value"][0]["total_count"];
            }
            echo $this->jsonEncode($this->result);

        }
        

        /********************************************************************* 
        // 함 수 : 수업 정보 가져옴
        // 설 명 : idx 값기준
        // 만든이: 박준기
        *********************************************************************/
        function request_modify_class_data(){
            $param = $this->param;

            if($this->value_check(array("idx"))){
                $sql = "select *,date_format(class.time,'%H:%i') as class_time  from class where idx = ".$param["idx"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 수업 등록
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_new_class(){
            $param = $this->param;

            if($this->value_check(array("branch","title","teacher","day","grade","time"))){
                $sql = "insert into class(branch_idx, title,teacher, day, grade, time,class_regdate) values(";
                $sql = $sql.$this->null_check($param["branch"]).",";
                $sql = $sql.$this->null_check($param["title"]).",";
                $sql = $sql.$this->null_check($param["teacher"]).",";
                $sql = $sql.$this->null_check($param["day"]).",";
                $sql = $sql.$this->null_check($param["grade"]).",";
                $sql = $sql.$this->null_check($param["time"]).",";
                $sql = $sql."now())";

                $this->result = $this->conn->db_insert($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 수업 수정
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function modify_class_data(){
            $param = $this->param;

            if($this->value_check(array("idx"))){
                $sql = "update class set ";
                $sql = $sql." branch_idx = ".$this->null_check($param["branch"]).",";
                $sql = $sql." title = ".$this->null_check($param["title"]).",";
                $sql = $sql." teacher = ".$this->null_check($param["teacher"]).",";
                $sql = $sql." day = ".$this->null_check($param["day"]).",";
                $sql = $sql." grade = ".$this->null_check($param["grade"]).",";
                $sql = $sql." time = ".$this->null_check($param["time"])." ";
                $sql = $sql." where idx =".$this->null_check($param["idx"]);

                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 수업 삭제
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function delete_modify_new_class(){
            $param = $this->param;

            if($this->value_check(array("idx"))){
                $sql = "update class set is_delete = '0' where idx=".$param["idx"];

                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 특정 수업의 학생 리스트
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function get_class_userlist(){
            $param = $this->param;

            $sql = "select u.idx as user_idx, u.name, cu.s_date as s_date, cu.e_date as e_date from class_relation as cr left join class_user as cu on cr.class_user_idx = cu.idx";
            $sql .= " left join user as u on u.idx = cu.user_idx";
            $sql .= " where cr.class_idx = ".$param["class_idx"];
            $sql .= " and DATE_FORMAT(e_date, '%Y-%m-%d') >= DATE_FORMAT(now(), '%Y-%m-%d')";


            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 락커리스트
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function locker_list(){
            $param = $this->param;
            $sql = "select idx, name, locker, e_date, case when e_date < curdate() then 2 when e_date - curdate() <= 10 then 1 when e_date - curdate() > 10 then 0 end as state";
            $sql = $sql." from locker where branch_idx =".$param["branch"];
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 락커 추가
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_locker(){
            $param = $this->param;
            
            $cash = 0;
            $etc = 0;
            $credit = 0;

            $sql = "select idx from locker where locker=".$param["locker"]." and branch_idx=".$param["branch_idx"];

            $result = $this->conn->db_select($sql);
            if($result["result"]=="1"){
                $locker_idx = $result["value"][0]["idx"];

                if($param["cash"]!=null){
                    $cash = $this->null_check($param["cash"]);
                }
                if($param["etc"]!=null){
                    $etc = $this->null_check($param["etc"]);
                }
                if($param["credit"]!=null){
                    $credit = $this->null_check($param["credit"]);
                }
                $this->conn->s_transaction();


                $sql = "update locker set ";
                $sql = $sql."name=".$this->null_check($param["name"]).",";
                $sql = $sql."number=".$this->null_check($param["number"]).",";
                $sql = $sql."s_date=".$this->null_check($param["s_date"]).",";
                $sql = $sql."e_date=".$this->null_check($param["e_date"]).",";
                $sql = $sql."cash=".$cash.",";
                $sql = $sql."credit=".$credit.",";
                $sql = $sql."etc=".$etc.",";
                $sql = $sql."register=".$this->null_check($param["register"]).",";
                $sql = $sql."memo=".$this->null_check($param["memo"]).",";
                $sql = $sql."regdate=".$this->null_check($param["regdate"]).",";
                $sql = $sql."branch_idx=".$this->null_check($param["branch_idx"]);
                $sql = $sql." where idx = ".$locker_idx;


                $update1_result = $this->conn->db_update($sql);
                
                if($update1_result["result"]=="1"){
                    if($param["mode"]=="add"){
                        $sql = "insert into locker_history(locker_idx, branch_idx, name, number, s_date, e_date, cash, credit, etc, register, memo, regdate, locker_state) ";
                        $sql = $sql."values(".$locker_idx;
                        $sql = $sql.",".$this->null_check($param["branch_idx"]);
                        $sql = $sql.",".$this->null_check($param["name"]);
                        $sql = $sql.",".$this->null_check($param["number"]);
                        $sql = $sql.",".$this->null_check($param["s_date"]);
                        $sql = $sql.",".$this->null_check($param["e_date"]);
                        $sql = $sql.",".$cash;
                        $sql = $sql.",".$credit;
                        $sql = $sql.",".$etc;
                        $sql = $sql.",".$this->null_check($param["register"]);
                        $sql = $sql.",".$this->null_check($param["memo"]);
                        $sql = $sql.",".$this->null_check($param["regdate"]);
                        $sql = $sql.",1)";
    
                        $insert_result = $this->conn->db_insert($sql);
                        if($insert_result["result"]=="1"){
                            $this->conn->commit();
                        }
                        $this->result = $insert_result;
    
                    }else{
                        $sql = "select idx from locker_history where locker_state = 1 and branch_idx =".$param["branch_idx"]." and locker_idx = ".$locker_idx;
                        $history_result = $this->conn->db_select($sql);
    
                        $history_idx = $history_result["value"]["0"]["idx"];
    
                        $update_sql = "update locker_history set ";
                        $update_sql = $update_sql."name=".$this->null_check($param["name"]).",";
                        $update_sql = $update_sql."number=".$this->null_check($param["number"]).",";
                        $update_sql = $update_sql."s_date=".$this->null_check($param["s_date"]).",";
                        $update_sql = $update_sql."e_date=".$this->null_check($param["e_date"]).",";
                        $update_sql = $update_sql."cash=".$cash.",";
                        $update_sql = $update_sql."credit=".$credit.",";
                        $update_sql = $update_sql."etc=".$etc.",";
                        $update_sql = $update_sql."register=".$this->null_check($param["register"]).",";
                        $update_sql = $update_sql."memo=".$this->null_check($param["memo"]).",";
                        $update_sql = $update_sql."regdate=".$this->null_check($param["regdate"]).",";
                        $update_sql = $update_sql."branch_idx=".$this->null_check($param["branch_idx"]).",";
                        $update_sql = $update_sql."locker_state=1";
                        $update_sql = $update_sql." where idx = ".$history_idx;
    
                        $update2_result = $this->conn->db_update($update_sql);
                        if($update2_result["result"]=="1"){
                            $this->conn->commit();
                        }
                        $this->result = $update2_result;
                    }
                }else{
                    $this->result = $update1_result;
                }   
            }else{
                $this->result = $result;
            }
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 이름으로 회원검색
        // 설 명 : 락커페이지에서 특정인의 정보 보여줌
        // 만든이: 박준기
        *********************************************************************/
        function seach_member(){
            $param = $this->param;
            if($this->value_check(array("member_name","branch"))){
                $sql = "select idx, name, phone, number from user where is_delete =1 and branch = ".$param["branch"]." and name like '%".$param["member_name"]."%'";
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);

        }


        /********************************************************************* 
        // 함 수 : 락커 데이터 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_locker_data(){
            $param = $this->param;
            if($this->value_check(array("locker","branch_idx"))){
                $sql = "select * from locker where locker=".$param["locker"]." and branch_idx=".$param["branch_idx"];
                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 이름으로 유저 데이터 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function locker_search_name(){
            $param = $this->param;
            if($this->value_check(array("keyword"))){
                $sql = "select * from locker where branch_idx = ".$param["branch"];
                $sql = $sql." and name like '%".$param["keyword"]."%'";

                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 이름으로 유저 데이터 가져옴
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function delete_lcoker(){
            $param = $this->param;
            if($this->value_check(array("locker", "branch"))){
                $sql = "select idx from locker where locker=".$param["locker"]." and branch_idx=".$param["branch"];
                $result = $this->conn->db_select($sql);
                if($result["result"]=="1"){
                    $locker_idx = $result["value"][0]["idx"];
                    $this->conn->s_transaction();
                    $sql = "update locker set ";
                    $sql = $sql."name=null,";
                    $sql = $sql."number=null,";
                    $sql = $sql."s_date=null,";
                    $sql = $sql."e_date=null,";
                    $sql = $sql."cash=null,";
                    $sql = $sql."credit=null,";
                    $sql = $sql."etc=null,";
                    $sql = $sql."register=null,";
                    $sql = $sql."memo=null,";
                    $sql = $sql."regdate=null";
                    $sql = $sql." where idx = ".$locker_idx;

                    $update1_result = $this->conn->db_update($sql);

                    if($update1_result["result"]=="1"){
                        $sql = "select idx from locker_history where locker_state = 1 and branch_idx =".$param["branch"]." and locker_idx = ".$locker_idx;
                        $history_result = $this->conn->db_select($sql);
    
                        $history_idx = $history_result["value"]["0"]["idx"];
    
                        $update_sql = "update locker_history set ";
                        $update_sql = $update_sql."locker_state=0";
                        $update_sql = $update_sql." where idx = ".$history_idx;
    
                        $update2_result = $this->conn->db_update($update_sql);

                        if($update2_result["result"]=="1"){
                            $this->conn->commit();
                        }
                        $this->result = $update2_result;
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 매출 리스트 가져옴(월별)
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_sales_list(){
            $param = $this->param;

            $year = $param["year"];

            $s_date = $year."-01";
            $e_date = $year."-12";
            $branch_idx = $param["branch_idx"];

            $sql = "select * from (";
            $sql = $sql." select DATE_FORMAT(a.Date,'%Y-%m') as all_date";
            $sql = $sql." from (select curdate() - INTERVAL (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a) ) month as Date";
            $sql = $sql." from (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as a";
            $sql = $sql." cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as b";
            $sql = $sql." cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as c";
            $sql = $sql." cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as d ) a";
            $sql = $sql." where DATE_FORMAT(a.Date,'%Y-%m') between '".$s_date."' and '".$e_date."') dd left join (";
            $sql = $sql." select branch_idx, DATE_FORMAT(regdate,'%Y-%m') as `date`, sum(cash) as cash, sum(credit) as credit, sum(etc) as etc";
            $sql = $sql." from  (select '수강료', cash, credit, etc, register, memo, regdate, branch_idx from class_user where is_delete != 0 and is_delete!=2";
            $sql = $sql." UNION ALL select '락커', cash, credit, etc, register, memo, regdate, branch_idx from locker_history)t";
            $sql = $sql." where branch_idx = ".$branch_idx." group by DATE_FORMAT(regdate, '%Y-%m') order by DATE_FORMAT(regdate, '%Y-%m')";
            $sql = $sql." ) bb on dd.all_date = bb.`date`";
            $sql = $sql." order by dd.all_date asc;";


            $income_result = $this->conn->db_select($sql);
            

            $sql = "select type, sum(cash) as s_cash, sum(credit) as s_credit, sum(etc) as s_etc, DATE_FORMAT(regdate, '%Y-%m') as regdate from spending";
            $sql = $sql." where type = '지출' and branch_idx = ".$branch_idx;
            $sql = $sql." group by regdate";
            $outcome_result = $this->conn->db_select($sql);


            $sql = "select type, sum(cash) as s_cash, sum(credit) as s_credit, sum(etc) as s_etc, DATE_FORMAT(regdate, '%Y-%m') as regdate from spending";
            $sql = $sql." where type = '수입' and branch_idx = ".$branch_idx;
            $sql = $sql." group by regdate";
            $i_result = $this->conn->db_select($sql);

            $list = $income_result["value"];

            foreach($list as $key => $value){
                foreach($outcome_result["value"] as $key2 => $value2){
                    if($income_result["value"][$key]["all_date"]==$outcome_result["value"][$key2]["regdate"]){
                        $s_cash = $outcome_result["value"][$key2]["s_cash"];
                        $s_credit = $outcome_result["value"][$key2]["s_credit"];
                        $s_etc = $outcome_result["value"][$key2]["s_etc"];
                        $income_result["value"][$key]["s_cash"] = $s_cash;
                        $income_result["value"][$key]["s_credit"] = $s_credit;
                        $income_result["value"][$key]["s_etc"] = $s_etc;
                    }
                }
                foreach($i_result["value"] as $key2 => $value2){
                    if($income_result["value"][$key]["all_date"]==$i_result["value"][$key2]["regdate"]){
                        $i_cash = $i_result["value"][$key2]["s_cash"];
                        $i_credit = $i_result["value"][$key2]["s_credit"];
                        $i_etc = $i_result["value"][$key2]["s_etc"];
                        $income_result["value"][$key]["i_cash"] = $i_cash;
                        $income_result["value"][$key]["i_credit"] = $i_credit;
                        $income_result["value"][$key]["i_etc"] = $i_etc;
                    }
                }
            }
            $this->result = $income_result;
            
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 매출 리스트 가져옴(월별)
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_sales_list_month(){
            $param = $this->param;

            $branch_idx = $param["branch_idx"];
            $date = $param["date"];

            
            $sql = "select * from (";
            $sql = $sql."select '수입' as type,'수강료' as category, (cash + credit + etc) as total, '' as s_total,register, memo, DATE_FORMAT(regdate,'%Y-%m-%d') as regdate, branch_idx from class_user where is_delete != 0";
            $sql = $sql." UNION ALL";
            $sql = $sql." select '수입' as type,'락커', (cash + credit + etc) as total, '' as s_total,register, memo, regdate, branch_idx from locker_history";
            $sql = $sql." UNION ALL";
            $sql = $sql." select type, category, '',(cash + credit + etc) as s_total, register, memo, regdate, branch_idx from spending where type ='지출'";
            $sql = $sql." UNION ALL";
            $sql = $sql." select type, category, (cash + credit + etc) as total, '', register, memo, regdate, branch_idx from spending where type ='수입'";
            $sql = $sql." )t where branch_idx = ".$branch_idx." and DATE_FORMAT(regdate,'%Y-%m') = '".$date."' order by regdate";

            $this->result = $this->conn->db_select($sql);
            
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 기타 수입/지출
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/

        function save_spending(){
            $param = $this->param;


            $cash = 0;
            $etc = 0;
            $credit = 0;

            if($param["cash"]!=null){
                $cash = $this->null_check($param["cash"]);
            }
            if($param["etc"]!=null){
                $etc = $this->null_check($param["etc"]);
            }
            if($param["credit"]!=null){
                $credit = $this->null_check($param["credit"]);
            }

            $sql = "insert into spending(type, category, cash, credit, etc, register, memo, regdate, branch_idx) values(";
            $sql = $sql.$this->null_check($param["type"]).",";
            $sql = $sql.$this->null_check($param["category"]).",";
            $sql = $sql.$cash.",";
            $sql = $sql.$credit.",";
            $sql = $sql.$etc.",";
            $sql = $sql.$this->null_check($param["register"]).",";
            $sql = $sql.$this->null_check($param["memo"]).",";
            $sql = $sql.$this->null_check($param["regdate"]).",";
            $sql = $sql.$this->null_check($param["branch_idx"]).")";

            $this->result = $this->conn->db_insert($sql);
            
            echo $this->jsonEncode($this->result);
            
        }

        /********************************************************************* 
        // 함 수 : 기타 수입/지출 삭제
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/

        function delete_spending(){
            $param = $this->param;
            $sql = "delete from spending where idx = ".$param["idx"];

            $this->result = $this->conn->db_delete($sql);
            
            echo $this->jsonEncode($this->result);
            
        }


        /********************************************************************* 
        // 함 수 : 기타 수입/지출 업데이트
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function update_spending(){
            $param = $this->param;

            $cash = 0;
            $etc = 0;
            $credit = 0;

            if($param["cash"]!=null){
                $cash = $this->null_check($param["cash"]);
            }
            if($param["etc"]!=null){
                $etc = $this->null_check($param["etc"]);
            }
            if($param["credit"]!=null){
                $credit = $this->null_check($param["credit"]);
            }

            if($this->value_check(array("idx"))){
                $sql = "update spending set ";
                $sql = $sql." type = ".$this->null_check($param["type"])." , ";
                $sql = $sql." category = ".$this->null_check($param["category"])." , ";
                $sql = $sql." cash = ".$cash." , ";
                $sql = $sql." credit = ".$credit." , ";
                $sql = $sql." etc = ".$etc." , ";
                $sql = $sql." register = ".$this->null_check($param["register"])." , ";
                $sql = $sql." memo = ".$this->null_check($param["memo"])." , ";
                $sql = $sql." regdate = ".$this->null_check($param["regdate"])." , ";
                $sql = $sql." branch_idx = ".$this->null_check($param["branch_idx"]);
                $sql = $sql." where idx = ".$param["idx"];

                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 기타 수입/지출 데이터 가져오기
        // 설 명 : idx 기준
        // 만든이: 박준기
        *********************************************************************/
        function get_spending_data(){
            $param = $this->param;
            $sql = "select type, category, cash, credit, etc, register, memo, regdate, branch_idx from spending where idx = ".$param["idx"];
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 기타 수입/지출
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function request_spending_list(){
            $param = $this->param;

            $s_date = $this->param["s_date"];
            $e_date = $this->param["e_date"];

            $sql = "select * from spending where ";
            $sql = $sql."branch_idx =".$param["branch_idx"]." ";
            $sql = $sql." and regdate between ".$this->null_check($s_date)." and ".$this->null_check($e_date);
            $sql = $sql." order by regdate asc";

            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        
        }

        /********************************************************************* 
        // 함 수 : 유저 전체 정보 다운로드
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function excel_download(){
            $param = $this->param;
            // 엑셀 클래스
            
            $objPHPExcel = new PHPExcel();
            // 엑셀 컬럼에 사용할 알파벳(26개)
            $alphabet = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

            $move_list = json_decode($param["move_list"], true);

            $like_sql = ""; //검색을 위한 like절


            $like_sql = "where is_delete = 1 "; //검색을 위한 like절
                
                if(isset($move_list["word"]) && isset($move_list["search_kind"])){ //검색 키워드와 검색종류가 있을경우
                    $word = $move_list["word"];
                    $search_kind = $move_list["search_kind"];

                    if($search_kind == "0"){ //이름검색
                        $like_sql .= " and name like '%".$word."%'";
                    }else if($search_kind == "1"){ //회원번호 검색
                        $like_sql .= " and number like '%".$word."%'";
                    }
                }
                $sql = "";
                $birth_sql="";
                if(isset($move_list["tab"])){ //탭값 있을경우
                    $tab = $move_list["tab"];

                    if($tab == "add"){

                    }else if($tab == "end"){
                        $like_sql = $like_sql." and e_date between curdate() AND DATE_ADD(CURDATE(), INTERVAL +10 DAY) ";
                    }else if($tab == "birth"){
                        $sql = "select * from (";
                        $birth_sql = ")bb where year(birth) = year(now()) and month(birth) = month(now()) ";
                    }
                }
                $sql = $sql."select * from (";
                $sql = $sql."select t.*, concat(email1, '@',email2) as email from (select * from user) t";
                $sql = $sql." left outer join (select cu.user_idx, count(cr.idx) as count, max(e_date) as e_date from class_user as cu left join class_relation as cr";
                $sql = $sql." on cr.class_user_idx = cu.idx where is_delete = 1 group by cu.user_idx)tt";
                $sql = $sql." on t.idx = tt.user_idx";
                $sql .= " ".$like_sql." ";
                $sql = $sql.$birth_sql;
                $sql = $sql.")ttt left join branch on ttt.branch = branch.idx";

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

                $column_arr = array("번호","이름","회원번호","지점","연락처","문자수신동의", "이메일", "이메일수신동의", "생일", "주소","주소상세","직업","등록일");
                $key_arr = array("name","number","branch_name","phone","c_phone","email","c_email","birth","address","detail_address", "job", "regdate");

                // Excel파일의 각 셀의 타이틀 설정
                for($i = 0; $i<count($column_arr); $i++){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$i]."1", $column_arr[$i]);
                }

                // for문을 이용해 DB에서 가져온 데이터를 순차적으로 입력한다.
                // 변수 i값은 2부터 시작하도록 해야한다.
                for($i = 0; $i<count($select_list); $i++){
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A".($i+2), ($i+1));
                    for($j = 0; $j<count($key_arr); $j++){
                        if($key_arr[$j] == "c_email" || $key_arr[$j] == "c_phone"){
                            if(number_format($select_list[$i][$key_arr[$j]])=="0"){
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$j+1].($i+2), "미동의");
                            }else{
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($alphabet[$j+1].($i+2), "동의");
                            }
                            
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
        // 함 수 : 브런치 추가시 락커 추가
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_branch_locker(){
            $param=$this->param;
            $branch_idx = $param["branch"];
            $sql = "insert into locker(number,branch_idx) values";
            $i = 1;
            while($i<=100){
                $sql = $sql."(".$i.",".$branch_idx;
                $sql = $sql."),";
                $i++;
            }
            $sql=substr($sql,0,-1);

            $this->result = $this->conn->db_insert($sql);
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 브런치 추가
        // 설 명 : 
        // 만든이: 박준기
        *********************************************************************/
        function add_branch(){
            $param=$this->param;
            $branch_idx = $param["branch_name"];
            $sql = "insert into branch(branch_name, regdate) values(";
            $sql = $sql.$this->null_check($param["branch_name"]).",now())";

            $this->result = $this->conn->db_insert($sql);
            echo $this->jsonEncode($this->result);
        }

    }
?>
