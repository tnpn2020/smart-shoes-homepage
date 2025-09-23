<?php
    class AdminMenu3Model extends gf{
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
        // 함 수 : 대분류 카테고리 조회하기
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_main_category_list(){
            $param = $this->param;
            $sql = "select * from main_category ";
            if(isset($param["state"])){
                $sql .= "where state = ".$param["state"]." ";
            }
            $sql .= "order by state desc, sequence asc;";
            $this->result = $this->conn->db_select($sql);
            
            if($this->result["result"] == "1"){//언어 데이터 조회하기
                $sql = "select * from main_category_name";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "0"){ //실패일경우 result 반환
                    $this->result = $result;
                }else{ //main_category idx에 맞는 언어 데이터 넣기
                    $name_data = $result["value"];
                    for($i=0; $i<count($name_data); $i++){
                       $main_category_idx = $name_data[$i]["main_category_idx"]; //언어 데이터의 메인 카테고리 idx
                       for($k=0; $k<count($this->result["value"]); $k++){
                            if($this->result["value"][$k]["idx"] == $main_category_idx){ //언어의 메인카테고리와 idx가 같다면
                                if(isset($this->result["value"][$k]["name_data"])){ //name_data가 있다면 그냥 넣기
                                    array_push($this->result["value"][$k]["name_data"], $name_data[$i]);
                                }else{ //name_data가 없다면 생성후 넣기
                                    $this->result["value"][$k]["name_data"] = array();
                                    array_push($this->result["value"][$k]["name_data"], $name_data[$i]);
                                }
                            }
                       }
                    }
                }
            }
            

            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 카테고리 조회하기
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_category_list(){
            $param = $this->param;
            if($this->value_check(array("category_kind", "fk_index"))){
                $category_kind = $param["category_kind"];
                $sql = null;
                $name_sql = null;
                if($category_kind == "1"){ //중분류 조회
                    $sql = "select * from category_1 where main_category_idx=".$param["fk_index"];
                    $name_sql = "select * from category_1_name where category_1_idx in (select idx from category_1 where main_category_idx = ".$param["fk_index"].");";
                }else if($category_kind == "2"){ //소분류 조회
                    $sql = "select * from category_2 where category_1_idx=".$param["fk_index"];
                    $name_sql = "select * from category_2_name where category_2_idx in (select idx from category_2 where category_1_idx = ".$param["fk_index"].");";
                }else if($category_kind == "3"){ //세분류 조회
                    $sql = "select * from category_3 where category_2_idx=".$param["fk_index"];
                    $name_sql = "select * from category_3_name where category_3_idx in (select idx from category_3 where category_2_idx = ".$param["fk_index"].");";
                }

                $sql .= " order by sequence asc, idx asc"; //카테고리 sequence로 정렬해서 가져오기

                if($sql != null){
                    $this->result = $this->conn->db_select($sql); //데이터 조회
                    $this->result["category_kind"] = $param["category_kind"]; //js에서 사용할 값 넣기


                    if($this->result["result"] == "1"){ //성공이면 이름 데이터 가져오기
                        $result = $this->conn->db_select($name_sql);
                        if($result["result"] == "0"){ //이름 조회 실패일 경우 결과값을 리턴할 result에 넣기
                            $this->result = $result;
                        }else{
                            //조회가 성공일 경우 idx에 맞게 데이터 정렬
                            $name_data = $result["value"];
                            for($i=0; $i<count($name_data); $i++){
                                $category_idx = $name_data[$i]["category_".$param["category_kind"]."_idx"]; //언어 데이터의 카테고리 idx 
                                for($k=0; $k<count($this->result["value"]); $k++){
                                    if($this->result["value"][$k]["idx"] == $category_idx){ //언어의 메인카테고리와 idx가 같다면
                                        if(isset($this->result["value"][$k]["name_data"])){ //name_data가 있다면 그냥 넣기
                                            array_push($this->result["value"][$k]["name_data"], $name_data[$i]);
                                        }else{ //name_data가 없다면 생성후 넣기
                                            $this->result["value"][$k]["name_data"] = array();
                                            array_push($this->result["value"][$k]["name_data"], $name_data[$i]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }else{
                    $this->result["result"]="0";
                    $this->result["error_code"]="404";
                    $this->result["message"]="알수없는 category_kind 값입니다.";
                }
            }
            
            echo $this->jsonEncode($this->result);
        }


        //랜덤한 n 자리 문자열을 return해주는 함수
        function rand_generateRandomString($leng){
            $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $char_length = strlen($char);
            $randomString = "";

            for($i = 0; $i < $leng; $i++){
                $randomString = $randomString.$char[rand(0, $char_length -1)];
            }

            return $randomString;
        }

        //key 코드 중복 체크
        function product_code_check($table_name){
            $today = date("Ymd");
            if($table_name == "gift"){
                $randomString = $today.$this->rand_generateRandomString(6)."_gift";
            }else{
                $randomString = $today.$this->rand_generateRandomString(6);
            }

            $select_id = "select count(*) as total from $table_name where product_code = ".$this->null_check($randomString);
            $list_result = $this->conn->db_select($select_id);
            $list = $list_result["value"];

            if($list[0]["total"] == 0){
                return $randomString;
            }else{
                return product_code_check();
            }
        }


        /********************************************************************* 
        // 함 수 : 제품 목록 조회 
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_list(){
            $param = $this->param;
            // print_r($param);
            // exit;
            if($this->value_check(array("page_size", "move_page"))){ //필수값 체크
                $project_setting_model = new ProjectSettingModel($this->array); //프로젝트 세팅값을 가져오기위한 모델 생성
                $project_setting_data = $project_setting_model->get_project_setting();
                if($project_setting_data == false){ //프로젝트 셋팅값이 없음
                    $this->result["result"]="0";
                    $this->result["error_code"]="404";
                    $this->result["message"]="프로젝트 셋팅이 설정되어있지 않습니다.";
                }else{//프로젝트 세팅값이 있다면
                    $category_count = $project_setting_data["category_count"]; //설정된 카테고리 갯수

                    $page_size = (int)$param["page_size"];
                    $page = (int)$param["move_page"];

                    $order_by = "";
                    if(!isset($param["orderby"]) || $param["orderby"] == "1"){ //등록순
                        $order_by = "order by regdate desc";
                    }else{ //수정순
                        $order_by = "order by modify_regdate desc";
                    }

                    $like_sql = "";
                    if(isset($param["search_type"]) && isset($param["search_text"])){
                        if($param["search_type"] == "1"){ //상품명 검색
                            $like_sql = "and (t2.product_name LIKE '%".$param["search_text"]."%' ";
                            $like_sql .= "or t2.admin_product_name LIKE '%".$param["search_text"]."%') ";
                        }elseif($param["search_type"] == "2"){ //검색어 검색
                            $like_sql = "and t1.keyword LIKE '%".$param["search_text"]."%' ";
                        }
                    }
                    if($param["tab"] == "total"){//전체 조회면
                        $sql = "select t1.*, t2.product_name, t2.admin_product_name, t2.thumnail_file, t2.state as lang_state from product as t1 left join product_name as t2 on t1.idx = t2.product_idx where t1.is_delete=0 and t2.lang_idx=1 ".$like_sql.$order_by;
                        $sql .= " limit ".$page_size*($page-1).",".$page_size;
                    }else{
                        if($param["tab"] == "indicate"){
                            $kind = "1";
                        }else if($param["tab"] == "not_indicate"){
                            $kind = "2";
                        }
                        $sql = "select t1.*, t2.product_name, t2.admin_product_name, t2.thumnail_file, t2.state as lang_state from product as t1 left join product_name as t2 on t1.idx = t2.product_idx where t1.is_delete=0 and t2.state=".$kind." and t2.lang_idx=1 ".$like_sql.$order_by;
                        $sql .= " limit ".$page_size*($page-1).",".$page_size;
                    }
                    $result = $this->conn->db_select($sql); //제품 목록 데이터 select


                    if(count($result["value"]) > 0){ //제품목록의 데이터가 있다면 해당하는 제품의 카테고리 목록을 조회해서 데이터를 정리해서 넣어줘야함
                        //카테고리 데이터 가져오기
                        $category_sql = null;
                        //category_relation where절 만들기
                        $category_relation_sql = "(";
                        for($i=0; $i<count($result["value"]); $i++){
                            if($i==0){
                                $category_relation_sql = $category_relation_sql." product_category_relation.product_idx=".$result["value"][$i]["idx"];
                            }else{
                                $category_relation_sql = $category_relation_sql." or product_category_relation.product_idx=".$result["value"][$i]["idx"];
                            }
                        }
                        $category_relation_sql = $category_relation_sql.")";

                        // echo $category_relation_sql;
                        if($category_count == 0){ //대분류만 일경우
                            $category_sql = "select product_category_relation.product_idx, main_category_name.name as main_category_name
                            from main_category 
                            left join main_category_name on main_category.idx = main_category_name.main_category_idx
                            left join product_category_relation on main_category.idx = product_category_relation.category_idx
                            where (".$category_relation_sql.") and main_category_name.lang_idx = 1 and main_category_name.lang_idx = 1;";
                        }elseif($category_count == 1){ //중분류까지
                            $category_sql = "select product_category_relation.product_idx, main_category_name.name as main_category_name, category_1_name.name as category_1_name 
                            from category_1 left join category_1_name on category_1.idx = category_1_name.category_1_idx 
                            left join main_category on category_1.main_category_idx = main_category.idx
                            left join main_category_name on main_category.idx = main_category_name.main_category_idx
                            left join product_category_relation on category_1.idx = product_category_relation.category_idx
                            where (".$category_relation_sql.") and category_1_name.lang_idx = 1 and main_category_name.lang_idx = 1;";
                        }elseif($category_count == 2){ //소분류까지
                            //여기 코딩해야함...
                        }elseif($category_count == 3){ //세분류까지
                            //여기 코딩해야함...
                        }

                        $category_result = $this->conn->db_select($category_sql);//카테고리 데이터 select
                        $category_data = $category_result["value"];
                        if(count($category_data) > 0){ //카테고리 데이터가 있다면 해당하는 product_idx에 맞는 제품 목록 데이터에 카테고리 목록을 집어넣기
                            //데이터 정리
                            $sort_data = array();
                            for($i=0; $i<count($category_data); $i++){
                                $data = $category_data[$i]; //카테고리 데이터
                                if(!isset($sort_data[$data["product_idx"]])){ //정리 데이터에 product_idx가 없다면 array객체 생성해서 넣기
                                    $sort_data[$data["product_idx"]] = array();
                                }
                                array_push($sort_data[$data["product_idx"]],$category_data[$i]);
                            }

                            // 정리한 데이터를 제품 목록에 넣기
                            foreach ($sort_data as $key => $value) {
                                for($i=0; $i<count($result["value"]); $i++){
                                    if($result["value"][$i]["idx"] == $key){ //제품 목록의 product_idx와 카테고리 데이터의 product_idx가 같다면 데이터 집어넣기
                                        $result["value"][$i]["category_data"] = $value;
                                    }
                                }
                            }
                        }
                    }
                    
                    if($result["result"] == 0){
                        $this->result = $result;
                    }else{
                        if($param["tab"] == "total"){//전체 조회면
                            $sql = "select count(*) as total_count from product as t1 left join product_name as t2 on t1.idx = t2.product_idx where t1.is_delete=0 and t2.lang_idx=1 ".$like_sql.$order_by;
                        }else{
                            if($param["tab"] == "indicate"){
                                $kind = "1";
                            }else if($param["tab"] == "not_indicate"){
                                $kind = "2";
                            }
                            $sql = "select count(*) as total_count from product as t1 left join product_name as t2 on t1.idx = t2.product_idx where t1.is_delete=0 and t2.state=".$kind." and t2.lang_idx=1 ".$like_sql.$order_by;
                        }
                        $total_result = $this->conn->db_select($sql);

                        $sql = "select count(case when t2.state='1' then 1 end) as indicate,
                        count(case when t2.state='2' then 1 end) as not_indicate
                        from product as t1";
                        $sql .= " left join product_name as t2 on t1.idx = t2.product_idx";
                        $sql .= " where t1.is_delete = 0 and t2.lang_idx = 1";
                        $other_category_result = $this->conn->db_select($sql);


                        if($total_result["result"] == 0){
                            $this->result = $total_result;
                        }else{
                            $this->result = $result;
                            $this->result["total_count"] = $total_result["value"][0]["total_count"];
                            $this->result["other_category_count"] = $other_category_result["value"][0];
                        }
                    }
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 제품 등록 
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_add(){
            $param = $this->param;
            // if(false){ //필수값 체크
            if($this->value_check(array("is_stock", "is_discount"))){ //필수값 체크
                
                $preview_product_idx = $param["preview_product_idx"];
                if($preview_product_idx != "0") {
                    // 데이터 삭제해주기
                    // 썸네일 파일 기본, 오리진 버전 가져와서 담아줄 배열 생성
                    $delete_thumbnail_img = [];
                    $delete_thumbnail_origin_img = [];

                    $delete_sql = "delete from preview_product where idx = $preview_product_idx";
                    $delete_result = $this->conn->db_delete($delete_sql);
                    if($delete_result["result"] == "1") {
                        // preview_product 삭제 성공 -> preview_product_name 삭제 시작
                        // 썸네일파일명 가져와서 배열에 담아주기
                        $preview_thumbnail_sql = "select thumnail_file from preview_product_name where preview_product_idx = $preview_product_idx";
                        $thumbnail_result = $this->conn->db_select($preview_thumbnail_sql);
                        $delete_name_sql = "delete from preview_product_name where preview_product_idx = $preview_product_idx";
                        $this->conn->db_delete($delete_name_sql);

                        for($i = 0; $i < count($thumbnail_result["value"]); $i++) {
                            array_push($delete_thumbnail_img, $this->file_path["product_thumnail_path"].$thumbnail_result["value"][$i]["thumnail_file"]);
                            array_push($delete_thumbnail_origin_img, $this->file_path["product_thumnail_path"].$thumbnail_result["value"][$i]["thumnail_file"]);
                        }
                        // 이미지 삭제요청
                        $this->file_manager->delete_file($delete_thumbnail_img);
                        $this->file_manager->delete_file($delete_thumbnail_origin_img);
                    }

                    // 카테고리 릴레이션 테이블 삭제해주기
                    $category_delete_sql = "delete from preview_product_category_relation where preview_product_idx = $preview_product_idx";
                    $this->conn->db_delete($category_delete_sql);

                    // preview_product_img 테이블 삭제해주기
                    $img_sql = "select * from preview_product_img where preview_product_idx = $preview_product_idx";
                    $img_result = $this->conn->db_select($img_sql);
                    $delete_img = [];
                    $delete_origin_img = [];

                    $delete_img_sql = "delete from preview_product_img where preview_idx = $preview_product_idx";
                    $delete_img_result = $this->conn->db_delete($delete_img_sql);
                    if($delete_img_result["result"] == "1") {
                        // 이미지 테이블 삭제 성공 이미지 파일 삭제해주기
                        for($i = 0; $i < count($img_result["value"]); $i++) {
                            array_push($delete_img, $this->file_path["product_img_path"].$img_result["value"][$i]["file_name"]);
                            array_push($delete_origin_img, $this->file_path["product_img_orign_path"].$img_result["value"][$i]["file_name"]);
                        }
                        // 이미지 삭제요청
                        $this->file_manager->delete_file($delete_img);
                        $this->file_manager->delete_file($delete_origin_img);
                    }

                }
                
                //설정된 언어 조회하기
                $langModel = new LangModel($this->array);
                $lang_data = $langModel->get_lang_list();
                
                $is_discount = $param["is_discount"]; //할인 설정 0:할인없음 1:할인율로 표기 2:금액으로 표기
                $discount_percent = null; //할인퍼센트 
                $discount_price = null; //할인금액

                $is_stock = $param["is_stock"]; //재고관리 0:사용안함 1: 사용
                $total_stock = null; //재고개수

                if($is_discount == "1"){ //할인율이면 할인율 데이터
                    if($this->value_check(array("discount_percent","discount_price"))){ //할인율 파라미터 체크  (아이피아 코스메틱은 할인율료 표기할때 할인금액도 들어가야함)
                        $discount_percent = $param["discount_percent"];
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }elseif($is_discount == "2"){ //할인금액
                    if($this->value_check(array("discount_price"))){ //할인금액 파라미터 체크
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                if($is_stock == "1"){ //재고 사용일 경우 
                    if($this->value_check(array("total_stock"))){ //재고개수 파라미터 체크
                        $total_stock = $param["total_stock"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                //제품 코드 생성
                $product_code = $this->product_code_check("product");

                //product sql을 만든다.
                $sql = null;
                // $sql = "insert into product(price, is_stock, is_discount, regdate, keyword, state"; //제품에 상태값이 있을경우(언어별 상태값 안씀)
                $sql = "insert into product(product_code, is_stock, is_discount, regdate, spec, add_info "; //제품에 상태값이 없을경우(언어별 상태값)
                if($discount_percent != null){ //제품 할인이 퍼센트면
                    $sql = $sql.",discount_percent";
                }
                if($discount_price != null){ //제품 할인이 금액이면
                    $sql = $sql.",discount_price";
                }
                if($total_stock != null){ //재고 사용이면
                    $sql = $sql.",total_stock";
                }
                $sql = $sql.") values(";
                $sql = $sql.$this->null_check($product_code);
                $sql = $sql.",".$param["is_stock"];
                $sql = $sql.",".$param["is_discount"];
                $sql = $sql.","."now()";
                $sql = $sql.",".$this->null_check($param["spec"]);
                $sql = $sql.",".$this->null_check($param["add_info"]);
                // $sql = $sql.",".$param["state"];
                if($discount_percent != null){ //제품 할인이 퍼센트면
                    $sql = $sql.",".$discount_percent;
                }
                if($discount_price != null){ //제품 할인이 금액이면
                    $sql = $sql.",".$discount_price;
                }
                if($total_stock != null){ //재고 사용이면
                    $sql = $sql.",".$total_stock;
                }
                $sql = $sql. ")";
                //product insert (트랜잭션걸어야함)
                $this->conn->s_transaction(); //트랜잭션 시작
                $product_insert_result = $this->conn->db_insert($sql); //product insert
                $product_idx = $product_insert_result["value"]; //insert한 product_idx

                //카테고리 추가하기
                $add_categort_list = json_decode($param["add_category_list"]); //추가해야할 category_idx 배열
                $product_category_relation_sql = "insert into product_category_relation(product_idx,category_idx,sequence) values";
                // print_r($add_categort_list);
                if(count($add_categort_list) > 0){ //카테고리를 넣어야한다면
                    for($i=0; $i<count($add_categort_list); $i++){
                        if($i == 0){ //처음이면 앞에 ,필요없음
                            $product_category_relation_sql = $product_category_relation_sql. "(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }else{
                            $product_category_relation_sql = $product_category_relation_sql. ",(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }
                    }
                    // echo $product_category_relation_sql;
                    $this->conn->db_insert($product_category_relation_sql); //카테고리 릴레이션 insert
                }
                
                //언어별 데이터 넣기
                $product_name_sql = "insert into product_name(short_name, product_idx, product_name, product_info, thumnail_file, description, meta_description, lang_idx, state) values";   //언어별 상태를 넣어야할경우
                // $product_name_sql = "insert into product_name(product_idx, product_name, thumnail_file, description, lang_idx) values";
                $product_img_sql = null; //한 언어당 제품 이미지가 여러개기 때문에 for문 안에서 처음부터 sql문을 만듬
                $description_datas = json_decode($param["description_datas"]); //추가해야할 description 배열
                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)

                for($i=0; $i<count($lang_data); $i++){ //언어별 for문, 여기서 언어 관련된 데이터를 db insert함
                    $lang_idx = strval($lang_data[$i]["idx"]); //현재 언어의 idx,  언어관련 엘리먼트는 언어 idx를 기준으로 key값이 넘어온다.
                    $description = $description_datas[$i]; //언어별 descriptiuon
                    $condition = $param["condition_".$lang_idx]; //상태값 1:정상 2:품절 3:숨김
                    $product_name = $param["product_name_".$lang_idx]; //제품명
                    $short_name = $param["short_name_".$lang_idx]; //제품명
                    $product_info = $param["product_info_".$lang_idx]; //추가정보
                    $thumnail_files = $_FILES["thumnail_file_".$lang_idx]; //썸네일 file array 객체
                    $product_files = $_FILES["product_file_".$lang_idx]; //제품 이미지 file array 객체
                    $meta_description = $param["meta_description_".$lang_idx]; //메타 디스크립션


                    $thumnail_result = $this->file_manager->upload_file($thumnail_files,$this->file_path["product_thumnail_path"], $this->file_path["product_thumnail_orign_path"]); //썸네일 파일 생성
                    $save_files = array_merge($save_files,$thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                    
                    $product_img_result = $this->file_manager->upload_file($product_files,$this->file_path["product_img_path"], $this->file_path["product_img_orign_path"]); //제품이미지 파일 생성
                    $save_files = array_merge($save_files,$product_img_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge

                    $convert_description = $this->file_manager->convert_description($this->file_path["product_description_path"],$this->file_link["product_description_path"], $description); //이미지 저장 및 변환
                    // 최진혁 수정 convert_description에서 배열로 리턴값이 넘어온다("description", "error_file_array");
                    // 저장된 이미지파일 삭제경로 저장
                    $save_files = array_merge($save_files,$convert_description["error_file_array"]);
                    
                    //제품 img sql 만들기
                    if($product_img_sql == null){ //최초 생성일경우 넣음
                        $product_img_sql = "insert into product_img(product_idx, file_name, lang_idx) values";
                        //저장된 제품 이미지 만큼 for문 돌림
                        for($k=0; $k<count($product_img_result["file_name"]); $k++){
                            if($k==0){ //처음이면 앞에 ,안붙임
                                $product_img_sql = $product_img_sql. "(";
                                $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                $product_img_sql = $product_img_sql. ")";
                            }else{
                                $product_img_sql = $product_img_sql. ",(";
                                $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                $product_img_sql = $product_img_sql. ")";
                            }
                        }
                        
                    }else{ //최초 sql생성이 아니면 뒤에 값을 붙이기만 하면됨
                        //저장된 제품 이미지 만큼 for문 돌림
                        for($k=0; $k<count($product_img_result["file_name"]); $k++){
                            $product_img_sql = $product_img_sql. ",(";
                            $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                            $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                            $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                            $product_img_sql = $product_img_sql. ")";
                        }
                    }
                    

                    //제품 name sql 만들기
                    if($i == 0){ //처음 sql문이라면 , 생략
                        $product_name_sql = $product_name_sql."(";
                    }else{
                        $product_name_sql = $product_name_sql.",(";
                    }
                    $product_name_sql = $product_name_sql.$this->null_check($short_name);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_idx);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_name);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_info);
                    $product_name_sql = $product_name_sql.",".$this->null_check($thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    $product_name_sql = $product_name_sql.",".$this->null_check($convert_description["description"]);
                    $product_name_sql = $product_name_sql.",".$this->null_check($meta_description);
                    $product_name_sql = $product_name_sql.",".$this->null_check($lang_idx);
                    $product_name_sql = $product_name_sql.",".$this->null_check($condition);
                    $product_name_sql = $product_name_sql. ")";
                }

                $product_img_sql;  //제품 name sql   null이면 이미지 저장 작업실패한거임
                if($product_name_sql != null){
                    $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                    $this->conn->db_insert($product_name_sql);
                }

                if($product_img_sql != null){
                    $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                    $this->conn->db_insert($product_img_sql);
                }
                // echo $product_info_sql;
                // if($product_info_sql != null){ //추가정보db insert  안쓸경우 주석처리
                //     $this->conn->db_insert($product_info_sql);
                // }

                $this->conn->commit();
                $this->result = $product_insert_result;
            }
            
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 제품 상세 조회
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_detail(){
            $param = $this->param;
            if($this->value_check(array("product_idx"))){
                $project_setting_model = new ProjectSettingModel($this->array); //프로젝트 세팅값을 가져오기위한 모델 생성
                $project_setting_data = $project_setting_model->get_project_setting();

                if($project_setting_data == false){ //프로젝트 셋팅값이 없음
                    $this->result["result"]="0";
                    $this->result["error_code"]="404";
                    $this->result["message"]="프로젝트 셋팅이 설정되어있지 않습니다.";
                }else{//프로젝트 세팅값이 있다면
                    $category_count = $project_setting_data["category_count"]; //설정된 카테고리 갯수
                    //product 조회
                    $product_sql = "select * from product where idx=".$param["product_idx"];
                    $this->result = $this->conn->db_select($product_sql);
                    $product_result = $this->result["value"][0];

                    //제품 언어별 데이터 조회
                    $product_name_sql = "select * from product_name where product_idx=".$param["product_idx"];
                    $product_name_data = $this->conn->db_select($product_name_sql);

                    //제품 카테고리 데이터 조회(프로젝트 세팅의 카테고리 갯수에 따라 sql문이 달라짐)

                    $category_sql = null;
                        
                    //category_relation where절 만들기
                    $category_relation_sql = "(";
                    $category_relation_sql = $category_relation_sql." product_category_relation.product_idx=".$param["product_idx"];

                    $category_relation_sql = $category_relation_sql.")";
                    if($category_count == 0){ //대분류만 일경우
                        $category_sql = "select product_category_relation.idx as relation_idx, main_category_name.name as main_category_name 
                        from main_category left join main_category_name on main_category.idx = main_category_name.main_category_idx 
                        left join product_category_relation on main_category.idx = product_category_relation.category_idx 
                        where (" .$category_relation_sql.") and main_category_name.lang_idx = 1";
                    }elseif($category_count == 1){ //중분류까지
                        $category_sql = "select product_category_relation.idx as relation_idx, category_1_idx as category_idx  ,main_category_name.name as main_category_name, category_1_name.name as category_1_name 
                        from category_1 left join category_1_name on category_1.idx = category_1_name.category_1_idx 
                        left join main_category on category_1.main_category_idx = main_category.idx
                        left join main_category_name on main_category.idx = main_category_name.main_category_idx
                        left join product_category_relation on category_1.idx = product_category_relation.category_idx
                        where (".$category_relation_sql.") and category_1_name.lang_idx = 1 and main_category_name.lang_idx = 1;";
                    }elseif($category_count == 2){ //소분류까지

                    }elseif($category_count == 3){ //세분류까지

                    }
                    
                    $product_relation_data =  $this->conn->db_select($category_sql);

                    //제품 이미지 조회
                    $product_img_sql = "select * from product_img where product_idx=".$param["product_idx"];
                    $product_img_data = $this->conn->db_select($product_img_sql);

                    //조회해온 데이터를 합치기
                    $this->result["value"][0]["lang"] = $product_name_data["value"];
                    $this->result["value"][0]["relation"] = $product_relation_data["value"];
                    $this->result["value"][0]["product_img"] = $product_img_data["value"];
                }
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 제품 수정 
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_modify(){
            $param = $this->param;
            // print_r($_FILES);
            // if(false){ //필수값 체크
            if($this->value_check(array("product_idx", "is_stock", "is_discount"))){ //필수값 체크
                //설정된 언어 조회하기
                $langModel = new LangModel($this->array);
                $lang_data = $langModel->get_lang_list();
                
                $is_discount = $param["is_discount"]; //할인 설정 0:할인없음 1:할인율로 표기 2:금액으로 표기
                $discount_percent = null; //할인퍼센트 
                $discount_price = null; //할인금액

                $is_stock = $param["is_stock"]; //재고관리 0:사용안함 1: 단일재고 2: 옵션재고
                $total_stock = null; //재고개수

                if($is_discount == "1"){ //할인율이면 할인율 데이터
                    if($this->value_check(array("discount_percent","discount_price"))){ //할인율 파라미터 체크  (아이피아 코스메틱은 할인율료 표기할때 할인금액도 들어가야함)
                        $discount_percent = $param["discount_percent"];
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }elseif($is_discount == "2"){ //할인금액
                    if($this->value_check(array("discount_price"))){ //할인금액 파라미터 체크
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                if($is_stock == "1"){ //재고 사용일 경우 
                    if($this->value_check(array("total_stock"))){ //재고개수 파라미터 체크
                        $total_stock = $param["total_stock"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                //product sql을 만든다.
                $sql = null;
                // $sql = "insert into product(price, is_stock, is_discount, regdate, keyword, state"; //제품에 상태값이 있을경우(언어별 상태값 안씀)
                $sql = "update product set "; //제품에 상태값이 없을경우(언어별 상태값)
                $sql = $sql."is_discount=".$param["is_discount"];
                $sql = $sql.","."modify_regdate="."now()";
                $sql = $sql.",spec = ".$this->null_check($param["spec"]);
                $sql = $sql.",add_info = ".$this->null_check($param["add_info"]);
                if($discount_percent != null){ //제품 할인이 퍼센트면
                    $sql = $sql.", discount_percent=".$param["discount_percent"];
                }else{
                    $sql = $sql.", discount_percent=null";
                }
                if($discount_price != null){ //제품 할인이 금액이면
                    $sql = $sql.",discount_price=".$param["discount_price"];
                }else{
                    $sql = $sql.",discount_price=null";
                }
                if($total_stock != null){ //재고 사용이면
                    $sql = $sql.",total_stock=".$param["total_stock"];
                }
                $sql = $sql. " where idx=".$param["product_idx"];

                $product_idx = $param["product_idx"];
                //product update (트랜잭션걸어야함)
                $this->conn->s_transaction(); //트랜잭션 시작

                $product_result = $this->conn->db_update($sql); //product update
                

                // //카테고리 추가하기
                $add_categort_list = json_decode($param["add_category_list"]); //추가해야할 category_idx 배열
                $product_category_relation_sql = "insert into product_category_relation(product_idx,category_idx,sequence) values";
                // print_r($add_categort_list);
                if(count($add_categort_list) > 0){ //카테고리를 넣어야한다면
                    for($i=0; $i<count($add_categort_list); $i++){
                        if($i == 0){ //처음이면 앞에 ,필요없음
                            $product_category_relation_sql = $product_category_relation_sql. "(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }else{
                            $product_category_relation_sql = $product_category_relation_sql. ",(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }
                    }
                    $this->conn->db_insert($product_category_relation_sql); //카테고리 릴레이션 insert
                }

                //삭제해야할 카테고리가 있다면 삭제
                $delete_relation_idx_array = json_decode($param["delete_relation_idx"]); //삭제해야할 product_category_relation idx들을 담은 배열
                if(count($delete_relation_idx_array) > 0){ //삭제할 카테고리가 있다면
                    $category_delete_sql = "delete from product_category_relation where ";
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
                
                //언어별 데이터 넣기
                
                $description_datas = json_decode($param["description_datas"]); //추가해야할 description 배열
                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)

                $delete_decription_files = []; //삭제해야할 description 파일 이름
                $delete_thumnail_files = []; //삭제해야할 썸네일 파일 이름
                for($i=0; $i<count($lang_data); $i++){ //언어별 for문, 여기서 언어 관련된 데이터를 db update함
                    $product_img_sql = null; //한 언어당 제품 이미지가 여러개기 때문에 for문 안에서 처음부터 sql문을 만듬
                   

                    $lang_idx = strval($lang_data[$i]["idx"]); //현재 언어의 idx,  언어관련 엘리먼트는 언어 idx를 기준으로 key값이 넘어온다.
                    $description = $description_datas[$i]; //언어별 descriptiuon
                    $condition = $param["condition_".$lang_idx]; //상태값 1:정상 2:품절 3:숨김
                    $product_name = $param["product_name_".$lang_idx]; //제품명
                    $short_name = $param["short_name_".$lang_idx];
                    $thumnail_files = $_FILES["thumnail_file_".$lang_idx]; //썸네일 file array 객체
                    $product_files = $_FILES["product_file_".$lang_idx]; //제품 이미지 file array 객체

                     //썸네일 삭제 및 description 원본을 가져오기위해 조회
                     $origin_sql = "select * from product_name where product_idx=".$product_idx." and lang_idx=".$lang_idx.";";
                     $origin_result = $this->conn->db_select($origin_sql);
                     $origin_data = $origin_result["value"][0]; //원본 product_name 데이터(언어 1개)
                    

                    $thumnail_result = null;
                    
                    //썸네일 파일이 있는지 확인
                    if($thumnail_files["error"][0] == "0"){
                        //썸네일 파일 있음
                        // echo "썸네일 파일 있음[".$lang_idx."]";
                        $thumnail_result = $this->file_manager->upload_file($thumnail_files,$this->file_path["product_thumnail_path"], $this->file_path["product_thumnail_orign_path"]); //썸네일 파일 생성
                        // print_r($thumnail_result);
                        $save_files = array_merge($save_files,$thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                    }

                    $product_img_result = null;
                    if($product_files["error"][0] == "0"){
                        // echo "제품 추가할 파일이있음";
                        $product_img_result = $this->file_manager->upload_file($product_files,$this->file_path["product_img_path"], $this->file_path["product_img_orign_path"]); //제품이미지 파일 생성
                        $save_files = array_merge($save_files,$product_img_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                    }
                    
                    $convert_description = $this->file_manager->convert_description($this->file_path["product_description_path"],$this->file_link["product_description_path"], $description); //이미지 저장 및 변환
                    $new_file_array = $this->file_manager->get_s3_image_array($convert_description["description"],$this->file_link["product_description_path"]); //새로운 description의 이미지 파일 목록
                    $origin_file_array = $this->file_manager->get_s3_image_array($origin_data["description"],$this->file_link["product_description_path"]); //원본 description의 이미지 파일 목록
                    
                    $delete_file = array_diff($origin_file_array, $new_file_array); //삭제해야할 이미지 파일가져옴(배열1 값중 배열2에 없는 값만 배열 형태로 반환.)
                    $delete_decription_files = array_merge($delete_decription_files,$delete_file); //삭제해야할 description파일 추가(마지막에 파일삭제로직에서 삭제할것임)


                    // 최진혁 수정 convert_description에서 배열로 리턴값이 넘어온다("description", "error_file_array");
                    // 저장된 이미지파일 삭제경로 저장
                    $save_files = array_merge($save_files,$convert_description["error_file_array"]);
                    
                    //제품 img sql 만들기
                    if($product_img_result != null){ //생성해야할 제품 이미지가 있다면
                        if($product_img_sql == null){ //최초 생성일경우 넣음
                            $product_img_sql = "insert into product_img(product_idx, file_name, lang_idx) values";
                            //저장된 제품 이미지 만큼 for문 돌림
                            for($k=0; $k<count($product_img_result["file_name"]); $k++){
                                if($k==0){ //처음이면 앞에 ,안붙임
                                    $product_img_sql = $product_img_sql. "(";
                                    $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                    $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                    $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                    $product_img_sql = $product_img_sql. ")";
                                }else{
                                    $product_img_sql = $product_img_sql. ",(";
                                    $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                    $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                    $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                    $product_img_sql = $product_img_sql. ")";
                                }
                            }
                            
                        }else{ //최초 sql생성이 아니면 뒤에 값을 붙이기만 하면됨
                            //저장된 제품 이미지 만큼 for문 돌림
                            for($k=0; $k<count($product_img_result["file_name"]); $k++){
                                $product_img_sql = $product_img_sql. ",(";
                                $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                $product_img_sql = $product_img_sql. ")";
                            }
                        }
                    }

                    $product_name_sql = "update product_name set ";   //언어별 상태를 넣어야할경우
                    
                    $product_name_sql = $product_name_sql."product_name=".$this->null_check($product_name);
                    $product_name_sql = $product_name_sql.",short_name=".$this->null_check($short_name);
                    if($thumnail_result != null){ //썸네일 파일 변경됨
                        $product_name_sql = $product_name_sql.",thumnail_file=".$this->null_check($thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                        array_push($delete_thumnail_files,$origin_data["thumnail_file"]);
                    }
                    $product_name_sql = $product_name_sql.",description=".$this->null_check($convert_description["description"]);
                    $product_name_sql = $product_name_sql.",state=".$this->null_check($condition);
                    $product_name_sql = $product_name_sql. " where product_idx=".$this->null_check($product_idx)." and lang_idx=".$lang_idx;

                    if($product_name_sql != null){
                        $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                        $this->conn->db_update($product_name_sql);
                    }

                    if($product_img_sql != null){
                        $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                        $this->conn->db_insert($product_img_sql);
                    }
                }

                //제품이미지 sql 삭제
                $delete_product_img_idx = json_decode($param["delete_product_img_idx"]); //삭제해야할 제품이미지 product_idx
                if(count($delete_product_img_idx) > 0){ //지워야할 파일 product_idx가 있다면
                    $product_img_delete_sql = "delete from product_img where ";
                    for($i=0; $i<count($delete_product_img_idx); $i++){
                        if($i==0){
                            $product_img_delete_sql = $product_img_delete_sql."idx=".$this->null_check($delete_product_img_idx[$i]);
                        }else{
                            $product_img_delete_sql = $product_img_delete_sql." or idx=".$this->null_check($delete_product_img_idx[$i]);
                        }
                    }
                    
                    $this->conn->db_delete($product_img_delete_sql);
                }

                $this->conn->commit();
                $this->result = $product_result;


                //지워야할 파일 삭제 로직 및 sql delete문 실행
                $delete_decription_files; //삭제해야할 description 파일 이름
                for($i=0; $i<count($delete_decription_files); $i++){
                    $delete_decription_files[$i] = $this->file_path["product_description_path"].$delete_decription_files[$i]; //full 경로로 변경
                }
                $this->file_manager->delete_file($delete_decription_files); //삭제요청

                //썸네일은 구매당시의 썸네일 파일 이름이 남아있어야하기때문에 주석처리(수정시 기존 썸네일은 삭제해야하면 주석해제하면됨)
                // $delete_thumnail_files; //삭제해야할 썸네일 파일 이름
                // $delete_thumnail_origin_files = []; //썸네일 파일 오리지날 파일 목록
                // for($i=0; $i<count($delete_thumnail_files); $i++){
                //     array_push($delete_thumnail_origin_files,$this->file_path["product_thumnail_orign_path"].$delete_thumnail_files[$i]); //오리진 full 경로
                //     $delete_thumnail_files[$i] = $this->file_path["product_thumnail_path"].$delete_thumnail_files[$i]; //full 경로로 변경
                // }
                // $this->file_manager->delete_file($delete_thumnail_origin_files); //삭제요청
                // $this->file_manager->delete_file($delete_thumnail_files); //삭제요청


                $delete_product_img_name = json_decode($param["delete_product_img_name"]); //삭제해야할 제품 이미지 파일 목록
                $delete_product_origin_img_name = []; //원본 이미지 경로
                for($i=0; $i<count($delete_product_img_name); $i++){
                    array_push($delete_product_origin_img_name,$this->file_path["product_img_orign_path"].$delete_product_img_name[$i]); //오리진 full 경로
                    $delete_product_img_name[$i] = $this->file_path["product_thumnail_path"].$delete_product_img_name[$i]; //full 경로로 변경
                }
                $this->file_manager->delete_file($delete_product_img_name); //삭제요청
                $this->file_manager->delete_file($delete_product_origin_img_name); //삭제요청

                
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 카테고리 이름 수정
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_category_modifiy(){
            $param = $this->param;
            if($this->value_check(array("category_idx","kind","names"))){
                

                $this->conn->s_transaction();
                if($param["kind"] == 0){
                    $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)
                    
                    // $background_files = $_FILES["thumnail_file"];
                    // $background_result = $this->file_manager->upload_file($background_files,$this->file_path["category_img_path"], $this->file_path["category_img_orign_path"]); //썸네일 파일 생성
                    // $save_files = array_merge($save_files,$background_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                    // $this->conn->set_file_list($save_files);
                }
                $name_array = json_decode($param["names"], true);
                for($i = 0; $i < count($name_array); $i++){
                    //대분류 카테고리명 수정
                    if($param["kind"] == 0){
                        $update_table = "main_category_name";
                        $where_key = "main_category_idx";
                    }else if($param["kind"] == 1){
                        //중분류 카테고리명 수정
                        $update_table = "category_1_name";
                        $where_key = "category_1_idx";
                    }else if($param["kind"] == 2){
                        //소분류 카테고리명 수정
                        $update_table = "category_2_name";
                        $where_key = "category_2_idx";
                    }else if($param["kind"] == 3){
                        //세분류 카테고리명 수정
                        $update_table = "category_3_name";
                        $where_key = "category_3_idx";
                    }

                    if($param["kind"] == 0){
                        if($param["delete_category_index"] != 0){
                            $sql = "select background_image from " . $update_table . " ";
                            $sql .= "where ". $where_key . " = " . $param["category_idx"];
                            $sql .= " and lang_idx = " . ($i + 1);

                            $main_category_result = $this->conn->db_select($sql);
                            if($main_category_result["result"] == 0){
                                $this->result = $main_category_result;
                            }else{
                                $delete_file = array();
                                if(count($main_category_result["value"]) > 0){
                                    // $del_image = $main_category_result["value"][0]["background_image"];
                                    // array_push($delete_file , $this->file_path["category_img_path"].$del_image);
                                    // array_push($delete_file , $this->file_path["category_img_orign_path"].$del_image);
                                }
                                // $this->file_manager->delete_file($delete_file);
                            }

                            if(!empty($param["sub_category_name"])){
                                $sql = "update $update_table set ";
                                $sql .= " name = ".$this->null_check($name_array[$i]) . ", ";
                                $sql .= " background_image = null, ";
                                $sql .= " sub_category_name = ".$this->null_check($param["sub_category_name"]) . " ";
                                $sql .= " where $where_key = ".$param["category_idx"];
                                $sql .= " and lang_idx = ".($i + 1);
                            }else{
                                $sql = "update $update_table set ";
                                $sql .= " name = ".$this->null_check($name_array[$i]) . ", ";
                                $sql .= " background_image = null ";
                                $sql .= " where $where_key = ".$param["category_idx"];
                                $sql .= " and lang_idx = ".($i + 1);
                            }
                        }else{
                            if(!empty($param["sub_category_name"])){
                                $sql = "update $update_table set ";
                                $sql .= " name = ".$this->null_check($name_array[$i]) . ", ";
                                $sql .= " sub_category_name = ".$this->null_check($param["sub_category_name"]) . " ";
                                $sql .= " where $where_key = ".$param["category_idx"];
                                $sql .= " and lang_idx = ".($i + 1);
                            }else{
                                $sql = "update $update_table set ";
                                $sql .= " name = ".$this->null_check($name_array[$i]) . " ";
                                $sql .= " where $where_key = ".$param["category_idx"];
                                $sql .= " and lang_idx = ".($i + 1);
                            }
                        }
                    }else{
                        if(!empty($param["sub_category_name"])){

                            $sql = "update $update_table set ";
                            $sql .= " name = ".$this->null_check($name_array[$i]) . ", ";
                            $sql .= " sub_category_name = ".$this->null_check($param["sub_category_name"]) . " ";
                            $sql .= " where $where_key = ".$param["category_idx"];
                            $sql .= " and lang_idx = ".($i + 1);
                        }else{
                            $sql = "update $update_table set name = ".$this->null_check($name_array[$i]);
                            $sql .= " where $where_key = ".$param["category_idx"];
                            $sql .= " and lang_idx = ".($i + 1);
                        }
                    }
                    
                    $this->result = $this->conn->db_update($sql);
                }
                $this->conn->commit();
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 카테고리 순서변경
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_category_relation_change(){
            $param = $this->param;
            if($this->value_check(array("relation_array"))){
                $relation_array = json_decode($param["relation_array"]);
                $sql = "UPDATE main_category SET sequence = CASE idx";
                $where_sql = "";
                for($i=1; $i<=count($relation_array); $i++){
                    $sql = $sql." WHEN ".$relation_array[$i-1]." THEN ".$i;
                    if($i==1){
                        $where_sql = $where_sql.$relation_array[$i-1];
                    }else{
                        $where_sql = $where_sql.",".$relation_array[$i-1];
                    }
                }
                $sql = $sql." END ";
                $sql = $sql." WHERE idx IN (".$where_sql.");";
                
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 미리보기용 임시 저장하기
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_product_temp_add(){
            $param = $this->param;
            // print_r($_FILES);
            // if(false){ //필수값 체크
            if($this->value_check(array("is_stock", "is_discount"))){ //필수값 체크
                $tab_category_idx = $param["category_flag"];

                // param값으로 넘어온 preview_product_idx 값이 0이면 처음 미리보기 누르는거니까 바로 임시저장 해주고
                // 처음이 아니면 미리보기 눌렀을때 임시 저장한 preview_product_idx를 담아넣음
                // 0이 아니면 이전에 한번 눌러서 임시저장된 데이터가 있으니까 그거 지우고 새로 넣어준다.
                $preview_product_idx = $param["preview_product_idx"];
                if($preview_product_idx != "0") {
                    // 데이터 삭제해주기
                    // 썸네일 파일 기본, 오리진 버전 가져와서 담아줄 배열 생성
                    $delete_thumbnail_img = [];
                    $delete_thumbnail_origin_img = [];

                    $delete_sql = "delete from preview_product where idx = $preview_product_idx";
                    $delete_result = $this->conn->db_delete($delete_sql);
                    if($delete_result["result"] == "1") {
                        // preview_product 삭제 성공 -> preview_product_name 삭제 시작
                        // 썸네일파일명 가져와서 배열에 담아주기
                        $preview_thumbnail_sql = "select thumnail_file from preview_product_name where preview_product_idx = $preview_product_idx";
                        $thumbnail_result = $this->conn->db_select($preview_thumbnail_sql);
                        $delete_name_sql = "delete from preview_product_name where preview_product_idx = $preview_product_idx";
                        $this->conn->db_delete($delete_name_sql);

                        for($i = 0; $i < count($thumbnail_result["value"]); $i++) {
                            array_push($delete_thumbnail_img, $this->file_path["product_thumnail_path"].$thumbnail_result["value"][$i]["thumnail_file"]);
                            array_push($delete_thumbnail_origin_img, $this->file_path["product_thumnail_path"].$thumbnail_result["value"][$i]["thumnail_file"]);
                        }
                        // 이미지 삭제요청
                        $this->file_manager->delete_file($delete_thumbnail_img);
                        $this->file_manager->delete_file($delete_thumbnail_origin_img);
                    }

                    // 카테고리 릴레이션 테이블 삭제해주기
                    $category_delete_sql = "delete from preview_product_category_relation where preview_product_idx = $preview_product_idx";
                    $this->conn->db_delete($category_delete_sql);

                    // preview_product_img 테이블 삭제해주기
                    $img_sql = "select * from preview_product_img where preview_product_idx = $preview_product_idx";
                    $img_result = $this->conn->db_select($img_sql);
                    $delete_img = [];
                    $delete_origin_img = [];

                    $delete_img_sql = "delete from preview_product_img where preview_idx = $preview_product_idx";
                    $delete_img_result = $this->conn->db_delete($delete_img_sql);
                    if($delete_img_result["result"] == "1") {
                        // 이미지 테이블 삭제 성공 이미지 파일 삭제해주기
                        for($i = 0; $i < count($img_result["value"]); $i++) {
                            array_push($delete_img, $this->file_path["product_img_path"].$img_result["value"][$i]["file_name"]);
                            array_push($delete_origin_img, $this->file_path["product_img_orign_path"].$img_result["value"][$i]["file_name"]);
                        }
                        // 이미지 삭제요청
                        $this->file_manager->delete_file($delete_img);
                        $this->file_manager->delete_file($delete_origin_img);
                    }

                }


                //설정된 언어 조회하기
                $langModel = new LangModel($this->array);
                $lang_data = $langModel->get_lang_list();
                
                $is_discount = $param["is_discount"]; //할인 설정 0:할인없음 1:할인율로 표기 2:금액으로 표기
                $discount_percent = null; //할인퍼센트 
                $discount_price = null; //할인금액

                $is_stock = $param["is_stock"]; //재고관리 0:사용안함 1: 사용
                $total_stock = null; //재고개수

                if($is_discount == "1"){ //할인율이면 할인율 데이터
                    if($this->value_check(array("discount_percent","discount_price"))){ //할인율 파라미터 체크  (아이피아 코스메틱은 할인율료 표기할때 할인금액도 들어가야함)
                        $discount_percent = $param["discount_percent"];
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }elseif($is_discount == "2"){ //할인금액
                    if($this->value_check(array("discount_price"))){ //할인금액 파라미터 체크
                        $discount_price = $param["discount_price"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                if($is_stock == "1"){ //재고 사용일 경우 
                    if($this->value_check(array("total_stock"))){ //재고개수 파라미터 체크
                        $total_stock = $param["total_stock"];
                    }else{
                        //필수값없음
                        echo $this->jsonEncode($this->result);
                        exit;
                    }
                }

                //제품 코드 생성
                $product_code = $this->product_code_check("product");

                //product sql을 만든다.
                $sql = null;
                // $sql = "insert into product(price, is_stock, is_discount, regdate, keyword, state"; //제품에 상태값이 있을경우(언어별 상태값 안씀)
                $sql = "insert into preview_product(product_code, is_stock, is_discount, regdate, spec, add_info "; //제품에 상태값이 없을경우(언어별 상태값)
                if($discount_percent != null){ //제품 할인이 퍼센트면
                    $sql = $sql.",discount_percent";
                }
                if($discount_price != null){ //제품 할인이 금액이면
                    $sql = $sql.",discount_price";
                }
                if($total_stock != null){ //재고 사용이면
                    $sql = $sql.",total_stock";
                }
                $sql = $sql.") values(";
                $sql = $sql.$this->null_check($product_code);
                $sql = $sql.",".$param["is_stock"];
                $sql = $sql.",".$param["is_discount"];
                $sql = $sql.","."now()";
                $sql = $sql.",".$this->null_check($param["spec"]);
                $sql = $sql.",".$this->null_check($param["add_info"]);
                // $sql = $sql.",".$param["state"];
                if($discount_percent != null){ //제품 할인이 퍼센트면
                    $sql = $sql.",".$discount_percent;
                }
                if($discount_price != null){ //제품 할인이 금액이면
                    $sql = $sql.",".$discount_price;
                }
                if($total_stock != null){ //재고 사용이면
                    $sql = $sql.",".$total_stock;
                }
                $sql = $sql. ")";
                //product insert (트랜잭션걸어야함)
                $this->conn->s_transaction(); //트랜잭션 시작
                $product_insert_result = $this->conn->db_insert($sql); //product insert
                $product_idx = $product_insert_result["value"]; //insert한 product_idx

                //카테고리 추가하기
                $add_categort_list = json_decode($param["add_category_list"]); //추가해야할 category_idx 배열
                $product_category_relation_sql = "insert into preview_product_category_relation(preview_product_idx,category_idx,sequence) values";
                // print_r($add_categort_list);
                if(count($add_categort_list) > 0){ //카테고리를 넣어야한다면
                    for($i=0; $i<count($add_categort_list); $i++){
                        if($i == 0){ //처음이면 앞에 ,필요없음
                            $product_category_relation_sql = $product_category_relation_sql. "(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }else{
                            $product_category_relation_sql = $product_category_relation_sql. ",(";
                            $product_category_relation_sql = $product_category_relation_sql.$this->null_check($product_idx);
                            $product_category_relation_sql = $product_category_relation_sql.",".$this->null_check($add_categort_list[$i]);
                            $product_category_relation_sql = $product_category_relation_sql. ",(select AUTO_INCREMENT from information_schema.tables where table_name = 'product_category_relation' and table_schema = database()))";
                        }
                    }
                    // echo $product_category_relation_sql;
                    $this->conn->db_insert($product_category_relation_sql); //카테고리 릴레이션 insert
                }
                
                //언어별 데이터 넣기
                $product_name_sql = "insert into preview_product_name(short_name, preview_product_idx, product_name, product_info, thumnail_file, meta_description, lang_idx, state) values";   //언어별 상태를 넣어야할경우
                // $product_name_sql = "insert into product_name(product_idx, product_name, thumnail_file, description, lang_idx) values";
                $product_img_sql = null; //한 언어당 제품 이미지가 여러개기 때문에 for문 안에서 처음부터 sql문을 만듬
                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)

                for($i=0; $i<count($lang_data); $i++){ //언어별 for문, 여기서 언어 관련된 데이터를 db insert함
                    $lang_idx = strval($lang_data[$i]["idx"]); //현재 언어의 idx,  언어관련 엘리먼트는 언어 idx를 기준으로 key값이 넘어온다.
                    $condition = $param["condition_".$lang_idx]; //상태값 1:정상 2:품절 3:숨김
                    $product_name = $param["product_name_".$lang_idx]; //제품명
                    $short_name = $param["short_name_".$lang_idx]; //제품명
                    $product_info = $param["product_info_".$lang_idx]; //추가정보
                    $thumnail_files = $_FILES["thumnail_file_".$lang_idx]; //썸네일 file array 객체
                    $product_files = $_FILES["product_file_".$lang_idx]; //제품 이미지 file array 객체
                    $meta_description = $param["meta_description_".$lang_idx]; //메타 디스크립션


                    $thumnail_result = $this->file_manager->upload_file($thumnail_files,$this->file_path["product_thumnail_path"], $this->file_path["product_thumnail_orign_path"]); //썸네일 파일 생성
                    $save_files = array_merge($save_files,$thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                    
                    $product_img_result = $this->file_manager->upload_file($product_files,$this->file_path["product_img_path"], $this->file_path["product_img_orign_path"]); //제품이미지 파일 생성
                    $save_files = array_merge($save_files,$product_img_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge

                    // 최진혁 수정 convert_description에서 배열로 리턴값이 넘어온다("description", "error_file_array");
                    // 저장된 이미지파일 삭제경로 저장

					//제품 img sql 만들기
                    if($product_img_sql == null){ //최초 생성일경우 넣음
                        $product_img_sql = "insert into product_img(preview_product_idx, file_name, lang_idx) values";
                        //저장된 제품 이미지 만큼 for문 돌림
                        for($k=0; $k<count($product_img_result["file_name"]); $k++){
                            if($k==0){ //처음이면 앞에 ,안붙임
                                $product_img_sql = $product_img_sql. "(";
                                $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                $product_img_sql = $product_img_sql. ")";
                            }else{
                                $product_img_sql = $product_img_sql. ",(";
                                $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                                $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                                $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                                $product_img_sql = $product_img_sql. ")";
                            }
                        }
                        
                    }else{ //최초 sql생성이 아니면 뒤에 값을 붙이기만 하면됨
                        //저장된 제품 이미지 만큼 for문 돌림
                        for($k=0; $k<count($product_img_result["file_name"]); $k++){
                            $product_img_sql = $product_img_sql. ",(";
                            $product_img_sql = $product_img_sql.$this->null_check($product_idx);
                            $product_img_sql = $product_img_sql.",".$this->null_check($product_img_result["file_name"][$k]);
                            $product_img_sql = $product_img_sql.",".$this->null_check($lang_idx);
                            $product_img_sql = $product_img_sql. ")";
                        }
                    }


                    
                    //제품 name sql 만들기
                    if($i == 0){ //처음 sql문이라면 , 생략
                        $product_name_sql = $product_name_sql."(";
                    }else{
                        $product_name_sql = $product_name_sql.",(";
                    }
                    $product_name_sql = $product_name_sql.$this->null_check($short_name);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_idx);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_name);
                    $product_name_sql = $product_name_sql.",".$this->null_check($product_info);
                    $product_name_sql = $product_name_sql.",".$this->null_check($thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    $product_name_sql = $product_name_sql.",".$this->null_check($meta_description);
                    $product_name_sql = $product_name_sql.",".$this->null_check($lang_idx);
                    $product_name_sql = $product_name_sql.",".$this->null_check($condition);
                    $product_name_sql = $product_name_sql. ")";
                }

                $product_img_sql;  //제품 name sql   null이면 이미지 저장 작업실패한거임
                if($product_name_sql != null){
                    $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                    $this->conn->db_insert($product_name_sql);
                }

				if($product_img_sql != null){
                    $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                    $this->conn->db_insert($product_img_sql);
                }

                $this->conn->commit();
                $this->result = $product_insert_result;
                $this->result["tab"] = $tab_category_idx;
            }
            
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 제품 삭제
        // 설 명 : 실제 delete를 하지않고 is_delete를 1로 변경
        // 만든이: 안정환
        *********************************************************************/
        function request_delete_product(){
            $param = $this->param;
            if($this->value_check(array("product_idx"))){
                $sql = "update product set is_delete=1 where idx=".$param["product_idx"];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 카테고리 relation(릴레이션) 제품 조회
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_category_relation(){
            $param = $this->param;
            if($this->value_check(array("category_idx"))){
                $sql = "select t1.idx as relation_idx,t2.*, t3.product_name, t3.admin_product_name, t3.thumnail_file
                from product_category_relation as t1 left join product as t2 on t1.product_idx = t2.idx 
                left join product_name as t3 on t1.product_idx = t3.product_idx
                where t3.lang_idx=1 and t1.category_idx=".$param["category_idx"]." and t2.is_delete=0 group by t2.idx order by t1.sequence asc;";

                $this->result = $this->conn->db_select($sql);
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 카테고리 relation(릴레이션) 제품 순서 변경
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_product_categort_relation_change(){
            $param = $this->param;
            if($this->value_check(array("relation_array"))){
                $relation_array = json_decode($param["relation_array"]);
                $sql = "UPDATE product_category_relation SET sequence = CASE idx";
                $where_sql = "";
                for($i=1; $i<=count($relation_array); $i++){
                    $sql = $sql." WHEN ".$relation_array[$i-1]." THEN ".$i;
                    if($i==1){
                        $where_sql = $where_sql.$relation_array[$i-1];
                    }else{
                        $where_sql = $where_sql.",".$relation_array[$i-1];
                    }
                }
                $sql = $sql." END ";
                $sql = $sql." WHERE idx IN (".$where_sql.");";
                
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        } 

    }
?>