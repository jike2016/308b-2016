<!DOCTYPE html>
<HTML>
<HEAD>
    <TITLE>个人任务详情</TITLE>
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
 * 台账数据中心》单位》个人》台账任务详情
 */
require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$userid = optional_param('userid', 0, PARAM_INT);//人员id

global $DB;
$user = $DB->get_record_sql("SELECT u.id,u.username,u.firstname,u.lastname from mdl_user u WHERE u.id = $userid ");
$sql = "SELECT m.id,m.mission_name,m.required_course_num,m.required_course_id,m.optional_course_num,m.optional_course_id,m.optional_choice_compeltions
        from mdl_mission_my m WHERE m.id = $missionid ";
$mission = $DB->get_record_sql($sql);

$detialdatas = get_detial_data($userid,$mission);//获取任务详情
echo_missiondetial($user,$mission,$detialdatas);//输出


//*****************************************************************************

/**
 * 获取用户的任务详细数据
 * @param $userid
 * @param $mission
 * @return array
 */
function get_detial_data($userid,$mission){

    $detialDatas = array();
    //必修课
    if($mission->required_course_num){
        $requirsecourseidStr = $mission->required_course_id;
//        $requiredcourses = get_course_completion_data($userid,$requirsecourseidStr,'必修课');
        $requiredcourses = get_course_completion_data2($userid,$requirsecourseidStr,'必修课');
        $detialDatas = array_merge($detialDatas,$requiredcourses);
    }

    //选修课
    if($mission->optional_course_num){
        $optioncourseidStr = $mission->optional_course_id;
//        $opletioncourses = get_course_completion_data($userid,$optioncourseidStr,'选修课');
        $opletioncourses = get_course_completion_data2($userid,$optioncourseidStr,'选修课');
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
            $criteria_sqlStr = " select a.courseid,(FORMAT((a.count/$countCriteria),2)*100) as courseschedule,IF((FORMAT((a.count/$countCriteria),2)*100)=100,1,0) as completionflag,a.timecompleted ";
        }elseif($aggr==2){//如果只需完成其一
            $criteria_sqlStr = " select a.courseid,(IF(a.count=0,0,100)) as courseschedule,IF((IF(a.count=0,0,100)=100),1,0) as completionflag,a.timecompleted  ";
        }

        $sql = "select c.id,c.fullname as coursename,'$coursetype' as coursetype,MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag,MIN(temp.timecompleted) as timecompleted
            from mdl_course c
            LEFT JOIN
                    (	 -- 正常进度统计
                            (   $criteria_sqlStr
                                    from
                                    (select  c.course as courseid,count(*) as count,MAX(c.timecompleted) as timecompleted
                                            from mdl_course_completion_crit_compl c
                                            where c.course = $courseid
                                            and c.criteriaid in ( $courseCriteriaStr )
                                            and c.userid in ( $userid )
                                    ) as a
                            )
                            UNION ALL -- 已完成（可能是设定人工设为完成）
                            (select a.course as courseid,100 as courseschedule,1 as completionflag,a.timecompleted
                                    from mdl_course_completions a
                                    where a.course = $courseid
                                    and a.userid in ( $userid )
                                    and a.timecompleted != ''
                             )
                    ) as temp
            ON c.id = temp.courseid
            WHERE c.id = $courseid ";
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
 * 获取课程进展数据  (废弃)
 * @param $userid
 * @param $courseidStr
 * @param $coursetype
 * @return array
 */
function get_course_completion_data($userid,$courseidStr,$coursetype){
    global $DB;
    $sql = "select c.id,c.fullname as coursename,'$coursetype' as coursetype,MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag,MIN(temp.timecompleted) as timecompleted
            from mdl_course c
            LEFT JOIN
                    (	 -- 正常进度统计
                            (select (FORMAT((a.count/b.count),2)*100) as courseschedule,IF((FORMAT((a.count/b.count),2)*100)=100,1,0) as completionflag,a.userid,a.courseid,a.timecompleted
                                    from
                                    (select  count(*) as count,cmc.userid,cm.course as courseid,cmc.timemodified as timecompleted
                                            from mdl_course_modules_completion cmc
                                            JOIN mdl_course_modules cm on cmc.coursemoduleid = cm.id
                                            where cm.course in ( $courseidStr )
                                            and cmc.userid =$userid
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
                                    and a.userid = $userid
                                    GROUP BY a.userid,a.course
                             )
                    ) as temp
            ON c.id = temp.courseid
            WHERE c.id in ( $courseidStr )
            GROUP BY c.id ";

    $result = $DB->get_records_sql($sql);
    return $result;
}
/**  * end (废弃) */


/**
 * 页面输出
 */
function echo_missiondetial($user,$mission,$detialdatas){

    $output = '
                    <h3 class="table_title_lg">'.$user->firstname.$user->lastname.'：个人任务完成情况</h3>
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
        if(null_to_zero($detialdata->completionflag)==0){
            $timecompleted = '/';
        }else{
            $timecompleted = date('Y-m-d H:i:s',$detialdata->timecompleted);
        }

        $output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$detialdata->coursename.'</td>
				<td>'.$detialdata->coursetype.'</td>
				<td>'.null_to_zero($detialdata->courseschedule).'%</td>
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





