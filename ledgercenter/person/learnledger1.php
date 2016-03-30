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
$histogramcounts = echo_histogram($personid,$mytime);

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
					课程学习进度比例</caption>
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
?>

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
</script>
<style>
	.highcharts-button{
		display: none;
	}
	text tspan{
		font-family: "微软雅黑";
	}
</style>


<!--柱状图-->
<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
<!--柱状图 end-->

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