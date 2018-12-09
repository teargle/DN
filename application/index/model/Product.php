<?php

namespace app\index\model;
use think\Model;

/**
 * 
 */
class Product extends Model
{
	protected $table = "dn_product";
	
	function __construct()
	{
		# code...
	}

	function get_product_with_category ($id) {
		return $this->join("dn_category" , "dn_category.id = dn_product.category_id")
					->field('dn_product.*,dn_category.title category_title')
					->where(["dn_product.id" => $id])
					->find();
	}

	function query_product_with_category( $where , $orderby = null , $start = null , $limit = null ) {
		return	$this->where($where)
					->join("dn_category" , "dn_category.id = dn_product.category_id")
					->field('dn_product.*,dn_category.title category_title')
                    ->limit($start , $limit)
                    ->select();
	}

	function count_product_with_category( $where ) {
		return $this->join("dn_category" , "dn_category.id = dn_product.category_id")
					->where($where)
                    ->count();
	}

}