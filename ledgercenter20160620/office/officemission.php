<script>
$('.lockpage').hide();
</script>

<?php

/** 单位台账》台账任务  徐东威  20160307*/

require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id

global $DB;

$mission = $DB->get_record_sql("select * from mdl_mission_my m where m.id = $missionid");//获取任务的详细信息
$missionName = $mission->mission_name;//任务名称
$org=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
$orgName = $org->name;
//查询所有下级单位id
$sumorgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$orgid);
$rank_mission = array();//单位任务数组
foreach($sumorgs as $org){
    //分别查下级单位id的所有人,筛掉单位账号
    $sumusers = $DB -> get_records_sql('
		select a.user_id, b.firstname,b.lastname
		from mdl_org_link_user a
		join mdl_user b on b.id= a.user_id
		where org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid =14
		)
	');

    //任务榜处理，处理每个单位中人员的任务记录
    $orgMission = handle_mission($org,$sumusers,$missionid);

    $rank_mission[$org->id] = array('name'=>$orgMission->name,'userCount'=>$orgMission->userCount,'requiredAvg'=>$orgMission->requiredAvg,'optionalAvg'=>$orgMission->optionalAvg);//将该单位的数据存入数组

}

$new_rank_mission = my_sort($rank_mission);//排序

echo_mission($new_rank_mission,$orgName,$missionName);//输出任务完成情况





/** 名次排序
 *  说明：按照完成情况排序
 */
function my_sort($rank_mission){

//    $count = array();//任务人数
    $requiredArray = array();//必修完成数
    $optionalArray = array();//选修完成数
    $sumArray = array();//必修、选修数之和
    foreach ($rank_mission as $obj) {
//        $count[] = $obj['userCount'];
        $requiredArray[] = $obj['requiredAvg'];
        $optionalArray[] = $obj['optionalAvg'];
        $sumArray[] = $obj['requiredAvg'] + $obj['optionalAvg'];
    }
    array_multisort($sumArray, SORT_DESC,$requiredArray, SORT_DESC, $optionalArray, SORT_DESC, $rank_mission);//排序：按照必修课数和选修课数排

    return $rank_mission;
}

/** 处理每个单位中人员的任务记录
 * @param  $sumusers 单位下的人员
 * @param  $missionid   任务id
 * @return  $missionObj  包含：单位名称 接受任务人数 平均完成必修课 平均完成选修课
 */
function handle_mission($org,$sumusers,$missionid){

    global $DB;

    $mission = $DB->get_record_sql("select * from mdl_mission_my m where m.id = $missionid ");
    $requiresCoursesID = explode(',',$mission->required_course_id);//必修课id
    $optionalCoursesID = explode(',',$mission->optional_course_id);//选修课id

    $users = array();//人员id数组
    foreach($sumusers as $sumuser){
        $users[] = $sumuser->user_id;
    }
    $usersStr = implode(',',$users);//生成例如 ：5,6,3,11 字符串

    //获取该单位下该任务的人员
    $missionUsers = array();
    if($usersStr != ''){
        $missionUsers = $DB->get_records_sql("select m.user_id,m.mission_id from mdl_mission_user_my m where m.mission_id = $missionid and m.user_id in ($usersStr)");
    }

    //计算完成课程的数量
    $requiredCount = 0;//该单位总完成的必修课数量
    $optionalCount = 0;//该单位总完成的选修课数量
    foreach($missionUsers as $missionUser){
        //必修课完成状态的判断
        foreach($requiresCoursesID as $requiresCourseID){
            $state1 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($requiresCourseID) and c.userid = $missionUser->user_id ");
            if($state1){
                $requiredCount++;//必修课完成数量加1
            }
        }
        //选修课完成状态的判断
        foreach($optionalCoursesID as $optionalCourseID){
            $state2 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($optionalCourseID) and c.userid = $missionUser->user_id ");
            if($state2){
                $optionalCount++;//选修课完成数量加1
            }
        }
    }

    //求平均完成数,只算选择该任务的人员
    $count = count($missionUsers);//人数
    if($count){ //人数不为零
        $requiredAvg = $requiredCount / $count;//平均必修课数量
        $optionalAvg = $optionalCount / $count;//平均选修课数量
    }else{
        $requiredAvg = 0;
        $optionalAvg = 0;
    }

    $missionObj = new stdClass();//单位任务对象
    $missionObj->name = $org->name;//单位名
    $missionObj->userCount = $count;//接受任务人数
    $missionObj->requiredAvg = $requiredAvg;//平均完成必修课数
    $missionObj->optionalAvg = $optionalAvg;//平均完成选修课数

    return $missionObj;
}


//输出任务完成情况
function echo_mission($rank_mission,$orgname,$missionName){

    $output = '
		<!-- <div style="width:35%;float:right;margin-right:10%;text-align:center;"> -->
		<div style="width:100%;margin-right:10%;text-align:center;">
			<div style="font-weight:600;">'.$orgname.' : '.$missionName.'排行榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>平均完成必修课</td>
						<td>平均完成选修课</td>
						<td>接受任务人数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_mission as $org){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$org["name"].'</td>
				<td>'.$org["requiredAvg"].'</td>
				<td>'.$org["optionalAvg"].'</td>
				<td>'.$org["userCount"].'</td>
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











