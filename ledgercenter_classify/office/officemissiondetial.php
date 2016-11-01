<!DOCTYPE html>
<HTML>
<HEAD>
    <TITLE>单位任务详情</TITLE>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="../css/bootstrap.css" />

    <style>
        *{font-family: "Microsoft YaHei", 微软雅黑}
        .table_title_lg,.table_title_sm {text-align: center;}
        .table thead td{font-weight: bold;}
        .table > thead > tr > td, .table > tbody > tr > td{text-align: center; vertical-align: middle;}
    </style>

</HEAD>
<?php
/**
 * 台账数据中心》单位》单位》台账任务详情
 */


require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id


global $DB;
$org = $DB->get_record_sql("SELECT * from mdl_org o WHERE o.id = $orgid ");
$sql = "SELECT m.id,m.mission_name,m.required_course_num,m.required_course_id,m.optional_course_num,m.optional_course_id,m.optional_choice_compeltions
        from mdl_mission_my m WHERE m.id = $missionid ";
$mission = $DB->get_record_sql($sql);
require_once('../comment_data.php');
require_once("../lib/my_lib.php");
$UserStr = get_org_user_array($orgid,$remove_role,$type=1);//获取当前单位下的人员
$missionUserArray = get_mission_user($UserStr,$missionid,$type=2);//获取接受任务的人员
$missionUserStr = implode(',',$missionUserArray);
$missionUserCount = count($missionUserArray);

//直接将该单位下的所有人员数据分类统计，不需要再划分子单位了！
$detialdatas = get_detial_data($missionUserStr,$mission);//获取任务详情
echo_missiondetial($org,$mission,$detialdatas,$missionUserCount);//输出

//*****************************************************************************

/**
 * 筛选出接受任务的人员
 */
function get_mission_user($UserStr,$missionid,$type=1){
    global $DB;
    $result = array();
    //获取人员接受任务的情况
    $sql = "SELECT mum.user_id as userid
            FROM mdl_mission_user_my mum
            WHERE mum.mission_id = $missionid
            AND mum.user_id IN ( $UserStr )";
    $users = $DB -> get_records_sql($sql);
    if($type==1){
        foreach($users as $user){
            $result[] = $user->userid;
        }
        $result = implode(',',$result);
    }elseif($type==2){
        foreach($users as $user){
            $result[] = $user->userid;
        }
    }elseif($type==3){
        $result = count($users);
    }
    return $result;
}


/**
 * 获取单位的任务详细数据
 * @param $UserStr
 * @param $mission
 * @return array
 */
function get_detial_data($UserStr,$mission){

    $detialDatas = array();
    //必修课
    if($mission->required_course_num){
        $requirsecourseidStr = $mission->required_course_id;
//        $requiredcourses = get_course_completion_data($UserStr,$requirsecourseidStr,'必修课');
        $requiredcourses = get_course_completion_data2($UserStr,$requirsecourseidStr,'必修课');
        $detialDatas = array_merge($detialDatas,$requiredcourses);
    }

    //选修课
    if($mission->optional_course_num){
        $optioncourseidStr = $mission->optional_course_id;
//        $opletioncourses = get_course_completion_data($UserStr,$optioncourseidStr,'选修课');
        $opletioncourses = get_course_completion_data2($UserStr,$optioncourseidStr,'选修课');
        $detialDatas = array_merge($detialDatas,$opletioncourses);
    }

    return $detialDatas;
}



/**
 * 获取用户的课程进度
 * @param $userid
 * @param $courseid
 * @param $coursetype
 * @return array|mixed {课程id，课程名称，课程性质，课程进度，课程完成标志，课程完成时间}
 */
function get_course_criteria_completion_schedule($userid,$courseid,$coursetype){
    global $DB;
    $result = array();
    require_once("../lib/my_lib.php");
    $courseCriteria = get_course_module_criteria($courseid,1);//获取课程活动规则
    if(!$courseCriteria){//如果没设置任何活动规则
        $sql = "select c.id,c.fullname as coursename,'$coursetype' as coursetype,'课程未设进度跟踪，默认进度0' as courseschedule,0 as completionflag,0 as timecompleted
                from mdl_course c
                WHERE c.id = $courseid ";
    }else {
        $countCriteria = count($courseCriteria);//规则的数量
        $courseCriteriaStr = implode(',',$courseCriteria);
        $aggr = get_course_criteria_aggregration($courseid);//规则的组合方式
        if($aggr==1){//如果要完成全部
            $criteria_sqlStr = " select a.userid,a.courseid,(FORMAT((a.count/$countCriteria),2)*100) as courseschedule,IF((FORMAT((a.count/$countCriteria),2)*100)=100,1,0) as completionflag,MAX(a.timecompleted) as timecompleted ";
        }elseif($aggr==2){//如果只需完成其一
            $criteria_sqlStr = " select a.userid,a.courseid,(IF(a.count=0,0,100)) as courseschedule,IF((IF(a.count=0,0,100)=100),1,0) as completionflag,a.timecompleted  ";
        }

        $sql = "select c.id,c.fullname as coursename,'$coursetype' as coursetype,SUM(temp.courseschedule) as courseschedule,SUM(temp.completionflag) as completionflag,MAX(temp.timecompleted) as timecompleted
                    from mdl_course c
                    LEFT JOIN
                            (	SELECT MAX(courseschedule) as courseschedule,MAX(completionflag) as completionflag,userid,courseid,MIN(timecompleted) as timecompleted
                                FROM
                                (   -- 正常进度统计
                                    (  $criteria_sqlStr
                                            from
                                            (select  c.userid,c.course as courseid,count(*) as count,MAX(c.timecompleted) as timecompleted
                                                    from mdl_course_completion_crit_compl c
                                                    where c.course = $courseid
                                                    and c.criteriaid in ( $courseCriteriaStr )
                                                    and c.userid in ( $userid )
                                                    GROUP BY c.userid,c.course
                                            ) as a
                                            GROUP BY a.userid,a.courseid
                                    )
                                    UNION ALL -- 已完成（可能是设定人工设为完成）
                                            (select a.userid,a.course as courseid,100 as courseschedule,1 as completionflag,a.timecompleted
                                                    from mdl_course_completions a
                                                    where a.course = $courseid
                                                    and a.userid in ( $userid )
                                                    and a.timecompleted != ''
                                             )
                                    ) as scheduleTable
                                    GROUP BY courseid,userid
                           ) as temp
                    ON c.id = temp.courseid
                    WHERE c.id in ( $courseid )";
    }
    $result = $DB->get_records_sql($sql);

    return $result;
}

/**
 * 获取用户任务课程进展数据
 * @param $userid
 * @param $courseidStr
 * @param $coursetype
 * @return array {课程id，课程名称，课程性质，课程进度，课程完成标志，课程完成时间}
 */
function get_course_completion_data2($userid,$courseidStr,$coursetype){
    $result = array();
    $courseids = explode(',',$courseidStr);
    foreach($courseids as $courseid){
        $schedule = get_course_criteria_completion_schedule($userid,$courseid,$coursetype);//获取单课程的进度
        //合并各课程进度
        $result = array_merge($result,$schedule);
    }

    return $result;
}


/**
 * 获取单位课程进展数据  （废弃）
 * @param $UserStr
 * @param $courseidStr
 * @param $coursetype
 * @return array
 */
function get_course_completion_data($UserStr,$courseidStr,$coursetype){
    global $DB;
    $sql = "select c.id,c.fullname as coursename,'$coursetype' as coursetype,SUM(temp.courseschedule) as courseschedule,SUM(temp.completionflag) as completionflag,MAX(temp.timecompleted) as timecompleted
            from mdl_course c
            LEFT JOIN
                    (	SELECT MAX(courseschedule) as courseschedule,MAX(completionflag) as completionflag,userid,courseid,MIN(timecompleted) as timecompleted
                        FROM
                        (   -- 正常进度统计
                            (select (FORMAT((a.count/b.count),2)*100) as courseschedule,IF((FORMAT((a.count/b.count),2)*100)=100,1,0) as completionflag,a.userid,a.courseid,a.timecompleted
                                    from
                                    (select  count(*) as count,cmc.userid,cm.course as courseid,cmc.timemodified as timecompleted
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
                            (select 100 as courseschedule,1 as completionflag,a.userid,a.course as courseid,a.timecompleted
                                    from mdl_course_completion_crit_compl a
                                    where a.course in ( $courseidStr )
                                    and a.userid in ( $UserStr )
                                    GROUP BY a.userid,a.course
                             )
                        ) as scheduleTable
						GROUP BY courseid,userid
                    ) as temp
            ON c.id = temp.courseid
            WHERE c.id in ( $courseidStr )
            GROUP BY c.id ";

    $result = $DB->get_records_sql($sql);
    return $result;
}
/**  end（废弃） */

/**
 * 页面输出
 */
function echo_missiondetial($org,$mission,$detialdatas,$missionUserCount){

    $output = '
                    <h3 class="table_title_lg">'.$org->name.'：单位任务完成情况</h3>
                    <h4 class="table_title_sm">（任务：'.$mission->mission_name.'&nbsp;&nbsp;选修课需完成数：'.$mission->optional_choice_compeltions.'）</h4>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <td>序号</td>
                                <td>课程名</td>
                                <td>课程性质</td>
                                <td>课程完成进度</td>
                                <td>完成时间</td>
                            </tr>
                        </thead>
                        <tbody>';
    $n=1;
    foreach($detialdatas as $detialdata){
        if(null_to_zero($detialdata->completionflag/$missionUserCount)<1 ){
            $timecompleted = '/';
        }else{
            $timecompleted = date('Y-m-d H:i:s',$detialdata->timecompleted);
        }
        if(is_number($detialdata->courseschedule)){
            $schedule = round(null_to_zero($detialdata->courseschedule/$missionUserCount),0);
        }else{
            $schedule = null_to_zero($detialdata->courseschedule);
        }


        $output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$detialdata->coursename.'</td>
				<td>'.$detialdata->coursetype.'</td>
				<td>'.$schedule.'</td>
				<td>'.$timecompleted.'</td>
			</tr>
			';
        $n++;
    }

    $output .='
                    </tbody>
                </table>';
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

