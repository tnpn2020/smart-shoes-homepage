<?php
    class AdminMenu1Model extends gf{
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
            $this->sumnote = $array["sumnote"];
            $this->conn = $array["db"];
            $this->file_manager = $array["file_manager"];
            $this->file_path = $array["file_path"]->get_path_php();
            $this->file_link = $array["file_path"]->get_link_php();

            //서브 path
            $this->file_path = array_merge($this->file_path,$array["sub_file_path"]->get_path_php());
            $this->file_link = array_merge($this->file_link,$array["sub_file_path"]->get_link_php());

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
        // 함 수 : 팝업 상세정보를 가져오는 함수 (수정시 사용)
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_popup_detail(){
            $param = $this->param;
            if($this->value_check(array("idx"))){
                $sql = "select * from popup where idx = ".$param["idx"];
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }
            }
            echo $this->jsonEncode($this->result);
        }        


        /********************************************************************* 
        // 함 수 : 팝업 수정 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function modify_popup(){
            $param = $this->param;
            if($this->value_check(array("idx", "upload_img"))){
                $sql = "select * from popup where idx = ".$param["idx"];
                $popup_result = $this->conn->db_select($sql);
                $orign_img_array = array();
                for($i = 0; $i < count($popup_result["value"]); $i++){
                    array_push($orign_img_array, $popup_result["value"][$i]["pc_file_name"]);
                }

                //수정시 사용할 이미지 배열
                $upload_img = json_decode($param["upload_img"]);
                //삭제해야할 이미지 배열
                $diff_array = array_diff($orign_img_array, $upload_img);

                $pc_files = $_FILES["pc_popup_files"]; //pc 이미지 파일 객체
                $pc_result = $this->file_manager->upload_file($pc_files,$this->file_path["pc_popup_img_path"], $this->file_path["pc_popup_img_origin_path"]); //파일생성

                
                //업로드한 이미지가 없는 경우 ---> 이미지를 수정하지 않은 경우
                //업로드되는 파일명을 기존 파일명으로 설정
                $upload_file_name = null;
                if(count($pc_result["file_name"]) == 0){
                    $upload_file_name = $popup_result["value"][0]["pc_file_name"];
                }else{
                    $upload_file_name = $pc_result["file_name"][0];
                }

                $sql = "update popup set ";
                $sql .= "name = ".$this->null_check($param["popup_name"]).",";
                $sql .= "link = ".$this->null_check($param["link"]).",";
                $sql .= "is_use = ".$param["is_use"].",";
                $sql .= "pc_file_name = ".$this->null_check($upload_file_name)." ";
                $sql .= "where idx = ".$param["idx"];
                $result = $this->conn->db_update($sql);

                if($result["result"] == "1"){
                    $this->result = $result;
                }else{
                    $this->result = $result;
                }

                echo $this->jsonEncode($this->result);
            }
        }
        

        /********************************************************************* 
        // 함 수 : 팝업 목록 조회
        // 설 명 : 사용 상태인 배너를 가장 먼저 들고옴
        // 만든이: 조경민
        *********************************************************************/
        function request_popup_list(){
            $param = $this->param;
            if($this->value_check(array("lang"))){
                $sql = "select * from popup order by sequence asc";
                
                $this->result = $this->conn->db_select($sql);
            }
            

            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 팝업 등록 
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_popup_reg(){
            $param = $this->param;
            $pc_files = $_FILES["pc_popup_files"]; //pc 이미지 파일 객체
            $pc_result = $this->file_manager->upload_file($pc_files,$this->file_path["pc_popup_img_path"], $this->file_path["pc_popup_img_origin_path"]); //파일생성


            $error_delete_array = array();//쿼리 실패시 삭제해야할 파일 목록
            array_push($error_delete_array,$pc_result["error_file_array"]);
            $sql = null;

            if($param["is_use"] == 1){
                //팝업 사용 상태로 등록시 등록되어 있던 팝업의 사용 상태를 조회하여 사용중인 팝업이 3개보다 많으면
                //사용 상태를 미사용으로 등록시키기
                $sql = "select count(*) as count from popup where is_use = 1"; //현재 사용중인 팝업의 수를 가져와서 3개면 변경을 막음
                $result = $this->conn->db_select($sql);
                if($result["value"][0]["count"] >= 3){
                    $param["is_use"] = 0;
                }
            }
            
            $sql = "insert into popup(name, pc_file_name, link, is_use, regdate) values(";
            $sql .= $this->null_check($param["popup_name"]).",";
            $sql .= $this->null_check($pc_result["file_name"][0]).",";
            $sql .= $this->null_check($param["link"]).",";
            $sql .= $param["is_use"].",";
            $sql .= "now()".")";

            $this->conn->set_file_list($error_delete_array); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
            $this->result = $this->conn->db_insert($sql);
            
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 팝업 삭제
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_delete_popup(){
            $param = $this->param;
            if($this->value_check(array("delete_idxs"))){
                $delete_idxs = json_decode($param["delete_idxs"]);
                //삭제할 배너 조회하기
                $sql = "select * from popup where (";
                $delete_sql = "delete from popup where (";
                for($i=0; $i<count($delete_idxs); $i++){
                    if($i==0){
                        $sql = $sql."idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql."idx=".$delete_idxs[$i];
                    }else{
                        $sql = $sql." or idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql." or idx=".$delete_idxs[$i];
                    }
                }
                $sql = $sql.") ";
                $delete_sql =$delete_sql.") ";
                $result = $this->conn->db_select($sql);
                $delete_popup_datas = $result["value"];
                if(count($delete_popup_datas) == 0){ //삭제할 데이터가 없다면
                    $this->result["result"] = "0";
                    $this->result["message"] = "삭제할 팝업이 없습니다";
                }else{
                    //삭제해야할 이미지 목록 만들기
                    $delete_pc_images = array();
                    $delete_pc_origin_images = array();

                    for($i=0; $i<count($delete_popup_datas); $i++){
                        array_push($delete_pc_images,$this->file_path["pc_popup_img_path"].$delete_popup_datas[$i]["pc_file_name"]);
                        array_push($delete_pc_origin_images,$this->file_path["pc_popup_img_origin_path"].$delete_popup_datas[$i]["pc_file_name"]);
                    }

                    //삭제해야할 파일 목록을 만들었으면 sql delete
                    $this->result = $this->conn->db_delete($delete_sql);
                    $this->file_manager->delete_file($delete_pc_images); //삭제요청
                    $this->file_manager->delete_file($delete_pc_origin_images); //삭제요청
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 팝업 순서 변경
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_popup_relation_change(){
            $param = $this->param;
            if($this->value_check(array("relation_array"))){
                $relation_array = json_decode($param["relation_array"]);
                $sql = "UPDATE popup SET sequence = CASE idx";
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
        // 함 수 : 팝업 사용상태 변경
        // 설 명 : 팝업은 하나만 사용가능하므로 사용상태 변경시 나머지 모든 팝업의
                   사용상태를 미사용으로 변경하고 변경한 배너의 사용여부만 변경
        // 만든이: 조경민
        *********************************************************************/
        function request_popup_is_use_change(){
            $param = $this->param;
            if($this->value_check(array("popup_idx", "is_use", "lang_idx"))){
                $sql = "select count(*) as count from popup where is_use = 1 and lang_idx = ".$param["lang_idx"]; //현재 사용중인 팝업의 수를 가져와서 3개면 변경을 막음
                $result = $this->conn->db_select($sql);
                if($result["result"] == 1){
                    $this->result = $result;
                    //팝업이 3개보다 많으면 팝업 상태 변경 막음
                    if($result["value"][0]["count"] >= 3 && $param["is_use"] == 1){
                        $this->result["message"] = "팝업은 3개까지 사용 가능합니다.";
                        $this->result["flag"] = 0;
                    }else{
                        //사용자가 선택한 팝업만 넘어온 상태로 변경
                        $sql = "UPDATE popup SET is_use=".$param["is_use"]." where idx=".$param["popup_idx"];
                        $this->result = $this->conn->db_update($sql);
                        $this->result["message"] = "적용되었습니다.";
                        $this->result["flag"] = 1;
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["message"] = "팝업 검색 실패";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        
        /********************************************************************* 
        // 함 수 : 개인정보방침 or 이용약관 설정 페이지에서 setting된 언어 list를 가져오는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_lang_list(){
            $param = $this->param;
            if($this->value_check(array("terms_idx"))){
                $sql = "select idx, name from lang";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "lang table 검색 성공";
                    $sql = "select t1.content, t1.lang_idx from terms_name as t1 left join terms as t2 on t1.terms_idx = t2.idx where t1.terms_idx = ".$param["terms_idx"];
                    $result = $this->conn->db_select($sql);
                    if($result["result"] == "1"){
                        $this->result["terms"] = $result["value"];
                        $this->result["message"] = "terms_name table 검색 성공";
                    }
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "lang table 검색 실패";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 개인정보 방침 or 이용약관 page에서 선택된 언어에 맞게 
        //         terms 테이블에 입력한 내용 저장하는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function register_terms(){
            $param = $this->param;
            if($this->value_check(array("terms_data", "terms_idx"))){
                $terms_data = json_decode($param["terms_data"]); //0 ---> content , 1 ---> lang_idx
                //terms_name table을 조회하여 데이터가 있으면 update , 없으면 insert
                $sql = "select * from terms_name where terms_idx = ".$param["terms_idx"]." and (";
                for($i = 0; $i < count($terms_data); $i++){
                    if($i == 0){
                        $sql .= "lang_idx = ".$terms_data[$i][1];
                    }else{
                        $sql .= " or lang_idx = ".$terms_data[$i][1];
                    }
                }
                $sql .= ")";
                // echo $sql;
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "언어별 terms_name 검색 성공";
                    $this->conn->s_transaction(); //트랜잭션 시작
                    if(count($result["value"]) != 0){ //검색결과가 있으면 update
                        for($i = 0; $i < count($terms_data); $i++){
                            $sql = "update terms_name set content = ".$this->null_check_v2($terms_data[$i][0]);
                            $sql .= " where terms_idx = ".$param["terms_idx"];
                            $sql .= " and lang_idx = ".$terms_data[$i][1];
                            $this->conn->db_update($sql);
                            
                        }
                    }else{ //없으면 insert
                        $sql = "insert into terms_name(content, lang_idx, terms_idx) values(";
                        for($i = 0; $i < count($terms_data); $i++){
                            $sql .= $this->null_check_v2($terms_data[$i][0]).","; //content
                            $sql .= $terms_data[$i][1].","; //lang_idx
                            if($i == count($terms_data) - 1){ //for문 마지막일때
                                $sql .= $param["terms_idx"].")";
                            }else{
                                $sql .= $param["terms_idx"]."), (";
                            }
                        }
                        $result = $this->conn->db_insert($sql);
                    }
                    $this->conn->commit();
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "언어별 terms_name 검색 실패";
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 배너 등록 type에 따라 등록되는 배너가 달라짐
        // 설 명 : type 1: 메인 2: 서브 ....
        // 만든이: 안정환
        *********************************************************************/
        function request_banner_reg(){
            $param = $this->param;
            if($this->value_check(array("type"))){
                $pc_files = $_FILES["pc_banner_files"]; //pc 이미지 파일 객체
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    $m_files = $_FILES["m_banner_files"]; //m 이미지 파일 객체
                    // if($param["type"] == 1){
                    //     $s_files = $_FILES["s_banner_files"]; //m 이미지 파일 객체
                    // }
                }

                
                
                $pc_result = $this->file_manager->upload_file($pc_files,$this->file_path["pc_banner_img_path"], $this->file_path["pc_banner_img_origin_path"]); //파일생성
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    $m_result = $this->file_manager->upload_file($m_files,$this->file_path["m_banner_img_path"], $this->file_path["m_banner_img_origin_path"]); //파일생성
                    // if($param["type"] == 1){
                    //     // $s_result = $this->file_manager->upload_file($s_files,$this->file_path["s_banner_img_path"], $this->file_path["s_banner_img_origin_path"]); //파일생성
                    // }
                    // else{
                    //     // $s_result["file_name"] = array();
                    //     // array_push($s_result["file_name"], "");
                    // }
                }

                if(count($pc_result["file_name"]) == 0){
                    $pc_result["file_name"][0] = "";
                }

                if(count($m_result["file_name"]) == 0){
                    $m_result["file_name"][0] = "";
                }
                
                $error_delete_array = array();//쿼리 실패시 삭제해야할 파일 목록
                array_push($error_delete_array,$pc_result["error_file_array"]);
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    array_push($error_delete_array,$m_result["error_file_array"]);
                    // if($param["type"] == 1){
                    //     array_push($error_delete_array,$s_result["error_file_array"]);
                    // }
                }
                $sql = null;
                if(isset($param["banner_title"]) && isset($param["banner_content"])){ //추천라인 배너
                    $sql = "insert into banner(name, pc_file_name, m_file_name, link, lang_idx, is_use, kind, title, content, ";
                    if(!empty($param["color"])){
                        $sql .= "background_color, ";
                    }
                    if(!empty($param["word_color"])){
                        $sql .= "word_color, ";
                    }
                    $sql .= "regdate, sequence) values(";
                    $sql .= $this->null_check($param["banner_name"]).",";
                    $sql .= $this->null_check($pc_result["file_name"][0]).",";
                    $sql .= $this->null_check($m_result["file_name"][0]).",";
                    $sql .= $this->null_check($param["link"]).",";
                    $sql .= $param["lang"].",";
                    $sql .= $param["is_use"].",";
                    $sql .= $param["type"].",";
                    $sql .= $this->null_check($param["banner_title"]).",";
                    $sql .= $this->null_check($param["banner_content"]).",";
                    if(!empty($param["color"])){
                        $sql .= $this->null_check($param["color"]).",";
                    }
                    if(!empty($param["word_color"])){
                        $sql .= $this->null_check($param["word_color"]).",";
                    }
                    $sql .= "now()".",";
                    $sql .= "(select AUTO_INCREMENT from information_schema.tables where table_name = 'banner' and table_schema = database())".")";
                }else{
                    $sql = "insert into banner(name, pc_file_name, m_file_name, link, lang_idx, is_use, kind, regdate, sequence) values(";
                    $sql .= $this->null_check($param["banner_name"]).",";
                    $sql .= $this->null_check($pc_result["file_name"][0]).",";
                    $sql .= $this->null_check($m_result["file_name"][0]).",";
                    $sql .= $this->null_check($param["link"]).",";
                    $sql .= $param["lang"].",";
                    $sql .= $param["is_use"].",";
                    $sql .= $param["type"].",";
                    $sql .= "now()".",";
                    $sql .= "(select AUTO_INCREMENT from information_schema.tables where table_name = 'banner' and table_schema = database())".")";
                }
                $this->conn->set_file_list($error_delete_array); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                $this->result = $this->conn->db_insert($sql);
            }
            

            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 배너 목록 조회
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_banner_list(){
            $param = $this->param;
            if($this->value_check(array("type","lang"))){
                $sql = "select * from banner where kind=".$param["type"]." and lang_idx=".$param["lang"]." order by sequence asc";
                $this->result = $this->conn->db_select($sql);
            }
            

            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 배너 삭제
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_delete_banner(){
            $param = $this->param;
            if($this->value_check(array("delete_idxs","type"))){
                $delete_idxs = json_decode($param["delete_idxs"]);
                //삭제할 배너 조회하기
                $sql = "select * from banner where (";
                $delete_sql = "delete from banner where (";
                for($i=0; $i<count($delete_idxs); $i++){
                    if($i==0){
                        $sql = $sql."idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql."idx=".$delete_idxs[$i];
                    }else{
                        $sql = $sql." or idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql." or idx=".$delete_idxs[$i];
                    }
                }
                $sql = $sql.") and kind=".$param["type"];
                $delete_sql =$delete_sql.") and kind=".$param["type"];
                $result = $this->conn->db_select($sql);
                $delete_banner_datas = $result["value"];
                if(count($delete_banner_datas) == 0){ //삭제할 데이터가 없다면
                    $this->result["result"] = "0";
                    $this->result["message"] = "삭제할 배너가 없습니다";
                }else{
                    //삭제해야할 이미지 목록 만들기
                    $delete_pc_images = array();
                    $delete_pc_origin_images = array();
                    $delete_m_images = array();
                    $delete_m_origin_images = array();

                    for($i=0; $i<count($delete_banner_datas); $i++){
                        array_push($delete_pc_images,$this->file_path["pc_banner_img_path"].$delete_banner_datas[$i]["pc_file_name"]);
                        array_push($delete_pc_origin_images,$this->file_path["pc_banner_img_origin_path"].$delete_banner_datas[$i]["pc_file_name"]);
                        array_push($delete_m_images,$this->file_path["m_banner_img_path"].$delete_banner_datas[$i]["m_file_name"]);
                        array_push($delete_m_origin_images,$this->file_path["m_banner_img_origin_path"].$delete_banner_datas[$i]["m_file_name"]);
                    }

                    //삭제해야할 파일 목록을 만들었으면 sql delete
                    $this->result = $this->conn->db_delete($delete_sql);
                    $this->file_manager->delete_file($delete_pc_images); //삭제요청
                    $this->file_manager->delete_file($delete_pc_origin_images); //삭제요청
                    $this->file_manager->delete_file($delete_m_images); //삭제요청
                    $this->file_manager->delete_file($delete_m_origin_images); //삭제요청
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 배너 순서 변경
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_banner_relation_change(){
            $param = $this->param;
            if($this->value_check(array("relation_array"))){
                $relation_array = json_decode($param["relation_array"]);
                $sql = "UPDATE banner SET sequence = CASE idx";
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
        // 함 수 : 배너 사용상태 변경
        // 설 명 : 
        // 만든이: 안정환
        *********************************************************************/
        function request_banner_is_use_change(){
            $param = $this->param;
            if($this->value_check(array("banner_idx", "is_use"))){
                $sql = "UPDATE banner SET is_use=".$param["is_use"]." where idx=".$param["banner_idx"];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 배너 수정
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function modify_banner(){
            $param = $this->param;
            // print_r($_FILES);
            // if(false){ //필수값 체크
            if($this->value_check(array("idx"))){ //필수값 체크
                $banner_idx = $param["idx"];

                $lang_idx = 1; //현재 언어의 idx,  언어관련 엘리먼트는 언어 idx를 기준으로 key값이 넘어온다.
                $banner_name = $param["banner_name"];
                $banner_title = $param["banner_title"];
                $banner_content = $param["banner_content"];
                $link = $param["link"];

                $thumnail_files = $_FILES["pc_img_file"]; //썸네일 file array 객체
                $m_thumnail_files = $_FILES["m_img_file"]; //썸네일 file array 객체

                //썸네일 삭제 및 description 원본을 가져오기위해 조회
                $origin_sql = "select * from banner where idx=".$banner_idx;
                $origin_result = $this->conn->db_select($origin_sql);
                $origin_data = $origin_result["value"][0]; //원본 product_name 데이터(언어 1개)
                    
                

                $thumnail_result = null;
                $m_thumnail_result = null;
                $save_files = array();
                
                //썸네일 파일이 있는지 확인
                if($thumnail_files["error"][0] == "0"){
                    //썸네일 파일 있음
                    // echo "썸네일 파일 있음[".$lang_idx."]";
                    $thumnail_result = $this->file_manager->upload_file($thumnail_files,$this->file_path["pc_banner_img_path"], $this->file_path["pc_banner_img_origin_path"]); //썸네일 파일 생성
                    // print_r($thumnail_result);
                    $save_files = array_merge($save_files,$thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                }
                
                if($thumnail_result != null){
                    if(count($thumnail_result["file_name"]) == 0){
                        $thumnail_result["file_name"][0] = null;
                    }
                }

                if($param["del_thumbnail_flag"] == 1){
                    $thumnail_result["file_name"][0] = null;
                }

                //썸네일 파일이 있는지 확인
                if($m_thumnail_files["error"][0] == "0"){
                    //썸네일 파일 있음
                    // echo "썸네일 파일 있음[".$lang_idx."]";
                    $m_thumnail_result = $this->file_manager->upload_file($m_thumnail_files,$this->file_path["m_banner_img_path"], $this->file_path["m_banner_img_origin_path"]); //썸네일 파일 생성
                    // print_r($m_thumnail_result);
                    $save_files = array_merge($save_files,$m_thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                }
                
                if($m_thumnail_result != null){
                    if(count($m_thumnail_result["file_name"]) == 0){
                        $m_thumnail_result["file_name"][0] = null;
                    }
                }

                if($param["del_m_thumbnail_flag"] == 1){
                    $m_thumnail_result["file_name"][0] = null;
                }

                
                $banner_sql = "update banner set ";   //언어별 상태를 넣어야할경우
                $delete_thumnail_files = []; //삭제해야할 썸네일 파일 이름
                $delete_m_thumnail_files = []; //삭제해야할 썸네일 파일 이름
                $banner_sql = $banner_sql."name=".$this->null_check($banner_name);
                $banner_sql = $banner_sql.",title=".$this->null_check($banner_title);
                $banner_sql = $banner_sql.",content=".$this->null_check($banner_content);
                $banner_sql = $banner_sql.",link=".$this->null_check($link);
                $banner_sql = $banner_sql.",is_use=".$param["is_use"];
                if($thumnail_result != null){ //썸네일 파일 변경됨
                    $banner_sql = $banner_sql.",pc_file_name=".$this->null_check($thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    array_push($delete_thumnail_files,$origin_data["pc_file_name"]);
                }
                if($m_thumnail_result != null){ //썸네일 파일 변경됨
                    $banner_sql = $banner_sql.",m_file_name=".$this->null_check($m_thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    array_push($delete_m_thumnail_files,$origin_data["m_file_name"]);
                }

                $banner_sql = $banner_sql. " where idx=".$banner_idx;
                

                $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                $this->result = $this->conn->db_update($banner_sql);
                
                if(count($delete_thumnail_files) > 0){
                    $delete_thumnail_origin_files = []; //썸네일 파일 오리지날 파일 목록
                    for($i=0; $i<count($delete_thumnail_files); $i++){
                        array_push($delete_thumnail_origin_files,$this->file_path["pc_banner_img_origin_path"].$delete_thumnail_files[$i]); //오리진 full 경로
                        $delete_thumnail_files[$i] = $this->file_path["pc_banner_img_path"].$delete_thumnail_files[$i]; //full 경로로 변경
                    }

                    $this->file_manager->delete_file($delete_thumnail_origin_files); //삭제요청
                    $this->file_manager->delete_file($delete_thumnail_files); //삭제요청
                }

                if(count($delete_m_thumnail_files) > 0){
                    $delete_m_thumnail_origin_files = []; //썸네일 파일 오리지날 파일 목록
                    for($i=0; $i<count($delete_m_thumnail_files); $i++){
                        array_push($delete_m_thumnail_origin_files,$this->file_path["m_banner_img_origin_path"].$delete_m_thumnail_files[$i]); //오리진 full 경로
                        $delete_m_thumnail_files[$i] = $this->file_path["m_banner_img_path"].$delete_m_thumnail_files[$i]; //full 경로로 변경
                    }

                    $this->file_manager->delete_file($delete_m_thumnail_origin_files); //삭제요청
                    $this->file_manager->delete_file($delete_m_thumnail_files); //삭제요청
                }
                

                $this->conn->commit();
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 배너 상세정보 조회
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_banner_detail(){
            $param = $this->param;
            if($this->value_check(array("banner_idx"))){
                $sql = "select * from banner where kind = 1 and idx = ".$param["banner_idx"];
                $result = $this->conn->db_select($sql);
                
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 연혁 등록
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function history_register(){
            $param = $this->param;
            if($this->value_check(array("date", "content"))){
                // 날짜
                $date = json_decode($param["date"] , true);
                // 내용
                $content = json_decode($param["content"], true);

                $sql = "insert into company_history(regdate) values(";
                $sql .= "now() ";
                $sql .= ")";

                $this->conn->s_transaction();
                $result = $this->conn->db_insert($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    // 연혁 idx
                    $history_idx = $result["value"];

                    $sql = "insert into company_history_name(year, month, content, lang_idx, company_history_idx) values ";
                    for($i = 0; $i<count($date); $i++){
                        $tmp_date = explode("-", $date[$i]);
                        $year = $tmp_date[0];
                        $month = $tmp_date[1];
                        $sql .= "(";
                        $sql .= $this->null_check($year) . ", ";
                        $sql .= $this->null_check($month) . ", ";
                        $sql .= $this->null_check($content[$i]) . ", ";
                        $sql .= ($i + 1) . ", ";
                        $sql .= $history_idx . " ";
                        if($i != count($date) -1 ){
                            $sql .= "),";
                        }else{
                            $sql .= ")";
                        }
                    }
                    
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
        // 함 수 : 연혁 리스트
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_company_history_list(){
            $param = $this->param;
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);

                $sql = "select t1.idx as company_history_idx, t2.* from company_history as t1 ";
                $sql .= "left join company_history_name as t2 ";
                $sql .= "on t1.idx = t2.company_history_idx ";
                $sql .= "where t1.idx is not null ";
                $sql .= "and t2.lang_idx = 1 ";
                // 검색어 부분
                if(isset($move_list["word"])){
                    if($move_list["word"] != ""){
                        // 제목으록 검색했을시
                        if($move_list["search_kind"] == "year"){
                            $sql .= " and t2.year like '%".$move_list["word"]."%' ";
                        }
                        else if($move_list["search_kind"] == "month") {
                            $sql .= " and t2.month like '%".$move_list["word"]."%' ";
                        }
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= "order by t2.year desc, t2.month desc ";
                $sql .= "limit ".$page_size*($page-1).",".$page_size;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    $inner_sql = "left join company_history_name as t2 ";
                    $inner_sql .= "on t1.idx = t2.company_history_idx ";
                    $inner_sql .= "where t1.idx is not null ";
                    $inner_sql .= "and t2.lang_idx = 1 ";
                    // 검색어부분
                    if(isset($move_list["word"])){
                        if($move_list["word"] != ""){
                            // 제목으록 검색했을시
                            if($move_list["search_kind"] == "year"){
                                $sql .= " and t2.year like '%".$move_list["word"]."%' ";
                            }
                            else if($move_list["search_kind"] == "month") {
                                $sql .= " and t2.month like '%".$move_list["word"]."%' ";
                            }
                        }
                    }
                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(t1.idx) as total from company_history as t1 ";
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
        // 함 수 : 연혁 상세정보
        // 설 명 : 
        // 만든이: 최진혁
        *********************************************************************/
        function history_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))){
                $sql  = "select t1.idx, t2.* from company_history as t1 ";
                $sql .= "left join company_history_name as t2 ";
                $sql .= "on t1.idx = t2.company_history_idx ";
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
        // 함 수 : 연혁 삭제
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_delete_history(){
            $param = $this->param;
            if($this->value_check(array("delete_idxs"))){
                $delete_idxs = json_decode($param["delete_idxs"]);
                //삭제할 연혁 조회하기 company_history_idx를 찾아서 해당 idx는 다 삭제
                $sql = "select company_history_idx as idx from company_history_name where idx in (";
                for($i=0; $i<count($delete_idxs); $i++){
                    if($i==count($delete_idxs) - 1){
                        $sql = $sql.$delete_idxs[$i];
                    }else{
                        $sql = $sql.$delete_idxs[$i] .", ";
                    }
                }
                $sql = $sql.")";
                $result = $this->conn->db_select($sql);
                for($i = 0; $i < count($result["value"]); $i++) {
                    $sql = "delete from company_history_name where company_history_idx = " .$result["value"][$i]["idx"];
                    $this->conn->s_transaction();
                    $company_history_name_result = $this->conn->db_delete($sql);
                    if($company_history_name_result["result"] == "1") {
                        $sql = "delete from company_history where idx = " .$result["value"][$i]["idx"];
                        $company_history_result = $this->conn->db_delete($sql);
                        if($company_history_result["result"] == "0") {
                            $this->conn->rollback();
                            $this->result = $company_history_result;
                            $this->result["message"] = "company history 테이블 삭제 실패";
                            return;
                        }
                    }
                    else {
                        $this->conn->rollback();
                        $this->result = $company_history_name_result;
                        $this->result["message"] = "company history name 테이블 삭제 실패";
                        return;
                    }

                }
                $this->result = $result;
                $this->conn->commit();
            }
            echo $this->jsonEncode($this->result);
        }


        /********************************************************************* 
        // 함 수 : 인증서 등록
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_certificate_reg(){
            $param = $this->param;
            if($this->value_check(array("type"))){
                $pc_files = $_FILES["pc_certificate_files"]; //pc 이미지 파일 객체
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    $m_files = $_FILES["m_certificate_files"]; //m 이미지 파일 객체
                    // if($param["type"] == 1){
                    //     $s_files = $_FILES["s_banner_files"]; //m 이미지 파일 객체
                    // }
                }

                
                
                $pc_result = $this->file_manager->upload_file($pc_files,$this->file_path["certificate_thumbnail_path"], $this->file_path["certificate_thumbnail_origin_path"]); //파일생성
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    $m_result = $this->file_manager->upload_file($m_files,$this->file_path["certificate_thumbnail_path"], $this->file_path["certificate_thumbnail_origin_path"]); //파일생성
                    // if($param["type"] == 1){
                    //     // $s_result = $this->file_manager->upload_file($s_files,$this->file_path["s_banner_img_path"], $this->file_path["s_banner_img_origin_path"]); //파일생성
                    // }
                    // else{
                    //     // $s_result["file_name"] = array();
                    //     // array_push($s_result["file_name"], "");
                    // }
                }

                if(count($pc_result["file_name"]) == 0){
                    $pc_result["file_name"][0] = "";
                }

                if(count($m_result["file_name"]) == 0){
                    $m_result["file_name"][0] = "";
                }
                
                $error_delete_array = array();//쿼리 실패시 삭제해야할 파일 목록
                array_push($error_delete_array,$pc_result["error_file_array"]);
                if($param["type"] != 3){ //추천라인 배너가 아닌경우만 mobile 이미지 등록
                    array_push($error_delete_array,$m_result["error_file_array"]);
                    // if($param["type"] == 1){
                    //     array_push($error_delete_array,$s_result["error_file_array"]);
                    // }
                }
                $sql = null;
                if(isset($param["certificate_title"]) && isset($param["certificate_content"])){ //추천라인 배너
                    $sql = "insert into certificate(name, pc_file_name, m_file_name, link, lang_idx, is_use, kind, title, content, ";
                    $sql .= "regdate, sequence) values(";
                    $sql .= $this->null_check($param["certificate_name"]).",";
                    $sql .= $this->null_check($pc_result["file_name"][0]).",";
                    $sql .= $this->null_check($m_result["file_name"][0]).",";
                    $sql .= $this->null_check($param["link"]).",";
                    $sql .= $param["lang"].",";
                    $sql .= $param["is_use"].",";
                    $sql .= $param["type"].",";
                    $sql .= $this->null_check($param["certificate_title"]).",";
                    $sql .= $this->null_check($param["certificate_content"]).",";
                    $sql .= "now()".",";
                    $sql .= "(select AUTO_INCREMENT from information_schema.tables where table_name = 'certificate' and table_schema = database())".")";
                }else{
                    $sql = "insert into certificate(name, pc_file_name, m_file_name, link, lang_idx, is_use, kind, regdate, sequence) values(";
                    $sql .= $this->null_check($param["certificate_name"]).",";
                    $sql .= $this->null_check($pc_result["file_name"][0]).",";
                    $sql .= $this->null_check($m_result["file_name"][0]).",";
                    $sql .= $this->null_check($param["link"]).",";
                    $sql .= $param["lang"].",";
                    $sql .= $param["is_use"].",";
                    $sql .= $param["type"].",";
                    $sql .= "now()".",";
                    $sql .= "(select AUTO_INCREMENT from information_schema.tables where table_name = 'certificate' and table_schema = database())".")";
                }
                $this->conn->set_file_list($error_delete_array); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                $this->result = $this->conn->db_insert($sql);
            }
            

            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 인증서 목록 조회
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_certificate_list(){
            $param = $this->param;
            if($this->value_check(array("type","lang"))){
                $sql = "select * from certificate where kind=".$param["type"]." and lang_idx=".$param["lang"]." order by sequence asc";
                $this->result = $this->conn->db_select($sql);
            }
            

            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 인증서 삭제
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_delete_certificate(){
            $param = $this->param;
            if($this->value_check(array("delete_idxs","type"))){
                $delete_idxs = json_decode($param["delete_idxs"]);
                //삭제할 배너 조회하기
                $sql = "select * from certificate where (";
                $delete_sql = "delete from certificate where (";
                for($i=0; $i<count($delete_idxs); $i++){
                    if($i==0){
                        $sql = $sql."idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql."idx=".$delete_idxs[$i];
                    }else{
                        $sql = $sql." or idx=".$delete_idxs[$i];
                        $delete_sql = $delete_sql." or idx=".$delete_idxs[$i];
                    }
                }
                $sql = $sql.") and kind=".$param["type"];
                $delete_sql =$delete_sql.") and kind=".$param["type"];
                $result = $this->conn->db_select($sql);
                $delete_banner_datas = $result["value"];
                if(count($delete_banner_datas) == 0){ //삭제할 데이터가 없다면
                    $this->result["result"] = "0";
                    $this->result["message"] = "삭제할 배너가 없습니다";
                }else{
                    //삭제해야할 이미지 목록 만들기
                    $delete_pc_images = array();
                    $delete_pc_origin_images = array();
                    $delete_m_images = array();
                    $delete_m_origin_images = array();

                    for($i=0; $i<count($delete_banner_datas); $i++){
                        array_push($delete_pc_images,$this->file_path["certificate_thumbnail_path"].$delete_banner_datas[$i]["pc_file_name"]);
                        array_push($delete_pc_origin_images,$this->file_path["certificate_thumbnail_origin_path"].$delete_banner_datas[$i]["pc_file_name"]);
                        array_push($delete_m_images,$this->file_path["certificate_thumbnail_path"].$delete_banner_datas[$i]["m_file_name"]);
                        array_push($delete_m_origin_images,$this->file_path["certificate_thumbnail_origin_path"].$delete_banner_datas[$i]["m_file_name"]);
                    }

                    //삭제해야할 파일 목록을 만들었으면 sql delete
                    $this->result = $this->conn->db_delete($delete_sql);
                    $this->file_manager->delete_file($delete_pc_images); //삭제요청
                    $this->file_manager->delete_file($delete_pc_origin_images); //삭제요청
                    $this->file_manager->delete_file($delete_m_images); //삭제요청
                    $this->file_manager->delete_file($delete_m_origin_images); //삭제요청
                }
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 인증서 순서 변경
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_certificate_relation_change(){
            $param = $this->param;
            if($this->value_check(array("relation_array"))){
                $relation_array = json_decode($param["relation_array"]);
                $sql = "UPDATE certificate SET sequence = CASE idx";
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
        // 함 수 : 인증서 사용상태 변경
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_certificate_is_use_change(){
            $param = $this->param;
            if($this->value_check(array("certificate_idx", "is_use"))){
                $sql = "UPDATE certificate SET is_use=".$param["is_use"]." where idx=".$param["certificate_idx"];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 인증서 수정
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function modify_certificate(){
            $param = $this->param;
            // print_r($_FILES);
            // if(false){ //필수값 체크
            if($this->value_check(array("idx"))){ //필수값 체크
                $certificate_idx = $param["idx"];

                $lang_idx = 1; //현재 언어의 idx,  언어관련 엘리먼트는 언어 idx를 기준으로 key값이 넘어온다.
                $certificate_name = $param["certificate_name"];
                // $banner_title = $param["banner_title"];
                // $banner_content = $param["banner_content"];
                // $link = $param["link"];

                $thumnail_files = $_FILES["pc_img_file"]; //썸네일 file array 객체
                $m_thumnail_files = $_FILES["m_img_file"]; //썸네일 file array 객체

                //썸네일 삭제 및 description 원본을 가져오기위해 조회
                $origin_sql = "select * from certificate where idx=".$certificate_idx;
                $origin_result = $this->conn->db_select($origin_sql);
                $origin_data = $origin_result["value"][0]; //원본 product_name 데이터(언어 1개)
                    
                

                $thumnail_result = null;
                $m_thumnail_result = null;
                $save_files = array();
                
                //썸네일 파일이 있는지 확인
                if($thumnail_files["error"][0] == "0"){
                    //썸네일 파일 있음
                    // echo "썸네일 파일 있음[".$lang_idx."]";
                    $thumnail_result = $this->file_manager->upload_file($thumnail_files,$this->file_path["certificate_thumbnail_path"], $this->file_path["certificate_thumbnail_origin_path"]); //썸네일 파일 생성
                    // print_r($thumnail_result);
                    $save_files = array_merge($save_files,$thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                }
                
                if($thumnail_result != null){
                    if(count($thumnail_result["file_name"]) == 0){
                        $thumnail_result["file_name"][0] = null;
                    }
                }

                if($param["del_thumbnail_flag"] == 1){
                    $thumnail_result["file_name"][0] = null;
                }

                //썸네일 파일이 있는지 확인
                if($m_thumnail_files["error"][0] == "0"){
                    //썸네일 파일 있음
                    // echo "썸네일 파일 있음[".$lang_idx."]";
                    $m_thumnail_result = $this->file_manager->upload_file($m_thumnail_files,$this->file_path["certificate_thumbnail_path"], $this->file_path["certificate_thumbnail_origin_path"]); //썸네일 파일 생성
                    // print_r($m_thumnail_result);
                    $save_files = array_merge($save_files,$m_thumnail_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                }
                
                if($m_thumnail_result != null){
                    if(count($m_thumnail_result["file_name"]) == 0){
                        $m_thumnail_result["file_name"][0] = null;
                    }
                }

                if($param["del_m_thumbnail_flag"] == 1){
                    $m_thumnail_result["file_name"][0] = null;
                }

                
                $certificate_sql = "update certificate set ";   //언어별 상태를 넣어야할경우
                $delete_thumnail_files = []; //삭제해야할 썸네일 파일 이름
                $delete_m_thumnail_files = []; //삭제해야할 썸네일 파일 이름
                $certificate_sql = $certificate_sql."name=".$this->null_check($certificate_name);
                // $certificate_sql = $certificate_sql.",title=".$this->null_check($banner_title);
                // $certificate_sql = $certificate_sql.",content=".$this->null_check($banner_content);
                // $certificate_sql = $certificate_sql.",link=".$this->null_check($link);
                $certificate_sql = $certificate_sql.",is_use=".$param["is_use"];
                if($thumnail_result != null){ //썸네일 파일 변경됨
                    $certificate_sql = $certificate_sql.",pc_file_name=".$this->null_check($thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    array_push($delete_thumnail_files,$origin_data["pc_file_name"]);
                }
                if($m_thumnail_result != null){ //썸네일 파일 변경됨
                    $certificate_sql = $certificate_sql.",m_file_name=".$this->null_check($m_thumnail_result["file_name"][0]); //썸네일 이미지는 무조건 1장임
                    array_push($delete_m_thumnail_files,$origin_data["m_file_name"]);
                }

                $certificate_sql = $certificate_sql. " where idx=".$certificate_idx;
                

                $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                $this->result = $this->conn->db_update($certificate_sql);
                
                if(count($delete_thumnail_files) > 0){
                    $delete_thumnail_origin_files = []; //썸네일 파일 오리지날 파일 목록
                    for($i=0; $i<count($delete_thumnail_files); $i++){
                        array_push($delete_thumnail_origin_files,$this->file_path["certificate_thumbnail_origin_path"].$delete_thumnail_files[$i]); //오리진 full 경로
                        $delete_thumnail_files[$i] = $this->file_path["certificate_thumbnail_path"].$delete_thumnail_files[$i]; //full 경로로 변경
                    }

                    $this->file_manager->delete_file($delete_thumnail_origin_files); //삭제요청
                    $this->file_manager->delete_file($delete_thumnail_files); //삭제요청
                }

                if(count($delete_m_thumnail_files) > 0){
                    $delete_m_thumnail_origin_files = []; //썸네일 파일 오리지날 파일 목록
                    for($i=0; $i<count($delete_m_thumnail_files); $i++){
                        array_push($delete_m_thumnail_origin_files,$this->file_path["certificate_thumbnail_origin_path"].$delete_m_thumnail_files[$i]); //오리진 full 경로
                        $delete_m_thumnail_files[$i] = $this->file_path["certificate_thumbnail_path"].$delete_m_thumnail_files[$i]; //full 경로로 변경
                    }

                    $this->file_manager->delete_file($delete_m_thumnail_origin_files); //삭제요청
                    $this->file_manager->delete_file($delete_m_thumnail_files); //삭제요청
                }
                

                $this->conn->commit();
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : 인증서 상세정보 조회
        // 설 명 : 
        // 만든이: 김경훈
        *********************************************************************/
        function request_certificate_detail(){
            $param = $this->param;
            if($this->value_check(array("certificate_idx"))){
                $sql = "select * from certificate where kind = 1 and idx = ".$param["certificate_idx"];
                $result = $this->conn->db_select($sql);
                
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }



        /********************************************************************* 
        // 함 수 : 게시글 등록 함수
        // title : 블로그 이름
        // lang_idx : 언어 구분 idx
        // category_idx : 카테고리 idx  
        // all_reg : 0 : 언어별 등록 , 1 : 모든 언어 등록
        // image_sync : 이미지 동기화 구분 변수
        // file_sync : 파일 동기화 구분 변수
        // 만든이: 조경민
        *********************************************************************/
        function notice_register(){
            $param = $this->param;
        

            $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)
            $content = $this->sumnote["sumnote"]; //언어별 description [base64, base64]
            $add_img_array = json_decode($param["add_img_array"], true);

            //데이터 insert시 사용하기 위해 배열에 각 파일들 저장
            $image_array = array();
            $file_array = array();
            $real_file_array = array();
            $sql = "select max(idx) from notice";
            $result = $this->conn->db_select($sql);
            if($result['result']==0){
                $this->result['result']='0';
                $this->result['message']='데이터를 불러오는데 실패하였습니다.';
            }else{
                $seq = (int)$result['value'][0]['max(idx)'] + 1;
            


                $description = $content; //언어별 descriptiuon
                $file = $_FILES["add_file"]; //첨부파일 file array 객체




                // 직접 파일 업로드 처리
                $upload_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["notice_file_path"];
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                $uploaded_files = array();
                $real_files = array();
                
                if(isset($file['name']) && is_array($file['name'])) {
                    for($i = 0; $i < count($file['name']); $i++) {
                        if($file['error'][$i] == 0 && !empty($file['name'][$i])) {
                            $original_name = $file['name'][$i];
                            $file_tmp = $file['tmp_name'][$i];
                            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                            $new_file_name = time() . sprintf('%05d', $i) . '.' . $file_extension;
                            
                            if(move_uploaded_file($file_tmp, $upload_path . $new_file_name)) {
                                $uploaded_files[] = $new_file_name;
                                $real_files[] = $original_name;
                            }
                        }
                    }
                }
                
                // file_manager 방식으로 결과 구성
                $file_result = array(
                    "file_name" => $uploaded_files,
                    "real_name" => $real_files,
                    "error_code" => "",
                    "error_file_array" => array()
                );
                

                
                $save_files = array_merge($save_files, $file_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge
                
                // 빈 파일명 제거
                $filtered_file_names = array();
                foreach($file_result["file_name"] as $uploaded_file){
                    if(!empty($uploaded_file)){
                        array_push($filtered_file_names, $uploaded_file);
                    }
                }
                array_push($file_array, $filtered_file_names);

                //실제 파일이름 (빈 파일 제외)
                $temp = array();
                for($j = 0; $j < count($file["name"]); $j++){
                    if(!empty($file["name"][$j]) && $file["error"][$j] == 0){
                    array_push($temp, $file["name"][$j]);
                    }
                }

                array_push($real_file_array, $temp);
            
                $this->conn->s_transaction(); //board_file과 board 게시글을 모두 삭제시키기 위해 둘다 삭제에 성공하면 commit 실행
                
                //게시글 insert문 (카테고리 포함)
                $category = $this->null_check($param["category"]);
                $sql = "insert into notice (seq, regdate, category) values ($seq, now(), $category)";
                
                $result = $this->conn->db_insert($sql);
                
                if($result['result']==0){
                    $this->result['result'] = "0";
                    $this->result['message'] = "데이터를 삽입하는데 실패하였습니다."; 
                    $this->conn->rollback();
                }else{
                    $idx = $result['value'];

                    //board_file db에 업로드된 파일명 저장
                    $upload_flag = true;
                    $upload_file=[];
                    $upload_file = $file_array[0];
                    $real_file = $real_file_array[0];
                        
                        //업로드할 파일이 있는 경우만 실행
                    if(count($upload_file) > 0){
                        //board_file db insert문
                        $sql = "insert into notice_file(notice_idx, upload_file, real_file) values(";
                        for($j = 0; $j < count($upload_file); $j++){
                            $sql .= $this->null_check($idx).",";
                            $sql .= $this->null_check($upload_file[$j]).",";
                            $sql .= $this->null_check($real_file[$j])."),(";
                        }
                        
                        $sql = substr($sql, 0, -2);
                        
                        $this->conn->set_file_list($save_files); //db오류 생겻을경우 롤백으로 인한 저장된 파일 삭제
                        $result = $this->conn->db_insert($sql);
                        if($result["result"] == "1"){
                            $this->result = $result;
                            $this->result["message"] = "파일 등록 성공";
                        }else{
                            $this->conn->rollback();
                            $this->result["result"] = "0";
                            $this->result["error_code"] = "300";
                            $this->result["message"] = "파일 등록 실패";
                        }
                    }
                    $kind = isset($param["kind"]) ? intval($param["kind"]) : 0;
                    $sql = "insert into notice_name (title, content, lang_idx, notice_idx, kind) values(";

                    $sql .= $this->null_check($param["title"]).","; //제목
                    $sql .= $this->null_check($content).","; //내용
                    $sql .= " 1, ";
                    $sql .= $idx.","; //notice_idx
                    $sql .= $kind.")"; //중요도
                    
                    $result = $this->conn->db_insert($sql);
                    
                    
                    if($result["result"] == "1"){
                        $this->result = $result;
                        $this->result["message"] = "게시글 등록 성공";
                        
                        //추가 : 조경민
                        //게시글 등록이 완료되면 sumnote_img table에 있는 이미지들의 state 값을 1로 변경하여 쓰레드에서 삭제되지 않도록 함
                        
                        if(count($add_img_array) > 0){
                            $sql = "update sumnote_img set state = 1";
                            for($i = 0; $i < count($add_img_array); $i++){
                                if($i == 0){
                                    $sql .= " where file_name = ".$this->null_check($add_img_array[$i]);
                                }else{
                                    $sql .= " or file_name = ".$this->null_check($add_img_array[$i]);
                                }
                            }
                            $result = $this->conn->db_update($sql);
                        }
                        
                        
                        $this->conn->commit();
                    }else{
                        $this->conn->rollback();
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "300";
                        $this->result["message"] = "게시글 등록 실패";
                    }
                }   
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }


        /*******************************************************************************
        // 함수 설명 : 공지 상세내용
        // 설명 : 
        // 만든이: 김경훈
        ********************************************************************************/
        function notice_detail(){
            $param = $this->param;
            if($this->value_check(array("target"))) {
                $sql = "select * from notice as t1";
                $sql .= " left join notice_name as t2 on t1.idx = t2.notice_idx ";
                $sql .= " where t1.idx = ".$param['target'];

                $result = $this->conn->db_select($sql);
                if($result['result']=='0'){
                    $this->result = $result;
                }else{
                    $file_sql = "select upload_file, real_file from notice_file where notice_idx = ".$param['target']." and upload_file is not null";
                    
                    $file_result = $this->conn->db_select($file_sql);
                    if($file_result['result'] =='0'){
                        $this->result = $file_result;
                    }else{
                        $this->result = $result;
                        if(count($file_result['value']) > 0){
                            $this->result['files'] = $file_result['value'];
                        }
                        
                    }
                    // $this->result['files'] = 
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }


        /********************************************************************* 
        // 함 수 : 공지 수정 함수
        // name : 블로그 이름
        // lang_idx : 언어 구분 idx
        // upload_img : 변경되지 않은 upload_img array 
        // 만든이: 최재혁
        *********************************************************************/
        function notice_modify(){
            $param = $this->param;
            // print_r($param);
            // print_r($_FILES);
            
            // exit;
            if($this->value_check(array("idx"))){
                $files = json_decode($param['files']);
                $idx = $param['idx'];
                
                $is_default_file = $param['is_default_file'];
                
                
                
                
                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)
                //삭제할 데이터를 삽입할 배열
                $delete_file_array = array();
                $delete_file_sql_array = array();
                //추가된 데이터를 삽입할 배열
                $file_array = array();
                $real_file_array = array();

                if(isset($files[0])){//0번째 파일이 있는데
                    if ($is_default_file == 0) { //0번째 파일 이름이 없는경우 (삭제한경우)
                        array_push($delete_file_array,$this->file_path["notice_file_path"].$files[0] -> upload_file);
                        array_push($delete_file_sql_array,$files[0] -> upload_file);
                        
                    }
                }
                for ($i = 1; $i < count($files); $i++) {
                    // 파일이 변경된 경우
                    if (isset($_FILES['add_file']['name'][$i]) && !empty($_FILES['add_file']['name'][$i])) {
                        array_push($delete_file_array,$this->file_path["notice_file_path"].$files[$i] -> upload_file);
                        array_push($delete_file_sql_array,$files[$i] -> upload_file);
                        
                    }
                    
                    // 파일이 삭제된 경우
                    if (!isset($_FILES['add_file']['name'][$i])) {
                        array_push($delete_file_array,$this->file_path["notice_file_path"].$files[$i] -> upload_file);
                        array_push($delete_file_sql_array,$files[$i] -> upload_file);
                    }
                }

                //일단 파일 다 업로드
                // 직접 파일 업로드 처리 (modify용)
                $upload_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["notice_file_path"];
                if (!file_exists($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                
                $uploaded_files = array();
                $real_files = array();
                
                if(isset($_FILES['add_file']['name']) && is_array($_FILES['add_file']['name'])) {
                    for($i = 0; $i < count($_FILES['add_file']['name']); $i++) {
                        if($_FILES['add_file']['error'][$i] == 0 && !empty($_FILES['add_file']['name'][$i])) {
                            $original_name = $_FILES['add_file']['name'][$i];
                            $file_tmp = $_FILES['add_file']['tmp_name'][$i];
                            $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                            $new_file_name = time() . sprintf('%05d', $i) . '.' . $file_extension;
                            
                            if(move_uploaded_file($file_tmp, $upload_path . $new_file_name)) {
                                $uploaded_files[] = $new_file_name;
                                $real_files[] = $original_name;
                            }
                        }
                    }
                }
                
                // file_manager 방식으로 결과 구성
                $file_result = array(
                    "file_name" => $uploaded_files,
                    "real_name" => $real_files,
                    "error_code" => "",
                    "error_file_array" => array()
                );
                

                $save_files = array_merge($save_files, $file_result["error_file_array"]); //에러났을경우를 대비하여 저장된 파일 목록을 save_files에 merge

                for($i = 0; $i < count($file_result['file_name']);$i++){
                    if($_FILES['add_file']['name'][$i]!=null){
                        //새로 추가된 데이터가 있다면 delete_file_array에 이전 파일 이름 푸시
                        
                        //새로 추가된 파일 변환된 이름 file_array에 푸시
                        array_push($file_array, $file_result["file_name"][$i]);
                        //새로 추가된 파일 실제이름 real_file_array에 푸시
                        array_push($real_file_array, $_FILES['add_file']['name'][$i]);
                    }
                }
        
                
                $delete_sql = "";
                if(count($delete_file_sql_array) > 0){
                    $delete_sql = "delete from notice_file where upload_file in (";
                    for($i=0;$i<count($delete_file_sql_array);$i++){
                        if($i==0){
                            $delete_sql .= $this->null_check($delete_file_sql_array[$i]);
                        }else{
                            $delete_sql .= ", ".$this->null_check($delete_file_sql_array[$i]);
                        }
                    }
                    $delete_sql .= ") ";
                    
                    $delete_result = $this->conn->db_delete($delete_sql);
                    if($delete_result['result']==0){
                        $this->result['result']='0';
                        $this->result['message']='데이터를 삭제하는데 실패하였습니다.';

                    }
                    // print_r($delete_result);
                    //     exit;
                }
                $this->conn->s_transaction(); //트랜잭션 시작
                $insert_sql = "";
                if(count($file_array) > 0){
                    $insert_sql = "insert into notice_file (notice_idx, upload_file, real_file) values (";
                    for($i=0;$i<count($file_array);$i++){
                        if($i==0){
                            $insert_sql .= "$idx, '".$file_array[$i]."', '".$real_file_array[$i]."'";
                        }else{
                            $insert_sql .= "), ("."$idx, '".$file_array[$i]."', '".$real_file_array[$i]."'";
                        }
                    }
                    $insert_sql .= ") ";

                    $insert_result = $this->conn->db_insert($insert_sql);
                    if($insert_result['result']==0){
                        $this->result['result']='0';
                        $this->result['message']='데이터를 삭제하는데 실패하였습니다.';
                        $this->conn->rollback();
                    }
                }
                //썸노트 처리 시작
                $content = $this->sumnote["sumnote"]; //언어별 description [base64, base64]
                $add_img_array = json_decode($param["add_img_array"]); //새로 추가된 이미지
                $del_img_array = json_decode($param["del_img_array"]); //삭제된 이미지
                // 업데이트 후 삭제할 이미지 파일 배열
                $diff_array = $del_img_array;

                //삭제할 파일 sumnote_img table에서도 삭제시켜주기
                if(count($diff_array) > 0){
                    $sql = "delete from sumnote_img where file_name = ";
                    for($i = 0; $i < count($diff_array); $i++){
                        if($i == 0){
                            $sql .= $this->null_check($diff_array[$i]);
                        }else{
                            $sql .= " or file_name = ".$this->null_check($diff_array[$i]);
                        }
                    }
                    $this->conn->db_delete($sql);
                }

                //추가된 sumnote 퍄일은 sumnote_img table의 state 값을 1로 변경 시켜주기
                if(count($add_img_array) > 0){
                    $sql = "update sumnote_img set state = 1";
                    for($i = 0; $i < count($add_img_array); $i++){
                        if($i == 0){
                            $sql .= " where file_name = ".$this->null_check($add_img_array[$i]);
                        }else{
                            $sql .= " or file_name = ".$this->null_check($add_img_array[$i]);
                        }
                    }
                    $this->conn->db_update($sql);
                }

                $delete_file_arr = array();
                foreach($diff_array as $key => $value){
                    $diff_file_path = $this->file_path["notice_content_path"].$value;
                    array_push($delete_file_arr, $diff_file_path);
                }
                //썸노트 이미지 삭제
                $this->file_manager->delete_file($delete_file_arr);
                //첨부파일 삭제
                $this->file_manager->delete_file($delete_file_array);
                
                // print_r($content);
                // exit;
                $kind = isset($param["kind"]) ? intval($param["kind"]) : 0;
                $sql = "update notice_name set ";
                $sql .= "title = ".$this->null_check($param["title"]).","; //제목
                $sql .= "content = ".$this->null_check($content).","; //내용
                $sql .= "kind = ".$kind; //중요도
                $sql .= " where notice_idx = $idx";

                $result = $this->conn->db_update($sql);
                
                // 카테고리 업데이트
                if($result['result']=='1' && isset($param["category"])){
                    $category_sql = "update notice set category = ".$this->null_check($param["category"])." where idx = $idx";
                    $result = $this->conn->db_update($category_sql);
                }
                
                if($result['result']=='1'){
                    $this->conn->commit();
                }
                $this->result = $result;    
                
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 공지 리스트 불러오기
        // 설 명 : 
        // 만든이: 최재혁
        *********************************************************************/
        function notice_list(){
            $param = $this->param;
            // print_r($param);
            // exit;
        
            
            if($this->value_check(array("page_count","page_size"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $move_list = json_decode($param["move_list"], true);
                
                // $sql = "select * from notice as t1 ";
                // $sql .= " left join notice_name as t2 on t1.idx = t2.notice_idx ";
                // $sql .= " where t1.idx is not null ";
                $sql = "select t1.*, t2.*,t3.real_file from notice as t1 ";
                $sql .= " left join notice_name as t2 on t1.idx = t2.notice_idx ";
                $sql .= " left join notice_file as t3 on t1.idx = t3.notice_idx ";
                $sql .= "where t1.idx is not null AND (t3.upload_file IS NOT NULL OR t3.upload_file IS NULL)";
                    
                
                // 검색어 부분
                if(isset($move_list["keyword"])){
                    if($move_list["keyword"] != "" ){
                        if($move_list["search_kind"] == 'all'){
                            $sql .= " and (t2.title like '%".$move_list["keyword"]."%' ";
                            $sql .= " or t2.content like '%".$move_list["keyword"]."%' ";
                            $sql .= " or t3.real_file like '%".$move_list["keyword"]."%') ";
                        }else if($move_list["search_kind"] == 'title'){
                            $sql .= " and t2.title like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == 'content'){
                            $sql .= " and t2.content like '%".$move_list["keyword"]."%' ";
                        }else if($move_list["search_kind"] == 'file'){
                            $sql .= " and t3.real_file like '%".$move_list["keyword"]."%' ";
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
                
                // 카테고리 필터
                if(isset($move_list["category"])){
                    if($move_list["category"] != ""){
                        $sql .= " and t1.category = ".$this->null_check($move_list["category"])." ";
                    }
                }
                // 등록일이 최신순인 순서부터 정렬
                $sql .= " group by t1.idx order by t1.regdate desc ";
                $sql .= " limit ".$page_size*($page-1).",".$page_size;
                // $sql .= " where (date_format(t1.regdate, '%Y-%m-%d') >= $start_date and date_format(t1.regdate, '%Y-%m-%d') <= $end_date)";

                // $sql .= " group by t1.order_number, t2.option_2_idx order by t1.regdate asc";
                // print_r($sql);
                // exit;


                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{

                    //반복되는 sql문 (inner_sql)
                    // $inner_sql = " left join notice_name as t2 on t1.idx = t2.notice_idx ";
                    // // $inner_sql .= "on t1.user_idx = t2.idx ";
                    // $inner_sql .= "where t1.idx is not null ";
                    $inner_sql = " left join notice_name as t2 on t1.idx = t2.notice_idx ";
                    $inner_sql .= " left join notice_file as t3 on t1.idx = t3.notice_idx ";
                    $inner_sql .= "where t1.idx is not null AND (t3.upload_file IS NOT NULL OR t3.upload_file IS NULL)";
                    // 검색어부분
                    if(isset($move_list["keyword"])){
                        if($move_list["keyword"] != "" ){
                            // // 제목으록 검색했을시
                            // if($move_list["search_kind"] == "title"){
                            //     $inner_sql .= " and t2.title like '%".$move_list["keyword"]."%' ";
                            // }else if($move_list["search_kind"] == "kind"){
                            //     $inner_sql .= " and t2.kind like '%".$move_list["keyword"]."%' ";
                            // // }else if($move_list["search_kind"] == "id"){
                            // //     $inner_sql .= " and t2.email like '%".$move_list["keyword"]."%' ";
                            // }
                            if($move_list["search_kind"] == 'all'){
                                $inner_sql .= " and (t2.title like '%".$move_list["keyword"]."%' ";
                                $inner_sql .= " or t2.content like '%".$move_list["keyword"]."%' ";
                                $inner_sql .= " or t3.real_file like '%".$move_list["keyword"]."%') ";
                            }else if($move_list["search_kind"] == 'title'){
                                $inner_sql .= " and t2.title like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == 'content'){
                                $inner_sql .= " and t2.content like '%".$move_list["keyword"]."%' ";
                            }else if($move_list["search_kind"] == 'file'){
                                $inner_sql .= " and t3.real_file like '%".$move_list["keyword"]."%' ";
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
                    
                    // 카테고리 필터 (total_count 계산에 누락되어 있던 부분)
                    if(isset($move_list["category"])){
                        if($move_list["category"] != ""){
                            $inner_sql .= " and t1.category = ".$this->null_check($move_list["category"])." ";
                        }
                    }
                    
                    $sql = "select * from ";
                    $sql .= "(";
                    $sql .= "select count(DISTINCT t1.idx) as total from notice as t1 ";
                    $sql .= $inner_sql;
                    $sql .= ") R1";
                    // $sql .= ") R1 ,";
                    // $sql .= "(";
                    // $sql .= "select count(t1.idx) as answer from notice as t1 ";
                    // $sql .= $inner_sql;
                    // $sql .= "and answer is not null ";
                    // $sql .= ") R2 ,";
                    // $sql .= "(";
                    // $sql .= "select count(t1.idx) as unanswer from notice as t1 ";
                    // $sql .= $inner_sql;
                    // $sql .= "and answer is null ";
                    // $sql .= ") R3 ";
                    // print_r($sql);
                    // exit;
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
                            // if($move_list["tab"] != ""){
                            //     if($move_list["tab"] == "second"){
                            //         $this->result["total_count"] = $total_result["value"][0]["answer"];
                            //     }else if($move_list["tab"] == "third"){
                            //         $this->result["total_count"] = $total_result["value"][0]["unanswer"];
                            //     }else{
                            //         $this->result["total_count"] = $total_result["value"][0]["total"];
                            //     }
                            // }else{
                            //     $this->result["total_count"] = $total_result["value"][0]["total"];
                            // }
                        }else{
                            $this->result["total_count"] = $total_result["value"][0]["total"];
                        }
                        $this->result["total"] = $total_result["value"][0]["total"];
                        // $this->result["answer_count"] = $total_result["value"][0]["answer"];
                        // $this->result["unanswer_count"] = $total_result["value"][0]["unanswer"];

                    }
                    
                }
            }
            // exit;
            echo $this->jsonEncode($this->result );
        }
        
        /********************************************************************* 
        // 함 수 : 공지사항 삭제
        // 설 명 : 선택된 공지사항들을 삭제
        // 만든이: AI Assistant
        *********************************************************************/
        function request_delete_notice(){
            $param = $this->param;
            
            if($this->value_check(array("target"))){
                $target_array = json_decode($param["target"]);
                
                if(count($target_array) > 0){
                    $this->conn->s_transaction();
                    
                    $success_count = 0;
                    $delete_files = array(); // 삭제할 파일들
                    
                    foreach($target_array as $notice_idx){
                        // 먼저 관련 파일 정보 조회
                        $file_sql = "SELECT upload_file FROM notice_file WHERE notice_idx = ".$this->null_check($notice_idx);
                        $file_result = $this->conn->db_select($file_sql);
                        
                        if($file_result["result"] == "1" && isset($file_result["value"])){
                            foreach($file_result["value"] as $file){
                                if(!empty($file["upload_file"])){
                                    $delete_files[] = $this->file_path["notice_file_path"].$file["upload_file"];
                                }
                            }
                        }
                        
                        // notice_file 테이블에서 삭제
                        $delete_file_sql = "DELETE FROM notice_file WHERE notice_idx = ".$this->null_check($notice_idx);
                        $file_delete_result = $this->conn->db_delete($delete_file_sql);
                        
                        // notice_name 테이블에서 삭제
                        $delete_name_sql = "DELETE FROM notice_name WHERE notice_idx = ".$this->null_check($notice_idx);
                        $name_delete_result = $this->conn->db_delete($delete_name_sql);
                        
                        // notice 테이블에서 삭제
                        $delete_notice_sql = "DELETE FROM notice WHERE idx = ".$this->null_check($notice_idx);
                        $notice_delete_result = $this->conn->db_delete($delete_notice_sql);
                        
                        if($notice_delete_result["result"] == "1"){
                            $success_count++;
                        } else {
                            // 하나라도 실패하면 롤백
                            $this->conn->rollback();
                            $this->result = array(
                                "result" => "0",
                                "error_code" => null,
                                "message" => "공지사항 삭제 중 오류가 발생했습니다.",
                                "value" => null
                            );
                            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                            return;
                        }
                    }
                    
                    // 모든 삭제가 성공한 경우
                    $this->conn->commit();
                    
                    // 파일 삭제
                    foreach($delete_files as $file_path){
                        if(file_exists($file_path)){
                            unlink($file_path);
                        }
                    }
                    
                    $this->result = array(
                        "result" => "1",
                        "error_code" => null,
                        "message" => $success_count."개의 공지사항이 삭제되었습니다.",
                        "value" => null
                    );
                } else {
                    $this->result = array(
                        "result" => "0",
                        "error_code" => null,
                        "message" => "삭제할 공지사항이 선택되지 않았습니다.",
                        "value" => null
                    );
                }
            }
            
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }
        
        /********************************************************************* 
        // 함 수 : 개인정보 방침 불러오기
        // 설 명 : 
        // 만든이: 최재혁
        *********************************************************************/
        function init_privacy(){
            $param = $this->param;
            // print_r($param);
            // exit;
            $sql = "select * from terms_name where terms_idx=".$param['idx'];
            $this->result = $this->conn->db_select($sql);
            echo $this->jsonEncode($this->result);
        }
        /********************************************************************* 
        // 함 수 : 개인정보 방침 업데이트
        // 설 명 : 
        // 만든이: 최재혁
        *********************************************************************/
        function update_privacy(){
            $param = $this->param;
            // print_r($param);
            // exit;
            $content = $this->null_check($param['content']);
            if($this->value_check(array('content'))){
                
                $sql = "update terms_name set content = $content where terms_idx = ".$param['idx'];
                $this->result = $this->conn->db_update($sql);
            }
            echo $this->jsonEncode($this->result);
        }

    /********************************************************************* 
    // 함 수 : notice_register_with_files
    // 설 명 : 새로운 방식의 공지사항 등록 (파일 업로드 포함)
    *********************************************************************/
    function notice_register_with_files(){
        $param = $this->param;
        
        $this->conn->s_transaction();
        
        try {
            // 1. 공지사항 기본 정보 저장
            $category = $this->null_check($param["category"]);
            $sql = "INSERT INTO notice (regdate, category) VALUES (NOW(), $category)";
            $result = $this->conn->db_insert($sql);
            
            if($result['result'] == 0){
                throw new Exception("공지사항 등록 실패");
            }
            
            $notice_idx = $result['value'];
            
            // 2. 공지사항 제목/내용 저장
            $title = $this->null_check($param["title"]);
            $content = $this->null_check($param["sumnote"]);
            $kind = isset($param["kind"]) ? intval($param["kind"]) : 0;
            $sql = "INSERT INTO notice_name (notice_idx, title, content, kind) VALUES ($notice_idx, $title, $content, $kind)";
            $result = $this->conn->db_insert($sql);
            
            if($result['result'] == 0){
                throw new Exception("공지사항 내용 등록 실패");
            }
            
            // 3. 첨부파일 정보 저장
            if(!empty($param["uploaded_files"])) {
                $uploaded_files = json_decode($param["uploaded_files"], true);
                
                if(is_array($uploaded_files) && count($uploaded_files) > 0) {
                    $file_sql = "INSERT INTO notice_file (notice_idx, upload_file, real_file) VALUES ";
                    $file_values = array();
                    
                    foreach($uploaded_files as $file) {
                        $upload_file = $this->null_check($file['upload_file']);
                        $real_file = $this->null_check($file['real_file']);
                        $file_values[] = "($notice_idx, $upload_file, $real_file)";
                    }
                    
                    $file_sql .= implode(', ', $file_values);
                    $result = $this->conn->db_insert($file_sql);
                    
                    if($result['result'] == 0){
                        throw new Exception("첨부파일 정보 저장 실패");
                    }
                }
            }
            
            $this->conn->commit();
            $this->result["result"] = "1";
            $this->result["message"] = "공지사항 등록 성공";
            
        } catch (Exception $e) {
            $this->conn->rollback();
            $this->result["result"] = "0";
            $this->result["message"] = $e->getMessage();
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // ===================== 홍보/행사 관리 메소드들 =====================
    
    // 홍보/행사 목록 조회
    function get_promotion_list(){
        try {
            $page = isset($this->param["page"]) ? intval($this->param["page"]) : 1;
            $limit = isset($this->param["limit"]) ? intval($this->param["limit"]) : 10;
            $offset = ($page - 1) * $limit;
            
            $search_keyword = isset($this->param["search_keyword"]) ? trim($this->param["search_keyword"]) : "";
            
            // WHERE 조건 구성
            $where_clause = "WHERE 1=1";
            if (!empty($search_keyword)) {
                $where_clause .= " AND (event_name LIKE '%$search_keyword%' OR event_location LIKE '%$search_keyword%')";
            }
            
            // 전체 개수 조회
            $count_sql = "SELECT COUNT(*) as total FROM promotion $where_clause";
            $count_result = $this->conn->db_select($count_sql);
            $total_count = $count_result['value'][0]['total'];
            
            // 목록 조회
            $list_sql = "SELECT idx, event_name, event_period, event_location, award_badge, 
                               is_active, sort_order, regdate, moddate,
                               DATE_FORMAT(regdate, '%Y.%m.%d %H:%i') as formatted_regdate,
                               DATE_FORMAT(moddate, '%Y.%m.%d %H:%i') as formatted_moddate
                        FROM promotion 
                        $where_clause 
                        ORDER BY sort_order ASC, idx DESC 
                        LIMIT $limit OFFSET $offset";
                        
            $list_result = $this->conn->db_select($list_sql);
            
            $this->result["result"] = "1";
            $this->result["value"] = $list_result['value'];
            $this->result["total_count"] = $total_count;
            $this->result["total_pages"] = ceil($total_count / $limit);
            $this->result["current_page"] = $page;
            
        } catch (Exception $e) {
            error_log("get_promotion_list 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "목록 조회 중 오류가 발생했습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 상세 조회
    function get_promotion_detail(){
        try {
            $idx = intval($this->param["idx"]);
            
            // 메인 정보 조회
            $sql = "SELECT * FROM promotion WHERE idx = $idx";
            $result = $this->conn->db_select($sql);
            
            if ($result['result'] == 0 || empty($result['value'])) {
                $this->result["result"] = "0";
                $this->result["message"] = "해당 데이터를 찾을 수 없습니다.";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $promotion_data = $result['value'][0];
            
            // 메인 이미지 경로 설정
            if (!empty($promotion_data['main_image'])) {
                $promotion_data['main_image'] = $this->file_link["promotion_main_image_path"] . $promotion_data['main_image'];
            }
            
            // 서브 이미지 조회
            $sub_images_sql = "SELECT * FROM promotion_images WHERE promotion_idx = $idx ORDER BY sort_order ASC";
            $sub_images_result = $this->conn->db_select($sub_images_sql);
            
            $sub_images = array();
            if ($sub_images_result['result'] == 1 && !empty($sub_images_result['value'])) {
                foreach ($sub_images_result['value'] as $image) {
                    $image['image_path'] = $this->file_link["promotion_sub_image_path"] . $image['image_file'];
                    $sub_images[] = $image;
                }
            }
            
            $promotion_data['sub_images'] = $sub_images;
            
            $this->result["result"] = "1";
            $this->result["value"] = array($promotion_data);
            
        } catch (Exception $e) {
            error_log("get_promotion_detail 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "상세 조회 중 오류가 발생했습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 등록
    function promotion_register(){
        try {
            $this->conn->s_transaction();
            
            // 기본 정보 INSERT
            $event_name = $this->null_check($this->param["event_name"]);
            $event_period = $this->null_check($this->param["event_period"]);
            $event_location = $this->null_check($this->param["event_location"]);
            $award_badge = $this->null_check($this->param["award_badge"]);
            $content = $this->null_check($this->param["content"]);
            $is_active = isset($this->param["is_active"]) ? intval($this->param["is_active"]) : 1;
            
            $sql = "INSERT INTO promotion (event_name, event_period, event_location, award_badge, content, is_active) 
                   VALUES ($event_name, $event_period, $event_location, $award_badge, $content, $is_active)";
            
            $result = $this->conn->db_insert($sql);
            
            if($result['result'] == 0){
                throw new Exception("홍보/행사 등록 실패");
            }
            
            $promotion_idx = $result['value'];
            
            // 메인 이미지 업로드 처리
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                $main_image_result = $this->upload_main_image($promotion_idx, $_FILES['main_image']);
                if ($main_image_result['result'] == 'success') {
                    $update_sql = "UPDATE promotion SET main_image = '".$main_image_result['filename']."' WHERE idx = $promotion_idx";
                    $this->conn->db_update($update_sql);
                }
            }
            
            // 서브 이미지 업로드 처리
            if (isset($_FILES['sub_images'])) {
                $this->upload_sub_images($promotion_idx, $_FILES['sub_images']);
            }
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "등록이 완료되었습니다.";
            $this->result["value"] = $promotion_idx;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("promotion_register 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "등록 중 오류가 발생했습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 삭제
    function promotion_delete(){
        try {
            $idx = intval($this->param["idx"]);
            
            $this->conn->s_transaction();
            
            // 기존 이미지 파일들 삭제
            $this->delete_promotion_files($idx);
            
            // 서브 이미지 레코드 삭제
            $delete_images_sql = "DELETE FROM promotion_images WHERE promotion_idx = $idx";
            $this->conn->db_delete($delete_images_sql);
            
            // 메인 레코드 삭제
            $delete_sql = "DELETE FROM promotion WHERE idx = $idx";
            $result = $this->conn->db_delete($delete_sql);
            
            if($result['result'] == 0){
                throw new Exception("홍보/행사 삭제 실패");
            }
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "삭제가 완료되었습니다.";
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("promotion_delete 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "삭제 중 오류가 발생했습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 수정
    function promotion_modify(){
        try {
            $this->conn->s_transaction();
            
            // 디버깅: 받은 파라미터 로그
            error_log("promotion_modify 받은 파라미터: " . print_r($this->param, true));
            
            $idx = intval($this->param["idx"]);
            $event_name = $this->null_check($this->param["event_name"]);
            $event_period = $this->null_check($this->param["event_period"]);
            $event_location = $this->null_check($this->param["event_location"]);
            $award_badge = $this->null_check($this->param["award_badge"]);
            $content = $this->null_check($this->param["content"]);
            $is_active = isset($this->param["is_active"]) ? intval($this->param["is_active"]) : 0;
            
            // 디버깅: 처리된 값들 로그
            error_log("처리된 값들 - idx: $idx, event_name: $event_name, event_period: $event_period, event_location: $event_location");
            error_log("award_badge: $award_badge, content 길이: " . strlen($content) . ", is_active: $is_active");
            
            // 기본 정보 업데이트
            $sql = "UPDATE promotion SET 
                    event_name = $event_name, 
                    event_period = $event_period, 
                    event_location = $event_location, 
                    award_badge = $award_badge, 
                    content = $content, 
                    is_active = $is_active, 
                    moddate = NOW() 
                    WHERE idx = $idx";
            
            // 디버깅: SQL 쿼리 로그
            error_log("실행할 SQL: " . $sql);
            
            $result = $this->conn->db_update($sql);
            
            // 디버깅: 업데이트 결과 로그
            error_log("db_update 결과: " . print_r($result, true));
            
            if($result['result'] == 0){
                throw new Exception("홍보/행사 수정 실패");
            }
            
            // 기존 메인 이미지 삭제 처리
            if (isset($this->param["deleted_main_image"]) && $this->param["deleted_main_image"] == 1) {
                // 기존 메인 이미지 파일 삭제
                $existing_sql = "SELECT main_image FROM promotion WHERE idx = $idx";
                $existing_result = $this->conn->db_select($existing_sql);
                if ($existing_result['result'] == 1 && !empty($existing_result['value'][0]['main_image'])) {
                    $existing_file = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_main_image_path"] . $existing_result['value'][0]['main_image'];
                    if (file_exists($existing_file)) {
                        unlink($existing_file);
                    }
                }
                
                // DB에서 메인 이미지 정보 삭제
                $update_sql = "UPDATE promotion SET main_image = NULL WHERE idx = $idx";
                $this->conn->db_update($update_sql);
            }
            
            // 새로운 메인 이미지 업로드 처리
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
                // 기존 메인 이미지 파일 삭제 (새 이미지로 교체)
                $existing_sql = "SELECT main_image FROM promotion WHERE idx = $idx";
                $existing_result = $this->conn->db_select($existing_sql);
                if ($existing_result['result'] == 1 && !empty($existing_result['value'][0]['main_image'])) {
                    $existing_file = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_main_image_path"] . $existing_result['value'][0]['main_image'];
                    if (file_exists($existing_file)) {
                        unlink($existing_file);
                    }
                }
                
                $main_image_result = $this->upload_main_image($idx, $_FILES['main_image']);
                if ($main_image_result['result'] == 'success') {
                    $update_sql = "UPDATE promotion SET main_image = '".$main_image_result['filename']."' WHERE idx = $idx";
                    $this->conn->db_update($update_sql);
                }
            }
            
            // 삭제할 서브 이미지 처리
            if (isset($this->param["deleted_sub_images"]) && !empty($this->param["deleted_sub_images"])) {
                $deleted_images = json_decode($this->param["deleted_sub_images"], true);
                if (is_array($deleted_images) && count($deleted_images) > 0) {
                    foreach ($deleted_images as $image_idx) {
                        // 파일 삭제
                        $file_sql = "SELECT image_file FROM promotion_images WHERE idx = " . intval($image_idx);
                        $file_result = $this->conn->db_select($file_sql);
                        if ($file_result['result'] == 1 && !empty($file_result['value'][0]['image_file'])) {
                            $file_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_sub_image_path"] . $file_result['value'][0]['image_file'];
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }
                        
                        // DB에서 레코드 삭제
                        $delete_sql = "DELETE FROM promotion_images WHERE idx = " . intval($image_idx);
                        $this->conn->db_delete($delete_sql);
                    }
                }
            }
            
            // 새로운 서브 이미지 업로드 처리
            if (isset($_FILES['sub_images'])) {
                $this->upload_sub_images($idx, $_FILES['sub_images']);
            }
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "수정이 완료되었습니다.";
            $this->result["value"] = $idx;
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("promotion_modify 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "수정 중 오류가 발생했습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 순서 변경
    function promotion_sort_change(){
        try {
            $idx = intval($this->param["idx"]);
            $direction = $this->param["direction"];
            
            if (!in_array($direction, ['up', 'down'])) {
                throw new Exception("잘못된 방향 파라미터입니다.");
            }
            
            $this->conn->s_transaction();
            
            // 현재 항목의 순서 조회
            $current_sql = "SELECT sort_order FROM promotion WHERE idx = $idx";
            $current_result = $this->conn->db_select($current_sql);
            
            if ($current_result['result'] == 0 || empty($current_result['value'])) {
                throw new Exception("해당 항목을 찾을 수 없습니다.");
            }
            
            $current_sort = intval($current_result['value'][0]['sort_order']);
            
            if ($direction == 'up') {
                // 위로 이동: 현재보다 작은 순서 중 가장 큰 것과 교체
                $target_sql = "SELECT idx, sort_order FROM promotion WHERE sort_order < $current_sort ORDER BY sort_order DESC LIMIT 1";
            } else {
                // 아래로 이동: 현재보다 큰 순서 중 가장 작은 것과 교체
                $target_sql = "SELECT idx, sort_order FROM promotion WHERE sort_order > $current_sort ORDER BY sort_order ASC LIMIT 1";
            }
            
            $target_result = $this->conn->db_select($target_sql);
            
            if ($target_result['result'] == 0 || empty($target_result['value'])) {
                throw new Exception("더 이상 이동할 수 없습니다.");
            }
            
            $target_idx = intval($target_result['value'][0]['idx']);
            $target_sort = intval($target_result['value'][0]['sort_order']);
            
            // 순서 교체
            $update1_sql = "UPDATE promotion SET sort_order = $target_sort WHERE idx = $idx";
            $update2_sql = "UPDATE promotion SET sort_order = $current_sort WHERE idx = $target_idx";
            
            $this->conn->db_update($update1_sql);
            $this->conn->db_update($update2_sql);
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "순서가 변경되었습니다.";
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("promotion_sort_change 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = $e->getMessage();
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 순서 일괄 저장
    function promotion_sort_save(){
        try {
            $sort_data = json_decode($this->param["sort_data"], true);
            
            if (!is_array($sort_data) || empty($sort_data)) {
                throw new Exception("저장할 순서 데이터가 없습니다.");
            }
            
            $this->conn->s_transaction();
            
            foreach ($sort_data as $item) {
                $idx = intval($item['idx']);
                $sort_order = intval($item['sort_order']);
                
                if ($idx <= 0 || $sort_order <= 0) {
                    continue; // 잘못된 데이터는 건너뛰기
                }
                
                $update_sql = "UPDATE promotion SET sort_order = $sort_order WHERE idx = $idx";
                $this->conn->db_update($update_sql);
            }
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "순서가 저장되었습니다.";
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("promotion_sort_save 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = $e->getMessage();
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 순서 변경 (popup 방식)
    function request_promotion_relation_change(){
        try {
            $relation_array = json_decode($this->param["relation_array"], true);
            
            if (!is_array($relation_array) || empty($relation_array)) {
                throw new Exception("순서 데이터가 없습니다.");
            }
            
            $this->conn->s_transaction();
            
            // 순서대로 sort_order 업데이트
            foreach ($relation_array as $index => $idx) {
                $sort_order = $index + 1; // 1부터 시작
                $update_sql = "UPDATE promotion SET sort_order = $sort_order WHERE idx = " . intval($idx);
                $this->conn->db_update($update_sql);
            }
            
            $this->conn->commit();
            
            $this->result["result"] = "1";
            $this->result["message"] = "순서가 변경되었습니다.";
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("request_promotion_relation_change 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = $e->getMessage();
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 메인 이미지 업로드
    private function upload_main_image($promotion_idx, $file){
        try {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_main_image_path"];
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if (!in_array($file_extension, $allowed_extensions)) {
                return array("result" => "error", "message" => "지원하지 않는 이미지 형식입니다.");
            }
            
            $filename = "main_" . $promotion_idx . "_" . time() . "." . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                return array("result" => "success", "filename" => $filename);
            } else {
                return array("result" => "error", "message" => "파일 업로드에 실패했습니다.");
            }
            
        } catch (Exception $e) {
            error_log("upload_main_image 에러: " . $e->getMessage());
            return array("result" => "error", "message" => "이미지 업로드 중 오류가 발생했습니다.");
        }
    }

    // 서브 이미지 업로드
    private function upload_sub_images($promotion_idx, $files){
        try {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_sub_image_path"];
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            $sort_order = 1;
            
            // 다중 파일 처리
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] == 0 && !empty($files['name'][$i])) {
                    $file_extension = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $filename = "sub_" . $promotion_idx . "_" . $i . "_" . time() . "." . $file_extension;
                        $upload_path = $upload_dir . $filename;
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                            // DB에 저장
                            $image_alt = $this->null_check($files['name'][$i]);
                            $insert_sql = "INSERT INTO promotion_images (promotion_idx, image_file, image_alt, sort_order) 
                                          VALUES ($promotion_idx, '$filename', $image_alt, $sort_order)";
                            $this->conn->db_insert($insert_sql);
                            $sort_order++;
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("upload_sub_images 에러: " . $e->getMessage());
        }
    }

    // 홍보/행사 파일들 삭제
    private function delete_promotion_files($promotion_idx){
        try {
            // 메인 이미지 조회 및 삭제
            $main_sql = "SELECT main_image FROM promotion WHERE idx = $promotion_idx";
            $main_result = $this->conn->db_select($main_sql);
            if ($main_result['result'] == 1 && !empty($main_result['value'])) {
                $main_data = $main_result['value'][0];
                if (!empty($main_data['main_image'])) {
                    $main_file_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_main_image_path"] . $main_data['main_image'];
                    if (file_exists($main_file_path)) {
                        unlink($main_file_path);
                    }
                }
            }
            
            // 서브 이미지 조회 및 삭제
            $sub_sql = "SELECT image_file FROM promotion_images WHERE promotion_idx = $promotion_idx";
            $sub_result = $this->conn->db_select($sub_sql);
            if ($sub_result['result'] == 1 && !empty($sub_result['value'])) {
                foreach($sub_result['value'] as $row) {
                    $sub_file_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->project_name . "/" . $this->file_path["promotion_sub_image_path"] . $row['image_file'];
                    if (file_exists($sub_file_path)) {
                        unlink($sub_file_path);
                    }
                }
            }
            
        } catch (Exception $e) {
            error_log("delete_promotion_files 에러: " . $e->getMessage());
        }
        }

    /********************************************************************* 
    // 함수: 신청서 리스트 조회
    // 설명: 관리자용 신청서 리스트 조회 (검색, 필터링, 페이징)
    *********************************************************************/
    function get_application_list(){
        $param = $this->param;
        
        // 기본값 설정
        $page = intval($param['page'] ?? 1);
        $limit = intval($param['limit'] ?? 20);
        $offset = ($page - 1) * $limit;
        
        // 검색 조건
        $search_type = $param['search_type'] ?? 'all';
        $search_keyword = $param['search_keyword'] ?? '';
        $status_filter = $param['status_filter'] ?? '';
        $date_from = $param['date_from'] ?? '';
        $date_to = $param['date_to'] ?? '';
        $applicant_type_filter = $param['applicant_type_filter'] ?? '';
        
        // WHERE 조건 구성
        $where_conditions = array();
        
        // 검색 조건
        if(!empty($search_keyword)) {
            $search_keyword = $this->null_check($search_keyword);
            
            if($search_type == 'name') {
                $where_conditions[] = "name LIKE %{$search_keyword}%";
            } elseif($search_type == 'phone') {
                $where_conditions[] = "phone LIKE %{$search_keyword}%";
            } elseif($search_type == 'email') {
                $where_conditions[] = "email LIKE %{$search_keyword}%";
            } else {
                // 전체 검색
                $where_conditions[] = "(name LIKE %{$search_keyword}% OR phone LIKE %{$search_keyword}% OR email LIKE %{$search_keyword}%)";
            }
        }
        
        // 상태 필터
        if(!empty($status_filter)) {
            $status_filter = $this->null_check($status_filter);
            $where_conditions[] = "status = {$status_filter}";
        }
        
        // 신청자 유형 필터
        if(!empty($applicant_type_filter)) {
            $applicant_type_filter = $this->null_check($applicant_type_filter);
            $where_conditions[] = "applicant_type = {$applicant_type_filter}";
        }
        
        // 날짜 범위 필터
        if(!empty($date_from)) {
            $date_from = $this->null_check($date_from);
            $where_conditions[] = "DATE(regdate) >= {$date_from}";
        }
        if(!empty($date_to)) {
            $date_to = $this->null_check($date_to);
            $where_conditions[] = "DATE(regdate) <= {$date_to}";
        }
        
        $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);
        
        // 전체 개수 조회
        $count_sql = "SELECT COUNT(*) as total FROM application {$where_clause}";
        $count_result = $this->conn->db_select($count_sql);
        $total_count = 0;
        
        if($count_result["result"] == "1" && !empty($count_result["value"])) {
            $total_count = intval($count_result["value"][0]["total"]);
        }
        
        // 상태별 개수 조회
        $status_sql = "SELECT 
                        status,
                        COUNT(*) as count 
                      FROM application 
                      GROUP BY status";
        $status_result = $this->conn->db_select($status_sql);
        $status_counts = array();
        
        if($status_result["result"] == "1" && !empty($status_result["value"])) {
            foreach($status_result["value"] as $row) {
                $status_counts[$row["status"]] = intval($row["count"]);
            }
        }
        
        // 리스트 조회
        $order_by = "ORDER BY regdate DESC";
        $list_sql = "SELECT 
                        idx, applicant_type, name, birthdate, phone, email, 
                        address, reason, status, regdate, moddate
                    FROM application 
                    {$where_clause} 
                    {$order_by} 
                    LIMIT {$limit} OFFSET {$offset}";
        
        $list_result = $this->conn->db_select($list_sql);
        
        if($list_result["result"] == "1") {
            $this->result["result"] = "1";
            $this->result["message"] = "신청서 리스트 조회 성공";
            $this->result["list"] = $list_result["value"] ?: array();
            $this->result["total_count"] = $total_count;
            $this->result["current_page"] = $page;
            $this->result["limit"] = $limit;
            $this->result["total_pages"] = ceil($total_count / $limit);
            $this->result["status_counts"] = $status_counts;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "신청서 리스트 조회 실패";
            $this->result["list"] = array();
            $this->result["total_count"] = 0;
            $this->result["current_page"] = $page;
            $this->result["limit"] = $limit;
            $this->result["total_pages"] = 0;
            $this->result["status_counts"] = array();
        }
        
        echo $this->jsonEncode($this->result);
    }

    /********************************************************************* 
    // 함수: 신청서 상세 조회
    // 설명: 특정 신청서의 상세 정보 조회
    *********************************************************************/
    function get_application_detail(){
        $param = $this->param;
        $idx = intval($param['idx'] ?? 0);
        
        if(empty($idx)) {
            $this->result["result"] = "0";
            $this->result["message"] = "신청서 ID가 필요합니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        $sql = "SELECT * FROM application WHERE idx = {$idx}";
        $result = $this->conn->db_select($sql);
        
        if($result["result"] == "1" && !empty($result["value"])) {
            $application = $result["value"][0];
            
            // JSON 데이터 파싱
            if(!empty($application["traffic_sources"])) {
                $application["traffic_sources_array"] = json_decode($application["traffic_sources"], true) ?: array();
            } else {
                $application["traffic_sources_array"] = array();
            }
            
            $this->result["result"] = "1";
            $this->result["message"] = "신청서 상세 조회 성공";
            $this->result["value"] = $application;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "신청서를 찾을 수 없습니다.";
        }
        
        echo $this->jsonEncode($this->result);
    }

    /********************************************************************* 
    // 함수: 신청서 상태 변경
    // 설명: 신청서의 처리 상태를 변경
    *********************************************************************/
    function update_application_status(){
        $param = $this->param;
        $idx = intval($param['idx'] ?? 0);
        $status = $param['status'] ?? '';
        $admin_memo = $param['admin_memo'] ?? '';
        
        if(empty($idx) || empty($status)) {
            $this->result["result"] = "0";
            $this->result["message"] = "필수 입력사항이 누락되었습니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        // 유효한 상태 값 확인
        $valid_statuses = array('pending', 'processed', 'rejected');
        if(!in_array($status, $valid_statuses)) {
            $this->result["result"] = "0";
            $this->result["message"] = "유효하지 않은 상태 값입니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        $status = $this->null_check($status);
        $admin_memo = $this->null_check($admin_memo);
        $admin_id = $this->session['admin_login_status']['idx'] ?? 0;
        
        $sql = "UPDATE application SET status = {$status},admin_memo = {$admin_memo},processed_at = NOW(),processed_by = {$admin_id},moddate = NOW() WHERE idx = {$idx}";
        
        $result = $this->conn->db_update($sql);
        
        
        if($result["result"] == "1") {
            $this->result = $result;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "상태 변경에 실패했습니다.";
        }
        
        echo $this->jsonEncode($this->result);
    }

    /********************************************************************* 
    // 함수: 신청서 엑셀 다운로드
    // 설명: 신청서 데이터를 엑셀 파일로 다운로드
    *********************************************************************/
    function download_applications_excel(){
        $param = $this->param;
        
        // 검색 조건 (리스트와 동일한 조건 적용)
        $search_type = $param['search_type'] ?? 'all';
        $search_keyword = $param['search_keyword'] ?? '';
        $status_filter = $param['status_filter'] ?? '';
        $date_from = $param['date_from'] ?? '';
        $date_to = $param['date_to'] ?? '';
        $applicant_type_filter = $param['applicant_type_filter'] ?? '';
        
        // WHERE 조건 구성
        $where_conditions = array();
        
        if(!empty($search_keyword)) {
            $search_keyword = $this->null_check($search_keyword);
            
            if($search_type == 'name') {
                $where_conditions[] = "name LIKE %{$search_keyword}%";
            } elseif($search_type == 'phone') {
                $where_conditions[] = "phone LIKE %{$search_keyword}%";
            } elseif($search_type == 'email') {
                $where_conditions[] = "email LIKE %{$search_keyword}%";
            } else {
                $where_conditions[] = "(name LIKE %{$search_keyword}% OR phone LIKE %{$search_keyword}% OR email LIKE %{$search_keyword}%)";
            }
        }
        
        if(!empty($status_filter)) {
            $status_filter = $this->null_check($status_filter);
            $where_conditions[] = "status = {$status_filter}";
        }
        
        if(!empty($applicant_type_filter)) {
            $applicant_type_filter = $this->null_check($applicant_type_filter);
            $where_conditions[] = "applicant_type = {$applicant_type_filter}";
        }
        
        if(!empty($date_from)) {
            $date_from = $this->null_check($date_from);
            $where_conditions[] = "DATE(regdate) >= {$date_from}";
        }
        if(!empty($date_to)) {
            $date_to = $this->null_check($date_to);
            $where_conditions[] = "DATE(regdate) <= {$date_to}";
        }
        
        $where_clause = empty($where_conditions) ? "" : "WHERE " . implode(" AND ", $where_conditions);
        
        // 전체 데이터 조회
        $sql = "SELECT * FROM application {$where_clause} ORDER BY regdate DESC";
        $result = $this->conn->db_select($sql);
        
        if($result["result"] != "1" || empty($result["value"])) {
            $this->result["result"] = "0";
            $this->result["message"] = "다운로드할 데이터가 없습니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        // 헤더 설정
        $filename = "applications_" . date('Y-m-d_H-i-s') . ".csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        
        // UTF-8 BOM 추가 (Excel에서 한글 깨짐 방지)
        echo "\xEF\xBB\xBF";
        
        // CSV 헤더
        $headers = array(
            'NO', '신청자유형', '이름', '생년월일', '연락처', '이메일', '주소',
            '신청사유', '유입경로', '추가요청사항', '이용약관동의', '개인정보동의', 
            '마케팅동의', '상태', '관리자메모', '신청일시', '처리일시'
        );
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        // 데이터 출력
        foreach($result["value"] as $index => $row) {
            // 유입경로 JSON 파싱
            $traffic_sources = '';
            if(!empty($row["traffic_sources"])) {
                $sources_array = json_decode($row["traffic_sources"], true);
                if(is_array($sources_array)) {
                    $source_labels = array();
                    foreach($sources_array as $source) {
                        switch($source) {
                            case 'search-engine': $source_labels[] = '검색엔진'; break;
                            case 'youtube': $source_labels[] = '유튜브'; break;
                            case 'sns': $source_labels[] = 'SNS'; break;
                            case 'other': $source_labels[] = '기타'; break;
                            default: $source_labels[] = $source; break;
                        }
                    }
                    $traffic_sources = implode(', ', $source_labels);
                }
            }
            
            // 상태 한글 변환
            $status_ko = '';
            switch($row["status"]) {
                case 'pending': $status_ko = '대기'; break;
                case 'processed': $status_ko = '처리완료'; break;
                case 'rejected': $status_ko = '거절'; break;
                default: $status_ko = $row["status"]; break;
            }
            
            // 신청자 유형 한글 변환
            $applicant_type_ko = '';
            switch($row["applicant_type"]) {
                case 'user': $applicant_type_ko = '사용자 본인'; break;
                case 'guardian': $applicant_type_ko = '보호자'; break;
                default: $applicant_type_ko = $row["applicant_type"]; break;
            }
            
            // 신청 사유 한글 변환
            $reason_ko = '';
            switch($row["reason"]) {
                case 'elderly-care': $reason_ko = '노인 케어'; break;
                case 'disability-support': $reason_ko = '장애인 지원'; break;
                case 'health-monitoring': $reason_ko = '건강 모니터링'; break;
                case 'safety-protection': $reason_ko = '안전 보호'; break;
                case 'family-care': $reason_ko = '가족 케어'; break;
                case 'other': $reason_ko = '기타'; break;
                default: $reason_ko = $row["reason"]; break;
            }
            
            $csv_row = array(
                $index + 1,
                $applicant_type_ko,
                $row["name"],
                $row["birthdate"],
                $row["phone"],
                $row["email"],
                $row["address"],
                $reason_ko,
                $traffic_sources,
                $row["additional_requests"],
                $row["terms_agreement"] ? 'Y' : 'N',
                $row["privacy_agreement"] ? 'Y' : 'N',
                $row["marketing_agreement"] ? 'Y' : 'N',
                $status_ko,
                $row["admin_memo"],
                $row["regdate"],
                $row["processed_at"]
            );
            
            fputcsv($output, $csv_row);
        }
        
        fclose($output);
        exit;
    }

}
?>