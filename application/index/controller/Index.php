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
    	$this->assign('topTitle' , 'SHOP');
    	return $this->fetch('shop');
    }

    public function product () {
    	$this->assign('topTitle' , 'SHOP');
    	return $this->fetch('product');
    }

    public function history(){
    	$this->assign('topTitle' , 'OUT HISTORY');
    	return $this->fetch('history');
    }

    public function mission(){
        $this->assign('topTitle' , 'OUR MISSION');
        return $this->fetch('mission');
    }
}