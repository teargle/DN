<?php

namespace app\index\model;
use think\Model;

/**
 * 
 */
class Intro extends Model
{
	protected $table = "dn_intro";
	
	//自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    //自定义初始化
    protected static function init()
    {
        //TODO:自定义的初始化
    }

}