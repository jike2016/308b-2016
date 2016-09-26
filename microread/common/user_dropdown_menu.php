<?php
/**
 *
 * Created by PhpStorm.
 * User: fubo_01
 * Date: 2016/9/13
 * Time: 18:01
 */

/**
 * Start 导航条用户下拉功能菜单
 * @param $page 所属页
 * @return String 菜单列表html
 */
function get_user_dropdown_menu($page = null){

    global $CFG;
    global $USER;
    global $DB;
    require_once($CFG->dirroot.'/user/my_role_conf.class.php');
    $role = new my_role_conf();

    $menuStr = '';

    /** Start 超级管理员 */
    if( $USER->id == 2){
        $menuStr = '<li><a href="'.new moodle_url('/my/').'">个人主页</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/org/').'">组织架构</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/ledgercenter/').'">台账数据中心</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/microread/admin/').'">微阅管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/course/course_classify/index_courseAdmin.php').'">微课管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/message/').'">消息</a></li>
                <li role="separator" class="divider"></li>
            ';
    }
    /** end */
    /** Start 慕课管理员 */
    else if($DB->record_exists('role_assignments', array('roleid' => $role->get_courseadmin_role(),'userid' => $USER->id))){
        $menuStr = '<li><a href="'.new moodle_url('/my/').'">个人主页</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/org_classify/').'">组织架构</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/ledgercenter/').'">台账数据中心</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/microread/admin/').'">微阅管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/course/course_classify/index_courseAdmin.php').'">微课管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/message/').'">消息</a></li>
                <li role="separator" class="divider"></li>
            ';
    }
    /** end */
    /** Start 分级管理员 */
    else if($DB->record_exists('role_assignments', array('roleid' => $role->get_gradingadmin_role(),'userid' => $USER->id))){
        $menuStr = '
                <li><a href="'.new moodle_url('/org_classify/').'">组织架构</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/ledgercenter_classify/').'">台账数据中心</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/mod/missionmy/index_classify.php/').'">台账任务</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/microread/admin_classify/').'">微阅审核</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/register/admin_check_classify.php').'">注册管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/course/course_classify/index_gradingAdmin.php').'">微课管理</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="'.new moodle_url('/message/').'">消息</a></li>
                <li role="separator" class="divider"></li>
            ';
    }
    /** end */
    /** Start 单位账号 */
    else if($DB->record_exists('role_assignments', array('roleid' => $role->get_unit_role(),'userid' => $USER->id))){
        $menuStr = '
                    <li><a href="'.new moodle_url('/ledgercenter_classify/').'">台账数据中心</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="'.new moodle_url('/message/').'">消息</a></li>
                    <li role="separator" class="divider"></li>';

    }
    /** end */
    /** Start 学生、其他 */
    else if($USER->id != ''){
        $menuStr = '
                    <li><a href="'.new moodle_url('/privatecenter/').'">个人中心</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="'.new moodle_url('/message/').'">消息</a></li>
                    <li role="separator" class="divider"></li>';
    }
    /** end */

    if($page == 'bookroom'){
        $menuStr .= '<li><a href="'.new moodle_url('user_upload.php').'">上传电子书</a></li>
                     <li role="separator" class="divider"></li>';
    }else if($page == 'docroom'){
        $menuStr .= '<li><a href="'.new moodle_url('user_upload.php').'">上传文档</a></li>
                     <li role="separator" class="divider"></li>';
    }else if($page == 'picroom'){
        $menuStr .= '<li><a href="'.new moodle_url('image-upload.php').'">上传图片</a></li>
                     <li role="separator" class="divider"></li>';
    }

    $menuStr .= '<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>';

    return $menuStr;
}
/** end 导航条用户下拉功能菜单 */
