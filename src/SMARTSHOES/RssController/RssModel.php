<?php
    class RssModel extends gf{
        private $param;
        private $dir;
        private $conn;
        private $file_manager;

        function __construct($array){
            if(isset($array["db"])){
                $this->conn = $array["db"];
            }
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
        }
        
        /********************************************************************* 
        // 함 수 : empty 체크
        // 설 명 : array("id","pw")
        // 만든이: 안정환
        // 담당자: 이은영
        *********************************************************************/
        function value_check($check_value_array){
            $object = array(
                "param"=>$this->json,
                "array"=>$check_value_array
            );
            $check_result = $this->empty_check($object);
            if($check_result["result"]){//param 값 체크 비어있으면 실행 안함
                if($check_result["value_empty"]){//필수 값이 비었을 경우
                    $this->result["result"]="0";
                    $this->result["error_code"]="101";
                    $this->result["message"]=$check_result["value_key"]."가 비어있습니다.";
                    return false;
                }else{
                    return true;
                }
            }else{
                $this->result["result"]="0";
                $this->result["error_code"]="100";
                $this->result["message"]=$check_result["value"]." 가 없습니다.";
                return false;
            }
        }

        /********************************************************************* 
        // 함 수 : 제품 상세페이지에서 제품 정보로 meta 태그를 생성하여 반환
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_product_meta_data(){
            $param = $this->param;  
            $sql = "select thumnail_file, product_name, meta_description from product_img as t1 left join product_name as t2 on t1.product_idx = t2.product_idx ";
            $sql .= "where t2.lang_idx = 1 ";
            $sql .= "and t1.product_idx = ".$param["idx"]." ";
            $sql .= "group by t1.product_idx ";
            $result = $this->conn->db_select($sql);
            $html = "";
            $add_parameter = "";
            if(isset($param["idx"]) && isset($param["main"]) && isset($param["category_1_idx"])){
                $add_parameter = '<meta property="og:url" content="https://incontro.co.kr?param=shop_view&idx='.$param["idx"].'&main='.$param["main"].'&category_1_idx='.$param["category_1_idx"].'">';
            }else if(isset($param["idx"]) && isset($param["category_1_idx"])){
                $add_parameter = '<meta property="og:url" content="https://incontro.co.kr?param=shop_view&idx='.$param["idx"].'&category_1_idx='.$param["category_1_idx"].'">';
            }

            if($result["result"] == "1"){
                if(count($result["value"]) > 0){
                    //메타 디스크립션을 입력하지 않은 경우 제품명 넣어주기
                    if($result["value"][0]["meta_description"] == null){
                        $result["value"][0]["meta_description"] = $result["value"][0]["product_name"];
                    }

                    $html .= $add_parameter;
                    $html .= '<meta property="og:title" content="'.$result["value"][0]["product_name"].'">';
                    $html .= '<meta property="og:type" content="website">';
                    $html .= '<meta property="og:description" content="'.$result["value"][0]["meta_description"].'">';
                    $html .= '<meta property="og:image" content="https://lbcontents.s3.ap-northeast-2.amazonaws.com/files/DONGSAN/_uploads/thumnail_img_orign/'.$result["value"][0]["thumnail_file"].'">';
                    $html .= '<meta name="description" content="'.$result["value"][0]["meta_description"].'">';
                    $html .= '<script>var meta_description = "'.$result["value"][0]["meta_description"].'"</script>';
                }
            }

            echo $html;
        }

        /********************************************************************* 
        // 함 수 : DB에 저장되어 있는 정보로 meta 태그를 생성하여 반환
        // 설 명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_meta_data(){
            $param = $this->param;  
            $sql = "select * from seo_data ";
            $result = $this->conn->db_select($sql);
            if($result["result"] == "1"){
                $html = "";
                if(count($result["value"]) > 0){
                    $html .= '<title>'.$result["value"][0]["title"].'</title>';
                    $html .= '<meta property="og:title" content="'.$result["value"][0]["title"].'">';
                    $html .= '<meta name="description" content="'.$result["value"][0]["description"].'">';
                    $html .= '<meta name="author" content="'.$result["value"][0]["author"].'">';
                    $html .= '<meta name="keyword" content="'.$result["value"][0]["keywords"].'">';
                }
            }

            echo $html;
        }

        function getCookiesStr($name){
			$data = null;
			if(isset($_COOKIE[$name])){
				$data = $_COOKIE[$name];
			}else{
				$data = null;
			}

			return $data;
		}
    }
?>