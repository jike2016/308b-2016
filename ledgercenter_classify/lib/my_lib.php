<?php
/**
 * 辅助文件 xdw
 */


/**
 * 获取课程进度规则中活动组合方式
 * @param $courseid
 * @return mixed  返回规则的组合方式 1:全部 2：任一
 */
function get_course_criteria_aggregration($courseid){
    global $DB;
    $sql = "SELECT c.id,c.method FROM mdl_course_completion_aggr_methd c
            WHERE c.course = $courseid
            AND c.criteriatype = 4";
    $aggrMethod = $DB->get_record_sql($sql);
    return $aggrMethod->method;
}

/**
 * 获取课程的进度规则（课程活动规则）
 * @param $courseid
 * @param int $type 需求的返回类型
 * @return mixed
 */
function get_course_module_criteria($courseid,$type=0){
    global $DB;
    $sql = "SELECT c.id,c.moduleinstance  FROM mdl_course_completion_criteria c
			WHERE c.course = $courseid
			AND c.criteriatype = 4";
    $coursecriterias = $DB->get_records_sql($sql);
    $result = array();
    if($type==0){
        foreach($coursecriterias as $coursecriteria ){
            $result[] = $coursecriteria->id;
        }
        $result = implode(',',$result);
    }elseif($type==1){
        foreach($coursecriterias as $coursecriteria ){
            $result[] = $coursecriteria->id;
        }
    }

    return $result;
}


/**
 * 获取学生课程完成的课程活动规则
 * @param $userid
 * @param $courseid
 * @param $courseCriteria
 * @return mixed
 */
function get_course_module_criteria_completion($userid,$courseid,$courseCriteria){
    global $DB;
    $sql = "SELECT * FROM mdl_course_completion_crit_compl c
                WHERE c.course = $courseid
                AND c.userid = $userid
                AND c.criteria in ( $courseCriteria ) ";
    $courseMC = $DB->get_record_sql($sql);

    return $courseMC;
}


/**
 * 获取课程的章节模块
 * @param $courseid
 * @param int $type 需要的返回类型
 * @return array|string 默认返回字符串
 */
function get_course_module($courseid,$type=1){
    global $DB;
    $sql = "select cm.id from mdl_course_modules cm where cm.course=$courseid ";
    $coursemodules = $DB->get_records_sql($sql);

    $result = array();
    if($type==1){
        foreach($coursemodules as $coursemodule){
            $result[] = $coursemodule->id;
        }
        $result = implode(',',$result);
    }elseif($type==2){
        foreach($coursemodules as $coursemodule){
            $result[] = $coursemodule->id;
        }
    }

    return $result;
}


/**
 *  获取当前单位下的人员
 * @param $orgid 当前单位
 * @param $remove_role 要移除的角色
 * @param $type 返回的数据类型 1：string 2：array
 * @return array
 */
function get_org_user_array($orgid,$remove_role,$type=1){
    global $DB;
    $orgIDStr = get_sub_self_orgid($orgid);
    $sql = "select ol.user_id
					from mdl_org_link_user ol
					where ol.org_id in ( $orgIDStr )
					and ol.user_id not in ( select userid from mdl_role_assignments where roleid in ( $remove_role ) )
					and ol.user_id != 2";
    $users  = $DB->get_records_sql($sql);

    $result = array();
    if($type==1){
        foreach($users as $user ){
            $result[] = $user->user_id;
        }
        $result = implode(',',$result);
    }elseif($type==2){
        foreach($users as $user ){
            $result[] = $user->user_id;
        }
    }

    return $result;
}


/**
 * 获取单位及下级单位的课程
 * @param $orgid 单位id
 * @return array 单位课程数组
 */
function get_org_course($orgid){

    global $DB;
    $orgIDStr = get_sub_self_orgid($orgid);
    $sql = "select oc.course_id,c.fullname from mdl_org_course_my oc
            LEFT JOIN mdl_course c on oc.course_id = c.id
            where oc.org_id in ( $orgIDStr )
            GROUP BY oc.course_id
            ORDER BY oc.course_id DESC ";
    $courses = $DB->get_records_sql($sql);
    return $courses;
}

/**
 * 获取当前单位及下级单位id
 * @param $orgid 当前单位id
 * @return string
 */
function get_sub_self_orgid($orgid){
    global $CFG;
    require_once($CFG->dirroot."/org_classify/org.class.php");
    $org = new org();
    $admin_node = $org->get_node($orgid);
    $child_node = $org->get_all_child($admin_node);
    $orgIDStr = ''.$orgid;
    if($child_node){
        $child_nodeIDs = array_column($child_node,'id');
        $orgIDStr .= ','.implode(',',$child_nodeIDs);
    }

    return $orgIDStr;
}

/**
1、不选时间，默认全部
2、周月总
------------------
1、sql 中各子查询表 的时间字段不同 （函数处理）
2、是否设置开始时间或结束时间 的判断
3、月周总 的实现 （统一采用时间日历，但要用cookie/session来保存选择的时间）

无时间
全部（sql 中拼接空的 time_sql）
有时间
只有起始 拼接 start_time < $time
只有结束 ……
有起止 $time between start_time and end_time
 */

/**
 * 时间字段筛选语句处理
 * @param $time_field 时间字段
 * @return string  关于time 的sql 查询语句
 */
function handle_sql_time($time_field,$startTime,$endTime){

    $time_sql = '';
    if( ($startTime==0) && ($endTime == 0)){
        return '';
    }
    else if(($startTime != 0) && ($endTime != 0)){
        return ' and '.$time_field.' between '.$startTime.' and '.$endTime;
    }
    else if(($startTime != 0)){
        return ' and '.$time_field.' >= '.$startTime;
    }
    else if(($endTime != 0)){
        return ' and '.$time_field.' <= '.$endTime;
    }

    return $time_sql;
}


/**
 * 将当前顶级单位下的所有人员信息统计合并到当前单位的各二级子单位下
 * @param $orgid 当前顶级单位
 * @param $usersdatas 当前顶级单位下所有人员的数据
 * @param $hassuborg 是否有子单位
 * @return array 二级单位的统计信息
 */
function merge_all_user_data_to_sub_org($orgid,$usersdatas,$hassuborg=0){

    //将人员信息按照其所属子单位合并，把人员数据转为单位数据
    $tempOrgDatas = array();
    $orgIds = array();
    foreach($usersdatas as $usersdata){

        if(in_array($usersdata->orgid,$orgIds)){

            $tempOrg = $tempOrgDatas[$usersdata->orgid];

            $tempOrg->taskflag += $usersdata->taskflag;
            $tempOrg->requiredcoursecount += $usersdata->requiredcoursecount;
            $tempOrg->optionalcoursecount += $usersdata->optionalcoursecount;
            $tempOrg->taskschedule += $usersdata->taskschedule;

            $tempOrgDatas[$usersdata->orgid] = $tempOrg;
        }
        else{
            $orgIds[] = $usersdata->orgid;

            $tempOrg = new stdClass();
            $tempOrg->id = $usersdata->orgid;
            $tempOrg->orgname = $usersdata->orgname;
            $tempOrg->taskflag = $usersdata->taskflag;
            $tempOrg->requiredcoursecount = $usersdata->requiredcoursecount;
            $tempOrg->optionalcoursecount = $usersdata->optionalcoursecount;
            $tempOrg->taskschedule = $usersdata->taskschedule;

            $tempOrgDatas[$usersdata->orgid] = $tempOrg;
        }
    }

    //调用merge_sub_org_data（）对单位数据进行合并
    if($hassuborg==1){
        $orgdatas = merge_sub_org_data($orgid,$tempOrgDatas);
    }elseif($hassuborg==0){
        $orgdatas = $tempOrgDatas;
    }

    return $orgdatas;
}


/**
 * 将三级子单位及以下单位数据合并到二级单位数据中
 * @param $orgid 当前顶级单位
 * @param $orgdatas 当前顶级单位下所有级别的单位数据
 * @return array 只有二级单位数据
 */
function merge_sub_org_data($orgid,$orgdatas){

    $new_orgdatas = array();
    $secondLevelOrgs = get_second_level_org($orgid);//这里便去掉了当前顶级单位本身这一级的数据
    foreach($secondLevelOrgs as $secondLevelOrg){
        $tempOrg = new stdClass();//初始化当前二级单位
        $tempOrg->id = $secondLevelOrg->id;
        $tempOrg->orgname = $secondLevelOrg->name;

        $subOrgIdStr =  get_sub_self_orgid($secondLevelOrg->id);
        $subOrgIds = explode(',',$subOrgIdStr);
        foreach($orgdatas as $orgdata){//合并二级以下子单位的数据到当前二级单位
            if(in_array($orgdata->id,$subOrgIds)){
                $tempOrg = handle_obj_attr($tempOrg,$orgdata);
//                $tempOrg->notecount += $orgdata->notecount;
//                $tempOrg->commentcount += $orgdata->commentcount;
//                $tempOrg->likecount += $orgdata->likecount;
//                $tempOrg->scorecount += $orgdata->scorecount;
//                $tempOrg->badgecount += $orgdata->badgecount;
//                $tempOrg->logincount += $orgdata->logincount;
//                $tempOrg->totaltime += $orgdata->totaltime;
                unset($orgdatas[$orgdata->id]);
            }
        }
        $tempOrg->id = $secondLevelOrg->id;
        $tempOrg->orgname = $secondLevelOrg->name;
        $new_orgdatas[$secondLevelOrg->id] = $tempOrg;
    }

    return $new_orgdatas;
}

/**
 * 变量属性相加，注意：只对数值类型的属性有效，其他类型（如字符串）要改写本方法！
 * @param $Obj 累加对象
 * @param $addObj 追加因子
 * @return mixed 累加追加因子后的对象
 */
function handle_obj_attr($Obj,$addObj){
    foreach($addObj as $key=>$value){
        $Obj->$key += $value;
    }
    return $Obj;
}

/**
 * 获取当前单位的二级子单位
 * @param $orgid 当前单位id
 * @return array 子单位id
 */
function get_second_level_org($orgid){
    global $DB;
    $sql = "select o.id,o.`name` from mdl_org  o
            where o.parent = $orgid ";
    $secondLevelOrg = $DB->get_records_sql($sql);
    return $secondLevelOrg;
}

