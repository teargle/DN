

$(document).ready(function(){
	$(".product-tabs-wrapper .products-tablist li").click(function(){
		$(".product-tabs-wrapper .products-tablist li").removeClass("active");
		$(this).addClass("active");
		var toggle = $(this).hasClass('description_tab') ;
		$(".tbs-panel-desc").toggleClass("hidden" , !toggle);
		$(".tbs-panel-reviews").toggleClass("hidden" , toggle);
	});
});