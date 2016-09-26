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
    
    <link rel="stylesheet" href="../../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
    <link rel="stylesheet" href="../../theme/more/style/navstyle.css" /> <!--全局-->
	
	<script src="../../theme/more/js/jquery-1.11.3.min.js"></script>
    
	<style>
		.navbar-form {
		padding: 10px 0px; }
	</style>
<script>
$(document).ready(function(){
	//聊天室 START
	//适配不同大小偏移值
	var winW=$(window).width();
	var winH=$(window).height();
	var leftval = (winW-900)/2;	
	var topval = (winH-600)/3;	
	$('.chat-box').offset({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
	//适配不同大小偏移值 end	
	
	$('.elevator-weixin').click(function(){
									
		$('.chat-box').show();	
	})
	$('#chat-close').click(function(){
		$('.chat-box').hide();
		//alert("关闭的top: " +$('.chat-box').offset().top);
	})
	//聊天室 End
	//收藏按钮
	$('#collection-btn').click(function()
	{
		$.ajax({
			url: "<?php echo $CFG->wwwroot;?>/privatecenter/mycollection/collectionpage.php",
			data: {mytitle: document.title, myurl: window.location.href },
			success: function(msg){
				if(msg=='1'){
					alert('收藏成功，可去个人中心查看')
				}
				else{
					msg=='2' ? alert('您已经收藏过了，请去个人中心查看收藏结果') :alert('收藏失败');
				}
			}
		});
	});
	//点赞按钮
	$('#like-btn').click(function()
	{
		$.ajax({
			url: "<?php echo $CFG->wwwroot;?>/like/courselike.php",
			data: {mytitle: document.title, myurl: window.location.href },
			success: function(msg){
				// alert(msg);
				if(msg=='1'){
					alert('点赞成功')
				}
				else{
					msg=='2' ? alert('你已经点赞了，不能再次点赞') :alert('点赞失败');
				}
			}
		});
	});
});
</script>
<!--Start VideoJS视频播放器 cx20160922-->
<!--<link type="text/css" href="/moodle/mod/lesson/videojs/video-js3/video-js.css" rel="stylesheet">
<script src="/moodle/mod/lesson/videojs/html5media-master/dist/api/1.1.8/html5media.min.js"></script>
<script type="text/javascript" src="/moodle/mod/lesson/videojs/video-js3/video.js"></script>
<script src="/moodle/mod/lesson/videojs/node_modules/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>
<!--End-->
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

	<?php require_once("includes/header.php"); ?>
    
	<div style="height:40px;"></div>

    <div id="page" class="container-fluid">
        <!--位置 <?php echo $OUTPUT->full_header(); ?>-->
        <div id="page-content" class="row-fluid">
            <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
                <div class="row-fluid">
                    <section id="region-main" class="<?php echo $regionmain; ?>">
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
                    </section>
                    <?php echo $OUTPUT->blocks('side-pre', $sidepre); ?>
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
	<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?note=2" target="_blank" ></a>
			<a class="elevator-weixin" style="cursor:pointer"></a>
            <a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
			<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
            <a class="elevator-top" href="#"></a>
			
		</div>
		<div class="chat-box">
			<div class="chat-head">
				<p>聊天室</p>
				<p id="chat-close" class="close">x</p>
			</div>
			<iframe src="<?php echo $CFG->wwwroot;?>/chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>
		</div>
</body>
</html>
