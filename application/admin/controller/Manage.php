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

// define("UPLOAD_IMAGE_PATH", "/home/DN/imgs/") ;
 define("UPLOAD_IMAGE_PATH", "D:/img/uploads/") ;

class Manage extends Common
{

	public function __construct()
	{
		parent::__construct();
		$this->_init();
	}

	private function _init() {
        $request = Request::instance();
        $main = $request->get('main') ;
        View::share('main',ucfirst($main));
    }

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
		$products = $product->get( $where )->order($orderby)->limit( $start, $limit )->select();
		$count = $product->get($where)->count();
        $products = collection($products)->toArray() ;
        $categorys = new Category;
        $cates = $categorys->select();
        $cates = collection($cates)->toArray() ;
        $cates = array_combine(array_column($cates, 'id'), $cates);
        
        foreach ($products as $key => &$value) {
            $value ['category_title'] = $cates [$value ['category_id']] ['title'];
            $value ['short_desc'] = trim(strip_tags($value ['short_desc']));
        }
        
		$data = [
				'total' => $count , 
				'rows' => $products,
		] ;
        echo json_encode($data) ;
        exit;
	}

	public function edit_product ( $id = 0) { 
		$product = null;
        $fcategory = $scategory = $tcategory = 0 ;
		if( $id ) {
			$Product = new Product;
	        $product = $Product->get($id) ;
	        $product ['property'] = json_decode($product['prop'] , true) ;

            // 归类
            $Category = new Category ;
            $categorys = Category::all() ;
            $categorys = array_combine(array_column($categorys, 'id'), $categorys);
            $parent_id = $product ['category_id'] ;
            if( $parent_id ) {
                $fcategory = $parent_id ;
                if( array_key_exists($parent_id, $categorys) ) {
                    $scategory = $parent_id ;
                    $fcategory = $categorys [$parent_id]['parent'] ;
                    if( array_key_exists ( $fcategory, $categorys ) ) {
                        $tcategory = $parent_id;
                        $scategory = $fcategory;
                        $fcategory = $categorys [$fcategory] ['parent'] ;
                    }
                }
            }
	        $this->_get_homepage( $product['is_home'] );
    	} else {
    		$this->_get_homepage( $product['is_home'] );
    	}
        View::share('fcategory', $fcategory ) ; 
        View::share('scategory', $scategory ) ;
        View::share('tcategory', $tcategory ) ;
        $this->_get_category();
        $this->_get_parent_category();
        View::share('product',$product);
    	return view('admin@manage/product');
	}

	private function _get_category() {
        $categorys = Category::all() ;
        $categorys = array_combine(array_column($categorys, 'id'), $categorys);
        View::share('categorys',$categorys);
    }

    private function _get_parent_category () {
        $categorys = Category::where('parent', 0 )->select();
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
        $data ['category_id'] = isset($data ['thirdClass']) ? $data ['thirdClass'] : ( isset($data ['secondClass']) ? $data ['secondClass'] : $data ['firstClass'] );
        unset( $data ['thirdClass'] ) ;
        unset( $data ['secondClass'] ) ;
        unset( $data ['firstClass'] ) ;
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
		$count = $category->count();
        $categorys = array_combine(array_column($categorys, 'id'), $categorys);
        $origal_categorys = $categorys;
        // 按照类排序
        usort( $categorys, function ( $c1, $c2) use($categorys) {
            $num1  = $num2 = 0 ;
            if( $c1 ['parent'] ) {
                if( ! empty( $categorys [$c1 ['parent']] ['parent']) ) {
                    $num1 = $categorys [$c1 ['parent']] ['parent'] * 10000 + $categorys [$c1 ['parent']]['sequence'] * 100 + $c1 ['sequence'] ;
                } else {
                    $num1 = $c1 ['parent'] * 10000 + $c1 ['sequence'] * 100 ;
                }
            }
            if( $c2 ['parent'] ) {
                if( !empty($categorys [$c2 ['parent']]['parent']) ) {
                    $num2 = $categorys [$c2 ['parent']]['parent'] * 10000 + $categorys [$c2 ['parent']]['sequence'] * 100 + $c2 ['sequence'] ;
                } else {
                    $num2 = $c2 ['parent'] * 10000 + $c2 ['sequence'] * 100 ;
                }
            }
            return $num1 - $num2 ;
        });
        foreach ($categorys as &$value) {
            $seq = 1;
            if( $value ['parent'] ) {
                if( ! empty( $origal_categorys [$value ['parent']] ['parent']) ) {
                    $seq = $origal_categorys [$origal_categorys [$value ['parent']]['parent'] ]['sequence'] * 10000 + $origal_categorys [$value ['parent']]['sequence'] * 100 + $value ['sequence'] ;
                } else {
                    $seq = $origal_categorys [$value ['parent']]['sequence'] * 10000 + $value ['sequence'] * 100 ;
                }
            } else {
                $seq = $value ['sequence'] * 10000;
            }
            $value ['seq'] = $seq ;
        }
        $sort_seq = array_column($categorys,'seq');
        array_multisort($sort_seq, SORT_ASC, $categorys);
		$data = [
				'total' => $count , 
				'rows' =>$categorys
		] ;
        echo json_encode($data) ;
        exit;
    }

    public function edit_category($id = 0) {
    	$category = null;
        $fcategory = $scategory = 0 ;
		if( $id ) {
			$Category = new Category;
	        $category = $Category->get($id) ;
            // 判断是否分类的等级
            $parent_id = $category ['parent'] ;
            if( $parent_id ) {
                $parent_ctegory = $Category->get($parent_id) ;
                if( $parent_ctegory ['parent'] ) {
                    $fcategory = $parent_ctegory ['parent'] ;
                    $scategory = $parent_id ;
                } else {
                    $fcategory = $parent_id ;
                }
            }
    	}
        View::share('fcategory', $fcategory ) ; 
        View::share('scategory', $scategory ) ;
        $this->_get_parent_category();
        $this->_get_category();
        View::share('category',$category);
    	return view('admin@manage/category');
    }

    function saveCategory() {
    	$request = Request::instance();
    	$post = $request->post();
    	$category = new Category;

        if( $post ['firstclass'] || $post ['secondclass'] ) {
            $post ['parent'] = !empty($post ['secondclass']) ? $post ['secondclass'] : $post ['firstclass'];
        } else {
            $post ['parent'] = 0;
        }
        unset( $post ['secondclass'] );
        unset( $post ['firstclass'] );

		if(array_key_exists('id', $post)) {
			$category->save($post , ['id' => $post ['id']]);
		} else {
            // 记录当前分类的序列
            $csequence = $category->where( 'parent' , $post ['parent'])->count();
            $post ['sequence'] = $csequence + 1;
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
		$orderby = $request->get('sort') ? $request->get('sort') : 'id' ;
		$limit = $request->get('pageSize') ?  $request->get('pageSize') : 20 ;
		$start = $request->get('offset') ? $request->get('offset') : 0 ; 
		$intro = new Intro ;
		$intros = $intro->limit($start,$limit)->order($orderby)->select() ;
		$count = $intro->count();
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
                'model' => $key == 'setting_web_logo' ? 'setting' : 'home'
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
                $dict->model = $key == 'setting_web_logo' ? 'setting' : 'home';
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
        $something = $dict->where('model' , "in" , 'setting,email')->select();
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
            $model = explode("_", $key)[0];
            $record = $dict->get( [
                'name' => $key,
                'model' => $model
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
                $dict->model = 'setting';
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
        View::share('feature',$ftur);
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

    public function delfeatures ( $id ) {
        $request = Request::instance();
        $id = $request->param('id');
        if( ! $id ) {
            echo $this->output_json(false, "ERROR param" ) ;
        }
        $feature = new Feature;
        $feature->where('id='.$id)->delete();
        echo $this->output_json ( true , "OK" , null) ;
    }

    public function saveNews() {
        $request = Request::instance();
        $data = $request->post();
        $news = new News;
        if(array_key_exists('id', $data)) {
            $news->save($data , ['id' => $data ['id']]);
        } else {
            $news->save($data);
        }
        echo $this->output_json ( true , "OK" , null) ;
    }

    public function delNews ( $id ) {
        $request = Request::instance();
        $id = $request->param('id');
        if( ! $id ) {
            echo $this->output_json(false, "ERROR param" ) ;
        }
        $news = new News;
        $news->where('id='.$id)->delete();
        echo $this->output_json ( true , "OK" , null) ;
    }

    public function upload () {
        $request = Request::instance();
        $names = explode('.', $_FILES["file"]["name"]);
        $extension = end ( $names );
        $allowedExts = array("gif", "jpeg", "jpg", "png");
        if(! in_array($extension, $allowedExts) ) {
            echo $this->output_json ( false , "不支持的文件" , null) ;
        }
        if( $_FILES["file"]["error"] > 0 ) {
            echo $this->output_json ( false , $_FILES["file"]["error"] , null) ;
        }
        $name = uniqid() . "." . $extension ;
        $path = UPLOAD_IMAGE_PATH . date('Y-m-d') . '/' ;
        if( ! is_dir ( $path ) ) {
            mkdir ($path , 0777 ) ;
        }
        move_uploaded_file($_FILES["file"]["tmp_name"], $path . $name );
        $url = $request->domain() . '/img/' . date('Y-m-d') . "/" . $name ;
        echo $this->output_json ( true , $url , ['url' => $url]);
    }

    public function delBannerImg( ) {
        $request = Request::instance();
        $name = $request->param('name');
        if( ! $name ) {
            echo $this->output_json(false, "ERROR param" ) ;
        }
        Dict::destroy(['name' => $name]);
        echo $this->output_json ( true , "OK" , null) ;
    }
}