<script>
$('.lockpage').hide();
</script>
<!--表格排序-->
<script type="text/javascript" src="js/my/sortTable.js"></script>
<!--表格排序-->

<?php
/**
 * 台账数据中心》单位台账》人员（个人）台账 》学习任务统计
 */
require_once("../../config.php");
//$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$orgid = optional_param('orgid', 0, PARAM_INT);
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间
$courseid = optional_param('courseid', 1, PARAM_INT);//1全部，其他

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
//echo_activelist($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//积极榜，按时间段
//echo_learnlist($orgid,$start_time,$end_time,$sumusers,$orgname->name,$remove_role);//学习榜，按时间段
if($courseid == 1){
	echo_all_course($orgid,$start_time,$end_time,$orgname->name,$remove_role);//学习排行榜》全部课程
}else{
	echo_single_course($orgid,$start_time,$end_time,$orgname->name,$remove_role,$courseid);//学习排行榜》单课程
}

//***************************************************************

//学习排行榜》全部课程
function echo_all_course($orgid,$start_time,$end_time,$orgname,$remove_role){
	global $DB;
	require_once("../lib/my_lib.php");
	$orgIDStr = get_sub_self_orgid($orgid);//获取当前单位及下级单位id
	$noteTime_sql = handle_sql_time('time',$start_time,$end_time);
	$commentTime_sql = handle_sql_time('commenttime',$start_time,$end_time);
	$likeTime_sql = handle_sql_time('liketime',$start_time,$end_time);
	$scoreTime_sql =  handle_sql_time('scoretime',$start_time,$end_time);
	$badgeTime_sql =  handle_sql_time('dateissued',$start_time,$end_time);
	$loginTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$totalTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$courseScheduleTime_sql =  handle_sql_time('timemodified',$start_time,$end_time);

	$sql = "select u.id,u.username,u.firstname,u.lastname,a.notecount,b.commentcount,c.likecount,d.scorecount,e.badgecount,f.logincount,g.totaltime,h.orgname,i.courseschedulecount
			from mdl_user as u
			left join -- 课程笔记
				(select count(n.id) as notecount,n.userid from mdl_note_my n
				where n.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				and n.notetype = 1
				$noteTime_sql
				group by n.userid) as a
			on u.id = a.userid
			left join -- 评论
				(select SUM(temp.count) as commentcount,temp.userid from
				(select count(a.id) as count,a.userid from mdl_comment_article_my a
				where a.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$commentTime_sql
				group by a.userid
				union all
				select count(c.id) as count,c.userid from mdl_comment_course_my c
				where c.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$commentTime_sql
				group by c.userid
				union all
				select count(v.id) as count,v.userid from mdl_comment_video_my v
				where v.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$commentTime_sql
				group by v.userid
				) as temp
				group by temp.userid) as b
			on u.id = b.userid
			left join -- 点赞
				(select count(l.id) as likecount,l.userid from mdl_course_like_my l
				where l.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$likeTime_sql
				GROUP BY l.userid) as c
			on u.id = c.userid
			left join -- 星评
			(select count(s.id) as scorecount,s.userid from mdl_score_course_my s
			where s.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
			$scoreTime_sql
			GROUP BY s.userid) as d
			on u.id = d.userid
			left join -- 证书
				(select count(b.id) as badgecount,b.userid from mdl_badge_issued b
				where b.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$badgeTime_sql
				GROUP BY b.userid) as e
			on u.id = e.userid
			left join -- 登录数
				(select count(l.id) as logincount,l.userid from mdl_logstore_standard_log l
				where l.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				and l.action = 'loggedin'
				$loginTime_sql
				GROUP BY l.userid) as f
			on u.id = f.userid
			left join -- 课程已完成学时
				(select FORMAT(((count(1)*60)/3600),2) as totaltime,l.userid from mdl_logstore_standard_log l
				where l.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				$totalTime_sql
				GROUP BY l.userid) as g
			on u.id = g.userid
			left join -- 所属单位名
				(select o.`name` as orgname,ol.user_id as userid
				from mdl_org_link_user ol
				left join mdl_org o on o.id = ol.org_id
				) as h
			on u.id = h.userid
			left join -- 课程已完成课时
				(select cmc.userid,count(1) as courseschedulecount from mdl_course_modules_completion cmc
				where cmc.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ($orgIDStr) )
				$courseScheduleTime_sql
				GROUP BY cmc.userid) as i
			on u.id = i.userid
			where u.id in (
				select user_id
				from mdl_org_link_user
				where org_id in ( $orgIDStr )  and user_id not in (
					select userid
					from mdl_role_assignments
					where roleid in ( $remove_role )
				)
			)
			and u.id != 2
			order by logincount desc";
	$users = $DB -> get_records_sql($sql);

	//<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
	$output = '
		<div  class="table_text_center one_table">
		<div class="table_title_lg">'.$orgname.'：个人 学习排行榜（全部课程）</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td onclick="sortTable(\'tblSort\',2,\'int\');" style="cursor:pointer">登录数</td>
					<td onclick="sortTable(\'tblSort\',3,\'int\');" style="cursor:pointer">笔记</td>
					<td onclick="sortTable(\'tblSort\',4,\'int\');" style="cursor:pointer">评论</td>
					<td onclick="sortTable(\'tblSort\',5,\'int\');" style="cursor:pointer">点赞</td>
					<td onclick="sortTable(\'tblSort\',6,\'int\');" style="cursor:pointer">星评</td>
					<td onclick="sortTable(\'tblSort\',7,\'int\');" style="cursor:pointer">证书</td>
					<td onclick="sortTable(\'tblSort\',8,\'float\');" style="cursor:pointer">已学学时</td>
					<td onclick="sortTable(\'tblSort\',9,\'float\');" style="cursor:pointer">已学课时</td>
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
				<td>'.null_to_zero($user->logincount).'</td>
				<td>'.null_to_zero($user->notecount).'</td>
				<td>'.null_to_zero($user->commentcount).'</td>
				<td>'.null_to_zero($user->likecount).'</td>
				<td>'.null_to_zero($user->scorecount).'</td>
				<td>'.null_to_zero($user->badgecount).'</td>
				<td>'.null_to_zero($user->totaltime).'</td>
				<td>'.null_to_zero($user->courseschedulecount).'</td>
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

//学习排行榜》单课程
function echo_single_course($orgid,$start_time,$end_time,$orgname,$remove_role,$courseid){
	global $DB;
	require_once("../lib/my_lib.php");
	$orgIDStr = get_sub_self_orgid($orgid);//获取当前单位及下级单位id
	$noteTime_sql = handle_sql_time('time',$start_time,$end_time);
	$commentTime_sql = handle_sql_time('commenttime',$start_time,$end_time);
	$totalTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$courseCriteriaCompletion_sql =  handle_sql_time('timecompleted',$start_time,$end_time);
	$courseCompletion_sql =  handle_sql_time('timecompleted',$start_time,$end_time);
	$courseschedulecount_sql =  handle_sql_time('timemodified',$start_time,$end_time);

	$courseCriteria = get_course_module_criteria($courseid,1);//获取课程活动规则
	$courseSchedule_sqlStr = "";
	if(!$courseCriteria){//如果没设置任何活动规则
		$courseSchedule_sqlStr = "left join -- 课程进度
									(select '课程未设进度跟踪' as courseschedule,temp.user_id as userid
										from
										(select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) ) as temp
									) as d
								on u.id = d.userid";
	}else{
		$countCriteria = count($courseCriteria);//规则的数量
		$courseCriteriaStr = implode(',',$courseCriteria);
		$aggr = get_course_criteria_aggregration($courseid);//规则的组合方式
		if($aggr==1){//如果要完成全部
			$criteria_sqlStr = " select (FORMAT(( a.count/ $countCriteria )*100,0)) as courseschedule,a.userid ";
		}elseif($aggr==2){//如果只需完成其一
			$criteria_sqlStr = " select (IF(a.count=0,0,100)) as courseschedule,a.userid ";
		}
		//课程进度
		$courseSchedule_sqlStr = "left join -- 课程进度
										(select MAX(temp.courseschedule) as courseschedule,temp.userid from
											(
												( $criteria_sqlStr
													from
													(SELECT c.userid,COUNT(1) as count FROM mdl_course_completion_crit_compl c
													WHERE c.course = $courseid
													AND c.criteriaid in ( $courseCriteriaStr )
													AND c.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
													$courseCriteriaCompletion_sql
													GROUP BY c.userid ) as a
												)
												UNION ALL -- 已完成（可能是设定人工设为完成）
												(select 100 as courseschedule,a.userid from mdl_course_completions a
													where a.course = $courseid
													and a.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
													AND a.timecompleted != ''
													$courseCompletion_sql
												 )
											) as temp
											GROUP BY temp.userid
										) as d
									on u.id = d.userid";
	}

	$sql = "select u.id,u.username,u.firstname,u.lastname,a.notecount,b.commentcount,c.totaltime,d.courseschedule,e.orgname,f.courseschedulecount
			from mdl_user as u
			left join -- 课程笔记
				(select count(n.id) as notecount,n.userid from mdl_note_my n
				where n.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				and n.notetype = 1
				$noteTime_sql
				and n.courseid = $courseid
				group by n.userid) as a
			on u.id = a.userid
			left join -- 评论
				(select SUM(temp.count) as commentcount,temp.userid from
					(select count(a.id) as count,a.userid from mdl_comment_article_my a
					join mdl_course_modules b on b.id=a.articleid and b.course=$courseid
					where a.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
					$commentTime_sql
					group by a.userid
					union all
					select count(c.id) as count,c.userid from mdl_comment_course_my c
					where c.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
					$commentTime_sql
					and courseid=$courseid
					group by c.userid
					union all
					select count(v.id) as count,v.userid from mdl_comment_video_my v
					join mdl_course_modules b on b.id=v.modid and b.course=$courseid
					where v.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
					$commentTime_sql
					group by v.userid
				) as temp
				group by temp.userid) as b
			on u.id = b.userid
			left join -- 课程已完成学时
				(select FORMAT(((count(1)*60)/3600),2) as totaltime,l.userid from mdl_logstore_standard_log l
				where l.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
				and l.courseid = $courseid
				$totalTime_sql
				GROUP BY l.userid) as c
			on u.id = c.userid
			-- 课程进度
			$courseSchedule_sqlStr
			left join -- 所属单位名
				(select o.`name` as orgname,ol.user_id as userid
				from mdl_org_link_user ol
				left join mdl_org o on o.id = ol.org_id
				) as e
			on u.id = e.userid
			left join -- 课程已完成课时
				(select  count(*) as courseschedulecount,cmc.userid from mdl_course_modules_completion cmc
						where cmc.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ($orgIDStr ) )
						and cmc.coursemoduleid in (select cm.id from mdl_course_modules cm  where cm.course = $courseid and cm.`completion` in (1,2) )
						$courseschedulecount_sql
						-- and cmc.completionstate = 1
				  		GROUP BY cmc.userid ) as f
			on u.id = f.userid
			where u.id in (
				select user_id
				from mdl_org_link_user
				where org_id in ( $orgIDStr )  and user_id not in (
					select userid
					from mdl_role_assignments
					where roleid in ( $remove_role )
				)
			)
			and u.id != 2";
	$users = $DB -> get_records_sql($sql);
	$sql = "select c.id,c.fullname from mdl_course c where c.id = $courseid";
	$course = $DB->get_record_sql($sql);

	//<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
	$output = '
		<div  class="table_text_center one_table">
		<div class="table_title_lg" >'.$orgname.'：个人 学习排行榜（'.$course->fullname.'）</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td onclick="sortTable(\'tblSort\',2,\'int\');" style="cursor:pointer">笔记</td>
					<td onclick="sortTable(\'tblSort\',3,\'int\');" style="cursor:pointer">评论</td>
					<td onclick="sortTable(\'tblSort\',4,\'my_string\');" style="cursor:pointer">课程进度</td>
					<td onclick="sortTable(\'tblSort\',5,\'float\');" style="cursor:pointer">已学学时</td>
					<td onclick="sortTable(\'tblSort\',6,\'float\');" style="cursor:pointer">已学课时</td>
					<td>所属单位</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){
		if(is_numeric(null_to_zero($user->courseschedule))){
			$schedule = null_to_zero($user->courseschedule).'%';
		}else{
			$schedule = $user->courseschedule;
		}
		$output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->lastname.$user->firstname.'</td>
				<td>'.null_to_zero($user->notecount).'</td>
				<td>'.null_to_zero($user->commentcount).'</td>
				<td>'.$schedule.'</td>
				<td>'.null_to_zero($user->totaltime).'</td>
				<td>'.null_to_zero($user->courseschedulecount).'</td>
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

/**
 * 将为空（null）的变量转为 0
 * @param $param 分析的变量
 * @return var|0
 */
function null_to_zero($param){
	return ($param==null)?0:$param;
}


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
