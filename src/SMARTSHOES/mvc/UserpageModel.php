<?php
    class UserpageModel extends gf{
        private $param;
        private $dir;
        private $conn;
        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
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
            $this->session = $array["session"];
            
            $this->BillingSMS = new BillingSMSModel($array);
            $this->MailForm = new MailForm($array);

            $this->email_project = $array["email_project_name"];
            $this->send_email = $array["to_email"];
            $this->email_logo = $array["email_logo"];
            $this->down_path = $array["down_path"];
        }

        /********************************************************************* 

        // 함 수 : lang table에서 이미지 아이콘을 가져오는 함수
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_lang_icon(){
            $sql = "select * from lang";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){
                $this->result = $result;
                $this->result["message"] = "이미지 아이콘 검색 성공";
            }else{
                $this->result["result"] = "0";
                $this->result["error_code"] = "302";
                $this->result["message"] = "이미지 아이콘 검색 실패";
            }
            echo $this->jsonEncode($this->result);
        }

        /********************************************************************* 
        // 함 수 : value_check
        // 설 명 : 필수 값 체크 array("title","author","password")
        // 만든이: 
        *********************************************************************/
        function value_check($check_value_array){

            foreach($check_value_array as $key) {
                // param에 키가 존재하지 않는 경우
                if(!isset($this->param[$key])) {
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "100";
                    $this->result["message"] = $key . " 값이 없습니다.";
                    return false;

                }
                
                // 값이 비어있는 경우 (빈 문자열, null, 공백만 있는 경우)
                if(empty(trim($this->param[$key]))) {
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "101";
                    $this->result["message"] = $key . "가 비어있습니다.";
                return false;
            }
        }


            return true;
        }

        /********************************************************************* 
        // 함 수 : upload_files
        // 설 명 : 파일 업로드 처리
        // 만든이: 
        *********************************************************************/
        function upload_files($upload_path, $file_input_name = 'file'){
            $uploaded_files = array();
            
            if(empty($_FILES[$file_input_name]['name'][0])) {
                return $uploaded_files;
            }
            
            // 업로드 폴더 생성
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            for($i = 0; $i < count($_FILES[$file_input_name]['name']); $i++) {
                if($_FILES[$file_input_name]['error'][$i] == 0) {
                    $original_name = $_FILES[$file_input_name]['name'][$i];
                    $file_tmp = $_FILES[$file_input_name]['tmp_name'][$i];
                    $file_size = $_FILES[$file_input_name]['size'][$i];
                    $file_type = $_FILES[$file_input_name]['type'][$i];
                    
                    // 파일 크기 체크 (10MB 제한)
                    if($file_size > 10 * 1024 * 1024) {
                        continue; // 10MB 초과 파일은 건너뛰기
                    }
                    
                    // 허용된 파일 타입 체크
                    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                    if(!in_array($file_type, $allowed_types)) {
                        continue; // 허용되지 않은 파일 타입은 건너뛰기
                    }
                    
                    // 파일명 생성 (중복 방지를 위해 타임스탬프 추가)
                    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                    $new_filename = time() . '_' . $i . '_' . uniqid() . '.' . $file_extension;
                    $destination = $upload_path . '/' . $new_filename;
                    
                    // 파일 업로드
                    if(move_uploaded_file($file_tmp, $destination)) {
                        $uploaded_files[] = array(
                            'original_name' => $original_name,
                            'saved_name' => $new_filename,
                            'file_size' => $file_size,
                            'file_type' => $file_type,
                            'mime_type' => $file_type,
                            'file_path' => $upload_path . '/' . $new_filename
                        );
                    }
                }
            }
            
            return $uploaded_files;
        }

        /********************************************************************* 
        // 함 수 : save_inquiry_files
        // 설 명 : 문의글 파일 정보를 DB에 저장
        *********************************************************************/
        function save_inquiry_files($inquiry_id, $files, $file_type = 'user') {
            if (empty($files)) {
                return true;
            }
            
            $values = array();
            foreach ($files as $file) {
                $values[] = sprintf(
                    "(%d, '%s', '%s', '%s', %d, '%s', '%s', NOW())",
                    intval($inquiry_id),
                    addslashes($file_type),
                    addslashes($file['original_name']),
                    addslashes($file['saved_name']),
                    intval($file['file_size']),
                    addslashes($file['file_path']),
                    addslashes($file['mime_type'])
                );
            }
            
            if (!empty($values)) {
                $sql = "INSERT INTO inquiry_files (inquiry_id, file_type, original_name, saved_name, file_size, file_path, mime_type, upload_date) VALUES " . implode(', ', $values);
                $result = $this->conn->db_insert($sql);
                return $result['result'] == '1';
            }
            
            return true;
        }

        /********************************************************************* 
        // 함 수 : get_inquiry_files
        // 설 명 : 문의글 파일 목록 조회
        *********************************************************************/
        function get_inquiry_files($inquiry_id, $file_type = null) {
            $where_condition = "inquiry_id = '" . addslashes($inquiry_id) . "'";
            
            if ($file_type) {
                $where_condition .= " AND file_type = '" . addslashes($file_type) . "'";
            }
            
            $sql = "SELECT * FROM inquiry_files WHERE {$where_condition} ORDER BY upload_date ASC";
            $result = $this->conn->db_select($sql);
            
            if ($result['result'] == '1' && !empty($result['value'])) {
                return $result['value'];
            }
            
            return array();
        }

         /********************************************************************* 
        // 함 수 : reg_inquiry
        // 설 명 : 문의글 등록
        *********************************************************************/
        function reg_inquiry(){
            // 필수 값 체크
            if(!$this->value_check(array("title", "author", "password", "category", "email", "content"))){
                return $this->result;
            }
            
            $param = $this->param;
            
            // 파일 업로드 처리
            $uploaded_files = $this->upload_files($this->project_name."/".$this->file_path["inquiry_file"]);
            
            // 현재 시간
            $now = date('Y-m-d H:i:s');
            
            // DB Insert 쿼리 (files 컬럼 제거하고 새로운 파일 테이블 사용)
            $sql = "INSERT INTO inquiry (title, author, password, category, email, content, reg_date, status
                      ) VALUES (
                        '" . addslashes($param['title']) . "',
                        '" . addslashes($param['author']) . "',
                        '" . addslashes($param['password']) . "',
                        '" . addslashes($param['category']) . "',
                        '" . addslashes($param['email']) . "',
                        '" . addslashes($param['content']) . "',
                        '" . $now . "',
                        'pending'
                      )";
            $this->result = $this->conn->db_insert($sql);
            
            // 문의글 등록 성공 시 파일 정보도 새 테이블에 저장
            if($this->result["result"] == "1" && !empty($uploaded_files)) {
                $inquiry_id = $this->result["value"];
                $this->save_inquiry_files($inquiry_id, $uploaded_files, 'user');
            }
            
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : check_inquiry_password
        // 설 명 : 문의글 비밀번호 확인 및 데이터 조회
        *********************************************************************/
        function check_inquiry_password(){
            error_log("check_inquiry_password 함수 시작");
            
            // 필수 값 체크
            if(!$this->value_check(array("inquiry_id", "password"))){
                error_log("check_inquiry_password: 필수값 체크 실패");
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $param = $this->param;
            error_log("check_inquiry_password: 파라미터 - inquiry_id={$param['inquiry_id']}, password 길이=" . strlen($param['password']));
            
            // 문의글 조회 및 비밀번호 확인
            $sql = "SELECT * FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "' AND password = '" . addslashes($param['password']) . "'";
            error_log("check_inquiry_password: SQL - " . $sql);
            
            $result = $this->conn->db_select($sql);
            error_log("check_inquiry_password: DB 결과 - result=" . $result["result"] . ", 데이터 개수=" . (isset($result["value"]) ? count($result["value"]) : 0));
            
            if($result["result"] == "1" && !empty($result["value"])) {
                $inquiry_data = $result["value"][0];
                
                // 세션 시작 (없으면 시작)
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // 세션에 검증 정보 저장 (30분 유효)
                $session_key = 'verified_inquiry_' . $param['inquiry_id'];
                $expires_at = time() + (30 * 60);
                $_SESSION[$session_key] = $expires_at;
                
                // 디버깅 로그
                error_log("세션 저장: 키={$session_key}, 만료시간={$expires_at}, 현재시간=" . time());
                
                // 새로운 파일 테이블에서 파일 정보 조회
                $inquiry_data['files'] = $this->get_inquiry_files($inquiry_data['id'], 'user');
                $inquiry_data['admin_files'] = $this->get_inquiry_files($inquiry_data['id'], 'admin');
                
                $this->result["result"] = "1";
                $this->result["message"] = "비밀번호 확인 성공";
                $this->result["inquiry_data"] = $inquiry_data;
                error_log("check_inquiry_password: 성공 - 세션 저장됨");
            } else {
                $this->result["result"] = "0";
                $this->result["message"] = "비밀번호가 일치하지 않거나 문의글이 존재하지 않습니다.";
                error_log("check_inquiry_password: 실패 - 비밀번호 불일치 또는 문의글 없음");
            }
            
            error_log("check_inquiry_password: 최종 응답 - " . json_encode($this->result, JSON_UNESCAPED_UNICODE));
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : update_inquiry
        // 설 명 : 문의글 수정
        *********************************************************************/
        function update_inquiry(){
            // 필수 값 체크
            if(!$this->value_check(array("inquiry_id", "title", "author", "password", "category", "email", "content"))){
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $param = $this->param;
            
            // 비밀번호 재확인
            $check_sql = "SELECT id FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "' AND password = '" . addslashes($param['password']) . "'";
            $check_result = $this->conn->db_select($check_sql);
            
            if($check_result["result"] != "1" || empty($check_result["value"])) {
                $this->result["result"] = "0";
                $this->result["message"] = "비밀번호가 일치하지 않습니다.";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // 새 파일 업로드 처리
            $uploaded_files = $this->upload_files($this->project_name."/".$this->file_path["inquiry_file"]);
            
            // 기존 파일과 새 파일 합치기 (기존 파일 삭제 로직은 프론트엔드에서 처리)
            $files_json = !empty($uploaded_files) ? json_encode($uploaded_files) : '';
            
            // 현재 시간
            $now = date('Y-m-d H:i:s');
            
            // DB Update 쿼리
            $sql = "UPDATE inquiry SET 
                        title = '" . addslashes($param['title']) . "',
                        author = '" . addslashes($param['author']) . "',
                        password = '" . addslashes($param['password']) . "',
                        category = '" . addslashes($param['category']) . "',
                        email = '" . addslashes($param['email']) . "',
                        content = '" . addslashes($param['content']) . "',
                        " . (!empty($files_json) ? "files = '" . addslashes($files_json) . "'," : "") . "
                        update_date = '" . $now . "'
                    WHERE id = '" . addslashes($param['inquiry_id']) . "'";
            
            $this->result = $this->conn->db_update($sql);
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함 수 : delete_inquiry
        // 설 명 : 문의글 삭제
        *********************************************************************/
        function delete_inquiry(){
            // 필수 값 체크
            if(!$this->value_check(array("inquiry_id", "password"))){
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            $param = $this->param;
            
            // 비밀번호 확인 및 파일 정보 조회
            $check_sql = "SELECT files FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "' AND password = '" . addslashes($param['password']) . "'";
            $check_result = $this->conn->db_select($check_sql);
            
            if($check_result["result"] != "1" || empty($check_result["value"])) {
                $this->result["result"] = "0";
                $this->result["message"] = "비밀번호가 일치하지 않거나 문의글이 존재하지 않습니다.";
                echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // 첨부파일 삭제
            $files_data = $check_result["value"][0]['files'];
            if(!empty($files_data)) {
                $files = json_decode($files_data, true);
                if(is_array($files)) {
                    $upload_path = $this->project_name."/".$this->file_path["inquiry_file"];
                    foreach($files as $file) {
                        $file_path = $upload_path . '/' . $file['saved_name'];
                        if(file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }
            
            // DB에서 문의글 삭제
            $sql = "DELETE FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "'";
            
            $this->result = $this->conn->db_delete($sql);
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }



    /********************************************************************* 
    // 함 수 : get_inquiry_list (관리자용 호출)
    // 설 명 : 문의글 리스트 조회 (검색, 페이징 포함)
    *********************************************************************/
    function get_inquiry_list(){
        $param = $this->param;
        
        // 기본값 설정
        $page = isset($param['page']) ? intval($param['page']) : 1;
        $limit = isset($param['limit']) ? intval($param['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        // 검색 조건 구성
        $where_conditions = array();
        
        if(!empty($param['search_type']) && !empty($param['search_keyword'])) {
            $search_keyword = addslashes($param['search_keyword']);
            switch($param['search_type']) {
                case 'title':
                    $where_conditions[] = "title LIKE '%{$search_keyword}%'";
                    break;
                case 'author':
                    $where_conditions[] = "author LIKE '%{$search_keyword}%'";
                    break;
                case 'email':
                    $where_conditions[] = "email LIKE '%{$search_keyword}%'";
                    break;
                case 'content':
                    $where_conditions[] = "content LIKE '%{$search_keyword}%'";
                    break;
                case 'all':
                    $where_conditions[] = "(title LIKE '%{$search_keyword}%' OR author LIKE '%{$search_keyword}%' OR content LIKE '%{$search_keyword}%' OR email LIKE '%{$search_keyword}%')";
                    break;
            }
        }
        
        if(!empty($param['category'])) {
            $where_conditions[] = "category = '" . addslashes($param['category']) . "'";
        }
        
        if(!empty($param['status'])) {
            $where_conditions[] = "status = '" . addslashes($param['status']) . "'";
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        // 정렬 조건
        $order_by = "ORDER BY reg_date DESC";
        if(!empty($param['sort'])) {
            switch($param['sort']) {
                case 'title_asc':
                    $order_by = "ORDER BY title ASC";
                    break;
                case 'title_desc':
                    $order_by = "ORDER BY title DESC";
                    break;
                case 'reg_date_asc':
                    $order_by = "ORDER BY reg_date ASC";
                    break;
                case 'status_desc':
                    $order_by = "ORDER BY status DESC";
                    break;
                default:
                    $order_by = "ORDER BY reg_date DESC";
            }
        }
        
        // 전체 개수 조회
        $count_sql = "SELECT COUNT(*) as total FROM inquiry {$where_clause}";
        $count_result = $this->conn->db_select($count_sql);
        $total_count = ($count_result["result"] == "1" && !empty($count_result["value"])) ? $count_result["value"][0]['total'] : 0;
        
        // 상태별 카운트 조회 (필터 조건 적용)
        $status_count_sql = "SELECT status, COUNT(*) as count FROM inquiry {$where_clause} GROUP BY status";
        $status_result = $this->conn->db_select($status_count_sql);
        $status_counts = array();
        
        if($status_result["result"] == "1" && !empty($status_result["value"])) {
            foreach($status_result["value"] as $status_row) {
                $status_counts[$status_row['status']] = $status_row['count'];
            }
        }
        
        // 리스트 조회 (비밀번호 제외) - 파일 유무는 새로운 테이블에서 확인
        $list_sql = "SELECT i.id, i.title, i.author, i.category, i.email, i.reg_date, i.update_date, i.status,
                           (CASE WHEN uf.inquiry_id IS NOT NULL THEN 1 ELSE 0 END) as has_files,
                           (CASE WHEN i.admin_reply IS NOT NULL AND i.admin_reply != '' THEN 1 ELSE 0 END) as has_reply
                    FROM inquiry i
                    LEFT JOIN (
                        SELECT DISTINCT inquiry_id 
                        FROM inquiry_files 
                        WHERE file_type = 'user'
                    ) uf ON i.id = uf.inquiry_id
                    {$where_clause} 
                    {$order_by} 
                    LIMIT {$limit} OFFSET {$offset}";
        
        $list_result = $this->conn->db_select($list_sql);
        
        if($list_result["result"] == "1") {
            $this->result["result"] = "1";
            $this->result["message"] = "문의글 리스트 조회 성공";
            $this->result["list"] = $list_result["value"] ?: array();
            $this->result["total_count"] = $total_count;
            $this->result["current_page"] = $page;
            $this->result["limit"] = $limit;
            $this->result["total_pages"] = ceil($total_count / $limit);
            $this->result["status_counts"] = $status_counts;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 리스트 조회 실패";
            $this->result["list"] = array();
            $this->result["total_count"] = 0;
            $this->result["status_counts"] = array();
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : admin_get_inquiry_detail
    // 설 명 : 관리자용 문의글 상세 조회 (비밀번호 검증 없이)
    *********************************************************************/
    function admin_get_inquiry_detail(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        
        // 관리자는 비밀번호 검증 없이 모든 정보 조회
        $sql = "SELECT * FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "'";
        
        $result = $this->conn->db_select($sql);
        
        if($result["result"] == "1" && !empty($result["value"])) {
            $inquiry_data = $result["value"][0];
            
            // 새로운 파일 테이블에서 파일 정보 조회
            $inquiry_data['files'] = $this->get_inquiry_files($inquiry_data['id'], 'user');
            $inquiry_data['admin_files'] = $this->get_inquiry_files($inquiry_data['id'], 'admin');
            
            $this->result["result"] = "1";
            $this->result["message"] = "문의글 상세 조회 성공";
            $this->result["inquiry"] = $inquiry_data;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "문의글을 찾을 수 없습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : save_reply
    // 설 명 : 관리자 답변 저장
    *********************************************************************/
    function save_reply(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if(!isset($this->param['reply_content']) || empty(trim($this->param['reply_content']))){
            $this->result["result"] = "0";
            $this->result["message"] = "답변 내용이 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        $now = date('Y-m-d H:i:s');
        
        // 답변 저장 및 상태 업데이트
        $sql = "UPDATE inquiry SET 
                admin_reply = '" . addslashes($param['reply_content']) . "',
                reply_date = '" . $now . "',
                status = '" . addslashes($param['status']) . "',
                update_date = '" . $now . "'
                WHERE id = '" . addslashes($param['inquiry_id']) . "'";
        
        $this->result = $this->conn->db_update($sql);
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : update_status
    // 설 명 : 문의글 상태만 변경
    *********************************************************************/
    function update_status(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if(!isset($this->param['status']) || empty($this->param['status'])){
            $this->result["result"] = "0";
            $this->result["message"] = "상태 정보가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        $now = date('Y-m-d H:i:s');
        
        // 상태 업데이트
        $sql = "UPDATE inquiry SET 
                status = '" . addslashes($param['status']) . "',
                update_date = '" . $now . "'
                WHERE id = '" . addslashes($param['inquiry_id']) . "'";
        
        $this->result = $this->conn->db_update($sql);
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : admin_delete_inquiry
    // 설 명 : 관리자 권한으로 문의글 삭제 (첨부파일 포함)
    *********************************************************************/
    function admin_delete_inquiry(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        
        // 새로운 파일 테이블에서 첨부파일 삭제
        $inquiry_files = $this->get_inquiry_files($param['inquiry_id']);
        foreach($inquiry_files as $file) {
            $file_path = $this->project_name."/".$this->file_path["inquiry_file"]."/".$file['saved_name'];
            if(file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // 파일 테이블에서 레코드 삭제
        $delete_files_sql = "DELETE FROM inquiry_files WHERE inquiry_id = '" . addslashes($param['inquiry_id']) . "'";
        $this->conn->db_delete($delete_files_sql);
        
        // DB에서 문의글 삭제
        $sql = "DELETE FROM inquiry WHERE id = '" . addslashes($param['inquiry_id']) . "'";
        
        $this->result = $this->conn->db_delete($sql);
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : download_inquiry_file
    // 설 명 : 문의글 첨부파일 다운로드
    *********************************************************************/
    function download_inquiry_file(){
        if(!isset($this->param['file_name']) || empty($this->param['file_name'])){
            echo "<script>alert('파일명이 없습니다.'); history.back();</script>";
            return;
        }
        
        $file_name = $this->param['file_name'];
        $original_name = isset($this->param['original_name']) ? $this->param['original_name'] : $file_name;
        $file_path = $this->project_name."/".$this->file_path["inquiry_file"]."/".$file_name;
        
        if(!file_exists($file_path)){
            echo "<script>alert('파일이 존재하지 않습니다.'); history.back();</script>";
            return;
        }
        
        // 파일 다운로드 헤더 설정
        $file_size = filesize($file_path);
        $mime_type = mime_content_type($file_path);
        
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . urlencode($original_name) . '"');
        header('Content-Length: ' . $file_size);
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        // 파일 내용 출력
        readfile($file_path);
        exit;
    }

    /********************************************************************* 
    // 함 수 : save_reply_with_files
    // 설 명 : 관리자 답변 저장 (파일 첨부 포함)
    *********************************************************************/
    function save_reply_with_files(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        if(!isset($this->param['reply_content']) || empty(trim($this->param['reply_content']))){
            $this->result["result"] = "0";
            $this->result["message"] = "답변 내용이 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        $now = date('Y-m-d H:i:s');
        
        // 관리자 답변 파일 업로드 처리
        $admin_files = array();
        if(isset($_FILES['reply_files']) && !empty($_FILES['reply_files']['name'][0])) {
            $admin_files = $this->upload_files($this->project_name."/".$this->file_path["inquiry_file"], 'reply_files');
        }
        
        // 답변 저장 및 상태 업데이트 (admin_files 컬럼 제거)
        $sql = "UPDATE inquiry SET 
                admin_reply = '" . addslashes($param['reply_content']) . "',
                reply_date = '" . $now . "',
                status = '" . addslashes($param['status']) . "',
                update_date = '" . $now . "'
                WHERE id = '" . addslashes($param['inquiry_id']) . "'";
        
        $this->result = $this->conn->db_update($sql);
        
        // 답변 저장 성공 시 관리자 파일도 새 테이블에 저장
        if($this->result["result"] == "1" && !empty($admin_files)) {
            $this->save_inquiry_files($param['inquiry_id'], $admin_files, 'admin');
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : verify_inquiry_access
    // 설 명 : 문의글 접근 권한 확인 (세션 기반)
    *********************************************************************/
    function verify_inquiry_access($inquiry_id) {
        // 세션 시작 (없으면 시작)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $session_key = 'verified_inquiry_' . $inquiry_id;
        
        // 세션에 검증 정보가 있는지 확인
        if (isset($_SESSION[$session_key])) {
            $expires_at = $_SESSION[$session_key];
            $current_time = time();
            
            // 디버깅 로그
            error_log("세션 검증: 키={$session_key}, 만료시간={$expires_at}, 현재시간={$current_time}");
            
            // 만료 시간 확인
            if ($current_time <= $expires_at) {
                error_log("세션 유효: 접근 허용");
                return true; // 접근 허용
            } else {
                // 만료된 세션 삭제
                error_log("세션 만료: 세션 삭제");
                unset($_SESSION[$session_key]);
                return false;
            }
        } else {
            error_log("세션 없음: 키={$session_key}");
        }
        
        return false; // 접근 거부
    }

    /********************************************************************* 
    // 함 수 : get_inquiry_for_view
    // 설 명 : view 페이지용 문의글 조회 (세션 검증 포함)
    *********************************************************************/
    function get_inquiry_for_view(){
        if(!isset($this->param['inquiry_id']) || empty($this->param['inquiry_id'])){
            $this->result["result"] = "0";
            $this->result["message"] = "문의글 ID가 없습니다.";
            $this->result["need_password"] = true;
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $inquiry_id = $this->param['inquiry_id'];
        
        // 세션 검증
        error_log("get_inquiry_for_view: inquiry_id={$inquiry_id} 세션 검증 시작");
        if (!$this->verify_inquiry_access($inquiry_id)) {
            error_log("get_inquiry_for_view: 세션 검증 실패");
            $this->result["result"] = "0";
            $this->result["message"] = "접근 권한이 없습니다. 비밀번호를 입력해주세요.";
            $this->result["need_password"] = true;
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        error_log("get_inquiry_for_view: 세션 검증 성공");
        
        // 문의글 조회
        $sql = "SELECT * FROM inquiry WHERE id = '" . addslashes($inquiry_id) . "'";
        $result = $this->conn->db_select($sql);
        
        if($result["result"] == "1" && !empty($result["value"])) {
            $inquiry_data = $result["value"][0];
            
            // 새로운 파일 테이블에서 파일 정보 조회
            $inquiry_data['files'] = $this->get_inquiry_files($inquiry_data['id'], 'user');
            $inquiry_data['admin_files'] = $this->get_inquiry_files($inquiry_data['id'], 'admin');
            
            $this->result["result"] = "1";
            $this->result["message"] = "문의글 조회 성공";
            $this->result["inquiry"] = $inquiry_data;
            $this->result["need_password"] = false;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "문의글을 찾을 수 없습니다.";
            $this->result["need_password"] = true;
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

    /********************************************************************* 
    // 함 수 : get_notice_list (사용자용)
    // 설 명 : 공지사항 리스트 조회 (검색, 페이징, 카테고리 필터 포함)
    *********************************************************************/
    function get_notice_list(){
        $param = $this->param;
        
        // 기본값 설정
        $page = isset($param['page']) ? intval($param['page']) : 1;
        $limit = isset($param['limit']) ? intval($param['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        // 검색 조건 구성
        $where_conditions = array();
        
        // 검색어 조건 (notice_name 테이블에서 검색)
        if(!empty($param['search_keyword'])) {
            $search_keyword = addslashes($param['search_keyword']);
            $where_conditions[] = "(nn.title LIKE '%{$search_keyword}%' OR nn.content LIKE '%{$search_keyword}%')";
        }
        
        // 카테고리 필터
        if(!empty($param['category'])) {
            $where_conditions[] = "n.category = '" . addslashes($param['category']) . "'";
        }
        
        $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
        
        // 정렬 조건 (최신순)
        $order_by = "ORDER BY n.regdate DESC";
        if(!empty($param['sort'])) {
            switch($param['sort']) {
                case 'title_asc':
                    $order_by = "ORDER BY nn.title ASC";
                    break;
                case 'title_desc':
                    $order_by = "ORDER BY nn.title DESC";
                    break;
                case 'reg_date_asc':
                    $order_by = "ORDER BY n.regdate ASC";
                    break;
                default:
                    $order_by = "ORDER BY n.regdate DESC";
            }
        }
        
        // 전체 개수 조회
        $count_sql = "SELECT COUNT(*) as total 
                      FROM notice n 
                      LEFT JOIN notice_name nn ON n.idx = nn.notice_idx 
                      {$where_clause}";
        $count_result = $this->conn->db_select($count_sql);
        $total_count = ($count_result["result"] == "1" && !empty($count_result["value"])) ? $count_result["value"][0]['total'] : 0;
        
        // 리스트 조회 (notice와 notice_name 조인) - 제목, 내용, 날짜, 중요도
        $list_sql = "SELECT n.idx, nn.title, nn.content, n.category, nn.kind, n.regdate, n.views,
                           CASE 
                               WHEN n.category = 'important' THEN '중요'
                               WHEN n.category = 'service' THEN '서비스 안내'
                               WHEN n.category = 'update' THEN '업데이트'
                               WHEN n.category = 'event' THEN '이벤트'
                               ELSE '일반'
                           END as category_text,
                           DATE_FORMAT(n.regdate, '%Y.%m.%d') as formatted_date
                    FROM notice n 
                    LEFT JOIN notice_name nn ON n.idx = nn.notice_idx 
                    {$where_clause} 
                    {$order_by} 
                    LIMIT {$limit} OFFSET {$offset}";
        
        $list_result = $this->conn->db_select($list_sql);
        
        if($list_result["result"] == "1") {
            $this->result["result"] = "1";
            $this->result["message"] = "공지사항 리스트 조회 성공";
            $this->result["list"] = $list_result["value"] ?: array();
            $this->result["total_count"] = $total_count;
            $this->result["current_page"] = $page;
            $this->result["limit"] = $limit;
            $this->result["total_pages"] = ceil($total_count / $limit);
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "공지사항 리스트 조회 실패";
            $this->result["list"] = array();
            $this->result["total_count"] = 0;
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : get_notice_detail (사용자용)
    // 설 명 : 공지사항 상세 조회
    *********************************************************************/
    function get_notice_detail(){
        if(!isset($this->param['idx']) || empty($this->param['idx'])){
            $this->result["result"] = "0";
            $this->result["message"] = "공지사항 ID가 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $param = $this->param;
        
        // 공지사항 상세 조회 (notice와 notice_name 조인)
        $sql = "SELECT n.idx, nn.title, nn.content, n.category, nn.kind, n.regdate, n.views, n.is_important,
                       CASE 
                           WHEN n.category = 'important' THEN '중요'
                           WHEN n.category = 'service' THEN '서비스 안내'
                           WHEN n.category = 'update' THEN '업데이트'
                           WHEN n.category = 'event' THEN '이벤트'
                           ELSE '일반'
                       END as category_text,
                       DATE_FORMAT(n.regdate, '%Y.%m.%d') as formatted_date
                FROM notice n 
                LEFT JOIN notice_name nn ON n.idx = nn.notice_idx 
                WHERE n.idx = '" . addslashes($param['idx']) . "'";
        
        $result = $this->conn->db_select($sql);
        
        if($result["result"] == "1" && !empty($result["value"])) {
            $notice_data = $result["value"][0];
            
            // 조회수 증가
            $update_views_sql = "UPDATE notice SET views = COALESCE(views, 0) + 1 WHERE idx = '" . addslashes($param['idx']) . "'";
            $this->conn->db_update($update_views_sql);
            
            // 첨부파일 조회 (notice_file 테이블에서)
            $file_sql = "SELECT upload_file, real_file FROM notice_file WHERE notice_idx = '" . addslashes($param['idx']) . "'";
            $file_result = $this->conn->db_select($file_sql);
            
            $files = array();
            if($file_result["result"] == "1" && !empty($file_result["value"])) {
                foreach($file_result["value"] as $file) {
                    // 실제 파일 경로로 파일 크기 확인
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->file_link["notice_file_path"] . $file['upload_file'];
                    $file_size = file_exists($file_path) ? filesize($file_path) : 0;
                    
                    $files[] = array(
                        'original_name' => $file['upload_file'],  // 서버 저장된 파일명
                        'saved_name' => $file['real_file'],       // 사용자가 보는 파일명
                        'file_size' => $file_size,
                        'download_url' => "?param=download_notice_file&file_name=" . urlencode($file['upload_file']) . "&original_name=" . urlencode($file['real_file'])
                    );
                }
            }
            $notice_data['files'] = $files;
            
            $this->result["result"] = "1";
            $this->result["message"] = "공지사항 상세 조회 성공";
            $this->result["notice"] = $notice_data;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "공지사항을 찾을 수 없습니다.";
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : download_notice_file (사용자용)
    // 설 명 : 공지사항 첨부파일 다운로드
    *********************************************************************/
    function download_notice_file(){
        if(!isset($this->param['file_name']) || empty($this->param['file_name'])){
            $this->result["result"] = "0";
            $this->result["message"] = "파일명이 없습니다.";
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        $file_name = $this->param['file_name'];
        $original_name = isset($this->param['original_name']) ? $this->param['original_name'] : $file_name;
        
        // 공지사항 첨부파일 경로 (file_link에는 이미 프로젝트명이 포함됨)
        $file_path = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->file_link["notice_file_path"] . $file_name;
        
        // 디렉토리가 없으면 생성
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/" . $this->file_link["notice_file_path"];
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        

        
        if(!file_exists($file_path)){
            $this->result["result"] = "0";
            $this->result["message"] = "파일이 존재하지 않습니다. 경로: " . $file_path;
            $this->result["debug_info"] = array(
                "file_name" => $file_name,
                "file_path" => $file_path,
                "directory_exists" => is_dir(dirname($file_path)),
                "notice_file_path" => $this->file_link["notice_file_path"]
            );
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
            return;
        }
        
        // 파일 내용을 Base64로 인코딩
        $file_content = file_get_contents($file_path);
        $file_base64 = base64_encode($file_content);
        $file_size = filesize($file_path);
        
        // MIME 타입 확인 (여러 방법으로 시도)
        $mime_type = 'application/octet-stream'; // 기본값
        
        if (function_exists('mime_content_type')) {
            $mime_type = mime_content_type($file_path);
        } elseif (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file_path);
            finfo_close($finfo);
        } else {
            // 파일 확장자로 MIME 타입 추정
            $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
            $mime_types = array(
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'txt' => 'text/plain',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed'
            );
            if (isset($mime_types[$extension])) {
                $mime_type = $mime_types[$extension];
            }
        }
        
        $this->result["result"] = "1";
        $this->result["message"] = "파일 다운로드 준비 완료";
        $this->result["file_data"] = $file_base64;
        $this->result["file_name"] = $original_name;
        $this->result["file_size"] = $file_size;
        $this->result["mime_type"] = $mime_type;
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    // 홍보/행사 목록 조회 (사용자 페이지용)
    function get_promotion_list(){
        try {
            $sql = "SELECT p.idx, p.event_name, p.event_period, p.event_location, 
                           p.award_badge, p.main_image, p.content, p.sort_order,
                           DATE_FORMAT(p.regdate, '%Y.%m.%d') as formatted_regdate
                    FROM promotion p 
                    WHERE p.is_active = 1 
                    ORDER BY p.sort_order ASC, p.idx DESC";
            
            $result = $this->conn->db_select($sql);
            
            if($result['result'] == 1){
                $promotions = $result['value'];
                
                // 각 프로모션의 서브 이미지 조회
                foreach($promotions as &$promotion) {
                    $sub_images_sql = "SELECT image_file FROM promotion_images 
                                      WHERE promotion_idx = ".$promotion['idx']." 
                                      ORDER BY sort_order ASC";
                    $sub_result = $this->conn->db_select($sub_images_sql);
                    
                    $promotion['sub_images'] = [];
                    if($sub_result['result'] == 1 && !empty($sub_result['value'])) {
                        $promotion['sub_images'] = $sub_result['value'];
                    }
                    
                    // 파일 경로 추가
                    if(!empty($promotion['main_image'])) {
                        $promotion['main_image_path'] = $this->file_path["promotion_main_image_path"] . $promotion['main_image'];
                    }
                    
                    // 서브 이미지 경로 추가
                    foreach($promotion['sub_images'] as &$sub_image) {
                        $sub_image['image_path'] = $this->file_path["promotion_sub_image_path"] . $sub_image['image_file'];
                    }
                }
                
                $this->result["result"] = "1";
                $this->result["message"] = "조회 성공";
                $this->result["value"] = $promotions;
            } else {
                $this->result["result"] = "0";
                $this->result["message"] = "데이터가 없습니다.";
                $this->result["value"] = [];
            }
            
        } catch (Exception $e) {
            error_log("get_promotion_list 에러: " . $e->getMessage());
            $this->result["result"] = "0";
            $this->result["message"] = "조회 중 오류가 발생했습니다.";
            $this->result["value"] = [];
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : get_first_promotion
    // 설 명 : 첫 번째 순서의 활성화된 promotion 데이터 조회 (메인 페이지용)
    // 만든이: 
    *********************************************************************/
    function get_first_promotion(){
        // 첫 번째 순서의 활성화된 promotion 조회
        $sql = "SELECT * FROM promotion WHERE is_active = 1 ORDER BY sort_order ASC, idx DESC LIMIT 1";
        $result = $this->conn->db_select($sql);
        
        if($result["result"]=="1") {
            $promotion = $result["value"][0];
            // 메인 이미지 경로 추가
            if($promotion['main_image']) {
                $promotion['main_image_path'] = $this->file_link["promotion_main_image_path"] . $promotion['main_image'];
            } else {
                $promotion['main_image_path'] = "";
            }
            
            $this->result["result"] = "1";
            $this->result["message"] = "조회되었습니다.";
            $this->result["value"] = $promotion;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "등록된 프로모션이 없습니다.";
            $this->result["value"] = null;
        }
        
        echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
    }

    /********************************************************************* 
    // 함 수 : 팝업 리스트 불러오기
    // 만든이: 최재혁
    *********************************************************************/
    function init_popup() {
        $param = $this->param;
        $sql = "select * from popup where is_use =1 order by sequence ";
        $this->result = $this->conn->db_select($sql);

        echo $this->jsonEncode($this->result);
    }

    /********************************************************************* 
    // 함수: 신청서 제출
    // 설명: 사용자 신청서 데이터를 저장
    *********************************************************************/
    function submit_application(){
        $param = $this->param;
        
        // 필수 필드 검증
        $required_fields = [
            'applicant_type', 'name', 'birthdate', 'phone', 
            'email', 'address', 'reason'
        ];
        
        foreach($required_fields as $field) {
            if(empty($param[$field])) {
                $this->result["result"] = "0";
                $this->result["message"] = "필수 입력사항이 누락되었습니다: " . $field;
                echo $this->jsonEncode($this->result);
                return;
            }
        }
        
        // 필수 동의사항 검증
        if(empty($param['terms_agreement']) || empty($param['privacy_agreement'])) {
            $this->result["result"] = "0";
            $this->result["message"] = "필수 동의사항에 체크해주세요.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        // 데이터 정리
        $applicant_type = $this->null_check($param['applicant_type']);
        $name = $this->null_check($param['name']);
        $birthdate = $this->null_check($param['birthdate']);
        $phone = $this->null_check($param['phone']);
        $email = $this->null_check($param['email']);
        $address = $this->null_check($param['address']);
        $reason = $this->null_check($param['reason']);
        $traffic_sources = $this->null_check($param['traffic_sources'] ?? '[]');
        $additional_requests = $this->null_check($param['additional_requests'] ?? '');
        $terms_agreement = intval($param['terms_agreement']);
        $privacy_agreement = intval($param['privacy_agreement']);
        $marketing_agreement = intval($param['marketing_agreement'] ?? 0);
        
        // 데이터 삽입
        $sql = "INSERT INTO application (
                    applicant_type, name, birthdate, phone, email, address, 
                    reason, traffic_sources, additional_requests,
                    terms_agreement, privacy_agreement, marketing_agreement,

                    status, regdate
                ) VALUES (
                    {$applicant_type}, {$name}, {$birthdate}, {$phone}, 
                    {$email}, {$address}, {$reason}, {$traffic_sources}, 
                    {$additional_requests}, {$terms_agreement}, {$privacy_agreement}, {$marketing_agreement},

                    'pending', NOW()
                )";
        
        $result = $this->conn->db_insert($sql);
        
        if($result["result"] == "1") {
            $this->result = $result;
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "신청서 저장 중 오류가 발생했습니다.";
        }
        
        echo $this->jsonEncode($this->result);
    }

    /********************************************************************* 
    // 함수: 약관 조회
    // 설명: 이용약관, 개인정보처리방침, 마케팅정보 수신동의 조회
    *********************************************************************/
    function get_terms(){
        $param = $this->param;
        $terms_idx = intval($param['terms_idx'] ?? 0);
        
        if(empty($terms_idx)) {
            $this->result["result"] = "0";
            $this->result["message"] = "약관 ID가 필요합니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        // 1: 개인정보방침, 2: 이용약관, 3: 이메일무단수집거부, 4: 마케팅정보 수신동의
        $valid_terms = array(1, 2, 3, 4);
        if(!in_array($terms_idx, $valid_terms)) {
            $this->result["result"] = "0";
            $this->result["message"] = "유효하지 않은 약관입니다.";
            echo $this->jsonEncode($this->result);
            return;
        }
        
        $sql = "SELECT * FROM terms_name WHERE terms_idx = {$terms_idx}";
        $result = $this->conn->db_select($sql);
        
        if($result["result"] == "1" && !empty($result["value"])) {
            $this->result["result"] = "1";
            $this->result["message"] = "약관 조회 성공";
            $this->result["value"] = $result["value"][0];
        } else {
            $this->result["result"] = "0";
            $this->result["message"] = "약관을 찾을 수 없습니다.";
        }
        
        echo $this->jsonEncode($this->result);
    }

}

?>