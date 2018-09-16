<?php
namespace app\index\controller;

use \think\Controller;
use \think\View;
use \think\Log;

class Index extends Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->init();
	}

	/**
	 *  初始化页面基本属性
	 */
	public function init () 
	{
		View::share('title','欢迎来到岱恩');
	}

    public function index()
    {
        return $this->fetch('index');
    }

    public function shop() {
    	return $this->fetch('shop');
    }

    public function product () {
    	return $this->fetch('product');
    }

}