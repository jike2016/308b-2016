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
	/*.bookdata-box p {font-size: 16px; color: #777777; float: left; margin: 12px 0px 0px 50px;}*/
	.bookdata-box p {font-size: 16px; color: #777777; text-align: center; line-height: 50px; text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
	.bookdata-box .score {font-size: 25px; color: #449D44;margin: 5px 0px 0px 0px;}
	
	.learningsituation-box {width: 100%; margin-top: 20px;}
	
	.learningsituation-box h5,.learningsituation-box h3 {float: left;}
	.learningsituation-box h5 {font-size: 14px; color: #CCCCCC;margin-left: 20px; margin-top: 30px;}
	.highcharts-button{display: none;}
	text tspan{font-family: "微软雅黑";}

	.data-box{width:16.66%; height:50px; border-right:1px solid #CCCCCC; float:left;}
	.data-box:last-child{border-right:0px}
	.data-box:hover {background-color: #F0F0F0; cursor: pointer;}
</style>
<?php
/**
 * 个人中心》台账数据》课程统计》单课程 页面
 */
	require_once("../../config.php");
	$courseid = optional_param('courseid', 1, PARAM_INT);
	$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
	$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间
	if($start_time==0 || $end_time==0){//如果时间为空
		$time = handle_time($start_time,$end_time);
		$start_time = $time['start_time'];
		$end_time = $time['end_time'];
	}

	//默认显示7天学习统计
	if(!isset($_GET["start_time"]) && !isset($_GET["end_time"])){
		$datas = get_detial_data($courseid);
		echo_detial($datas["courseName"],$datas["detials"],$datas["courses"]);

		$Histogram_data = echo_week_learn($courseid);//折线图数据
		$learntitle = '七日学习情况';

	}
	else{//如果选择了时间段，按照时间段范围显示
		$datas = get_detial_data_by_time($courseid,$start_time,$end_time);
		echo_detial($datas["courseName"],$datas["detials"],$datas["courses"]);

		if( ($end_time-$start_time) > 86400 ){//如果查询时间段大于一天，则显示折线图
			$Histogram_data = echo_time_learn($courseid,$start_time,$end_time);//折线图数据
		}
		$learntitle = '学习情况';

	}

	echo '
		<div class="learningsituation-box">
			<div class="head-box2"><h3> '.$learntitle.'</h3><h5>单位：课时</h5></div>

			<!--折线图-->
			<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
			<!--折线图 end-->
		</div>
	</div>';




//**************************************************************


/**
 * 查询时间判断
 * @param $start_time
 * @param $end_time
 * @return array
 */
function handle_time($start_time,$end_time){

	global $DB;
	global $USER;
	$minTime = $DB->get_record_sql("select MIN(l.timecreated) as mintime from mdl_logstore_standard_log l where l.userid = $USER->id ");
	if($start_time==0 && $end_time==0){
		$start_time = $minTime->mintime;
		$end_time = time();
	}elseif($start_time!=0){
		$end_time = time();
	}elseif($end_time!=0){
		$start_time = $minTime->mintime;
	}
	return array('start_time'=>$start_time,'end_time'=>$end_time);
}

/**
 * 输出时间段内的学习情况
 * @param $start_time
 * @param $end_time
 * @return array
 */
//只提供
//日折线：时间段在31天以内
//月折线：时间段大于31天
//从结束时间往前开始计算
function echo_time_learn($courseid,$start_time,$end_time){
	$day_onlinetime='';//存放每点的数据
	$day_week = '""';//存放每点的日期
	$timeslot = $end_time - $start_time;//查询的时间段
	//时间段长度的划分
	if($timeslot > 2678400){//如果( 时间段 > 一个月)，用月线显示
		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m', $endtime).'-01'); // 结束时间当月1号的零点
		$day_week = '"'.date('Y-m', $starttime).'"';//存放每点的日期
		$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$courseid).','.$day_onlinetime;
		$timeslot = $timeslot - ($endtime - $starttime);//重新计算时间段

		while($timeslot > 0){
			//计算月时间段长度
			$monthend = $starttime-1;//下月初 - 1，即为上月末
			$monthstart = strtotime(date('Y-m', $monthend).'-01');//上月初
			$monthtimeslot = ($monthend - $monthstart) + 1;//月时间段，注意时间的精确性
			//更新时间点
			$endtime = $starttime;
			if($timeslot > $monthtimeslot){//如果剩余时间大于月时间段
				$starttime = $endtime-$monthtimeslot;
			}else{
				$starttime = $start_time;
			}
			//查询时间段内的数据
			$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$courseid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m', $starttime).'",'.$day_week;
			$timeslot = $timeslot - $monthtimeslot;//时间段减去月时间段。同时，这里是循环的出口
		}

	}elseif($timeslot >= 86400){//如果( 一天 < 时间段 < 一个月)，用日线显示

		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m-d', $endtime)); // 结束时间当天的零点
		$day_week = '"'.date('Y-m-d', $starttime).'"';//存放每点的日期
		$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$courseid).','.$day_onlinetime;
		$timeslot = $timeslot - ($endtime - $starttime);//重新计算时间段

		while($timeslot > 0){
			//更新时间点
			$endtime = $starttime;
			if($timeslot > 86400){//如果剩余时间大于一天
				$starttime = $endtime-86400;
			}else{
				$starttime = $start_time;
			}
			//查询时间段内的数据
			$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$courseid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m-d', $starttime).'",'.$day_week;
			$timeslot = $timeslot - 86400;//时间段减去一天。同时，这里是循环的出口

		}
	}
	return array($day_week,$day_onlinetime);

}

/**
 * 字段的sql 拼接
 * @param $field
 * @param $startTime
 * @param $endTime
 * @return string
 */
function handle_field_time_sql($field,$startTime,$endTime){

	$time_sql = '';
	if($startTime==0 && $endTime==0){

	}elseif($startTime!=0 && $endTime!=0){
		$time_sql = ' and '.$field.' between '.$startTime.' and '.$endTime;
	}elseif($startTime!=0){
		$time_sql = ' and '.$field.' > '.$startTime;
	}elseif($endTime!=0){
		$time_sql = ' and '.$field.' < '.$endTime;
	}
	return $time_sql;
}

//按时间段获取基础信息
function get_detial_data_by_time($courseid,$startTime,$endTime){
	global $USER;
	global $DB;
	$notetime_sql = handle_field_time_sql('time',$startTime,$endTime);
	$commenttime_sql = handle_field_time_sql('commenttime',$startTime,$endTime);
	//课程信息
	$courseName = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
	//查询笔记，评论
	$sql = "select * from
			(select count(1)as notecount from mdl_note_my where notetype=1 and userid=$USER->id and courseid=$courseid  $notetime_sql) as a,
			( select count(1) as commentcount from
					(select commenttime from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course=$courseid where a.userid=$USER->id  $commenttime_sql
					union ALL
					select commenttime from mdl_comment_course_my where courseid=$courseid and userid=$USER->id  $commenttime_sql
					union ALL
					select commenttime from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course=$courseid where a.userid=$USER->id  $commenttime_sql
					ORDER BY commenttime desc)
				as temp) as b";
	$detials = $DB->get_record_sql($sql);

	//获取课程
	$sql = 'select
		c.id,c.fullname
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		where a.userid='.$USER->id.'
		GROUP BY courseid ORDER BY a.timecreated DESC';
	$courses=$DB->get_records_sql($sql);

	return array('courseName'=>$courseName->fullname,'detials'=>$detials,'courses'=>$courses);
}

//获取基础信息
function get_detial_data($courseid){
	global $USER;
	global $DB;
	//课程信息
	$courseName = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
	//查询笔记，评论
	$sql = "select * from
			(select count(1)as notecount from mdl_note_my where notetype=1 and userid=$USER->id and courseid=$courseid ) as a,
			( select count(1) as commentcount from
					(select commenttime from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course=$courseid where a.userid=$USER->id
					union ALL
					select commenttime from mdl_comment_course_my where courseid=$courseid and userid=$USER->id
					union ALL
					select commenttime from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course=$courseid where a.userid=$USER->id
					ORDER BY commenttime desc)
				as temp) as b";
	$detials = $DB->get_record_sql($sql);

	//获取课程
	$sql = 'select
		c.id,c.fullname
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		where a.userid='.$USER->id.'
		GROUP BY courseid ORDER BY a.timecreated DESC';
	$courses=$DB->get_records_sql($sql);

	return array('courseName'=>$courseName->fullname,'detials'=>$detials,'courses'=>$courses);
}

//输出基础数据信息
function echo_detial($courseName,$detials,$courses){
	echo '
		<div class="maininfo-box">
		<!--新加下拉菜单 修改-->
		<div class="head-box1">
			<h3>学习报告</h3>

			<!--学习报告类型下拉表单-->
			<div class="dropdownlist">
				<div class="input-group">
					<input type="text" class="form-control learnreportkinds" style="background-color:#ffffff;cursor:pointer" readOnly="true" value="'.$courseName.'">
					<div class="input-group-btn">
						<button class="btn btn-info  dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul id="learnreportkindslist" class="dropdown-menu dropdown-menu-right" style="cursor:pointer">
							<li value="1"><a>全部</a></li>';
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

	echo '
		<div class="line"></div>
	<div class="bookdata-box">
		<div id="note-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程笔记：<span class="score">'.$detials->notecount.'</span></p>
		</div>

		<div id="comment-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程评论：<span class="score">'.$detials->commentcount.'</span></p>
		</div>

	</div>
	';
}

//获取用户在该时间段内完成的课时数（章节数）
function get_coursecomplete_count($starttime,$endtime,$courseid){
	global $USER;
	global $DB;
	$sql = "select cmc.userid,count(1) as count
				from mdl_course_modules_completion cmc
				join mdl_course_modules cm on cmc.coursemoduleid = cm.id
				where cmc.userid =$USER->id
				and cm.course = $courseid
				and cmc.timemodified between $starttime and $endtime ";
	$records = $DB->get_record_sql($sql);
	return $records->count;
}

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
//		$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$courseid).','.$day_onlinetime;
		$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$courseid).','.$day_onlinetime;
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
	index_flag = 2;//单课程
	var courseid = <?php echo $courseid; ?>;
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
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').parent('.myclass').parent('.right-banner').load('mybookdata/index.php');
				if(time_flag){
					$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/course_index.php?start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
				}else{
					$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/course_index.php');
				}

			}
			else{//单门课程
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid='+$(this).val());
				if(time_flag){
					$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid='+$(this).val()+'&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
				}else{
					$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid='+$(this).val());
				}
			}
		});	
		//课程排序下拉菜单点击局部刷新事件 end
		
		
		$('#note-data').click(function(){      //查看笔记数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/note_data_course.php?courseid=<?php //echo $courseid;?>//");
//			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load("mybookdata/note_data_course.php?courseid=<?php //echo $courseid;?>//");
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load("mybookdata/note_data_course.php?courseid=<?php echo $courseid;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>");
		});
		$('#comment-data').click(function(){   //查看评论数据详情
			$('.lockpage').show();
			<!--$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/comment_data_course.php?courseid=<?php echo $courseid;?>");-->
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load("mybookdata/comment_data_course.php?courseid=<?php echo $courseid;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>");
		});	
//		$('#like-data').click(function(){      //查看点赞数据详情
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/like-data.html');
//		});
	})
</script>