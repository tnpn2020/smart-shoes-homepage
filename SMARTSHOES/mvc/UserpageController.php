<?php
    class UserpageController{
        function __construct($array){
            $model = new UserpageModel($array);
            $json = $array["json"];
            /********************************************************************* 
            // 설 명 : 관련 함수 Model로 이동
            // 담당자: 
            *********************************************************************/
            $param1 = null;
            if(isset($json["param1"])){
                $param1 = $json["param1"];
                $model->$param1();
            }
        }
    }
?>