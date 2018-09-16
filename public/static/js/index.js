
// 调整产品图片
function init_justImg () {
	$(window).resize(function(){
		justImg( );
	}) ;
	justImg();
}
function justImg ( ) {
	justImgWidth ();
	$(".photos-col a").each(function(index){
		jsutImgOne ( $(this) , index ) ;
	}) ;
	justImgAll();
}
function justImgWidth( ) {
	$img = $(".photos-col a:eq(0)");
	var imgWidth = $img.width();
	var colNum = Math.round ( $img.parent().width() / $img.width() );
	var changeRate = 0 ;
	if( imgWidth < 180 ) {
		changeRate = colNum - 1 ;
	} else if ( imgWidth > 250 ) {
		changeRate = colNum + 1 ;
	}
	if( changeRate == 0 ) return ;
	var rate = ( 100 / changeRate ) + '%' ;
	$(".photos-col a").width( rate );
}
function jsutImgOne ( $img , n ) {
	var colNum = Math.round ( $img.parent().width() / $img.width() );
	current_col = n % colNum ;
	current_row = parseInt( n / colNum ) ; 
	var rate = ( current_col / colNum  + 0.002 * current_col ) * 100 + '%' ;
	$img.css('left' , rate ) ;
	$img.css('top' , current_row * $img.height() + 2 * current_row ) ;
}

function justImgAll ( ) {
	var imgNum = $(".photos-col a").length ;
	$img = $(".photos-col a:eq(0)");
	var colNum = Math.round ( $img.parent().width() / $img.width() );
	number_row = parseInt( imgNum / colNum ) + 1 ; 
	
	all_height = number_row * $img.height() + number_row * 2 ;
	$(".photos-imgs").height( number_row * $img.height() ) ;
	$(".photos-col").height( number_row * $img.height() ) ;
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