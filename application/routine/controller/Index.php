<?php
namespace app\routine\controller;

use \think\Controller;
use \think\Log;
use think\Request;

use app\index\model\Dict;
use app\index\model\Feature;
use app\index\model\Intro;
use app\index\model\Category;
use app\index\model\Product;
use app\index\model\Contact;

define( 'MOBILE_PAGE_LIMIT' , 3);

class Index extends Controller
{ 

	public function __construct()
	{
		parent::__construct();
	}

	public function getHome() {
		$dict = new Dict() ;
        $banners = $dict->where('model' , 'home')
             ->where('name','like',"%banner_mobile_img%")->select();
        $banners = array_column($banners, 'value') ;

        $features = Feature::all();
        $intros = Intro::all();

        $cates = $this->_get_parent_category() ;

        $products = Product::all(['is_home' => 1]) ;

        $funder = $dict->where('name' , 'like','st_%')->select();
        $fd = array_combine(array_column($funder, 'name'), array_column($funder, 'value'));

        return output_json ( true , "OK" , [
        	'banners' => $banners,
            'features' => $features,
            'intros' => $intros,
            'categorys' => $cates,
            'products' => $products,
            'funder' => $fd
        ]) ;
	}

	public function saveContact() {
		$request = Request::instance();
        $post = $request->post();

        $data = [
            'name' => $post ['name'],
            'contact' => $post ['contact'],
            'information' => $post ['information']
        ];

        $contact = new Contact ;
        $contact->data($data) ;
        $contact->save() ;
        return output_json ( true , "OK" , null) ;
	}

    private function _get_parent_category() {
        $categorys = Category::all() ;
        $categorys = array_combine(array_column($categorys, 'id'), $categorys);
        $cates = array() ;
        foreach( $categorys as $idx => $cate ) {
            if( $cate ['parent'] == 0 ) {
                array_push( $cates, $cate ) ;
            }
            
        }
        
        return $cates ;
    }

    public function list() {
        $request = Request::instance();
        $get = $request->get();
        $keyword = $get ['keyword'] ;
        $offset = isset($get ['offset']) ? $get ['offset'] : 0;
        $product = new Product ;
        $products = $product->where('title','like',"%{$keyword}%")
             ->whereOr('short_title','like',"%{$keyword}%")
             ->whereOr('short_desc','like',"%{$keyword}%")
             ->limit($offset * MOBILE_PAGE_LIMIT,MOBILE_PAGE_LIMIT)
             ->select();
        $total = $product->where('title','like',"%{$keyword}%")
             ->whereOr('short_title','like',"%{$keyword}%")
             ->whereOr('short_desc','like',"%{$keyword}%")
             ->count();
        $pages = ceil ( $total / MOBILE_PAGE_LIMIT ) ;
        return output_json ( true , "OK" , [
            'products' => $products,
            'total' => $total , // 总记录
            'pages' => $pages,  // 总页数
            'offset' => $offset,  // 当前页
            'num'   => count($products),
            'limit' => MOBILE_PAGE_LIMIT
        ]) ;
    }

    public function product() {
        $request = Request::instance();
        $get = $request->get();
        $id = $get ['id'] ;
        $product = new Product ;
        $prds =$product->where('id', $id )->select();
        $prd = $prds [0] ;
        $prd ['prop'] = json_decode($prd['prop'],true);
        return output_json ( true , "OK" , $prd ) ;
    }
}
