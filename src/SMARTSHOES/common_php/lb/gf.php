<?php
if(PHP_VERSION_ID >= 80200){
	#[\AllowDynamicProperties]
	class gf{		
		function jsonDecode($obj){
			$json = json_decode($obj,true);
			return $json;
		}

		function sha_password($pw){
			return '*'.strtoupper(sha1(sha1($pw, true)));
		}

		function null_check($str){
			$result_data;
			if(isset($str)){
				if($str == ""){
					$result_data = "NULL";
				}else if($str == "null"){
					$result_data = "NULL";
				}else {
					$result_data = "'".addslashes($str)."'";
				}
			}else{
				$result_data = "NULL";
			}
			
			return $result_data;
		}

		function nullCheck($array,$json){
			$str = $json;
			foreach($json as $key => $value) {
				for($i=0;$i<count($array);$i++){
					if($array[$i]==$key){
						$str[$key] = $this->null_check($value);
					}
				}
			}
			return $str;
		}

		//null_checkv2 이후에는 이것사용 
		function null_check_v2($str){
			$result_data;
			If($str == ""){
				$result_data = "NULL";
			}Else If($str == "null"){
				$result_data = "NULL";
			}Else {
				if(is_numeric($str)){
					$result_data = $str;
				}else{
					$type = gettype($str);
					if($type=="string"){
						$result_data = "'".$str."'";
					}else if($type=="array"){
						//배열도 두종류가 있다.
						if(isset($str[0])){
							for($i=0;$i<count($str);$i++){
								$str[$i] = $this->null_check_v2($str[$i]);
							}
						}else{
							foreach($str as $key => $value) {
								$str[$key] = $this->null_check_v2($value);
							}
						}
						$result_data = $str;
					}
				}
			}
			return $result_data;
		}

		function null_v2($json){
			foreach($json as $key => $value) {
				$json[$key] = $this->null_check_v2($value);
			}
			return $json;
		}

		function currentDateCompare($targetDate){
			$timenow = date("Y-m-d H:i:s"); 
			$timetarget = $targetDate;
			$str_now = strtotime($timenow);
			$str_target = strtotime($timetarget);
			
			if($str_now < $str_target) {
				return true;
			}else{
				return false;
			}
		}

		function fetchList($list){
			if($list){
				while($row[] = $list->fetch_assoc());
				return array_filter($row);
			}else{
				return $list;
			}
		}

		function setCookies($name,$time,$array){
			//86400 * 30 한달
			setcookie($name, json_encode($array), time() + $time);
		}

		function setCookiesStr($name,$time,$str){
			setcookie($name, $str, time() + $time);
		}

		function delCookies($name){
			setcookie($name, '', time() - 1);
		}

		function getCookies($name){
			$data = null;
			if(isset($_COOKIE[$name])){
				$data = json_decode($_COOKIE[$name],true);
			}else{
				$data = null;
			}

			return $data;
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

		function start_session(){
			if (!isset($_SESSION)) {
				session_cache_expire(999); //세션이 유지될 시간(분)
				session_start();
			}
		}

		function setSession($array,$name){
			$this->start_session();
			$_SESSION[$name] = $array;
		}

		function getSession($name){
			$this->start_session();
			if(isset($_SESSION[$name])){
				return $_SESSION[$name];
			}else{
				return "";
			}
		}

		function delSession($name){
			if(isset($_SESSION[$name])){
				$_SESSION[$name] = null;
			}
		}

		function timeSplit($regdate,$type){
			//$type Y-M-D 
			$timestamp = strtotime($regdate);
			return date($type,$timestamp);
		}

		function ymd($str){
			$temp = strtotime($str);
			return date("Y-m-d", $temp);
		}

		
		//최진혁 MAIL CURL
		function mail_curlSet($url, $header_data, $json_data){
			$ch = curl_init($url);//필수로 curl 초기화

			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($json_data));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  // 이건 아래 옵션 때문에 필요 없긴 하다.
			curl_setopt($ch, CURLOPT_POST, 1);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//로컬호스트에서 동작 시키려고 코드추가
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//로컬호스트에서 동작 시키려고 코드추가
			
			$output = curl_exec($ch);
			curl_close ($ch);
			// print_r($json_data);
			return $output;
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

		function curl_get_url($url,$header_data){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header_data);
			curl_setopt($ch, CURLOPT_URL, $url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				
			$response = curl_exec($ch);
			
			curl_close($ch);
			
			return $response;
		}

		function null_check_v3($param,$null_check_array){
			foreach ($param as $key => $value) {
				for($i=0;$i<count($null_check_array);$i++){
					if($key==$null_check_array[$i]){
						$param[$key]=$this->null_check($value);
					}
				}
			}

			return $param;
		}

		function rand_name(){
			$someTime=((strtotime(date("Y-m-d H:i:s",time()))-(9*60*60))-(strtotime("1971-1-1 00:00:00 GMT"))).mt_rand(1, 100000);
			return $someTime;
		}

		function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ){
			$datetime1 = date_create($date_1);
			$datetime2 = date_create($date_2);
			
			$interval = date_diff($datetime1, $datetime2);
			
			return $interval->format($differenceFormat);
			
		}

		function br2nl($string){
			return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
		}

		function MobileCheck() { 
			$useragent=$_SERVER['HTTP_USER_AGENT'];

			if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
				return "mobile";
			}else{
				return "pc";
			}
		}

		function param_url($array,$params){
			$url = null;
			$json_array = array();
			for($i=0;$i<count($array);$i++){
				if(isset($params[$array[$i]])){
					$json_array[$array[$i]] = $params[$array[$i]];
				}
			}
			foreach($json_array as $key => $value){
				$url = $url."&".$key."=".$value;
			}

			return $url;
		}

		function column_check($orign_param,$column){
            $flag = true;
            for($i=0;$i<count($column);$i++){
                if($flag==true){
                    if(isset($orign_param[$column[$i]])){
                        
                    }else{
                        $flag = false;
                        $this->result["result"]="0";
                        $this->result["error_code"]="2";
                        $this->result["message"]="파라미터가 없습니다.";
                        $this->result["value"] = $column[$i];
						echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
                    }
                }
            }
            return $flag;
        }
		function jsonEncode($json){
			return json_encode($json,JSON_UNESCAPED_UNICODE);
		}

		//error_code 100:파일 없음, 101:일부전송, 102:확장자 틀림, 103:파일사이즈 초과
		function file_check($json){
			$flag = true;
			$error_code = null;
			$extensiton_array = $json["extension"];
			$extension_flag = false;//파일 확장자 구분
			$file_size = $json["size"];
			$file_flag = true;

			//$_FILES['files']
			if(isset($json["files"])){
				foreach ($json["files"]['name'] as $f => $name) {
					//script에서 파일이 없을경우 추가 해서 그냥 보낼수도 있기때문에 파일 없음을 체크 하지 않는다.
					// if ($json["files"]['error'][$f] == 4) {
					// 	$flag = false;
					// 	$error_code = "0";
					// 	break;
					// }

					if ($json["files"]['error'][$f] == 4) {
						
					}else if($json["files"]['error'][$f] == 3){
						$flag = false;
						$error_code = "101";//파일 일부전송
						break;
					}else{
						$info = new SplFileInfo($name);//확장자관련
						$extension = $info->getExtension();//확장자관련
						for($i=0;$i<count($extensiton_array);$i++){
							if($extension==$extensiton_array[$i]){
								$extension_flag=true;
							}
						}

						if($extension_flag==false){//확장자 틀림 
							$flag=false;
							$error_code = "102";
							break;
						}

						if($file_size < $json["files"]["size"][$f]){//파일사이즈초과
							$flag=false;
							$error_code="103";
							break;
						}
					}
				}
			}else{
				$error_code="100";//실제 파일 매개변수가 넘어오지 않았을 경우
			}

			$result = array(
				"error_code"=>$error_code,
				"files"=>$json["files"],
				"param"=>$json["param"]
			);
			$json["havior"]($result);
		}

		function file_check_v2($json){
			$flag = true;
			$error_code = null;
			if(isset($json["extension"])){
				$extensiton_array = $json["extension"];
			}else{
				$extensiton_array = array();
			}
			$extension_flag = false;//파일 확장자 구분
			$file_size = $json["size"];
			$file_flag = true;

			//$_FILES['files']
			if(isset($json["files"])){
				foreach ($json["files"]['name'] as $f => $name) {
					//script에서 파일이 없을경우 추가 해서 그냥 보낼수도 있기때문에 파일 없음을 체크 하지 않는다.
					// if ($json["files"]['error'][$f] == 4) {
					// 	$flag = false;
					// 	$error_code = "0";
					// 	break;
					// }
					if ($json["files"]['error'][$f] == 4) {
						$error_code = "304";//파일이 없을 때
					}else if($json["files"]['error'][$f] == 3){
						$flag = false;
						$error_code = "301";//파일 일부전송
						break;
					}else{
						if(isset($json["extension"])){
							$info = new SplFileInfo($name);//확장자관련
							$extension = $info->getExtension();//확장자관련
							if(count($extensiton_array)>0){
								for($i=0;$i<count($extensiton_array);$i++){
									if($extension==$extensiton_array[$i]){
										$extension_flag=true;
									}
								}
								if($extension_flag==false){//확장자 틀림 
									$flag=false;
									$error_code = "302";
									break;
								}
							}
						}

						if($file_size < $json["files"]["size"][$f]){//파일사이즈초과
							$flag=false;
							$error_code="303";
							break;
						}
					}
				}
			}else{
				$error_code="300";//실제 파일 매개변수가 넘어오지 않았을 경우
			}

			$result = array(
				"error_code"=>$error_code,
				"files"=>$json["files"],
				// "idx"=>$json["idx"],
			);
			$json["havior"]($result);
		}

		function file_set($json){
			$file_name_array = array();
			
			if($json["type"]=="upload"){
				foreach ($json['param']["files"]['name'] as $f => $name) {
					if($json['param']['files']['error'][$f] == 4) {//파일이 없을 경우
						
					}else{
						//파일 이름을 랜덤하게 생성 한다.
						$info = new SplFileInfo($name);
						$file_name = $this->rand_name().".".$info->getExtension();
						move_uploaded_file($json["dir"].$file_name,$json['param']['files']["tmp_name"][$f]);
						array_push($file_name_array,$file_name);
					}
				}

				$array_result = array(
					"file_name"=>$file_name_array,
					"param"=>$json["param"]
				);
			}elseif($json["type"]=="delete"){
				$result = $json["s3"]->delFile($json["dir"].$json["file_name"]);
				$array_result = array(
					"result" => $result
				);
			}

			

			$json["havior"]($array_result);
		}

		function file_set_v2($json){
			
			$error_code = "";
			$file_name_array = array();
			if($json["type"]=="upload"){
				foreach ($json["files"]['name'] as $f => $name) {
					if($json['files']['error'][$f] == 4) {//파일이 없을 경우
						
					}else{

						// //파일 이름을 랜덤하게 생성 한다.
						$info = new SplFileInfo($name);
						$file_name = $this->rand_name().".".$info->getExtension(); //제공하는 함수 확장자(램던.확장자)
						
						//파일 이름 중복확인
						$exist_check = file_exists($json["init_dir"].$json["dir"].$file_name); //이은영 수정:파일이 존재하는지 확인
						// $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
						while($exist_check == 1){ //같은 파일이 존재한다면 존재하지 않을 때까지 랜덤값을 파일이름으로 생성
							$file_name = $this->rand_name().".".$info->getExtension();
							$exist_check = file_exists($json["init_dir"].$json["dir"].$file_name); //이은영 수정:파일이 존재하는지 확인
							// $exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
						}

						// print_r($json["files_tmp_name"][$f]);
						print_r($json["init_dir"].$json["dir"]);
						$result = move_uploaded_file($json["files_tmp_name"][$f], $json["init_dir"].$json["dir"].$file_name); //이은영 수정:파일저장하는 부분
						// // $result = $json["s3"]->insertFile($json["dir"].$file_name,$json['files']["tmp_name"][$f]);
						if($result != "1"){//error 이은영 수정:변수값이 1이 아니면 실패
							$error_code = $result;
						}else{
							array_push($file_name_array,$file_name);
						}
					}
				}

				$array_result = array(
					"file_name"=>$file_name_array,
					"init_dir"=>$json["init_dir"],
					// "s3"=>$json["s3"],
					"error_code"=>$error_code
				);
			}
		}

		//파일 압축 같이 진행 zip_dir
		function file_set_v3($json){
			$error_code = "";
			$file_name_array = array();
			if($json["type"]=="upload"){
				foreach ($json["files"]['name'] as $f => $name) {
					if($json['files']['error'][$f] == 4) {//파일이 없을 경우
						
					}else{
						//파일 이름을 랜덤하게 생성 한다.
						$info = new SplFileInfo($name);
						$file_name = $this->rand_name().".".$info->getExtension();
						
						//파일 이름 중복확인
						$exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
						while($exist_check == 1){
							$file_name = $this->rand_name().".".$info->getExtension();
							$exist_check = $json["s3"]->fileExists($json["dir"].$file_name);
						}

						//해당 부분에서 파일 압축 함께 진행 origin 넣고 sub 넣는다. 이름은 같게

						$result = $json["s3"]->insertFile($json["dir"].$file_name,$json['files']["tmp_name"][$f]);
						
						if(isset($json["zip_dir"])){
							//이미지파일 용량 줄이기
							$file_size = filesize($json['files']["tmp_name"][$f]);
							$percent = "0.2";
							if($file_size < 2097152){
								$percent = "0.9";
							}elseif(2097152 <= $file_size &&  $file_size < 4194304){
								$percent = "0.85";
							}elseif( 4194304 <= $file_size &&  $file_size < 6291456){
								$percent = "0.8";
							}elseif( 6291456 <= $file_size &&  $file_size < 8388608){
								$percent = "0.75";
							}elseif( 8388608 <= $file_size &&  $file_size < 10485760){
								$percent = "0.7";
							}elseif( 10485760 <= $file_size &&  $file_size < 12582912){
								$percent = "0.65";
							}elseif( 12582912 <= $file_size &&  $file_size < 14680064){
								$percent = "0.6";
							}elseif( 14680064 <= $file_size &&  $file_size < 16777216){
								$percent = "0.55";
							}elseif( 14680064 <= $file_size &&  $file_size < 16777216){
								$percent = "0.55";
							}elseif( 16777216 <= $file_size &&  $file_size < 18874368){
								$percent = "0.5";
							}elseif( 18874368 <= $file_size &&  $file_size < 20971520){
								$percent = "0.45";
							}elseif( 20971520 <= $file_size &&  $file_size < 23068672){
								$percent = "0.4";
							}elseif( 23068672 <= $file_size &&  $file_size < 25165824){
								$percent = "0.4";
							}elseif( 25165824 <= $file_size &&  $file_size < 27262976){
								$percent = "0.35";
							}elseif( 27262976 <= $file_size &&  $file_size < 29360128){
								$percent = "0.3";
							}

							$create_file = $this->img_resize(array(
								"percent"=>$percent,
								"file_name"=>$file_name,
								"file"=>$json['files']["tmp_name"][$f],
								"file_size"=>102400
							));

							$result = $json["s3"]->insertFile($json["zip_dir"].$file_name,$create_file);

							unlink($create_file);
						}
						
						if($result){//error
							$error_code = $result;
						}else{
							array_push($file_name_array,$file_name);
						}

						// $size_info = getimgagesize($json["files"]["tmp_name"][$f]);
						// $file_o_width = $size_info[0];
						// $file_o_height = $size_info[1];
					}
				}

				$array_result = array(
					"file_name"=>$file_name_array,
					"s3"=>$json["s3"],
					"error_code"=>$error_code
				);
			}

			return $array_result;
		}

		function sql_in($array){
			$array = array_values($array);
			if(count($array)>0){
				$tag_str = "";
				for($i=0;$i<count($array);$i++){
					if($i==0){
						$tag_str = "'".$array[$i]."'";
					}else{
						$tag_str = $tag_str.","."'".$array[$i]."'";
					}
				}

				return $tag_str;
			}else{
				return false;
			}
		}

		function param_empty_check($object){
			$param = $object["param"];
			$array = $object["array"];

			$result = true;
			
			for($i=0;$i<count($array);$i++){
				if(!isset($param[$array[$i]])){
					$result = false;
					break;
				}
			}

			return $result;
		}

		//필수 파라미터 체크
		function empty_check($object){
			$param = $object["param"];
			$array = $object["array"];
			
			$return_array = array(
				"result"=>true, //javascript에서 ajax로 필수값을 넘기지 않았을 때, flase값 반환
				"value"=>null, //javascript에서 ajax로 넘기지 않는 필수값 확인가능
				"value_empty"=>false, //사용자가 빈값을 넘기는 경우 true
				"value_key"=>null //사용자가 넘긴 빈값이 무엇인지 확인가능
			);

			for($i=0;$i<count($array);$i++){
				if(!isset($param[$array[$i]])){
					$return_array["result"] = false;
					$return_array["value"] = $array[$i];
					break;
				}else{//필수 파라미터가 존재 하는경우
					$value = trim($param[$array[$i]]);
					if($value==""){
						$return_array["value_empty"]=true;
						$return_array["value_key"]=$array[$i];
						break;
					}
				}
			}

			return $return_array;
		}

		function img_resize($object){
			$percent = $object["percent"];

			$file_size = filesize($object["file"]);
			$o_file = $object["file"];//원본 파일 이된다.
			$o_target_file = "temp/".$object["file_name"];
			$info = new SplFileInfo($object["file_name"]);
			//원본이미지 저장
			move_uploaded_file($object["file"], $o_target_file);
			
			if($file_size>$object["file_size"]){
				// Get new dimensions
				list($width, $height) = getimagesize($o_target_file);
				$new_width = ($width * $percent);
				$new_height = ($height * $percent);

				// Resample
				$image_p = imagecreatetruecolor($new_width, $new_height);            

				switch ($info->getExtension()) {
					case 'jpg':
						$image = imagecreatefromjpeg($o_target_file);
						break;
					case 'jpeg':
						$image = imagecreatefromjpeg($o_target_file);
						break;
					case 'png':
						$image = imagecreatefrompng($o_target_file);
						break;
					case 'gif':
						$image = imagecreatefromgif($o_target_file);
						break;
					default:
						$image = imagecreatefromjpeg($o_target_file);
				}

				imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				$target_file="temp/".$object["file_name"];
				//unlink($o_target_file);
				imagejpeg($image_p, $target_file);
				return $target_file;
			}else{
				return $o_target_file;
			}
		}
	}
}else{
	print_r("php version is not 8.2.0");
}
?>