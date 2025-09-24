<?php
	class AutoLoaderRegister{
		function __construct($folder){
			// print_r($folder);
			for($i=0;$i<count($folder);$i++){
				new auto_loader($folder[$i]);
			}
		}
	}
?>