<?php
namespace app\index\controller;

use \think\Controller;
use \think\View;
use \think\Log;
use think\Request;
use think\Db;

use app\index\model\Product;
use app\index\model\Comment;
use app\index\model\Intro;
use app\index\model\Feature;
use app\index\model\Category;
use app\index\model\News;
use app\index\model\Dict;
use app\index\model\Viewlog;

class Index extends Controller
{
    private $lang = null;

	public function __construct()
	{
		parent::__construct();
        if( is_mobile_browser() ) {
            $request= \think\Request::instance();
            $url = $request->url();
            $i = strpos( $url , 'index' );
            $url = substr_replace  ($url , 'mobile' ,$i , strlen('index') );
            $this->redirect($url);
            exit;
        }
		$this->init();
	}

	/**
	 *  初始化页面基本属性
	 */
	public function init () 
	{
        $this->lang = get_lang();
		View::share('title',$this->lang ['web_title']);
        View::share('lang', $this->lang );
	}

    public function index()
    {
        $products = Product::all(['is_home' => 1]) ;
        $data ['products'] = $products;

        $feature = Feature::all();
        $data ['feature'] = $feature;

        $intro = Intro::all();
        $data ['intro'] = $intro;

        $news = Db::table('dn_news')->order('id', 'desc')->select();
        $data ['news'] = array_slice($news , 0 , 3);

        $this->_get_home_banner();
        $this->_get_home_something();
        $this->_get_category();
        $this->_get_setting_info();
        $this->assign('rivet' , 'home');
        $this->assign('data' , $data);
        return $this->fetch('index');
    }

    private function _get_home_banner() {
        $dict = new Dict ;
        $banner_names = "%banner_mobile_img%";
        if( !is_mobile_browser () ) {
            $banner_names = "%banner_web_img%";
        }
        $banners = $dict->where('model' , 'home')
             ->where('name','like', $banner_names)->select();
        $this->assign('banners' , $banners);

    }

    private function _get_home_something () {
        $dict = new Dict ;
        $something = $dict->where('model' , 'home')
             ->where('name','like',"st_%")->select();
        $something = array_column($something, 'value' , 'name');
        $this->assign('something' , $something);
    }

    private function _get_setting_info() {
        $something = Dict::where('model' , 'setting')
             ->where('name','like',"setting_%")->select();
        $something = array_column($something, 'value' , 'name');
        $this->assign('setting' , $something);
    }

    public function shop() {
    	$request = Request::instance();
        $get = $request->get();
        $keyword = array_key_exists('keyword', $get) ? $get ['keyword'] : $request->get('keyword');
        $current_page = isset($get ['current_page']) && $get ['current_page'] > -1 ? $get ['current_page'] : 0 ;
        $start = $current_page * SHOP_LIMIT ;
        $product = new Product ;
        $products = [] ;
        $count = 0;
        if ( isset($get ['category_id']) ) {
            $categoryIds = $this->_get_sub_category($get ['category_id']) ;
            $products = Db::table('dn_product')->alias('p')
                //->fetchSql(true)
                ->join('dn_category c','p.category_id = c.id')
                ->whereOr('p.category_id','in', implode(",", $categoryIds))
                ->limit($start, SHOP_LIMIT)
                ->column("p.id,p.title,p.short_title,p.img_url");
            $count = Db::table('dn_product')->alias('p')
                ->join('dn_category c','p.category_id = c.id')
                ->whereOr('p.category_id','in', implode(",", $categoryIds))
                ->count();
            
        } else if( $keyword ) {
            $products = Db::table('dn_product')->alias('p')
                ->join('dn_category c','p.category_id = c.id')
                ->whereOr('p.title','like',"%{$keyword}%")
                ->whereOr('p.prop','like',"%{$keyword}%")
                ->whereOr('p.short_title','like',"%{$keyword}%")
                ->whereOr('p.short_desc','like',"%{$keyword}%")
                ->whereOr('c.title','like',"%{$keyword}%")
                ->whereOr('c.detail','like',"%{$keyword}%")
                ->limit($start, SHOP_LIMIT)
                ->column("p.id,p.title,p.short_title,p.img_url");
            $count = Db::table('dn_product')->alias('p')
                ->join('dn_category c','p.category_id = c.id')
                ->whereOr('p.title','like',"%{$keyword}%")
                ->whereOr('p.prop','like',"%{$keyword}%")
                ->whereOr('p.short_title','like',"%{$keyword}%")
                ->whereOr('p.short_desc','like',"%{$keyword}%")
                ->whereOr('c.title','like',"%{$keyword}%")
                ->whereOr('c.detail','like',"%{$keyword}%")
                ->count();
        } else {
            $products = $product->limit($start , SHOP_LIMIT)->select();
            $count = $product->count();
        }

        $total_page = ceil($count / SHOP_LIMIT) ;
        $current_page = $current_page > ($total_page - 1) ? $total_page - 1  : $current_page ;
        $next_page = ( $current_page + 1 ) > $total_page - 1 ? $total_page - 1  : $current_page + 1 ;
        $pre_page = ( $current_page - 1 ) < 0 ? 0 : $current_page - 1 ;
        $this->assign('start' , $start) ;
        $this->assign('limit' , SHOP_LIMIT) ;
        $this->assign('current_page' , $current_page) ;
        $this->assign("total_page" , $total_page) ;
        $this->assign("next_page" , $next_page) ;
        $this->assign("pre_page" , $pre_page) ;
        $this->assign('topTitle' , $this->lang ['page_shop']);
        $this->assign('products' , array_values($products) ) ;
        $this->assign('keyword' , $keyword);
        $this->assign("category_id" , isset($get ['category_id']) ? $get ['category_id'] : '' ) ;
        $this->_get_category();
        $this->_get_sort();
        $this->_get_setting_info();
        return $this->fetch('shop');
    }

    public function product ($id  = 0) {
        $Product = new Product;
        $p = $Product->get( $id );
        $product = $Product->get($id)->toArray();
        $product ['property'] = json_decode($product ['prop'],true) ;
        $product ['related_products'] = json_decode(json_encode($this->_related($Product, $product ['category_id'] , 4)),true) ;
        $product ['category'] = $p->category->find()->toArray();
        $this->_save_log( 'product' , $id );
        $this->assign('product' , $product ) ;
    	$this->assign('topTitle' , 'SHOP');
        $this->_get_category();
        $this->_get_setting_info();
    	return $this->fetch('product');
    }

    public function intro($id  = 0){
        $intro = new Intro;
        $it = $intro->get($id)->toArray();
        $this->assign('intro' , $it);
    	$this->assign('topTitle' , $it ['title'] );
        $this->_get_category();
        $this->_get_setting_info();
    	return $this->fetch('intro');
    }

    public function newslist() {
        $news = Db::table('dn_news')->order('id', 'desc')->select();
        $this->assign('news' , $news);
        $this->assign('topTitle' , '新闻');
        $this->_get_category();
        $this->_get_setting_info();
        return $this->fetch('newslist');
    }

    public function news($id = 0){
        $news = News::get([$id]);
        $this->assign('news' , $news);
        $this->assign('topTitle' , $news ['title']);
        $this->_get_category();
        $this->_get_setting_info();
        return $this->fetch('news');
    }

    public function tt () {
        $a = [
            'ITEM Name' => 'Tricraft 4 inch Cut Off Wheel',
            'Size' => '4 x 3/64 x 5/8 Inch Or 105 x 1.0 x 16 mm',
            'Type' => 'Flat Type 1 or Depressed Center Type 42 or 27',
            'Power Tool' =>  'Angle Grinder',
            'MAX RPM' => '15,300',
            'Safety Standard' => 'MPA EN12413',
            'Features' => 'Fast cutting, long lasting cutting on all steel and stainless steel (INOX) .',
            'Application' => 'Metal ,Steel , Stainless Steel & Inox'
        ];
        echo json_encode($a) ;
    }

    private function _related($product, $category_id , $limit) {
        return $product->where( 
            'category_id' , $category_id
        )->limit($limit)->select() ;
    }

    public function comment() {
        $request = Request::instance();
        $post = $request->post();

        $data = [
            'product_id' => $post ['product_id'],
            'author' => $post ['author'],
            'email' => $post ['email'],
            'comment' => $post ['comment']
        ];

        $comment = new Comment ;
        $comment->data($data) ;
        $comment->save() ;

        $this->redirect("/index/index/product/id/{$post ['product_id']}");
    }

    public function category ( $id = 0){
        $category = Category::get([$id]);
        $this->assign('category' , $category);
        $this->assign('topTitle' , $category ['title']);
        $this->_get_category();
        $this->_get_setting_info();
        return $this->fetch('category');
    }

    private function _get_category() {
        $categorys = Category::all() ;
        $categorys = array_combine(array_column($categorys, 'id'), $categorys);
        $cates = array() ;
        foreach( $categorys as $idx => $cate ) {
            if( $cate ['parent'] == 0 ) {
                array_push( $cates, $cate ) ;
            }
            
        }
        foreach ($cates as &$one) {
            $two = array();
            foreach( $categorys as $i => $c ) {
                if( $c ['parent'] == $one ['id'] ) {
                    array_push($two, $c);
                }
                
            }
            if( $two ) {
                $one ['child'] = $two ;
            }
        }
        
        $this->assign('categorys' , $cates) ;
        return $cates ;
    }

    public function _get_sort() {
        $sorts = [
            'product_sort_default' => $this->lang ['product_sort_default'],
            'product_sort_popularity' => $this->lang ['product_sort_popularity'],            
            'product_sort_average_rating' => $this->lang ['product_sort_average_rating']
        ];
        View::share('sorts', $sorts );
    }

    public function contact () {
        $this->assign('topTitle' , $this->lang ['index_contact_us']);
        $this->_get_category();
        $this->_get_setting_info();
        return $this->fetch('contact');
    }

    public function _save_log ( $model, $relate_id ) {
        $view = new Viewlog ;
        $data = [
            'model' => $model,
            'relate_id' => $relate_id,
            'create_time' => date("Y-m-d H:i:s")
        ];
        $view->data ( $data ) ;
        $view->save ( ) ;

        $m = model ( 'product' ) ;
        $m->where('id', $relate_id)->setInc('pv', 1);
    }

    private function _get_sub_category ( $id ) {
        $categorys = Category::all() ;
        $result = array( $id );
        foreach ($categorys as $key => $category) {
            if(in_array($category ['parent'], $result) ){
                array_push($result, $category ['id']) ;
            }
        }

        foreach ($categorys as $key => $category) {
            if(in_array($category ['parent'], $result) ){
                array_push($result, $category ['id']) ;
            }
        }
        return $result;

    }

}