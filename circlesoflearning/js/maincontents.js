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
		var textmy = mytext;
		textmy = textmy.replace(/[\ |\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,"");
		if(textmy.length <= 10){
			alert('评论内容不能少于10个汉字');
		}
		else{
			$.ajax({
				url: "../circlesoflearning/blogbackground.php",
				data: { mycomment: mytext, blogid: valueid, type:"comment"},
				success: function(msg){
					if(msg=="1"){
						location.reload();
					}
					else if(msg=='2')
					{
						alert('评论失败，评论内容重复！')
					}
					else {
						alert('评论失败，一分钟內只能评论一次！')
					}
				}
			});
		}
	});
	/** End 添加评论按钮点击事件 朱子武 20160315*/

	$(".people-box").click(function() {
		var userid = $(this).children(".name").attr("value");
		// window.open(window.location.protocol+"//"+window.location.host+"/moodle/circlesoflearning/index.php?userid="+userid);
		window.location.href="index.php?userid="+userid;
	});

	<!-- Start 搜索按钮点击事件  朱子武 20160315-->
	$(".submit").click(function(){
		var nameval = $(this).siblings(".search_key").val();
		searchDataFromTable(nameval);
	});
	<!-- End 搜索按钮点击事件  朱子武 20160315-->
	
	// 鼠标放到个人头像上显示个人信息
	(function () {
		var detailBox = $('.detailInfo');	// 详细信息div

		/**
		 * 加载用户数据
		 */
		function ajaxLoadUserData () {
			var uid = detailBox.attr("uid"); // 用户id

			$.ajax({
				type: 'POST',
				url: "../circlesoflearning/bloguserinfo.php",
				data: { uid: uid},
				success: function(data){
					detailBox.find('.portarit').html(data.usericon);
					detailBox.find('.username').html(data.firstname);
					detailBox.find('.phone').html(data.phone1);
					detailBox.find('.organ').html(data.name);
					detailBox.find('.loading').hide();
					detailBox.find('.content').show();
				},
				dataType: 'json'
			});
		}

		$('.trends-block .portarit').mouseenter(function(e){
				// 鼠标从详细信息div回到头像，阻止触发div
				if(e.fromElement == detailBox.get(0) || detailBox.find(e.fromElement).length > 0)
					return;

				var boxHeight = detailBox.height();	// 详细信息div的高度
				var pos = $(this).offset();			// 头像相对于客户区的坐标

				pos.left += 40;

				var uid = $(this).parents(".trends-block > li").attr("uid"); // 用户id
				detailBox.attr("uid", uid);

				// 显示正在加载图标，隐藏内容
				detailBox.find('.content').hide();
				detailBox.find('.loading').show();

				// 显示详细信息div
				detailBox.stop().delay(500).show(0, ajaxLoadUserData).css(pos).animate({left: pos.left + 10, opacity: 1}, 200);
			})
			.mouseleave(function(e){
				// 鼠标从头像进入详细信息div，阻止隐藏div
				if(e.toElement == detailBox.get(0) || detailBox.find(e.toElement).length > 0)
					return;

				// 隐藏详细信息div
				detailBox.clearQueue().animate({left: "-=10px", opacity: 0}, 200).hide(0);
			});

		// 隐藏详细信息div
		detailBox.mouseleave(function(e){
			// 鼠标从详细信息div进入头像，阻止隐藏div
			if($(e.toElement).is('.trends-block .portarit > img'))
				return;

			detailBox.clearQueue().animate({left: "-=10px", opacity: 0}, 200).hide(0);
		});
	})();

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

/** Start 搜索学员信息 朱子武 20160315*/
function searchDataFromTable(searchtext)
{
	$.ajax({
		url: "../circlesoflearning/blogbackground.php",
		dataType:"json",
		data: {type: "searchuser", searchtext: searchtext},
		success: function(msg) {
			$(".concerned-list").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment["lastname"] + comment["firstname"];
				var concernID = comment["concernid"];
				var userIcon = comment["userIcon"];
				addDataInBox(concernID, user_name, userIcon);
			});
			$(".people-box").click(function()
			{
				var userid = $(this).children(".name").attr("value");
				window.open(window.location.protocol+"//"+window.location.host+"/moodle/circlesoflearning/index.php?userid="+userid);
			});
		}
	});
}
/** End 搜索学员信息 朱子武 20160315*/

/** Start 显示学员信息 朱子武 20160315*/
function addDataInBox(concernID, userName, userIcon)
{
	$(".concerned-list").append('<div class="people-box" style="cursor: pointer;"><div class="Learnerimg-box"><img src ="'+userIcon+'"></div><div class="line"></div><p class="name" value = '+concernID+'>'+userName+'</p></div>');
}
/** End 显示学员信息 朱子武 20160315*/