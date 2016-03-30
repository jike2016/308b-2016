<?php
require_once("../../config.php");
$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$personid = optional_param('personid', 0, PARAM_INT);

global $DB;
$user = $DB -> get_records_sql('select id,lastname,firstname from mdl_user where id='.$personid);
echo $user[$personid]->lastname.$user[$personid]->firstname;
echo '</br>学习统计';
$mytime = 0;
if($timeid==1){
	$mytime= time()-3600*24*7;
	$sql='and a.timecreated>'.$mytime;
}
elseif($timeid==2){
	$mytime= time()-3600*24*30;
	$sql='and a.timecreated>'.$mytime;
}
elseif($timeid==3){
	$sql='';
}



//输出饼状图
$haspiechar = echo_piechar($personid,$sql);
//输出柱状图
$histogramcounts = echo_histogram($personid,$mytime);
if($timeid==1){
	//输出折线图
	$Histogram_data = echo_week_learn($personid);
}
//根据用户id，时间，查询所有课程和在所有课程操作比例
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
		echo '</br></br>无课程学习数据';
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
//柱状图数据查询 '课程笔记', '个人笔记', '评论', '登录', '收藏', '勋章'
function echo_histogram($personid,$mytime){
	if($mytime!=0){
		$notesql='select notetype,count(1)as count from mdl_note_my where userid='.$personid.' and time >'.$mytime.'  GROUP BY notetype';
		$commentsql='select 1,count(1)as count from mdl_comment_course_my where commenttime>'.$mytime.' and userid='.$personid;
		$collectionsql='select 1,count(1)as count from mdl_collection_my where collectiontime>'.$mytime.' and userid='.$personid;
		$badgesql='select 1,count(1)as count from mdl_badge_issued where dateissued>'.$mytime.' and userid='.$personid;
		$loginsql='select 1,count(1)as count from mdl_logstore_standard_log where timecreated>'.$mytime.' and action=\'loggedin\' and userid='.$personid;
	}
	else{
		$notesql='select notetype,count(1)as count from mdl_note_my where userid='.$personid.' GROUP BY notetype';
		$commentsql='select 1,count(1)as count from mdl_comment_course_my where userid='.$personid;
		$collectionsql='select 1,count(1)as count from mdl_collection_my where userid='.$personid;
		$badgesql='select 1,count(1)as count from mdl_badge_issued where userid='.$personid;
		$loginsql='select 1,count(1)as count from mdl_logstore_standard_log where action=\'loggedin\' and userid='.$personid;
	}
	global $DB;
	$histogramcounts = '';
	//笔记
	$notecounts = $DB -> get_records_sql($notesql);//1:课程笔记2：个人笔记
	
	if(!isset($notecounts[1]->count))
		$histogramcounts .= '0, ';
	else
		$histogramcounts .= $notecounts[1]->count.', ';
	if(!isset($notecounts[2]->count))
		$histogramcounts .= '0, ';
	else
		$histogramcounts .= $notecounts[2]->count.', ';
	
	// $histogramcounts .= $notecounts[2]->count.', ';
	//评论
	$comments = $DB -> get_records_sql($commentsql);//1:课程笔记2：个人笔记
	$histogramcounts .= $comments[1]->count.', ';
	//收藏
	$collections = $DB -> get_records_sql($collectionsql);//1:课程笔记2：个人笔记
	$histogramcounts .= $collections[1]->count.', ';
	//勋章
	$badges = $DB -> get_records_sql($badgesql);//1:课程笔记2：个人笔记
	$histogramcounts .= $badges[1]->count.', ';
	//登录
	$logins = $DB -> get_records_sql($loginsql);//1:课程笔记2：个人笔记
	$histogramcounts .= $logins[1]->count.' ';
	return $histogramcounts;
}



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
function handler_day_onlinetime($starttime,$endtime,$personid){
	// global $USER;
	global $DB;
	$records = $DB->get_records_sql('
		SELECT 
		(
			SELECT COUNT(id) 
			FROM mdl_logstore_standard_log AS tb1 
			WHERE tb1.id<= tb2.id 
			and tb1.userid='.$personid.'		
			and tb1.timecreated > '.$starttime.'
			and tb1.timecreated < '.$endtime.'
		) as tempid, 
		action,timecreated  
		FROM mdl_logstore_standard_log AS tb2 
		where tb2.userid='.$personid.'
		and tb2.timecreated > '.$starttime.'
		and tb2.timecreated < '.$endtime.'
		ORDER BY 1
	');
	return calculate_day_onlinetime($records);
	// return 7;
}
//输出7日学时情况
function echo_week_learn($personid){
	$weekday = array('"周末"','"周一"','"周二"','"周三"','"周四"','"周五"','"周六"'); 
	// echo $weekday[date('w', time())]; 
	$starttime = strtotime(date('Y-m-d', time())); // 当天的零点
	$endtime = time();//当前时间
	$day_onlinetime='';//存放每天的时间
	$day_week ='"今天"';//存放当天的星期名
	for($i=0;$i<7;$i++){
		//查询时间段内的数据
		$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$personid).','.$day_onlinetime;
		//更新时间点
		$endtime =$starttime;
		$starttime = $endtime-86400;//减一天
		if($i<6){
			$day_week = $weekday[date('w', $starttime)].','.$day_week;
		}	
	}
	 return array($day_week,$day_onlinetime);
}
?>

<script>
	$('.lockpage').hide();
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
	});
</script>
<script type="text/javascript">
	$(function() {
		$('#Histogram').highcharts({
			chart: {
				type: 'bar'
			},
			title: {
				text: '学习事件统计'
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: [ '课程笔记', '个人笔记', '评论', '收藏', '勋章', '登录'],
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: '数量',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {
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
			series: [  {
				name: '数量',
				data: [<?php echo $histogramcounts; ?>]
			}]
		});
	});
	if(<?php if($timeid==1)echo 'true';else echo 'false';?>){
		$(function() {
		$('#Histogram2').highcharts({
			
			title: {   //正标题
				text: ' '
			},
			subtitle: { //副标题
				text: ' '
			},
			xAxis: {  //X轴文本
				categories: [<?php if(isset($Histogram_data))echo $Histogram_data[0].'';?>],
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
				data: [<?php if(isset($Histogram_data))echo $Histogram_data[1];?>]
			}]
		});
	});
	}
	
</script>
<style>
	.highcharts-button{
		display: none;
	}
	text tspan{
		font-family: "微软雅黑";
	}
	.maininfo-box {width: 100%;background-color: #FFFFFF; height: 665px; border: 1px solid #ccc; padding: 0px 20px 20px 20px;}
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
</style>


<!--柱状图-->
<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
<!--柱状图 end-->
<?php 
if($timeid==1)
	echo '<div class="learningsituation-box">
		<h3>七日学习情况</h3><h5>单位：课时</h5>
		
		<!--折线图-->
		<div id="Histogram2" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
</div>';
?>

<!--表格
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<td>选择</td>
			<td>名称</td>
			<td>时间</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>1</td>
			<td>政治教育</td>
			<td>2016.1.16</td>
		</tr>
		<tr>
			<td>2</td>
			<td>思想教育</td>
			<td>2016.1.16</td>
		</tr>
	</tbody>
</table>
<!--表格 end-->