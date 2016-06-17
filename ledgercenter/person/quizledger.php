<script>
$('.lockpage').hide();
</script>
<?php
require_once("../../config.php");
$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$personid = optional_param('personid', 0, PARAM_INT);

global $DB;
$user = $DB -> get_records_sql('select id,lastname,firstname from mdl_user where id='.$personid);
echo '</br><div  align="center" style="margin-left: auto;margin-right: auto;">'.$user[$personid]->lastname.$user[$personid]->firstname.'：考试统计</div>';
$mytime = 0;
if($timeid==1){
	$mytime= time()-3600*24*7;
	// $sql='and a.timecreated>'.$mytime;
}
elseif($timeid==2){
	$mytime= time()-3600*24*30;
	// $sql='and a.timecreated>'.$mytime;
}
elseif($timeid==3){
	// $sql='';
}
echo_quiztabel($personid,$mytime);
function echo_quiztabel($personid,$mytime){
	global $DB;
	$quizcounts = $DB -> get_records_sql('
		select
		d.id,c.fullname,d.typeofquiz,e.timefinish, d.name,h.grade
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_quiz_attempts e on e.quiz=d.id and e.userid='.$personid.' and e.state=\'finished\'
		join mdl_quiz_grades h on h.quiz=d.id and h.userid='.$personid.' and h.timemodified>'.$mytime.'
		where a.userid='.$personid.' and d.timeopen!=0 and d.timeclose!=0 and d.attempts=1 and d.typeofquiz in (1,2)
		order by e.timefinish desc
	');
	
	echo '
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
</table>';
}

?>
