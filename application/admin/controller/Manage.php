<?php
namespace app\admin\controller;
use think\Controller;
use \think\View;
use \think\Log;
use think\Request;

use app\index\model\Product;
use app\index\model\Category;
use app\index\model\Intro;
use app\index\model\Dict;
use app\index\model\News;
use app\index\model\Feature;

class Manage extends Common
{

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {}

	public function index() {
		return view('admin@manage/index');
	}

	public function product() {
		$request = Request::instance();
		$where = [
			'status' => 'A',
		] ;
		$orderby = $request->get('sort') ;
		$limit = $request->get('pageSize');
		$start = $request->get('offset') ; 
		$product = new Product ;
		$products = $product->query_product_with_category( $where , $orderby , $start , $limit ) ;
		$count = $product->count_product_with_category( $where );
		$data = [
				'total' => $count , 
				'rows' =>$products
		] ;
        echo json_encode($data) ;
        exit;
	}

	public function edit_product ( $id = 0) { 
		$product = null;
		if( $id ) {
			$Product = new Product;
	        $product = $Product->get_product_with_category($id) ;
	        $product ['property'] = json_decode($product['prop'] , true) ;
	        $this->_get_homepage( $product['is_home'] );
    	} else {
    		$this->_get_homepage( $product['is_home'] );
    	}
        $this->_get_category();
        View::share('product',$product);
    	return view('admin@manage/product');
	}

	private function _get_category() {
        $categorys = Category::all() ;
        $categorys = array_combine(array_column($categorys, 'id'), $categorys);
        View::share('categorys',$categorys);
    }

    private function _get_parent_category () {
        $categorys = Category::where('parent',0 )->select();
        $cates = [ 0 => '无'] ;
        foreach( $categorys as $c ) {
            $cates[$c ['id']] = $c ['title'];
        }
        View::share('cates',$cates);
    }

    private function _get_homepage( $is_home = 0 ) {
    	$home = array(0=>"否", 1=>'是');
        View::share('home_arr',$home);
        View::share("ishome" , $is_home );
    }

    function saveProduct() {
    	$request = Request::instance();
    	$post = $request->post();
    	$data = [] ;
    	foreach( $post ['params'] as $param ) {
    		$data [$param['name']] = $param ['value'];
    	}
    	$data ['prop'] = json_encode($post['prop']) ;
    	$product = new Product;
		if(array_key_exists('id', $data)) {
			$product->save($data , ['id' => $data ['id']]);
		} else {
			$data ['status'] = 'A';
			$product->save($data);
		}
		echo $this->output_json ( true , "OK" , null) ;
    }

    private function output_json($success = true , $message = '' , $obj = array() ) {
    	$data = [
    		'result' => $success,
    		'message' => $message,
    		'obj' => $obj
    	] ;
    	return json_encode($data) ;
    }

    public function delProduct () {
    	$request = Request::instance();
    	$id = $request->param('id');
    	if( ! $id ) {
    		echo $this->output_json(false, "ERROR param" ) ;
    	}
    	$product = new Product ;
    	$product->save( ['status' => 'X'] , ['id' => $id]);
    	echo $this->output_json() ;
    }

    public function category() {
    	$request = Request::instance();
		$orderby = $request->get('sort') ;
		$limit = $request->get('pageSize');
		$start = $request->get('offset') ; 
		$category = new Category ;
		$categorys = $category->limit($start,$limit)->order($orderby)->select() ;
		$count = $category->count();;
		$data = [
				'total' => $count , 
				'rows' =>$categorys
		] ;
        echo json_encode($data) ;
        exit;
    }

    public function edit_category($id = 0) {
    	$category = null;
		if( $id ) {
			$Category = new Category;
	        $category = $Category->get($id) ;
    	}
        $this->_get_parent_category();
        View::share('category',$category);
    	return view('admin@manage/category');
    }

    function saveCategory() {
    	$request = Request::instance();
    	$post = $request->post();
    	$category = new Category;
		if(array_key_exists('id', $post)) {
			$category->save($post , ['id' => $post ['id']]);
		} else {
			$category->save($post);
		}
		echo $this->output_json ( true , "OK" , null) ;
    }

    function delCategory() {
    	$request = Request::instance();
    	$id = $request->param('id');
    	if( ! $id ) {
    		echo $this->output_json(false, "ERROR param" ) ;
    	}
    	$category = new Category;
		$category->where('id='.$id)->delete();
		echo $this->output_json ( true , "OK" , null) ;
    }

    public function test() {
    	return view('admin@manage/test');
    }

    public function intro() {
    	$request = Request::instance();
		$orderby = $request->get('sort') ;
		$limit = $request->get('pageSize');
		$start = $request->get('offset') ; 
		$intro = new Intro ;
		$intros = $intro->limit($start,$limit)->order($orderby)->select() ;
		$count = $intro->count();;
		$data = [
				'total' => $count , 
				'rows' =>$intros
		] ;
        echo json_encode($data) ;
        exit;
    }

    public function edit_intro($id = 0) {
    	$intro = null;
		if( $id ) {
			$Intro = new Intro;
	        $intro = $Intro->get($id) ;
    	}
        View::share('intro',$intro);
    	return view('admin@manage/intro');
    }

    function saveIntro() {
    	$request = Request::instance();
    	$post = $request->post();
    	$intro = new Intro;
		if(array_key_exists('id', $post)) {
			$intro->save($post , ['id' => $post ['id']]);
		} else {
			$intro->save($post);
		}
		echo $this->output_json ( true , "OK" , null) ;
    }

    public function home() {
        $dict = new Dict ;
        $home = $dict->field('name,value')->where('model' , 'home')->select() ;
        echo $this->output_json ( true , "OK" , $home) ;
        exit;
    }

    public function saveIndex() {
        $request = Request::instance();
        $post = $request->post();
        $dict = new Dict ;
        $result = true;
        foreach ($post as $key => $value) {
            if( empty( $value ) ) continue ;
            $record = $dict->get( [
                'name' => $key,
                'model' => 'home'
            ]) ;
            if( $record ) {
                if( $record ['value'] != $value ) {
                    $result = $dict->save(['value' => $value] , [
                        'id' => $record ['id']
                    ]);
                }
            } else {
                $dict = new Dict ;
                $dict->name = $key;
                $dict->value = $value;
                $dict->model = 'home';
                $result = $dict->save();
            }
        }
        if( $result ) {
            echo $this->output_json ( true , "OK" , null) ;
        } else {
            echo $this->output_json ( false , "更新失败" , null) ;
        }
    }

    public function news() {
        $request = Request::instance();
        $orderby = $request->get('sort') ;
        $limit = $request->get('pageSize');
        $start = $request->get('offset') ; 
        $news = new News ;
        $dataNews = $news->limit($start,$limit)->order($orderby)->select() ;
        $count = $news->count();;
        $data = [
                'total' => $count , 
                'rows' =>$dataNews
        ] ;
        echo json_encode($data) ;
        exit;
    }

    public function edit_news($id = 0) {
        $data = null;
        if( $id ) {
            $news = new News;
            $data = $news->get($id) ;
        }
        View::share('news',$data);
        return view('admin@manage/news');
    }

    public function setting() {
        $dict = new Dict ;
        $something = $dict->where('model' , 'setting')
             ->where('name','like',"setting_%")->select();
        $something = array_column($something, 'value' , 'name');
        echo $this->output_json ( true , "OK" , $something) ;
    }

    public function saveSetting() {
        $request = Request::instance();
        $post = $request->post();
        $dict = new Dict ;
        $result = true;
        foreach ($post as $key => $value) {
            if( empty( $value ) ) continue ;
            $record = $dict->get( [
                'name' => $key,
                'model' => 'setting'
            ]) ;
            if( $record ) {
                if( $record ['value'] != $value ) {
                    $result = $dict->save(['value' => $value] , [
                        'id' => $record ['id']
                    ]);
                }
            } else {
                $dict = new Dict ;
                $dict->name = $key;
                $dict->value = $value;
                $dict->model = 'home';
                $result = $dict->save();
            }
        }
        if( $result ) {
            echo $this->output_json ( true , "OK" , null) ;
        } else {
            echo $this->output_json ( false , "更新失败" , null) ;
        }
    }
    
    public function feature() {
        $request = Request::instance();
        $orderby = $request->get('sort') ;
        $limit = $request->get('pageSize');
        $start = $request->get('offset') ; 
        $feature = new Feature ;
        $features = $feature->limit($start,$limit)->order($orderby)->select() ;
        $count = $feature->count();
        $data = [
                'total' => $count , 
                'rows' =>$features
        ] ;
        echo json_encode($data) ;
        exit;
    }

    public function edit_feature( $id = 0) {
        $ftur = null;
        if( $id ) {
            $feature = new Feature;
            $ftur = $feature->get($id) ;
        }
        View::share('featrue',$ftur);
        return view('admin@manage/feature');
    }

    public function saveFeatures() {
        $request = Request::instance();
        $data = $request->post();
        $feature = new Feature;
        if(array_key_exists('id', $data)) {
            $feature->save($data , ['id' => $data ['id']]);
        } else {
            $feature->save($data);
        }
        echo $this->output_json ( true , "OK" , null) ;
    }
}