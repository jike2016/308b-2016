
<style>

	table th {text-align: center;}
	table {text-align: center;}
	table {margin-top: 15px;}

	.table > thead > tr > th { padding: 4px;}
	.table > thead > tr > th, .table > tbody > tr > th { vertical-align: middle;}
	.checkexam {height: 24px; width: 100px; padding: 0px;}
	.no {width: 10%;}
	.courseName {width: 30%;}
	.state {width: 20%;}
	.completeTime {width: 30%;}

</style>


<?php

/**
 * 徐东威 台账数据中心 台账任务统计
 */
require_once("../../config.php");
$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$personid = optional_param('personid', 0, PARAM_INT);//人员id

global $DB;

$mytime = 0;
if($timeid==1){
	$mytime= time()-3600*24*7;//从当前时间往前推一周
	$sql = ' and mm.time_start > '.$mytime; //任务的开始时间 与 当前时间比较
}
elseif($timeid==2){
	$mytime= time()-3600*24*30;//从当前时间往前推一月
	$sql = ' and mm.time_start > '.$mytime;//任务的开始时间 与 当前时间比较
}
elseif($timeid==3){//总
	$sql='';
}


//获取该人员的任务
$misisons = $DB->get_records_sql("select * from mdl_mission_user_my mum where  mum.user_id = $personid");

//Start 对每个任务进行分析
$index = 0;
foreach($misisons as $misison){
	//获取具体的任务，（在此筛选查看时间内的任务）
	$missionDetail = $DB->get_record_sql("select mm.id,mm.mission_name,mm.required_course_id,mm.optional_course_id,mm.time_start,mm.time_end from mdl_mission_my mm where mm.id = $misison->mission_id  ".$sql);
	if($index){
		echo '<hr style="height:5px;border:none;border-top:5px ridge #000000;" /><br/>';
	}
	if($missionDetail){//如果该任务在查看时间内，就显示
		show_complete($missionDetail,$personid);//显示任务的完成情况
		$index = 1;
	}
}
//End 对每个任务进行分析




/** START 任务完成状态
 *
 * @param  $missionDetail 任务内容
 * @param 	$personid	任务人员id
 */
function show_complete($missionDetail,$personid){

	global $DB;

	$missionName = $missionDetail->mission_name;//任务名称
	$missionEndTime = $missionDetail->time_end;//任务截止时间
	$requiredCouresID = $missionDetail->required_course_id;//必修课
	$optionalCouresID = $missionDetail->optional_course_id;//选修课

	$requiredCoures = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($requiredCouresID)");//必修课程
	$optionalCoures = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($optionalCouresID)");//选修课程

	$html1 = echo_courseStateTable($requiredCoures,$personid,$missionEndTime);//必修课任务分析
	$html2 = echo_courseStateTable($optionalCoures,$personid,$missionEndTime);//选修课任务分析

	$missionState = '未完成';
	if($html1["flag"] && $html2["flag"]) {
		$missionState = '已完成';
	}
	echo "<h2>$missionName( $missionState)</h2>";
	//START 输出必修课任务状态
	$state1 = '未完成';
	if($html1["flag"] ){
		$state1 = '已完成';
	}
	echo ' <div>
			<h4 style="color: red;">必修课明细('.$state1.')</h4>
		</div>';
	echo $html1["htmltable"];
	//END 输出必修课任务状态

	//START 输出选修课任务状态
	$state2 = '未完成';
	if($html2["flag"] ){
		$state2 = '已完成';
	}
	echo '<div>
			<h4 style="color: #0000EE;">选修课明细('.$state2.')</h4>
	   </div>';
	echo $html2["htmltable"];
	//END 输出选修课任务状态

}
/** END 任务完成状态 */


/** START 表格输出
 *
 * @param  $courses  课程任务
 * @param 	$personid  任务人员id
 * @param 	$missionEndTime 任务截止时间
 * @return 	$html 数组，包含：$htmltable 表格数据html；$flag 完成状态: 0  未完成，1 完成
 */
function echo_courseStateTable($courses,$personid,$missionEndTime){

	$flag = 1;//完成状态的返回值

	$table = new html_table();//定义表格
	$table->attributes['class'] = 'collection';
	$table->attributes['class'] = 'table table-striped table-bordered';
	$table->head = array(
		'序号',
		'课程名称',
		'状态',
		'完成时间'
	);
	$table->colclasses = array('no', 'courseName', 'state','completeTime');//定义列的绑定名

	global $DB;

	$no = 1;//序号
	foreach($courses as $course){

		$courseName = $course->fullname;
		$state = '未完成';
		$completeTime = '——';//完成时间
		$flag2 = 0;//对每个课程完成状态的标记

		$completeState = $DB->get_record_sql("select c.id,c.timecompleted from mdl_course_completion_crit_compl c where c.userid = $personid and c.course = $course->id");
		if($completeState){//如果有记录

			$time = $completeState->timecompleted;
			$completeTime = userdate($time,'%Y-%m-%d %H:%M');
			$state = '超时';
			if($time < $missionEndTime){ //如果课程完成时间 < 任务截止时间
				$state = '完成';
				$flag2 = 1;//将完成状态赋值1
			}
		}

		//将各字段的数据填充到表格相应的位置中
		$row = array($no,$courseName,$state,$completeTime);//绑定数据
		$table->data[] = $row;

		$flag = $flag * $flag2;//对所有课程的完成状态的统计，如果有一门课程是0，则表明该子任务没有将所有课程完成
		$no++;//序号自增
	}

	$htmltable = html_writer::table($table);//表格数据html

	$html = array('htmltable'=>$htmltable,'flag'=>$flag);

	return $html;//完成状态的返回值
}
/** END 表格输出 */


























