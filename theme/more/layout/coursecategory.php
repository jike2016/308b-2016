<?php
//用户登录后显示的页面不同于columns3.php
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
 * Moodle's Clean theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_clean
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);

// Set default (LTR) layout mark-up for a three column page.
$regionmainbox = 'span9';
$regionmain = 'span8 pull-right';
$sidepre = 'span4 desktop-first-column';
$sidepost = 'span3 pull-right';
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmainbox = 'span9 pull-right';
    $regionmain = 'span8';
    $sidepre = 'span4 pull-right';
    $sidepost = 'span3 desktop-first-column';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
	<?php echo $OUTPUT->standard_head_html() ?>

	<link rel="stylesheet" href="../theme/more/style/coursecategory/courseforstudent.css" type="text/css" />
	<script src="../theme/more/js/jquery-1.11.3.min.js"></script><!--全局-->
<!--	<script src="../theme/more/js/bootstrap.min.js"></script><!--全局-->


<!--　start　不要-->
<!--	<link rel="stylesheet" href="../theme/more/style/coursecategory/coursecategorystyle.css" type="text/css" />-->
	<script src="../theme/more/js/jquery.easing.min.js"></script><!--全局-->
	<script src="../theme/more/js/custom.js"></script><!--全局-->
	<script src="../theme/more/js/course_category.js"></script><!--coursecategory.php-->
	<script src="../theme/more/js/coursecategory.js"></script><!--coursecategory.php-->
<!--　end　不要-->

<!--start 课程列表显示格式切换-->
<script>
	$(document).ready(function() {
		//导航条列表样式控制 start
		$('.navRight li').removeClass('active');
		$('.navRight .mod_course').addClass('active');
		//导航条列表样式控制 end

		//课程列表显示格式(网格、列表)切换 start
		$('#list_btn_tr').click(function() {
			$('.list_btn').removeClass('active');
			$(this).addClass('active');
			$('.course-list').hide();
			$('.course-list-th').show();
		});
		$('#list_btn_th').click(function() {
			$('.list_btn').removeClass('active');
			$(this).addClass('active');
			$('.course-list-th').hide();
			$('.course-list').show();
		});
		//课程列表显示格式切换 end

		//start 课程分类，更多按钮
		$('#more-type').mouseover(function(){
			$('.coursetypebox-more').show();
			$('#more-type').addClass('active');
		})

		$('.coursetypebox-more').mouseover(function(){
			$('.coursetypebox-more').show();
		})

		$('.coursetypebox-more').mouseout(function(){
			$('.coursetypebox-more').hide();
			$('#more-type').removeClass('active');
		})
		//end 课程分类，更多按钮

	});
</script>
<!--end 课程列表显示格式切换-->

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<!--头部导航条-->
<?php require_once("includes/header.php"); ?>
<!--头部导航条 end -->

<div id="page" class="">
   <!--位置 <?php echo $OUTPUT->full_header(); ?>-->
<!--    <div id="page-content" class="row-fluid">-->
	
	<div id="region-main-box" class="">

			<!--11月12日v0.1内容开始-->
			<div>
				<div id="main2">
				<?php
					//echo $OUTPUT->course_content_header();
					echo $OUTPUT->main_content();
					//echo $OUTPUT->course_content_footer();
				?>
				</div>

				<?php
				//echo $OUTPUT->blocks('side-pre', $sidepre);
				?><!--左边 -->
			</div>
        <?php //echo $OUTPUT->blocks('side-post', $sidepost); ?><!--右边 -->
    </div>

    <footer id="page-footer">
        <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
<!--         <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p> -->
        <?php
        echo $html->footnote;
//        echo $OUTPUT->login_info();
//        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>

<!--底部导航条-->
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->

<!--右下角按钮-->
<?php require_once("includes/link_button.php"); ?>
<!--右下角按钮 end-->
		
<!--主面板end-->
</body>

</html>
