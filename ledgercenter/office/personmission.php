<script>
$('.lockpage').hide();
</script>

<?php

/** 台账数据中心》个人台账》台账任务统计  徐东威 20160307 */

require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id

global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
// echo $orgname->name;

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

echo_missions($orgid,$missionid,$sumusers,$orgname->name);//任务统计


/** 任务统计  */
function echo_missions($orgid,$missionid,$sumusers,$orgname){

    global $DB;

    // 获取‘该单位下该任务相关’的人员信息
    $users = $DB->get_records_sql("
                select u.id,u.lastname,u.firstname from mdl_user u join mdl_mission_user_my mum on u.id = mum.user_id
                where u.id in (
                            select user_id
                            from mdl_org_link_user
                            where org_id= $orgid and user_id not in (
                                select userid
                                from mdl_role_assignments
                                where roleid =14
                            )
		        )
		        and mum.mission_id = $missionid;
		");

    //获取任务的具体信息
//    $mission = $DB->get_record_sql("select m.mission_name,m.required_course_id,m.optional_course_id,m.time_end from mdl_mission_my m where m.id = $missionid");
    $mission = $DB->get_record_sql("select * from mdl_mission_my m where m.id = $missionid");
    $missionName = $mission->mission_name;//任务名称
    $requiredCoursesID = explode(',',$mission->required_course_id);//必修课
    $optionalCoursesID = explode(',',$mission->optional_course_id);//选修课


    //统计学生的任务信息
    $userArray = array();
    foreach($users as $user){

        $requiredCount = 0;//必修课完成数量
        $optionalCount = 0;//选修课完成数量
        //获取学生的必修课课程完成情况
        foreach($requiredCoursesID as $requiredCourseID){
            $state1 = $DB->get_record_sql("select c.userid,c.timecompleted from mdl_course_completion_crit_compl c where c.userid = $user->id and c.course = $requiredCourseID");
            if($state1){
                $requiredCount++;//必修课完成数量加1
            }
        }
        //获取学生的选修课课程完成情况
        foreach($optionalCoursesID as $optionalCourseID) {
            $state2 = $DB->get_record_sql("select c.userid,c.timecompleted from mdl_course_completion_crit_compl c where c.userid = $user->id and c.course = $optionalCourseID");
            if ($state2) {
                $optionalCount++;//选修课完成数量加1
            }
        }

        $userArray[$user->id] =array('userid'=>$user->id,'lastname'=>$user->lastname,'firstname'=>$user->firstname,'requiredCount'=>$requiredCount,'optionalCount'=>$optionalCount);

    }

    //名次排序
    $requiredArray = array();//必修完成数
    $optionalArray = array();//选修完成数
    $sumArray = array();//必修、选修数之和
    foreach ($userArray as $user) {
        $requiredArray[] = $user['requiredCount'];
        $optionalArray[] = $user['optionalCount'];
        $sumArray[] = $user['requiredCount'] + $user['optionalCount'];
    }
    array_multisort($sumArray, SORT_DESC,$requiredArray, SORT_DESC, $optionalArray, SORT_DESC, $userArray);//排序：按照必修课数和选修课数排

//    echo_missionTable($userArray,$sumusers,$orgname,$missionName);//输出任务榜单列表
    echo_missionTable($userArray,$sumusers,$orgname,$mission);//输出任务榜单列表

}
/** end */


/** 输出任务榜单列表 */
function echo_missionTable($users,$sumusers,$orgname,$mission){

    $output = '
		<!-- <div style= "width:80%; float:left; margin-left:5%;text-align:center;"> -->
		<div style= "width:90%; margin-left:5%;text-align:center;">
		<div style= "font-weight:600;">'. $orgname .':'. $mission->missionName .' 排行榜</div>
		<p></p>
		<table class="table table-striped table-bordered" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td>接受任务</td>
					<td>已完成/需完成&nbsp;&nbsp;必修课</td>
					<td>已完成/至少需完成&nbsp;&nbsp;选修课</td>
				</tr>
			</thead>
			<tbody>';
    $n=1;
    foreach($users as $user){ //输出有信息的人员
        unset($sumusers[$user["userid"]]);//从单位人员数组中删除该任务相关的人员
        $output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user["lastname"].$user["firstname"].'</td>
				<td>是</td>
				<td>'.$user["requiredCount"].'/'.$mission->required_course_num.'</td>
				<td>'.$user["optionalCount"].'/'.$mission->optional_choice_compeltions.'</td>
			</tr>
			';
        $n++;
    }
    //输出没有数据的人（即单位人员数组中任务不相关的人员）
    foreach($sumusers as $sumuser){
        $output .='
				<tr>
					<td>'.$n.'</td>
					<td>'.$sumuser->lastname.$sumuser->firstname.'</td>
					<td>否</td>
					<td>0/'.$mission->required_course_num.'</td>
					<td>0/'.$mission->optional_choice_compeltions.'</td>
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













