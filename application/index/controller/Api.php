<?php
namespace app\index\controller;

use \think\Controller;
use \think\View;
use \think\Request;

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
        return output_json ( true , "OK" , null ) ;
	}
}