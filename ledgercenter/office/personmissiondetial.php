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
        $requiredcourses = get_course_completion_data($userid,$requirsecourseidStr,'必修课');
        $detialDatas = array_merge($detialDatas,$requiredcourses);
    }

    //选修课
    if($mission->optional_course_num){
        $optioncourseidStr = $mission->optional_course_id;
        $opletioncourses = get_course_completion_data($userid,$optioncourseidStr,'选修课');
        $detialDatas = array_merge($detialDatas,$opletioncourses);
    }

    return $detialDatas;
}

/**
 * 获取课程进展数据
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





