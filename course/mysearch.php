<?php
/**
 *课程搜索结果页
 */
require_once("../config.php");
$search    = optional_param('searchParam', '', PARAM_RAW);  // 搜索的关键字
$page      = optional_param('page', 1, PARAM_INT);     // 当前显示的页码
$perpage   = optional_param('perpage', '5', PARAM_RAW); // 每页显示的记录数
$search = trim(strip_tags($search)); // trim & clean raw searched string
$paramarray = array();
$paramarray["search"] = $search;
$paramarray["page"] = $page;
$paramarray["perpage"] = $perpage;
$PAGE->set_url('/course/mysearch.php', $paramarray);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('coursesearchresult');//设置layout
$coursesearchrenderer = $PAGE->get_renderer('core', 'course');//设置renderer
if ($CFG->forcelogin) {
    require_login();
}
$strcourses = new lang_string("courses");
$strsearch = new lang_string("search");
$strsearchresults = new lang_string("searchresults");
$strnovalidcourses = new lang_string('novalidcourses');
$PAGE->set_title("课程搜索结果页");
$PAGE->set_heading("xx搜索");
echo $OUTPUT->header();
echo $coursesearchrenderer->my_course_searchresult($search,$page,$perpage);
echo $OUTPUT->footer();