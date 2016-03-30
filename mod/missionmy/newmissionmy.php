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

$PAGE->set_url('/mod/missionmy/newmissionmy.php');
$PAGE->set_title('创建台账任务');
$PAGE->set_heading('创建台账任务');
$PAGE->set_pagelayout('missionmy');//设置layout文件
require_login();//要求登录
global $DB;
class simplehtml_form extends moodleform {

	//Add elements to form //初始化表单元素
	public function definition() {
		global $DB;
		global $CFG;
		global $USER;

		//1.	输入界面（台账任务的内容）
		$mform = $this->_form; // Don't forget the underscore!
		$editoroptions = $this->_customdata['editoroptions'];
		$mform->addElement('text', 'mission_name', '任务标题'); // Add elements to your form
		$mform->setType('mission_name', PARAM_FILE);                  //Set type of element
		$mform->addRule('mission_name', null, 'required');
		$mform->addRule('mission_name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
		//$mform->setDefault('email', 'Please enter email');        //Default value

		//必修课
		$requiredCourses = $DB->get_records_sql('select m.id,m.fullname from mdl_course m where m.id != 1');//获取所有课程
		$requiredCourseOfOptions = array();
		//将课程id和fullname生成键值对
		foreach($requiredCourses as $course){
			$requiredCourseOfOptions[$course->id]=$course->fullname;
		}
		$requiredSelect = $mform->addElement('select', 'requiredSelect', '必修课', $requiredCourseOfOptions);
		$requiredSelect->setMultiple(true);

		//选修课
		$optionalCourses = $DB->get_records_sql('select m.id,m.fullname from mdl_course m where m.id != 1');//获取所有课程
		$optionalCourseOfOptions = array();
		//将课程id和fullname生成键值对
		foreach($optionalCourses as $course){
			$optionalCourseOfOptions[$course->id]=$course->fullname;
		}
		$optionalSelect = $mform->addElement('select', 'optionalSelect', '选修课', $optionalCourseOfOptions);
		$optionalSelect->setMultiple(true);

		$mform->addElement('date_time_selector', 'startTime', '任务开始时间');
		$mform->addElement('date_time_selector', 'endTime', '任务结束时间');

		$enableSelect = array('0'=>'开启','1'=>'关闭');
		$mform->addElement('select', 'enable', '任务开关',$enableSelect); // Add elements to your form

		//分配任务
//		$missionUsers = $DB->get_records_sql('select u.id,u.lastname,u.firstname from mdl_user u ');//获取所有人员
		$missionUserIDs = array();
//		//将课程id和name生成键值对
//		foreach($missionUsers as $user){
//			$missionUserIDs[$user->id] = $user->lastname.$user->firstname;
//		}
//		$this->add_action_buttons(false, '添加人员');
		$userSelect = $mform->addElement('select', 'userSelect', '分配人员', $missionUserIDs);

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

		$userSelect->setMultiple(true);

//		echo'<button id="show" class="btn btn-success">添加人员</button>';

		/** Start 推送台账任务消息复选框 02.28 毛英东 */
		$mform->addElement('checkbox', 'pushmissionwarning', "是否推送台账任务消息");
		/** End */
		
		$this->add_action_buttons(true, '创建台账');
	}

	//Custom validation should be added here	在这里添加自定义验证
	function validation($data, $files) {
		//验证标签名是否重复
		global $DB;

		//对选修课和必修课是否重复的判断
		$selectDifferent = null;

		if(isset($data['requiredSelect']) && isset($data['optionalSelect'])){
			$requiredSelect = $data['requiredSelect'];
			$optionalSelect = $data['optionalSelect'];

			$selectDifferent  = array_intersect($requiredSelect,$optionalSelect);
		}

		$errors = '';
		if ($selectDifferent) {
            $errors['requiredSelect'] = '选课冲突!（可能是必修课和选修课重复了）';
            $errors['optionalSelect'] = '选课冲突!（可能是必修课和选修课重复了）';
        }
		return $errors;
	}

}


echo $OUTPUT->header();
echo '<h2>添加台账</h2>';

$mform = new simplehtml_form();

//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/mod/missionmy/index.php'));
} else if ($fromform = $mform->get_data()) {

    //数据处理流程$mform->get_data() 返回所有提交的数据.(即获取填写表单的数据)
    //插入数据
	//	获取输入控件中的值
	//mission_my表
	$missionArray=new stdClass();
	$missionArray->mission_name = $fromform->mission_name;//台账标题
	$requiredCourseNum = 0;
	$requiredCourseID = 0;
	if(isset($fromform->requiredSelect)){
		$requiredCourseNum = count($fromform->requiredSelect);
		$requiredCourseID = implode(',',$fromform->requiredSelect);
	}
	$missionArray->required_course_num = $requiredCourseNum;//必修课数量
	$missionArray->required_course_id = $requiredCourseID;//必修课
	$optionalCourseNum = 0;
	$optionalCourseID = 0;
	if(isset($fromform->optionalSelect)){
		$optionalCourseNum = count($fromform->optionalSelect);
		$optionalCourseID = implode(',',$fromform->optionalSelect);
	}
	$missionArray->optional_course_num = $optionalCourseNum;//选修课数量
	$missionArray->optional_course_id = $optionalCourseID;//选修课
	$missionArray->time_start = $fromform->startTime;//开始时间
	$missionArray->time_end = $fromform->endTime;//结束时间
	$missionArray->enable = $fromform->enable;//任务开关
	$newid = $DB->insert_record('mission_my',$missionArray, true);//	将数据插入数据库中
	
	/** Start 推送台账任务提醒02.28 毛英东 */
	if(isset($_POST['pushmissionwarning']) && $_POST['pushmissionwarning'] == '1' ){   //勾选了推送
		//接收信息的用户
		$users_i = $DB->get_records_sql("select `id` from `mdl_user`");
		foreach($users_i as $user_i){
			$bulk_users[] = $user_i -> id;
		}
		$msg = '您有一个新的台账任务《'.$missionArray->mission_name.'》，请进入个人中心查看';
		list($in, $params) = $DB->get_in_or_equal($bulk_users);
		$rs = $DB->get_recordset_select('user', "id $in", $params);
		foreach ($rs as $user) {
			//TODO we should probably support all text formats here or only FORMAT_MOODLE
			//For now bulk messaging is still using the html editor and its supplying html
			//so we have to use html format for it to be displayed correctly
			message_post_message($USER, $user, $msg, FORMAT_HTML);
		}
		unset($bulk_users);
		unset($users_i);
		$rs->close();
	}
	/** End */


	//mission_user_my表,对每个用户创建一条记录
	$mission_user_Array = new stdClass();
	$mission_user_Array->mission_id = $newid;//新任务的id
	/** Start 添加组织架构后的修改 徐东威 20160306 */
	$userIDs1 = $fromform->hidden_user;//获取用户id
	$userIDs2 = explode(',',$userIDs1);
	foreach($userIDs2 as $userID){
		$mission_user_Array->user_id = $userID;
		$newMissionUserID = $DB->insert_record('mission_user_my',$mission_user_Array, true);//将数据插入数据库中
	}
	/** End */
//	if(isset($fromform->userSelect)){//判断是否分配人员
//		$userIDs = $fromform->userSelect;
//		foreach($userIDs as $userID){
//			$mission_user_Array->user_id = $userID;
//			$newMissionUserID = $DB->insert_record('mission_user_my',$mission_user_Array, true);//将数据插入数据库中
//		}
//	}

	redirect(new moodle_url('/mod/missionmy/index.php', array('id' => $newid)));

} else {
  	// 这里用于处理数据不符合要求或第一次显示表单

  	//设置默认数据
  	//$mform->set_data($toform);
  	//显示表单
  	$mform->display();
}


echo $OUTPUT->footer();
