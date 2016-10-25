<?php
/** 台账数据中心》个人微阅数据统计 xdw */

require_once("../../config.php");
$personid = optional_param('personid', 0, PARAM_INT);
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间

if($start_time==0 || $end_time==0){//如果时间为空
	$time = handle_time($start_time,$end_time);
	$start_time = $time['start_time'];
	$end_time = $time['end_time'];
}

global $DB;
$user = $DB -> get_records_sql('select id,lastname,firstname from mdl_user where id='.$personid);
echo '<div class="table_title_lg">'.$user[$personid]->lastname.$user[$personid]->firstname.'：微阅统计</div>';

//用开始和结束时间段来查询
$sql="and a.timecreated >= $start_time and  a.timecreated <= $end_time ";


/**Start 输出饼状图 */
echo '<div class="table_title">书库学习比例：</div>';
$haspiechar = echo_piechar($personid,$sql);
/**End 输出饼状图 */

/** Start 输出柱状图 */
echo '<div class="table_title">学习事件：</div>';
$categories_date = "'文库浏览数', '文库上传数', '文库通过审阅数', '书库浏览数', '书库上传数', '书库通过审阅数', '图库上传数', '图库通过审阅数','评论数','星评数'";
$histogramcounts = echo_histogram($personid,$start_time,$end_time);//按照时间段查询
/** End 输出柱状图 */

/** Start 输出折线图 */
if( ($end_time-$start_time) > 86400 ){//如果查询时间段大于一天，则显示折线图
	$Histogram_data = echo_week_learn($personid,$start_time,$end_time);//按照时间段显示
}
/** End 输出折线图 */



//////////////////////////////////////////////////////////////////////


/**
 * 查询时间判断
 * @param $start_time
 * @param $end_time
 * @return array
 */
function handle_time($start_time,$end_time){

	global $DB;
	$minTime = $DB->get_record_sql("select MIN(l.timecreated) as mintime from mdl_logstore_standard_log l");
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

/**Start 饼状图 根据用户id，时间，查询图书和在所看图书中的比例 */
function echo_piechar($personid,$sql){
	
	global $DB;
	$ebookBrowseCounts = $DB -> get_records_sql("
		select e.id,e.`name` as ebookname,count(1) as count from mdl_microread_log a
			left join mdl_ebook_my e on e.id = a.contextid
			where a.action = 'view'
			and a.target = 1
			and a.userid  = $personid
			$sql
			GROUP BY e.id
			ORDER BY count DESC
	");

	if(count($ebookBrowseCounts)==0){
		echo '</br></br>无微阅学习数据';
		return 0;
	}
	else{
		$sumcount=0;
		$output = '
			<!--饼状图 第一个数字是总数-->
			<div style="width: 100%; margin: 0 auto;">
				<table id=\'piechart\'>
					<caption></caption>
					<thead>
						<tr>
							<th></th>';
		foreach($ebookBrowseCounts as $ebookBrowseCount){
			$sumcount+=$ebookBrowseCount->count;
			$output .= '<th>'.$ebookBrowseCount->ebookname.'</th>';
		}

		$output .= '
				</tr>
			</thead>
			<tbody>
				<tr>
				<th>'.$sumcount.'</th>';
		foreach($ebookBrowseCounts as $ebookBrowseCount){
			$output .= '<td>'.$ebookBrowseCount->count.'</td>';
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
/**End  饼状图 根据用户id，时间，查询图书和在所看图书中的比例 */

/** Start 柱状图数据查询
 * “文库”：浏览文档数、文库上传数、文库通过审阅数；
 * “书库”：上传数、书库通过审阅数；
 * “图库”：上传数、图库通过审阅数；
 * “文库”与“书库”：评论数、星评数
 */
function echo_histogram($personid,$start_time,$end_time){

	//文库浏览文档数
	$sql = "and m.timecreated > $start_time and m.timecreated < $end_time";
//	$doc_browseNum_sql="SELECT 1,count(DISTINCT m.contextid) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 2 AND m.userid = $personid ";
	$doc_browseNum_sql="SELECT 1,count(1) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 2 AND m.userid = $personid ";
	//书库浏览文档数
	$sql = "and m.timecreated > $start_time and m.timecreated < $end_time";
//	$book_browseNum_sql="SELECT 1,count(DISTINCT m.contextid) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 1 AND m.userid = $personid ";
	$book_browseNum_sql="SELECT 1,count(1) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 1 AND m.userid = $personid ";
	//图库浏览文档数
	$sql = "and m.timecreated > $start_time and m.timecreated < $end_time";
//	$pic_browseNum_sql="SELECT 1,count(DISTINCT m.contextid) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 3 AND m.userid = $personid ";
	$pic_browseNum_sql="SELECT 1,count(1) as count FROM mdl_microread_log m WHERE m.action = 'view' $sql AND m.target = 3 AND m.userid = $personid ";

	//文库上传数、通过审阅数
	$sql = "and d.timecreated > $start_time and d.timecreated < $end_time";
	$doc_upload_sql = "SELECT 1,count(1) as count FROM mdl_doc_user_upload_my d WHERE d.upload_userid = $personid $sql";
	$doc_passCheck_sql = "SELECT 1,count(1) as count FROM mdl_doc_user_upload_my d WHERE d.admin_check = 1 AND d.upload_userid = $personid $sql";
	// 书库上传数、书库通过审阅数
	$sql = "and e.timecreated > $start_time and e.timecreated < $end_time";
	$ebook_upload_sql="SELECT 1,count(1) as count FROM mdl_ebook_user_upload_my e where e.uploaderid = $personid $sql ";
	$ebook_passCheck_sql="SELECT 1,count(1) as count FROM mdl_ebook_user_upload_my e where e.uploaderid = $personid and e.admin_check = 1 $sql ";
	// 图库上传数、图库通过审阅数
	$sql = "and p.timecreated > $start_time and p.timecreated < $end_time";
	$pic_upload_sql="SELECT 1,count(1) as count FROM mdl_pic_user_upload_my p where p.uploaderid = $personid $sql ";
	$pic_passCheck_sql="SELECT 1,count(1) as count FROM mdl_pic_user_upload_my p where p.uploaderid = $personid and p.admin_check = 1 $sql ";
	//评论数
	$sql1 = "and d.commenttime > $start_time and d.commenttime < $end_time";
	$sql2 = "and e.commenttime > $start_time and e.commenttime < $end_time";
	$comment_sql = "SELECT 1,SUM(table_comment.count) as count FROM(
					SELECT count(1) as count from mdl_doc_comment_my d where d.userid = $personid $sql1
					UNION ALL
					SELECT count(1) as count from mdl_ebook_comment_my e where e.userid = $personid $sql2
					) as table_comment";
	//星评数
	$sql1 = "and d.scoretime > $start_time and d.scoretime < $end_time";
	$sql2 = "and e.scoretime > $start_time and e.scoretime < $end_time";
	$star_comment_sql = "SELECT 1,SUM(table_star_comment.count) as count FROM(
						SELECT count(1) as count FROM mdl_doc_score_my d WHERE d.userid = $personid $sql1
						UNION ALL
						SELECT count(1) as count FROM mdl_ebook_score_my e WHERE e.userid = $personid $sql2
						) as table_star_comment";

	global $DB;
	$histogramcounts = '';
	//文库浏览数
	$doc_browseNum = $DB -> get_records_sql($doc_browseNum_sql);
	$histogramcounts .= $doc_browseNum[1]->count.', ';
	//文库上传数
	$doc_upload = $DB -> get_records_sql($doc_upload_sql);
	$histogramcounts .= $doc_upload[1]->count.', ';
	//文库通过审查数
	$doc_passCheck = $DB -> get_records_sql($doc_passCheck_sql);
	$histogramcounts .= $doc_passCheck[1]->count.', ';
	//书库浏览数
	$book_browseNum = $DB -> get_records_sql($book_browseNum_sql);
	$histogramcounts .= $book_browseNum[1]->count.', ';
	//书库上传数
	$ebook_upload = $DB -> get_records_sql($ebook_upload_sql);
	$histogramcounts .= $ebook_upload[1]->count.', ';
	//书库通过审查数
	$ebook_passCheck = $DB -> get_records_sql($ebook_passCheck_sql);
	$histogramcounts .= $ebook_passCheck[1]->count.', ';
	//图库浏览数
//	$pic_browseNum = $DB -> get_records_sql($pic_browseNum_sql);
//	$histogramcounts .= $pic_browseNum[1]->count.', ';
	//图库上传数
	$pic_upload = $DB -> get_records_sql($pic_upload_sql);
	$histogramcounts .= $pic_upload[1]->count.', ';
	//图库通过审查数
	$pic_passCheck = $DB -> get_records_sql($pic_passCheck_sql);
	$histogramcounts .= $pic_passCheck[1]->count.', ';
	//评论数
	$comment = $DB -> get_records_sql($comment_sql);
	$histogramcounts .= $comment[1]->count.', ';
	//星评数
	$star_comment = $DB -> get_records_sql($star_comment_sql);
	$histogramcounts .= $star_comment[1]->count.' ';

	return $histogramcounts;
}
/**End 柱状图数据查询*/

/**start  计算时间段内学习时间
 * 获取时间段内事件数目，每个数目乘上对应的（固定）时间
 * 设定： 微阅书库平均阅读时长》45秒
 * 		  微阅文库平均阅读时长》60秒
 */
function calculate_day_onlinetime($records){

	$ebook_time = 45;//微阅书库平均阅读时长
	$doc_time = 60;//微阅文库平均阅读时长

	$sumtime = 0;//总时间
	foreach($records as $record){
		if($record->type == 1){//书库
			$sumtime += $ebook_time * $record->recordcount;
		}
		elseif($record->type == 2){//文库
			$sumtime += $doc_time * $record->recordcount;
		}
	}
	return round($sumtime/3600,1);//小时
}
/** end */

/** Start 统计时间段内的学习时间
 * 统计时间段内微阅中各部分的浏览的点击事件次数
 */
function handler_day_onlinetime($starttime,$endtime,$personid){

	global $DB;
	$records = $DB->get_records_sql("
		SELECT m.target AS type,COUNT(1) AS recordcount from mdl_microread_log m
		WHERE m.action = 'view' AND m.target in (1,2) AND m.userid = $personid
		AND m.timecreated > $starttime AND m.timecreated < $endtime
		GROUP BY m.target
	");
	return calculate_day_onlinetime($records);
}
/**End 统计时间段内的学习时间*/

/**Start （折线图，按照时间段显示）xdw 20160525*/
//只提供
//日折线：时间段在31天以内
//月折线：时间段大于31天
//从结束时间往前开始计算
function echo_week_learn($personid,$start_time,$end_time){

	$day_onlinetime='';//存放折线图中每点的数据（纵坐标）
	$day_week = '""';//存放折线图中每点的日期（横坐标）
	$timeslot = $end_time - $start_time;//查询的时间段
	//时间段长度的划分
	if($timeslot > 2678400){//如果( 时间段 > 一个月)，用月线显示
		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m', $endtime).'-01'); // 结束时间当月1号的零点
		$day_week = '"'.date('Y-m', $starttime).'"';//存放每点的日期
		$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$personid).','.$day_onlinetime;
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
			$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$personid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m', $starttime).'",'.$day_week;
			$timeslot = $timeslot - $monthtimeslot;//时间段减去月时间段。同时，这里是循环的出口
		}

	}elseif($timeslot >= 86400){//如果( 一天 < 时间段 < 一个月)，用日线显示

		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m-d', $endtime)); // 结束时间当天的零点
		$day_week = '"'.date('Y-m-d', $starttime).'"';//存放每点的日期
		$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$personid).','.$day_onlinetime;
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
			$day_onlinetime = handler_day_onlinetime($starttime,$endtime,$personid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m-d', $starttime).'",'.$day_week;
			$timeslot = $timeslot - 86400;//时间段减去一天。同时，这里是循环的出口

		}
	}
	return array($day_week,$day_onlinetime);
}
/**End*/

?>

<!--Start 饼状图 -->
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
<!--end 饼状图 -->

<!--Start 柱状图、折线图 -->
<script type="text/javascript">

	/** Start 柱状图 */
	$(function() {
		$('#Histogram').highcharts({
			chart: {
				type: 'bar'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: [ <?php echo $categories_date; ?>],
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
	/** End 柱状图 */

	/** Start 折线图 */
	if(<?php if($end_time - $start_time >= 86400)echo 'true';else echo 'false';?>){
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
				name: '学习学时',
				data: [<?php if(isset($Histogram_data))echo $Histogram_data[1];?>]
			}]
		});
	});
	}
	/** End 折线图 */

</script>
<!--end  柱状图、折线图 -->

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


<!-- Start 柱状图-->
<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
<!-- end 柱状图 -->


<?php
/** Start 输出折线图 */
if($end_time - $start_time > 2678400){
	echo '</br></br></br><div class="table_title" >学习情况（月）：</div></br></br>
			<P>单位：学时</P>
		<div class="learningsituation-box">
			<!--折线图-->
			<div id="Histogram2" style="width: 100%; height: 400px; margin: 0 auto"></div>
			<!--折线图 end-->
		</div>';
}elseif($end_time - $start_time > 86400){
	echo '</br></br></br><div class="table_title" >学习情况（日）：</div></br></br>
			<P>单位：学时</P>
		<div class="learningsituation-box">
			<!--折线图-->
			<div id="Histogram2" style="width: 100%; height: 400px; margin: 0 auto"></div>
			<!--折线图 end-->
		</div>';
}
/** End 输出折线图 */

?>
