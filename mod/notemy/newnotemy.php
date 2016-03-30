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

$PAGE->set_url('/mod/notemy/newnotemy.php');
$PAGE->set_title('创建智能笔记');
$PAGE->set_heading('创建智能笔记');

require_login();//要求登录
global $DB;
class simplehtml_form extends moodleform {

	//Add elements to form //初始化表单元素
	public function definition() {
		global $CFG;
		global $USER;

		//1.	输入界面（笔记标题和笔记内容）
		$mform = $this->_form; // Don't forget the underscore!
		$editoroptions = $this->_customdata['editoroptions'];
		$mform->addElement('text', 'title', '笔记题目'); // Add elements to your form
		$mform->setType('title', PARAM_FILE);                  //Set type of element
		$mform->addRule('title', null, 'required');
		$mform->addRule('title', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		//$mform->setDefault('email', 'Please enter email');        //Default value
		$mform->addElement('editor','content', get_string('coursesummary'), null, $editoroptions);
		$mform->addHelpButton('content', 'coursesummary');
		$mform->setType('content', PARAM_RAW);//Set type of element
		$summaryfields = 'content';

		//设置表单中的默认值
		if(isset($_GET['noteTitle'])){
			$noteTitle = $_GET['noteTitle'];//获取url 传递的笔记标题
		}else{
			$noteTitle = null;
		}
		
		if(!empty($noteTitle)){
			$mform->setDefault('title',$noteTitle);//设置默认的标题，即默认以课程的名称为标题
			$mform->setDefault('editor',null);//设置笔记内容
		}

		$mform->addRule('content', null, 'required');
		$this->add_action_buttons(true, '创建笔记');
	}
	//Custom validation should be added here
	/*
	 * add by zxf
	 * time:2016/1/9
	 * add note not need the function to valid
	 */
	/*
	function validation($data, $files) {
		//验证标签名是否重复
		global $DB;
		$duplicate=$DB->record_exists('note_my', array('tagname'=>$data['tagname']));
		if ($duplicate) {
            $errors['tagname'] = '标签名称已存在';
        }
		return $errors;
	}*/
	/*
	 * add by zxf
	 * time:2016/1/9
	 * so end
	 */
}


echo $OUTPUT->header();
//echo '<h2>添加笔记</h2>';

$mform = new simplehtml_form();

//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/mod/notemy/index.php'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //插入数据
	$time=date('Y-m-d-G-i-s');;//获取当前时间
	$noteArray=new stdClass();
	//3.	获取用户iD
	$noteArray->userid=$USER->id;//用户id
	//2.	获取输入控件中的值（笔记标题和笔记内容的值）
	$noteArray->title=$fromform->title;//笔记题目
	//$temp=$fromform->content['text'];//存放数组
	$noteArray->content=$fromform->content['text'];//笔记内容
	//4.	获取当前时间
	$noteArray->time=$time;//笔记时间
	//$noteArray=array('id'=>'','usserid'=>$USER->id,'title'=>$fromform['notetitle'],'content'=>$fromform['notecontent'],'time'=>$time);

	//5.	将数据插入数据库中
	$newid = $DB->insert_record('note_my',$noteArray, true);
//	redirect(new moodle_url('/mod/notemy/index.php'));
	//header('Location: '.new moodle_url('/mod/notemy/index.php').'');
	redirect(new moodle_url('/mod/notemy/index.php', array('id' => $newid)));
} else {
  // 这里用于处理数据不符合要求或第一次显示表单
 
  //设置默认数据
  //$mform->set_data($toform);
  //显示表单
  $mform->display();
}


echo $OUTPUT->footer();
