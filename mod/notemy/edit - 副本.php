

<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
<script type="text/javascript">

	$(document).ready(function(){

		//打印按钮
		$('#id_print_btn').click(function (){

			//获得 div 里的所有 html 数据
			var noteTitle = document.getElementById('id_title').value;//笔记标题
			var noteContent = document.getElementById('id_mycontenteditable').innerHTML; //笔记内容
			var userName = document.getElementById('userName').innerHTML; //笔记作者
			var time = document.getElementById('time').innerHTML; //笔记作者
			var flag = document.getElementById('flag').innerHTML; //笔记类型判断
			//对内容的处理
			var newNoteContent = noteContent.replace(/<p>/g, "<p>&nbsp;&nbsp;");//段落开头的空两格
			var courseTitle = '';

			if(flag == 1){
//				alert('课程笔记');
				var courseName = document.getElementById('courseName').innerHTML; //课程名称
				courseTitle = '<tr><th colspan="3"><h1>'+userName +'《'+ courseName +'》心得</h1></th></tr>';
			}else if(flag == 2){
//				alert('个人笔记');
			}


			//打印页面内容的布局
			var headstr = "<html><head><title></title></head><body>" ;
			var text = '<table>';
				text += courseTitle;
				text += '<tr><th colspan="3"><h2>'+ noteTitle +'</h2></th></tr>';
				text += '<tr><td colspan="3">&nbsp;&nbsp;'+ newNoteContent +'</td></tr>';
				text += '<tr><td></td><td></td><td height="20px;"></td></tr>';
				text += '<tr><td></td><td></td><td  width="20%">姓名：'+ userName +'</td></tr>';
				text += '<tr><td></td><td></td><td  width="20%">'+ time +'</td></tr>';
				text += '</table>';
			var footstr = "</body></html>";

			var html = headstr + text + footstr;

			var printpage = window.open();
			printpage.document.write(html);
			printpage.print();
			printpage.close();

		});

	});

</script>



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
//2.	从跳转url中获取笔记的id
$notemyid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);

//获取标签相关信息
//3.	通过笔记id获取这条笔记的数据
$notemydata = $DB->get_record('note_my', array('id' => $notemyid));
require_login();//要求登录
$currenturl = new moodle_url('/mod/notemy/edit.php', array('id' => $notemyid, 'action' => $action));
$PAGE->set_url($currenturl);
$PAGE->set_title($notemydata->title);
$PAGE->set_heading($notemydata->title);
$PAGE->set_pagelayout('incourseforstudent');//设置layout
class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		global $USER;
		global $DB;
		$notemyid = required_param('id', PARAM_INT);
		$notemydata = $DB->get_record('note_my', array('id' => $notemyid));
		/**Start 打印 */
		$userName = fullname($USER);//用户名
		$time = userdate(time(),'%Y年%m月%d日');
		echo "<p id='userName' style='display:none;'>$userName </p>";
		echo "<p id='time' style='display:none;'>$time </p>";
		if($notemydata->notetype == 1){ //1:课程笔记； 2：个人笔记
			echo "<p id='flag' style='display:none;'>1</p>";
			$course = $DB->get_record_sql("select c.id,c.fullname from mdl_course c where c.id = $notemydata->courseid");
			$courseName = $course->fullname;
			echo "<p id='courseName' style='display:none;'>$courseName</p>";
		}else{
			echo "<p id='flag' style='display:none;'>2</p>";
		}
		/**End 打印 */
		
		//4.	将笔记标题和笔记内容填入到相应的控件中
		$mform = $this->_form; // Don't forget the underscore!//创建表单对象

		$mform->addElement('text', 'title', '笔记标题'); // Add elements to your form //向表单中添加元素
		$mform->setType('title', PARAM_FILE);                  //Set type of element
		$mform->addRule('title', null, 'required');
		$mform->addRule('title', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

		$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
		$mform->addElement('editor','mycontent', '笔记内容',null,$editoroptions);//添加内容框
		$mform->setType('mycontent', PARAM_RAW);//Set type of element
		$summaryfields = 'mycontent';
		//$mform->addHelpButton('mycontent', 'coursesummary');
		//$mform->setType('mycontent', PARAM_RAW);
		$mform->addRule('mycontent', get_string('required'), 'required', null, 'client');
		//$notemydata = file_prepare_standard_editor($notemydata, 'mycontent', $editoroptions, null, 'mod_notemy', 'notemydata', 0);

		//$mform->setDefault('email', 'Please enter email');        //Default value
		//$mform->addElement('textarea', 'content', '笔记内容'); // Add elements to your form
		//$this->add_action_buttons(true, '保存更改');
		
		//为添加创建按钮组
		$buttonarray=array();
		$buttonarray[] =& $mform->createElement('submit', 'submitbutton', '保存更改');
		$buttonarray[] =& $mform->createElement('button', 'print_btn', '打印');
		$buttonarray[] =& $mform->createElement('cancel', 'cancel', '取消');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		
		
	}
	//Custom validation should be added here
	/*
	 * add by zxf
	 * time:2016/1/9
	 * 笔记修改无须验证
	 */
	/*
	function validation($data, $files) {
		//验证标签名是否重复
		global $DB;
		$duplicate=$DB->record_exists('note_my', array('notetitle'=>$data['title']));
		if ($duplicate) {
            $errors['notetitle'] = '笔记已存在';
        }
		return $errors;
	}
	/*
	/*
	 * add by zxf
	 * time:2016/1/9
	 * end
	 */
}

echo $OUTPUT->header();

echo '<h2>笔记</h2>';

$mform = new simplehtml_form($currenturl);

//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	echo '<script>window.close();</script>';
	//redirect(new moodle_url('/mod/notemy/index.php'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //修改数据
	//5.	获取界面中的控件中值（笔记标题和笔记内容）
	//6.	获取当前修改的时间
	//$time=date('Y-m-d-G-i-s');;//获取当前时间
	$time = time();
	$notemy=new stdClass();
	$notemy->id=$notemyid;
	$notemy->userid=$USER->id;
	$notemy->title = $fromform->title;//修改笔记题目
	$notemy->content = $fromform->mycontent['text'];//修改笔记内容
	$notemy->time=$time;//当前时间
	//7.	将数据更新到数据库中
	$newid=$DB->update_record_raw('note_my', $notemy);
	redirect(new moodle_url('/mod/notemy/edit.php', array('id' => $notemyid)));
	//redirect(new moodle_url('/mod/notemy/index.php', array('id' => $newid)));
} else {
	// 这里用于处理数据不符合要求或第一次显示表单

	//添加
	$notemydata->mycontent = array('text'=> $notemydata->content);//这里需要将
    //设置默认数据
    $mform->set_data($notemydata);
    //显示表单
    $mform->display();
}


echo $OUTPUT->footer();
