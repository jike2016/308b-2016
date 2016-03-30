
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

$PAGE->set_pagelayout('newnotemy_personal');//设置layout
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
		global $DB;


		//1.	输入界面（笔记标题和笔记内容）
		$mform = $this->_form; // Don't forget the underscore!

		$mform->addElement('text', 'title', '笔记题目:'); // Add elements to your form
		$mform->setType('title', PARAM_FILE);                  //Set type of element
		$mform->addRule('title', null, 'required');
		$mform->addRule('title', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');


		//获取用户所选的课程信息
		$Courses = $DB->get_records_sql('select
									c.id,c.category,c.fullname,c.summary
									from mdl_user_enrolments a
									join mdl_enrol b on b.id=a.enrolid
									join mdl_course c on c.id=b.courseid
									where a.userid='.$USER->id.'
									GROUP BY courseid ORDER BY a.timecreated DESC');
		$CourseOfOptions = array();
		//将课程id和fullname生成键值对
		foreach($Courses as $course){
			$CourseOfOptions[$course->id]=$course->fullname;
		}
		$mform->addElement('select', 'courseSelect', '针对课程:', $CourseOfOptions);
		$mform->setType('courseSelect', PARAM_FILE);                  //Set type of element
		$mform->addRule('courseSelect', null, 'required');
		$mform->addRule('courseSelect', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

		// $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
		$editoroptions = array('maxfiles' => 2, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true,'enable_filemanagement' =>false);
		$mform->addElement('editor','content', '笔记内容:', null, $editoroptions);
		// $mform->addHelpButton('content', 'coursesummary');
		$mform->setType('content', PARAM_RAW);//Set type of element
		$summaryfields = 'content';
		$mform->addRule('content', null, 'required');

		//获取url 提交的参数
		// if(isset($_GET['courseid'])){
			// $courseid = $_GET['courseid'];//获取url 传递的笔记标题
			// $course = $DB->get_record_sql("select c.id,c.fullname from mdl_course c where c.id = $courseid");

			// $mform->setDefault('title',$course->fullname);//设置默认的标题，即默认以课程的名称为标题
			// $mform->setDefault('courseSelect',$courseid);//设置默认的标题，即默认以课程的名称为标题
			// $mform->setDefault('editor',null);//设置笔记内容
		// }else{
			// $courseid = null;
		// }
		//获取url 提交的参数
		if(isset($_GET['courseid'])){
			$courseid = $_GET['courseid'];//获取url 传递的笔记标题
			$mform->setDefault('courseSelect',$courseid);//设置默认的标题，即默认以课程的名称为标题
		}
		//获取url 提交的参数
		if(isset($_GET['noteTitle'])){
			$noteTitle = $_GET['noteTitle'];//获取url 传递的笔记标题
			$mform->setDefault('title',$noteTitle);//设置默认的标题，即默认以课程的名称为标题
		}

		// $this->add_action_buttons(true, '创建笔记');
		$buttonarray=array();
		$buttonarray[] =& $mform->createElement('submit', 'submitbutton', '创建笔记');
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
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
	$time=time();//获取当前时间
	$noteArray=new stdClass();
	//3.	获取用户iD
	$noteArray->userid=$USER->id;//用户id
	$noteArray->notetype = 1;//笔记类型 1:课程笔记 2:个人笔记
	$noteArray->courseid = $fromform->courseSelect;//课程id
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
	if($newid){
		echo '<script>alert("创建成功");</script>';
	}else{
		echo '<script>alert("创建失败");</script>';
	}
//	redirect(new moodle_url('/mod/notemy/index.php'));
	$url = new moodle_url('/mod/notemy/newnotemy_course.php', array('id' => $newid));
	echo "<script language='javascript' type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;

} else {
  // 这里用于处理数据不符合要求或第一次显示表单
 
  //设置默认数据
  //$mform->set_data($toform);
  //显示表单
  $mform->display();
}


echo $OUTPUT->footer();
