<?php
/** 分级管理员课程创建、编辑页面
 *  （是 D:\WWW\moodle\course\edit.php 的副本改写）
 * 注意：是‘课程属性’管理页面，而不是‘课程学习内容’编辑界面
 */

require_once('../../config.php');
require_once('../lib.php');
require_once('edit_form_my.php');
require_once($CFG->dirroot.'/org_classify/org.class.php');

/** Start 身份认证  分级管理员\ 慕课管理员、超级管理员 */
global $USER;
require_once($CFG->dirroot.'/user/my_role_conf.class.php');
$role = new my_role_conf();
if($DB->record_exists('role_assignments', array('roleid' => $role->get_gradingadmin_role(),'userid' => $USER->id))){
	$role_flag = 1;//分级管理员
	$return_indexURL = $CFG->wwwroot.'/course/course_classify/index_gradingAdmin.php';//分级管理员 课程管理
}
else if($DB->record_exists('role_assignments', array('roleid' => $role->get_courseadmin_role(),'userid' => $USER->id))
		  || ($USER->id == 2) ){
	$role_flag = 2;//慕课管理员、超级管理员
	$return_indexURL = $CFG->wwwroot.'/course/course_classify/index_courseAdmin.php';//慕课管理员 课程管理
}
if($role_flag != 1 && $role_flag != 2){
	redirect($CFG->wwwroot);
}
/** end */

$id = optional_param('id', 0, PARAM_INT); // Course id.
$categoryid = optional_param('category', 0, PARAM_INT); // Course category - can be changed in edit form.
$returnto = optional_param('returnto', 0, PARAM_ALPHANUM); // Generic navigation return page switch.
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL); // A return URL. returnto must also be set to 'url'.

if ($returnto === 'url' && confirm_sesskey() && $returnurl) {
	// If returnto is 'url' then $returnurl may be used as the destination to return to after saving or cancelling.
	// Sesskey must be specified, and would be set by the form anyway.
	$returnurl = new moodle_url($returnurl);
} else {
	if (!empty($id)) {
		$returnurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $id));
	} else {
		$returnurl = new moodle_url($CFG->wwwroot . '/course/');
	}
	if ($returnto !== 0) {
		switch ($returnto) {
			case 'category':
				$returnurl = new moodle_url($CFG->wwwroot . '/course/index.php', array('categoryid' => $categoryid));
				break;
			case 'catmanage':
				$returnurl = new moodle_url($CFG->wwwroot . '/course/management.php', array('categoryid' => $categoryid));
				break;
			case 'topcatmanage':
				$returnurl = new moodle_url($CFG->wwwroot . '/course/management.php');
				break;
			case 'topcat':
				$returnurl = new moodle_url($CFG->wwwroot . '/course/');
				break;
		}
	}
}

$PAGE->set_pagelayout('admin');
if ($id) {
	$pageparams = array('id' => $id);
} else {
	$pageparams = array('category' => $categoryid);
}
if ($returnto !== 0) {
	$pageparams['returnto'] = $returnto;
	if ($returnto === 'url' && $returnurl) {
		$pageparams['returnurl'] = $returnurl;
	}
}
$PAGE->set_url('/course/course_classify/create_course.php', $pageparams);

// Basic access control checks.
if ($id) {
	// Editing course.
	//编辑课程
	if ($id == SITEID){
		// Don't allow editing of  'site course' using this from.
		print_error('cannoteditsiteform');
	}

	// Login to the course and retrieve also all fields defined by course format.
	$course = get_course($id);
//	require_login($course);
	$course = course_get_format($course)->get_course();

	$category = $DB->get_record('course_categories', array('id'=>$course->category), '*', MUST_EXIST);
	$coursecontext = context_course::instance($course->id);
//	require_capability('moodle/course:update', $coursecontext);//课程修改权限

} else if ($categoryid) {
	// Creating new course in this category.
	//新建课程
	$course = null;
	require_login();
	$category = $DB->get_record('course_categories', array('id'=>$categoryid), '*', MUST_EXIST);
	$catcontext = context_coursecat::instance($category->id);
//	require_capability('moodle/course:create', $catcontext);
	$PAGE->set_context($catcontext);

} else {
	require_login();
	print_error('needcoursecategroyid');
}

// Prepare course and the editor.
$editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
$overviewfilesoptions = course_overviewfiles_options($course);
if (!empty($course)) {
	// Add context for editor.
	$editoroptions['context'] = $coursecontext;
	$editoroptions['subdirs'] = file_area_contains_subdirs($coursecontext, 'course', 'summary', 0);
	$course = file_prepare_standard_editor($course, 'summary', $editoroptions, $coursecontext, 'course', 'summary', 0);
	if ($overviewfilesoptions) {
		file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, $coursecontext, 'course', 'overviewfiles', 0);
	}

	// Inject current aliases.
	$aliases = $DB->get_records('role_names', array('contextid'=>$coursecontext->id));
	foreach($aliases as $alias) {
		$course->{'role_'.$alias->roleid} = $alias->name;
	}

} else {
	// Editor should respect category context if course context is not set.
	$editoroptions['context'] = $catcontext;
	$editoroptions['subdirs'] = 0;
	$course = file_prepare_standard_editor($course, 'summary', $editoroptions, null, 'course', 'summary', null);
	if ($overviewfilesoptions) {
		file_prepare_standard_filemanager($course, 'overviewfiles', $overviewfilesoptions, null, 'course', 'overviewfiles', 0);
	}
}

// First create the form.
//创建表单
$args = array(
	'course' => $course,
	'category' => $category,
	'editoroptions' => $editoroptions,
	'returnto' => $returnto,
	'returnurl' => $returnurl
);
$editform = new course_edit_form(null, $args);
//判断表单提交
if ($editform->is_cancelled()) {
	// The form has been cancelled, take them back to what ever the return to is.
//	redirect($returnurl);
	redirect($return_indexURL);
} else if ($data = $editform->get_data()) {
	// Process data if submitted.
	if (empty($course->id)) {
		// In creating the course.
		//创建课程
		$course = create_course($data, $editoroptions);

		/** Start 如果是分级管理员创建的课程，记录课程的管理(创建)单位 */
		if($role_flag == 1){
			$user_org = $DB->get_record_sql("select * from mdl_org_link_user ol where ol.user_id = $USER->id");
			$browseableStr = $user_org->org_id;//获取当前单位节点id
			$org = new org();
			$org_node = $org->get_node($user_org->org_id);//获取当前单位节点
			$sub_orgs = $org->get_all_child($org_node);//获取当前单位节点下的所有子节点

			$courseID = $course->id;
			$org_courseObjArray = array();
			$org_courseObj = new stdClass();//当前单位
			$org_courseObj->org_id = $user_org->org_id;
			$org_courseObj->course_id = $courseID;
			$org_courseObjArray[] = $org_courseObj;
			foreach($sub_orgs as $temp){//各子单位
				$subOrg_courseObj = new stdClass();
				$subOrg_courseObj->org_id = $temp['id'];
				$subOrg_courseObj->course_id = $courseID;
				$org_courseObjArray[] = $subOrg_courseObj;
			}
			$DB->insert_record("course_org_my",array("courseid"=>$course->id,"manage_org"=>$user_org->org_id,"browseable_org"=>$browseableStr),true);
			$DB->insert_records("org_course_my",$org_courseObjArray);
		}
		/** end */
		/** Start 如果是慕课管理员、超级管理员 创建的课程，默认所有的单位可浏览 */
		if($role_flag == 2){
			$org = new org();
			$root_noteID = $org->get_root_node_id();
			$org_node = $org->get_node($root_noteID);//获取单位根节点
			$sub_orgs = $org->get_all_child($org_node);//获取根节点下的所有子节点

			$courseID = $course->id;
			$org_courseObjArray = array();
			$org_courseObj = new stdClass();//当前单位
			$org_courseObj->org_id = $root_noteID;
			$org_courseObj->course_id = $courseID;
			$org_courseObjArray[] = $org_courseObj;
			foreach($sub_orgs as $temp){//各子单位
				$subOrg_courseObj = new stdClass();
				$subOrg_courseObj->org_id = $temp['id'];
				$subOrg_courseObj->course_id = $courseID;
				$org_courseObjArray[] = $subOrg_courseObj;
			}
			$DB->insert_record("course_org_my",array("courseid"=>$course->id,"manage_org"=>$user_org->org_id,"browseable_org"=>$root_noteID),true);
			$DB->insert_records("org_course_my",$org_courseObjArray);
		}
		/** end */

		/** Start 将课程ID加入mdl_course_order_my表中 用于统计周 月 总浏览数 0160302 毛英东 */
		$DB -> execute("insert into mdl_course_order_my (courseid)values(".$course->id.")");
		/** End */

		/** Start 将课程ID加入评分表mdl_score_course_sum_my中 20160302 毛英东 */
		$DB -> execute("insert into mdl_score_course_sum_my (courseid)values(".$course->id.")");
		/** End */

		/** Start 推送新课程添加提醒02.27 毛英东 */
		if(isset($_POST['pushnewcourse']) && $_POST['pushnewcourse'] == '1' ){   //勾选了推送考试
			//接收信息的用户
			$users_i = $DB->get_records_sql("select `id` from `mdl_user`");
			foreach($users_i as $user_i){
				$bulk_users[] = $user_i -> id;
			}
			$msg = '新课程《'.$course->fullname .'》开课了！ <br />
            <a href="../course/view.php?id='.$course->id .'">点击查看课程详情</a> <br />';
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

		// Get the context of the newly created course.
		$context = context_course::instance($course->id, MUST_EXIST);

		if (!empty($CFG->creatornewroleid) and !is_viewing($context, NULL, 'moodle/role:assign') and !is_enrolled($context, NULL, 'moodle/role:assign')) {
			// Deal with course creators - enrol them internally with default role.
			enrol_try_internal_enrol($course->id, $USER->id, $CFG->creatornewroleid);
		}

		// The URL to take them to if they chose save and display.
		$courseurl = new moodle_url('/course/view.php', array('id' => $course->id));

		// If they choose to save and display, and they are not enrolled take them to the enrolments page instead.
		if (!is_enrolled($context) && isset($data->saveanddisplay)) {
			// Redirect to manual enrolment page if possible.
			$instances = enrol_get_instances($course->id, true);
			foreach($instances as $instance) {
				if ($plugin = enrol_get_plugin($instance->enrol)) {
					if ($plugin->get_manual_enrol_link($instance)) {
						// We know that the ajax enrol UI will have an option to enrol.
						$courseurl = new moodle_url('/enrol/users.php', array('id' => $course->id, 'newcourse' => 1));
						break;
					}
				}
			}
		}
	} else {
		// Save any changes to the files used in the editor.
		//已有，更新课程
		update_course($data, $editoroptions);
		// Set the URL to take them too if they choose save and display.
		$courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
		$returnurl = new moodle_url($return_indexURL);
	}

	if (isset($data->saveanddisplay)) {
		// Redirect user to newly created/updated course.
		redirect($courseurl);
	} else {
		// Save and return. Take them back to wherever.
		redirect($returnurl);
	}
}

// Print the form.

$site = get_site();

$streditcoursesettings = get_string("editcoursesettings");
$straddnewcourse = get_string("addnewcourse");
$stradministration = get_string("administration");
$strcategories = get_string("categories");

if (!empty($course->id)) {
	// Navigation note: The user is editing a course, the course will exist within the navigation and settings.
	// The navigation will automatically find the Edit settings page under course navigation.
	$pagedesc = $streditcoursesettings;
	$title = $streditcoursesettings;
	$fullname = $course->fullname;
} else {
	// The user is adding a course, this page isn't presented in the site navigation/admin.
	// Adding a new course is part of course category management territory.
	// We'd prefer to use the management interface URL without args.
	$managementurl = new moodle_url('/course/management.php');
	// These are the caps required in order to see the management interface.
	$managementcaps = array('moodle/category:manage', 'moodle/course:create');
	if ($categoryid && !has_any_capability($managementcaps, context_system::instance())) {
		// If the user doesn't have either manage caps then they can only manage within the given category.
		$managementurl->param('categoryid', $categoryid);
	}
	// Because the course category management interfaces are buried in the admin tree and that is loaded by ajax
	// we need to manually tell the navigation we need it loaded. The second arg does this.
	navigation_node::override_active_url($managementurl, true);

	$pagedesc = $straddnewcourse;
	$title = "$site->shortname: $straddnewcourse";
	$fullname = $site->fullname;
	$PAGE->navbar->add($pagedesc);
}

$PAGE->set_title($title);
$PAGE->set_heading($fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($pagedesc);

$editform->display();

echo $OUTPUT->footer();
