<?php
require_once("../../config.php");
$bookdataType = optional_param('bookdataType', 1, PARAM_INT);//1:课程台账 2：微阅台账 3：、、、、
?>

<script>
	/** Start 功能显示判断*/
//	if(getQueryString('bookdataType') == 2){//微阅台账
	if(<?php echo $bookdataType; ?> == 2){//微阅台账
		$('.maininfo-box-index').load('mybookdata/microread_index.php?');
		//微阅样式修改
		$('.kinds-a ').removeClass('a-active');
		$('#microread_statistic').addClass('a-active');
	}
	else{//课程台账
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
		//判断时间控件是否显示启用，启用便加上起止时间
		if(time_flag){
			index_flag = 1;//全部课程
			if(check_time()){
				$('.maininfo-box-index').load('mybookdata/course_index.php?start_time='+start_time+'&end_time='+end_time);
			}
		}else{
			$('.maininfo-box-index').load('mybookdata/course_index.php');
		}
	});
	/*** 台账数据 - 课程统计 end***/

	/*** 台账数据 - 微阅统计 ***/
	$('#microread_statistic').click(function() {           //微阅统计-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		if(time_flag){
			if(check_time()){
				$('.maininfo-box-index').load('mybookdata/microread_index.php?start_time='+start_time+'&end_time='+end_time);
			}
		}else{
			$('.maininfo-box-index').load('mybookdata/microread_index.php');
		}

	});
	/*** 台账数据 - 微阅统计 end***/

	//验证查询的起止时间
	function check_time(){
		start_time = $('#start_time').val();//开始时间
		end_time = $('#end_time').val();//结束时间

		if (start_time != '') {
			start_time = get_unix_time(start_time);
		} else {
			start_time = 0;
		}
		if (end_time != '') {
			end_time = get_unix_time(end_time);
		} else {
			end_time = 0;
		}
		if ((start_time != 0 && end_time != 0) && (start_time >= end_time)) {
			alert('请确认结束时间大于开始时间！');
			$('.lockpage').hide();
			return false;
		}
		return true;
	}

</script>

<?php require_once("time_plug/time_plug_style.php");//引入时间控件样式?>

<!--我的课程-->
<div class="myclass">
	<div class="kinds">
		<a id="course_statistic" class="kinds-a a-active" href="javascript:void(0)">课程统计</a>l
		<a id="microread_statistic" class="kinds-a " href="javascript:void(0)">微阅统计</a>
		<div style="float: right">
			<?php require_once("time_plug/time_plug_html.php");//引入时间控件?>
		</div>
	</div>

	<!--我的课程页面-课程页面-->
	<div class="maininfo-box-index">

	</div>
	<!--我的课程页面-课程页面end-->
