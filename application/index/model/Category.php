<?php

namespace app\index\model;
use think\Model;

/**
 * 
 */
class Category extends Model
{
	protected $table = "dn_category";
	
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

	public function product() {
		return $this->belongsTo('product','category_id','id')->field('title','detail','description','intro','img_url');
	}

}