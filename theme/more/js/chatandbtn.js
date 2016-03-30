//聊天室 START
$(document).ready(function(){
	//适配不同大小偏移值
	var winW=$(window).width();
	var winH=$(window).height();
	var leftval = (winW-900)/2;	
	var topval = (winH-600)/3;	
	$('.chat-box').offset({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
	//适配不同大小偏移值 end	
	
	$('.elevator-weixin').click(function(){
									
		$('.chat-box').show();	
	})
	$('#chat-close').click(function(){
		$('.chat-box').hide();
		//alert("关闭的top: " +$('.chat-box').offset().top);
	})
	//聊天室 End
	//收藏按钮
	$('#collection-btn').click(function()
	{
		$.ajax({
			url: "<?php echo $CFG->wwwroot;?>/privatecenter/mycollection/collectionpage.php",
			data: {mytitle: document.title, myurl: window.location.href },
			success: function(msg){
				if(msg=='1'){
					alert('收藏成功，可去个人中心查看')
				}
				else{
					msg=='2' ? alert('您已经收藏过了，请去个人中心查看收藏结果') :alert('收藏失败');
				}
			}
		});
	});
	//点赞按钮
	$('#like-btn').click(function()
	{
		$.ajax({
			url: "<?php echo $CFG->wwwroot;?>/like/courselike.php",
			data: {mytitle: document.title, myurl: window.location.href },
			success: function(msg){
				// alert(msg);
				if(msg=='1'){
					alert('点赞成功')
				}
				else{
					msg=='2' ? alert('你已经点赞了，不能再次点赞') :alert('点赞失败');
				}
			}
		});
	});
});