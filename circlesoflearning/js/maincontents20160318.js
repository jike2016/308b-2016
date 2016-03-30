$(document).ready(function(){
	// 点击缩略图放大
	$(".trends-content > .thumb").click(function () {
		$(this).hide().next('.pic-big').fadeIn();
	});
	// 点击大图缩小
	$(".trends-content > .pic-big").click(function () {
		$(this).hide().prev('.thumb').fadeIn();
	});

	$("#like").click(function () {     //点赞动作
		$('.trends-bottom li').removeClass('a-active');
		$(this).addClass('a-active');
		$(this).parent().siblings('.comment-banner').fadeOut();
	});
	$("#comment").click(function () {  //评论动作
		$(this).addClass('a-active');
		alert(123);
		$(this).parent().siblings('.comment-banner').fadeIn();
	});

	$("#forward").click(function () {  //转发动作
		$('.trends-bottom li').removeClass('a-active');
		$(this).parent().siblings('.comment-banner').fadeOut();
	});
	$("#delete").click(function () {   //删除动作
		$('.trends-bottom li').removeClass('a-active');

		$(this).parent().siblings('.comment-banner').fadeOut();
	});
})

function followUser(obj)
{
	var btn = document.getElementById("followUser-btn");//根据id获取button节点
	alert(btn);
	//$.ajax({
	//	url: "../blog/blogbackground.php",
	//	data: {concernid: obj.value, type:"concern" },
	//	success: function(msg){
	//		if(msg=="1"){
	//			//alert("关注成功");
	//			location.reload();
	//		}
	//		else if(msg=="2"){
	//			location.reload();
	//		}
	//		else
	//		{
	//			alert("关注失败");
	//		}
	//	}
	//});
}