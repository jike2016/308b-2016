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
 **/
require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");

$delete     = optional_param('delete', 0, PARAM_INT);
$tagname     = optional_param('tagname', 0, PARAM_TEXT);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$returnurl = new moodle_url('/mod/tagmy/index.php');
require_login();//要求登录
$PAGE->set_url('/mod/tagmy/index.php');
$PAGE->set_title('全局标签管理');
$PAGE->set_heading('全局标签管理');
$PAGE->set_pagelayout('registerforadmin');//设置layout
//判断权限。。有bug
if (!has_any_capability(array(
        'mod/tagmy:addinstance'
        ), $PAGE->context)) {
    redirect($CFG->wwwroot);
}
//判断权限。。有bug
if ($delete) {
   
    if (!$confirm) {
        echo $OUTPUT->header();

        // Delete this tag?
         echo $OUTPUT->heading('你确定要删除标签\''.$tagname.'\'吗？');
        $deletebutton = $OUTPUT->single_button(
                            new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
                            '确定');
        echo $OUTPUT->box('将在全站内容中删除该标签' . $deletebutton, 'generalbox');

        // Go back.
        echo $OUTPUT->action_link($returnurl, get_string('cancel'));

        echo $OUTPUT->footer();
        die();
    } else {
        require_sesskey();
        //删除tag_my表数据
		global $DB;
		$DB->delete_records('tag_my', array('id' => $delete));
		//删除tag_table_my表数据
		$DB->delete_records('tag_link', array('tagid' => $delete));
        redirect($returnurl);
    }
}
//columns1

echo $OUTPUT->header();
echo '<h2>全局标签: 标签管理</h2>';
echo $OUTPUT->single_button(new moodle_url('newtagmy.php', array('confirm' => 1)), '添加一个标签');
$table = new html_table();
$table->attributes['class'] = 'collection';
$table->head = array(
		'标签名称',
		'引用次数',
		'动作'
	);
$table->colclasses = array('name', 'num', 'actions');
//取数据展示
global $DB;	 
$tag_my = $DB->get_records_sql('select * FROM mdl_tag_my;');
foreach ($tag_my as $tag) {
	$actions=null;
	$name = $tag->tagname;//名称
	$num = $tag->num;//引用次数
	//编辑按钮
	$url = new moodle_url('/mod/tagmy/edit.php', array('id' => $tag->id, 'action' => 'details'));
	$actions .= $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
	//删除按钮
	$url = new moodle_url(qualified_me());
	$url->param('delete', $tag->id);
	$url->param('tagname', $name);
    $actions .= $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
	$row = array($name, $num, $actions);
	$table->data[] = $row;
}

echo $htmltable = html_writer::table($table);
echo $OUTPUT->footer();

