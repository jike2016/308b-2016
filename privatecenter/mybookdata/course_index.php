<script>
$('.lockpage').hide();
</script>
<style>
	/***下拉菜单 以及 饼状图**/
	.dropdownlist { float: left; margin-top: 15px; margin-left: 20px;width: 150px;}
	.dropdownlist .dropdown-menu {min-width: 150px;}
	.learnreportkinds{width: 170px;}
	.head-box1 {width: 100%; height: 60px;}
	.head-box1 h3 {float: left;}
	.head-box2 {width: 100%;height: 60px;}
	/***下拉菜单 以及 饼状图 @end**/
	
	/*.maininfo-box {width: 100%;background-color: #FFFFFF; min-height: 665px; border: 1px solid #ccc; padding: 0px 20px 20px 20px;}*/
	.maininfo-box {width: 100%;background-color: #FFFFFF; min-height: 665px; border: 0px solid #ccc; padding: 0px 20px 20px 20px;}
	.maininfo-box h3 {color: #777777; font-size: 23px;}
	.maininfo-box .line {width: 100%; height: 2px; background-color: #CCCCCC;}
	.bookdata-box {width:100%;height: 50px; margin-top: 15px;}
	.bookdata-box p {font-size: 16px; color: #777777; float: left; margin: 12px 0px 0px 50px;}
	.bookdata-box .score {font-size: 25px; color: #449D44;margin: 5px 0px 0px 0px;}
	
	.learningsituation-box {width: 100%; margin-top: 20px;}
	
	.learningsituation-box h5,.learningsituation-box h3 {float: left;}
	.learningsituation-box h5 {font-size: 14px; color: #CCCCCC;margin-left: 20px;}
	.highcharts-button{display: none;}
	text tspan{font-family: "微软雅黑";}
	
	.data-box:hover {background-color: #F0F0F0; cursor: pointer;}
</style>
<?php
require_once("../../config.php");

echo_detial();
$Histogram_data = echo_week_learn();

echo '<div class="learningsituation-box">
		<div class="head-box2"><h3>七日学习情况</h3></div>';
		$mytime= time()-3600*24*7;
		$sql='and a.timecreated>'.$mytime;
		global $USER;
		$haspiechar = echo_piechar($USER->id,$sql);
echo '

		<!--折线图-->
		<h5>单位：课时</h5>
		<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
	</div>
</div>';

///////////////////////////////////////////////////////////////////////////
/** 计算每天的学习时间 岑霄20160308
1、获取第一个事件的时间，
2、从第二个事件开始，判断寻找登录和登出事件，然后相减获取一段时间，
3、循环步骤2，直到记录结束
*/
function calculate_day_onlinetime($records){
	$sumtime = 0;//总时间
	$k=count($records);
	if($k>0){
		$timestartflag = $records[1]->timecreated;//时间段起点
		$calculateflag= false;//false:没有计算过，true：计算过
		
		for($n=2;$n<=$k;$n++){
			if($records[$n]->action=='loggedin'){//登录
				if($calculateflag==false){
					$sumtime += $records[$n-1]->timecreated-$timestartflag;
					$timestartflag = $records[$n]->timecreated;
					$calculateflag= false;
				}
				else{
					$timestartflag = $records[$n]->timecreated;
					$calculateflag= false;
				}
				
			}
			elseif($records[$n]->action=='loggedout'){//登出
				$sumtime += $records[$n]->timecreated-$timestartflag;
				$calculateflag=true;
				if($n<$k){
					$timestartflag = $records[$n+1]->timecreated;
				}
			}
		}
		if($records[$k]->action!='loggedout'&&$records[$k]->action!='loggedin'){
			$sumtime += $records[$k]->timecreated-$timestartflag;
		}
	}
	return round($sumtime/3600,1);//小时
}

//处理每天的学习时间
function handler_day_onlinetime($starttime,$endtime){
	global $USER;
	global $DB;
	$records = $DB->get_records_sql('
		SELECT 
		(
			SELECT COUNT(id) 
			FROM mdl_logstore_standard_log AS tb1 
			WHERE tb1.id<= tb2.id 
			and tb1.userid='.$USER->id.'		
			and tb1.timecreated > '.$starttime.'
			and tb1.timecreated < '.$endtime.'
		) as tempid, 
		action,timecreated  
		FROM mdl_logstore_standard_log AS tb2 
		where tb2.userid='.$USER->id.'
		and tb2.timecreated > '.$starttime.'
		and tb2.timecreated < '.$endtime.'
		ORDER BY 1
	');
	return calculate_day_onlinetime($records);
	// return 7;
}

//输出7日学时情况
function echo_week_learn(){
	$weekday = array('"周末"','"周一"','"周二"','"周三"','"周四"','"周五"','"周六"'); 
	// echo $weekday[date('w', time())]; 
	$starttime = strtotime(date('Y-m-d', time())); // 当天的零点
	$endtime = time();//当前时间
	$day_onlinetime='';//存放每天的时间
	$day_week ='"今天"';//存放当天的星期名
	for($i=0;$i<7;$i++){
		//查询时间段内的数据
		$day_onlinetime = handler_day_onlinetime($starttime,$endtime).','.$day_onlinetime;
		//更新时间点
		$endtime =$starttime;
		$starttime = $endtime-86400;//减一天
		if($i<6){
			$day_week = $weekday[date('w', $starttime)].','.$day_week;
		}	
	}
	 return array($day_week,$day_onlinetime);
}

//输出基本信息
function echo_detial(){
	global $USER;
	global $DB;
	//查询笔记，评论，点赞，星评，勋章数
	$notes = $DB->get_record_sql('select count(1) as count from mdl_note_my where userid='.$USER->id);
	$comment_video = $DB->get_record_sql('select count(1) as count from mdl_comment_video_my where userid='.$USER->id);
	$comment_article = $DB->get_record_sql('select count(1) as count from mdl_comment_article_my where userid='.$USER->id);
	$comment_course = $DB->get_record_sql('select count(1) as count from mdl_comment_course_my where userid='.$USER->id);
	$commentsum=$comment_video->count +$comment_course->count+$comment_article->count;
	$like_course = $DB->get_record_sql('select count(1) as count from mdl_course_like_my where userid='.$USER->id);
	$score_course = $DB->get_record_sql('select count(1) as count from mdl_score_course_my where userid='.$USER->id);
	$badges = $DB->get_record_sql('select count(1) as count from mdl_badge_issued where userid='.$USER->id);
	echo '
		<div class="maininfo-box">
		<!--新加下拉菜单 修改-->
	<div class="head-box1">
		<h3>课程学习报告</h3>
		
		<!--学习报告类型下拉表单-->
		<div class="dropdownlist">
			<div class="input-group">
				<input type="text" class="form-control learnreportkinds" style="background-color:#ffffff;cursor:pointer" readOnly="true" value="全部">
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
		<div class="line"></div>
		<div class="bookdata-box">
			<div id="note-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>笔记：</p>
				<p class="score">'.$notes->count.'</p>
			</div>
			<div id="comment-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>评论：</p>
				<p class="score">'.$commentsum.'</p>
			</div>
			<div id="like-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>点赞：</p>
				<p class="score">'.$like_course->count.'</p>
			</div>
			<div id="star-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>星评：</p>
				<p class="score">'.$score_course->count.'</p>
			</div>
			<div id="medal-data" class="data-box"style="float: left;width:20%; height:50px; float:left;">
				<p>证书：</p>
				<p class="score">'.$badges->count.'</p>
			</div>
		</div>
	';
}

function echo_piechar($personid,$sql){
	global $DB;
	$coursecounts = $DB -> get_records_sql('
	select 
	a.courseid,b.fullname,
	count(a.courseid) AS count 
	from mdl_logstore_standard_log a
	join mdl_course b on b.id=a.courseid
	where a.userid='.$personid.' '.$sql.' and a.courseid in (
		select
		DISTINCT c.id
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		where a.userid='.$personid.'
	)
	GROUP BY a.courseid
	ORDER BY count DESC
	');
	if(count($coursecounts)==0){
		echo '</br></br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无课程学习数据</br></br>';
		return 0;
	}
	else{
		$sumcount=0;
	$output = '
		<!--饼状图 第一个数字是总数-->
		<div style="width: 100%; margin: 0 auto;">
			<table id=\'piechart\'>
				<caption>
					课程学习比例</caption>
				<thead>
					<tr>
						<th></th>';
	foreach($coursecounts as $course){
		$sumcount+=$course->count;
		$output .= '<th>'.$course->fullname.'</th>';
	}

	$output .= '
			</tr>
		</thead>
		<tbody>
			<tr>
			<th>'.$sumcount.'</th>';
	foreach($coursecounts as $course){
		$output .= '<td>'.$course->count.'</td>';
	}
	$output .= '
					</tr>
				</tbody>
			</table>
		</div>
		<!--饼状图 end-->
	';
	echo $output;
	return 1;//有值
	}
	
}

?>


<script type="text/javascript">
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

<!--饼状图-->
<script>
	gvChartInit();
	$(document).ready(function() {
		if(<?php echo $haspiechar;?>){
			$('#piechart').gvChart({
				chartType: 'PieChart',
				gvSettings: {
					vAxis: {
						title: 'No of players'
					},
					hAxis: {
						title: 'Month'
					},
					width: 700,
					height: 400
				}
			});
		}
		//课程排序下拉菜单点击局部刷新事件
		$('#learnreportkindslist li').click(function(){ 
			$('.lockpage').show();		
			$(this).parent().parent().parent().children('.learnreportkinds').val($(this).text());
			 // alert($(this).val());
			if($(this).val()==1){
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index.php');
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').parent('.right-banner').load('mybookdata/index.php');
			}
			else{
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid='+$(this).val());
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid='+$(this).val());
			}
			// $(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index2.html');
		});	
		//课程排序下拉菜单点击局部刷新事件 end
		
		
		$('#note-data').click(function(){      //查看笔记数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/note_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/note_data.php');
		});
		$('#comment-data').click(function(){   //查看评论数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/comment_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/comment_data.php');
		});
		$('#like-data').click(function(){      //查看点赞数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/like_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/like_data.php');
		});
		$('#star-data').click(function(){      //查看星评数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/star_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/star_data.php');
		});
		$('#medal-data').click(function(){     //查看勋章数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/medal_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/medal_data.php');
		});
	});
</script>
<!--饼状图 end-->