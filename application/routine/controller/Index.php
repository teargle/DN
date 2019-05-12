<?php
namespace app\routine\controller;

use \think\Controller;
use \think\Log;
use think\Request;

use app\index\model\Dict;
use app\index\model\Contact;

class Index extends Controller
{ 

	public function __construct()
	{
		parent::__construct();
	}

	public function getBanner() {
		$dict = new Dict ;
        $banners = $dict->where('model' , 'home')
             ->where('name','like',"%banner_mobile_img%")->select();
        $banners = array_column($banners, 'value') ;
        return output_json ( true , "OK" , [
        	'banners' => $banners
        ]) ;
	}

	public function saveContact （） {
		$request = Request::instance();
        $post = $request->post();

        $data = [
            'name' => $post ['product_id'],
            'contact' => $post ['author'],
            'information' => $post ['information']
        ];

        $contact = new Contact ;
        $contact->data($data) ;
        $contact->save() ;
        return output_json ( true , "OK" , null) ;
	}
}
