<?php
    echo phpinfo();
    exit;
    $Root = $_SERVER["DOCUMENT_ROOT"]; 
    $project_name = 'SMARTSHOES'; 
    $dir = $Root."/".$project_name."/";
    include_once($dir.'common_php/vendor/autoloader_register.php');
    include_once($dir.'common_php/vendor/autoloader.php');
    
    $server_mode = "server"; //서버 동작모드(server or s3)
    
    $email = "";
    $email_project_name = "시닉스레이";
    $email_logo = "https://s3.ap-northeast-2.amazonaws.com/lbcontents/images/DONGSAN/160192713825253.png";
    
    $no_image = "https://s3.ap-northeast-2.amazonaws.com/lbcontents/images/admin/159725223480867.jpg"; //이미지가 없을때 나타나는 이미지

    if(!isset($json["move_page"])){
        $json["move_page"]="1";
    }
    $download_path = $Root.'/SMARTSHOES/common_php/lib/download/';
    
    $import_class = []; //import할 php 폴더 경로
    //공통 import 
    $DirInc = $dir.'common_php/lb';
    $import_class = array_merge($import_class,[$DirInc]);

    //이동 전용 라이브러리 import
    
    if(!isset($json["ctl"]) || $json["ctl"] == "move"){ // ctl이 move이거나 없을경우
        $MoveController = $dir.'MoveController';
        $LangController = $dir.'LangController';
        // $RssController = $dir.'RssController';
        // $import_class =  array_merge($import_class,[$MoveController, $LangController, $RssController]);
        $import_class =  array_merge($import_class,[$MoveController, $LangController]);
    }else{
        //api 전용 라이브러리 import
        require 'common_php/lib/aws-sdk-php-resources-master/vendor/autoload.php';

        $DirApp = $dir.'app';
        $MVC = $dir.'mvc';
        $Excel = $dir.'common_php/lib/PHPExcel-1.8.2/Classes';
        $Mail = $dir.'common_php/lib/PHPMailer-master/src';

        $import_class = array_merge($import_class,[$MVC,$DirApp,$Excel,$Mail]);
    }

    $subfilepath = $dir.'sub_file_path';
    $import_class =  array_merge($import_class,[$subfilepath]);

    $autoloader = new AutoLoaderRegister($import_class);//include_once에 있는 클래스
    $array = array(
        "sumnote"=>$json,
        "json"=>$json,
        "dir"=>$dir,
        "project_name" => $project_name,
        "project_path" => $project_name."/page/user/",
        "project_admin_path" => $project_name."/page/adm/",
        "project_admin_image_path" => "https://lbcontents.s3.ap-northeast-2.amazonaws.com/images/admin/",
        "no_image" => $no_image,
        "to_email"=>$email,
        "email_project_name" => $email_project_name,
        "email_logo" => $email_logo,
        "data"=>json_encode($json),
        "session"=>new Session($project_name),
        "version"=>"?v=".date("Y-m-d H:i:s"),
        "file_path"=>new file_path($server_mode, $project_name),
        "sub_file_path"=>new sub_file_path($server_mode, $project_name),
        "send_number" => "01039518339",
        // "send_number" => "",
        // "send_id" => "admin",
        "send_id" => "tester",
        "down_path" => $download_path,
        // 알림톡 발송시 변경
        // "send_id" => "ipiacosmetic",
    );

    if(!isset($json["ctl"]) || $json["ctl"] == "move"){
        //RSS meta data 조회를 위해 모든 페이지에서 db로 조회가 필요해져서 추가
        // $db = new db("syfm498"); // DB변경시 여기 수정
        $db = new db($project_name); // DB변경시 여기 수정
        $array["file_manager"] = new file_manager($array["dir"], $server_mode, $project_name); //파일매니저 프로젝트 경로, 저장모드(s3,server), 프로젝트이름
        $array["db"] = new AppDB($db, $array["file_manager"]);
        new MoveController($array);
    }else{
        // $db = new db("shoppingmall"); // DB변경시 여기 수정
        // $db = new db("syfm498"); // DB변경시 여기 수정
        $db = new db($project_name); // DB변경시 여기 수정
        $array["file_manager"] = new file_manager($array["dir"], $server_mode, $project_name); //파일매니저 프로젝트 경로, 저장모드(s3,server), 프로젝트이름
        $array["db"] = new AppDB($db, $array["file_manager"]);
        $app = new App($array);
    }
    
?>