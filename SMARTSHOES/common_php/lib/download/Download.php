<?php
class Download
{
    //type : 0->로컬, 1->외부지 , url : 파일이 업로드 되어있는 경로, realname : 실제 파일명, dir : 루트 디렉토리
    function __construct($type, $url, $realname, $dir)
    {   
        //로컬에 업로드된 파일을 다운로드 할때
        if ($type == 0) {
            $file = $url;
            $file_name = $realname;

            // echo $file;
            // print_r('file_url:'.$url.'filename:'.$file_name);
            // $file = 'D:/github/lbcontents/html/homepage2/IDF/img/area_color_icon.svg'; // 파일의 전체 경로
            // $file_name = 'file.svg'; // 저장될 파일 이름

            header('Content-type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            // header('Content-Disposition: attachment; filename="test.xlsx"');
            header('Content-Transfer-Encoding: binary');
            header('Content-length: ' . filesize($file));
            // header('Content-length: ' . filesize("D:/github/lbcontents/donga_tg/EXAMPLEBOARD/uploads/15293428743293.png"));

            header('Expires: 0');
            header("Pragma: public");
            // header("Pragma: no-cache");

            $fp = fopen($file, 'rb');
            fpassthru($fp);
            fclose($fp);
        }
        //외부지에 업로드되어있는 파일을 다운로드 할때
        //외부지에 업로드외어있는 파일을 바로 읽어 오지 않고 내부로 다운로드 받았다가 다시 읽어들임
        //이미지가 있는 내부 경로를 알수 없음
        //단점 -> 속도가 느림 Server(storge) -> Server(local) ->client 로 전송되기에 속도가 느림 
        //참고 url : http://trudy.kr/58
        //Snoopy.class.php 필요
        elseif ($type == 1) {
            $include_dir = $dir;
            $tmp_name = $this->get_random_name(10);
            // $file_dir = $dir . "uploads/";
            $file_dir = "tmp/";
            include_once($include_dir . "Snoopy.class.php");

            // date_default_timezone_set('Asia/Seoul');
            // $timestamp = time(); // 파일을 임시적으로 작성하기에 timestamp값을 이용함

            $remotely = $url;  // server(storge)의 이미지 주소

            if(!is_dir($file_dir)){
                mkdir($file_dir);
            }

            $location = $file_dir . $this->formatSizeUnits(5) . $realname;  // server(local)의 저장될 이미지 주소
            
            $snoopy = new Snoopy;
            $snoopy->fetch($remotely);

            // 파일을 local에 저장 함
            $temp = fopen($location, 'w');
            fwrite($temp, $snoopy->results);
            fclose($temp);
            // local에 파일이 존재 유무 체크
            // if (file_exists($location)) {
            //     $fsize = filesize($location);   // 다운로드로 사용할 경우를 대비한 파일 크기
                // $path_parts = pathinfo($location);  // 경로 정보
                // $ext = strtolower($path_parts["extension"]);  // 확장자 정보
                // // Determine Content Type 
                // switch ($ext) {
                //     case "pdf":
                //         $ctype = "application/pdf";
                //         $cdispostion = true;
                //         break;
                //     case "exe":
                //         $ctype = "application/octet-stream";
                //         $cdispostion = true;
                //         break;
                //     case "zip":
                //         $ctype = "application/zip";
                //         $cdispostion = true;
                //         break;
                //     case "doc":
                //         $ctype = "application/msword";
                //         $cdispostion = true;
                //         break;
                //     case "xls":
                //     case "xlsx":
                //         $ctype = "application/vnd.ms-excel";
                //         $cdispostion = true;
                //         break;
                //     case "ppt":
                //         $ctype = "application/vnd.ms-powerpoint";
                //         $cdispostion = true;
                //         break;
                //     case "gif":
                //         $ctype = "image/gif";
                //         $cdispostion = true;
                //         break;
                //     case "png":
                //         $ctype = "image/png";
                //         $cdispostion = true;
                //         break;
                //     case "jpeg":
                //     case "jpg":
                //         $ctype = "image/jpg";
                //         $cdispostion = true;
                //         break;
                //     default:
                //         $ctype = "application/force-download";
                //         $cdispostion = true;
                // }



            //     //익스플로러 안글깨짐 코드
            //     // $donwload_file_name = basename($location);
            //     $donwload_file_name = preg_replace( '/^.+[\\\\\\/]/', '', $location);
                
                
            //     $broswerList = array('Edge', 'MSIE', 'Chrome', 'Firefox', 'iPhone', 'iPad', 'Android', 'PPC', 'Safari', 'Trident', 'none');
            //     $browserName = 'none';

                
            //     foreach ($broswerList as $userBrowser){
            //         if($userBrowser === 'none') break;
            //         if(strpos($_SERVER['HTTP_USER_AGENT'], $userBrowser)) {
            //             $browserName = $userBrowser;
            //             break;
            //         }
            //     }
            //     if($browserName === 'MSIE'||$browserName === 'Trident'||$browserName === 'Edge'){ //익스플로러,엣지일때 파일이름을 EUC_KR로 변경해야 한글이 안깨짐
            //         $donwload_file_name = iconv('utf-8', 'euc-kr', $donwload_file_name);    
            //     }
            //     //익스플로러 안글깨짐 코드 끝
            //     // echo $location;
            //     header("Pragma: public"); // required 
            //     header("Expires: 0");
            //     header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            //     Header("Cache-Control: private");
            //     header("Content-Type: ".$ctype);
            //     if ($cdispostion == true) {  // 다운로드로 전환할 경우에 사용함
            //         header("Content-Disposition: attachment; filename=\"" . $donwload_file_name . "\";");
            //     }
            //     header("Content-Transfer-Encoding: binary");
            //     header("Content-Length: " . $fsize);
                
            //     // ob_clean();
            //     flush();
            //     chmod($location, 0777);
            //     readfile($location);

            // } else {
            //     die('File Not Found');
            // }

            // set the download rate limit (=> 20.5 kb/s)
            $download_rate = 2000.5;
            if(file_exists($location) && is_file($location))
            {   
                //익스플로러 안글깨짐 코드
                $donwload_file_name = $realname;
                // $donwload_file_name = preg_replace( '/^.+[\\\\\\/]/', '', $location);
                
                
                $broswerList = array('Edge', 'MSIE', 'Chrome', 'Firefox', 'iPhone', 'iPad', 'Android', 'PPC', 'Safari', 'Trident', 'none');
                $browserName = 'none';
                
                foreach ($broswerList as $userBrowser){
                    if($userBrowser === 'none') break;
                    if(strpos($_SERVER['HTTP_USER_AGENT'], $userBrowser)) {
                        $browserName = $userBrowser;
                        break;
                    }
                }
                if($browserName === 'MSIE'||$browserName === 'Trident'|| $browserName === 'Edge'){ //익스플로러,엣지일때 파일이름을 EUC_KR로 변경해야 한글이 안깨짐
                    $donwload_file_name = iconv('utf-8', 'euc-kr', $donwload_file_name);    
                }
                
                //익스플로러 안글깨짐 코드 끝
                header('Cache-control: private');
                header('Content-Type: application/octet-stream');
                header('Content-Length: '.filesize($location));
                header('Content-Disposition: filename='.$donwload_file_name);

                flush();
                $file = fopen($location, "r");
                while(!feof($file)){
                    // send the current file part to the browser
                    print fread($file, round($download_rate * 1024));
                    // flush the content to the browser
                    flush();
                    // sleep one second
                    sleep(1);
                }
                fclose($file);
                // 최종적으로 local server의 파일을 지워 버림 이곳에 조건을 넣으면은 조건에 따라서 지울수 있음    
                @unlink($location);
            }else {
                die('Error: The file '.$local_file.' does not exist!');
            }
        } else {
            die('FIle Not Found');
        }
    }

    function get_random_name($length){
        $characters  = "0123456789";  
        $characters .= "abcdefghijklmnopqrstuvwxyz";  
        $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
        // $characters .= "_";  
            
        $string_generated = "";  
            
        $nmr_loops = $length;  
        while ($nmr_loops--)  
        {  
            $string_generated .= $characters[mt_rand(0, strlen($characters) - 1)];  
        }  
            
        return $string_generated; 
    }

    function formatSizeUnits($bytes){
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}

