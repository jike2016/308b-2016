<script>
$('.lockpage').hide();
</script>
<?php
require_once("../../config.php");
//$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
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
echo '<div class="table_title_lg">'.$user[$personid]->lastname.$user[$personid]->firstname.'：考试统计</div>';

//$mytime = 0;
//if($timeid==1){
//	$mytime= time()-3600*24*7;
//	// $sql='and a.timecreated>'.$mytime;
//}
//elseif($timeid==2){
//	$mytime= time()-3600*24*30;
//	// $sql='and a.timecreated>'.$mytime;
//}
//elseif($timeid==3){
//	// $sql='';
//}
//echo_quiztabel($personid,$mytime);//按照周、月、总来查询
echo_quiztabel($personid,$start_time,$end_time);//按照时间段来查询

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

//function echo_quiztabel($personid,$mytime){
function echo_quiztabel($personid,$start_time,$end_time){
	global $DB;
	$quizcounts = $DB -> get_records_sql('
		select
		d.id,c.fullname,d.typeofquiz,e.timefinish, d.name,h.grade
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_quiz_attempts e on e.quiz=d.id and e.userid='.$personid.' and e.state=\'finished\'
		join mdl_quiz_grades h on h.quiz=d.id and h.userid='.$personid.' and h.timemodified > '.$start_time.' and h.timemodified < '.$end_time.'
		where a.userid='.$personid.' and d.timeopen!=0 and d.timeclose!=0 and d.attempts=1 and d.typeofquiz in (1,2)
		order by e.timefinish desc
	');
	
	echo '
	<div  class="table_text_center one_table ">
	<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<td>序号</td>
			<td>课程名称</td>
			<td>考试名称</td>
			<td>考试类型</td>
			<td>考试时间</td>
			<td>成绩</td>
		</tr>
	</thead>
	<tbody>
	';
	$n=1;
	foreach($quizcounts as $quiz){
		if($quiz->typeofquiz==1){
			$quiz->typeofquiz='统一考试';
		}else{
			$quiz->typeofquiz='自主考试';
		}
		echo '<tr>
			<td>'.$n.'</td>
			<td>'.$quiz->fullname.'</td>
			<td>'.$quiz->name.'</td>
			<td>'.$quiz->typeofquiz.'</td>
			<td>'.userdate($quiz->timefinish,'%Y-%m-%d %H:%M').'</td>
			<td>'.$quiz->grade.'</td>
		</tr>
		';
	$n++;
	}
	
	echo '
	</tbody>
</table>
</div>';
}


?>
