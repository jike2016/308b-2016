<script>
	/** Start 功能显示判断*/
//	if(getQueryString('class') == 'zhibo'){
//		$('.maininfo-box').load('myclass/course_zhibo.php');
//		 //直播样式的修改
//		$('.kinds-a ').removeClass('a-active');
//		$('#course_zhibo').addClass('a-active');
//	}
//	else{
//		$('.maininfo-box').load('myclass/course_inprogress.php');
//		 //正在播放的样式修改
//		$('.kinds-a ').removeClass('a-active');
//		$('#course_inprogress').addClass('a-active');
//	}
	/** end 功能显示判断*/

	//点击时样式的切换	
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});
	/***我的课程-正在学习***/
	$('#course_inprogress').click(function() {        //正在学习-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('.maininfo-box').load('myclass/course_inprogress.php');				
	});
	/***我的课程-正在学习 end***/
			
	/***我的课程-已完成***/
	$('#course_complete').click(function() {           //已完成-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('.maininfo-box').load('myclass/course_complete.php');
	});
	/***我的课程-已完成 end***/

	/***我的课程-直播***/
	$('#course_zhibo').click(function() {           //直播-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('.maininfo-box').load('myclass/course_zhibo.php');
	});
	/***我的课程-直播 end***/

</script>

<!--我的课程-->
	<div class="myclass">
		<div class="kinds">
			<a id="course_inprogress" class="kinds-a a-active" href="javascript:void(0)">正在学习</a>l
			<a id="course_complete" class="kinds-a " href="javascript:void(0)">已完成</a>l
			<a id="course_zhibo" class="kinds-a " href="javascript:void(0)">直播</a>

		</div>

		<!--我的课程页面-课程页面-->
		<div class="maininfo-box">
						
		</div>
<!--我的课程页面-课程页面end-->
