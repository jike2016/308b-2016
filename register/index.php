<style>
		.navbar-form {padding: 10px 0px; }
		html, body {
			background-color: #ffffff;
		}
		@media (max-width: 1199px){
			body #region-main .mform:not(.unresponsive) .fitem .fitemtitle {
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
		.mform .fdescription.required {
			    display:none;
				
			}
			
	</style>
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

require(dirname(__FILE__).'/../config.php');
// require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");

$PAGE->set_url('/register/index.php');
$PAGE->set_title('用户注册');
$PAGE->set_heading('用户注册');
	$PAGE->set_pagelayout('incourseforstud');//设置layout
	echo $OUTPUT->header();//输出layout文件
// require_login();//要求登录
global $DB;
class simplehtml_form extends moodleform {

	//Add elements to form //初始化表单元素
	public function definition() {
		global $CFG;
		global $USER;

		//1.	输入界面（笔记标题和笔记内容）
		$mform = $this->_form; // Don't forget the underscore!
		 // Print the required moodle fields first.
        

        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
		
       
        $mform->setType('username', PARAM_RAW);

        $auths = core_component::get_plugin_list('auth');
        $enabled = get_string('pluginenabled', 'core_plugin');
        $disabled = get_string('plugindisabled', 'core_plugin');
        $authoptions = array($enabled => array(), $disabled => array());
        $cannotchangepass = array();
      
        

        $mform->addElement('passwordunmask', 'newpassword', '密码', 'size="20"');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->disabledIf('newpassword', 'createpassword', 'checked');

        $mform->disabledIf('newpassword', 'auth', 'in', $cannotchangepass);

        $mform->addElement('text', 'fistname', '姓名', 'size="20"');
	    $mform->addElement('text', 'policenum', '警官证号', 'size="20"');
		$mform->addElement('text', 'fistname', '组织架构', 'size="20"');
		$mform->addElement('text', 'phonenum', '电话号码', 'size="20"');
		$editoroptions = array('maxfiles' => 2, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true,'enable_filemanagement' =>false);
		$mform->addElement('editor','content', '个性签名:', null, $editoroptions);
		// $mform->addHelpButton('content', 'coursesummary');
		$mform->setType('content', PARAM_RAW);//Set type of element
		$summaryfields = 'content';
		$this->add_action_buttons(true, '创建账号');
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



echo '<h2>用户注册</h2>';

$mform = new simplehtml_form();

//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	 $mform->display();
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.    
    //插入数据
		echo '<h2>注册功能未开启</h2>';
} else {
  // 这里用于处理数据不符合要求或第一次显示表单

  //设置默认数据
  //$mform->set_data($toform);
  //显示表单
  $mform->display();
}


echo $OUTPUT->footer();
