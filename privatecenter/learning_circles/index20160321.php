<style>
	.learning_circles_main {width: 100%; height: 900px;  background-color: #FFFFFF; overflow-y: scroll;}
</style>

<script>	
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});
	/***学习圈-全部内容***/
	$('#all_contents').click(function() {        //全部内容-标签动作
		$("#iframepage").attr("src","../circlesoflearning/index.php");	
		$(".learning_circles_main").css("height","900px");			
	});
	/***学习圈-全部内容 end***/
			
	/***学习圈-我的关注***/
	$('#my_attention').click(function() {           //我的关注-标签动作
		$('#iframepage').attr('src','../circlesoflearning/myconcerned.php');	
		$(".learning_circles_main").css("height","600px");			
	});
	/***学习圈-我的关注 end***/
	
	/***学习圈-与我相关***/
	$('#affect_me').click(function() {           //与我相关-标签动作
		$('#iframepage').attr('src','../circlesoflearning/myrelated.php');	
		$(".learning_circles_main").css("height","900px");			
	});
	/***学习圈-与我相关 end***/
	
	/***学习圈-写篇博客***/
	$('#write_blog').click(function() {           //写篇博客-标签动作
		$('#iframepage').attr('src','../circlesoflearning/edit.php');	
		$(".learning_circles_main").css("height","600px");	
	});
	/***学习圈-写篇博客 end***/
	
	
</script>

<!--学习圈-->
<div class="kinds">
	<a id="all_contents" class="kinds-a a-active" href="javascript:void(0)">全部内容</a>l
	<a id="my_attention" class="kinds-a" href="javascript:void(0)">我的关注</a>l
	<a id="affect_me" class="kinds-a" href="javascript:void(0)">与我相关</a>l
	<a id="write_blog" class="kinds-a" href="javascript:void(0)">写篇博客</a>
</div>


 <iframe id="iframepage" src="../circlesoflearning/index.php" class="learning_circles_main" marginheight="0" marginwidth="0" frameborder="0"  width=100%   name="iframepage"  ></iframe>

		
<!--学习圈 end-->
