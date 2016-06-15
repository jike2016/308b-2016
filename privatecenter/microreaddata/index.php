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
	
	.maininfo-box {width: 100%;background-color: #FFFFFF; min-height: 665px; border: 1px solid #ccc; padding: 0px 20px 20px 20px;}
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

echo_detial();//输出评论、星评、上传和审核状态等信息
$Histogram_data = echo_week_learn($USER->id);//折线图

echo '<div class="learningsituation-box">
		<div class="head-box2"><h3>七日学习情况</h3></div>';

		$mytime= time()-3600*24*7;
		$sql='and a.timecreated>'.$mytime;
		global $USER;
		$haspiechar = echo_piechar($USER->id,$sql);//饼状图

echo '
		<!--折线图-->
		<h5>单位：课时</h5>
		<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
	</div>
</div>';



/**Start 输出基本信息:评论、星评、上传和审核状态 */
function echo_detial(){
	global $USER;
	global $DB;
	//查询评论、星评、上传和审核状态
	$comment_doc = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_doc_comment_my d WHERE d.userid = $USER->id");
	$comment_ebook = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_ebook_comment_my e WHERE e.userid = $USER->id");
	$commentsum = $comment_doc->count + $comment_ebook->count;//文库和书库‘评论’之和
	$starComment_doc = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_doc_score_my d WHERE d.userid = $USER->id");
	$starComment_ebook = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_ebook_score_my e WHERE e.userid = $USER->id");
	$starCommentsum = $starComment_doc->count + $starComment_ebook->count;//文库和书库‘星评’之和
	$upload_doc = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_doc_user_upload_my d WHERE d.upload_userid = $USER->id");
	$upload_ebook = $DB->get_record_sql("SELECT count(1) AS count FROM mdl_ebook_user_upload_my e WHERE e.uploaderid = $USER->id");
	$uploadsum = $upload_doc->count + $upload_ebook->count;//文库和书库‘上传’之和

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
				<p>评论：</p>
				<p class="score">'.$commentsum.'</p>
			</div>
			<div id="starComment-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>星评：</p>
				<p class="score">'.$starCommentsum.'</p>
			</div>
			<div id="upload-data" class="data-box"style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
				<p>上传：</p>
				<p class="score">'.$uploadsum.'</p>
			</div>
		</div>
	<!--end 评论、星评、上传和审核状态-->
	';
}
/**end 输出基本信息:评论、星评、上传和审核状态 */

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
					<caption>
						书籍学习比例</caption>
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
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('microreaddata/comment_data.php');
		});	
		$('#starComment-data').click(function(){   //查看星评数据详情
			$('.lockpage').show();
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('microreaddata/starComment_data.php');
		});
		$('#upload-data').click(function(){     //查看上传数据详情
			$('.lockpage').show();
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('microreaddata/upload_data.php');
		});
		/** end 评论、星评、上传和审核状态的链接 */

	});
</script>
