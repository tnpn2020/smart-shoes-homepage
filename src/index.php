<?php

   error_reporting(E_ALL);
   ini_set("display_errors", 1);
   $json = array();
   $json = $_REQUEST;
   $sumnote = $json;
   $json = param_replace($json);
   $port = $_SERVER['SERVER_PORT'];
   $hostname=$_SERVER["HTTP_HOST"];

   

    include_once "SMARTSHOES/action.php";


 

 // 87���� ���� ������ �Դϴ� ��� ���� ! 
 // include_once "movemake/index.php";//�������ũ
 function param_replace($array){
     $json_array = $array;
     if(is_array($json_array) || is_object($json_array)){
         $result = array();
         foreach($json_array as $key => $value){
             $result[$key] = xss_data($value);
         }
         return $result;
     }
     return $json_array;
 }

 function xss_data($data){
     // fix&entity\n;

     // $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;&gt'), $data);
     $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u','$1;', $data);
     $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
     // $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

     // "on"�Ǵ� xmlns�� �����ϴ� ��� �Ӽ� ����
     $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

     // javascript : �� vbscript : �������� ����
     $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
     $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
     $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

     // IE������ �۵��մϴ� : <span style = "width : expression (alert ('Ping! '));"> </ span>
     $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
     $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?bsehaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
     $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

     // ���� �����̽� ��� ���� (�ʿ����� ����)
     $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
     do
     {			// ��ġ �ʴ� �±� ����
         $old_data = $data;
         $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
     }
     while ($old_data !== $data);

     return $data;
 }

?>
