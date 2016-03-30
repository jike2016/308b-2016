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
 * First step page for creating a new badge
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

if(empty($CFG))
{
//	require_once(dirname(dirname(__FILE__)).'/config.php');
	require("../config.php");
}
//require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");


global $DB;
//$tagmyid = required_param('id', PARAM_INT);
//$action = optional_param('action', 'details', PARAM_TEXT);
//获取标签相关信息
//$tagmydata = $DB->get_record('tag_my', array('id' => $tagmyid));
//require_login();//要求登录
//$currenturl = new moodle_url('/mod/tagmy/edit.php', array('id' => $tagmyid, 'action' => $action));
//$PAGE->set_url($currenturl);
//$PAGE->set_title($tagmydata->tagname);
//$PAGE->set_heading($tagmydata->tagname);
$PAGE->set_url('/circlesoflearning/edit.php');
$PAGE->set_title('写一篇博客');
$PAGE->set_pagelayout('myblogeditdata');//设置layout文件
class blog_edit_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;

		$mform = $this->_form; // Don't forget the underscore!

//		$mform->addElement('static','aaa', '微博内容');//添加内容框

		$editoroptions = array('maxfiles' => 4, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
		$mform->addElement('editor','summary_editor', '微博内容',null,$editoroptions);//添加内容框

//		$mform->addElement('filemanager', 'attachment_filemanager', get_string('attachment', 'forum'), null, $attachmentoptions);
//		$mform->addElement('text', 'tagname', '标签名称'); // Add elements to your form
		$mform->setType('summary_editor', PARAM_RAW);                  //Set type of element
		$mform->addRule('summary_editor', null, 'required');
		$mform->addRule('summary_editor', get_string('maximumchars', '', 10000), 'maxlength', 10000, 'client');
//		$mform->setDefault('email', 'Please enter email');        //Default value
		$publishstates = array();
		$i = 0;

//		foreach (blog_entry::get_applicable_publish_states() as $state => $desc) {
		$publishstates['publish'] = '公开';   // No maximum was set.
		$publishstates['private'] = '仅自己可见';
//			$i++;
//		}
		$mform->addElement('select', 'publishstate', get_string('publishto', 'blog'), $publishstates);

		$this->add_action_buttons(true, '发送');
	}
	//Custom validation should be added here
	function validation($data, $files) {

	}
}

echo $OUTPUT->header();


$mform = new blog_edit_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/circlesoflearning/index.php'));
} else if ($fromform = $mform->get_data()) {
	//数据处理流程$mform->get_data() 返回所有提交的数据.
	//修改数据

	$my_result->format = $fromform->summary_editor['format'];

//	匹配用户上传的图片和文字
	$blog_content = getimgsAndContents($fromform->summary_editor['text']);
	// 获取微博内容
	$my_result->summary = $blog_content->contents;
//	获取图片，如果有
	if($blog_content->pictures)
	{
		$my_result->pictures = $blog_content->pictures;
	}
	$my_result->subject = '0';
	$my_result->module = 'blog';
//	获得微博作者
	$my_result->userid = $USER->id;
	$my_result->courseid = '0';
	$my_result->groupid = '0';
	$my_result->moduleid = '0';
	$my_result->coursemoduleid = '0';

	$my_result->rating = '0';
	$my_result->summaryformat = '1';

	$my_result->publishstate = $fromform->publishstate;
	// 获取是否是被修改
	$my_result->created = time();
	$my_result->lastmodified = $my_result->created;

	$DB->insert_record('circles_of_learning', $my_result, true);

	redirect(new moodle_url('/circlesoflearning/index.php'));
} else {
	// 这里用于处理数据不符合要求或第一次显示表单

	//设置默认数据
	$mform->set_data($tagmydata);
	//显示表单
	$mform->display();
}


echo $OUTPUT->footer();

/**
 * 提取字符串中图片url地址
 * @param type $str
 * @return type
 */
function getimgsAndContents($str) {
	$contents = strip_tags($str);
	$pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";
	preg_match_all($pattern,$str,$images);

	$blog_content = new stdClass();
	$blog_content->contents = $contents;
	if(count($images))
	{
//		$blog_content->pictures = json_encode($images[1]);
		$blog_content->pictures = str_replace("\\/", "/", json_encode($images[1]));
	}
	return $blog_content;
}
