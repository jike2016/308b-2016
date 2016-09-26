<style>
	.maininfo-box {width: 100%;background-color: #FFFFFF;box-sizing: border-box; height: 665px;overflow-y: scroll; border: 1px solid #ccc; padding: 0px 20px 20px 20px;}
	/***下拉菜单 以及 饼状图**/
	.dropdownlist { float: left; margin-top: 15px; margin-left: 20px;width: 150px;}
	.dropdownlist .dropdown-menu {min-width: 150px;}
	.learnreportkinds{width: 170px;}
	.head-box1 {width: 100%; height: 60px;}
	.head-box1 h3 {float: left;}
	.head-box2 {width: 100%;height: 60px;}
	/***下拉菜单 以及 饼状图 @end**/
	
	.maininfo-box h3 {color: #777777; font-size: 23px;}
	.maininfo-box .line {width: 100%; height: 2px; background-color: #CCCCCC;}
	.bookdata-box {width:100%;height: 50px; margin-top: 15px;}
	.bookdata-box p {font-size: 16px; color: #777777; float: left; margin: 12px 0px 0px 50px;}
	.bookdata-box .score {font-size: 25px; color: #449D44;margin: 5px 0px 0px 0px;}
	
	.learningsituation-box {width: 100%; margin-top: 20px;}
	
	.learningsituation-box h5,.learningsituation-box h3 {float: left;}
	.learningsituation-box h5 {font-size: 14px; color: #CCCCCC;margin-left: 20px; margin-top: 30px;}
	.highcharts-button{display: none;}
	text tspan{font-family: "微软雅黑";}
	
	.data-box:hover {background-color: #F0F0F0; cursor: pointer;}
</style>
<?php
	require_once("../../config.php");
	$courseid = optional_param('courseid', 1, PARAM_INT);
	global $DB;
	global $USER;
	$coursename = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
	echo '
		<div class="maininfo-box">
	<!--新加下拉菜单 修改-->
	<div class="head-box1">
		<h3>学习报告</h3>
		
		<!--学习报告类型下拉表单-->
		<div class="dropdownlist">
			<div class="input-group">
				<input type="text" class="form-control learnreportkinds" style="background-color:#ffffff;cursor:pointer" readOnly="true" value="'.$coursename->fullname.'">
				<div class="input-group-btn">
					<button class="btn btn-info  dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul id="learnreportkindslist" class="dropdown-menu dropdown-menu-right" style="cursor:pointer">
						<li value="1"><a>全部</a></li>';
	$courses=$DB->get_records_sql('select
		c.id,c.fullname
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		where a.userid='.$USER->id.' 
		GROUP BY courseid ORDER BY a.timecreated DESC');
	foreach($courses as $course){
		echo '<li value="'.$course->id.'"><a>'.$course->fullname.'</a></li>';
	}
	echo '
					</ul>
					
				</div>
			</div>
		</div>
		<!--学习报告类型下拉表单end-->
	</div>
	<!--新加下拉菜单 修改 end-->
	';
	$notes = $DB->get_record_sql('select count(1)as recordcount from mdl_note_my where notetype=1 and userid='.$USER->id.' and courseid='.$courseid);
	$comments = $DB->get_records_sql('select commenttime from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course='.$courseid.' where a.userid='.$USER->id.'
		union ALL
		select commenttime from mdl_comment_course_my where courseid='.$courseid.' and userid='.$USER->id.'
		union ALL
		select commenttime from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course='.$courseid.' where a.userid='.$USER->id.'
		ORDER BY commenttime desc');
	// $like_course = $DB->get_record_sql('select count(1) as count from mdl_course_like_my where userid='.$USER->id);
	
	echo '
		<div class="line"></div>
	<div class="bookdata-box">		
		<div id="note-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程笔记：</p>
			<p class="score">'.$notes->recordcount.'</p>
		</div>
		
		<div id="comment-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程评论：</p>
			<p class="score">'.count($comments).'</p>
		</div>
		
	</div>
	';



echo '
	<div class="learningsituation-box">
		<div class="head-box2"><h3> 七日学习情况</h3><h5>单位：课时</h5></div>
		
		<!--折线图-->
		<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
	</div>
</div>';

$Histogram_data = echo_week_learn($courseid);
//输出7日学时情况
function echo_week_learn($courseid){
	$weekday = array('"周末"','"周一"','"周二"','"周三"','"周四"','"周五"','"周六"'); 
	// echo $weekday[date('w', time())]; 
	$starttime = strtotime(date('Y-m-d', time())); // 当天的零点
	$endtime = time();//当前时间
	$day_onlinetime='';//存放每天的时间
	$day_week ='"今天"';//存放当天的星期名
	for($i=0;$i<7;$i++){
		//查询时间段内的数据
		$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$courseid).','.$day_onlinetime;
		//更新时间点
		$endtime =$starttime;
		$starttime = $endtime-86400;//减一天
		if($i<6){
			$day_week = $weekday[date('w', $starttime)].','.$day_week;
		}	
	}
	 return array($day_week,$day_onlinetime);
}

//处理每天的学习时间
function handler_day_onlinetime($starttime,$endtime,$courseid){
	global $USER;
	global $DB;
	$records = $DB->get_record_sql('
		SELECT count(1) as recordcount 
		FROM mdl_logstore_standard_log AS a 
		where a.userid='.$USER->id.'
		and a.timecreated > '.$starttime.'
		and a.timecreated < '.$endtime.'
		and courseid='.$courseid.'
	');
	return calculate_day_onlinetime($records);
	// return 7;
}

/** 计算每天的学习时间 岑霄20160308
获取事件数目，每个数目算在线1分钟
*/
function calculate_day_onlinetime($records){
	$sumtime = 0;//总时间
	$sumtime = 60*$records->recordcount;
	return round($sumtime/3600,1);//小时
}
?>
<script type="text/javascript">
	$('.lockpage').hide();

	$(function() {
		$('#Histogram').highcharts({
			
			title: {   //正标题
				text: ' '
			},
			subtitle: { //副标题
				text: ' '
			},
			xAxis: {  //X轴文本
				categories: [<?php echo $Histogram_data[0];?>],
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: { //Y轴提示
					text: '',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {  //数据点提示
				valueSuffix: ''
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			credits: {  
				enabled: false
			},
			series: [{ //数据
				name: '学习课时',
				data: [<?php echo $Histogram_data[1];?>]
			}]
		});
	});
</script>

<script>
	$(document).ready(function(){		
		//课程排序下拉菜单点击局部刷新事件
		$('#learnreportkindslist li').click(function(){  
			$('.lockpage').show();		
			$(this).parent().parent().parent().children('.learnreportkinds').val($(this).text());
			// $(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index2.html');
			if($(this).val()==1){//全部课程
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index.php');
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').parent('.myclass').parent('.right-banner').load('mybookdata/index.php');
			}
			else{//单门课程
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid='+$(this).val());
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid='+$(this).val());
			}
		});	
		//课程排序下拉菜单点击局部刷新事件 end
		
		
		$('#note-data').click(function(){      //查看笔记数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/note_data_course.php?courseid=<?php //echo $courseid;?>//");
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load("mybookdata/note_data_course.php?courseid=<?php echo $courseid;?>");
		});	
		$('#comment-data').click(function(){   //查看评论数据详情
			$('.lockpage').show();
			<!--$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/comment_data_course.php?courseid=<?php echo $courseid;?>");-->
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load("mybookdata/comment_data_course.php?courseid=<?php echo $courseid;?>");
		});	
//		$('#like-data').click(function(){      //查看点赞数据详情
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/like-data.html');
//		});
	})
</script>