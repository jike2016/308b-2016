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
	20160101集体学习插件 岑霄
 **/
require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");

require_login();//要求登录
$PAGE->set_url('/mod/teamlearn/index.php');
$PAGE->set_title('集体学习');
$PAGE->set_heading('集体学习');
//判断权限。。有bug
if (!has_any_capability(array(
        'mod/teamlearn:addinstance'
        ), $PAGE->context)) {
    redirect($CFG->wwwroot);
}
global $DB;
$teamlearn_switchdata = $DB->get_record('teamlearn_switch', array('id' => '1'));
class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		
		$mform = $this->_form; // Don't forget the underscore! 
		$mform->addElement('select', 'teamlearn_switch', '开关选择', array('1' => '开','2' => '关'));
		$this->add_action_buttons(true, '保存更改');
	}
	//Custom validation should be added here
	function validation($data, $files) {
		//验证标签名是否重复
		// global $DB;
		// $duplicate=$DB->record_exists('tag_my', array('tagname'=>$data['tagname']));
		// if ($duplicate) {
            // $errors['tagname'] = '标签名称已存在';
        // }
		//return $errors;
		return null;
	}
}
$currenturl = new moodle_url('/mod/teamlearn/index.php');
echo $OUTPUT->header();
echo '<h2>集体学习</h2>';
$mform = new simplehtml_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/mod/teamlearn/index.php'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //修改数据
	$teamlearn_switch = new stdClass();
	$teamlearn_switch->id = 1;
	$teamlearn_switch->teamlearn_switch = $fromform->teamlearn_switch;
	$DB->update_record_raw('teamlearn_switch', $teamlearn_switch);
	redirect(new moodle_url('/mod/teamlearn/index.php'));
} else {
  // 这里用于处理数据不符合要求或第一次显示表单
 
  //设置默认数据
  $mform->set_data($teamlearn_switchdata);
  //显示表单
  $mform->display();
}

echo $OUTPUT->footer();
