<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin-only code to delete a course utterly.
 *
 * @package core_course
 * @copyright 2002 onwards Martin Dougiamas (http://dougiamas.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');


$id = required_param('id', PARAM_INT); // Course ID.
$delete = optional_param('delete', '', PARAM_ALPHANUM); // Confirmation hash.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
$coursecontext = context_course::instance($course->id);

require_login();
/** Start 身份认证  分级管理员| 慕课管理员、超级管理员 */
global $USER;
require_once($CFG->dirroot.'/user/my_role_conf.class.php');
$role = new my_role_conf();
if($DB->record_exists('role_assignments', array('roleid' => $role->get_gradingadmin_role(),'userid' => $USER->id))){
    $role_flag = 1;//分级管理员
    $return_indexURL = $CFG->wwwroot.'/course/course_classify/index_gradingAdmin.php';
}
else if($DB->record_exists('role_assignments', array('roleid' => $role->get_courseadmin_role(),'userid' => $USER->id))
        || ($USER->id == 2) ){
    $role_flag = 2;//慕课管理员、超级管理员
    $return_indexURL = $CFG->wwwroot.'/course/course_classify/index_courseAdmin.php';
}
if($role_flag != 1 && $role_flag != 2){
    redirect($CFG->wwwroot);
}
/** end */

$categorycontext = context_coursecat::instance($course->category);
$PAGE->set_url('/delete_my.php', array('id' => $id));
$PAGE->set_context($categorycontext);
$PAGE->set_pagelayout('admin');
navigation_node::override_active_url(new moodle_url('/course/management.php', array('categoryid'=>$course->category)));

$courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
$coursefullname = format_string($course->fullname, true, array('context' => $coursecontext));
//$categoryurl = new moodle_url('/course/management.php', array('categoryid' => $course->category));
$categoryurl = new moodle_url($return_indexURL);

//Start 对删除课程确定后才执行
// Check if we've got confirmation.
if ($delete === md5($course->timemodified)) {
    // We do - time to delete the course.
    require_sesskey();

    $strdeletingcourse = get_string("deletingcourse", "", $courseshortname);

    $PAGE->navbar->add($strdeletingcourse);
    $PAGE->set_title("$SITE->shortname: $strdeletingcourse");
    $PAGE->set_heading($SITE->fullname);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strdeletingcourse);
    // We do this here because it spits out feedback as it goes.
	//删除课程
    delete_course($course);
    //同步更新课程所属管理单位记录 和 单位可查看课程记录
    $DB->execute("delete from mdl_course_org_my where courseid = $course->id");
    $DB->execute("delete from mdl_org_course_my where course_id = $course->id");

    echo $OUTPUT->heading( get_string("deletedcourse", "", $courseshortname) );
    // Update course count in categories.
    fix_course_sortorder();
    $categoryurl = new moodle_url($return_indexURL);
    echo $OUTPUT->continue_button($categoryurl);
    echo $OUTPUT->footer();
    exit; // We must exit here!!!
}
//end 对删除课程确定后才执行

$strdeletecheck = get_string("deletecheck", "", $courseshortname);
$strdeletecoursecheck = get_string("deletecoursecheck");
$message = "{$strdeletecoursecheck}<br /><br />{$coursefullname} ({$courseshortname})";

$continueurl = new moodle_url('/course/course_classify/delete_my.php', array('id' => $course->id, 'delete' => md5($course->timemodified)));

$PAGE->navbar->add($strdeletecheck);
$PAGE->set_title("$SITE->shortname: $strdeletecheck");
$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();
echo $OUTPUT->confirm($message, $continueurl, $categoryurl);
echo $OUTPUT->footer();
exit;