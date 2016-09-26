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
//查询所有下级单位id
$sumorgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$orgid);
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

