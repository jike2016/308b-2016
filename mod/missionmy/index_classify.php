<?php 
/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License") +  you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/
/**
 * This page lists all the instances of wepeng in a particular course
 *
 * @author Sebastian Wagner
 * @version 1.7
 * @package wepeng
 *
 * 分级管理员 页面
 **/
require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir."/formslib.php");
require_once($CFG->dirroot."/user/my_role_conf.class.php");
require_once($CFG->dirroot."/org_classify/org.class.php");


global $DB;
global $USER;
$delete     = optional_param('delete', 0, PARAM_INT);
$mission_name     = optional_param('mission_name', 0, PARAM_TEXT);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$returnurl  = new moodle_url('/mod/missionmy/index_classify.php');

require_login();//要求登录
/**start  权限判断 只允许分级管理员 */
$role =  new my_role_conf();
if(!$DB->record_exists("role_assignments",array('userid'=>$USER->id,'roleid'=>$role->get_gradingadmin_role()))){
	redirect(new moodle_url('/../moodle/index.php'));
}
if(!$DB->record_exists("org_link_user",array('user_id'=>$USER->id))){//当账号所属的单位被删除时
	echo "<script>alert('您当前的账号未分配所属单位，请联系系统管理员');</script>";
	exit();
}
/**end 权限判断  */


$PAGE->set_url('/mod/missionmy/index_classify.php');
$PAGE->set_title('台账任务管理');
$PAGE->set_heading('台账任务管理');
$PAGE->set_pagelayout('registerforadmin');//设置layout

//判断权限。。有bug
//权限未确定？？？？
//if (!has_any_capability(array(
//        'mod/missionmy:addinstance'
//        ), $PAGE->context)) {
//    redirect($CFG->wwwroot);
//}
//判断权限。。有bug

//处理本页提交的删除操作
if ($delete) {
   
    if (!$confirm) {
        echo $OUTPUT->header();

        // Delete this mission?
		echo $OUTPUT->heading('你确定要删除任务\''.$mission_name.'\'吗？');
        $deletebutton = $OUTPUT->single_button(
                            new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
                            '确定');
        echo $OUTPUT->box('将在全站内容中删除该任务' . $deletebutton, 'generalbox');

        // Go back.
        echo $OUTPUT->action_link($returnurl, get_string('cancel'));

        echo $OUTPUT->footer();
        die();
    } else {
        require_sesskey();

		global $DB;
		$DB->delete_records('mission_my', array('id' => $delete));//删除mission_my表数据
		$DB->delete_records('mission_user_my',array('mission_id'=>$delete));//删除mission_user_my表数据
        redirect($returnurl);
    }
}

echo $OUTPUT->header();
echo '<h2>任务标签: 任务管理</h2>';
echo $OUTPUT->single_button(new moodle_url('newmissionmy_classify.php', array('confirm' => 1)), '添加一个任务');
//echo $OUTPUT->single_button(new moodle_url('newmissionmy.php', array('confirm' => 1)), '添加一个任务');
$table = new html_table();
$table->attributes['class'] = 'collection';
$table->head = array(
		'任务标题',
		'必修课数量',
		'必修课',
		'选修课数量',
		'选修课',
		'选修课应完成数量',
		'任务开始时间',
		'任务结束时间',
		'所属单位',
		'开启或关闭',
		'动作'
	);
$table->colclasses = array('mission_name', 'required_course_num', 'required_course_id','optional_course_num','optional_course_id','optional_choice_compeltions','start_time','end_time','belong_org','enable','action');
//取数据展示
global $DB;
global $USER;

$org = new org();
$userid = $USER->id;//获取用户id
//获取当前分级管理员所在单位和下属单位
$now_org_nodeID = $org->get_nodeid_with_userid($userid);
$now_org_node = $org->get_node($now_org_nodeID);
$sub_org_nodes = $org->get_all_child($now_org_node);
$orgIDStr = $now_org_nodeID;
foreach($sub_org_nodes as $node){
	$orgIDStr .= ','.$node['id'];
}
//由可查看单位，筛选任务
$sql = 'select * from mdl_mission_my m where m.org_id in ('.$orgIDStr.') order by m.time_start desc';
$mission_my = $DB->get_records_sql(sprintf($sql));
foreach ($mission_my as $mission) {
	$actions=null;
	$mission_name = $mission->mission_name;//台账标题
	$required_course_num = $mission->required_course_num;//必修课数量
	//必修课名称的获取
	$requiredCourse = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($mission->required_course_id)" );
	$reLen = count($requiredCourse);
	$requiredCourseNames = '';
	$reCount = 0;
	foreach($requiredCourse as $course){
		$reCount++;
		if($reCount != $reLen){
			$requiredCourseNames .= $course->fullname.' / ';
		}else{
			$requiredCourseNames .= $course->fullname;
		}
	}
	$required_course_id = $requiredCourseNames;//必修课

	$optional_course_num = $mission->optional_course_num;//选修课数量
	//选修课名称的获取
	$optionalCourse = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($mission->optional_course_id)");
	$opLen = count($optionalCourse);
	$optionalCourseNames = '';
	$opCount = 0;
	foreach($optionalCourse as $course){
		$opCount++;
		if($opCount != $opLen){
			$optionalCourseNames .= $course->fullname.' / ';
		}else{
			$optionalCourseNames .= $course->fullname;
		}
	}
	$optional_course_id = $optionalCourseNames;//选修课

	$optional_choice_compeltions = $mission->optional_choice_compeltions;//选修课应完成数量
	$time_start = userdate($mission->time_start,'%Y-%m-%d %H:%M');//开始时间
	$time_end = userdate($mission->time_end,'%Y-%m-%d %H:%M');//结束时间
	$enable = $mission->enable;//任务开关
	if($enable == 0){
		$enable = '已开启';
	}else if($enable == 1){
		$enable = '已关闭';
	}

	//任务所属单位的判断
	$orgID = $mission->org_id;
	$belong_org = '';
	if($DB->record_exists("org",array("id"=>$orgID))){
		$org_node = $org->get_node($orgID);
		$belong_org = $org_node['name'];
	}

	//编辑按钮
	$url = new moodle_url('/mod/missionmy/edit_classify.php', array('id' => $mission->id, 'action' => 'details'));
	$actions .= $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";//添加
	//删除按钮
	$url = new moodle_url(qualified_me());//这里是在本页进行数据删除的处理
	$url->param('delete', $mission->id);
	$url->param('mission_name', $mission->mission_name);
    $actions .= $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";

	//将各字段的数据填充到表格相应的位置中
	$row = array($mission_name,$required_course_num,$required_course_id,$optional_course_num,$optional_course_id,$optional_choice_compeltions,$time_start,$time_end,$belong_org,$enable,$actions);
	$table->data[] = $row;
}

echo $htmltable = html_writer::table($table);
echo $OUTPUT->footer();

