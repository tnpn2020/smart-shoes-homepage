<?php
	class RssController {
		private $param;
        private $dir;
        private $version;
		function __construct($init_object){
			
			$this->param = $init_object["json"];
            $this->dir = $init_object["dir"];
            $this->version = $init_object["version"];
            $this->model = new RssModel($init_object);
		}

		//제품 상세페이지에서 제품 정보로 meta tag를 생성하는 함수
		function request_product_meta_data(){
			$this->model->request_product_meta_data();
		}

		//DB에 저장되어 있는 data로 meta tag를 생성하는 함수
		function request_meta_data(){
			$this->model->request_meta_data();
		}
	}
?>