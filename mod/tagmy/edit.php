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

require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");


global $DB;
$tagmyid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);
//获取标签相关信息
$tagmydata = $DB->get_record('tag_my', array('id' => $tagmyid));
require_login();//要求登录
$currenturl = new moodle_url('/mod/tagmy/edit.php', array('id' => $tagmyid, 'action' => $action));
$PAGE->set_url($currenturl);
$PAGE->set_title($tagmydata->tagname);
$PAGE->set_heading($tagmydata->tagname);
$PAGE->set_pagelayout('registerforadmin');//设置layout

class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		
		$mform = $this->_form; // Don't forget the underscore! 
		$mform->addElement('text', 'tagname', '标签名称'); // Add elements to your form
		$mform->setType('tagname', PARAM_FILE);                  //Set type of element
		$mform->addRule('tagname', null, 'required');
		$mform->addRule('tagname', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		//$mform->setDefault('email', 'Please enter email');        //Default value
		$this->add_action_buttons(true, '保存更改');
	}
	//Custom validation should be added here
	function validation($data, $files) {
		//验证标签名是否重复
		$errors=null;
		global $DB;
		$duplicate=$DB->record_exists('tag_my', array('tagname'=>$data['tagname']));
		if ($duplicate) {
            $errors['tagname'] = '标签名称已存在';
        }
		return $errors;
	}
}

echo $OUTPUT->header();


$mform = new simplehtml_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/mod/tagmy/index.php'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //修改数据
	$tagmy = new stdClass();
	$tagmy->id = $tagmyid;
	$tagmy->tagname = $fromform->tagname;
	$DB->update_record_raw('tag_my', $tagmy);
	
	redirect(new moodle_url('/mod/tagmy/index.php'));
} else {
  // 这里用于处理数据不符合要求或第一次显示表单
 
  //设置默认数据
  $mform->set_data($tagmydata);
  //显示表单
  $mform->display();
}


echo $OUTPUT->footer();
