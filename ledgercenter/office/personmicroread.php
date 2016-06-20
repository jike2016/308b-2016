<script>
$('.lockpage').hide();
</script>
<?php 
require_once("../../config.php");
$orgid = optional_param('orgid', 0, PARAM_INT);
$start_time = optional_param('start_time', 1, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 1, PARAM_TEXT);//结束时间

global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);

//获取该组织下的人员
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

echo_browseNum($orgid,$start_time,$end_time,$sumusers,$orgname->name);//浏览数榜，按时间段
echo_upload($orgid,$start_time,$end_time,$sumusers,$orgname->name);//上传榜，按时间段
echo_passCheck($orgid,$start_time,$end_time,$sumusers,$orgname->name);//通过审查榜，按时间段


/** Start 浏览数排行榜 xdw **/
function echo_browseNum($orgid,$start_time,$end_time,$sumusers,$orgname){

	//按照时间段查询
	$sql = 'and m.timecreated > '.$start_time .' and m.timecreated < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select m.userid,u.firstname,u.lastname,count(1) as count  from mdl_microread_log m
		join mdl_user u on m.userid = u.id
		where m.action = \'view\'
		and m.target in (1,2)
		and m.userid in (
			select a.user_id
				from mdl_org_link_user a
				join mdl_user b on b.id= a.user_id
				where org_id='.$orgid.'
			 	and a.user_id not in (
					select userid
					from mdl_role_assignments
					where roleid =14)
		)
		'.$sql.'
		GROUP BY m.userid
		ORDER BY count desc'
	);

	$output = '
		<div style= "width:30%; float:left; margin-left:2%;text-align:center;">
		<div style= "font-weight:600;">'.$orgname.':个人浏览榜</div>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>浏览总数</td>
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
/** end 浏览数排行榜 xdw **/

/** Start 上传排行榜 xdw **/
function echo_upload($orgid,$start_time,$end_time,$sumusers,$orgname){

	//按照时间段查询
	$sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
	$sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select table_new.userid,sum(table_new.count) as count,u.firstname,u.lastname from (
			(select d.upload_userid as userid,count(1) as count from mdl_doc_user_upload_my d
					where d.upload_userid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid =14)
					)
					'.$sql_doc.'
					GROUP BY d.upload_userid)
			union
			(select e.uploaderid as userid,count(1) as count from mdl_ebook_user_upload_my e
					where e.uploaderid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid =14)
					)
					'.$sql_ebook.'
					GROUP BY e.uploaderid)
		) as table_new
		join mdl_user u	on u.id = table_new.userid
		GROUP BY table_new.userid
		ORDER BY table_new.count DESC'
	);

	$output = '
		<div style= "width:30%; float:left; margin-left:3%;text-align:center;">
		<div style= "font-weight:600;">'.$orgname.':个人上传榜</div>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>上传总数</td>
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
/** end 上传排行榜 xdw **/

/** Start 通过审查排行榜 xdw **/
function echo_passCheck($orgid,$start_time,$end_time,$sumusers,$orgname){

	//按照时间段查询
	$sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
	$sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select table_new.userid,sum(table_new.count) as count,u.firstname,u.lastname from (
			(select d.upload_userid as userid,count(1) as count from mdl_doc_user_upload_my d
					where d.upload_userid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid =14)
					)
					'.$sql_doc.'
					and d.admin_check = 1
					GROUP BY d.upload_userid)
			union
			(select e.uploaderid as userid,count(1) as count from mdl_ebook_user_upload_my e
					where e.uploaderid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid =14)
					)
					'.$sql_ebook.'
					and e.admin_check = 1
					GROUP BY e.uploaderid)
		) as table_new
		join mdl_user u	on u.id = table_new.userid
		GROUP BY table_new.userid
		ORDER BY table_new.count DESC'
	);

	$output = '
		<div style= "width:30%; float:left; margin-left:3%;text-align:center;">
		<div style= "font-weight:600;">'.$orgname.':个人通过审查榜</div>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>通过审查总数</td>
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
/** end 通过审查排行榜 xdw **/

?>

<style>
     td {text-align:center}
</style>
