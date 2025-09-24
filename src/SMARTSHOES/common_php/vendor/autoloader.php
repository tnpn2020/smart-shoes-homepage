<?php
	class auto_loader{
		private $_filepath;
		function __construct($filepath){
			// echo $filepath."<br/>";
			error_reporting(E_ALL);
			ini_set("display_errors", 1);
			$this->_filepath=$filepath;
			// print_r($this->_filepath."<br>");
			spl_autoload_register(array($this, 'load'));
		}

		public function load($class) {
			// echo $class."<br/>";
			$ary = explode('\\',$class);
			// print_r($ary);
			$dir = $this->_filepath.'/'.$ary[(count($ary)-1)].'.php';
			// echo $dir."[".file_exists($dir)."]"."<br/>";
			if(file_exists($dir)){
				include_once $dir;
			}
		}
	}
?>