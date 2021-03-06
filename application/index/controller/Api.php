<?php
namespace app\index\controller;

use \think\Controller;
use \think\View;
use \think\Request;
use PHPMailer\PHPMailer;
use PHPMailer\Exception;
use PHPMailer\SMTP;
use app\index\model\Dict;

use app\index\model\Contact;

class Api extends Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function saveContact() {
		$request = Request::instance();
        $post = $request->post();

        $data = [
            'name' => $post ['name'],
            'contact' => $post ['contact'],
            'information' => $post ['information']
        ];

        $cantact = new Contact ;
        $cantact->data($data) ;
        $cantact->save() ;

        $content = "称呼： " . $post ['name'] . "<br />";
        $content .= "联系方式: " . $post ['contact'] . "<br />";
        $content .= "信息内容： " . $post ['information'] . "<br />";
        $this->_send_email ( $content );

        return output_json ( true , "OK" , null ) ;
	}

    private function _send_email( $content ) {
        $dict = Dict::where('model' , 'email')->select();
        $dict = array_column($dict, 'value' , 'name');
        $fields = array("email_smtp","email_address","email_secure","email_port") ;
        $keys = array_keys ( $dict ) ;
        $diff = array_diff( $fields, $keys);
        if( $diff ) {
            return true;
        }
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP( true );                                            // Send using SMTP
            $mail->Host       = $dict ['email_smtp'] ;//                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $dict ['email_address'] ;                    // SMTP username
            $mail->Password   = $dict ['email_secure'] ;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      
            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = $dict ['email_port'] ;                      // TCP port to connect to
            $mail->SMTPDebug = 0;
            //Recipients
            $mail->setFrom($dict ['email_address'], 'DN');
            $mail->addAddress($dict ['email_address'], 'DN');     // Add a recipient

            // Content
            $mail->Subject = 'WEBSITE INFORMATION';
            //$mail->Body    = $content;
            $mail->msgHTML($content);

            $mail->send();
        } catch (Exception $e) {
            
        }
    }
}