<?php
class PaymentModel
{
    //테스트
    // private $imp_rest_api_key = "7941013266574294";
    // private $imp_rest_api_secret_key = "NQKdu6ie0Il0QEfuhOgK3fai5PWS1YIOOs2jzlxA3SBUxRoPFFzIVK11zYTBWCqK5s4ENBfRuQL1HaQn";

    // //t실제 
    private $imp_rest_api_key = "9528815403003657";
    private $imp_rest_api_secret_key = "gfN48Hw8gKZ4RvvXOw0dcAUfoap2LUU29I6iqCqGnqLfVgcKMdXZNSeb3EeOy9QISX0iArYXRJcM0pxN";
    
    function __construct(){
       
    }

    //아임포트 토큰 가져오기

    function get_token(){
        $url = "https://api.iamport.kr/users/getToken";
        $header_data = array(
            'Content-Type: application/json'
        );
        $json_data = '{"imp_key": "'.$this->imp_rest_api_key.'", "imp_secret":"'.$this->imp_rest_api_secret_key.'"}';
        $output = $this->curlSet($url,$header_data,$json_data);

        $decode = json_decode($output);
        $token = $decode->response->access_token;
        return $token;
    }

    //아임포트 현금영수증 발행
    function payment_receipts($array){
        // imp_uid	
        // 아임포트 거래 고유번호

        // identifier	
        // 현금영수증 발행대상 식별정보. 국세청현금영수증카드, 휴대폰번호, 주민등록번호, 사업자등록번호 중에 한개

        //이후 이름,메일등 추가 가능 

        $imp_uid = $array["imp_uid"];
        $identifier = $array["identifier"];

        $token = $this->get_token();
        $header_data = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );


        $url = "https://api.iamport.kr/receipts/";
        $json_data = '{';
        $json_data = $json_data.'"imp_uid":"'.$imp_uid.'", ';
        $json_data = $json_data.'"identifier":"'.$identifier.'" ';
        $json_data = $json_data.'}';

        $output = $this->curlSet($url,$header_data,$json_data);
        $result = json_decode($output,true);
        return $result;
    }

    //아임포트 환불
    function payment_refund($uid, $merchant_uid, $reason){
        // echo $uid;
        // echo $merchant_uid; 

        $token = $this->get_token();
        $header_data = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $reason = $reason;

        $url = "https://api.iamport.kr/payments/cancel";
        $json_data = '{';
        $json_data = $json_data.'"imp_uid":"'.$uid.'", ';
        $json_data = $json_data.'"reason":"'.$reason.'", ';
        $json_data = $json_data.'"merchant_uid":"'.$merchant_uid.'" ';
        $json_data = $json_data.'}';
        
        // $url = "https://api.iamport.kr/payments/cancel";
        // $json_data = '{';
        // $json_data = $json_data.'"imp_uid":"112321321312312312312312312312", ';
        // $json_data = $json_data.'"reason":"zczczxczczczczczxczc", ';
        // $json_data = $json_data.'"merchant_uid":"cxzxczxczxczczxczcczxczczcsaffds" ';
        // $json_data = $json_data.'}';
        // echo $json_data;

        $output = $this->curlSet($url,$header_data,$json_data);
        $result = json_decode($output,true);
        return $result;
    }

    //일부 금액 환불
    function payment_part_refund($uid, $merchant_uid, $reason, $refund_amount){
        
        $token = $this->get_token();
        $header_data = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $reason = $reason;

        $url = "https://api.iamport.kr/payments/cancel";
        $json_data = '{';
        $json_data = $json_data.'"imp_uid":"'.$uid.'", ';
        $json_data = $json_data.'"reason":"'.$reason.'", ';
        $json_data = $json_data.'"merchant_uid":"'.$merchant_uid.'", ';
        $json_data = $json_data.'"amount":"'.$refund_amount.'" ';
        $json_data = $json_data.'}';


        $output = $this->curlSet($url,$header_data,$json_data);
        $result = json_decode($output, true);
        return $result;
    }

    //아임포트에 imp_uid로 결제정보 확인
    function payment_check($uuid){

        $token = $this->get_token();
        $header_data = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $url = "https://api.iamport.kr/payments/".$uuid;

        $json_data = '{';
        $json_data = $json_data.'"imp_uid":"'.$uuid.'" ';
        $json_data = $json_data.'}';
        
        $output = $this->curlSet($url,$header_data,null);
        $result = json_decode($output,true);
        
        return $result;
    }

    //아임포트에 전체 결제정보 확인
    //최대 100개까지됨
    function all_payment_check($imp_uid_array){
        // print_r($imp_uid_array);

        $token = $this->get_token();
        $header_data = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token
        );

        $url = "https://api.iamport.kr/payments?";
        // $url = "https://api.iamport.kr/payments?imp_uid[]=". $imp_uid_array[0];
        $url_flag = 0;

        foreach($imp_uid_array as $key => $value){
            if($url_flag == 0){
                $url = $url . "imp_uid[]=".$value;
                $url_flag = 1;
            }else{
                $url = $url . "&imp_uid[]=".$value;
            }
            
        }
        
        // print_r($url);
        
        $output = $this->curlSet($url,$header_data,null);
        $result = json_decode($output,true);
        return $result;
    }


    function curlSet($url,$header_data,$json_data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header_data);
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  // 이건 아래 옵션 때문에 필요 없긴 하다.
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//로컬호스트에서 동작 시키려고 코드추가
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//로컬호스트에서 동작 시키려고 코드추가
        
        $output = curl_exec($ch);
        curl_close ($ch);
        return $output;
    }
}
?>