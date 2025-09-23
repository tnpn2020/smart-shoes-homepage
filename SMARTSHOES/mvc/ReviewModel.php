<?php
    class ReviewModel extends gf{
        private $param;
        private $dir;
        private $conn;

        function __construct($array){
            $this->param = $array["json"];
            $this->dir = $array["dir"];
            $this->conn = $array["db"];
            $this->result = array(
                "result" => null,
                "error_code" => null,
                "message" => null,
                "value" => null,
            );
            $this->session = $array["session"];
        }

        /********************************************************************* 
        // 함 수 : empty 체크
        // 설 명 : array("id","pw")
        // 만든이: 안정환
        *********************************************************************/
        function value_check($check_value_array){
            $object = array(
                "param"=>$this->param,
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
        // 함수 설명 : review 리스트
        // 설명 : 
        // 만든이: 최진혁
        // 수정 : 조경민->리뷰 제품 이미지 들고오는 코드 추가 하였습니다.
        *********************************************************************/
        function review_list(){
            $param = $this->param;
            if($this->value_check(array("lang_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];

                $sql = "select distinct t1.purchase_order_idx, t1.product_idx, t1.grade_point, t1.review, group_concat(img) as img,";
                $sql .= " t3.name as user_name, t1.regdate, t4.product_name, t4.thumnail_file from product_review as t1";
                $sql .= " left join product_review_img as t2 on t1.idx = t2.review_idx";
                $sql .= " left join user as t3 on t1.user_idx = t3.idx";
                $sql .= " left join product_name as t4 on t1.product_idx = t4.product_idx";
                $sql .= " where t4.lang_idx = ".$param["lang_idx"]." and t4.state != 3";
                if($param["word"] != null && $param["word"] != "" && $param["word"] != "undefined"){
                    $sql .= " and t4.product_name like '%".$param["word"]."%' ";
                }
                $sql .= " group by t1.purchase_order_idx order by t1.regdate desc";
                $sql .= " limit ".$page_size*($page-1).",".$page_size;
                $result = $this->conn->db_select($sql);
                if($result["result"] == 0){
                    $this->result = $result;
                }else{
                    //해당 상품의 옵션명을 만들어서 해당하는 게시글의 넣어주기
                    $sql = "select t1.purchase_order_idx, t2.idx, s1.name as option_1_name, s2.name as option_2_name, s3.name as option_3_name, s4.name as option_4_name, t3.category_idx";
                    $sql .= " from product_review as t1 left join order_product as t2 on t1.purchase_order_idx = t2.idx";
                    $sql .= " left join (select name, option_1_idx from option_1_name where lang_idx = ".$param["lang_idx"].") as s1 on s1.option_1_idx = t2.option_1_idx";
                    $sql .= " left join (select name, option_2_idx from option_2_name where lang_idx = ".$param["lang_idx"].") as s2 on s2.option_2_idx = t2.option_2_idx";
                    $sql .= " left join (select name, option_3_idx from option_3_name where lang_idx = ".$param["lang_idx"].") as s3 on s3.option_3_idx = t2.option_3_idx";
                    $sql .= " left join (select name, option_4_idx from option_4_name where lang_idx = ".$param["lang_idx"].") as s4 on s4.option_4_idx = t2.option_4_idx";
                    $sql .= " left join product_category_relation as t3 on t1.product_idx = t3.product_idx";
                    $sql .= " left join product_name as t4 on t1.product_idx = t4.product_idx";
                    $sql .= " where t4.lang_idx = 1 and t4.state != 3";
                    if($param["word"] != null && $param["word"] != "" && $param["word"] != "undefined"){
                        $sql .= " and t4.product_name like '%".$param["word"]."%' ";
                    }
                    $sql .= " group by t1.purchase_order_idx";
                    $sql .= " order by t1.regdate desc limit ".$page_size*($page-1).",".$page_size;
                    $option_result = $this->conn->db_select($sql);
                    if($option_result["result"] == 0){
                        $this->result = $option_result;
                    }else{
                        //옵션 full_name 만들기
                        for($i = 0; $i < count($option_result["value"]); $i++){
                            //리뷰의 order_idx와 검색 결과의 idx가 일치하면 실행
                            $temp_array = [];
                            if($result["value"][$i]["purchase_order_idx"] == $option_result["value"][$i]["idx"]){
                                for($j = 1 ;$j <= 4; $j++){
                                    array_push($temp_array, $option_result["value"][$i]["option_".$j."_name"]);
                                }
                                $result["value"][$i]["category_idx"] = $option_result["value"][$i]["category_idx"];
                            }
                            $result["value"][$i]["option_name_arr"] = $temp_array;
                        }
                        //게시글 총 개수 구하기
                        $sql = "select count(distinct t1.purchase_order_idx) as total from product_review as t1";
                        $sql .= " left join product_review_img as t2 on t1.idx = t2.review_idx";
                        $sql .= " left join user as t3 on t1.user_idx = t3.idx";
                        $sql .= " left join product_name as t4 on t1.product_idx = t4.product_idx";
                        $sql .= " where t4.lang_idx = 1 and t4.state != 3";
                        if($param["word"] != null && $param["word"] != "" && $param["word"] != "undefined"){
                            $sql .= " and t4.product_name like '%".$param["word"]."%' ";
                        }
                        $total_result = $this->conn->db_select($sql);
                        if($total_result["result"] == 0){
                            $this->result = $total_result;
                        }else{
                            $this->result = $result;
                            $this->result["total_count"] = $total_result["value"][0]["total"];
                        }
                    }
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

        /********************************************************************* 
        // 함수 설명 : shop_view page에서 상품에 해당하는 모든 리뷰를 가져오는 함수
        // 설명 : 
        // 만든이: 조경민
        *********************************************************************/
        function request_shop_view_review(){
            $param = $this->param;
            if($this->value_check(array("pd_idx"))){
                $page_size = (int)$param["page_size"];
                $page = (int)$param["move_page"];
                $sql = "select t1.*,t2.name, group_concat(t3.img) as img from product_review as t1";
                $sql .= " left join user as t2 on t1.user_idx = t2.idx";
                $sql .= " left join product_review_img as t3 on t1.idx = t3.review_idx";
                $sql .= " where t1.product_idx = ".$param["pd_idx"];
                $sql .= " group by purchase_order_idx";
                $sql .= " order by t1.regdate desc limit ".$page_size*($page-1).",".$page_size;
                $result = $this->conn->db_select($sql);
                if($result["result"] == "1"){
                    $this->result = $result;
                    $this->result["message"] = "상품 상세페이지 리뷰 검색 성공";
                    //개수
                    $sql = "select count(distinct t1.purchase_order_idx) as total_count from product_review as t1";
                    $sql .= " left join user as t2 on t1.user_idx = t2.idx";
                    $sql .= " left join product_review_img as t3 on t1.idx = t3.review_idx";
                    $sql .= " where t1.product_idx = ".$param["pd_idx"];
                    $result = $this->conn->db_select($sql);
                    $this->result["total_count"] = $result["value"][0]["total_count"];
                }else{
                    $this->result["result"] = "0";
                    $this->result["error_code"] = "302";
                    $this->result["message"] = "상품 상세페이지 리뷰 검색 성공";
                }
            }
            echo json_encode($this->result, JSON_UNESCAPED_UNICODE);
        }

    }
?>