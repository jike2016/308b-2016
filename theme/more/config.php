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
 * Theme More config file.
 *
 * @package    theme_more
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->name = 'more';
$THEME->parents = array('clean', 'bootstrapbase');

$THEME->doctype = 'html5';
$THEME->sheets = array(   //css文件
    // 'custom',             // Must come first: Page layout.
    'alertstyle',         // Must come second: General styles.
	'allpage',
	//'bootstrap',
	//'navstyle'
    // 'admin',
    // 'blocks',
    // 'calendar',
    // 'course',
    // 'dock',
    // 'grade',
    // 'message',
    // 'question',
    // 'user',
    // 'tabs',
    // 'filemanager'
);


$THEME->lessfile = 'moodle';
$THEME->parents_exclude_sheets = array('bootstrapbase' => array('moodle'), 'clean' => array('custom'));
$THEME->lessvariablescallback = 'theme_more_less_variables';
$THEME->extralesscallback = 'theme_more_extra_less';
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = array();
$THEME->enable_dock = true;
$THEME->editor_sheets = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_more_process_css';
$THEME->layouts = array(
    // Main course page.
    'course' => array(
        'file' => 'course.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	//课程目录页
    'coursecategory' => array(
        'file' => 'coursecategory.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//学生看到的课程内容页,左边隐藏，内容居中
	'incourseforstudent' => array(
        'file' => 'incourseforstudent.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
	//管理员看到的课程内容页
    'incourse' => array(
        'file' => 'incourse.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//学生看到的单课程首页
	'courseforstudent' => array(
        'file' => 'courseforstudent.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//管理员看到的单课程首页
	'courseforadmin' => array(
        'file' => 'courseforadmin.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//打印试题页面
	'quizprint' => array(
        'file' => 'quizprint.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//主页，网站首页 The site home page.
    'frontpage' => array(
        'file' => 'indexpage.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true),
    ),
	//选课页面
	'xuankepage' => array(
        'file' => 'xuankepage.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//登录页
	'loginpage' => array(
        'file' => 'loginpage.php',
        'regions' => array(),
        'options' => array('langmenu' => true),
    ),
	 //个人主页 My dashboard page.
    'mydashboard' => array(
        'file' => 'mydashboard.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	 //视频播放页面，lesson
    'lessonvideoforstud' => array(
        'file' => 'lessonvideoforstud.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	//台账任务页
	'missionmy' => array(
        'file' => 'missionmy.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	//台账任务页_分级管理员页面
	'missionmy_classify' => array(
		'file' => 'missionmy_classify.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
		'options' => array('langmenu' => true),
	),
	//学习圈blog
	'blogmy' => array(
        'file' => 'blogmy.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	//集体学习
	'teamlernpicker' => array(
        'file' => 'teamlernpicker.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
	 //个人中心》个人资料
    'mypersonaldata' => array(
        'file' => 'mypersonaldata.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//笔记创建
    'newnotemy_personal' => array(
        'file' => 'newnotemy_personal.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//笔记创建
    'pageforstud' => array(
        'file' => 'pageforstud.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
    ),
	//写博客页面
	'myblogeditdata' => array(
			'file' => 'myblogeditdata.php',
			'regions' => array('side-pre', 'side-post'),
			'defaultregion' => 'side-pre',
	),
	//单独只为有usermenu的页面
	'usermenu' => array(
			'file' => 'usermenu.php',
			'regions' => array('side-pre', 'side-post'),
			'defaultregion' => 'side-pre',
	),
	//管理员广告设置页面
	'ad_board' => array(
			'file' => 'ad_board.php',
			'regions' => array('side-pre', 'side-post'),
			'defaultregion' => 'side-pre',
	),
	//openmetting页面
	'openmettingmy' => array(
		'file' => 'openmettingmy.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//message消息页面
	'message' => array(
		'file' => 'message.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//课程搜索结果页
	'coursesearchresult' => array(
		'file' => 'coursesearchresult.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//课程评分页面
	'course_score' => array(
		'file' => 'course_score.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//注册页面
	'register' => array(
		'file' => 'register.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//分级管理员看到的单课程首页
	'courseforgradingadmin' => array(
		'file' => 'courseforgradingadmin.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//慕课管理员课程管理页
	'courseclassifymanage' => array(
		'file' => 'courseclassifymanage.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	//管理员注册审核页面
	'registerforadmin' => array(
		'file' => 'registerforadmin.php',
		'regions' => array('side-pre', 'side-post'),
		'defaultregion' => 'side-pre',
	),
	// Server administration scripts. 覆盖父类样式
	'admin' => array(
//		'file' => 'columns2.php',
//		'file' => 'mydashboard.php',
		'file' => 'my_admin.php',
		'regions' => array('side-pre'),
		'defaultregion' => 'side-pre',
	),
	//绩效登录
	'loginoauthpage' => array(
		'file' => 'loginoauthpage.php',
		'regions' => array('side-pre'),
		'defaultregion' => 'side-pre',
	),


);