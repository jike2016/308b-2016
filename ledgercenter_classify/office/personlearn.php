<script>
$('.lockpage').hide();
</script>
<?php 
require_once("../../config.php");
//$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$orgid = optional_param('orgid', 0, PARAM_INT);
$start_time = optional_param('start_time', 1, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 1, PARAM_TEXT);//结束时间

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');

global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
// echo $orgname->name;
$sumusers = $DB -> get_records_sql('
	select a.user_id, b.firstname,b.lastname
	from mdl_org_link_user a
	join mdl_user b on b.id= a.user_id
	where org_id='.$orgid.' and a.user_id not in (
		select userid 
		from mdl_role_assignments 
		where roleid in ('.$remove_role.')
	)
');

//echo_activelist($orgid,$timeid,$sumusers,$orgname->name);//积极榜，按月周总
//echo_learnlist($orgid,$timeid,$sumusers,$orgname->name);//学习榜，按月周总
echo_activelist($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//积极榜，按时间段
echo_learnlist($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//学习榜，按时间段

//活动排行榜
function echo_activelist($orgid,$start_time,$end_time,$sumusers,$orgname,$remove_role){
//function echo_activelist($orgid,$timeid,$sumusers,$orgname){
//Start 去掉月周总的查询
//	if($timeid==1){
//	$mytime= time()-3600*24*7;
//	$sql='and a.timecreated>'.$mytime;
//	}
//	elseif($timeid==2){
//		$mytime= time()-3600*24*30;
//		$sql='and a.timecreated>'.$mytime;
//	}
//	elseif($timeid==3){
//		$sql='';
//	}
//End 去掉月周总的查询
	//按照时间段查询
	$sql = 'and a.timecreated > '.$start_time .' and a.timecreated < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select 
		a.userid,count(1) as count,b.lastname,b.firstname 
		from mdl_logstore_standard_log a 
		join mdl_user b on b.id= a.userid 
		where a.userid in (
			select user_id
			from mdl_org_link_user 
			where org_id='.$orgid.' and user_id not in (
				select userid 
				from mdl_role_assignments 
				where roleid in ('.$remove_role.')
			)
		)
		'.$sql.'
		GROUP BY a.userid 
		ORDER BY count DESC'
	);
	$output = '
		<div style= "width:35%; float:left; margin-left:5%;text-align:center;">
		<div style= "font-weight:600;">'.$orgname.':个人积极榜</div>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>事件总数</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){
		unset($sumusers[$user->userid]);
		$output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->lastname.$user->firstname.'</td>
				<td>'.$user->count.'</td>
			</tr>
			';
		$n++;
	}
	//输出没有数据的人
	foreach($sumusers as $sumuser){
		$output .='
				<tr>
					<td>'.$n.'</td>
					<td>'.$sumuser->lastname.$sumuser->firstname.'</td>
					<td>0</td>
				</tr>
				';
		$n++;
	}
	$output .='
		</tbody>
	</table>
	</div>
	';
	echo $output;
}

//学习排行榜
function echo_learnlist($orgid,$start_time,$end_time,$sumusers,$orgname,$remove_role){
//Start 去掉月周总的查询
//function echo_learnlist($orgid,$timeid,$sumusers,$orgname){
//	if($timeid==1){
//	$mytime= time()-3600*24*7;
//	$sql='and a.timemodified>'.$mytime;
//	}
//	elseif($timeid==2){
//		$mytime= time()-3600*24*30;
//		$sql='and a.timemodified>'.$mytime;
//	}
//	elseif($timeid==3){
//		$sql='';
//	}
//End 去掉月周总的查询
	//按时间段查询
	$sql = 'and a.timemodified > '.$start_time .' and a.timemodified < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select 
		a.userid,count(1) as count,b.lastname,b.firstname
		from mdl_course_modules_completion a 
		join mdl_user b on b.id = a.userid 
		where a.completionstate=1 and a.userid in (
			select user_id
			from mdl_org_link_user 
			where org_id='.$orgid.' and user_id not in (
				select userid 
				from mdl_role_assignments 
				where roleid in ('.$remove_role.')
			)
		) 
		'.$sql.'
		GROUP BY a.userid 
		ORDER BY count DESC
	');
	$output ='
		<div style= "width:35%;float:right;margin-right:10%;text-align:center;">
		<div style= "font-weight:600;">'.$orgname.':个人学习榜</div>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>活动完成总数</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){
		unset($sumusers[$user->userid]);
		$output .='
				<tr>
					<td>'.$n.'</td>
					<td>'.$user->lastname.$user->firstname.'</td>
					<td>'.$user->count.'</td>
				</tr>
				';
		$n++;
	}
	//输出没有数据的人
	foreach($sumusers as $sumuser){
		$output .='
				<tr>
					<td>'.$n.'</td>
					<td>'.$sumuser->lastname.$sumuser->firstname.'</td>
					<td>0</td>
				</tr>
				';
		$n++;
	}
	$output .='
			</tbody>
		</table>
		</div>
	';
	echo $output;
}

?>

<style>
     td {text-align:center}
</style>
