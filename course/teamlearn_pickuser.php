<?php

//  显示集体学习用户选择界面  20160102 岑霄

require_once('../config.php');
require_once('lib.php');
require_once("$CFG->libdir/formslib.php");
require_once($CFG->libdir.'/completionlib.php');
require_once("$CFG->dirroot/enrol/locallib.php");
require_once("$CFG->dirroot/enrol/renderer.php");
$id      = required_param('id', PARAM_INT); // course id
$action  = optional_param('action', '', PARAM_ALPHANUMEXT);
$filter  = optional_param('ifilter', 0, PARAM_INT);
$search  = optional_param('search', '', PARAM_RAW);
$role    = optional_param('role', 0, PARAM_INT);
$fgroup  = optional_param('filtergroup', 0, PARAM_INT);
$status  = optional_param('status', -1, PARAM_INT);
$newcourse = optional_param('newcourse', false, PARAM_BOOL);

require_login();//要求登录
$PAGE->set_url('/course/teamlearn_pickuser.php');
$PAGE->set_title('集体学习设置');
$PAGE->set_heading('集体学习设置');
$PAGE->set_pagelayout('teamlernpicker');//设置layout文件,默认cloumns1


//查询已选课的用户
// $course = $DB->get_record('course', array('id'=>2), '*', MUST_EXIST);
// $manager = new course_enrolment_manager($PAGE, $course, $filter, $role, $search, $fgroup, $status);
// $table = new course_enrolment_users_table($manager, $PAGE);
// $users = $manager->get_users_for_display($manager, $table->sort, $table->sortdirection, $table->page, $table->perpage);
// $GLOBALS['users'];
// $displaylist = new stdClass();
// foreach($users as $user){
	// $displaylist->id= $user->userid;
	// $displaylist->firstname= $user->firstname;
// }
$GLOBALS['id']=$id;
class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		global $DB;
		global $USER;
		$users = $DB->get_records_sql('select userid,firstname from mdl_enrol a join mdl_user_enrolments b join mdl_user c where a.courseid ='.$GLOBALS['id'].' and a.id = b.enrolid and b.userid= c.id and b.userid!='.$USER->id);
		$userlist= array();
		// foreach($users as $user){
			// $userlist[$user->userid]=$user->firstname;
		// }
		$mform = $this->_form; // Don't forget the underscore! 
		$select = $mform->addElement('select', 'userSelect', '集体学习用户',$userlist);
		$select->setMultiple(true);
		/** Start 获取人员 朱子武20160303 */
		$mform->addElement('button', 'add_person', "添加人员");
		/** End */

		/** Start 删除人员 朱子武20160306 */
		$mform->addElement('button', 'delete_person', "删除人员");
		
		/** End */
		
		/** Start 隐藏的select用户 岑霄20160306*/
		$attributes='id="id_hidden_user"';
		$mform->addElement('hidden', 'hidden_user', '所选用户',$attributes);
		/**End */
		//$mform->addHelpButton('multicategory', 'coursecategory');
		//$mform->setDefault('multiuser', $category->id);
		$this->add_action_buttons(true, '提交');
	}
	//Custom validation should be added here
	function validation($data, $files) {
		//验证标签名是否重复
		// global $DB;
		// $duplicate=$DB->record_exists('tag_my', array('tagname'=>$data['tagname']));
		// if ($duplicate) {
            // $errors['tagname'] = '标签名称已存在';
        // }
		return null;
		//return $errors;
	}
}
$currenturl = new moodle_url('/course/teamlearn_pickuser.php?id='.$id);
echo $OUTPUT->header();
echo '<h2>集体学习：选择用户</h2>';
$mform = new simplehtml_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/course/view.php?id='.$id));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.
	//保存用户id到session里，跳转课程页面开始集体学习
	$_SESSION['collectiveIDs'] = Array();
	
	/** Start 徐东威 更改数据获取方式 */
	// $users= $fromform->firstname;
	// if($users!=null){
		// foreach($users as $user){
			// $_SESSION['collectiveIDs'][] = $user;
		// }
		// $_SESSION['collectiveLearn'] = TRUE;
	// }
	$usersID= explode(',',$fromform->hidden_user);//获取添加集体学习的伙伴ID
	if($usersID!=null){
		foreach($usersID as $userID){
			$_SESSION['collectiveIDs'][] = $userID;
		}
		$_SESSION['collectiveLearn'] = TRUE;
	}
	/** End */
	
	// $teamlearn_switch->teamlearn_switch = $fromform->teamlearn_switch;
	// $DB->update_record_raw('teamlearn_switch', $teamlearn_switch);
	redirect(new moodle_url('/course/view.php?id='.$id));
} else {
  // 这里用于处理数据不符合要求或第一次显示表单
 
  //设置默认数据
  //$users= array('1' => '开','2' => '关');
  //$mform->set_data($users);
  //显示表单
  $mform->display();
}
echo $OUTPUT->footer();//输出左右和底部
