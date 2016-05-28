<?php

/**
 * 课程搜索结果
 */

require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->dirroot . "/lib/dml/moodle_database.php");
require_once($CFG->dirroot . "/lib/coursecatlib.php");

class theme_more_coursesearchresult_renderer extends plugin_renderer_base {
//class theme_more_coursesearchresult_renderer extends core_course_renderer {
//class theme_more_coursesearchresult_core_renderer extends core_renderer {

//	public function __construct(moodle_page $page, $target) {
//		$this->strings = new stdClass;
//		parent::__construct($page, $target);
//		$this->add_modchoosertoggle();
//	}

	public function search_courses ($search,$page,$perpage) {

		$content = '2016';

		return $content;
	}


	//START 获取summary中的图片 /** 2016-03-02 徐东威 */
	public function my_get_course_formatted_summary_pix($course) {
		global $CFG;
		require_once($CFG->libdir. '/filelib.php');

		$context = context_course::instance($course->id);

		$my_course_summary_pix=$this->my_search_summary_pix($course->summary);
		$summary = file_rewrite_pluginfile_urls($my_course_summary_pix, 'pluginfile.php', $context->id, 'course', 'summary', null);
		$summary = format_text($summary, $course->summaryformat,null, $course->id);

		return $summary;
	}
	//END 获取summary中的图片

}
