<script>
$('.lockpage').hide();
index_flag = 3;//微阅统计首页
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
	.maininfo-box {width: 100%;box-sizing: border-box;background-color: #FFFFFF; height: 665px;overflow-y:scroll;border: 1px solid #ccc; padding: 0px 20px 50px 20px;}
	.maininfo-box h3 {color: #777777; font-size: 23px;}
	.maininfo-box .line {width: 100%; height: 2px; background-color: #CCCCCC;}
	.bookdata-box {width:100%;height: 50px; margin-top: 15px;}
	/*.bookdata-box p {font-size: 16px; color: #777777; float: left; margin: 12px 0px 0px 50px;}*/
	.bookdata-box p {font-size: 16px; color: #777777;  text-align: center; line-height: 50px; text-overflow: ellipsis;overflow: hidden;white-space: nowrap;}
	.bookdata-box .score {font-size: 25px; color: #449D44;margin: 5px 0px 0px 0px;}
	
	.learningsituation-box {width: 100%; margin-top: 20px;}
	
	.learningsituation-box h5,.learningsituation-box h3 {float: left;}
	.learningsituation-box h5 {font-size: 14px; color: #CCCCCC;margin-left: 20px;}
	.highcharts-button{display: none;}
	text tspan{font-family: "微软雅黑";}

	.data-box{width:16.66%; height:50px; border-right:1px solid #CCCCCC; float:left;}
	.data-box:last-child{border-right:0px}
	.data-box:hover {background-color: #F0F0F0; cursor: pointer;}
</style>

<?php
/**
 * 个人中心》台账数据》微阅统计》index 页面
 */
require_once("../../config.php");
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间

if($start_time==0 || $end_time==0){//如果时间为空
	$time = handle_time($start_time,$end_time);
	$start_time = $time['start_time'];
	$end_time = $time['end_time'];
}

//默认显示7天学习统计
if(!isset($_GET["start_time"]) && !isset($_GET["end_time"])){
	$datas = get_detial_data();//输出评论、星评、上传和审核状态等信息
	echo_detial($datas["detials"]);

	$Histogram_data = echo_week_learn($USER->id);//获取折线图数据  显示学时，不是课时

	$learntitle = '七日学习情况';
	$mytime= time()-3600*24*7;
	$sql='and a.timecreated>'.$mytime;
	$ebookBrowseCounts =  get_piechart_data($sql);//获取饼状图数据
}
else{
	$datas = get_detial_data_by_time($start_time,$end_time);
	echo_detial($datas["detials"]);

	if( ($end_time-$start_time) > 86400 ){//如果查询时间段大于一天，则显示折线图
		$Histogram_data = echo_time_learn($start_time,$end_time);//获取折线图数据
	}

	$learntitle = '学习情况';
	$time_sql = handle_field_time_sql('a.timecreated',$start_time,$end_time);
	$ebookBrowseCounts =  get_piechart_data($time_sql);//获取饼状图数据
}


echo '<div class="learningsituation-box">
		<div class="head-box2"><h3>'.$learntitle.'</h3></div>';

$haspiechar = echo_piechar($ebookBrowseCounts);//输出饼状图

echo '
		<!--折线图-->
		<h5>单位：学时</h5>
		<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
	</div>
</div>';




//*******************************************************************


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
function echo_time_learn($start_time,$end_time){
	global $USER;
	$personid = $USER->id;
	$day_onlinetime='';//存放每点的数据
	$day_week = '""';//存放每点的日期
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

/**
 * 根据起止时间来查询基础数据 笔记、评论、……
 * @param $startTime
 * @param $endTime
 * @return array
 */
function get_detial_data_by_time($startTime,$endTime){
	global $USER;
	global $DB;
	$commenttime_sql = handle_field_time_sql('commenttime',$startTime,$endTime);
	$scoretime_sql = handle_field_time_sql('scoretime',$startTime,$endTime);
	$uploadtime_sql = handle_field_time_sql('timecreated',$startTime,$endTime);

	//查询评论、星评、上传和审核状态
	$sql = "SELECT * from
			(SELECT SUM(count) as commentcount FROM
				(SELECT count(1) AS count FROM mdl_doc_comment_my d WHERE d.userid = $USER->id $commenttime_sql
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_comment_my e WHERE e.userid = $USER->id $commenttime_sql
				) as temp
			) as a,
			(SELECT SUM(count) as scorecount FROM
				(SELECT count(1) AS count FROM mdl_doc_score_my d WHERE d.userid = $USER->id $scoretime_sql
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_score_my e WHERE e.userid = $USER->id $scoretime_sql
				) as temp
			) as b,
			(SELECT SUM(count) as uploadcount FROM
				(SELECT count(1) AS count FROM mdl_doc_user_upload_my d WHERE d.upload_userid = $USER->id $uploadtime_sql
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_user_upload_my e WHERE e.uploaderid = $USER->id $uploadtime_sql
					UNION ALL
					SELECT count(1) AS count FROM mdl_pic_user_upload_my p WHERE p.uploaderid = $USER->id $uploadtime_sql
				) as temp
			) as c
			";
	$detials = $DB->get_record_sql($sql);

	return array("detials"=>$detials);
}

/**
 * 获取饼状图数据
 * @param $time_sql 时间查询条件
 * @return array
 */
function get_piechart_data($time_sql){
	global $DB;
	global $USER;
	$sql = "select e.id,e.`name` as ebookname,count(1) as count from mdl_microread_log a
				left join mdl_ebook_my e on e.id = a.contextid
				where a.action = 'view'
				and a.target = 1
				and a.userid  = $USER->id
				$time_sql
				GROUP BY e.id
				ORDER BY count DESC
			";
	$ebookBrowseCounts = $DB -> get_records_sql($sql);

	return $ebookBrowseCounts;
}

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
 * 获取基本信息数据,默认全部
 */
function get_detial_data(){
	global $USER;
	global $DB;
	//查询评论、星评、上传和审核状态
	$sql = "SELECT * from
			(SELECT SUM(count) as commentcount FROM
				(SELECT count(1) AS count FROM mdl_doc_comment_my d WHERE d.userid = $USER->id
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_comment_my e WHERE e.userid = $USER->id
				) as temp
			) as a,
			(SELECT SUM(count) as scorecount FROM
				(SELECT count(1) AS count FROM mdl_doc_score_my d WHERE d.userid = $USER->id
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_score_my e WHERE e.userid = $USER->id
				) as temp
			) as b,
			(SELECT SUM(count) as uploadcount FROM
				(SELECT count(1) AS count FROM mdl_doc_user_upload_my d WHERE d.upload_userid = $USER->id
					UNION ALL
					SELECT count(1) AS count FROM mdl_ebook_user_upload_my e WHERE e.uploaderid = $USER->id
					UNION ALL
					SELECT count(1) AS count FROM mdl_pic_user_upload_my p WHERE p.uploaderid = $USER->id
				) as temp
			) as c
			";
	$detials = $DB->get_record_sql($sql);

	return array("detials"=>$detials);
}

/**Start 输出基本信息:评论、星评、上传和审核状态 */
function echo_detial($detials){

	echo '
		<div class="maininfo-box">
	<!--新加下拉菜单 修改-->
	<div class="head-box1">
		<h3>微阅学习报告</h3>
	</div>
	<!--新加下拉菜单 修改 end-->
	<!--Start 评论、星评、上传和审核状态-->
		<div class="line"></div>
		<div class="bookdata-box">
			<div id="comment-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>评论：<span class="score">'.$detials->commentcount.'</span></p>
			</div>
			<div id="starComment-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>星评：<span class="score">'.$detials->scorecount.'</span></p>
			</div>
			<div id="upload-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>上传：<span class="score">'.$detials->uploadcount.'</span></p>
			</div>
		</div>
	<!--end 评论、星评、上传和审核状态-->
	';
}
/**end 输出基本信息:评论、星评、上传和审核状态 */

/**Start 饼状图 处理图书和在所看图书中的比例 */
function echo_piechar($ebookBrowseCounts){

	if(count($ebookBrowseCounts)==0){
		echo '</br></br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;无微阅学习比例数据</br></br>';
		return 0;
	}
	else{
		$sumcount=0;
		$output = '
			<!--饼状图 第一个数字是总数-->
			<div style="width: 100%; margin: 0 auto;">
				<table id=\'piechart\'>
					<caption>
						书库书籍学习比例</caption>
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

/**Start 输出7日学时情况*/
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
/**end 输出7日学时情况*/

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

<script>
	gvChartInit();
	$(document).ready(function() {
		//Start 图形控制
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
		//end 图形控制

		//课程排序下拉菜单点击局部刷新事件
//		$('#learnreportkindslist li').click(function(){
//			$('.lockpage').show();
//			$(this).parent().parent().parent().children('.learnreportkinds').val($(this).text());
//			 // alert($(this).val());
//			if($(this).val()==1){
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index.php');
//			}
//			else{
//				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid='+$(this).val());
//			}
//			// $(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index2.html');
//		});
		//课程排序下拉菜单点击局部刷新事件 end
		
		/** Start 评论、星评、上传和审核状态的链接 */
		$('#comment-data').click(function(){   //查看评论数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/microreadComment_data.php');
//			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadComment_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadComment_data.php?start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
		});
		$('#starComment-data').click(function(){   //查看星评数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/starComment_data.php');
//			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadStarComment_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadStarComment_data.php?start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
		});
		$('#upload-data').click(function(){     //查看上传数据详情
			$('.lockpage').show();
//			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/upload_data.php');
//			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadUpload_data.php');
			$(this).parent().parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/microreadUpload_data.php?start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
		});
		/** end 评论、星评、上传和审核状态的链接 */

	});
</script>
