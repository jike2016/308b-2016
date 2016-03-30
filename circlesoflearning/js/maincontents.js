$(document).ready(function(){
	$(".trends-content > .thumb").click(function(){
		//$(this).hide().next('.pic-big').fadeIn();
		if($(this).hasClass("pic-big"))
			$(this).removeClass("pic-big");
		else
		{
			$(".trends-content > .thumb").removeClass("pic-big");
			$(this).addClass("pic-big");
		}

	});

	$(".like-btn").click(function () {     //点赞动作
		$(".comment-banner").hide();
		$(".trends-bottom li").removeClass("a-active");
		var valueid = $(this).val();
		$.ajax({
			url: "../circlesoflearning/blogbackground.php",
			data: {blogid: valueid, relatedtype:"3", type:"related" },
			success: function(msg){
				if(msg=="1"){
					location.reload();
				}
				else{
					msg=="2" ? alert("你已经为它点赞了，无需再次点赞") :alert("点赞失败");
				}
			}
		});
		$(this).addClass("a-active");
		$(this).parent().siblings(".comment-banner").fadeOut();
	});

	$(".comment-btn").click(function () {  //评论动作
		if($(this).hasClass("a-active"))
		{
			$(this).parent().siblings(".comment-banner").fadeOut();
			$(this).removeClass("a-active");
		}
		else{
			$(".comment-banner").hide();
			$(this).addClass("a-active");
			$(this).parent().siblings(".comment-banner").fadeIn();
		}
		
	});

	$(".forward-btn").click(function () {  //转发动作
		$(".comment-banner").hide();
		$(".trends-bottom li").removeClass("a-active");
		var valueid = $(this).val();
		$.ajax({
			url: "../circlesoflearning/blogbackground.php",
			data: {blogid: valueid, relatedtype:"2", type:"related" },
			success: function(msg){
				if(msg=="1"){
					location.reload();
				}
			}
		});
		$(this).parent().siblings(".comment-banner").fadeOut();
	});

	$(".delete-btn").click(function () {   //删除动作
		$(".comment-banner").hide();
		$(".trends-bottom li").removeClass("a-active");
		var valueid = $(this).val();
		$.ajax({
			url: "../circlesoflearning/blogbackground.php",
			data: {blogid: valueid, type:"delete" },
			success: function(msg){
				if(msg=="1"){
					location.reload();
				}
				else if(msg=="2")
				{
					alert("您没有权限进行操作");
				}
			}
		});
		$(this).parent().siblings(".comment-banner").fadeOut();
	});

	/** Start 添加评论按钮点击事件 朱子武 20160315*/
	$(".commentBtnClick").click(function() {
		var mytext =$(this).parent().children(".form-control").val();
		var valueid = $(this).val();
		if(mytext==""){
			alert("请输入评论内容");
		}
		else{
			$.ajax({
				url: "../circlesoflearning/blogbackground.php",
				data: { mycomment: mytext, blogid: valueid, type:"comment"},
				success: function(msg){
					if(msg=="1"){
						location.reload();
					}else{
						alert("评论失败");
					}
				}
			});
		}
	});
	/** End 添加评论按钮点击事件 朱子武 20160315*/
	
})

function followUser(value)
{
	$.ajax({
		url: "../circlesoflearning/blogbackground.php",
		data: {concernid: value, type:"concern" },
		success: function(msg){
			if(msg=="1"){
				location.reload();
			}
			else if(msg=="2"){
				location.reload();
			}
			else
			{
				alert("关注失败");
			}
		}
	});
}