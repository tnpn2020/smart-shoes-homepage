<?php
	class MailModel{
		function __construct(){

		}

		function send_mail($json){
			$Root = $_SERVER["DOCUMENT_ROOT"];
			// require($Root."/lib/PHPMailer-master/src/PHPMailer.php");
			// require($Root."/lib/PHPMailer-master/src/SMTP.php");
			include_once $Root."/common_php/lib/PHPMailer-master/src/PHPMailer.php";
			include_once $Root."/common_php/lib/PHPMailer-master/src/SMTP.php";

			$mail = new PHPMailer\PHPMailer\PHPMailer();
			$mail->IsSMTP(); // enable SMTP
			
			$body = $json["body"];
			$title = $json["title"];
			$to_list = $json["to_list"];
			$set_from = $json["set_from"];
			$from_name = $json["from_name"];
			
			
			$mail->CharSet = "UTF-8";
			$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only, 0 = No message
			$mail->SMTPAuth = true; // authentication enabled
			$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
			$mail->Host = "smtp.gmail.com";
			$mail->Port = 465; // or 587
			$mail->IsHTML(true);
			// $mail->Username = "mailer24kr@gmail.com";
			$mail->Username = "ipia.drhedison@gmail.com";  
			// $mail->Password = "uggizcxzypojibpb";
			$mail->Password = "ictxespsxczonvqj"; 
			$mail->SetFrom($set_from,$from_name);
			$mail->Subject = $title;
			$mail->Body = $body;
			
			for($i=0;$i<count($to_list);$i++){
				$mail->AddAddress($to_list[$i]);
			}
			//
			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			$result = $mail->Send();
			return $result;
			//	if(!$mail->Send()) {
			//        echo "Mailer Error: ";
			//     } else {
			//        echo "Message has been sent";
			//     }
		}
	}
?>