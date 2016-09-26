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
    
    <link rel="stylesheet" href="<?php echo $CFG->wwwroot; ?>/theme/more/style/bootstrap.css" type="text/css"><!--全局-->
<!--    <link rel="stylesheet" href="../../theme/more/style/navstyle.css" /> <!--全局-->
		<!-- Start 添加css样式 朱子武 20160315-->
	<link rel="stylesheet" href="../../theme/more/style/articlecomment/articlecomment.css" />
	<!-- End 添加css样式 朱子武 20160315-->
	<script src="../../theme/more/js/jquery-1.11.3.min.js"></script>
    
	<style>
		.navbar-form {
		padding: 10px 0px; }
		* {margin: 0;padding: 0;list-style: none;border: 0;}
		html,body {font-family: "微软雅黑";font-size: 14px;min-height:1000px;padding-top: 0px; background-color: #ffffff}
		.navRight a, a:hover {color: #3E3E3E;text-decoration: none;}
		ui,ol{margin: 0px}
		p {margin: 0px;}
		nav .center .r-box .dropdown-toggle,nav .center .r-box .searchbtn,nav .center .r-box .search{box-sizing: initial}
		.container-fluid {  min-height: 800px;  }
	</style>
<script>
$(document).ready(function(){
	/** Start 添加评论按钮点击事件 朱子武 20160315*/
		$('#commentBtn').click(function() {
			var mytext =$(this).parent().children('.form-control').val();
			if(mytext==""){
				alert('请输入评论内容');
			}
			else{
				$.ajax({
					url: "../../mod/page/mygetarticlecomment.php",
					data: { mycomment: mytext, articleid: getQueryString('id') },
					success: function(msg){
						if(msg=='1'){
							// location.reload();
							window.location.href=window.location.href+'&page=1';
						}else{
							alert('评论失败');
						}
					}
				});
			}
		});
		/** End 添加评论按钮点击事件 朱子武 20160315*/

});
/** Start 获取url中的文章id 朱子武 20160315*/
	function getQueryString(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
		var r = window.location.search.substr(1).match(reg);
		if (r != null) return unescape(r[2]);
		return null;
	}
	/** End 获取url中的文章id 朱子武 20160315*/
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
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
                    </section>
                    <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
                </div>
            </div>
            <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
        </div>
    
<!--        <footer id="page-footer">-->
<!--            <div id="course-footer">--><?php //echo $OUTPUT->course_footer(); ?><!--</div>-->
<!--    <!--         <p class="helplink">--><?php //echo $OUTPUT->page_doc_link(); ?><!--</p> -->
<!--            --><?php
//            echo $html->footnote;
//    //        echo $OUTPUT->login_info();
//    //        echo $OUTPUT->home_link();
//            echo $OUTPUT->standard_footer_html();
//            ?>
<!--        </footer>-->
    
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
		
    </div>

<!--底部导航条-->
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->

<!--右下角按钮-->
<?php require_once("includes/link_button.php"); ?>
<!--右下角按钮 end-->

</body>
</html>
