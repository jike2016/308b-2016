<script>
$('.lockpage').hide();
</script>

<!--表格排序-->
<script type="text/javascript" src="js/my/sortTable.js"></script>
<!--表格排序-->

<?php
/**
 * 台账数据中心》单位台账》人员（个人）台账 》微阅统计
 */
require_once("../../config.php");
$orgid = optional_param('orgid', 0, PARAM_INT);
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间

if($start_time==0 || $end_time==0){//如果时间为空
	$time = handle_time($start_time,$end_time);
	$start_time = $time['start_time'];
	$end_time = $time['end_time'];
}

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');

global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);


echo_microread_rank_list($orgid,$start_time,$end_time,$orgname->name,$remove_role);

exit;//***退出执行以下代码***********************


//获取该组织下的人员
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

echo_browseNum($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//浏览数榜，按时间段
echo_upload($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//上传榜，按时间段
echo_passCheck($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//通过审查榜，按时间段


//************************************************************************************


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

/** Start 微阅排行榜 xdw **/
function echo_microread_rank_list($orgid,$start_time,$end_time,$orgname,$remove_role){
	global $DB;
	require_once("../lib/my_lib.php");
	$UserStr = get_org_user_array($orgid,$remove_role,$type=1);//获取当前单位下的人员
	$UserStr = (!$UserStr) ? 0 : $UserStr;//判断是否有人员
	$browsTime_sql = handle_sql_time('timecreated',$start_time,$end_time);
	$uploadTime_sql = handle_sql_time('timecreated',$start_time,$end_time);
	$passCheckTime_sql = handle_sql_time('timecreated',$start_time,$end_time);

	$sql = "
			select u.id,u.username,u.firstname,u.lastname,a.browsecount,b.uploadcount,c.passcheckcount,d.orgname
			from mdl_user as u
			LEFT JOIN -- 浏览数
				(select m.userid,count(1) as browsecount
				from mdl_microread_log m
				where m.action = 'view'
				and m.target in (1,2,3)
				and m.userid in ( $UserStr )
				$browsTime_sql
				GROUP BY m.userid) as a
			on u.id = a.userid
			LEFT JOIN -- 上传数
				(select table_new.userid,sum(table_new.count) as uploadcount
				 from
					(
						(select d.upload_userid as userid,count(1) as count from mdl_doc_user_upload_my d
								where d.upload_userid in ( $UserStr )
								$uploadTime_sql
								GROUP BY d.upload_userid)
						union all
						(select e.uploaderid as userid,count(1) as count from mdl_ebook_user_upload_my e
								where e.uploaderid in ( $UserStr )
								$uploadTime_sql
								GROUP BY e.uploaderid)
						union all
						(select p.uploaderid as userid,count(1) as count from mdl_pic_user_upload_my p
								where p.uploaderid in ( $UserStr )
								$uploadTime_sql
								GROUP BY p.uploaderid)
					) as table_new
					GROUP BY table_new.userid ) as b
			on u.id = b.userid
			LEFT JOIN -- 上传数
			(select table_new.userid,sum(table_new.count) as passcheckcount
			from (
						(select d.upload_userid as userid,count(1) as count from mdl_doc_user_upload_my d
								where d.upload_userid in ( $UserStr )
								$passCheckTime_sql
								and d.admin_check = 1
								GROUP BY d.upload_userid)
						union all
						(select e.uploaderid as userid,count(1) as count from mdl_ebook_user_upload_my e
								where e.uploaderid in ( $UserStr )
								$passCheckTime_sql
								and e.admin_check = 1
								GROUP BY e.uploaderid)
						union all
						(select p.uploaderid as userid,count(1) as count from mdl_pic_user_upload_my p
								where p.uploaderid in ( $UserStr )
								$passCheckTime_sql
								and p.admin_check = 1
								GROUP BY p.uploaderid)
					) as table_new
					GROUP BY table_new.userid) as c
			on u.id = c.userid
			left join -- 所属单位名
				(select o.`name` as orgname,ol.user_id as userid
				from mdl_org_link_user ol
				left join mdl_org o on o.id = ol.org_id
				) as d
			on u.id = d.userid
			WHERE u.id in ( $UserStr )
			GROUP BY u.id";
	$users = $DB -> get_records_sql($sql);

	//<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
	$output = '
		<div  class="table_text_center one_table">
		<div  class="table_title_lg" >'.$orgname.'：微阅 个人 排行榜</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td onclick="sortTable(\'tblSort\',2,\'int\');" style="cursor:pointer">浏览数</td>
					<td onclick="sortTable(\'tblSort\',3,\'int\');" style="cursor:pointer">上传数</td>
					<td onclick="sortTable(\'tblSort\',4,\'int\');" style="cursor:pointer">通过审查数</td>
					<td>所属单位</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){
		$output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->lastname.$user->firstname.'</td>
				<td>'.null_to_zero($user->browsecount).'</td>
				<td>'.null_to_zero($user->uploadcount).'</td>
				<td>'.null_to_zero($user->passcheckcount).'</td>
				<td>'.$user->orgname.'</td>
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
/** end 微阅排行榜 xdw **/

/**
 * 将为空（null）的变量转为 0
 * @param $param 分析的变量
 * @return var|0
 */
function null_to_zero($param){
	return ($param==null)?0:$param;
}

/** Start 浏览数排行榜 xdw **/
function echo_browseNum($orgid,$start_time,$end_time,$sumusers,$orgname,$remove_role){

	//按照时间段查询
	$sql = 'and m.timecreated > '.$start_time .' and m.timecreated < '.$end_time;

	global $DB;
	$users = $DB -> get_records_sql('
		select m.userid,u.firstname,u.lastname,count(1) as count  from mdl_microread_log m
		join mdl_user u on m.userid = u.id
		where m.action = \'view\'
		and m.target in (1,2,3)
		and m.userid in (
			select a.user_id
				from mdl_org_link_user a
				join mdl_user b on b.id= a.user_id
				where org_id='.$orgid.'
			 	and a.user_id not in (
					select userid
					from mdl_role_assignments
					where roleid in ('.$remove_role.') )
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
function echo_upload($orgid,$start_time,$end_time,$sumusers,$orgname,$remove_role){

	//按照时间段查询
	$sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
	$sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
	$sql_pic = 'and p.timecreated > '.$start_time .' and p.timecreated < '.$end_time;

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
										where roleid in ('.$remove_role.') )
					)
					'.$sql_doc.'
					GROUP BY d.upload_userid)
			union all
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
										where roleid in ('.$remove_role.') )
					)
					'.$sql_ebook.'
					GROUP BY e.uploaderid)
			union all
			(select p.uploaderid as userid,count(1) as count from mdl_pic_user_upload_my p
					where p.uploaderid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid in ('.$remove_role.') )
					)
					'.$sql_pic.'
					GROUP BY p.uploaderid)
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
function echo_passCheck($orgid,$start_time,$end_time,$sumusers,$orgname,$remove_role){

	//按照时间段查询
	$sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
	$sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
	$sql_pic = 'and p.timecreated > '.$start_time .' and p.timecreated < '.$end_time;

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
										where roleid in ('.$remove_role.') )
					)
					'.$sql_doc.'
					and d.admin_check = 1
					GROUP BY d.upload_userid)
			union all
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
										where roleid in ('.$remove_role.') )
					)
					'.$sql_ebook.'
					and e.admin_check = 1
					GROUP BY e.uploaderid)
			union all
			(select p.uploaderid as userid,count(1) as count from mdl_pic_user_upload_my p
					where p.uploaderid in
					(
								select a.user_id
									from mdl_org_link_user a
									join mdl_user b on b.id= a.user_id
									where org_id='.$orgid.'
									and a.user_id not in (
										select userid
										from mdl_role_assignments
										where roleid in ('.$remove_role.') )
					)
					'.$sql_pic.'
					and p.admin_check = 1
					GROUP BY p.uploaderid)
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
