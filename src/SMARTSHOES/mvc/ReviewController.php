<?php
    class ReviewController{
        function __construct($array){
            $model = new ReviewModel($array);
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