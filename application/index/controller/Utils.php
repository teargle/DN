<?php

namespace app\index\controller ;

use \think\Controller;
use think\Request;
use PHPMailer\PHPMailer;
use PHPMailer\Exception;
use PHPMailer\SMTP;

/**
 * 
 */
class Utils extends Controller
{
	
	function __construct()
	{
		//
	}

	function tt() 
	{
		$request = Request::instance();
		$post = $request->post();
		print_r($request->post()) ;
		exit ;
	}

	function ta () {
		echo url('index/index/comment') ;
		exit ;
	}

	function tb () {
		$mail = new PHPMailer(true);
		try {
		    //Server settings
		    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
		    $mail->isSMTP();                                            // Send using SMTP
		    $mail->Host       = 'smtp.163.com';                    // Set the SMTP server to send through
		    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
		    $mail->Username   = 'teargle@163.com';                     // SMTP username
		    $mail->Password   = 'teargle830906';                               // SMTP password
		    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      
    		// Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
		    $mail->Port       = 465;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom('teargle@163.com', 'Mailer');
		    $mail->addAddress('teargle@163.com', 'lei.xiong');     // Add a recipient

		    // Content
		    // $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = 'Here is the subject';
		    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
		    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		    $mail->send();
		    echo 'Message has been sent';
		} catch (Exception $e) {
		    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}

	public function tc () {
		phpinfo() ;
	}

}