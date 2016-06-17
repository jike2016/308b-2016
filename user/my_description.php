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

require_once('../config.php');
require_once("$CFG->libdir/formslib.php");
require_once('lib.php');

$PAGE->set_url('/user/my_description.php');
$PAGE->set_title('个人资料');
$PAGE->set_heading('个人资料');
$PAGE->set_pagelayout('mypersonaldata');//设置layout文件

class simplehtml_form extends moodleform {

    //Add elements to form //初始化表单元素
    public function definition() {
        global $DB;
        global $CFG;
        global $USER;

        //1.	输入界面（台账任务的内容）
        $mform = $this->_form; // Don't forget the underscore!
//        $editoroptions = $this->_customdata['editoroptions'];//控制编辑框的参数
//        $filemanageroptions = $this->_customdata['filemanageroptions'];//控制编辑框的参数
		$mform->addElement('html', '<div class="mypersondiv">');
        $mform->addElement('static', 'username', '姓名:', 'size="20"');

        $mform->setType('username', PARAM_RAW);
        $mform->disabledIf('username', '', '');

        $mform->addElement('checkbox', 'createpassword', '修改密码:');//修改密码开关
        $mform->addElement('passwordunmask', 'oldpassword', '原密码:', 'size="20"');
        $mform->setType('oldpassword', PARAM_RAW);
        $mform->disabledIf('oldpassword', 'createpassword');
        $mform->addElement('passwordunmask', 'newpassword', '新密码:', 'size="20"');
        $mform->setType('newpassword', PARAM_RAW);
        $mform->disabledIf('newpassword', 'createpassword');
        $mform->addElement('passwordunmask', 'verifynewpassword', '确认密码:', 'size="20"');
        $mform->setType('verifynewpassword', PARAM_RAW);
        $mform->disabledIf('verifynewpassword', 'createpassword');

        $mform->addElement('text', 'phone1', '电话:', 'maxlength="20" size="25"');
        $mform->setType('phone1', PARAM_NOTAGS);

        $mform->addElement('static', 'unit_name', '单位:'); // Add elements to your form
        $mform->setType('unit_name', PARAM_FILE);                  //Set type of element
        $mform->addRule('unit_name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        $mform->disabledIf('unit_name', '', '');

//        $editoroptions = array( 'height' =>10, 'width'=>20, 'maxfiles' => EDITOR_UNLIMITED_FILES, 'maxbytes'=>$CFG->maxbytes, 'trusttext'=>false, 'noclean'=>true);
//        $mform->addElement('editor', 'description_editor',  '自述:', null, $editoroptions);
//        $mform->setType('description_editor', PARAM_CLEANHTML);
        $editoroptions = array( 'style'=>"height:100px;width: 500px;",'placeholder'=>'建议在200字以内......','maxlength'=>'300', 'wrap'=>'virtual', 'rows'=>10 ,'cols'=>10);
        $mform->addElement('textarea', 'description_editor', '自述:', $editoroptions);

        //头像组合
        if (empty($USER->newadminuser)) {

            if (!empty($CFG->enablegravatar)) {
                $mform->addElement('html', html_writer::tag('p', get_string('gravatarenabled')));
            }

            $mform->addElement('static', 'currentpicture', '当前头像:');

            $filemanageroptions = array( 'maxbytes' => '5242880','subdirs'=>0,'maxfiles'=>1,'accepted_types'=>'web_image');
            $mform->addElement('filemanager', 'imagefile', '上传新头像:', '', $filemanageroptions);

        }
        $this->add_action_buttons(true, '保存修改');
    }

    //Custom validation should be added here	在这里添加自定义验证
    function validation($data, $files) {
        //验证标签名是否重复
        global  $DB;
        global  $USER;

        //如果开启了密码修改按钮
        if($data['createpassword'] == 1){
            $oldpassword = $data['oldpassword'];//输入的验证密码
            $errorcode = 0;
            $user = authenticate_user_login($USER->username, $oldpassword, false, $errorcode);//用验证密码登录检验

            /** Start 叶靖华 20160326 */
            if ($data['newpassword']!=$data['verifynewpassword']){
                $errors['verifynewpassword']='密码不一致！';
            }
            if ($user==false){
                $errors['oldpassword'] = '原密码错误！';
            }
        }
        if(strlen($data['description_editor']) > 250){
            $errors['description_editor'] = '抱歉，建议在200字左右！';
        }

        return $errors;
        /*
        $password2 = $DB->get_record_sql("select u.id,u.password from mdl_user u where u.id = $USER->id");//原密码
        if($user->password != $password2->password){
            $errors['oldpassword'] = '原密码错误！';
        }
        return $errors;
        */
        /**End */
    }

    /**
     * Extend the form definition after data has been parsed.
     */
    public function definition_after_data() {

        global $USER, $CFG, $DB, $OUTPUT;

        $mform = $this->_form;

        $user = $DB->get_record('user', array('id' => $USER->id));

        // Print picture.
        if (empty($USER->newadminuser)) {
            if ($user) {
                $context = context_user::instance($user->id, MUST_EXIST);
                $fs = get_file_storage();
                $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
                if (!empty($user->picture) && $hasuploadedpicture) {
                    $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
                } else {
                    $imagevalue = get_string('none');
                }
            } else {
                $imagevalue = get_string('none');
            }
            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($imagevalue);

            if ($user && $mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }
        }

    }

}


echo $OUTPUT->header();
//echo '<h2>个人资料</h2>';
global $DB;
global $USER;
require_login();
$mform = new simplehtml_form();

$filemanageroptions = array(
    'maxbytes'       => '0',
    'subdirs'        => 0,
    'maxfiles'       => 1,
    'accepted_types' => 'web_image');

//处理流程如下
if ($mform->is_cancelled()) {
    //按了取消按钮
    redirect(new moodle_url('/user/my_description.php'));
} else if ($fromform = $mform->get_data()) {

    $phone1 = $fromform->phone1;//电话
    if(!isset($fromform->newpassword)){//如果没有修改密码，则获取原来的密码
        $user = $DB->get_record_sql("select u.id,u.firstname,u.lastname,u.`password`,u.email,u.description,u.phone1 from mdl_user u where u.id = $USER->id");//获取用户信息
        $newpassword = $user->password;
    }else{//如果设置新密码，则对新密码加密
        $newpassword = hash_internal_user_password($fromform->newpassword);
    }
//    $description_editor = $fromform->description_editor['text'];//自述
    $description_editor = $fromform->description_editor;//自述
    $imagefile = $fromform->imagefile;//获取头像id

    $newform = new stdClass();
    $newform->id = $USER->id;
    $newform->imagefile = $imagefile;
    useredit_update_picture($newform, $mform, $filemanageroptions);//保存用户更改头像

    //更新数据
//    $result = $DB->update_record_raw("user", array('id'=>$USER->id,'phone1'=>$phone1, 'password'=>$newpassword, 'description'=>$description_editor, 'picture'=>$imagefile ));
    $result = $DB->update_record_raw("user", array('id'=>$USER->id,'phone1'=>$phone1, 'password'=>$newpassword, 'description'=>$description_editor));

    redirect(new moodle_url('/user/my_description.php'));

} else {
    // 这里用于处理数据不符合要求或第一次显示表单

    $user = $DB->get_record_sql("select u.id,u.firstname,u.lastname,u.`password`,u.email,u.description,u.phone1 from mdl_user u where u.id = $USER->id");//获取用户信息
    $userName = $user->lastname.$user->firstname;
    // $password = $user->password;
    $description = $user->description;
    $phone1 = $user->phone1;
    //获取单位
    //    $unitName = $DB->get_record_sql("select o.name from mdl_org o where o.id = ( select olu.org_id from mdl_org_link_user olu where olu.user_id = $USER->id )");
    $unit = $DB->get_record_sql("select o.name,o.parent from mdl_org o where o.id = ( select olu.org_id from mdl_org_link_user olu where olu.user_id = $USER->id )");
    $unitName = $unit->name;
    while($unit->parent != -1){
        $unit = $DB->get_record_sql("select o.name,o.parent from mdl_org o where o.id = $unit->parent");//获取上一级的单位信息
        $unitName = $unit->name.'-'.$unitName;
    }

    $personaldata  = new stdClass();
    $personaldata->username = $userName;
    $personaldata->phone1 = $phone1;
    $personaldata->unit_name = $unitName;
//    $personaldata->description_editor = array('text'=> $description);
    $personaldata->description_editor = $description;
//    $personaldata->currentpicture = array('text'=> $description);

    $mform->set_data($personaldata);
    $mform->display();
}

echo $OUTPUT->footer();


/** 保存更改的头像 */
function useredit_update_picture(stdClass $usernew, moodleform $userform, $filemanageroptions = array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/gdlib.php");

    $context = context_user::instance($usernew->id, MUST_EXIST);
    $user = $DB->get_record('user', array('id' => $usernew->id), 'id, picture', MUST_EXIST);

    $newpicture = $user->picture;
    // Get file_storage to process files.
    $fs = get_file_storage();
    if (!empty($usernew->deletepicture)) {
        // The user has chosen to delete the selected users picture.
        $fs->delete_area_files($context->id, 'user', 'icon'); // Drop all images in area.
        $newpicture = 0;

    } else {
        // Save newly uploaded file, this will avoid context mismatch for newly created users.
        file_save_draft_area_files($usernew->imagefile, $context->id, 'user', 'newicon', 0, $filemanageroptions);
        if (($iconfiles = $fs->get_area_files($context->id, 'user', 'newicon')) && count($iconfiles) == 2) {
            // Get file which was uploaded in draft area.
            foreach ($iconfiles as $file) {
                if (!$file->is_directory()) {
                    break;
                }
            }
            // Copy file to temporary location and the send it for processing icon.
            if ($iconfile = $file->copy_content_to_temp()) {
                // There is a new image that has been uploaded.
                // Process the new image and set the user to make use of it.
                // NOTE: Uploaded images always take over Gravatar.
                $newpicture = (int)process_new_icon($context, 'user', 'icon', 0, $iconfile);
                // Delete temporary file.
                @unlink($iconfile);
                // Remove uploaded file.
                $fs->delete_area_files($context->id, 'user', 'newicon');
            } else {
                // Something went wrong while creating temp file.
                // Remove uploaded file.
                $fs->delete_area_files($context->id, 'user', 'newicon');
                return false;
            }
        }
    }

    if ($newpicture != $user->picture) {
        $DB->set_field('user', 'picture', $newpicture, array('id' => $user->id));
        return true;
    } else {
        return false;
    }
}