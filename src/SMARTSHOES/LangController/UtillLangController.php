<?php

if(PHP_VERSION_ID >= 80200){
	#[\AllowDynamicProperties]
	class UtillLangController {
		private $param;
        private $dir;
        private $version;
		function __construct($init_object){
			
			$this->param = $init_object["json"];
            $this->dir = $init_object["dir"];
            $this->version = $init_object["version"];
            $this->model = new UtillLangModel($init_object);
		}

		function change_lang(){
			$this->model->change_lang();
		}

		function lang_check(){
			$this->model->lang_check();
		}

		function lang($page,$str){
			$result = $this->model->lang($page,$str);
			return $result;
		}

		function print_css($page_type, $main_lang){
			$this->model->print_css($page_type,$main_lang,$this->version,$this->dir);
		}

		function font_link(){
			$this->model->font_link();
		}
	}
}else{
	print_r("php version is not 8.2.0");
}
?>