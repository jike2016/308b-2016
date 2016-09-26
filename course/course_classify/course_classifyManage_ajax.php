<?php
/** 处理分级管理员 课程管理 */
require_once('../../config.php');
require_once($CFG->dirroot.'/org_classify/org.class.php');

$courseID = $_POST['courseid'];
$orgID = $_POST['orgid'];
$option_type = $_POST['option_type'];

global $DB;
/** Start 修改管理单位 */
if($option_type == 1){
    //判断是否存在'课程--单位'记录
    if( $DB->record_exists("course_org_my",array("courseid"=>$courseID)) ){
        $res = $DB->execute("update mdl_course_org_my co set co.manage_org = $orgID where co.courseid = $courseID ");
    }else{
        $res = $DB->insert_record_raw("course_org_my",array("courseid"=>$courseID,"manage_org"=>$orgID),true);
    }

    if($res){
        $result = [
            "success"=>true,
//    "page_total" => $page_total,
//    "data"=>$evaluationStr,
        ];
        echo json_encode($result);
    }
    else{
        $result = [
            "success"=>false,
        ];
        echo json_encode($result);
    }
}
/** end 修改管理单位 */

/** Start 修改查看单位 */
if($option_type == 2){

    $orgIDStr = implode(',',array_column($orgID,'id'));

    //判断是否存在'课程--单位'记录
    if( $DB->record_exists("course_org_my",array("courseid"=>$courseID)) ){
        //更新课程单位数据表
        $res1 = $DB->execute("update mdl_course_org_my co set co.browseable_org = '$orgIDStr' where co.courseid = $courseID ");

        //更新单位可查看课程数据表 mdl_org_course_org_my
        $res2 = $DB->execute("delete from mdl_org_course_my where course_id = $courseID ");
        foreach($orgID as $orgnote){//处理选中的各单位
            $orgCourseObjArray = get_orgCourseObjArray($orgnote['id'],$courseID);//获取新的单位课程数据
            $res = $DB->insert_records("org_course_my",$orgCourseObjArray);
        }
    }
    else{
        $res1 = $DB->insert_record_raw("course_org_my",array("courseid"=>$courseID,"browseable_org"=>$orgIDStr),true);
        foreach($orgID as $orgnote){//处理选中的各单位
            $orgCourseObjArray = get_orgCourseObjArray($orgnote['id'],$courseID);//获取新的单位课程数据
            $res2 = $DB->insert_records("org_course_my",$orgCourseObjArray);
        }
    }

    if($res1 && $res2){
        $result = [
            "success"=>true,
//    "page_total" => $page_total,
//    "data"=>$evaluationStr,
        ];
        echo json_encode($result);
    }
    else{
        $result = [
            "success"=>false,
        ];
        echo json_encode($result);
    }
}
/** end 修改查看单位 */

/**Start  获取本单位及其下级单位的单位课程数组
 * @param $orgID 单位id
 * @param $courseID 课程id
 * @return array
 */
function get_orgCourseObjArray($orgID,$courseID){

    $org = new org();
    $org_node = $org->get_node($orgID);//获取当前单位节点
    $sub_orgs = $org->get_all_child($org_node);//获取当前单位节点下的所有子节点

    $org_courseObjArray = array();
    $org_courseObj = new stdClass();//当前单位
    $org_courseObj->org_id = $orgID;
    $org_courseObj->course_id = $courseID;
    $org_courseObjArray[] = $org_courseObj;
    foreach($sub_orgs as $temp){//各子单位
        $subOrg_courseObj = new stdClass();
        $subOrg_courseObj->org_id = $temp['id'];
        $subOrg_courseObj->course_id = $courseID;
        $org_courseObjArray[] = $subOrg_courseObj;
    }
    return $org_courseObjArray;
}
/**end  获取单位课程数组 */