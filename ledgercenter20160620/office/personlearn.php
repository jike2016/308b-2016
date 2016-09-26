<script>
$('.lockpage').hide();
</script>
<?php 
require_once("../../config.php");
$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$orgid = optional_param('orgid', 0, PARAM_INT);

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
		where roleid =14 
	)
');

echo_activelist($orgid,$timeid,$sumusers,$orgname->name);
echo_learnlist($orgid,$timeid,$sumusers,$orgname->name);

function echo_activelist($orgid,$timeid,$sumusers,$orgname){
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
				where roleid =14 
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

function echo_learnlist($orgid,$timeid,$sumusers,$orgname){
	if($timeid==1){
	$mytime= time()-3600*24*7;
	$sql='and a.timemodified>'.$mytime;
	}
	elseif($timeid==2){
		$mytime= time()-3600*24*30;
		$sql='and a.timemodified>'.$mytime;
	}
	elseif($timeid==3){
		$sql='';
	}
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
				where roleid =14 
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
