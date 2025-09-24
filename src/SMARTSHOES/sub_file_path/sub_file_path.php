<?php
class sub_file_path{
    private $path_json;
    private $project_name;
    private $server_mode;

    function __construct($server_mode,$project_name){ //생성자
        $this->project_name = $project_name;
        $this->server_mode = $server_mode;
        $this->path_json = array();
        

        /*
        주의사항
        file_path와 key겹치면 안됨
        */

        $this->path_json["product_file_path"] = "_uploads/product_file/";
        
        $this->path_json["inquiry_file"] = "_uploads/inquiry_file/";
        
        //공지사항 관련
        $this->path_json["notice_image_path"] = "_uploads/notice/image/";
        $this->path_json["notice_image_orign_path"] = "_uploads/notice/origin_image/";
        $this->path_json["notice_file_path"] = "_uploads/notice/file/";
        $this->path_json["notice_content_path"] = "_uploads/notice/content/";

        //홍보/행사 관련
        $this->path_json["promotion_main_image_path"] = "_uploads/promotion/main/";
        $this->path_json["promotion_sub_image_path"] = "_uploads/promotion/sub/";
        $this->path_json["promotion_content_path"] = "_uploads/promotion/content/";

        $this->path_json["img_file"] = "_uploads/img_file/";


        
        $this->path_json["pc_popup_image_path"] = "_uploads/pc_popup_image/";
        $this->path_json["pc_popup_image_orign_path"] = "_uploads/pc_popup_image_orign/";

        
    }

    // js에서 사용
    function get_path(){
        $array = array();
        if($this->server_mode == "s3"){
            return $array;
        }elseif($this->server_mode == "server"){
            foreach($this->path_json as $key=>$value) {
                $array[$key] = $this->project_name."/".$value;
            }
            return $array;
        }
    }

    //php 모델 함수에서 사용
    function get_path_php(){
        return $this->path_json;
    }

    //php 모델 함수에서 사용 => db저장용 링크 projectname이 들어있음
    function get_link_php(){
        if($this->server_mode == "s3"){
            return $array;
        }elseif($this->server_mode == "server"){
            foreach($this->path_json as $key=>$value) {
                $array[$key] = $this->project_name."/".$value;
            }
            return $array;
        }
        // return $this->project_name.$this->path_json;
    }
    
}
?>