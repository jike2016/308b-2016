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
    <?php echo $OUTPUT->standard_head_html() ?>
    
    <link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->
	
	<script src="../theme/more/js/jquery-1.11.3.min.js"></script>
    
	<style>
		.navbar-form {
		padding: 10px 0px; }
			
		/*************左边菜单**************/
			.left-menu{width: 14%;float: left;border-bottom: 0px solid #eee; background-color: #FFFFFF; border-radius: 5px; margin-top:40px;}
			.menubtn {border-radius: 5px;height: 60px; background-color: #FFFFFF;cursor:pointer; border: 1px solid #eee; color: #777;}
			.menubtn:hover{background-color: #85d1f7; color: #FFFFFF;}
			.menubtn:hover h4 .glyphicon{color: #FFFFFF; }
			.menubtn-active {background-color: #2cace6; color: #FFFFFF;}
			.menubtn-active h4 .glyphicon{color: #FFFFFF; }			
			.menubtn h4 .glyphicon{ top: 2px;}
			.menubtn h4 {margin: 17px 0px 0px 20%;}
			/*************@end左边菜单**************/
			
			
			.banner_right {width: 75%; margin-left: 5%; float: right;}
			
			/************右边板块*   此处以下样式为页面，以上的代码为辅助   ***********/
			
			/****关注的人***/
			.concerned-list {width: 100%; padding: 0px 0% 15px 0%;}
			.concerned-list .people-box {width:18.8%;float: left; height: 80px; margin-right: 1.1%;  margin-bottom: 10px; background-image:url(../theme/more/pix/blogmy/back3.jpg);background-size:100%;border-radius: 5px;}
			.concerned-list .people-box .Learnerimg-box {width: 50px;height: 50px;border-radius: 25px;overflow: hidden;float: left;margin-top: 15px; margin-left: 8%; }
			.concerned-list .people-box .line {float: left; height: 40px; width: 1px; background-color: #ffffff; margin: 20px 5%;}
			.concerned-list .people-box .Learnerimg-box img {height: 100%;}
			.concerned-list .people-box p{color: #ffffff; font-size: 16px; margin: 15px 0px 0px 50%; font-weight: 600;}
			/****关注的人 end***/
			
			/****与我相关* background-color: #85d1f7; **/
			.about-myself {width: 100%; padding: 0px 3% 20px 0%; }
			.about-myself .info { width: 100%; border-radius: 10px; padding: 13px 15px 13px 15px; margin-bottom: 20px; -moz-box-shadow: 5px 5px 5px #888888; /* 老的 Firefox */box-shadow: 5px 5px 5px #888888;background-image:url(../theme/more/pix/blogmy/back3.jpg);background-size:100%;}
			.about-myself .last {border-bottom: 0px;}
			.about-myself .info .usename { color: #0077B3; font-size: 18px; font-weight: 600; margin: 0px;}
			.about-myself .info .divison_line {width: 100%; height: 1px; background-color: #F0F0F0;margin-bottom: 7px;}
			.about-myself .info .time { color: #f0f0f0; font-size: 12px;}
			.about-myself .info a {text-decoration: none;font-size: 16px; color: #FFFFFF;}
			.about-myself .info a:hover {color: #0077B3;}
			/****与我相关 end***/
			
			/************右边板块 @end************/
	</style>
	<script>
		$(document).ready(function() {
			if(getQueryString('mycontent')==1){
				// $('.menubtn').removeClass('menubtn-active');
				$('#myconcerned').addClass('menubtn-active');
			}
			else if(getQueryString('mycontent')==2){
				// $('.menubtn').removeClass('menubtn-active');
				$('#myrelated').addClass('menubtn-active');
			}
			else{
				$('#mycourse').addClass('menubtn-active');
			}
			
			/** SATRT  添加事件点击（全部、我的关注、与我相关） 朱子武 20160310*/
			$('#mycourse').click(function() {
				window.location.href = window.location.protocol+'//'+window.location.host+'/moodle/blog/index.php';
			});
			$('#myconcerned').click(function() {
				window.location.href = window.location.protocol+'//'+window.location.host+'/moodle/blog/index.php?mycontent=1';
			});
			$('#myrelated').click(function() {
				window.location.href = window.location.protocol+'//'+window.location.host+'/moodle/blog/index.php?mycontent=2';
			});
			/** END  添加事件点击 朱子武 20160310*/	
			
			$('.menubtn').click(function() {
				$('.menubtn').removeClass('menubtn-active');
				$(this).addClass('menubtn-active');
			});	
		});	
		
	function getQueryString(name) { 
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
	var r = window.location.search.substr(1).match(reg); 
	if (r != null) return unescape(r[2]); return null; 
	} 
	</script>
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

	<?php require_once("includes/header.php"); ?>
    
	<div style="height:20px;"></div>

    <div id="page" class="container-fluid">
        <!--位置 <?php echo $OUTPUT->full_header(); ?>-->
        <div id="page-content" class="row-fluid">
            <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
                <div class="row-fluid">
                    <section id="region-main" style="margin-left: auto;margin-right: auto; width: 74.35897436%;">
				
					<!--左边菜单-->
					<div class="left-menu">
						<div id="mycourse" class="menubtn"><h4><span class="glyphicon glyphicon-align-justify"></span>&nbsp;全部内容</h4></div>	
						<div id="myconcerned" class="menubtn"><h4><span class="glyphicon glyphicon-screenshot"></span>&nbsp;我的关注</h4></div>				
						<div id="myrelated" class="menubtn"><h4><span class="glyphicon glyphicon-user"></span>&nbsp;与我相关</h4></div>
						<a href="../blog/edit.php?action=add" style="text-decoration:none;"><div  class="menubtn"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;写篇博客</h4></div></a>
						
					</div>
					<!--左边菜单end-->

					
					<div id='mainfather' style="width: 85%;margin-left:15%; ">
					
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
						</div>
                    </section>
                    <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
                </div>
            </div>
            <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
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
</html>
