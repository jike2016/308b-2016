<script>
$('.lockpage').hide();
</script>
<!--表格排序-->
<script type="text/javascript" src="js/my/sortTable.js"></script>
<!--表格排序-->
<script>
    $(document).ready(function(){
        //查看详细
        $('.detialLink').on('click',function(){
            var userid = $(this).attr('userid');
            var missionid = $(this).attr('missionid');
//            console.log(userid);
//            console.log(missionid);
////            $('.table-box').load('office/personmissiondetial.php?userid='+userid+'&missionid='+missionid);
//            var detialHtml = '<div id="showdetial" style="width: 100%;height:100%;background: #fff;position: absolute;top:0;left:0;overflow-y: auto;" ></div>';
//            $('.table-box').append(detialHtml);
//            $('#showdetial').load('office/personmissiondetial.php?userid='+userid+'&missionid='+missionid);
            window.open('office/personmissiondetial.php?userid='+userid+'&missionid='+missionid);
        });
    });
</script>


<?php

/** 台账数据中心》个人台账》台账任务统计  徐东威 20160307 */

require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');
global $DB;
$orgname=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
// echo $orgname->name;
echo_missions_rank_list($orgid,$orgname->name,$remove_role,$missionid);//输出任务排行榜

exit;//*********************************************

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

echo_missions($orgid,$missionid,$sumusers,$orgname->name,$remove_role);//任务统计

//*******************************************************************



/**
 * 获取用户在某课程的进度
 * @param $UserStr
 * @param $courseid
 * @return array {用户id，课程进度，课程是否完成标志 }
 */
function get_course_criteria_completion_schedule($UserStr,$courseid){
    global $DB;
    $result = array();
    require_once("../lib/my_lib.php");
    $courseCriteria = get_course_module_criteria($courseid,1);//获取课程活动规则
    if(!$courseCriteria){//如果没设置任何活动规则
        $sql = "select ol.user_id as userid,0 as courseschedule,0 as completionflag
                                    from mdl_org_link_user ol
                                    WHERE ol.user_id  in ( $UserStr )";
    }else {
        $countCriteria = count($courseCriteria);//规则的数量
        $courseCriteriaStr = implode(',',$courseCriteria);
        $aggr = get_course_criteria_aggregration($courseid);//规则的组合方式
        if($aggr==1){//如果要完成全部
            $criteria_sqlStr = " select a.userid,(FORMAT((a.count/$countCriteria),2)*100) as courseschedule,IF((FORMAT((a.count/$countCriteria),2)*100)=100,1,0) as completionflag ";
        }elseif($aggr==2){//如果只需完成其一
            $criteria_sqlStr = " select a.userid,(IF(a.count=0,0,100)) as courseschedule,IF((IF(a.count=0,0,100)=100),1,0) as completionflag ";
        }
        $sql = " select temp.userid,MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag  from
                (
                    (   $criteria_sqlStr
                            from
                            (select c.userid,COUNT(1) as count
                                    from mdl_course_completion_crit_compl c
                                    where c.course = $courseid
                                    and c.criteriaid in ($courseCriteriaStr)
                                    and c.userid in ( $UserStr )
                                    GROUP BY c.userid ) as a
                            GROUP BY a.userid
                    )
                    UNION ALL -- 已完成（可能是设定人工设为完成）
                    (select a.userid,100 as courseschedule,1 as completionflag
                        from mdl_course_completions a
                        where a.course = $courseid
                        and a.userid in ( $UserStr )
                        and a.timecompleted != ''
                        GROUP BY a.userid
                     )
                ) as temp
                GROUP BY temp.userid";
    }
    $result = $DB->get_records_sql($sql);

    return $result;
}


/**
 * 获取人员任务课程完成情况
 * @param $UserStr  人员ids
 * @param $courseidStr  课程ids
 * @return array {用户id，必修课（或选修课）课程进度之和，完成的课程数}
 */
function get_course_completion_data2($UserStr,$courseidStr){
    $courseids = explode(',',$courseidStr);
    $result = array();
    foreach($courseids as $courseid){
        $schedule = get_course_criteria_completion_schedule($UserStr,$courseid);//获取单课程的进度
        //合并各课程进度
        foreach($schedule as $key=>$value){
            $result[$key]->userid = $value->userid;
            $result[$key]->sumcourseschedule += $value->courseschedule;
            $result[$key]->completioncount += $value->completionflag;
        }
    }

    return $result;
}


/**
 * 获取人员课程完成情况 (废弃)
 * @param $UserStr
 * @param $courseidStr
 * @return array
 */
function get_course_completion_data($UserStr,$courseidStr){
    global $DB;
    $sql = "select temp2.userid,SUM(temp2.courseschedule) as sumcourseschedule,SUM(temp2.completionflag) as completioncount from (
                    select MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag,temp.userid,temp.courseid  from
                            (	 -- 正常进度统计
                                (select (FORMAT((a.count/b.count),2)*100) as courseschedule,IF((FORMAT((a.count/b.count),2)*100)=100,1,0) as completionflag,a.userid,a.courseid
                                        from
                                        (select  count(*) as count,cmc.userid,cm.course as courseid
                                            from mdl_course_modules_completion cmc
                                            JOIN mdl_course_modules cm on cmc.coursemoduleid = cm.id
                                            where cm.course in ( $courseidStr )
                                            and cmc.userid in ( $UserStr )
                                            and cm.`completion` in (1,2)
                                            and cmc.completionstate = 1
                                            GROUP BY cmc.userid,cm.course ) as a,
                                        (select count(*) as count,cm.course as courseid
                                            from mdl_course_modules cm
                                            where cm.course in ( $courseidStr )
                                            and cm.`completion` in (1,2)
                                            GROUP BY cm.course) as b
                                        where a.courseid = b.courseid
                                        GROUP BY a.userid,a.courseid
                                )
                                UNION ALL -- 已完成（可能是设定人工设为完成）
                                (select 100 as courseschedule,1 as completionflag,a.userid,a.course as courseid
                                    from mdl_course_completion_crit_compl a
                                    where a.course in ( $courseidStr )
                                    and a.userid in ( $UserStr )
                                    GROUP BY a.userid,a.course
                                 )
                            ) as temp
                    GROUP BY temp.userid,temp.courseid
                    ORDER BY temp.userid
                    ) as temp2
                    GROUP BY temp2.userid";
    $result = $DB->get_records_sql($sql);
    return $result;
}
/** *end (废弃) */

/**Start 台账任务排行榜 xdw */
function echo_missions_rank_list($orgid,$orgname,$remove_role,$missionid){
    global $DB;
    require_once("../lib/my_lib.php");
    $UserStr = get_org_user_array($orgid,$remove_role,$type=1);//获取当前单位下的人员
    $UserStr = (!$UserStr) ? 0 : $UserStr;//判断是否有人员
    $sql = "SELECT m.id,m.mission_name,m.required_course_num,m.required_course_id,m.optional_course_num,m.optional_course_id,m.optional_choice_compeltions
                from mdl_mission_my m WHERE m.id = $missionid";
    $mission = $DB->get_record_sql($sql);

    //获取人员接受任务的情况
    $sql = "SELECT u.id,u.username,u.firstname,u.lastname,a.taskflag,b.orgname
                FROM mdl_user u
                LEFT JOIN -- 是否接受任务
                    (SELECT mum.user_id as userid,1 as taskflag
                    FROM mdl_mission_user_my mum
                    WHERE mum.mission_id = $missionid ) as a
                ON u.id = a.userid
                LEFT JOIN
                    (select o.`name` as orgname,ol.user_id as userid
                        from mdl_org_link_user ol
                        left join mdl_org o on o.id = ol.org_id
                        ) as b
                ON u.id = b.userid
                WHERE u.id IN ( $UserStr )";
    $users = $DB -> get_records_sql($sql);

    //获取必修课统计数据
    $requiredcourses = array();
    if($mission->required_course_num){
        $requirsecourseidStr = $mission->required_course_id;
//        $requiredcourses = get_course_completion_data($UserStr,$requirsecourseidStr);
        $requiredcourses = get_course_completion_data2($UserStr,$requirsecourseidStr);
    }

    //获取选修课统计数据
    $opletioncourses = array();
    if($mission->optional_course_num){
        $optioncourseidStr = $mission->optional_course_id;
//        $opletioncourses = get_course_completion_data($UserStr,$optioncourseidStr);
        $opletioncourses = get_course_completion_data2($UserStr,$optioncourseidStr);
    }

    //合并统计数据
    foreach($users as $user){
        if($user->taskflag==1){
//            对没有设置必/选课程的判断、、、、、
            //必修课
            if($requiredcourses[$user->id]){
                $user->requiredcoursecount = $requiredcourses[$user->id]->completioncount;
                $user->requiredcourseschdule = $requiredcourses[$user->id]->sumcourseschedule;
            }

            //选修课
            if($opletioncourses[$user->id]){
                $user->optionalcoursecount = $opletioncourses[$user->id]->completioncount;
                $user->optionalcourseschdule = $opletioncourses[$user->id]->sumcourseschedule;
            }


            //任务进度
            //此处选修课的进度统计不够精确，应该是获取至少需要选修课程数量如：x 的前 x 门选修课最高纪录来求平均
            if($mission->required_course_num){//必修课为空
                $temprequired =  ($user->requiredcourseschdule/$mission->required_course_num) * ($mission->required_course_num/($mission->required_course_num + $mission->optional_choice_compeltions));
            }else{
                $temprequired = 100*($mission->required_course_num / ($mission->required_course_num + $mission->optional_choice_compeltions));;
            }
            if($mission->optional_choice_compeltions){//选修课为空
                if($user->optionalcoursecount<$mission->optional_choice_compeltions) {//选修课没有达到规定数量
                    $tempoptional = ($user->optionalcourseschdule / $mission->optional_course_num) * ($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
                }else{//满足规定数量
                    $tempoptional = 100*($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
                }
            }else{
                $tempoptional = 100*($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
            }

            $tempschedule =  $temprequired+$tempoptional;
            if(!($mission->required_course_num || $mission->optional_choice_compeltions )){//必修课和选修课均为空
                $tempschedule = 100;
            }
            $user->taskschedule = round($tempschedule,0);

        }
    }


    //<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
    $output = '
		<div  class="table_text_center one_table">
		<div class="table_title_lg" >'.$orgname.'：个人 任务排行榜（任务：'.$mission->mission_name.'）</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>姓名</td>
					<td onclick="sortTable(\'tblSort\',2,\'string\');" style="cursor:pointer">是否分配任务</td>
					<td onclick="sortTable(\'tblSort\',3,\'string\');" style="cursor:pointer">已完成/需完成 必修课</td>
					<td onclick="sortTable(\'tblSort\',4,\'string\');" style="cursor:pointer">已完成/需完成 选修课</td>
					<td onclick="sortTable(\'tblSort\',5,\'my_string\');" style="cursor:pointer">任务完成进度</td>
					<td>详细</td>
					<td>所属单位</td>
				</tr>
			</thead>
			<tbody>';
    $n=1;
    foreach($users as $user){
        if(null_to_zero($user->taskflag)==0){
            $taskflagnew = '否';
            $required = '-';
            $optional = '-';
            $schedule = '-';
            $detialLink = '-';
        }else{
            $taskflagnew = '是';
            $required = null_to_zero($user->requiredcoursecount).'/'.$mission->required_course_num;
            $optional = null_to_zero($user->optionalcoursecount).'/'.$mission->optional_choice_compeltions;
            $schedule = null_to_zero($user->taskschedule).'%';
//            $schedule = ($schedule==100)? '已完成':$schedule.'%';
            $detialLink = '<a href="#" class="detialLink" userid="'.$user->id.'"  missionid="'.$missionid.'" >查看</a>';//通过jq获取点击对象
        }

        $output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->lastname.$user->firstname.'</td>
				<td>'.$taskflagnew.'</td>
				<td>'.$required.'</td>
				<td>'.$optional.'</td>
				<td>'.$schedule.'</td>
				<td>'.$detialLink.'</td>
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
/**end 台账任务排行榜 xdw */


/**
 * 将为空（null）的变量转为 0
 * @param $param 分析的变量
 * @return var|0
 */
function null_to_zero($param){
    return ($param==null)?0:$param;
}

/** 任务统计  */
function echo_missions($orgid,$missionid,$sumusers,$orgname,$remove_role){

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
                                where roleid in ('.$remove_role.')
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













