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
//2.	从跳转url中获取任务的id
$missionmyid = required_param('id', PARAM_INT);
$action = optional_param('action', 'details', PARAM_TEXT);

//获取标签相关信息
//3.	通过任务id获取这条任务的数据
$missionmydata = $DB->get_record('mission_my', array('id' => $missionmyid));
require_login();//要求登录
$currenturl = new moodle_url('/mod/missionmy/edit.php', array('id' => $missionmyid, 'action' => $action));
$PAGE->set_url($currenturl);
$PAGE->set_title($missionmydata->mission_name);
$PAGE->set_heading($missionmydata->mission_name);
$PAGE->set_pagelayout('missionmy');//设置layout文件

class simplehtml_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;
		global $UESR;
		global $DB;
		$missionmyid = required_param('id', PARAM_INT);
		$missionmydata = $DB->get_record('mission_my', array('id' => $missionmyid));
		//将台账任务的内容填入到相应的控件中
		$mform = $this->_form; // Don't forget the underscore!//创建表单对象

		$mform->addElement('text', 'mission_name', '任务标题'); // Add elements to your form
		$mform->setType('mission_name', PARAM_FILE);                  //Set type of element
		$mform->addRule('mission_name', null, 'required');
		$mform->addRule('mission_name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');

		//必修课
		$requiredCourses = $DB->get_records_sql('select m.id,m.fullname from mdl_course m where m.id != 1');//获取所有课程
		$requiredCourseOfOptions = array();
		//将课程id和fullname生成键值对
		foreach($requiredCourses as $course){
			$requiredCourseOfOptions[$course->id]=$course->fullname;
		}
		$requiredSelect = $mform->addElement('select', 'requiredSelect', '必修课', $requiredCourseOfOptions);
		$requiredSelect->setMultiple(true);
		//获取数据并设置为默认值
		$requiredCoursesID = explode(',',$missionmydata->required_course_id);
		$requiredSelect->setSelected($requiredCoursesID);

		//选修课
		$optionalCourses = $DB->get_records_sql('select m.id,m.fullname from mdl_course m where m.id != 1');//获取所有课程
		$optionalCourseOfOptions = array();
		//将课程id和fullname生成键值对
		foreach($optionalCourses as $course){
			$optionalCourseOfOptions[$course->id]=$course->fullname;
		}
		$optionalSelect = $mform->addElement('select', 'optionalSelect', '选修课', $optionalCourseOfOptions);
		$optionalSelect->setMultiple(true);
		//获取数据并设置为默认值
		$optionalCoursesID = explode(',',$missionmydata->optional_course_id);
		$optionalSelect->setSelected($optionalCoursesID);

		$mform->addElement('date_time_selector', 'time_start', '任务开始时间');//注意：这里的控件名称要与数据库的字段对应才能自动填充
		$mform->addElement('date_time_selector', 'time_end', '任务结束时间');//注意：这里的控件名称要与数据库的字段对应才能自动填充

		$enableSelect = array('0'=>'开启','1'=>'关闭');
		$mform->addElement('select', 'enable', '任务开关',$enableSelect); // Add elements to your form




//		//分配任务
//		$missionUsers = $DB->get_records_sql('select u.id,u.lastname,u.firstname from mdl_user u ');//获取所有人员
//		$missionUserIDs = array();
//		//将课程id和fullname生成键值对
//		foreach($missionUsers as $user){
//			$missionUserIDs[$user->id] = $user->lastname.$user->firstname;
//		}
//		$userSelect = $mform->addElement('select', 'userSelect', '分配人员', $missionUserIDs);
//		$userSelect->setMultiple(true);

		/**Start 徐东威 20160306*/
		//获取数据并设置为默认值
		$userIDs = $DB->get_records_sql("select * from mdl_mission_user_my mum where mum.mission_id = $missionmyid");//获取任务相关人员
		$userids = '';
		$num = 1;
		foreach($userIDs as $userID){
			if($num !=count($userIDs)){
				$userids .= $userID->user_id.',';
			}else{
				$userids .= $userID->user_id.'';
			}
			$num++;
		}

		$missionUserIDs = array();
		if($num!=1){
			$missionUsers = $DB->get_records_sql("select u.id,u.lastname,u.firstname from mdl_user u where u.id in ($userids)");//获取人员信息
			//将课程id和fullname生成键值对
			foreach($missionUsers as $user){
				$missionUserIDs[$user->id] = $user->lastname.$user->firstname;
			}
		}
		$userSelect = $mform->addElement('select', 'userSelect', '分配人员', $missionUserIDs);
		$userSelect->setMultiple(true);


		/** Start 隐藏的select用户 岑霄20160306*/
		$attributes='id="id_hidden_user"';
		$mform->addElement('hidden', 'hidden_user', '所选用户',$attributes);
		/**End */

//		$userNum = 0;
//		$userIDArray = array();
//		foreach($userIDs as $user){
//			$userIDArray[$userNum] = $user->user_id;
//			$userNum++;
//		}
//		$userSelect->setSelected($userIDArray);//设置已选人员默认值
		/** End */


		/** Start 获取人员 朱子武20160303 */
		$mform->addElement('button', 'add_person', "添加人员");
		/** End */

		/** Start 删除人员 朱子武20160306 */
		$mform->addElement('button', 'delete_person', "删除人员");
		/** End */
		
//		//获取数据并设置为默认值
//		$userIDs = $DB->get_records_sql("select * from mdl_mission_user_my mum where mum.mission_id = $missionmyid");//获取相关人员
//		$userNum = 0;
//		$userIDArray = array();
//		foreach($userIDs as $user){
//			$userIDArray[$userNum] = $user->user_id;
//			$userNum++;
//		}
//		$userSelect->setSelected($userIDArray);//设置已选人员默认值


		$this->add_action_buttons(true, '保存更改');
	}

	//Custom validation should be added here
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

echo '<h2>修改台账</h2>';

$mform = new simplehtml_form($currenturl);

//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/mod/missionmy/index.php'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //修改数据
	//	获取界面中的控件中值
	//	获取当前修改的时间
	$time=date('Y-m-d-G-i-s');;//获取当前时间
	$missionMy=new stdClass();

	//注意：这里的字段要与数据库中的完全一样
	$missionMy->id = $missionmyid;//台账id
	$missionMy->mission_name = $fromform->mission_name;//台账标题
	$requiredCourseNum = 0;
	$requiredCourseID = 0;
	if(isset($fromform->requiredSelect)){
		$requiredCourseNum = count($fromform->requiredSelect);
		$requiredCourseID = implode(',',$fromform->requiredSelect);
	}
	$missionMy->required_course_num = $requiredCourseNum;//必修课数量
	$missionMy->required_course_id = $requiredCourseID;//必修课
	$optionalCourseNum = 0;
	$optionalCourseID = 0;
	if(isset($fromform->optionalSelect)){
		$optionalCourseNum = count($fromform->optionalSelect);
		$optionalCourseID = implode(',',$fromform->optionalSelect);
	}
	$missionMy->optional_course_num = $optionalCourseNum;//选修课数量
	$missionMy->optional_course_id = $optionalCourseID;//选修课
	$missionMy->time_start = $fromform->time_start;//开始时间
	$missionMy->time_end = $fromform->time_end;//结束时间
	$missionMy->enable = $fromform->enable;//任务开关
	//	将数据更新到数据库中
	$newid=$DB->update_record_raw('mission_my', $missionMy);


	//mission_user_my表,对每个用户更新一条记录
	//思路：对原来的记录进行更新并无意义，所以删除原来的数据，重新添加新的记录
	$deleteUser = $DB->delete_records('mission_user_my',array('mission_id'=>$missionmyid));//删除原有数据

	$mission_user_Array = new stdClass();
	$mission_user_Array->mission_id = $missionmyid;//任务的id
	/** Start 徐东威 20160306 */
	//mission_user_my表,对每个用户创建一条记录
	$userIDs1 = $fromform->hidden_user;//获取用户id
	$userIDs2 = explode(',',$userIDs1);
	foreach($userIDs2 as $userID){
		$mission_user_Array->user_id = $userID;
		$newMissionUserID = $DB->insert_record('mission_user_my',$mission_user_Array, true);//将数据插入数据库中
	}
	/**End */

//	$mission_user_Array = new stdClass();
//	$mission_user_Array->mission_id = $missionmyid;
//	if(isset($fromform->userSelect)){
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
    $mform->set_data($missionmydata);//当控件的名称与数据库的字段对应的时候便会自动进行填充
    //显示表单
    $mform->display();
}


echo $OUTPUT->footer();
