<?php
/**
 * Created by PhpStorm.
 * User: fubo_01
 * Date: 2016/9/3
 * Time: 17:36
 *
 * 分级课程管理
 */


/** Start 根据用户id ,获取可学习的课程
 * (通过用户id ,获取用户所属单位，再通过单位来获取该单位可浏览的课程)
 * @param $userID 用户id
 * @param $gradingFlag 是否是分级管理员
 * @return String 可学习课程id
 */
function get_view_course($userID,$gradingFlag){

    global $DB;
    $courseIDStr = '';//可查看课程
    if($gradingFlag){//如果是分级管理员，下属单位课程也可看
        $subAndSelfOrgIDs = get_sub_and_slef_orgID();
        $sql = "select oc.course_id from mdl_org_course_my oc where oc.org_id in ($subAndSelfOrgIDs)";
    }else{//如果是学员，只需查询本单位可看的课程
        $sql = "select oc.course_id from mdl_org_course_my oc where oc.org_id = (select org_id from mdl_org_link_user ol where ol.user_id = $userID )";
    }
    $org_courses = $DB->get_records_sql($sql);
    foreach($org_courses as $course){
        $courseIDStr .= $course->course_id.',';
    }
    if($courseIDStr){
        $courseIDStr = substr($courseIDStr,0,(strlen($courseIDStr)-1));
    }
    return $courseIDStr;
}
/** end */

/** Start 按照用户所属级别，筛选课程
 * 功能：在用户实例中追加 可浏览课程、可浏览课程标志 两个属性
 */
function add_userAllowCourse(){
    global $USER;
    global $CFG;
    global $DB;
    //超级管理员、慕课管理员可以查看网站的全部课程，其他角色则只能查看部分课程
    $USER->ava_course_my = '';//用户可浏览课程
    $USER->ava_course_flag_my = false;//角色标志
    require_once($CFG->dirroot.'/user/my_role_conf.class.php');
    $role = new my_role_conf();
    $gradingFlag = $DB->record_exists('role_assignments', array('roleid' => $role->get_gradingadmin_role(),'userid' => $USER->id));
    //如果不是超级管理员或慕课管理员（即：是分级管理员、学员）
    if($USER->id != 2 && !$DB->record_exists('role_assignments', array('roleid' => $role->get_courseadmin_role(),'userid' => $USER->id)) ){
        $USER->ava_course_my = get_view_course($USER->id,$gradingFlag);//获取当前用户可查看的课程id字符串
        $USER->ava_course_flag_my = true;
    }
    //如果是分级管理员，除了能查看单位可查看课程外，还查看他可编辑的课程
    if($gradingFlag){
        $allowCourse = get_view_course_grading();
        $USER->ava_course_my = ($USER->ava_course_my != '' && $allowCourse != '' ) ? ($USER->ava_course_my.','.$allowCourse) :(($USER->ava_course_my != '')?$USER->ava_course_my:$allowCourse);
    }
}
/** end 按照用户所属级别，筛选课程 */

/** Start 分级管理员能够查看的本单位的课程外，还能查看他可编辑的课程
 * -----------------------------------------------------------------------------------------
 *  说明：分级管理员能管理的课程，但他所管理的单位不一定能看该课程，
 *      1、上级分级管理员（A）能管理某下级单位的课程，但不是A所有管理的所有单位都能看到该课程
 *      2、
 * -----------------------------------------------------------------------------------------
 */
function get_view_course_grading(){

    global $CFG;
    global $DB;
    global $USER;
    require_once($CFG->dirroot.'/org_classify/org.class.php');
    //根据当前分级管理员所在的级别，获取其可管理的下级单位
    $org = new org();
    $admin_nodeID = $org->get_nodeid_with_userid($USER->id);
    $admin_node = $org->get_node($admin_nodeID);
    $child_node = $org->get_all_child($admin_node);
    $orgIDStr = ''.$admin_nodeID;
    if($child_node){
        $child_nodeIDs = array_column($child_node,'id');
        $orgIDStr .= ','.implode(',',$child_nodeIDs);
    }

    $courseIDStr = '';
    $courseIDs = $DB->get_records_sql("select co.courseid from mdl_course_org_my co where co.manage_org in ($orgIDStr)");
    foreach($courseIDs as $courseID){
        $courseIDStr .= $courseID->courseid.',';
    }
    if($courseIDStr){
        $courseIDStr = substr($courseIDStr,0,(strlen($courseIDStr)-1));
    }
    return $courseIDStr;
}
/** end */


/** Start 获取 */
function get_sub_and_slef_orgID(){

    global $USER;
    global $CFG;
    require_once($CFG->dirroot."/org_classify/org.class.php");
    //根据当前分级管理员所在的级别，获取其可管理的下级单位
    $org = new org();
    $admin_nodeID = $org->get_nodeid_with_userid($USER->id);
    $admin_node = $org->get_node($admin_nodeID);
    $child_node = $org->get_all_child($admin_node);
    $orgIDStr = ''.$admin_nodeID;
    if($child_node){
        $child_nodeIDs = array_column($child_node,'id');
        $orgIDStr .= ','.implode(',',$child_nodeIDs);
    }
    return $orgIDStr;
}