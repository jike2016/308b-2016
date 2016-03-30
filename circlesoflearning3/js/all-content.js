$(function () {
	// 点击缩略图放大
	$(".trends-content > .thumb").click(function () {
		$(this).hide().next('.pic-big').fadeIn();
	});
	// 点击大图缩小
	$(".trends-content > .pic-big").click(function () {
		$(this).hide().prev('.thumb').fadeIn();
	});
});