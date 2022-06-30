
// 调整产品图片
var colNum = 5 ;
function init_justImg () {
	$(window).resize(function(){
		$(".photos-col a").each(function(index){
			productimg ( $(this) ) ;
		}) ;
	}) ;
	$(".photos-col a").each(function(index){
			productimg ( $(this) ) ;
	}) ;
}
/**
 *  一排放5个，调整图片大小，以适应这个数量
 **/
function productimg( $img ) {
	var index = $img.data("index");
	current_col = (index - 1) % colNum ; // 当前第几列
	current_row = parseInt( (index - 1) / colNum ) ; //当前第几行, 从0行开始
	var imgheight = imgWidth = Math.round ( $(".photos-col").width() / colNum ) - 5;
	// 设置图片位置
	var left = ( imgWidth  + 1 ) * current_col;
	$img.css('left' , left ) ;
	var top = ( imgheight + 0.002 ) * current_row;
	$img.css('top' , top) ;
	//设置图片大小 正方形
	$img.width( imgWidth ) ;
	$img.height( imgheight ) ;
	// 整体高度要调整一下一遍能显示所有图片
	var photos_height = (current_row + 1) * imgheight ;
	if( $(".photos-col").height() < photos_height ) {
		$(".photos-col").height( photos_height ) ;
	}
}

// 调整新闻
function init_justNews() {

	var totalWidth = $('.new-container').width();
	if ( totalWidth > 960 ) {
		$(".new-left").removeClass("hidden");
		$(".new-left").css("float" , "left");
		$(".new-right").css("float" , "right");
		$('.new-container').removeAttr('padding-left');
		$('.new-container').removeAttr('padding-right');
	} else if ( totalWidth > 960 || totalWidth > 560 ) {
		$(".new-left").css("float" , "none");
		$(".new-right").css("float" , "none");
		$('.new-container').css('padding-left' , '15px');
		$('.new-container').css('padding-right' , '15px');
		$(".new-left").addClass("hidden");
	} else if ( totalWidth < 560 ) {
		$(".new-left").removeClass("hidden");
		$(".new-left").css("float" , "left");
		$(".new-right").css("float" , "right");
		$('.new-container').removeAttr('padding-left');
		$('.new-container').removeAttr('padding-right');
	}

}