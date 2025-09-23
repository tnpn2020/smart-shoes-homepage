<?php
    class CommonController{
        function __construct($array){
            $model = new CommonModel($array);
            $json = $array["json"];
            $param1 = null;
            if(isset($json["param1"])){
                $param1 = $json["param1"];
                $model->$param1();
            }
            else{
                $error = array(
                    "result" => "0",
                    "error_code" => "404",
                    "message" => "Not fount",
                );
                echo json_encode($error,JSON_UNESCAPED_UNICODE);
            }
        }
    }
?>