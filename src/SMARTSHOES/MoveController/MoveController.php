<?php

if(PHP_VERSION_ID >= 80200){
#[\AllowDynamicProperties]
    class MoveController{
        private $param;
        private $dir;
        private $version;
        private $project_name;
        private $file_path;
        private $utillLang;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->version = $array["version"];
            $this->project_name = $array["project_name"];
            $this->project_path = $array["project_path"];
            $this->project_admin_path = $array["project_admin_path"];
            $this->project_admin_image_path = $array["project_admin_image_path"];
            $this->data = $array["data"];
            $this->session = $array["session"];
            $this->file_path = $array["file_path"];
            $this->sub_file_path = $array["sub_file_path"];
            $this->utillLang = new UtillLangController($array);
            /*********************************************************************
            // 설 명 : 일반페이지와 관리자페이지 이동 구분
            *********************************************************************/
            if(isset($this->param["param"])){
                if($this->param["param"] == "adm"){
                    // $this->session = $array["session"];
                    $this->move_adm();
                }elseif($this->param["param"] == "sumnote"){
                    $this->move_sumnote();
                }else{
                    $this->move();//예외처리:param이 adm이 아니라면 일반페이지로 이동
                }
            }else{
                $this->move();
            }
        }

        /********************************************************************* 
        // 함 수 : 
        // 설 명 : 썸노트 iframe으로 부를시 사용  
        *********************************************************************/
        function move_sumnote(){
            $id = "null";
            if(isset($this->param["id"])){
                $id = $this->param["id"];
            }
            include_once $this->dir."lib/summernote-0.8.9-dist/dist/index_move.php";
        }

        /********************************************************************* 
        // 함 수 : move()
        // 설 명 : 페이지이동(사용자)
        *********************************************************************/
        function move(){
            $dir = $this->dir;
            $version = $this->version; //페이지 안의 css's version사용
            
            // $user_idx = $this->session->get_user_idx();
            $is_login = 0;
            if($this->session->is_login_php()){
                $is_login = -1;
            }else{
                $is_login = 0;
            }

            //상품 상세보기 리뷰에 사용
            $shop_review_page = "1";
            if(isset($this->param["shop_review_page"])){
                $shop_review_page = $this->param["shop_review_page"];
            }
            //상품 상세보기 qna에 사용
            $shop_qna_page = "1";
            if(isset($this->param["shop_qna_page"])){
                $shop_qna_page = $this->param["shop_qna_page"];
            }

            $tab = null;
            if(isset($this->param["tab"])){
                $tab = $this->param["tab"];
            }

            $param = null;
            if(isset($this->param["param"])){
                $param = $this->param["param"];
                if($param == "search"){
                    if($this->param["flag"] == "rss"){
                        include_once $dir."page/search/rss.php";
                    }else if($this->param["flag"] == "sitemap"){
                        include_once $dir."page/search/sitemap.php";
                    }else{
                        include_once $dir."page/user/index.php";
                    }
                }else{
                    $file_isset = $dir."page/user/".$param.".php";
                    if(is_file($file_isset)){
                        include_once $file_isset;
                    }else{
                        include_once $dir."page/user/index.php";
                    }
                }
            }else{
                
                include_once $dir."page/user/index.php";
            }
        }

        /********************************************************************* 
        // 함 수 : move_adm()
        // 설 명 : 페이지이동(관리자)
        *********************************************************************/
        function move_adm(){
            $dir = $this->dir;
            $version = $this->version;
            /********************************************************************* 
            // $browser 익스플로러인지 확인 후, 크롬사용권유 (관리자로그인페이지)
            *********************************************************************/
            $browser = null;
            $param = null;
            if(isset($this->param["browser"])) {
                $browser = $this->param["browser"];
            }

            if(!isset($this->param["move_page"])){
				$this->param["move_page"]="1";
            }

            $this->param["project_name"] = $this->project_name;
            $data = json_encode($this->param);
            

            $userAgent = $_SERVER["HTTP_USER_AGENT"];
            if(preg_match("/MSIE*/", $userAgent)){
                $browser = "Explorer";                              
            }elseif(preg_match("/Trident*/", $userAgent) &&  preg_match("/rv:11.0*/", $userAgent) &&  preg_match("/Gecko*/", $userAgent)){
                $browser = "Explorer";
            }else{
                $browser = "no_ie";
            }
            /********************************************************************* 
            // 관리자페이지
            *********************************************************************/
            if(isset($this->param["param1"])){
                $session = $this->session;
                if($session->is_admin_login_php()){
                // if(true){

                    $param1 = $this->param["param1"];
                    $file_isset = $dir."page/adm/adm_".$param1.".php";
                    if(is_file($file_isset)){
                        include_once $file_isset;
                    }else{
                        include_once $dir."page/adm/adm_login.php";
                    }
                }else{
                    include_once $dir."page/adm/adm_login.php";
                }
            }else{
                include_once $dir."page/adm/adm_login.php";
            }
        }

        /********************************************************************* 
        // 함 수 : side_active()
        // 설 명 : 사용자페이지 탭 연결
        // 담당자 : 최진혁  
        *********************************************************************/
        function side_active($now_tab){
            if(isset($this->param["param"])){
                if($now_tab == $this->param["param1"]){
                    echo "current";
                }else if($now_tab == "menu1_banner_main" && $this->param["param1"] == "menu1_banner_main_upload"){
                    echo "current";
                }else if($now_tab == "menu1_banner_sub1" && $this->param["param1"] == "menu1_banner_sub1_upload"){
                    echo "current";
                }else if($now_tab == "menu1_banner_sub2" && $this->param["param1"] == "menu1_banner_sub2_upload"){
                    echo "current";
                }else if($now_tab == "menu1_banner_sub3" && $this->param["param1"] == "menu1_banner_sub3_upload"){
                    echo "current";
                }
                else{
                    echo "";
                }
            }else{
                echo "";
            }
        }

        function header_active($now_header){
            if(isset($this->param["param"])){
                $header_array = explode('_', $now_header); // menu1_alarm 일때  _ 기준으로 문자를 array 나눈다
                $param1_array = explode('_', $this->param["param1"]);
                if($header_array[0] == $param1_array[0]){
                    echo "current";
                }else{
                    echo "";
                }
            }else{
                echo "";
            }
        }

        function tab_display($now_tab){
            if(isset($this->param["tab"])){
                if($now_tab == $this->param["tab"]){
                    echo "block";
                }else{
                    echo "none";
                }
            }else{
                echo "none";
            }
        }

        /********************************************************************* 
        // 함 수 : session_display()
        // 설 명 : session에 따라 header의 사용자 menu display 변경
        // 담당자 : 조경민  
        *********************************************************************/
        function session_display($type){
            if($this->session->is_login_php()){ //로그인이 되어있을때 (session이 있을때)
                if($type == 1){
                    echo "none";
                }else{
                    echo "";
                }
            }else{
                if($type == 1){
                    echo "";
                }else{
                    echo "none";
                }
            }
        }
    }
}else{
    print_r("php version is not 8.2.0");
}
?>