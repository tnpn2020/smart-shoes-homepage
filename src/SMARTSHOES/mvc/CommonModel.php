<?php
    class CommonModel extends gf{
        private $json;
        private $dir;
        private $conn;

        function __construct($array){
            $this->json = $array["json"];
            $this->sumnote = $array["sumnote"];
            $this->dir = $array["dir"];
            $this->project_name = $array["project_name"];
            $this->conn = $array["db"];
            $this->file_manager = $array["file_manager"];
            $this->file_path = $array["sub_file_path"]->get_path_php();
            $this->file_link = $array["sub_file_path"]->get_link_php();
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
        // 만든이: 안정환
        // 담당자: 
        *********************************************************************/
        function value_check($check_value_array){
            $object = array(
                "param"=>$this->json,
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
        // 함 수 : request_lang()
        // 설 명 : 프로젝트에 적용되는 언어 목록 가져오기
        // 담당자: 
        *********************************************************************/
        function request_lang(){
            $sql = "select * from lang";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){
                $this->result = $result;
                $this->result["message"] = "프로젝트 언어 검색 성공";
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "301";
                $this->result["message"] = "프로젝트 언어 검색 실패";
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : request_image_url
        // 설 명 : sumnote에서 등록한 이미지를 서버에 등록하고 등록된 파일이름을 return해주는 함수
        // 만든이: 조경민
        *********************************************************************/
        function request_image_url(){
            $param = $this->json;
            //첨부 파일 처리
            $download_files = $_FILES["file"];
            $file_folder = $this->file_path[$param["path"]];
            $download_result = $this->file_manager->no_image_upload_file($download_files, $file_folder);
            $real_name_arr = $download_result["real_name"];
            $download_file_arr = $download_result["file_name"];
            $download_count = count($download_result["file_name"]);
            $error_download_file_arr = $download_result["error_file_array"];

            //sumnote_img 테이블에 업로드 이미지 데이터 insert
            if(count($download_file_arr) > 0){
                $sql = "insert into sumnote_img(file_name, origin_file_name, path, regdate) values(";
                for($i = 0; $i < count($download_file_arr); $i++){
                    $sql .= $this->null_check($download_file_arr[$i]).",";
                    $sql .= $this->null_check($real_name_arr[$i]).",";
                    $sql .= $this->null_check($file_folder).",";
                    $sql .= "now()),(";
                }

                $sql = substr($sql, 0, -2);

                $result = $this->conn->db_insert($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["value"] = $download_file_arr;
                    $this->result["path"] = $param["path"];
                }else{
                    $this->result = $result;
                }
            }

            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : request_notice_file_upload
        // 설 명 : 공지사항 첨부파일 업로드
        *********************************************************************/
        function request_notice_file_upload(){
            // 디버깅 로그
            error_log("파일 업로드 함수 호출됨");
            error_log("파일 정보: " . print_r($_FILES, true));
            
            // 파일 업로드 체크
            if(empty($_FILES['notice_file']['name'])) {
                $this->result["result"] = "0";
                $this->result["message"] = "업로드할 파일이 없습니다.";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // 업로드 경로 설정
            $relative_path = $this->file_path["notice_file_path"];
            $upload_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $relative_path;
            
            error_log("상대 경로: " . $relative_path);
            error_log("업로드 경로: " . $upload_path);
            
            // 업로드 폴더 생성
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // 단일 파일 처리
            if($_FILES['notice_file']['error'] == 0) {
                $original_name = $_FILES['notice_file']['name'];
                $file_tmp = $_FILES['notice_file']['tmp_name'];
                $file_size = $_FILES['notice_file']['size'];
                
                // 파일 크기 체크 (10MB 제한)
                if($file_size > 10 * 1024 * 1024) {
                    $this->result["result"] = "0";
                    $this->result["message"] = "파일 크기가 10MB를 초과합니다.";
                    echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                    return;
                }
                
                // 파일명 생성 (중복 방지)
                $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                $new_file_name = time() . sprintf('%05d', rand(0, 99999)) . '.' . $file_extension;
                $file_path = $upload_path . $new_file_name;
                
                // 파일 업로드
                if(move_uploaded_file($file_tmp, $file_path)) {
                    $file_info = array(
                        'upload_file' => $new_file_name,
                        'real_file' => $original_name,
                        'file_size' => $file_size
                    );
                    
                    $this->result["result"] = "1";
                    $this->result["message"] = "파일 업로드 성공";
                    $this->result["files"] = array($file_info);
                    $this->result["upload_path"] = $relative_path;
                } else {
                    $this->result["result"] = "0";
                    $this->result["message"] = "파일 저장 실패";
                }
            } else {
                $this->result["result"] = "0";
                $this->result["message"] = "파일 업로드 오류: " . $_FILES['notice_file']['error'];
            }
            
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : request_image_url2
        // 설 명 : inquiry 방식을 참고한 간단한 이미지 업로드
        *********************************************************************/
        function request_image_url2(){
            $param = $this->json;
            
            // 파일 업로드 체크
            if(empty($_FILES['file']['name'][0])) {
                $this->result["result"] = "0";
                $this->result["message"] = "업로드할 파일이 없습니다.";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // 업로드 경로 설정
            $relative_path = $this->file_path[$param["path"]];
            $upload_path = $this->project_name . "/" . $relative_path;
            
            // 업로드 폴더 생성
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            $uploaded_files = array();
            $file_names = array();
            
            for($i = 0; $i < count($_FILES['file']['name']); $i++) {
                if($_FILES['file']['error'][$i] == 0) {
                    $original_name = $_FILES['file']['name'][$i];
                    $file_tmp = $_FILES['file']['tmp_name'][$i];
                    $file_size = $_FILES['file']['size'][$i];
                    $file_type = $_FILES['file']['type'][$i];
                    
                    // 파일 크기 체크 (10MB 제한)
                    if($file_size > 10 * 1024 * 1024) {
                        continue;
                    }
                    
                    // 이미지 파일만 허용
                    $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
                    if(!in_array($file_type, $allowed_types)) {
                        continue;
                    }
                    
                    // 파일명 생성 (중복 방지)
                    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                    $new_filename = time() . '_' . $i . '_' . uniqid() . '.' . $file_extension;
                    $destination = $upload_path . '/' . $new_filename;
                    
                    // 파일 업로드
                    if(move_uploaded_file($file_tmp, $destination)) {
                        $file_names[] = $new_filename;
                        $uploaded_files[] = array(
                            'original_name' => $original_name,
                            'saved_name' => $new_filename,
                            'file_size' => $file_size,
                            'file_type' => $file_type,
                            'file_path' => $destination
                        );
                    }
                }
            }
            
            // 결과 반환
            if(count($file_names) > 0){
                $this->result["result"] = "1";
                $this->result["message"] = "이미지 업로드 성공";
                $this->result["value"] = $file_names;
                $this->result["path"] = $param["path"];
                $this->result["upload_path"] = $upload_path; // 디버깅용
            }else{
                $this->result["result"] = "0";
                $this->result["message"] = "업로드된 파일이 없습니다.";
            }
            
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }


        /********************************************************************* 
        // 함 수 : DB에 저장되어 있는 배송안내 정보를 가져오는 함수
        // 만든이: 조경민
        *********************************************************************/
        function request_delivery_info(){
            $sql = "select * from delivery_info";
            $sql .= " order by lang_idx asc";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){
                $this->result = $result;;
                $this->result["message"] = "게시글 검색 성공";
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "301";
                $this->result["message"] = "게시글 검색 실패";
                echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 배송안내 정보 수정 함수
        // lang_idx : 언어 구분 idx
        // 만든이: 조경민
        *********************************************************************/
        function modify_delivery_info(){
            $param = $this->json;
            if($this->value_check(array("lang_idx"))){
                //언어별 등록 or 모든 언어 등록에 따라 변수 설정해주기
                $lang_obj = array();
                if($param["all_reg"] == 0){ //각 언어별 등록
                    $length = 1;
                    array_push($lang_obj, 1);
                }else{ //모든 언어 함께 등록
                    $lang_obj = json_decode($param["lang_idx"]);
                    $length = count($lang_obj);
                }

                $sql = "select * from delivery_info";
                $result = $this->conn->db_select($sql);

                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)
                $content_array = json_decode($this->sumnote["sumnote"]); //언어별 description [base64, base64]

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
                    $diff_file_path = $this->file_path["delivery_content_path"].$value;
                    array_push($delete_file_arr, $diff_file_path);
                }
                $this->file_manager->delete_file($delete_file_arr);
                $this->result = $result;

                //이미 등록된 게시글이 있으면 해당 게시글 수정
                if(count($result["value"]) > 0){
                    //게시글 insert문
                    //상품안에 들어갈 각 내용 Type의 사용여부도 함께 저장
                    for($i = 0; $i < $length; $i++){
                        $lang_idx = $lang_obj[$i];
                        $sql = "update delivery_info set ";
                        $sql .= "content = ".$this->null_check($content_array[$i]).",";
                        $sql .= "reg_date = now()";
                        $update_result = $this->conn->db_update($sql);
                    }
    
                    if($update_result["result"] == "1"){
                        $this->result = $update_result;
                        $this->result["message"] = "게시글 수정 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "302";
                        $this->result["message"] = "게시글 수정 실패";
                    }   
                }else{
                    //등록된 게시글이 없으면 등록
                    $sql = "insert into delivery_info(content, lang_idx, reg_date) values(";
                    for($i = 0; $i < $length; $i++){
                        $lang_idx = $lang_obj[$i];
                        $sql .= $this->null_check($content_array[$i]).",";
                        $sql .= $lang_idx.",";
                        $sql .= "now()),(";
                    }

                    $sql = substr($sql, 0, -2);
                    $result = $this->conn->db_insert($sql);
                    if($result["result"] == "1"){
                        $this->result = $result;
                        $this->result["message"] = "게시글 등록 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "300";
                        $this->result["message"] = "게시글 등록 실패";
                    }   
                } 
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }


        /********************************************************************* 
        // 함 수 : DB에 저장되어 있는 개인정보처리방침 정보를 가져오는 함수
        // 만든이: 조경민
        *********************************************************************/
        function request_terms_info(){
            $param = $this->json;
            if($this->value_check(array("category_idx"))){
                $sql = "select * from terms";
                $sql .= " where category_idx = ".$param["category_idx"];
                $sql .= " order by lang_idx asc";
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;;
                    $this->result["message"] = "게시글 검색 성공";
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "301";
                    $this->result["message"] = "게시글 검색 실패";
                    echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
                }
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : 개인정보처리방침 및 이용약관 정보 수정 함수
        // category_idx : 1 ---> 개인정보처리방침, 2 ---> 이용약관
        // lang_idx : 언어 구분 idx
        // 만든이: 조경민
        *********************************************************************/
        function modify_terms_info(){
            $param = $this->json;
            if($this->value_check(array("lang_idx", "category_idx"))){
                //언어별 등록 or 모든 언어 등록에 따라 변수 설정해주기
                $lang_obj = array();
                if($param["all_reg"] == 0){ //각 언어별 등록
                    $length = 1;
                    array_push($lang_obj, 1);
                }else{ //모든 언어 함께 등록
                    $lang_obj = json_decode($param["lang_idx"]);
                    $length = count($lang_obj);
                }

                $sql = "select * from terms where category_idx = ".$param["category_idx"];
                $result = $this->conn->db_select($sql);

                $save_files = array(); //저장된 이미지 파일 풀경로를 담는 배열(중간에 에러가 발생하였을경우 전부 삭제해야함)
                $content_array = json_decode($this->sumnote["sumnote"]); //언어별 description [base64, base64]

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
                    $diff_file_path = $this->file_path["terms_content_path"].$value;
                    array_push($delete_file_arr, $diff_file_path);
                }
                $this->file_manager->delete_file($delete_file_arr);
                $this->result = $result;

                //이미 등록된 게시글이 있으면 해당 게시글 수정
                if(count($result["value"]) > 0){
                    //게시글 insert문
                    //상품안에 들어갈 각 내용 Type의 사용여부도 함께 저장
                    for($i = 0; $i < $length; $i++){
                        $lang_idx = $lang_obj[$i];
                        $sql = "update terms set ";
                        $sql .= "category_idx = ".$param["category_idx"].",";
                        $sql .= "content = ".$this->null_check($content_array[$i]).",";
                        $sql .= "reg_date = now()";
                        $update_result = $this->conn->db_update($sql);
                    }
    
                    if($update_result["result"] == "1"){
                        $this->result = $update_result;
                        $this->result["message"] = "게시글 수정 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "302";
                        $this->result["message"] = "게시글 수정 실패";
                    }   
                }else{
                    //등록된 게시글이 없으면 등록
                    $sql = "insert into terms(category_idx, content, lang_idx, reg_date) values(";
                    for($i = 0; $i < $length; $i++){
                        $lang_idx = $lang_obj[$i];
                        $sql .= $param["category_idx"].",";
                        $sql .= $this->null_check($content_array[$i]).",";
                        $sql .= $lang_idx.",";
                        $sql .= "now()),(";
                    }

                    $sql = substr($sql, 0, -2);
                    $result = $this->conn->db_insert($sql);
                    if($result["result"] == "1"){
                        $this->result = $result;
                        $this->result["message"] = "게시글 등록 성공";
                    }else{
                        $this->result["result"] = "0";
                        $this->result["error_code"] = "300";
                        $this->result["message"] = "게시글 등록 실패";
                    }   
                } 
            }
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }
    }
?>