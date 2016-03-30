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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrapbase
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//echo '11';exit;
require_once($CFG->dirroot . "/course/renderer.php");
class theme_more_core_course_renderer extends core_course_renderer {
   /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|coursecat $category
     */
    public function course_category($category) {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');
        $coursecat = coursecat::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';
		//如果没有参数则为课程分类首页显示右边
		if(count($_GET)==0)
		{
			$output .= '<div style="float:left; width:18%; padding:0px">';
		}
		

        if (can_edit_in_category($coursecat->id)) {
            // Add 'Manage' button if user has permissions to edit this category.
            $managebutton = $this->single_button(new moodle_url('/course/management.php',
                array('categoryid' => $coursecat->id)), get_string('managecourses'), 'get');
            $this->page->set_button($managebutton);
        }
        if (!$coursecat->id) {
            if (coursecat::count_all() == 1) {
                // There exists only one category in the system, do not display link to it
                $coursecat = coursecat::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        } else {
            $this->page->set_title("$site->shortname: ". $coursecat->get_formatted_name());

            // Print the category selector
            $output .= html_writer::start_tag('div', array('class' => 'categorypicker'));
            $select = new single_select(new moodle_url('/course/index.php'), 'categoryid',
                    coursecat::make_categories_list(), $coursecat->id, null, 'switchcategory');
            $select->set_label(get_string('categories').':');
            $output .= $this->render($select);
            $output .= html_writer::end_tag('div'); // .categorypicker
        }

        // Print current category description
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }

        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
                ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);
       
      

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add action buttons
        $output .= $this->container_start('buttons');
        $context = get_category_or_system_context($coursecat->id);
		//管理员添加课程按钮
        // if (has_capability('moodle/course:create', $context)) {
            // Print link to create a new course, for the 1st available category.
            // if ($coursecat->id) {
                // $url = new moodle_url('/course/edit.php', array('category' => $coursecat->id, 'returnto' => 'category'));
            // } else {
                // $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
            // }
            // $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
        // }
		 // Add course search form.
		$output .= $this->course_search_form();
		$output .= "</div>";

		
        ob_start();
        if (coursecat::count_all() == 1) {
            print_course_request_buttons(context_system::instance());
        } else {
            print_course_request_buttons($context);
        }
        $output .= ob_get_contents();
        ob_end_clean();
        $output .= $this->container_end();
		//right
		//如果没有参数则为课程分类首页显示右边
		if(count($_GET)==0)
		{
			/*
			$output .= '<div style="float:right; width:79%;">
			<div style="width:100%; height:129px; margin:50px 0px 20px 0px; background-color:#fff;">
				<div style="float:left; margin:2px 2px"><img src="http://localhost/moodle/themechange/images/c-1.jpg"></div>
				<div style="float:right; width:75%; margin:3px 5px;">
					<div style="width:100%; height:23px;"><p style="font-size:18px;">唐诗经典</p></div>
					<div style="width:100%; height:23px;"><a href="#" class="agreen">浙江大学</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="agreen">胡可先、陶然</a></div>
					<div style="width:100%; height:30px; margin:5px 0px;">
						<button class="btn btn-success btn-sm active week">
						<p><span class="glyphicon glyphicon-time" style="color:#fff;"></span>&nbsp;</p><p>6/13周</p>
						</button>
					</div>
					<div style="width:100%; height:20px; margin:5px 0px;">
						<button class="btn btn-info" style="float:right;">查看课程</button>
					</div>
				</div>
			</div>
			<button type="button" class="btn btn-lg btn-block morebutton">载入更多</button>
		</div><!--right-->  ';
		*/
		
			$output .= '<div style="float:right; width:79%;">';
			$output .= $this->my_frontpage_available_courses();
			$output .= '</div>';
		}
		
        return $output;
    }
	
	/**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function my_frontpage_available_courses() {
        global $CFG;
        require_once($CFG->libdir. '/coursecatlib.php');

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_courses_display_options(array(
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = coursecat::get(0)->get_courses($chelper->get_courses_display_options());
        $totalcount = coursecat::get(0)->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        return $this->my_coursecat_courses($chelper, $courses, $totalcount);
    }
	
	/**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function my_coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        foreach ($courses as $course) {
            $coursecount ++;
            $classes = ($coursecount%2) ? 'odd' : 'even';
            if ($coursecount == 1) {
                $classes .= ' first';
            }
            if ($coursecount >= count($courses)) {
                $classes .= ' last';
            }
            $content .= $this->my_coursecat_coursebox($chelper, $course, $classes);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // .courses
        return $content;
    }
	
	 /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param course_in_list|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function my_coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG;
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $classes .= ' collapsed';
            $nametag = 'div';
        }
		$my_courseurl=new moodle_url('/course/view.php', array('id' => $course->id));
        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
			'onclick' => 'window.location.href="'.$my_courseurl.'"',
			'style' => 'cursor:pointer',
        ));

        //$content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
		$coursenamelink_link = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            '查看课程');
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename);
								
		$content .= '<div style="width:100%; height:170px; margin:0px 0px 0px 0px; background-color:#fff;" >
				<div style="float:left; margin:10px 10px"><img src="http://localhost/moodle/theme/more/pix/c1.jpg"  style="height:150px; width:265px;"></div>
				<div style="float:right; width:68%; margin:20px 5px;">
					<div style="width:100%; height:23px;"><p style="font-size:18px;">'.$coursenamelink.'</p></div>
					<div style="width:100%; height:23px;"><a href="#" class="agreen">教师：</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="agreen">胡可先、陶然</a></div>
					<div style="width:100%; height:70px; margin:5px 0px;">';
		//课程摘要
		$content .=$this->my_coursecat_coursebox_content($chelper, $course);			
		$content .= '			
					</div>
					
				</div>
			</div>';
       // $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
       // $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
       /* if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = html_writer::empty_tag('img', array('src' => $this->output->pix_url('i/info'),
                    'alt' => $this->strings->summary));
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }*/
       // $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
		/*
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }*/

       // $content .= html_writer::end_tag('div'); // .info

     //   $content .= html_writer::start_tag('div', array('class' => 'content'));
        // $content .= $this->my_coursecat_coursebox_content($chelper, $course);
    //    $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .coursebox
	
        return $content;
    }
	/**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|course_in_list $course
     * @return string
     */
    protected function my_coursecat_coursebox_content(coursecat_helper $chelper, $course) {
		//echo'11';exit;
		$my_chelper=new theme_more_coursecat_helper();
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
		
        // display course summary
        if ($course->has_summary()) {
            //$content .= html_writer::start_tag('div', array('class' => 'summary'));
			
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            //$content .= html_writer::end_tag('div'); // .summary
        }  
        return $content;
    }
	
	 
	
	 /**
     * Renders html to display a course search form
     *
     * @param string $value default value to populate the search field
     * @param string $format display format - 'plain' (default), 'short' or 'navbar'
     * @return string
     */
    function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }

        switch ($format) {
            case 'navbar' :
                $formid = 'coursesearchnavbar';
                $inputid = 'navsearchbox';
                $inputsize = 20;
                break;
            case 'short' :
                $inputid = 'shortsearchbox';
                $inputsize = 12;
                break;
            default :
                $inputid = 'coursesearchbox';
                $inputsize = 30;
        }

        $strsearchcourses= get_string("searchcourses");
        $searchurl = new moodle_url('/course/search.php');

        $output = html_writer::start_tag('form', array('id' => $formid, 'action' => $searchurl, 'method' => 'get'));
        $output .= html_writer::start_tag('fieldset', array('class' => 'coursesearchbox invisiblefieldset'));
        //$output .= html_writer::tag('label', $strsearchcourses.': ', array('for' => $inputid));

			//<span class="glyphicon glyphicon-search" id="basic-addon1"></span>
        // $output .= html_writer::empty_tag('input', array('type' => 'text', 'id' => $inputid,
            // 'size' => $inputsize, 'name' => 'search', 'value' => s($value)));
        $output .='<button class="btn btn-sm btn-default" style="float:left; width:20%; border-radius:0px;"><span class="glyphicon glyphicon-search" id="basic-addon1"></span></button>
  			<input name="search" type="text" class="form-control" placeholder="搜索" aria-describedby="basic-addon1" style=" width:72%;height:22px; float:right; border-radius:0px;">';
        $output .= html_writer::end_tag('fieldset');
        $output .= html_writer::end_tag('form');
		

        return $output;
    }

	/**
     * Returns HTML to display a tree of subcategories and courses in the given category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat top category (this category's name and description will NOT be added to the tree)
     * @return string
     */
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        $categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }

        // Start content generation
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div', $attributes);

        if ($coursecat->get_children_count()) {
            $classes = array(
                'collapseexpand',
                'collapse-all',
            );
            if ($chelper->get_subcat_depth() == 1) {
                $classes[] = 'disabled';
            }
			//显示全部折叠
            // Only show the collapse/expand if there are children to expand.
            //$content .= html_writer::start_tag('div', array('class' => 'collapsible-actions'));
            //$content .= html_writer::link('#', get_string('collapseall'),
            //        array('class' => implode(' ', $classes)));
            //$content .= html_writer::end_tag('div');
            //$this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
        }
		
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .course_category_tree

        return $content;
    }
	/**
     * Returns HTML to display the subcategories and courses in the given category
     *
     * This method is re-used by AJAX to expand content of not loaded category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_category_content(coursecat_helper $chelper, $coursecat, $depth) {
        $content = '';
        // Subcategories
        $content .= $this->coursecat_subcategories($chelper, $coursecat, $depth);

        // AUTO show courses: Courses will be shown expanded if this is not nested category,
        // and number of courses no bigger than $CFG->courseswithsummarieslimit.
        $showcoursesauto = $chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO;
        if ($showcoursesauto && $depth) {
            // this is definitely collapsed mode
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
        }

        // Courses
        if ($chelper->get_show_courses() > core_course_renderer::COURSECAT_SHOW_COURSES_COUNT) {
            $courses = array();
            if (!$chelper->get_courses_display_option('nodisplay')) {
                $courses = $coursecat->get_courses($chelper->get_courses_display_options());
            }
            if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $chelper->set_courses_display_option('viewmoreurl', new moodle_url($viewmoreurl, array('categoryid' => $coursecat->id)));
                }
            }
            $content .= $this->coursecat_courses($chelper, $courses, $coursecat->get_courses_count());
        }

        if ($showcoursesauto) {
            // restore the show_courses back to AUTO
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO);
        }

        return $content;
    }

	 /**
     * Renders the list of subcategories in a category
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call get_child_categories_count() AFTER get_child_categories() to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        return $content;
    }
	/**
     * Returns HTML to display a course category as a part of a tree
     *
     * This is an internal function, to display a particular category and all its contents
     * use {@link core_course_renderer::course_category()}
     *
     * @param coursecat_helper $chelper various display options
     * @param coursecat $coursecat
     * @param int $depth depth of this category in the current tree
     * @return string
     */
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        // open category tag
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // do not load content
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // load category content
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
            }
        }

        // Make sure JS file to expand category content is included.
        $this->coursecat_include_js();

        $content = html_writer::start_tag('div', array(
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ));

        // category name
        $categoryname = $coursecat->get_formatted_name();
        //$categoryname = html_writer::link(new moodle_url('/course/index.php',
        //        array('categoryid' => $coursecat->id)),
        //        $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        //$content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname'));
		$temp_url = new moodle_url('/course/index.php', array('categoryid' => $coursecat->id));
		$content .= '<a href="'.$temp_url.'"><button type="button" class="btn btn-lg btn-block bgreen" style="margin:0; border-radius:0; border-top:0px">
        	<div style="padding:8px 0">
            <p style="float:left; font-size:16px">'.$categoryname.'</p>
            <p style="float:right; font-size:16px">580</p>
            </div>
        </button></a>';
        $content .= html_writer::end_tag('div'); // .info

        // add category content to the output
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .category

        // Return the course category tree HTML
        return $content;
    }
}


class theme_more_coursecat_helper extends coursecat_helper{
	/**
     * Returns given course's summary with proper embedded files urls and formatted
     *
     * @param course_in_list $course
     * @param array|stdClass $options additional formatting options
     * @return string
     */
    public function get_course_formatted_summary($course, $options = array()) {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        if (!$course->has_summary()) {
            return '';
        }
        $options = (array)$options;
        $context = context_course::instance($course->id);
        if (!isset($options['context'])) {
            // TODO see MDL-38521
            // option 1 (current), page context - no code required
            // option 2, system context
            // $options['context'] = context_system::instance();
            // option 3, course context:
            // $options['context'] = $context;
            // option 4, course category context:
            // $options['context'] = $context->get_parent_context();
        }
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat, $options, $course->id);
        if (!empty($this->searchcriteria['search'])) {
            $summary = highlight($this->searchcriteria['search'], $summary);
        }
        return $summary;
    }

}
