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

/** START 岑霄 为学生设置 Layout */
if(!has_capability('mod/page:addinstance', $context)){
    $PAGE->set_pagelayout('pageforstud');
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

if($page->swfurl)
{
    /** Start 增加在线阅读office内容 岑霄 20160425 */
    echo '<div id="documentViewer" class="flexpaper_viewer" style="width:100%;height:900px;align-content:center" ></div>';
    /**End */
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
$evaluationCount = my_get_article_evaluation_count($id);
$count_page = $evaluationCount->ceilcount;

$current_page = $_SESSION['pageid'];
unset ($_SESSION['pageid']);

//输出文档的上下页按钮 start
echo '<div class="funtion-btn-box">
			'.my_printf_doc_jump($course,$id).'
	   </div>';
//输出文档的上下页按钮 end

echo '<button id="hiddencourseid" value="'.$course->id.'" style="display: none;"></button>
	  <button id="hiddencoursefullname" value="'.$page->name.'" style="display: none;"></button>
        <!--课程评论板块-->
		<div class="main-box">
			<div class="scoreinfo">
				<p class="title">评论（<span>'.$evaluationCount->count.'</span>）</p>
			</div>
			<div class="evaluation-list" >
				<div class="mycomment">
					<textarea class="form-control" id="comment-text" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                    <img src="../../theme/more/img/emotion.png" class="pull-left emotion" style="width:25px;height:25px;margin-top:4px;cursor:pointer">
					<button id="commentBtn" class="btn btn-primary">发表评论</button>
					<div class="division"></div>
				</div>
				<div class="division"></div>
				<!--评论内容-->
				 '.my_get_article_evaluation2($id, $current_page - 1).'
         		<!--评论内容 end-->

         		<!--分页-->
				<div class="paginationbox">
					<a href="../../mod/page/view.php?id='.$id.'&page=1" >首页</a>
		        	<a href="../../mod/page/view.php?id='.$id.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
		        	'.my_get_article_evaluation_current_count2($count_page, $id, $current_page).'
		        	<a href="../../mod/page/view.php?id='.$id.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>
		        	<a href="../../mod/page/view.php?id='.$id.'&page='.$count_page.'" >尾页</a>
				</div>
				<!--分页 end-->
			</div>
		</div>
		<!--课程评论板块 end-->';

echo $OUTPUT->footer();

/* START    获取评价数目页数 朱子武 20160315*/
function my_get_article_evaluation_count($articleid)
{
    global $DB;
    $evaluation = $DB->get_records_sql('SELECT id as mycount FROM mdl_comment_article_my WHERE articleid = ? ', array($articleid));
    //$evaluation = $DB->get_records_sql('SELECT courseid, count(*) as mycount FROM mdl_comment_course_my WHERE courseid = ? ', array($course->id));
    //$mycount = $evaluation[$course->id]->mycount;
//    $mycount = count($evaluation);
//    $mycount = ceil($mycount/10);
//    return ($mycount <= 1 ? 1: $mycount);

    $mycount = count($evaluation) < 0 ? 0 : count($evaluation);
    $evaluationCount = new stdClass();
    $evaluationCount->count = $mycount;
    $mycount = ceil($mycount/10);
    $evaluationCount->ceilcount = ($mycount <= 1 ? 1: $mycount);
    return $evaluationCount;
}
/** 获取评价数目页数 END*/

/** START 输出页码 朱子武 20160315*/
function my_get_article_evaluation_current_count($count_page, $articleid, $current_page)
{
    $pagestr = '';
    /** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
    $numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
    $numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
    /** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
    for($num = $numstart; $num <= $numend; $num ++)
    {
//          $pagestr.='<li><a href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'">'.$num.'</a></li>';
        if($num == $current_page)
        {
            $pagestr.='<li><a class="pagination_li_active" href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'">'.$num.'</a></li>';
        }
        else
        {
            $pagestr.='<li><a href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'">'.$num.'</a></li>';
        }
    }
    return $pagestr;
}
/** 输出页码 END*/

/** START 输出页码 */
function my_get_article_evaluation_current_count2($count_page, $articleid, $current_page)
{
    $pagestr = '';
    /** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
    $numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
    $numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
    /** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
    for($num = $numstart; $num <= $numend; $num ++)
    {
//          $pagestr.='<li><a href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'">'.$num.'</a></li>';
        if($num == $current_page)
        {
            $pagestr.='<a class="active" href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'" >'.$num.'</a>';
        }
        else
        {
            $pagestr.='<a href="../../mod/page/view.php?id='.$articleid.'&page='.$num.'" >'.$num.'</a>';
        }
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

/** START 获取课程评价 */
function my_get_article_evaluation2($articleid, $current_page)
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
        $evaluationStr .= '  <!--评论内容-->
                    <div class="evaluation">
             		<div class="evaluation-con">
                     	<a href="#" class="img-box">
                     		'.$userobject->metadata['useravatar'].'
                     	</a>
                     	<div class="content-box">
                        	<div class="user-info clearfix">
                            	<a href="#" class="username">'.$value->lastname.$value->firstname.'</a>
                         	</div>
                         	<!--user-info end-->
                     		<p class="content commentinfo "> '.$value->comment.'</p>
                         	<div class="info">
                         		<span class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</span>
                         	</div>
                     	</div>
              			<!--content end-->
              		</div>
					<!--evaluation-con end-->
         		</div><!--评论内容1 end-->';

    }
    return $evaluationStr;
}
/** 获取课程评价 END*/

/**start nlw20160809 课程文档阅读增加页面跳转链接/添加上下页跳转按钮 */
/**输出课程文档阅读增加页面跳转链接
 *
 * @author nlw
 * @param $course
 * @param  $id 当前活动的id
 * @return
 */
function my_printf_doc_jump($course,$id){

    $output = '';
    $modinfo = get_fast_modinfo($course);//获取课程的全部信息
    $cms = $modinfo->cms;
    $sections = $modinfo->sections;
    $count = count($sections[0]);//获取第一个section中activity数量
    $key_cms = array_keys($cms);//获取所有activity的id
    for ($i=$count;$i<count($key_cms);$i++){//从第二个section的第一个activity开始遍历
        if ($id == $key_cms[$i]){
            if ($i == $count){//第一个activity
                if (count($key_cms)> $count + 1) {//如果本课程只有一个activity
                    $url_next = return_doc_jump($modinfo, $key_cms[$i + 1]);
                    $output = '<a href="' . $url_next . '" class="next-page" >下一页</a>';
                }
            }elseif ($i == (count($key_cms)-1)){//最后一个activity
                $url_previous = return_doc_jump($modinfo,$key_cms[$i-1]);
                $output =  '<a href="'.$url_previous.'" class="pre-page" >上一页</a>';
            }else{//中间的activity
                $url_previous = return_doc_jump($modinfo,$key_cms[$i-1]);
                $output = '<a href="'.$url_previous.'" class="pre-page" >上一页</a>';
                $url_next = return_doc_jump($modinfo,$key_cms[$i+1]);
                $output .= '<a href="'.$url_next.'" class="next-page" >下一页</a>';
            }
        }
    }
    return $output;
}
/**返回当前文档的id在课程活动$session_my的下标和指定下标的URL*/
/**获取指定id的活动URL
 * @author nlw
 * @param $modinfo
 * @param $id 活动的id
 *
 */
function return_doc_jump($modinfo,$index_my){

    $cms=$modinfo->cms[$index_my];//每个课程中的各个活动的信息
    $cms_url=$cms->url;//活动的URL对象
    if(!empty($cms_url)){//如果不是空
        $cms_url_path=$cms_url->get_path();//从URL对象中获取path，注意：这里是的path是protect属性，要调用系统的方法进行获取
    }else{
        $cms_url_path='#';
    }
    $cms_url_path=$cms_url_path.'?id='.$index_my;//拼接URL

    return $cms_url_path;
}
/**课程文档阅读增加页面跳转链接/添加上下页跳转按钮 end*/