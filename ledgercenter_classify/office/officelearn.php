<script>
$('.lockpage').hide();
</script>
<!--表格排序-->
<script type="text/javascript" src="js/my/sortTable.js"></script>
<!--表格排序-->

<?php
/**
 * 台账数据中心》单位台账》单位（子单位）台账 》学习任务统计
 */
require_once("../../config.php");
//$timeid = optional_param('timeid', 1, PARAM_INT);//1周2月3总
$orgid = optional_param('orgid', 0, PARAM_INT);
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间
$courseid = optional_param('courseid', 1, PARAM_INT);//1全部，其他

if($start_time==0 || $end_time==0){//如果时间为空
	$time = handle_time($start_time,$end_time);
	$start_time = $time['start_time'];
	$end_time = $time['end_time'];
}

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');

global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
//查询所有下级单位id
$sumorgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$orgid);


if($courseid == 1){//全部课程
	echo '<div class="table_title_lg" >'.$orgname->name.'：学习统计（全部课程）</div>';
	echo_all_course($orgid,$start_time,$end_time,$remove_role);//学习排行榜》全部课程
	//输出课程学习比例图（饼状图）
	$datas = get_course_study_time($orgid,$start_time,$end_time,$remove_role);//获取饼状图数据
	if($datas){
		require_once("../lib/myPieChart/my_pie_chart.php");
		echo '</br><div class="table_title" >课程学习比例：</div>';
		echo_piechar($datas,'');
	}
	//折线图
	if( ($end_time-$start_time) > 86400 ){//如果查询时间段大于一天，则显示折线图
		require_once("../lib/my_lib.php");
		$orgUsers = get_org_user_array($orgid,$remove_role);//获取当前单位的人员
		if($orgUsers){
			$Histogram_data = get_time_learn($start_time,$end_time,$orgUsers);//获取折线图数据
			echo '<div class="table_title" >学习情况（课时分布）:</div>';
			require_once("../lib/myLineChart/my_line_chart.php");
			echo_line_chart($Histogram_data);//输出
		}
	}

}else{//单课程
	$sql = "select c.id,c.fullname from mdl_course c where c.id = $courseid";
	$course = $DB->get_record_sql($sql);
	echo '<div class="table_title_lg" >'.$orgname->name.'：学习统计（'.$course->fullname.'）</div>';
	echo_single_course($orgid,$start_time,$end_time,$remove_role,$courseid);//学习排行榜》单课程
	//折线图
	if( ($end_time-$start_time) > 86400 ){//如果查询时间段大于一天，则显示折线图
		require_once("../lib/my_lib.php");
		$orgUsers = get_org_user_array($orgid,$remove_role);//获取当前单位的人员
		$coursemoduleid = get_course_module($courseid);
		if($orgUsers){
			$Histogram_data = get_time_learn($start_time,$end_time,$orgUsers,$courseid,$coursemoduleid);//获取折线图数据
			echo '</br><div class="table_title" ">学习情况（课时分布）:</div>';
			require_once("../lib/myLineChart/my_line_chart.php");
			echo_line_chart($Histogram_data);//输出
		}
	}
}


exit;//***退出执行以下代码***********************

$rank_active = array();
$rank_learn = array();
foreach($sumorgs as $org){
	//分别查下级单位id的所有人,筛掉单位账号
//	$sumusers = $DB -> get_records_sql('
//		select a.user_id, b.firstname,b.lastname
//		from mdl_org_link_user a
//		join mdl_user b on b.id= a.user_id
//		where org_id='.$org->id.' and a.user_id not in (
//			select userid
//			from mdl_role_assignments
//			where roleid =14
//		)
//	');

	$totalPeople = get_totalPeople_org($org,$remove_role);//子单位总人数
	//积极榜处理
//	$avg_org_active = calculate_active_org($org->id,count($sumusers),$timeid);//按照周月总查询
//	$avg_org_active = calculate_active_org($org->id,count($sumusers),$start_time,$end_time);//按照时间段查询
//	$rank_active[]= array('orgname'=>$org->name,'avgcount'=>$avg_org_active,'totalPeople'=>count($sumusers));
	$totalLearnEvent = get_totalActiveEvent_org($org,$start_time,$end_time,$remove_role);//子单位下的总事件完成数
	$avg_org_active = round($totalLearnEvent/$totalPeople,2);//人均完成数
	$rank_active[] = array('orgname'=>$org->name,'avgcount'=>$avg_org_active,'totalPeople'=>$totalPeople,'totalEvent'=>$totalLearnEvent);

	//学习榜处理
//	$avg_org_learn = calculate_learn_org($org->id,count($sumusers),$timeid);//按照周月总查询
//	$avg_org_learn = calculate_learn_org($org->id,count($sumusers),$start_time,$end_time);//按照时间段查询
//	$rank_learn[]= array('orgname'=>$org->name,'avgcount'=>$avg_org_learn,'totalPeople'=>count($sumusers));
	$totalLearnEvent = get_totalLearnEvent_org($org,$start_time,$end_time,$remove_role);//子单位下的总事件完成数
	$avg_org_learn = round($totalLearnEvent/$totalPeople,2);//人均完成数
	$rank_learn[] = array('orgname'=>$org->name,'avgcount'=>$avg_org_learn,'totalPeople'=>$totalPeople,'totalEvent'=>$totalLearnEvent);

}
//排序
//quick_sort_swap($rank_active,0,count($rank_active)-1);
//quick_sort_swap($rank_learn,0,count($rank_learn)-1);
sort_multarray($rank_active);//活动
sort_multarray($rank_learn);//学习

//输出榜单
echo_active_org($rank_active,$orgname->name);
echo_learn_org($rank_learn,$orgname->name);

// $rank_active2 = array();
// $rank_active2[]= array(orgname=>'D1',avgcount=>2);
// $rank_active2[]= array(orgname=>'D2',avgcount=>1);
// $rank_active2[]= array(orgname=>'D3',avgcount=>0);
// $rank_active2[]= array(orgname=>'D4',avgcount=>0);
// $rank_active2[]= array(orgname=>'D5',avgcount=>0);
// $rank_active2[]= array(orgname=>'D6',avgcount=>6);
// $rank_active2[]= array(orgname=>'D7',avgcount=>3);
// $rank_active2[]= array(orgname=>'D8',avgcount=>8);
// $rank_active2[]= array(orgname=>'D9',avgcount=>1);
// quick_sort_swap($rank_active2,0,count($rank_active2)-1);
// $a=1;


//*********************************************************************************

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


//获取用户在该时间段内完成的课时数（章节数）
function get_coursecomplete_count($starttime,$endtime,$userStr = 0,$courseid,$coursemoduleid = 0 ){
	global $DB;

	if($courseid==1) {//全部课程
		$sql = "select cmc.userid,count(1) as count from mdl_course_modules_completion cmc
			where cmc.userid in ($userStr)
			and cmc.timemodified between $starttime and $endtime ";
	}else{//单课程
		$sql = "select cmc.userid,count(1) as count from mdl_course_modules_completion cmc
			where cmc.userid in ($userStr)
			and cmc.coursemoduleid in ( $coursemoduleid )
			and cmc.timemodified between $starttime and $endtime ";
	}
	$records = $DB->get_record_sql($sql);
	return $records->count;
}

/**
 * 获取时间段内的学习情况
 * @param $start_time
 * @param $end_time
 * @return array
 */
//只提供
//日折线：时间段在31天以内
//月折线：时间段大于31天
//从结束时间往前开始计算
function get_time_learn($start_time,$end_time,$userStr,$courseid=1,$coursemoduleid=0){
	$day_onlinetime='';//存放每点的数据
	$day_week = '""';//存放每点的日期
	$timeslot = $end_time - $start_time;//查询的时间段
	//时间段长度的划分
	if($timeslot > 2678400){//如果( 时间段 > 一个月)，用月线显示
		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m', $endtime).'-01'); // 结束时间当月1号的零点
		$day_week = '"'.date('Y-m', $starttime).'"';//存放每点的日期
		$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$userStr,$courseid,$coursemoduleid).','.$day_onlinetime;
		$timeslot = $timeslot - ($endtime - $starttime);//重新计算时间段

		while($timeslot > 0){
			//计算月时间段长度
			$monthend = $starttime-1;//下月初 - 1，即为上月末
			$monthstart = strtotime(date('Y-m', $monthend).'-01');//上月初
			$monthtimeslot = ($monthend - $monthstart) + 1;//月时间段，注意时间的精确性
			//更新时间点
			$endtime = $starttime;
			if($timeslot > $monthtimeslot){//如果剩余时间大于月时间段
				$starttime = $endtime-$monthtimeslot;
			}else{
				$starttime = $start_time;
			}
			//查询时间段内的数据
			$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$userStr,$courseid,$coursemoduleid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m', $starttime).'",'.$day_week;
			$timeslot = $timeslot - $monthtimeslot;//时间段减去月时间段。同时，这里是循环的出口
		}

	}elseif($timeslot >= 86400){//如果( 一天 < 时间段 < 一个月)，用日线显示

		$endtime = $end_time;//结束时间
		$starttime = strtotime(date('Y-m-d', $endtime)); // 结束时间当天的零点
		$day_week = '"'.date('Y-m-d', $starttime).'"';//存放每点的日期
		$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$userStr,$courseid,$coursemoduleid).','.$day_onlinetime;
		$timeslot = $timeslot - ($endtime - $starttime);//重新计算时间段

		while($timeslot > 0){
			//更新时间点
			$endtime = $starttime;
			if($timeslot > 86400){//如果剩余时间大于一天
				$starttime = $endtime-86400;
			}else{
				$starttime = $start_time;
			}
			//查询时间段内的数据
			$day_onlinetime = get_coursecomplete_count($starttime,$endtime,$userStr,$courseid,$coursemoduleid).','.$day_onlinetime;
			$day_week = '"'.date('Y-m-d', $starttime).'",'.$day_week;
			$timeslot = $timeslot - 86400;//时间段减去一天。同时，这里是循环的出口

		}
	}
	return array($day_week,$day_onlinetime);

}

/**
 * 获取单位的各课程中学员学时总和
 * @param $orgid
 * @return array
 */
function get_course_study_time($orgid,$start_time,$end_time,$remove_role){
	global $DB;
	require_once("../lib/my_lib.php");
	$orgIDStr = get_sub_self_orgid($orgid);//获取当前单位及下级单位id
	$logTime_sql = handle_sql_time('a.timecreated',$start_time,$end_time);

	$sql = "select a.courseid,b.fullname as name,count(a.courseid) AS count
			from mdl_logstore_standard_log a
			join mdl_course b on b.id=a.courseid
			where a.userid in (
				select ol.user_id
				from mdl_org_link_user ol
				where ol.org_id in ( $orgIDStr )
				and ol.user_id not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and ol.user_id != 2
			)
			and a.courseid in (
				select oc.course_id from mdl_org_course_my oc
            	LEFT JOIN mdl_course c on oc.course_id = c.id
            	where oc.org_id in ( $orgIDStr )
				GROUP BY oc.course_id
			)
			$logTime_sql
			GROUP BY a.courseid
			ORDER BY count DESC";
	$datas = $DB->get_records_sql($sql);
	return $datas;
}

//学习排行榜》全部课程
function echo_all_course($orgid,$start_time,$end_time,$remove_role){
	global $DB;
	require_once("../lib/my_lib.php");
	$orgIDStr = get_sub_self_orgid($orgid);//获取当前单位及下级单位id
	$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
	$noteTime_sql = handle_sql_time('time',$start_time,$end_time);
	$commentTime_sql = handle_sql_time('commenttime',$start_time,$end_time);
	$likeTime_sql = handle_sql_time('liketime',$start_time,$end_time);
	$scoreTime_sql =  handle_sql_time('scoretime',$start_time,$end_time);
	$badgeTime_sql =  handle_sql_time('dateissued',$start_time,$end_time);
	$loginTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$totalTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$courseschedule_sql =  handle_sql_time('timemodified',$start_time,$end_time);

	$sql = "select o.id,o.`name` as orgname,a.notecount,b.commentcount,c.likecount,d.scorecount,e.badgecount,f.logincount,g.totaltime,h.usercount,i.courseschedulecount
			from mdl_org as o
			LEFT JOIN -- 课程笔记
				(select count(n.id) as notecount,ol.org_id as orgid from mdl_note_my n
				LEFT JOIN mdl_org_link_user ol on n.userid = ol.user_id
				where n.notetype = 1
				$noteTime_sql
				and n.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and n.userid != 2
				and ol.org_id in ( $orgIDStr )
				GROUP BY ol.org_id) as a
			on o.id = a.orgid
			LEFT JOIN -- 评论
			(select SUM(temp.count) as commentcount,temp.orgid from
				(select count(a.id) as count,ol.org_id as orgid from mdl_comment_article_my a
				LEFT JOIN mdl_org_link_user ol on a.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$commentTime_sql
				and a.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and a.userid != 2
				GROUP BY ol.org_id
				union all
				select count(c.id) as count,ol.org_id as orgid from mdl_comment_course_my c
				LEFT JOIN mdl_org_link_user ol on c.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$commentTime_sql
				and c.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and c.userid != 2
				GROUP BY ol.org_id
				union all
				select count(v.id) as count,ol.org_id as orgid from mdl_comment_video_my v
				LEFT JOIN mdl_org_link_user ol on v.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$commentTime_sql
				and v.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and v.userid != 2
				GROUP BY ol.org_id
				) as temp
				group by temp.orgid) as b
			on o.id = b.orgid
			LEFT JOIN -- 点赞
				(select count(l.id) as likecount,ol.org_id as orgid from mdl_course_like_my l
					LEFT JOIN mdl_org_link_user ol on l.userid = ol.user_id
					where ol.org_id in ( $orgIDStr )
					$likeTime_sql
					and l.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
					and l.userid != 2
					GROUP BY ol.org_id) as c
			on o.id = c.orgid
			LEFT JOIN -- 星评
				(select count(s.id) as scorecount,ol.org_id as orgid from mdl_score_course_my s
				LEFT JOIN mdl_org_link_user ol on s.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$scoreTime_sql
				and s.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and s.userid != 2
				GROUP BY ol.org_id) as d
			on o.id = d.orgid
			LEFT JOIN -- 证书
				(select count(b.id) as badgecount,ol.org_id as orgid from mdl_badge_issued b
				LEFT JOIN mdl_org_link_user ol on b.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$badgeTime_sql
				and b.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and b.userid != 2
				GROUP BY ol.org_id) as e
			on o.id = e.orgid
			LEFT JOIN -- 登录数
				(select count(l.id) as logincount,ol.org_id as orgid from mdl_logstore_standard_log l
					LEFT JOIN mdl_org_link_user ol on l.userid = ol.user_id
					where ol.org_id in ( $orgIDStr )
					$loginTime_sql
					and l.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
					and l.userid != 2
					and l.action = 'loggedin'
					GROUP BY ol.org_id) as f
			on o.id = f.orgid
			LEFT JOIN -- 已完成学时
				(select FORMAT(((count(1)*60)/3600),1) as totaltime,ol.org_id as orgid from mdl_logstore_standard_log l
				LEFT JOIN mdl_org_link_user ol on l.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$totalTime_sql
				and l.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and l.userid != 2
				GROUP BY ol.org_id) as g
			on o.id = g.orgid
			LEFT JOIN -- 单位总人数
				(select count(1) as usercount,ol.org_id as orgid from mdl_org_link_user ol
					where ol.org_id in ( $orgIDStr )
					and ol.user_id not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
					and ol.user_id != 2
					GROUP BY ol.org_id ) as h
			on o.id = h.orgid
			LEFT JOIN -- 已完成课时
				(select count(1) as courseschedulecount,ol.org_id as orgid from mdl_course_modules_completion cmc
				LEFT JOIN mdl_org_link_user ol on cmc.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				$courseschedule_sql
				and cmc.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and cmc.userid != 2
				GROUP BY ol.org_id) as i
			on o.id = i.orgid
			where o.id in ( $orgIDStr )
			GROUP BY o.id";
	$users = $DB -> get_records_sql($sql);

	//如果当前单位下有子单位，需要考虑将其二级以下的子单位数据合并到当前所属的二级单位
	if(count($users) > 1){
		$users = merge_sub_org_data($orgid,$users);
	}

	//<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
	$output = '
		<div  class="table_text_center">
		<div class="table_title" >学习排行榜：</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>单位</td>
					<td onclick="sortTable(\'tblSort\',2,\'int\');" style="cursor:pointer">登录数</td>
					<td onclick="sortTable(\'tblSort\',3,\'int\');" style="cursor:pointer">笔记</td>
					<td onclick="sortTable(\'tblSort\',4,\'int\');" style="cursor:pointer">评论</td>
					<td onclick="sortTable(\'tblSort\',5,\'int\');" style="cursor:pointer">点赞</td>
					<td onclick="sortTable(\'tblSort\',6,\'int\');" style="cursor:pointer">星评</td>
					<td onclick="sortTable(\'tblSort\',7,\'int\');" style="cursor:pointer">证书</td>
					<td onclick="sortTable(\'tblSort\',8,\'float\');" style="cursor:pointer">已学学时</td>
					<td onclick="sortTable(\'tblSort\',9,\'float\');" style="cursor:pointer">已学课时</td>
					<td>总人数</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){
		$output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->orgname.'</td>
				<td>'.null_to_zero($user->logincount).'</td>
				<td>'.null_to_zero($user->notecount).'</td>
				<td>'.null_to_zero($user->commentcount).'</td>
				<td>'.null_to_zero($user->likecount).'</td>
				<td>'.null_to_zero($user->scorecount).'</td>
				<td>'.null_to_zero($user->badgecount).'</td>
				<td>'.null_to_zero($user->totaltime).'</td>
				<td>'.null_to_zero($user->courseschedulecount).'</td>
				<td>'.null_to_zero($user->usercount).'</td>
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
function echo_single_course($orgid,$start_time,$end_time,$remove_role,$courseid){
	global $DB;
	require_once("../lib/my_lib.php");
	$orgIDStr = get_sub_self_orgid($orgid);//获取当前单位及下级单位id
	$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
	$noteTime_sql = handle_sql_time('time',$start_time,$end_time);
	$commentTime_sql = handle_sql_time('commenttime',$start_time,$end_time);
	$totalTime_sql =  handle_sql_time('timecreated',$start_time,$end_time);
	$courseCriteriaCompletion_sql =  handle_sql_time('timecompleted',$start_time,$end_time);
	$courseCompletion_sql =  handle_sql_time('timecompleted',$start_time,$end_time);
	$courseschedulecount_sql =  handle_sql_time('timemodified',$start_time,$end_time);

	$courseCriteria = get_course_module_criteria($courseid,1);//获取课程活动规则
	$courseSchedule_sqlStr = "";
	if(!$courseCriteria){//如果没设置任何活动规则
		$courseSchedule_sqlStr = "LEFT JOIN -- 课程进度,单位进度总和（待求人均进度）
									(select 0 as courseschedule,o.id as orgid
										from mdl_org o
										WHERE o.id in ( $orgIDStr )
									GROUP BY o.id ) as d
								on o.id = d.orgid";
	}else{
		$countCriteria = count($courseCriteria);//规则的数量
		$courseCriteriaStr = implode(',',$courseCriteria);
		$aggr = get_course_criteria_aggregration($courseid);
		if($aggr==1){//如果要完成全部
			$criteria_sqlStr = " select (FORMAT((a.count/$countCriteria),2)*100) as courseschedule,a.userid,a.orgid ";
		}elseif($aggr==2){//如果只需完成其一
			$criteria_sqlStr = " select (IF(a.count=0,0,100)) as courseschedule,a.userid,a.orgid ";
		}
		//课程进度
		$courseSchedule_sqlStr = "LEFT JOIN -- 课程进度,单位进度总和（待求人均进度）
									(select SUM(temp2.courseschedule) as courseschedule,temp2.orgid from
										(select MAX(temp.courseschedule) as courseschedule,temp.userid,temp.orgid from
											(
												(   $criteria_sqlStr
													from
													(select  count(*) as count,c.userid,ol.org_id as orgid
														FROM mdl_course_completion_crit_compl c
													 LEFT JOIN mdl_org_link_user ol on c.userid = ol.user_id
													where c.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
													and c.userid not in ( select userid from mdl_role_assignments where roleid in ( 14,27 ) )
													and c.userid != 2
													and c.course = $courseid
													and c.criteriaid in ( $courseCriteriaStr )
													$courseCriteriaCompletion_sql
													GROUP BY c.userid ) as a
												)
												UNION ALL -- 已完成（可能是设定人工设为完成）
												(select 100 as courseschedule,a.userid,ol.org_id as orgid from mdl_course_completions a
													LEFT JOIN mdl_org_link_user ol on a.userid = ol.user_id
													where a.course = $courseid
													AND a.timecompleted != ''
													and a.userid not in ( select userid from mdl_role_assignments where roleid in ( 14,27 ) )
													and a.userid != 2
													and a.userid in (select ol.user_id from mdl_org_link_user ol where ol.org_id in ( $orgIDStr ) )
													$courseCompletion_sql
												 )
											) as temp
										GROUP BY temp.userid
										) as temp2
									GROUP BY temp2.orgid) as d
								on o.id = d.orgid";
	}


	$sql = "select o.id,o.`name` as orgname,a.notecount,b.commentcount,c.totaltime,d.courseschedule,h.usercount,i.courseschdulecount
			from mdl_org as o
			LEFT JOIN -- 课程笔记
				(select count(n.id) as notecount,ol.org_id as orgid from mdl_note_my n
				LEFT JOIN mdl_org_link_user ol on n.userid = ol.user_id
				where n.notetype = 1
				and n.courseid = $courseid
				$noteTime_sql
				and n.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and n.userid != 2
				and ol.org_id in ( $orgIDStr )
				GROUP BY ol.org_id) as a
			on o.id = a.orgid
			LEFT JOIN -- 评论
			(select SUM(temp.count) as commentcount,temp.orgid from
				(select count(a.id) as count,ol.org_id as orgid from mdl_comment_article_my a
				LEFT JOIN mdl_org_link_user ol on a.userid = ol.user_id
				JOIN mdl_course_modules b on b.id=a.articleid and b.course=$courseid
				where ol.org_id in ( $orgIDStr )
				$commentTime_sql
				and a.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and a.userid != 2
				GROUP BY ol.org_id
				union all
				select count(c.id) as count,ol.org_id as orgid from mdl_comment_course_my c
				LEFT JOIN mdl_org_link_user ol on c.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				and courseid=$courseid
				$commentTime_sql
				and c.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and c.userid != 2
				GROUP BY ol.org_id
				union all
				select count(v.id) as count,ol.org_id as orgid from mdl_comment_video_my v
				LEFT JOIN mdl_org_link_user ol on v.userid = ol.user_id
				JOIN mdl_course_modules b on b.id=v.modid and b.course=$courseid
				where ol.org_id in ( $orgIDStr )
				$commentTime_sql
				and v.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and v.userid != 2
				GROUP BY ol.org_id
				) as temp
				group by temp.orgid) as b
			on o.id = b.orgid
			LEFT JOIN -- 已完成学时
				(select FORMAT(((count(1)*60)/3600),1) as totaltime,ol.org_id as orgid from mdl_logstore_standard_log l
				LEFT JOIN mdl_org_link_user ol on l.userid = ol.user_id
				where ol.org_id in ( $orgIDStr )
				and l.courseid = $courseid
				$totalTime_sql
				and l.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
				and l.userid != 2
				GROUP BY ol.org_id) as c
			on o.id = c.orgid
			-- 课程进度,单位进度总和（待求人均进度）
			$courseSchedule_sqlStr
			LEFT JOIN -- 单位总人数
				(select count(1) as usercount,ol.org_id as orgid from mdl_org_link_user ol
					where ol.org_id in ( $orgIDStr )
					and ol.user_id not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
					and ol.user_id != 2
					GROUP BY ol.org_id ) as h
			on o.id = h.orgid
			LEFT JOIN -- 已完成课时
				(select  count(*) as courseschdulecount,ol.org_id as orgid from mdl_course_modules_completion cmc
							LEFT JOIN mdl_org_link_user ol on cmc.userid = ol.user_id
							where ol.org_id in ( $orgIDStr )
							and cmc.coursemoduleid in (select cm.id from mdl_course_modules cm  where cm.course = $courseid and cm.`completion` in (1,2) )
							$courseschedulecount_sql
							and cmc.userid not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
							and cmc.userid != 2
							-- and cmc.completionstate = 1
							GROUP BY ol.org_id) as i
			on o.id = i.orgid
			where o.id in ( $orgIDStr )
			GROUP BY o.id";
	$users = $DB -> get_records_sql($sql);
	$sql = "select c.id,c.fullname from mdl_course c where c.id = $courseid";
	$course = $DB->get_record_sql($sql);

	//如果当前单位下有子单位，需要考虑将其二级以下的子单位数据合并到当前所属的二级单位
	if(count($users) > 1){
		$users = merge_sub_org_data($orgid,$users);
	}

	//<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
	$output = '
		<div  class="table_text_center">
		<div class="table_title">学习排行榜：</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>单位</td>
					<td onclick="sortTable(\'tblSort\',2,\'int\');" style="cursor:pointer">笔记</td>
					<td onclick="sortTable(\'tblSort\',3,\'int\');" style="cursor:pointer">评论</td>
					<td onclick="sortTable(\'tblSort\',4,\'my_string\');" style="cursor:pointer">平均课程进度</td>
					<td onclick="sortTable(\'tblSort\',5,\'float\');" style="cursor:pointer">已学学时</td>
					<td onclick="sortTable(\'tblSort\',6,\'float\');" style="cursor:pointer">已学课时</td>
					<td>总人数</td>
				</tr>
			</thead>
			<tbody>';
	$n=1;
	foreach($users as $user){

		if(!$courseCriteria){
			$schedule = '课程未设进度跟踪';
		}else{
			$schedule = null_to_zero(round(($user->courseschedule/$user->usercount),1)).'%';
		}

		$output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->orgname.'</td>
				<td>'.null_to_zero($user->notecount).'</td>
				<td>'.null_to_zero($user->commentcount).'</td>
				<td>'.$schedule.'</td>
				<td>'.null_to_zero($user->totaltime).'</td>
				<td>'.null_to_zero($user->courseschdulecount).'</td>
				<td>'.null_to_zero($user->usercount).'</td>
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


//输出学习榜
function echo_learn_org($rank_learn,$orgname){
	$output = '
		<div style="width:45%;float:right;margin-right:2%;text-align:center;">
			<div style="font-weight:600;">'.$orgname.':单位学习榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>					
						<td>总人数</td>
						<td>总活动完成数</td>
						<td>平均活动完成数</td>
					</tr>
				</thead>
				<tbody>';
	$n=1;
	foreach($rank_learn as $org){
		$output .= '	
			<tr>
				<td>'.$n.'</td>
				<td>'.$org["orgname"].'</td>
				<td>'.$org["totalPeople"].'</td>
				<td>'.$org["totalEvent"].'</td>
				<td>'.$org["avgcount"].'</td>
			</tr>';
		$n++;
	}
	$output .= '	
			</tbody>
		</table>
		</div>
	';
	echo $output;
}

//输出积极榜
function echo_active_org($rank_active,$orgname){
	$output = '
		<div style="width:45%; float:left; margin-left:2%;text-align:center;">
			<div style="font-weight:600;">'.$orgname.':单位积极榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>总人数</td>
						<td>总事件完成数</td>
						<td>平均事件完成数</td>
					</tr>
				</thead>
				<tbody>';
	$n=1;
	foreach($rank_active as $org){
		$output .= '	
			<tr>
				<td>'.$n.'</td>
				<td>'.$org["orgname"].'</td>
				<td>'.$org["totalPeople"].'</td>
				<td>'.$org["totalPeople"]*$org["avgcount"].'</td>
				<td>'.$org["avgcount"].'</td>
			</tr>';
		$n++;
	}
	$output .= '	
			</tbody>
		</table>
		</div>
	';
	echo $output;
}

/** start 求当前子单位中的‘人数’
 * @ param org 当前子单位对象;
 * @ return  totalPeople 当前子单位下的总人数;
 */
function get_totalPeople_org($org,$remove_role){
	global $DB;

	$totalPeople = 0;//当前子单位总人数
	//1、当前子单位下的人员
	$totalPeopleArray = $DB -> get_records_sql('
		select a.user_id, b.firstname,b.lastname
		from mdl_org_link_user a
		join mdl_user b on b.id= a.user_id
		where org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
	');
	$totalPeople = count($totalPeopleArray);

	//2、当前子单位的下级子单位
	$subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
	$subOrgPeople = 0;//各下级子单位的人数之和
	foreach($subOrgs as $subOrg){
		$subOrgPeople = $subOrgPeople + get_totalPeople_org($subOrg,$remove_role);//这里递归调用
	}

	$totalPeople = $totalPeople + $subOrgPeople;//当前单位下的人数 + 当前单位的下级子单位人数

	return $totalPeople;
}
/** end */

/** start 学习榜 求当前子单位中某时间段内的‘完成事件数’
 * @ param org 当前子单位对象;
 * @ return  totalEvent 当前子单位下的‘总事件数’;
 */
function get_totalLearnEvent_org($org,$start_time,$end_time,$remove_role){

	$sql = 'and b.timemodified > '.$start_time .' and b.timemodified < '.$end_time;

	global $DB;
	$totalEvent = 0;//当前子单位下的人员事件完成数
	//1、当前子单位下的人员事件完成数
	$sumcount = $DB -> get_record_sql('
		select count(1) as count
		from mdl_org_link_user a
		join mdl_course_modules_completion b on b.completionstate=1 and b.userid=a.user_id
		where a.org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
		'.$sql.'
	');
	$totalEvent = $sumcount->count;

	//2、当前子单位的下级子单位
	$subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
	$subOrgEvent = 0;//各下级子单位的人员完成事件之和
	foreach($subOrgs as $subOrg){
		$subOrgEvent = $subOrgEvent + get_totalLearnEvent_org($subOrg,$start_time,$end_time,$remove_role);//这里递归调用
	}
	$totalEvent = $totalEvent + $subOrgEvent;//当前单位下的人员完成事件 + 当前单位的下级子单位人员完成事件

	return $totalEvent;
}
/** end */

/** start 积极榜 求当前子单位中某时间段内的‘完成事件数’
 * @ param org 当前子单位对象;
 * @ return  totalEvent 当前子单位下的‘总事件数’;
 */
function get_totalActiveEvent_org($org,$start_time,$end_time,$remove_role){

	$sql = 'and b.timecreated > '.$start_time .' and b.timecreated < '.$end_time;

	global $DB;
	$totalEvent = 0;//当前子单位下的人员事件完成数
	//1、当前子单位下的人员事件完成数
	$sumcount = $DB -> get_record_sql('
		select COUNT(a.user_id) as count
		from mdl_org_link_user a
		join mdl_logstore_standard_log b on b.userid = a.user_id
		where a.org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
		'.$sql.'
	');
	$totalEvent = $sumcount->count;

	//2、当前子单位的下级子单位
	$subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
	$subOrgEvent = 0;//各下级子单位的人员完成事件之和
	foreach($subOrgs as $subOrg){
		$subOrgEvent = $subOrgEvent + get_totalActiveEvent_org($subOrg,$start_time,$end_time,$remove_role);//这里递归调用
	}
	$totalEvent = $totalEvent + $subOrgEvent;//当前单位下的人员完成事件 + 当前单位的下级子单位人员完成事件

	return $totalEvent;
}
/** end */

/** Start 多维数组排序 */
function sort_multarray(&$array) {
	$newArray = array();
	foreach($array as $item){
		$newArray[] = $item['avgcount'];
	}
	array_multisort($newArray, SORT_DESC, $array);
}
/** end */



//计算单位在积极榜的平均事件数
function calculate_active_org($orgid,$sumuserscount,$start_time,$end_time,$remove_role){
//function calculate_active_org($orgid,$sumuserscount,$timeid){
//Start 去掉周月总的查询
//	if($timeid==1){
//	$mytime= time()-3600*24*7;
//	$sql='and b.timecreated>'.$mytime;
//	}
//	elseif($timeid==2){
//		$mytime= time()-3600*24*30;
//		$sql='and b.timecreated>'.$mytime;
//	}
//	elseif($timeid==3){
//		$sql='';
//	}
//End 去掉周月总的查询

	//按照时间段进行查询
	$sql = 'and b.timecreated > '.$start_time .' and b.timecreated < '.$end_time;

	global $DB;
	$sumcount = $DB -> get_record_sql('
		select COUNT(a.user_id) as count
		from mdl_org_link_user a
		join mdl_logstore_standard_log b on b.userid = a.user_id
		where a.org_id='.$orgid.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
		'.$sql.'
	');
	if($sumuserscount==0){
		return 0;
	}
	else{
		return round($sumcount->count/$sumuserscount);
	}
}

//计算单位在学习榜的平均事件数
function calculate_learn_org($orgid,$sumuserscount,$start_time,$end_time,$remove_role){
//function calculate_learn_org($orgid,$sumuserscount,$timeid){
//Start 去掉周月总的查询
//	if($timeid==1){
//	$mytime= time()-3600*24*7;
//	$sql='and b.timemodified>'.$mytime;
//	}
//	elseif($timeid==2){
//		$mytime= time()-3600*24*30;
//		$sql='and b.timemodified>'.$mytime;
//	}
//	elseif($timeid==3){
//		$sql='';
//	}
//End 去掉周月总的查询
	//按照时间段进行查询
	$sql = 'and b.timemodified > '.$start_time .' and b.timemodified < '.$end_time;

	global $DB;
	$sumcount = $DB -> get_record_sql('
		select count(1) as count
		from mdl_org_link_user a
		join mdl_course_modules_completion b on b.completionstate=1 and b.userid=a.user_id
		where a.org_id='.$orgid.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
		'.$sql.'
	');
	if($sumuserscount==0){
		return 0;
	}
	else{
		return round($sumcount->count/$sumuserscount);
	}
}

//快速排序-倒叙
function quick_sort_swap(&$array, $start, $end) {
	if($end <= $start) return;
	$keycount = $array[$start]["avgcount"];
	$keyname = $array[$start]["orgname"];
	$left = $start;
	$right = $end;
	while($left < $right) {
		while($left < $right && $array[$right]["avgcount"] <= $keycount)
			$right--;
		$array[$left] = $array[$right];
		while($left < $right && $array[$left]["avgcount"] >= $keycount)
			$left++;
		$array[$right] = $array[$left];

	}
	$array[$right]["avgcount"] = $keycount;
	$array[$right]["orgname"] = $keyname;
	quick_sort_swap($array, $start, $right - 1);
	quick_sort_swap($array, $right+1, $end);
}

?>

