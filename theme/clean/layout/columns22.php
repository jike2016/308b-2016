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
 * The two column layout.
 *
 * @package   theme_clean
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);

// Set default (LTR) layout mark-up for a two column page (side-pre-only).
$regionmain = 'span9 pull-right';
$sidepre = 'span3 desktop-first-column';
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmain = 'span9';
    $sidepre = 'span3 pull-right';
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title>column2<?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
    <link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap-theme.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/style.css" rel='stylesheet' type="text/css">
</head>

<body <?php echo $OUTPUT->body_attributes('two-column'); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner">
	<nav class="navbar navbar-inverse navbar-fixed-top" style="margin:0px; padding:0px">
		<div style="width:1600px; margin:auto"> 
               	
        <form class="navbar-form navbar-left" style="width:70%; margin:12px 0px 0px 0px">
             <div class="logo" style="float:left; width:22%;" onmouseover="this.style.cursor='pointer'" onclick="document.location='<?php echo $CFG->wwwroot;?>';"></div>
			<div class="btn-group btn-group-justified" style="float:left;width:70%; " >
				<div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>"><button type="button" class="btn lgfont">首页</button></a><!--这里的链接中需要id参数-->
  				</div>                
  				<div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>/mod/forum/view.php?id=1"><button type="button" class="btn lgfont">微阅</button></a><!--这里的链接中需要id参数-->
  				</div>
                <div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>/course/index.php"><button type="button" class="btn lgfont">微课</button></a>
  				</div>
                <div class="btn-group" role="group">
    				<a href="#"><button type="button" class="btn lgfont">直播</button></a> 
  				</div>
               
			</div>
            </form>
            
            <form class="navbar-form navbar-right" role="search" style="margin:12px 0px 0px 0px;">
  				<div class="form-group" style=" margin:8px 0px;">
    			<input type="text" class="form-control" style="height:30px;" placeholder="Search">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          
  				 <?php echo $OUTPUT->user_menu(); ?>
  				</div>
                
			</form>
		</div>      
	</nav>
</header>
<div style="height:40px;"></div>


<div id="page" class="container-fluid">
   <?php echo $OUTPUT->full_header(); ?>
    <div id="page-content" class="row-fluid">
        <section id="region-main" class="<?php echo $regionmain; ?>">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
        <?php echo $OUTPUT->blocks('side-pre', $sidepre);
        ?>
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
</body>

<script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery-1.11.3.min.js"></script>
<script src="<?php echo $CFG->wwwroot;?>/themechange/js/bootstrap.min.js"></script>
</html>
