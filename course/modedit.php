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
* Adds or updates modules in a course using new formslib
*
* @package    moodlecore
* @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

require_once("../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->libdir.'/plagiarismlib.php');
require_once($CFG->dirroot . '/course/modlib.php');

$add    = optional_param('add', '', PARAM_ALPHA);     // module name
$update = optional_param('update', 0, PARAM_INT);
$return = optional_param('return', 0, PARAM_BOOL);    //return to course/view.php if false or mod/modname/view.php if true
$type   = optional_param('type', '', PARAM_ALPHANUM); //TODO: hopefully will be removed in 2.0
$sectionreturn = optional_param('sr', null, PARAM_INT);

$url = new moodle_url('/course/modedit.php');
$url->param('sr', $sectionreturn);
if (!empty($return)) {
    $url->param('return', $return);
}

if (!empty($add)) {
    $section = required_param('section', PARAM_INT);
    $course  = required_param('course', PARAM_INT);

    $url->param('add', $add);
    $url->param('section', $section);
    $url->param('course', $course);
    $PAGE->set_url($url);

    $course = $DB->get_record('course', array('id'=>$course), '*', MUST_EXIST);
    require_login($course);

    /*Start 增加推送考试提醒功能 2016.02.27 毛英东  */
    if(isset($_POST['pushquizwarning']) && $_POST['pushquizwarning'] == '1' ){   //勾选了推送考试
        //查询接收信息的用户
        $users_i = $DB->get_records_sql("select c.userid from mdl_course a join mdl_enrol b on b.courseid=a.id join mdl_user_enrolments c on c.enrolid=b.id where a.id=".$course->id);
        foreach($users_i as $user_i){
            $bulk_users[] = $user_i->userid;
        }
        $msg = '《'.$course->fullname .'》课程考试('.$_POST['name'].')';
//        如果设置了考试开始时间
        if(isset($_POST['timeopen']['enabled']) && $_POST['timeopen']['enabled'] == '1'){
            $msg .= '于'.intval($_POST['timeopen']['year']).'年'.intval($_POST['timeopen']['month']).'月'.intval($_POST['timeopen']['day']).'日'.intval($_POST['timeopen']['hour']).'时'.intval($_POST['timeopen']['minute']).'分开放，';
        }
//        如果设置了考试结束时间
        if(isset($_POST['timeclose']['enabled']) && $_POST['timeclose']['enabled'] == '1'){
            $msg .= '于'.intval($_POST['timeclose']['year']).'年'.intval($_POST['timeclose']['month']).'月'.intval($_POST['timeclose']['day']).'日'.intval($_POST['timeclose']['hour']).'时'.intval($_POST['timeclose']['minute']).'分截止，';
        }
//        没有设置考试开始时间和结束时间
        if(!(isset($_POST['timeopen']['enabled']) && $_POST['timeopen']['enabled'] == '1') && !(isset($_POST['timeclose']['enabled']) && $_POST['timeclose']['enabled'] == '1')){
            $msg .= '已添加，';
        }
        $msg .= ' 祝大家取得好成绩。<br /><a href="../course/view.php?id='.$course->id .'">点击查看考试详情</a><br />';
        list($in, $params) = $DB->get_in_or_equal($bulk_users);
        $rs = $DB->get_recordset_select('user', "id $in", $params);
        foreach ($rs as $user) {
            //TODO we should probably support all text formats here or only FORMAT_MOODLE
            //For now bulk messaging is still using the html editor and its supplying html
            //so we have to use html format for it to be displayed correctly
            message_post_message($USER, $user, $msg, FORMAT_HTML);  //循环发送消息
        }
        $rs->close();
    }
    /*End*/

    // There is no page for this in the navigation. The closest we'll have is the course section.
    // If the course section isn't displayed on the navigation this will fall back to the course which
    // will be the closest match we have.
    navigation_node::override_active_url(course_get_url($course, $section));

    list($module, $context, $cw) = can_add_moduleinfo($course, $add, $section);

    $cm = null;

    $data = new stdClass();
    $data->section          = $section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible          = $cw->visible;
    $data->course           = $course->id;
    $data->module           = $module->id;
    $data->modulename       = $module->name;
    $data->groupmode        = $course->groupmode;
    $data->groupingid       = $course->defaultgroupingid;
    $data->id               = '';
    $data->instance         = '';
    $data->coursemodule     = '';
    $data->add              = $add;
    $data->return           = 0; //must be false if this is an add, go back to course view on cancel
    $data->sr               = $sectionreturn;

    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');
        file_prepare_draft_area($draftid_editor, null, null, null, null, array('subdirs'=>true));
        $data->introeditor = array('text'=>'', 'format'=>FORMAT_HTML, 'itemid'=>$draftid_editor); // TODO: add better default
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $context)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');

        $data->_advancedgradingdata['methods'] = grading_manager::available_methods();
        $areas = grading_manager::available_areas('mod_'.$module->name);

        foreach ($areas as $areaname => $areatitle) {
            $data->_advancedgradingdata['areas'][$areaname] = array(
                'title'  => $areatitle,
                'method' => '',
            );
            $formfield = 'advancedgradingmethod_'.$areaname;
            $data->{$formfield} = '';
        }
    }

    if (!empty($type)) { //TODO: hopefully will be removed in 2.0
        $data->type = $type;
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);

    if ($data->section && $course->format != 'site') {
        $heading = new stdClass();
        $heading->what = $fullmodulename;
        $heading->to   = $sectionname;
        $pageheading = get_string('addinganewto', 'moodle', $heading);
    } else {
        $pageheading = get_string('addinganew', 'moodle', $fullmodulename);
    }
    $navbaraddition = $pageheading;

} else if (!empty($update)) {

    $url->param('update', $update);
    $PAGE->set_url($url);

    // Select the "Edit settings" from navigation.
    navigation_node::override_active_url(new moodle_url('/course/modedit.php', array('update'=>$update, 'return'=>1)));

    // Check the course module exists.
    $cm = get_coursemodule_from_id('', $update, 0, false, MUST_EXIST);

    // Check the course exists.
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

    // require_login
    require_login($course, false, $cm); // needed to setup proper $COURSE

    list($cm, $context, $module, $data, $cw) = can_update_moduleinfo($cm);

    $data->coursemodule       = $cm->id;
    $data->section            = $cw->section;  // The section number itself - relative!!! (section column in course_sections)
    $data->visible            = $cm->visible; //??  $cw->visible ? $cm->visible : 0; // section hiding overrides
    $data->cmidnumber         = $cm->idnumber;          // The cm IDnumber
    $data->groupmode          = groups_get_activity_groupmode($cm); // locked later if forced
    $data->groupingid         = $cm->groupingid;
    $data->course             = $course->id;
    $data->module             = $module->id;
    $data->modulename         = $module->name;
    $data->instance           = $cm->instance;
    $data->return             = $return;
    $data->sr                 = $sectionreturn;
    $data->update             = $update;
    $data->completion         = $cm->completion;
    $data->completionview     = $cm->completionview;
    $data->completionexpected = $cm->completionexpected;
    $data->completionusegrade = is_null($cm->completiongradeitemnumber) ? 0 : 1;
    $data->showdescription    = $cm->showdescription;
    if (!empty($CFG->enableavailability)) {
        $data->availabilityconditionsjson = $cm->availability;
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_MOD_INTRO, true)) {
        $draftid_editor = file_get_submitted_draft_itemid('introeditor');
        $currentintro = file_prepare_draft_area($draftid_editor, $context->id, 'mod_'.$data->modulename, 'intro', 0, array('subdirs'=>true), $data->intro);
        $data->introeditor = array('text'=>$currentintro, 'format'=>$data->introformat, 'itemid'=>$draftid_editor);
    }

    if (plugin_supports('mod', $data->modulename, FEATURE_ADVANCED_GRADING, false)
            and has_capability('moodle/grade:managegradingforms', $context)) {
        require_once($CFG->dirroot.'/grade/grading/lib.php');
        $gradingman = get_grading_manager($context, 'mod_'.$data->modulename);
        $data->_advancedgradingdata['methods'] = $gradingman->get_available_methods();
        $areas = $gradingman->get_available_areas();

        foreach ($areas as $areaname => $areatitle) {
            $gradingman->set_area($areaname);
            $method = $gradingman->get_active_method();
            $data->_advancedgradingdata['areas'][$areaname] = array(
                'title'  => $areatitle,
                'method' => $method,
            );
            $formfield = 'advancedgradingmethod_'.$areaname;
            $data->{$formfield} = $method;
        }
    }

    if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$data->modulename,
                                             'iteminstance'=>$data->instance, 'courseid'=>$course->id))) {
        // add existing outcomes
        foreach ($items as $item) {
            if (!empty($item->gradepass)) {
                $decimalpoints = $item->get_decimals();
                $data->gradepass = format_float($item->gradepass, $decimalpoints);
            }
            if (!empty($item->outcomeid)) {
                $data->{'outcome_'.$item->outcomeid} = 1;
            }
        }

        // set category if present
        $gradecat = false;
        foreach ($items as $item) {
            if ($gradecat === false) {
                $gradecat = $item->categoryid;
                continue;
            }
            if ($gradecat != $item->categoryid) {
                //mixed categories
                $gradecat = false;
                break;
            }
        }
        if ($gradecat !== false) {
            // do not set if mixed categories present
            $data->gradecat = $gradecat;
        }
    }

    $sectionname = get_section_name($course, $cw);
    $fullmodulename = get_string('modulename', $module->name);

    if ($data->section && $course->format != 'site') {
        $heading = new stdClass();
        $heading->what = $fullmodulename;
        $heading->in   = $sectionname;
        $pageheading = get_string('updatingain', 'moodle', $heading);
    } else {
        $pageheading = get_string('updatinga', 'moodle', $fullmodulename);
    }
    $navbaraddition = null;

} else {
    require_login();
    print_error('invalidaction');
}

$pagepath = 'mod-' . $module->name . '-';
if (!empty($type)) { //TODO: hopefully will be removed in 2.0
    $pagepath .= $type;
} else {
    $pagepath .= 'mod';
}
$PAGE->set_pagetype($pagepath);
//$PAGE->set_pagelayout('admin');
/** Start 替换系统原来的layout 20160411 */
$PAGE->set_pagelayout('openmettingmy');//设置layout文件
/** End 替换系统原来的layout 20160411 */

$modmoodleform = "$CFG->dirroot/mod/$module->name/mod_form.php";
if (file_exists($modmoodleform)) {
    require_once($modmoodleform);
} else {
    print_error('noformdesc');
}

$mformclassname = 'mod_'.$module->name.'_mod_form';
$mform = new $mformclassname($data, $cw->section, $cm, $course);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    if ($return && !empty($cm->id)) {
        redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$cm->id");
    } else {
        redirect(course_get_url($course, $cw->section, array('sr' => $sectionreturn)));
    }
} else if ($fromform = $mform->get_data()) {
    // Convert the grade pass value - we may be using a language which uses commas,
    // rather than decimal points, in numbers. These need to be converted so that
    // they can be added to the DB.
    if (isset($fromform->gradepass)) {
        $fromform->gradepass = unformat_float($fromform->gradepass);
    }

    if (!empty($fromform->update)) {
        if($fromform->swfid)
        {
            /**START CX 20160820增加pdf路径*/
//            $fromform->swfurl = convert_file_swf($fromform->swfid);
            $urlarray = convert_file_swf($fromform->swfid);
            $fromform->swfurl = $urlarray[0];
            $fromform->pdfurl=$urlarray[1];
            /**End*/
        }
        list($cm, $fromform) = update_moduleinfo($cm, $fromform, $course, $mform);
    } else if (!empty($fromform->add)) {
        /** START 在这里做处理上传的文件转为swf类型操作 朱子武 */
        if($fromform->swfid)
        {

            /**START CX 20160820增加pdf路径*/
//            $fromform->swfurl = convert_file_swf($fromform->swfid);
            $urlarray = convert_file_swf($fromform->swfid);
            $fromform->swfurl = $urlarray[0];
            $fromform->pdfurl=$urlarray[1];
            /**End*/

            $fromform = add_moduleinfo($fromform, $course, $mform);
        }
        else
        {
            $fromform = add_moduleinfo($fromform, $course, $mform);
        }
    } else {
        print_error('invaliddata');
    }

    if (isset($fromform->submitbutton)) {
        if (empty($fromform->showgradingmanagement)) {
            redirect("$CFG->wwwroot/mod/$module->name/view.php?id=$fromform->coursemodule");
        } else {
            $returnurl = new moodle_url("/mod/$module->name/view.php", array('id' => $fromform->coursemodule));
            redirect($fromform->gradingman->get_management_url($returnurl));
        }
    } else {
        redirect(course_get_url($course, $cw->section, array('sr' => $sectionreturn)));
    }
    exit;

} else {

    $streditinga = get_string('editinga', 'moodle', $fullmodulename);
    $strmodulenameplural = get_string('modulenameplural', $module->name);

    if (!empty($cm->id)) {
        $context = context_module::instance($cm->id);
    } else {
        $context = context_course::instance($course->id);
    }

    $PAGE->set_heading($course->fullname);
    $PAGE->set_title($streditinga);
    $PAGE->set_cacheable(false);

    if (isset($navbaraddition)) {
        $PAGE->navbar->add($navbaraddition);
    }

    echo $OUTPUT->header();

    if (get_string_manager()->string_exists('modulename_help', $module->name)) {
        echo $OUTPUT->heading_with_help($pageheading, 'modulename', $module->name, 'icon');
    } else {
        echo $OUTPUT->heading_with_help($pageheading, '', $module->name, 'icon');
    }

    $mform->display();

    echo $OUTPUT->footer();
}

/** START 返回值增加了pdf路径，为app服务 cx 20160820*/
/** START 转换文件格式 朱子武 20160510*/
function convert_file_swf($swfid)
{
    $file_path_my = get_document_file_url($swfid);
    $file_type = pathinfo($file_path_my->filename, PATHINFO_EXTENSION);
    $houzhui = substr(strrchr($file_path_my->filename, '.'), 1);
    $filename = rand(1000,9999).time().rand(1000,9999);
    $documentroot = $_SERVER['DOCUMENT_ROOT'];   // 文档的服务器绝对路径
    $httproot = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];  // 文档的服务器远程路径
    if(in_array($file_type, array('doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx','pdf')))
    {
        $swf_filepath = $documentroot.'/document_doc_swf/swf/'.$filename.'.swf';
        $pdf_filepath = $documentroot.'/document_doc_swf/pdf/'.$filename.'.pdf';
        if(!word2swf($file_path_my->filepath, $pdf_filepath, $swf_filepath))
        {
//          转换出错，提示用户
            print_error('cannotreadfile', 'error', '', $filename.'.'.$houzhui);
        }
    }
//    elseif(in_array($file_type, array('pdf')))
//    {
//        $swf_filepath = $documentroot.'/document_doc_swf/swf/'.$filename.'.swf';
//        if(!pdf2swf($file_path_my->filepath, $swf_filepath))
//        {
////                转换出错，提示用户
//            print_error('cannotreadfile', 'error', '', $filename.'.'.$houzhui);
//        }
//    }
    elseif(in_array($file_type, array('txt'))){
        $swf_filepath = $documentroot.'/document_doc_swf/swf/'.$filename.'.swf';
        $pdf_filepath = $documentroot.'/document_doc_swf/pdf/'.$filename.'.pdf';
        $txt_outputpath = $documentroot.'/document_doc_swf/txt/'.$filename.'.txt';
        if(!txt2swf($file_path_my->filepath,$txt_outputpath, $pdf_filepath, $swf_filepath))
        {
//                转换出错，提示用户
            print_error('cannotreadfile', 'error', '', $filename.'.'.$houzhui.'.'.$file_path_my->filepath.'.'.$txt_outputpath);
        }
    }
    $swf_filepath = str_replace($documentroot, $httproot, $swf_filepath);
    $pdf_filepath = str_replace($documentroot, $httproot, $pdf_filepath);
    return array($swf_filepath,$pdf_filepath);
    //return $swf_filepath;
}
/** END 转换文件格式 朱子武 20160510*/

/** START 获取上传的文件路径对象  朱子武  20160507 */
function get_document_file_url($itemid)
{
    global $USER, $CFG;
    $context = context_user::instance($USER->id, MUST_EXIST);
    $fs = get_file_storage();
    if ($documentfiles = $fs->get_area_files($context->id, 'user', 'draft', $itemid))
    {
        $fileclass = new stdClass();
        foreach($documentfiles as $documentValue)
        {
            $filename = $documentValue->get_filename();
            if($filename==='.')
            {
                continue;
            }
            else
            {
                $contenthash = $documentValue->get_contenthash();
                $filepath = $documentValue->get_filepath();
                $fileclass->filepath = $CFG->dataroot.$filepath.'filedir'.$filepath.substr($contenthash, 0, 2).$filepath.substr($contenthash, 2, 2).$filepath.$contenthash;
                $fileclass->filename = $documentValue->get_filename();
            }
        }
        return $fileclass;
    }
}
/** END 获取上传的文件路径  朱子武  20160507 */

/** START 转换文件  毛英东  20160508 */
function word2pdf($source_file, $output_file){
    $result =  `unoconv --format pdf --output "$output_file" "$source_file" 2>&1`;
    if(file_exists($output_file) && filesize($output_file) > 0){
        return true;
    }else{
        exit($result);
        return false;
    }
}

function pdf2swf($source_file, $output_file){
    $command = "/usr/swftools/bin/pdf2swf -f -T 9 -s languagedir=/usr/local/swftools-2013-04-09-1007/xpdf-chinese-simplified $source_file -o $output_file";
    $result = `$command`;
    if(file_exists($output_file) && filesize($output_file) > 0){
        return true;
    }else{
        echo $result;
        exit();
        return false;
    }

}

function word2swf($word_filepath, $pdf_filepath, $swf_filepath){
    if(word2pdf($word_filepath, $pdf_filepath)){
        if(pdf2swf($pdf_filepath, $swf_filepath)){
            return true;
        }
    }
    return false;

}

function txt2swf($txt_filepath,$txt_ouputpath, $pdf_filepath, $swf_filepath){
    if(txt_transcoding($txt_filepath,$txt_ouputpath)){
        if(word2pdf($txt_ouputpath, $pdf_filepath)){
            if(pdf2swf($pdf_filepath, $swf_filepath)){
                return true;
            }
        }
    }
    return false;
}

function txt_transcoding($txt_filepath,$txt_ouputpath){
    $result=`iconv -f GBK -t UTF-8 $txt_filepath -o $txt_ouputpath`;
    if(file_exists($txt_ouputpath) && filesize($txt_ouputpath) > 0) {
        return true;
    }
    return false;
}
/** END 转换文件  毛英东  20160508 */
