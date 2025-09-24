<?php
    class LangController{
        function __construct($array){
            $model = new LangModel($array);
            $json = $array["json"];
            /********************************************************************* 
            // 설 명 : 관련 함수 Model로 이동
            // 담당자: 
            *********************************************************************/
            $param1 = null;
            if(isset($json["param1"])){
                $param1 = $json["param1"];
                $session = $array["session"];
                if($session->is_admin_login()){
                // if(true){
                    if(method_exists($model,$param1)){
                        $model->$param1();
                    }else{
                        $result = array(
                            "result" => null,
                            "error_code" => null,
                            "message" => null,
                            "value" => null,
                        );
                        $result["result"]="0";
                        $result["error_code"]="404";
                        $result["message"]="컨트롤러[".get_class($this)."] <br/> 모델[".get_class($model)."] <br/> [".$param1."] 함수를 찾을 수 없습니다";
                        echo json_encode($result,JSON_UNESCAPED_UNICODE);
                    }
                    
                }
            }
        }
    }
?>