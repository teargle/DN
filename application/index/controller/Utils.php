<?php

namespace app\index\controller ;

use \think\Controller;
use think\Request;

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
}