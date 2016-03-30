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
 * Page module version information
 *
 * @package mod_page
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/page/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // Page instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

/** Start 设置的评论page 朱子武 20160315*/
$page      = optional_param('page', 1, PARAM_INT);
$_SESSION['pageid'] = $page;
/** End 设置的评论page 朱子武 20160315*/

if ($p) {
    if (!$page = $DB->get_record('page', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('page', $page->id, $page->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('page', $id)) {
        print_error('invalidcoursemodule');
    }
    $page = $DB->get_record('page', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/page:view', $context);
/** START 岑霄 为学生设置Layout */
if(!has_capability('mod/page:addinstance', $context)){
    $PAGE->set_pagelayout('incourseforstudent');
    //默认为incourse管理员可见
}
/** End  */
// Trigger module viewed event.
$event = \mod_page\event\course_module_viewed::create(array(
   'objectid' => $page->id,
   'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('page', $page);
$event->trigger();

// Update 'viewed' state if required by completion system
require_once($CFG->libdir . '/completionlib.php');
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/page/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? array() : unserialize($page->displayoptions);

if ($inpopup and $page->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$page->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname.': '.$page->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($page);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($page->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($page->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo format_module_intro('page', $page, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($page->content, 'pluginfile.php', $context->id, 'mod_page', 'content', $page->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $page->contentformat, $formatoptions);
echo $OUTPUT->box($content, "generalbox center clearfix");

$strlastmodified = get_string("lastmodified");
echo "<div class=\"modified\">$strlastmodified: ".userdate($page->timemodified)."</div>";

/** Start 获取设置的评论page 朱子武 20160315*/
$count_page = my_get_article_evaluation_count($id);
$current_page = $_SESSION['pageid'];
unset ($_SESSION['pageid']);

echo '<!--评论-->
                    <div class="commentbox">
                        <div class="commentboxtitle">
                            <div><h3>评论</h3></div>
                        </div>
                        <div class="mycomment">
                                <textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                                <button id="commentBtn" class="btn btn-success">发表评论</button>
                            </div>
                            '.my_get_article_evaluation($id, $current_page - 1).'
                    </div>
                    		<!--分页按钮-->
			<div class="paginationbox">
				<ul class="pagination">
					<li>
						<a href="../../mod/page/view.php?id='.$id.'&page=1">首页</a>
					</li>
					<li>
						<a href="../../mod/page/view.php?id='.$id.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
					</li>
					'.my_get_article_evaluation_current_count($count_page, $id).'
					<li>
						<a href="../../mod/page/view.php?id='.$id.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>
					</li>
					<li>
						<a href="../../mod/page/view.php?id='.$id.'&page='.$count_page.'">尾页</a>
					</li>
				</ul>
			</div>
			<!--分页按钮 end-->
                    <!--评论 end-->
                    ';

echo $OUTPUT->footer();

/* START    获取评价数目页数 朱子武 20160315*/
function my_get_article_evaluation_count($articleid)
{
    global $DB;
    $evaluation = $DB->get_records_sql('SELECT id as mycount FROM mdl_comment_article_my WHERE articleid = ? ', array($articleid));
    //$evaluation = $DB->get_records_sql('SELECT courseid, count(*) as mycount FROM mdl_comment_course_my WHERE courseid = ? ', array($course->id));
    //$mycount = $evaluation[$course->id]->mycount;
    $mycount = count($evaluation);
    $mycount = ceil($mycount/10);
    return ($mycount <= 1 ? 1: $mycount);
}
/** 获取评价数目页数 END*/

/** START 输出页码 朱子武 20160315*/
function my_get_article_evaluation_current_count($count_page, $articleid)
{
    $pagestr = '';
    for($num = 1; $num <= $count_page; $num ++)
    {
        $pagestr.='<li><a href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'">'.$num.'</a></li>';
    }
    return $pagestr;
}
/** 输出页码 END*/

/** START 获取课程评价 朱子武 20160315*/
function my_get_article_evaluation($articleid, $current_page)
{

    $my_page = $current_page * 10;
    global $DB;
    global $OUTPUT;
    $evaluation = $DB->get_records_sql('SELECT a.id, a.userid, a.comment, b.firstname, b.lastname, a.commenttime FROM mdl_comment_article_my a JOIN mdl_user b ON a.userid = b.id WHERE articleid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($articleid));

    $evaluationStr = '';
    foreach($evaluation as $value)
    {
        $userobject = new stdClass();
        $userobject->metadata = array();
        $user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
        $userobject->metadata['useravatar'] = $OUTPUT->user_picture (
            $user,
            array(
                'link' => false,
                'visibletoscreenreaders' => false
            )
        );

        $userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
        $evaluationStr.= '
                 <!--评论内容-->
                 <div class="comment container">
                        <div class="comment-l">
                            <div class="Learnerimg-box">
                                <!--<img src="<?php echo $CFG->wwwroot;?>/theme/more/pix/lessonvideo/learnner.jpg" alt="依米暖暖">-->
                                '.$userobject->metadata['useravatar'].'
                            </div>
                        </div>
                        <div class="comment-r">
                            <p class="name">'.$value->lastname.$value->firstname.'</p>
                            <p class="commentinfo">
                                '.$value->comment.'
                            </p>
                            <p class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</p>
                        </div>
                 </div>
                        <!--评论内容1 end-->
        ';
    }
    return $evaluationStr;
}
/** 获取课程评价 END*/
