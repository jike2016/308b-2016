<?php
/**
 *控制注册功能的全局开关
 * 叶靖华
 * 20160331
 */
require(dirname(__FILE__).'/../config.php');
require_once("$CFG->libdir/formslib.php");

require_login();//要求登录
global $USER;
if($USER->id!=2){
	redirect(new moodle_url('/../moodle/index.php'));
}
$PAGE->set_url('/register/register_switch.php');
$PAGE->set_title('注册功能开关');
$PAGE->set_heading('注册功能开关');
$PAGE->set_pagelayout('registerforadmin');//设置layout

global $DB;
class simplehtml_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        global $DB;
        $register_switchdata = $DB->get_record('register_switch', array('id' => '1'))->register_switch;
        $mform = $this->_form; // Don't forget the underscore!
        if ($register_switchdata==1) {
            $mform->addElement('select', 'register_switch', '开关选择', array('1' => '开', '2' => '关'));
        }elseif($register_switchdata==2){
            $mform->addElement('select', 'register_switch', '开关选择', array('2' => '关', '1' => '开'));
        }

        $this->add_action_buttons(true, '保存更改');
    }
}
$currenturl = new moodle_url('/register/register_switch.php');
echo $OUTPUT->header();

echo '<h2>注册功能开关</h2>';
$mform = new simplehtml_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
    //按了取消按钮
    redirect(new moodle_url('/my/'));
} else if ($fromform = $mform->get_data()) {
    //数据处理流程$mform->get_data() 返回所有提交的数据.
    //修改数据
    $register_switch = new stdClass();
    $register_switch->id = 1;
    $register_switch->register_switch = $fromform->register_switch;
    $DB->update_record_raw('register_switch', $register_switch);
    redirect(new moodle_url('/register/register_switch.php'));
} else {
    // 这里用于处理数据不符合要求或第一次显示表单
    //设置默认数据
    //$mform->set_data();
    //显示表单
    $mform->display();
}

echo $OUTPUT->footer();
