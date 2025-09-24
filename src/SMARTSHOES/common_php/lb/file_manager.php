<?php
class file_manager extends gf{
    private $mode; //파일매니저 저장모드(s3,server)

    private $server_path; //server모드일경우의 기본경로
    private $s3_path; //s3모드일경우의 기본경로

    function __construct($dir,$mode,$project_name){ //생성자(기본 프로젝트 디렉토리 경로)
        $this->server_path = $dir;
        $this->mode = $mode;
        if($project_name == "pome_main"){
            $this->server_path = "s3 사용안함";
        }else{
            $this->server_path = $project_name ."/";
        }
    }




    //s3 또는 파일 관리 매니저

    // move_uploaded_file($file["tmp_name"][$f], $file_path.$file_folder.$file_name); //생성
    // unlink($file_path.$file_folder.$file); //삭제

    // $up_file_folder = "/_UploadData/";
    // if(!is_dir($file_path.$up_file_folder)){
    //     mkdir($file_path.$up_file_folder);
    // }
    // if(!is_dir($file_path.$file_folder)){
    //     mkdir($file_path.$file_folder);
    // }
    // if(!is_dir($file_path.$stor_folder)){
    //     mkdir($file_path.$stor_folder);
    // }



    /********************************************************************* 
    // 함 수 : 폴더 경로 확인(저장소가 서버일경우)
    // 설 명 : 파일명은 넣지말것
    // 만든이: 안정환
    *********************************************************************/
    function check_folder($dir){
        $array = explode('/', $dir);
        $dir = "";
        // print_r($array);
        for($i=0; $i<count($array); $i++){
            if($array[$i] != ""){
                if($i == 0){
                    $dir = $array[$i];
                }else{
                    $dir = $dir."/".$array[$i];
                }
                if(!is_dir($dir)){
                    mkdir($dir);
                }
            }
        }
    }

    function rand_name(){
        $someTime=((strtotime(date("Y-m-d H:i:s",time()))-(9*60*60))-(strtotime("1971-1-1 00:00:00 GMT"))).mt_rand(1, 100000);
        return $someTime;
    }

    function server_file_exist($path){
        return file_exists($path);
    }


    /********************************************************************* 
    // 함 수 : 파일업로드
    // 설 명 : $files : $_FILES['files'], $path : "_uploads/poroduct_img/", $origin_path : "_uploads/poroduct_img_orign/"
    // 만든이: 안정환
    *********************************************************************/
    function upload_file($files, $path, $origin_path){
        if($this->mode == "server"){ //서버모드이면 서버에 저장
            $result = $this->file_upload_server(array(
                "zip_dir" => $this->server_path.$path, //압축된 파일저장 경로
                "dir" => $this->server_path.$origin_path, //원본 파일저장 경로
                "files" => $files,
            ));

            if($result["error_code"]){ //에러면 에러 처리
                
            }else{
                //업로드 성공
                return $result;
            }
        }else if($this->mode == "s3"){ //s3모드이면 s3에 저장
            
            $result = $this->file_upload_s3(array(
                "zip_dir" => $this->s3_path.$path, //압축된 파일저장 경로
                "dir" => $this->s3_path.$origin_path, //원본 파일저장 경로
                "files" => $files
            ));

            

            if($result["error_code"]){//에러
                //이미 저장된 이미지가 있다면 삭제
                // $result["s3"]->multiDel($this->s3_path.$path, $result["file_name"]);
                // $result["s3"]->multiDel($this->s3_path.$origin_path, $result["file_name"]);

                return false;
            }else{//파일 저장 성공
                return $result; 
            }
        }else if($this->mode == "sms"){
            // 최진혁 sms 일떄 모드 추가
            $result = $this->sms_upload_s3(array(
                "zip_dir" => $this->s3_path.$path, //압축된 파일저장 경로
                "dir" => $this->s3_path.$origin_path, //원본 파일저장 경로
                "files" => $files
            ));
            if($result["error_code"]){//에러
                //이미 저장된 이미지가 있다면 삭제
                // $result["s3"]->multiDel($this->s3_path.$path, $result["file_name"]);
                // $result["s3"]->multiDel($this->s3_path.$origin_path, $result["file_name"]);

                return false;
            }else{//파일 저장 성공
                return $result; 
            }
        }
    }

    /********************************************************************* 
    // 함 수 : 파일삭제
    // 설 명 : $files : $_FILES['files'], $path : "_uploads/poroduct_img/", $origin_path : "_uploads/poroduct_img_orign/"
    // 만든이: 안정환
    *********************************************************************/
    function delete_file($files_name_arr){
        if($this->mode == "server"){ //서버모드이면 서버에 저장
            $file_count = count($files_name_arr);
            if($file_count != 0){
                foreach($files_name_arr as $key => $name){
                    $full_path = $this->server_path.$name;
                    if(file_exists($full_path)){
                        unlink($full_path);
                    }
                }
            }
        }else if($this->mode == "s3"){ //s3모드이면 파일 삭제
            if(count($files_name_arr) > 0){
                $delete_files = [];
                for($i=0; $i<count($files_name_arr); $i++){
                    $name_array = explode("/", $files_name_arr[$i]);
                    if($name_array[0] != "files"){
                        array_push($delete_files, $this->s3_path.$files_name_arr[$i]);
                        // $files_name_arr[$i] = $this->s3_path.$files_name_arr[$i];
                    }else{
                        array_push($delete_files,$files_name_arr[$i]);
                    }
                }
                $s3manage = new s3Manager();
                $s3manage->multiDel_shoppingmall($delete_files); //파일 삭제
            }
        }
    }

    //서버용
    function file_upload_server($json){
        $error_code = "";
        $file_name_array = array();
        $error_file_array = array();
        
        $this->check_folder($json["dir"]); //폴더 경로 점검
        $this->check_folder($json["zip_dir"]); //폴더 경로 점검

        foreach ($json["files"]['name'] as $f => $name) {
            if($json['files']['error'][$f] == 4) {//파일이 없을 경우(form에는 file 태그가 4개가 있는데 파일을 2개만 첨부했을경우)
                
            }else{
                //파일 이름을 랜덤하게 생성 한다.
                $info = new SplFileInfo($name);
                $file_name = $this->rand_name().".".$info->getExtension();

                //파일 이름 중복확인
                $exist_check = $this->server_file_exist($json["dir"].$file_name);
                while($exist_check == 1){
                    $file_name = $this->rand_name().".".$info->getExtension();
                    $exist_check = $this->server_file_exist($json["dir"].$file_name);
                }

                $origin_result = move_uploaded_file($json['files']["tmp_name"][$f], $json["dir"].$file_name); //생성
                if($origin_result){ //오리지날 이미지 업로드가 성공이라면 용량줄이기 실행
                    //이미지 용량 줄인후 줄인이미지 넣기
                    if(isset($json["zip_dir"])){
                        $file_size = filesize($json["dir"].$file_name);
                        $percent = "0.2";
                        if($file_size < 2097152){
                            $percent = "0.9";
                        }elseif(2097152 <= $file_size &&  $file_size < 4194304){
                            $percent = "0.85";
                        }elseif( 4194304 <= $file_size &&  $file_size < 6291456){
                            $percent = "0.8";
                        }elseif( 6291456 <= $file_size &&  $file_size < 8388608){
                            $percent = "0.75";
                        }elseif( 8388608 <= $file_size &&  $file_size < 10485760){
                            $percent = "0.7";
                        }elseif( 10485760 <= $file_size &&  $file_size < 12582912){
                            $percent = "0.65";
                        }elseif( 12582912 <= $file_size &&  $file_size < 14680064){
                            $percent = "0.6";
                        }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
                            $percent = "0.55";
                        }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
                            $percent = "0.55";
                        }elseif( 16777216 <= $file_size &&  $file_size < 18874368){
                            $percent = "0.5";
                        }elseif( 18874368 <= $file_size &&  $file_size < 20971520){
                            $percent = "0.45";
                        }elseif( 20971520 <= $file_size &&  $file_size < 23068672){
                            $percent = "0.4";
                        }elseif( 23068672 <= $file_size &&  $file_size < 25165824){
                            $percent = "0.4";
                        }elseif( 25165824 <= $file_size &&  $file_size < 27262976){
                            $percent = "0.35";
                        }elseif( 27262976 <= $file_size &&  $file_size < 29360128){
                            $percent = "0.3";
                        }

                         //줄인용량 파일 생성
                        $create_file = $this->img_resize_server(array(
                            "percent"=>$percent,
                            "file_name"=>$file_name,
                            "file"=>$json["dir"].$file_name,
                            "file_size"=>102400,
                            "save_dir" =>$json["zip_dir"]
                        ));
                       
                        $zip_result = $create_file;
                        
                        // if($origin_result == false){
                        //     echo "origin_result error";
                        // }
                        if($zip_result == false){ //압축파일 생성 실패면 원본이미지 삭제
                            unlink($json["dir"].$file_name); //삭제
                        }
                        if($origin_result == false || $zip_result == false){//error
                            
                            $error_code = true;
                            break;
                        }else{
                            array_push($file_name_array,$file_name);
                            array_push($error_file_array, $json["dir"].$file_name);
                            array_push($error_file_array, $json["zip_dir"].$file_name);
                        }
                    }
                }                
            }
        }
        
        $array_result = array(
            "file_name"=>$file_name_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );
        // print_r($array_result);
        return $array_result;
        
    }



    /********************************************************************* 
    // 함 수 : 파일업로드
    // 설 명 : $files : $_FILES['files'], $path : "_uploads/poroduct_img/", $origin_path : "_uploads/poroduct_img_orign/"
    // 만든이: 안정환
    *********************************************************************/
    function no_image_file_upload($files, $path){
            $result = $this->no_image_file_upload_server(array(
                "dir" => $this->server_path.$path, //원본 파일저장 경로
                "files" => $files,
            ));

            if($result["error_code"]){ //에러면 에러 처리
                
            }else{
                //업로드 성공
                return $result;
            }
    }



    function no_image_file_upload_server($json){
        
        $error_code = "";
        $file_name_array = array();
        $error_file_array = array();
        $real_name_array = array();

        
        $this->check_folder($json["dir"]); //폴더 경로 점검

        foreach ($json["files"]['name'] as $f => $name) {
            if($json['files']['error'][$f] == 4) {//파일이 없을 경우(form에는 file 태그가 4개가 있는데 파일을 2개만 첨부했을경우)
                
            }else{
                //파일 이름을 랜덤하게 생성 한다.
                $info = new SplFileInfo($name);
                $file_name = $this->rand_name().".".$info->getExtension();
                //파일 이름 중복확인
                $exist_check = $this->server_file_exist($json["dir"].$file_name);
                while($exist_check == 1){
                    $file_name = $this->rand_name().".".$info->getExtension();
                    $exist_check = $this->server_file_exist($json["dir"].$file_name);
                }
                $origin_result = move_uploaded_file($json['files']["tmp_name"][$f], $json["dir"].$file_name); //생성
                
                if($origin_result != "1"){//error
                    
                    $error_code = true;
                    break;
                }else{
                    array_push($file_name_array,$file_name);
                    array_push($error_file_array, $json["dir"].$file_name);
                    array_push($real_name_array,$name);

                }
                    
                                
            }
        }
        
        $array_result = array(
            "file_name"=>$file_name_array,
            "real_name"=>$real_name_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );
        // print_r($array_result);
        return $array_result;
        
    }


    //server image resize
    function img_resize_server($object){
        $percent = $object["percent"];
        $file_size = filesize($object["file"]);
        $o_file = $object["file"];//원본 파일 이된다.
        $o_target_file = $o_file;
        $info = new SplFileInfo($object["file_name"]);
        
        if($file_size>$object["file_size"]){
            // Get new dimensions
            list($width, $height) = getimagesize($o_target_file);
            $new_width = ($width * $percent);
            $new_height = ($height * $percent);

            // Resample
            $image_p = imagecreatetruecolor($new_width, $new_height);            
            switch ($info->getExtension()) {
                case 'jpg':
                    $image = imagecreatefromjpeg($o_target_file);
                    break;
                case 'JPG':
                    $image = imagecreatefromjpeg($o_target_file);
                    break;
                case 'jpeg':
                    $image = imagecreatefromjpeg($o_target_file);
                    break;
                case 'JPEG':
                    $image = imagecreatefromjpeg($o_target_file);
                    break;
                case 'png':
                    $image = imagecreatefrompng($o_target_file);
                    break;
                case 'PNG':
                    $image = imagecreatefrompng($o_target_file);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($o_target_file);
                    break;
                case 'GIF':
                    $image = imagecreatefromgif($o_target_file);
                    break;
                default:
                    $image = imagecreatefromjpeg($o_target_file);
            }
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $target_file = $object["save_dir"].$object["file_name"];
            //unlink($o_target_file);
            return imagejpeg($image_p, $target_file); //압축이미지 생성
        }else{
            $target_file = $object["save_dir"].$object["file_name"];
            return copy($o_target_file, $target_file); //원본이미지 복사
        }
    }

    //s3용
    function file_upload_s3($json){
        $json["s3"] = new s3Manager();
        $error_code = "";
        $file_name_array = array();
        $error_file_array = array();
        foreach ($json["files"]['name'] as $f => $name) {
            if($json['files']['error'][$f] == 4) {//파일이 없을 경우(form에는 file 태그가 4개가 있는데 파일을 2개만 첨부했을경우)
                
            }else{
                //파일 이름을 랜덤하게 생성 한다.
                $info = new SplFileInfo($name);
                $file_name = $this->rand_name().".".$info->getExtension();
                
                //파일 이름 중복확인
                $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                while($exist_check == 1){
                    $file_name = $this->rand_name().".".$info->getExtension();
                    $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                }
                
                //해당 부분에서 파일 압축 함께 진행 origin 넣고 sub 넣는다. 이름은 같게
                $this->image_rotate($json['files'], $f); //이미지 회전
                $result = $json["s3"]->insertFile($json["dir"].$file_name,$json['files']["tmp_name"][$f]);
                
                if(isset($json["zip_dir"])){
                    $file_size = filesize($json['files']["tmp_name"][$f]);
                    $percent = "0.2";
                    if($file_size < 2097152){
                        $percent = "0.9";
                    }elseif(2097152 <= $file_size &&  $file_size < 4194304){
                        $percent = "0.85";
                    }elseif( 4194304 <= $file_size &&  $file_size < 6291456){
                        $percent = "0.8";
                    }elseif( 6291456 <= $file_size &&  $file_size < 8388608){
                        $percent = "0.75";
                    }elseif( 8388608 <= $file_size &&  $file_size < 10485760){
                        $percent = "0.7";
                    }elseif( 10485760 <= $file_size &&  $file_size < 12582912){
                        $percent = "0.65";
                    }elseif( 12582912 <= $file_size &&  $file_size < 14680064){
                        $percent = "0.6";
                    }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
                        $percent = "0.55";
                    }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
                        $percent = "0.55";
                    }elseif( 16777216 <= $file_size &&  $file_size < 18874368){
                        $percent = "0.5";
                    }elseif( 18874368 <= $file_size &&  $file_size < 20971520){
                        $percent = "0.45";
                    }elseif( 20971520 <= $file_size &&  $file_size < 23068672){
                        $percent = "0.4";
                    }elseif( 23068672 <= $file_size &&  $file_size < 25165824){
                        $percent = "0.4";
                    }elseif( 25165824 <= $file_size &&  $file_size < 27262976){
                        $percent = "0.35";
                    }elseif( 27262976 <= $file_size &&  $file_size < 29360128){
                        $percent = "0.3";
                    }

                    $create_file = $this->img_resize(array(
                        "percent"=>$percent,
                        "file_name"=>$file_name,
                        "file"=>$json['files']["tmp_name"][$f],
                        "file_size"=>102400
                    ));

                    $result = $json["s3"]->insertFile($json["zip_dir"].$file_name,$create_file);
                    unlink($create_file);
                }
                
                if($result){//error
                    $error_code = $result;
                }else{
                    array_push($file_name_array,$file_name);
                    array_push($error_file_array, $json["dir"].$file_name);
                    array_push($error_file_array, $json["zip_dir"].$file_name);
                }
            }
        }
        $array_result = array(
            "file_name"=>$file_name_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );

        return $array_result;
    }

    
    //s3용
    function sms_upload_s3($json){
        // print_r($json);
        $json["s3"] = new s3Manager();
        $error_code = "";
        $file_name_array = array();
        $file_size_array = array();
        $file_tmp_array = array();
        $error_file_array = array();
        foreach ($json["files"]['name'] as $f => $name) {
            if($json['files']['error'][$f] == 4) {//파일이 없을 경우(form에는 file 태그가 4개가 있는데 파일을 2개만 첨부했을경우)
                
            }else{
                //파일 이름을 랜덤하게 생성 한다.
                $info = new SplFileInfo($name);
                $file_name = $this->rand_name().".".$info->getExtension();
                
                //파일 이름 중복확인
                $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                while($exist_check == 1){
                    $file_name = $this->rand_name().".".$info->getExtension();
                    $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                }
                
                //해당 부분에서 파일 압축 함께 진행 origin 넣고 sub 넣는다. 이름은 같게
                $result = $json["s3"]->insertFile($json["dir"].$file_name,$json['files']["tmp_name"][$f]);

                if($result){//error
                    $error_code = $result;
                }else{
                    array_push($file_name_array,$file_name);
                    array_push($file_size_array,$json["files"]["size"][$f]);
                    array_push($file_tmp_array, $json["files"]["tmp_name"][$f]);
                    array_push($error_file_array, $json["dir"].$file_name);
                }
            }
        }
        $array_result = array(
            "file_name"=>$file_name_array,
            "file_size"=>$file_size_array,
            "file_tmp" => $file_tmp_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );

        return $array_result;
    }


    /************************************************description 관련 메소드********************************************************* */
    /*
        s3Path : 저장될 경로(s3 모드일경우 s3경로 ex: files/프로젝트이름/폴더이름.../)(server 모드일경우 파일저장될 경로)
        s3Link : 대체될 파일 link경로(s3일 경우 https://s3.ap-northeast-2.amazonaws.com/...)(server 모드일 경우 서버 경로)
        $description : sumnote의 내용
    */
    function convert_description($s3Path,$s3Link,$description){
        // 에러가 났을때 처리할 이미지파일 array 
        $error_file_array = array();
        $s3Manage = null;
        $this->check_folder($this->server_path."temp/"); //폴더 경로 점검
        if($this->mode == "s3"){ //s3모드이면 s3매니저 가져오기
            $s3Manage = new s3Manager();
        }
        $description_array = [];
        // echo $description;
        
        
        //data-filename 삭제하기 끝

        //base64 찾는 string
        // $base64StartString = '<img src="data:image/';
        $base64StartString = '<img src="data:image/';
        $base64EndString = '" data-filename=';
        //image파일 시작지점 찾기
        $pos = strpos($description, $base64StartString);
        $end = strpos($description, $base64EndString);
        

        while($pos !== false){

            //확장자 가져오기
            $extensionStartString = 'data:image/';
            $extensionEndString = ';base64';

            $extensionStartpos = strpos($description, $extensionStartString);
            $extensionEnd = strpos($description, $extensionEndString);
            $extension = substr($description, $extensionStartpos+11, ($extensionEnd-$extensionStartpos)-11);
            if($extension=="jpeg"){
                $extension="jpg";
            }
            //확장자 가져오기 끝

            $pos = $pos + 10;  //searchString 글자수만큼+
            //base64파일이 있음


            //파일이름 및 확장자
            $fileName = $this->rand_name(); //파일명은 랜덤으로 만듬
            $fileName = $fileName.".".$extension; //랜덤으로 만든 파일이름과 확장자를 합쳐줌

            //base64 String가져오기
            $base64FileString = substr($description,$pos,$end-$pos); //base64형식의 파일 string
            
            // echo $base64FileString;
            //이미지 파일 저장
            if($this->mode == "server"){
                //파일 이름 중복확인
                $exist_check = $this->server_file_exist($this->server_path.$s3Path.$fileName);
                while($exist_check == 1){
                    $fileName = $this->rand_name(); //파일명은 랜덤으로 만듬
                    $fileName = $fileName.".".$extension; //랜덤으로 만든 파일이름과 확장자를 합쳐줌
                    $exist_check = $this->server_file_exist($this->server_path.$s3Path.$fileName);
                }
                $this->description_file_upload_server($this->server_path.$s3Path, $this->base64_to_image($base64FileString, $this->server_path."temp/".$fileName), $fileName);
                array_push($error_file_array, $this->server_path.$s3Path.$fileName);
            }elseif($this->mode == "s3"){
                $s3Manage->insertFile($this->s3_path.$s3Path.$fileName, $this->base64_to_image($base64FileString, $this->server_path."temp/".$fileName));
            }
            unlink($this->server_path."temp/".$fileName);//업로드가 완료되면 temp파일삭제
            
            //링크 대체하기전 0부터 base64해당 부분까지 string 짜르기
            $description_front = substr($description,0,$end);
            $description_array[] = str_replace($base64FileString,$s3Link.$fileName,$description_front); //짜른 부분 링크대체후 array push

            //앞에 자른부분은 필요없으니 원본 description은 짜른 이후 부분만 넣어줌
            $description = substr($description, $end);


            //data-filename 삭제하기 
            //*다음파일 찾을때 방해됨
            $filenameStartString = 'data-filename="';
            $filenameEndString = '" style=';
            $end = strpos($description, $base64EndString);
            $filename_pos = strpos($description, $filenameStartString, $end);
            $filename_end = strpos($description, $filenameEndString, $filename_pos+15);
            $fileName = substr($description, $filename_pos, ($filename_end - $filename_pos)+1);

            //data-filename 삭제하기전 해당 부분까지 string 짜르기
            $filename_front = substr($description,0,$filename_end+1);
            $description_array[] = str_replace($fileName,"",$filename_front);
            //앞에 자른부분은 필요없으니 원본 description은 짜른 이후 부분만 넣어줌
            $description = substr($description, $filename_end+1);
            //data-filename 삭제하기 끝

            $pos = strpos($description, $base64StartString);
            $end = strpos($description, $base64EndString);
        }
        $complete_description = "";
        //array description 합치기
        for($i=0; $i<count($description_array); $i++){
            $complete_description = $complete_description.$description_array[$i];
        }
        $complete_description = $complete_description.$description; //나머지 뒷부분 description 합치기
        // echo $complete_description."\n";

        // 결과값 리턴
        $result = array(
            // 내용
            "description" => $complete_description,
            // 에러시 삭제할 파일 array
            "error_file_array" => $error_file_array,
        );
        return $result;
    }

    //temp폴더에 파일 임시 생성
    function base64_to_image($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen($output_file, 'w' ); //root폴더의 temp폴더에 임시 생성
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
        // echo $data[1];
        // we could add validation here with ensuring count( $data ) > 1
        $base64 = str_replace("%2b","+",$data[1]);
        fwrite( $ifp, base64_decode( $base64 ) );
        // clean up the file resource
        fclose( $ifp ); 
        return $output_file; 
    }

    function uuidgen4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
    }
         
        /********************************************************************* 
        // 함수명: get_s3_image_array($a,$b,$c) 
        // 함수 설명 : description 내용에 있는 s3 LINK인 파일의 이름을 배열로 리턴
        // $description: description 내용
        // $s3Link: s3 이미지 링크 ex: https://s3.ap-northeast-2.amazonaws.com/lbplatform/files/life_in/boardImage
        // 만든이: 과장 만든날 : 2019-05-15
        *********************************************************************/
    function get_s3_image_array($description, $s3Link){
        $array = array();

        $img_startString = '<img src="'.$s3Link;
        $img_pos = strpos($description, $img_startString);
        while($img_pos !== false){
            $description = substr($description, $img_pos);// base64StartString앞부분을 제거후 뒷부분만 가져옴

            $img_endString = '">';
            $img_end_pos = strpos($description, $img_endString);
            $img_tag = substr($description, 0, $img_end_pos+2); //img tag 원문                


            //파일이름 빼내기
            $link_startString = $s3Link;
            $link_endString = 'style=';
            $link_pos = strpos($description, $link_startString);
            $link_end = strpos($description, $link_endString);
            $link = substr($description, $link_pos, $link_end-$link_pos); 

            $file_name_array = explode( '/', $link);
            $file_name = $file_name_array[count($file_name_array)-1];
            $file_name = str_replace(" ","",$file_name);
            $file_name = str_replace('"',"",$file_name);
            
            

            $array[] = $file_name;
            
            $description = str_replace($img_tag,"",$description);
            
            $img_pos = strpos($description, $img_startString);
        }
        return $array;
    }


    /********************************************************************* 
    // 함수 설명 : description 전용 이미지 생성 메소드
    // $path : 저장될 이미지 경로
    // $file : 저장할 파일 경로
    // $file_name : 저장될 파일 이름
    *********************************************************************/
    function description_file_upload_server($path, $file, $file_name){
        $error_code = "";
        $file_name_array = array();
        $error_file_array = array();
        $this->check_folder($path); //폴더 경로 점검
        $file_size = filesize($file);
        $percent = "0.2";
        if($file_size < 2097152){
            $percent = "0.9";
        }elseif(2097152 <= $file_size &&  $file_size < 4194304){
            $percent = "0.85";
        }elseif( 4194304 <= $file_size &&  $file_size < 6291456){
            $percent = "0.8";
        }elseif( 6291456 <= $file_size &&  $file_size < 8388608){
            $percent = "0.75";
        }elseif( 8388608 <= $file_size &&  $file_size < 10485760){
            $percent = "0.7";
        }elseif( 10485760 <= $file_size &&  $file_size < 12582912){
            $percent = "0.65";
        }elseif( 12582912 <= $file_size &&  $file_size < 14680064){
            $percent = "0.6";
        }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
            $percent = "0.55";
        }elseif( 14680064 <= $file_size &&  $file_size < 16777216){
            $percent = "0.55";
        }elseif( 16777216 <= $file_size &&  $file_size < 18874368){
            $percent = "0.5";
        }elseif( 18874368 <= $file_size &&  $file_size < 20971520){
            $percent = "0.45";
        }elseif( 20971520 <= $file_size &&  $file_size < 23068672){
            $percent = "0.4";
        }elseif( 23068672 <= $file_size &&  $file_size < 25165824){
            $percent = "0.4";
        }elseif( 25165824 <= $file_size &&  $file_size < 27262976){
            $percent = "0.35";
        }elseif( 27262976 <= $file_size &&  $file_size < 29360128){
            $percent = "0.3";
        }

        //줄인용량 파일 생성
        $create_file = $this->img_resize_server(array(
            "percent"=>$percent,
            "file_name"=>$file_name,
            "file"=>$file,
            "file_size"=>102400,
            "save_dir" =>$path
        ));
        
        
        if($create_file == false){ //압축파일 생성 실패면 원본이미지 삭제
           
        }

        $array_result = array(
            "file_name"=>$file_name_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );
        // print_r($array_result);
        return $array_result;
        
    }



    
    // 이미지 파일이 아닝 첨부파일 
    function no_image_upload_file($files, $origin_path){
        // print_r($files);
        // print_r($origin_path);
        // print_r($this->server_path);
        // exit;
        $result = $this->file_upload_noimage(array(
            // "dir" => $this->s3_path.$origin_path, //원본 파일저장 경로
            "dir" => $this->server_path.$origin_path, //원본 파일저장 경로
            "files" => $files,
        ));
        if($result["error_code"]){//에러
            //이미 저장된 이미지가 있다면 삭제
            // $result["s3"]->multiDel($this->s3_path.$path, $result["file_name"]);
            // $result["s3"]->multiDel($this->s3_path.$origin_path, $result["file_name"]);
            return false;
        }else{//파일 저장 성공
            return $result; 
        }
    }


    /********************************************************************* 
    // 함수 설명 : 파일 업로드(기본) - 버퍼에 문제 생길때, 비워줌
    // $path : 저장될 이미지 경로
    // $file : 저장할 파일 경로
    // $file_name : 저장될 파일 이름
    // 개발자: 박준기
    *********************************************************************/

    //이미지 파일이 아닌 용도
    function file_upload_noimage($json){
        $json["s3"] = new s3Manager();
        $error_code = "";
        $file_name_array = array();
        $real_name_array = array();
        $error_file_array = array();
        foreach ($json["files"]['name'] as $f => $name) {
            if($json['files']['error'][$f] == 4) {//파일이 없을 경우(form에는 file 태그가 4개가 있는데 파일을 2개만 첨부했을경우)
                $file_name = "";
                array_push($file_name_array, $file_name);
            }else{
                //파일 이름을 랜덤하게 생성 한다.
                $info = new SplFileInfo($name);
                $file_name = $this->rand_name().".".$info->getExtension();
                
                //파일 이름 중복확인
                $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                while($exist_check == 1){
                    $file_name = $this->rand_name().".".$info->getExtension();
                    $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
                }
                $result = $json["s3"]->insertFile($json["dir"].$file_name,$json['files']["tmp_name"][$f]);
                if($result){//error
                    $error_code = $result;
                }else{
                    array_push($file_name_array,$file_name);
                    array_push($real_name_array,$name);
                    array_push($error_file_array, $json["dir"].$file_name);
                }
            }
            
        }
        $array_result = array(
            "file_name"=>$file_name_array,
            "real_name"=>$real_name_array,
            "error_code"=>$error_code,
            "error_file_array" => $error_file_array,
        );

        return $array_result;
    }

    

    //이미지 등록시 회전하는 문제 해결
    function image_rotate($file, $index){
        if(file_exists($file["tmp_name"][$index])){
            $destination_extension = strtolower(pathinfo($file["name"][$index],PATHINFO_EXTENSION));
            if(in_array($destination_extension, ["jpg","jpeg"]) && exif_imagetype($file["tmp_name"][$index]) === IMAGETYPE_JPEG){
                if(function_exists('exif_read_data')){
                    $exif = exif_read_data($file["tmp_name"][$index]);
                    if(!empty($exif) && isset($exif["Orientation"])){
                        $orientation = $exif["Orientation"];
                        switch($orientation){
                            case 2:
                                $flip = 1;
                                $deg = 0;
                                break;
                            case 3:
                                $flip = 0;
                                $deg = 180;
                                break;
                            case 4:
                                $flip = 2;
                                $deg = 0;
                                break;
                            case 5:
                                $flip = 2;
                                $deg = -90;
                                break;
                            case 6:
                                $flip = 0;
                                $deg = -90;
                                break;
                            case 7:
                                $flip = 1;
                                $deg = -90;
                                break;
                            case 8:
                                $flip = 0;
                                $deg = 90;
                                break;
                            default:
                                $flip = 0;
                                $deg = 0;
                        }
                        $img = imagecreatefromjpeg($file["tmp_name"][$index]);
                        if($deg !==1 && $img !== null){
                            if($flip != 0){
                                imageflip($img, $flip);
                            }
                            $img = imagerotate($img,$deg, 0);
                            imagejpeg($img, $file["tmp_name"][$index]);
                        }
                    }
                }
            }
        }
    }

}
?>