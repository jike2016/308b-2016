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

if(empty($CFG))
{
//	require_once(dirname(dirname(__FILE__)).'/config.php');
	require("../config.php");
}
//require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");


global $DB;

$PAGE->set_url('/circlesoflearning/edit.php');
$PAGE->set_title('发表一个动态');
$PAGE->set_pagelayout('myblogeditdata');//设置layout文件

class blog_edit_form extends moodleform {
	//Add elements to form
	public function definition() {
		global $CFG;

		$mform = $this->_form; // Don't forget the underscore!

		$editoroptions = array( 'style'=>"height:100px;width: 100%;",'placeholder'=>'建议在200字以内......','maxlength'=>'250', 'wrap'=>'virtual', 'rows'=>10 ,'cols'=>10);
		$mform->addElement('textarea', 'summary_editor', '动态内容:', $editoroptions);
		$mform->setType('summary_editor', PARAM_RAW);                  //Set type of element
		$mform->addRule('summary_editor', null, 'required');
		
		$mform->addElement('html', '<img src="../theme/more/img/emotion.png" class="emotion" style="width:25px;height:25px;cursor:pointer;margin-left:86px"><!-- end  2016.3.25 毛英东 添加表情 -->');
		
		$filemanageroptions = array('maxbytes' => '5242880','subdirs'=>0,'maxfiles'=>4,'accepted_types'=>'web_image');
		$mform->addElement('filemanager', 'imagefile', '上传图片:', '', $filemanageroptions);

//		$filemanageroptions = array('maxbytes' => '10000000','subdirs'=>0,'maxfiles'=>4,'accepted_types'=>'web_image');
//		$mform->addElement('filemanager', 'imagefile', '图片:', '', $filemanageroptions);

		$publishstates = array();
		$i = 0;

//		foreach (blog_entry::get_applicable_publish_states() as $state => $desc) {
		$publishstates['publish'] = '公开';   // No maximum was set.
		$publishstates['private'] = '仅自己可见';
//			$i++;
//		}
		$mform->addElement('select', 'publishstate', get_string('publishto', 'blog'), $publishstates);

		$this->add_action_buttons(true, '发送');
	}
	//Custom validation should be added here
	function validation($data, $files) {

	}
}

echo $OUTPUT->header();


$mform = new blog_edit_form($currenturl);
//处理流程如下
if ($mform->is_cancelled()) {
	//按了取消按钮
	redirect(new moodle_url('/circlesoflearning/index.php'));
} else if ($fromform = $mform->get_data()) {
	//数据处理流程$mform->get_data() 返回所有提交的数据.

//	$filemanageroptions = array(
//		'maxbytes'       => '0',
//		'subdirs'        => 0,
//		'maxfiles'       => 4,
//		'accepted_types' => 'web_image');

	$context = context_user::instance($USER->id, MUST_EXIST);

	/** Start 判断是否有上传图片 朱子武 20160328*/
	if($fromform->imagefile)
	{
		//	file_save_draft_area_files($fromform->imagefile, $context->id, 'blog', 'post',
//			$fromform->imagefile, $filemanageroptions);

		/** Start 获取图片地址 朱子武 20160328*/
		$fs = get_file_storage();
		if ($imagefiles = $fs->get_area_files($context->id, 'user', 'draft',$fromform->imagefile))
		{
			$image_Path_my = array();
			foreach($imagefiles as $imageValue)
			{
				$filename = $imageValue->get_filename();
				if($filename==='.')
				{
					continue;
				}
				else
				{
//					$component = $imageValue->get_component();
//					$filearea = $imageValue->get_filearea();
					$component = 'user';
					$filearea = 'draft';
					$contextid = $imageValue->get_contextid();
					$filename = $imageValue->get_filename();
					$filepath = $imageValue->get_filepath();
					$itemid = $imageValue->get_itemid();
					$image_Path_my[] = $CFG->wwwroot.$filepath.'draftfile.php'.$filepath.$contextid.$filepath.$component.$filepath.$filearea.$filepath.$itemid.$filepath.$filename;
				}
			}
			$my_result->pictures = str_replace("\\/", "/", json_encode($image_Path_my));
		}
		/** Start 获取图片地址 朱子武 20160328*/

		/** Start 修改缓冲区数据库，防止被系统删除图片 朱子武 20160328*/

		$file_result = $DB->get_records_sql('SELECT * FROM mdl_files WHERE itemid = '.$fromform->imagefile);
		if(count($file_result))
		{
			foreach($file_result as $file_value)
			{
				$file_value->component = 'blog';
				$file_value->filearea = 'image';

				$DB->update_record('files',$file_value,true);
			}
		}

		/** End 修改缓冲区数据库，防止被系统删除图片 朱子武 20160328*/
	}

	//修改数据

// 获取微博内容
	$my_result->summary = $fromform->summary_editor;

//	匹配用户上传的图片和文字
//	$blog_content = getimgsAndContents($fromform->summary_editor['text']);
	// 获取微博内容
//	$my_result->summary = $blog_content->contents;
//	获取图片，如果有
//	if($fromform->imagefile)
//	{
//		$my_result->pictures = $fromform->imagefile;
//	}
	$my_result->format = '1';
	$my_result->subject = '0';
	$my_result->module = 'blog';
//	获得微博作者
	$my_result->userid = $USER->id;
	$my_result->courseid = '0';
	$my_result->groupid = '0';
	$my_result->moduleid = '0';
	$my_result->coursemoduleid = '0';

	$my_result->rating = '0';
	$my_result->summaryformat = '1';

	$my_result->publishstate = $fromform->publishstate;
	// 获取是否是被修改
	$my_result->created = time();
	$my_result->lastmodified = $my_result->created;

	$DB->insert_record('circles_of_learning', $my_result, true);
	$DB->insert_record('post', $my_result, true);

	redirect(new moodle_url('/circlesoflearning/index.php'));
} else {
	// 这里用于处理数据不符合要求或第一次显示表单

	//设置默认数据
	$mform->set_data();
	//显示表单
	$mform->display();
}


echo $OUTPUT->footer();

/**
 * 提取字符串中图片url地址
 * @param type $str
 * @return type
 */
function getimgsAndContents($str) {
	$contents = strip_tags($str);
	$pattern = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";
	preg_match_all($pattern,$str,$images);

	$blog_content = new stdClass();
	$blog_content->contents = $contents;
	if(count($images))
	{
//		$blog_content->pictures = json_encode($images[1]);
		$blog_content->pictures = str_replace("\\/", "/", json_encode($images[1]));
	}
	return $blog_content;
}
