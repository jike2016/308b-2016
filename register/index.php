<style>
		.navbar-form {padding: 10px 0px; }
		html, body {
			background-color: #ffffff;
		}
		@media (max-width: 1199px){
			body #region-main .mform:not(.unresponsive) .fitem .fitemtitle {
			    display: block;
			    display: block;
			    margin-top: 4px;
			    margin-bottom: 4px;
			    text-align: left;
			    width: 100%;
			    float: left;
			    width: 12%;
			}
			body #region-main .mform:not(.unresponsive) .fitem .felement {
			    margin-left: 0;
			    width: 100%;
			    float: left;
			    padding-left: 0;
			    padding-right: 0;
			    float: right;
			    width: 88%;
			}
			select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
				height: 40px;
			}
			.editor_atto {
				margin-top: 10px;
			}
			#region-main .mform:not(.unresponsive) .fitem .fitemtitle label {
			    font-weight: 100;
			    font-size: 16px;
			}
			.mform .fdescription.required {
			    margin-left: 0px;
			    text-align: center;
			    margin-bottom: 40px;
				display:none;
			}
			table#form td.submit, .form-buttons, .path-admin .buttons, #fitem_id_submitbutton, .fp-content-center form+div, div.backup-section+form, #fgroup_id_buttonar {
			    padding: 19px 20px 20px;
			    margin-top: 30px;
			}
			table#form td.submit, .form-buttons, .path-admin .buttons, #fitem_id_submitbutton, .fp-content-center form+div, div.backup-section+form, #fgroup_id_buttonar {
			    padding: 19px 0px 0px;
			    margin-top: 30px;
			}
		}
		.mform .fdescription.required {display:none;}
			
	</style>
<?php
/**用户注册页面
 * 叶靖华
 * 20160330
 */

require(dirname(__FILE__).'/../config.php');
require_once("$CFG->libdir/formslib.php");


$PAGE->set_url('/register/index.php');
$PAGE->set_title('用户注册');
$PAGE->set_heading('用户注册');
//$PAGE->set_pagelayout('incourseforstud');//设置layout
$PAGE->set_pagelayout('register');//设置layout
echo $OUTPUT->header();//输出layout文件
global $DB;

$s=optional_param('s',0 ,PARAM_INT);
/** @var  $register_switch 获取注册功能状态 叶靖华 20160331
 * 状态 1	注册功能已开
 * 状态 2	注册功能已关
 */
$register_switch = $DB->get_record('register_switch', array('id' => '1'))->register_switch;


/** End */
class simplehtml_form extends moodleform {

	//Add elements to form //初始化表单元素
	public function definition() {
		global $CFG;
		global $USER;

		$mform = $this->_form;
        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
        $mform->setType('username', PARAM_RAW);
		$mform->addRule('username', '用户名不能为空！', 'required', null, 'client');
        $auths = core_component::get_plugin_list('auth');
        $enabled = get_string('pluginenabled', 'core_plugin');
        $disabled = get_string('plugindisabled', 'core_plugin');
        $authoptions = array($enabled => array(), $disabled => array());
        $cannotchangepass = array();

        $mform->addElement('passwordunmask', 'newpassword', '密码', 'size="20"');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->disabledIf('newpassword', 'createpassword', 'checked');
        $mform->disabledIf('newpassword', 'auth', 'in', $cannotchangepass);
		$mform->addRule('newpassword', '密码不能为空！', 'required', null, 'client');
		$mform->addElement('passwordunmask', 'verifypassword', '确认密码', 'size="20"');
		$mform->setType('verifypassword', PARAM_RAW);
		$mform->disabledIf('verifypassword', 'createpassword', 'checked');
		$mform->addRule('verifypassword', '密码不能为空！', 'required', null, 'client');
		$mform->disabledIf('verifypassword', 'auth', 'in', $cannotchangepass);
		$mform->addElement('text', 'firstname', '姓名', 'size="20"');
		$mform->addRule('firstname', '姓名不能为空！', 'required', null, 'client');
		$mform->addElement('text', 'phonenum', '电话号码', 'size="20"');
		$mform->addElement('text', 'orgSelect', '所属单位', 'size="20"');
		$mform->disabledIf('orgSelect', '');//设置只读
		$mform->addElement('button', 'add_org', "选择单位");

		/** Start 隐藏的select单位 */
		$attributes='id="id_hidden_org"';
		$mform->addElement('hidden', 'hidden_org', '所选单位',$attributes);
		$mform->addRule('hidden_org', '所属单位不能为空！', 'required', null, 'client');
		/**End */

		$editoroptions = array( 'style'=>"height:100px;width: 500px;",'placeholder'=>'此处输入您的部级别，注册更容易通过管理员的审核哦！','maxlength'=>'300', 'wrap'=>'virtual', 'rows'=>10 ,'cols'=>10);
		$mform->addElement('textarea', 'description_editor', '自述:', $editoroptions);
		$this->add_action_buttons(true, '创建账号');
	}

	function validation($data, $files) {
		global $DB;
		$errors=array();
		if ($DB->get_record("user",array("username"=>$data['username']))!=false
			||$DB->get_record("register_temp_user",array("username"=>$data['username']))!=false){
			$errors['username']='用户名已经被注册！';
			return $errors;
		}
		if (mb_strlen($data['newpassword'])<6 ){
			$errors['newpassword']='密码长度要超过6位！';
			return $errors;
		}
		if ($data['newpassword']!=$data['verifypassword']){
			$errors['verifypassword'] = '密码不一致！';
			return $errors;
		}
		if ($data['hidden_org'] == '所选单位'){
			$errors['orgSelect'] = '所属单位不能为空！';
			return $errors;
		}
		if ($DB->get_record("user",array("firstname"=>$data['firstname']))!=false
			||$DB->get_record("register_temp_user",array("firstname"=>$data['firstname']))!=false){ //不允许重名
			$errors['firstname']='该姓名已经被注册！';
		}
		return $errors;
	}
}

if ($register_switch==1) {
	if ($s==1){
		echo '<h2>注册成功，请等待管理员审核</h2>';
	}else if($s==2) {
		echo '<h2>注册失败</h2>';
	}else{
		echo '<h2>用户注册</h2>';
	}
}else{
	echo '<h2>注册功能未开启</h2>';
}
$mform = new simplehtml_form();
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.
    //插入数据
	if ($register_switch==1) {
		$registerUser = new stdClass();
		$registerUser->username = $fromform->username;
		$registerUser->password = hash_internal_user_password($fromform->newpassword);
		$registerUser->firstname = $fromform->firstname;
		$registerUser->phone1 = $fromform->phonenum;
		$registerUser->org_id = (int)$fromform->hidden_org;
		$registerUser->description = $fromform->description_editor;
		$registerUser->admin_check=0;
		$registerUser->timecreated = time();
		if($DB->insert_record_raw("register_temp_user", $registerUser, true)) {
			redirect(new moodle_url('/register/index.php?s=1'));
		}else{
			redirect(new moodle_url('/register/index.php?s=2'));
		}
	}
} else {
	// 这里用于处理数据不符合要求或第一次显示表单
	//设置默认数据
	//$mform->set_data($toform);
	//显示表单
	global $USER;
	if ($USER->id > 0) {    //用户已经登录  不允许注册  跳转到首页
		redirect(new moodle_url('/'));
	}
	if ($register_switch==1&&$s!=1)
		$mform->display();
}

echo $OUTPUT->footer();
