<?php
	class fcm {
		private $project;
		//$project : send에 있음 생성할때 프로젝트 이름으로 server key 구분
		function __construct($project){
			$this->project = $project;
		}
		//list 는 토큰 값 fcm 이라는 매개변수로 전달
		//msg {"title"=>"title", "message"=>"message", "havior"=>"notice"}
		//setting : sound : {'default'}
		function send_fcm ($list, $msg, $setting){
			$notification = $msg;
			
			foreach($setting as $key => $value){
				$notification[$key]=$value;
			}
			$notification["data"]=$msg;
			if($list){
				$tokens = array();
				for($i=0;$i<count($list);$i++){
					$fcm = $list[$i]["fcm"];
					if($fcm!=""){
						array_push($tokens,$fcm);
					}
					if(($i/499)==1){
						$fields = array(
							'registration_ids' => $tokens,
							'data' => $notification,
							'notification'=>$notification,
						);
						$this->send($fields);
						$tokens = array();
					}

					if((count($list)-1)==$i){
						if(count($tokens)>0){
							$fields = array(
								'registration_ids' => $tokens,
								'data' => $notification,
								'notification'=>$notification,
							);
							$this->send($fields);
							$tokens = array();
						}
					}
				}
			}else{
				return false;
			}
			
		}

		function send($fields){
			$url = 'https://fcm.googleapis.com/fcm/send';

			$headers=null;
			if($this->project=="PAPRIKA"){
				$headers = array(
					'Authorization:key =AAAAt6jftmY:APA91bEbRLSul2qrl4tR4JjFt4FR97417VHG86W1x24CETxNF7KlAGQETzIJGpt7ZW68c2b3yiUN6aONj6OBonvpdEbU3gxc6z6NkRwr9G0yS0wcnNdO3RdwoVvELQtFh543lgyVuygl' ,
					'Content-Type: application/json');
			}else if($this->project=="BANCHAN_PREMIUM"){
				$headers = array(
					'Authorization:key =AAAACdQ3ORg:APA91bEKZbL4WDOsYskEEASw9qXdl5rUb4Mtb3zbP-TFdelMXxow_jy6fhG267xuPNAFBS2wOUbp_89y7RdGbpZcaAFj_ZrOiOkB5rkcvFh_k5-HGuEshF0hwDsScU4soS2r_rkEfXr6' ,
					'Content-Type: application/json');
				// 윤철이 테스트 코드
				// $headers = array(
				// 	'Authorization:key =AIzaSyC2G0f5f1z8oXZZduHGssFZl8x-A6lXHY4' ,
				// 	'Content-Type: application/json');
			}else if($this->project=="BANCHAN"){
				$headers = array(
					'Authorization:key =AAAACLg7Dxk:APA91bFy1HZVWY-FUienoPvUSOiKqEEzlwv8CRt7a-636fDNQ55aiqRfD0dEpdTypSc46FX7c7iHhHX6_NCjvGtrz3ZbWNzIgWaW4RwXgxT721C8h2RaqPHW-czYbe8nRGrobcMGKEiY' ,
					'Content-Type: application/json');
			}else if($this->project=="BANCHAN_CHORYANG"){
				$headers = array(
					'Authorization:key =AAAAobwNfD8:APA91bFO9ERegRzlU_n5YBwajntfyic8_Laux7YYZUGoHNsjKOLScJ4xWmbJH1JvKrPL8hJVF5i24xlj_p7jb-Uto2GvGXsmo6sCF9Ku4jz4JjgzQW7zICsSiMu7weft_b11PVebApEm' ,
					'Content-Type: application/json');
			}

	

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);
			if ($result === FALSE) {
				die('Curl failed: ' . curl_error($ch));
			}
			
			if ($result === FALSE) {
				return false;
			}else{
				curl_close($ch);
				return true;
			}
		}
	}
?>