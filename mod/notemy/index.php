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
$notetitle     = optional_param('title', 0, PARAM_TEXT);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
$returnurl = new moodle_url('/mod/notemy/index.php');
require_login();//要求登录
$PAGE->set_url('/mod/notemy/index.php');
$PAGE->set_title('智能笔记管理');
$PAGE->set_heading('智能笔记管理');
//判断权限。。有bug
/*if (!has_any_capability(array(
        'mod/notemy:addinstance'
        ), $PAGE->context)) {
    redirect($CFG->wwwroot);
}*/
require_login();//这里设置为只判断用户为登录状态即可
//判断权限。。有bug
if ($delete) {
   
    if (!$confirm) {
        echo $OUTPUT->header();

        // Delete this note?
         echo $OUTPUT->heading('你确定要删除笔记\''.$notetitle.'\'吗？');
        $deletebutton = $OUTPUT->single_button(
                            new moodle_url($PAGE->url, array('delete' => $delete, 'confirm' => 1)),
                            '确定');
        echo $OUTPUT->box('将在全站内容中删除该笔记' . $deletebutton, 'generalbox');

        // Go back.
        echo $OUTPUT->action_link($returnurl, get_string('cancel'));

        echo $OUTPUT->footer();
        die();
    } else {
        require_sesskey();
        //删除tag_my表数据
		global $DB;
		$DB->delete_records('note_my', array('id' => $delete));
        redirect($returnurl);
    }
}
//columns1

echo $OUTPUT->header();
echo '<h2>笔记标签: 笔记管理</h2>';
echo $OUTPUT->single_button(new moodle_url('newnotemy.php', array('confirm' => 1)), '添加一个笔记');
$table = new html_table();
$table->attributes['class'] = 'collection';
$table->head = array(
		'笔记标题',
		'笔记内容',
		'时间',
		'动作'
	);
$table->colclasses = array('title', 'content', 'time','actions');
//取数据展示
global $DB;
global $USER;

$userid = $USER->id;//获取用户id
$note_my = $DB->get_records_sql('select * FROM mdl_note_my where userid='.$userid.' ORDER BY id DESC;');
foreach ($note_my as $note) {
	$actions=null;
	$note_title = $note->title;//标题
	$note_content = $note->content;//内容
	$note_time=$note->time;//时间
	//编辑按钮
	$url = new moodle_url('/mod/notemy/edit.php', array('id' => $note->id, 'action' => 'details'));
	$actions .= $OUTPUT->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";//添加
	//删除按钮
	$url = new moodle_url(qualified_me());
	$url->param('delete', $note->id);
	$url->param('title', $note->title);
    $actions .= $OUTPUT->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
	$row = array($note_title, $note_content,$note_time, $actions);
	$table->data[] = $row;
}

echo $htmltable = html_writer::table($table);
echo $OUTPUT->footer();

