<script>
$('.lockpage').hide();
</script>
<style>
	.kinds {margin-bottom: 5px;}
	.maininfo-box {background-color: #FFFFFF; height: 665px;}
	.classinfo-box {min-height: 615px; padding: 0px;}
	.footer-box {background-color: #F0F0F0;  border: 1px solid #ccc; border-top: 0px;}
	.footer { background-color: #F0F0F0; width: 60%; margin: auto; height: 48px; padding: 15px 0px;}
	.footer p {display: inline-block; color: #5E5E5E;}
	.footer .p-14-red {color: #C9302C;}
	.footer .right,.center {float: right;}
	.footer .right{margin-right: 60px;}
	.footer a {margin-right: 40px;}	
	table td {text-align: center;}
	table {margin-top: 0px;}
	table thead {background-color: #F0F0F0; padding: 5px;}
	table thead tr {height: 50px; color: #777777;}
	.table > thead > tr > td { padding: 4px;}
	.table > thead > tr > td, .table > tbody > tr > td { vertical-align: middle;}
	.td1 {width: 5%;}
	.td2 {width: 19%;}
	.td3 {width: 23%;}
	.td4 {width: 24%;}
	.td5 {width: 24%;}
	.td6 {width: 5%;}
</style>

<script type="text/javascript">

	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mymission/index.php?page="+page);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mymission/index.php?page="+page);
	});

</script>


<?php
require_once("../../config.php");
$page = optional_param('page', 0, PARAM_INT);
global $DB;
global $USER;

echo_missions($page);//输出任务列表

/**START 输出任务列表 */
function echo_missions($page){

	$offset=($page-1)*10;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	global $DB;
	global $USER;

	$userID = $USER->id;

	echo '<div class="maininfo-box">
	<div class="classinfo-box">
		<table class="table table-hover">
			<thead>
				<tr>
					<td class="td1">序号</td>
					<td class="td2">任务名称</td>
					<td class="td3">必修课</td>
					<td class="td4">选修课</td>
					<td class="td5">时间</td>
					<td class="td6">时间</td>
				</tr>
			</thead>
			<tbody>';

	$missions = $DB->get_records_sql("select * from mdl_mission_my mm join mdl_mission_user_my mum on mm.id = mum.mission_id where mum.user_id = $userID limit $offset,10");//获取该用户10条任务
	$missionscount = $DB->get_record_sql('select count(*) as record_count from mdl_mission_my mm join mdl_mission_user_my mum on mm.id = mum.mission_id where mum.user_id = '.$userID);//获取该用户的所有任务
	$no = ($page-1)*10+1;//序号
	//START 对每个任务的输出
	foreach($missions as $mission){
		$missionName = $mission->mission_name;//任务名称
		$requiredCourseIDs = $mission->required_course_id;//必修课课程id
		$optionalCourseIDs = $mission->optional_course_id;//选修课课程id
		$requiredCourses = $DB->get_records_sql("select * from mdl_course c where c.id in ($requiredCourseIDs) ");//获取必修课课程
		$optionalCourses = $DB->get_records_sql("select * from mdl_course c where c.id in ($optionalCourseIDs) ");//获取选修课课程
		$requiredCourseNames = '';//必修课课程名称
		$optionalCourseNames = '';//选修课课程名称
		$requiredCourseNum = count($requiredCourses);//必修课课程数量
		$optionalCourseNum = count($optionalCourses);//选修课课程数量
		$requiredCount = 1;
		$optionalCount = 1;
		foreach($requiredCourses as $requiredCourse){
			if($requiredCount == $requiredCourseNum){
				$requiredCourseNames .= $requiredCourse->fullname;
			}
			else{
				$requiredCourseNames .= $requiredCourse->fullname.' / ';
			}
			$requiredCount++;
		}
		foreach($optionalCourses as $optionalCourse){
			if($optionalCount == $optionalCourseNum){
				$optionalCourseNames .= $optionalCourse->fullname;
			}
			else{
				$optionalCourseNames .= $optionalCourse->fullname.' / ';
			}
			$optionalCount++;
		}

		$startTime = $mission->time_start;//开始时间
		$endTime = $mission->time_end;//结束时间
		$time = '开始：'.userdate($startTime,'%Y-%m-%d %H:%M').' </br> 结束：'.userdate($endTime,'%Y-%m-%d %H:%M');

		echo '<tr>
				<td>'.$no.'</td>
				<td>'.$missionName.'</td>
				<td>'.$requiredCourseNames.'</td>
				<td>'.$optionalCourseNames.'</td>
				<td>'.$time.'</td>
				<td><a href="../mod/missionmy/showcomplete.php?id='.$mission->mission_id.'&action=details" target="_blank"><button class="btn btn-info checkexam">查看</button></a></td>
			</tr>';
		$no++;

	}//END 对每个任务的输出

	echo '</tbody>
		</table>
	</div>';

	echo_end($page,$missionscount);//输出上下页按钮
}
/**END 输出任务列表 */

/** START 输出上下页按钮等
 * @param  $page 页码
 * @param  $count 中的 $count->record_count 总的记录数
 */
function echo_end($page,$count){

	$total = $count->record_count;//总记录数
	$pagenum=ceil($total/10);//总页数
	echo '
	<div class="footer-box">
		<div class="footer">';
	if($page==1&&($pagenum==1||$pagenum==0)){
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	elseif($page==1){//第一页
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	elseif($page==$pagenum){//最后一页
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	else{
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	if($pagenum==0){
		echo '
				<div class="center">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
			</div>
		</div>
		</div>
		';
	}
	else{
		echo '
				<div class="center">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$page.'</p>
					<p>页</p>
				</div>
			</div>
		</div>
		</div>
		';
	}

}
/**END 输出上下页按钮 */

?>


