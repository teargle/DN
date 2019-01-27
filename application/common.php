<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function is_mobile_browser() {
	if (isset ( $_SERVER ['HTTP_USER_AGENT'] ) && 
			preg_match ( '/(Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini)/i', 
				$_SERVER ['HTTP_USER_AGENT'] )) {
		return true;
	}
	return false;
}

function output_json($success = true , $message = '' , $obj = array() ) {
	$data = [
		'result' => $success,
		'message' => $message,
		'obj' => $obj
	] ;
	return json_encode($data) ;
}

// 配置
define("SHOP_LIMIT" , 4);

//语言加载
include "language_zh.php" ;