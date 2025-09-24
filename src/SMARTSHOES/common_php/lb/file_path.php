<?php
class file_path{
    private $path_json;
    private $project_name;
    private $server_mode;

    function __construct($server_mode,$project_name){ //생성자
        $this->project_name = $project_name;
        $this->server_mode = $server_mode;
        $this->path_json = array();
        
        if($server_mode == "server"){ //서버 모드면
            //회사파일패스
            $this->path_json["company_file_path"] = "_uploads/company_file/";
            //공지사항파일패스
            $this->path_json["notice_file_path"] = "_uploads/notice_file/";
            //상품파일패스
            $this->path_json["product_file_path"] = "_uploads/product_file/";
            $this->path_json["flag_nation_path"] = "_uploads/flag_nation_img/";
            $this->path_json["product_popup_path"] = "_uploads/product_popup_img/";
            $this->path_json["product_popup_orign_path"] = "_uploads/product_popup_img_orign/";
            $this->path_json["product_img_path"] = "_uploads/product_img/";
            $this->path_json["product_img_orign_path"] = "_uploads/product_img_orign/";
            $this->path_json["product_thumnail_path"] = "_uploads/thumnail_img/";
            $this->path_json["product_thumnail_orign_path"] = "_uploads/thumnail_img_orign/";
            $this->path_json["product_description_path"] = "_uploads/description_img/";
            $this->path_json["coupon_img_path"] = "_uploads/coupon_img/";
            $this->path_json["coupon_img_origin_path"] = "_uploads/coupon_img_origin/";
            $this->path_json["notice_description_path"] = "_uploads/notice_description_img/";
            $this->path_json["lang_image_path"] = "page/adm/images/";
            $this->path_json["faq_description_path"] = "_uploads/faq_description_img/";
            $this->path_json["inquiry_img_path"] = "_uploads/inquiry_img/";
            $this->path_json["inquiry_img_origin_path"] = "_uploads/inquiry_origin_img/";
            $this->path_json["pc_banner_img_path"] = "_uploads/pc_banner_img/";
            $this->path_json["pc_banner_img_origin_path"] = "_uploads/pc_banner_origin_img/";
            $this->path_json["m_banner_img_path"] = "_uploads/m_banner_img/";
            $this->path_json["m_banner_img_origin_path"] = "_uploads/m_banner_origin_img/";
            $this->path_json["pc_popup_img_path"] = "_uploads/pc_popup_img/";
            $this->path_json["pc_popup_img_origin_path"] = "_uploads/pc_popup_origin_img/";
            $this->path_json["certification_img_path"] = "_uploads/certification_img/";
            $this->path_json["certification_img_origin_path"] = "_uploads/certification_origin_img/";
            $this->path_json["review_img_path"] = "_uploads/review_img/";
            $this->path_json["review_img_origin_path"] = "_uploads/review_origin_img/";
            $this->path_json["qna_img_path"] = "_uploads/qna_img/";
            $this->path_json["qna_img_origin_path"] = "_uploads/qna_origin_img/";
            $this->path_json["event_img_path"] = "_uploads/event_img/";
            $this->path_json["event_img_origin_path"] = "_uploads/event_origin_img/";
            $this->path_json["event_add_file_path"] = "_uploads/event_add_file/";
            $this->path_json["event_description_path"] = "_uploads/event_description_img/";
            $this->path_json["win_description_path"] = "_uploads/notice_description_img/";
            $this->path_json["dl_sample_img_path"] = "_uploads/dl_sample_img/";
            $this->path_json["dl_sample_origin_img_path"] = "_uploads/dl_sample_origin_img/";
            $this->path_json["dl_sample_worksheet_img_path"] = "_uploads/dl_sample_worksheet_img/";
            $this->path_json["dl_sample_worksheet_origin_img_path"] = "_uploads/dl_sample_worksheet_origin_img/";
            $this->path_json["chat_file_path"] = "_uploads/chat_file/";
            $this->path_json["chat_file_img_path"] = "_uploads/chat_file_img_path/";
            $this->path_json["chat_file_img_origin_path"] = "_uploads/chat_file_img_origin_path/";
            $this->path_json["test"] = "_uploads/test/";
            $this->path_json["teacher_img_path"] = "_uploads/teacher_img/";
            $this->path_json["teacher_img_origin_path"] = "_uploads/teacher_origin_img/";
            $this->path_json["company_img_path"] = "_uploads/company_img/";
            $this->path_json["company_img_origin_path"] = "_uploads/company_origin_img/";
        }else if($server_mode == "s3"){ //s3 모드면
            $this->path_json["licence_path"] = "_uploads/licence_path/";
            $this->path_json["licence_origin_path"] = "_uploads/licence_origin_path/";
            $this->path_json["bank_img_path"] = "_uploads/bank_img_path/";
            $this->path_json["bank_img_origin_path"] = "_uploads/bank_img_origin_path/";
            $this->path_json["product_img_path"] = "_uploads/product_img/";
            $this->path_json["product_img_orign_path"] = "_uploads/product_img_orign/";
            $this->path_json["product_thumnail_path"] = "_uploads/thumnail_img/";
            $this->path_json["product_thumnail_orign_path"] = "_uploads/thumnail_img_orign/";
            $this->path_json["product_description_path"] = "_uploads/description_img/";
            $this->path_json["product_order_info_description_path"] = "_uploads/product_order_info_description_img/";
            $this->path_json["coupon_img_path"] = "_uploads/coupon_img/";
            $this->path_json["coupon_img_origin_path"] = "_uploads/coupon_img_origin/";
            $this->path_json["notice_description_path"] = "_uploads/notice_description_img/";
            $this->path_json["expo_description_path"] = "_uploads/expo_description_img/";
            $this->path_json["company_description_path"] = "_uploads/company_description_img/";
            $this->path_json["lang_image_path"] = "images/";
            $this->path_json["faq_description_path"] = "_uploads/faq_description_img/";
            $this->path_json["inquiry_img_path"] = "_uploads/inquiry_img/";
            $this->path_json["inquiry_img_origin_path"] = "_uploads/inquiry_origin_img/";
            $this->path_json["pc_banner_img_path"] = "_uploads/pc_banner_img/";
            $this->path_json["pc_banner_img_origin_path"] = "_uploads/pc_banner_origin_img/";
            $this->path_json["m_banner_img_path"] = "_uploads/m_banner_img/";
            $this->path_json["m_banner_img_origin_path"] = "_uploads/m_banner_origin_img/";
            $this->path_json["s_banner_img_path"] = "_uploads/s_banner_img/";
            $this->path_json["s_banner_img_origin_path"] = "_uploads/s_banner_origin_img/";
            $this->path_json["pc_popup_img_path"] = "_uploads/pc_popup_img/";
            $this->path_json["pc_popup_img_origin_path"] = "_uploads/pc_popup_origin_img/";
            $this->path_json["certification_img_path"] = "_uploads/certification_img/";
            $this->path_json["certification_img_origin_path"] = "_uploads/certification_origin_img/";
            $this->path_json["review_img_path"] = "_uploads/review_img/";
            $this->path_json["review_img_origin_path"] = "_uploads/review_origin_img/";
            $this->path_json["qna_img_path"] = "_uploads/qna_img/";
            $this->path_json["qna_img_origin_path"] = "_uploads/qna_origin_img/";
            $this->path_json["event_img_path"] = "_uploads/event_img/";
            $this->path_json["event_img_origin_path"] = "_uploads/event_origin_img/";
            $this->path_json["event_add_file_path"] = "_uploads/event_add_file/";
            $this->path_json["event_description_path"] = "_uploads/event_description_img/";
            $this->path_json["win_description_path"] = "_uploads/notice_description_img/";
            $this->path_json["dl_sample_img_path"] = "_uploads/dl_sample_img/";
            $this->path_json["dl_sample_origin_img_path"] = "_uploads/dl_sample_origin_img/";
            $this->path_json["dl_sample_worksheet_img_path"] = "_uploads/dl_sample_worksheet_img/";
            $this->path_json["dl_sample_worksheet_origin_img_path"] = "_uploads/dl_sample_worksheet_origin_img/";
            $this->path_json["chat_file_path"] = "_uploads/chat_file/"; //일반 파일
            $this->path_json["chat_file_img_path"] = "_uploads/chat_file_img_path/"; //이미지 파일
            $this->path_json["chat_file_img_origin_path"] = "_uploads/chat_file_img_origin_path/";
            $this->path_json["test"] = "_uploads/test/";
            $this->path_json["test_origin"] = "_uploads/test_origin/";
            $this->path_json["report_description_path"] = "_uploads/report_description_img/";
            $this->path_json["reference_description_path"] = "_uploads/reference_description_img/";
            $this->path_json["reference_file_path"] = "_uploads/reference_file_path/"; //이미지 파일
            $this->path_json["teacher_img_path"] = "_uploads/teacher_img/";
            $this->path_json["teacher_img_origin_path"] = "_uploads/teacher_origin_img/";
            $this->path_json["schedule_img_path"] = "_uploads/schedule_img/";
            $this->path_json["schedule_img_origin_path"] = "_uploads/schedule_origin_img/";
            $this->path_json["expo_img_path"] = "_uploads/expo_img/";
            $this->path_json["expo_img_origin_path"] = "_uploads/expo_origin_img/";
            $this->path_json["company_img_path"] = "_uploads/company_img/";
            $this->path_json["company_img_origin_path"] = "_uploads/company_origin_img/";
            $this->path_json["construction_thumnail_path"] = "_uploads/thumnail_img/";
            $this->path_json["construction_thumnail_orign_path"] = "_uploads/thumnail_img_orign/";
        }else if($server_mode == "sms"){
            //  SMS용
            $this->path_json["SMS_img_path"] = "_uploads/SMS_img_path/";
            $this->path_json["SMS_img_origin_path"] = "_uploads/SMS_img_origin_path/";
        }
    }

    // js에서 사용
    function get_path(){
        $array = array();
        if($this->server_mode == "s3"){
            foreach($this->path_json as $key=>$value) {
                $array[$key] = "https://pomesoft-s3.s3.ap-northeast-2.amazonaws.com/pomesoft/_uploads/".$this->project_name."/".$value;

            }
            return $array;
        }else if($this->server_mode == "sms"){
            foreach($this->path_json as $key=>$value) {
                $array[$key] = "https://pomesoft-s3.s3.ap-northeast-2.amazonaws.com/pomesoft/_uploads/SMS/".$value;
            }
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
            foreach($this->path_json as $key=>$value) {
                $array[$key] = "https://pomesoft-s3.s3.ap-northeast-2.amazonaws.com/pomesoft/_uploads/".$this->project_name."/".$value;
            }
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