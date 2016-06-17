<script>
	/** Start 功能显示判断*/
	if(getQueryString('class') == 'microread'){
		$('.maininfo-box-index').load('mybookdata/microread_index.php');
		//微阅样式修改
		$('.kinds-a ').removeClass('a-active');
		$('#microread_statistic').addClass('a-active');
	}
	else{
		$('.maininfo-box-index').load('mybookdata/course_index.php');
		//课程样式修改
		$('.kinds-a ').removeClass('a-active');
		$('#course_statistic').addClass('a-active');
	}
	/** end 功能显示判断*/

	//点击时样式的切换
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});

	/*** 台账数据 - 课程统计 ***/
	$('#course_statistic').click(function() {        //课程统计-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('.maininfo-box-index').load('mybookdata/course_index.php');
	});
	/*** 台账数据 - 课程统计 end***/

	/*** 台账数据 - 微阅统计 ***/
	$('#microread_statistic').click(function() {           //微阅统计-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('.maininfo-box-index').load('mybookdata/microread_index.php');
	});
	/*** 台账数据 - 微阅统计 end***/


</script>

<!--我的课程-->
<div class="myclass">
	<div class="kinds">
		<a id="course_statistic" class="kinds-a a-active" href="javascript:void(0)">课程统计</a>l
		<a id="microread_statistic" class="kinds-a " href="javascript:void(0)">微阅统计</a>

	</div>

	<!--我的课程页面-课程页面-->
	<div class="maininfo-box-index">

	</div>
	<!--我的课程页面-课程页面end-->
