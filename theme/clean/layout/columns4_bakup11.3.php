<?php
//首页样式
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
    <link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap-theme.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/style.css" rel='stylesheet' type="text/css">
	 <link href="<?php echo $CFG->wwwroot;?>/themechange/css/alertstyle.css" rel="stylesheet" type="text/css" />
    
    <script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery-1.11.3.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/bootstrap.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/slider.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery.easing.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/custom.js"></script>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<header role="banner">
	<nav class="navbar navbar-inverse navbar-fixed-top" style="margin:0px; padding:0px">
		<div style="width:1600px; margin:auto"> 
               	
        <form class="navbar-form navbar-left" style="width:63%; margin:12px 0px 0px 0px">
            <div class="logo" style="float:left; width:30%;"></div>
			<div class="btn-group btn-group-justified" style="float:right;width:70%;" >
				                
  				<div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>/mod/forum/view.php"><button type="button" class="btn lgfont">微阅</button></a><!--这里的链接中需要id参数-->
  				</div>
                <div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>/course/index.php"><button type="button" class="btn lgfont">微课</button></a>
  				</div>
                <div class="btn-group" role="group">
    				<a href="#"><button type="button" class="btn lgfont"><span class="glyphicon glyphicon-cloud" aria-hidden="true"></span>&nbsp;直播</button></a> 
  				</div>
               
			</div>
            </form>
            
            <form class="navbar-form navbar-right" role="search" style="margin:12px 0px 0px 0px;">
  				<div class="form-group" style=" margin:8px 0px;">
    			<input type="text" class="form-control" style="height:40px;" placeholder="Search">
  				</div>
                 <!--?php echo $OUTPUT->user_menu(); ?-->
  				 <div class="login fr" style="padding:18px 0px 0px 0px;">      				
      					<ul class="inline">
        					<li class="openlogin"><a href="#" style="color:#CCC" onClick="return false;">登录</a></li>
                            <li><a href="#" style="color:#CCC">l</a></li>
        					<li class="openlogin"><a href="#" style="color:#CCC" onClick="return false;">注册</a></li>
      					</ul>
    				</div>   
    			<div class="clear"></div>
                
			</form>
		</div>      
	</nav>

<!--11月2日新加内容开始-->
<div class="clear"></div>
<div class="loginmask"></div>
<div id="loginalert"  style="margin-top:100px;">
  <div class="pd20 loginpd">
    <h3><i class="closealert fr"></i>
      <div class="clear"></div>
    </h3>
    <div class="loginwrap">
      <div class="loginh">
        <div class="fl">会员登录</div>
        <div class="fr">还没有账号<a id="sigup_now" href="http://sc.chinaz.com/" onClick="return false;">立即注册</a></div>
        <div class="clear"></div>
      </div>
      <h3><span class="login_warning">用户名或密码错误</span>
        <div class="clear"></div>
      </h3>
      <div class="clear"></div>
      <form action="" method="post" id="login_form">
        <div class="logininput">
          <input type="text" name="username" class="loginusername" value="邮箱/用户名" />
          <input type="text" class="loginuserpasswordt" value="密码" />
          <input type="password" name="password" class="loginuserpasswordp" style="display:none" />
        </div>
        <div class="loginbtn">
          <div class="loginsubmit fl">
            <input type="submit" value="登录" style="margin:auto; height:50px;" />
            <div class="loginsubmiting">
              <div class="loginsubmiting_inner"></div>
            </div>
          </div>
          <div class="logcheckbox fl">
            <input id="bcdl" type="checkbox" checked="true" />
            保持登录</div>
          <div class="fr"><a href="#">忘记密码?</a></div>
          <div class="clear"></div>
        </div>
      </form>
    </div>
  </div>
</div>
	<!--11月2日新加内容结束-->


	<div id="banner_tabs" class="flexslider">
		<div class="slides">
			<div style="background-color:#201c1b">
			<a title="" target="_blank" href="#">
				<img width="1920" alt="" style="background: url(<?php echo $CFG->wwwroot;?>/themechange/images/banner1.jpg) no-repeat center;" src="<?php echo $CFG->wwwroot;?>/themechange/images/alpha.png">
			</a>          
			</div>
			<div style="background-color:#dbe98f">
			<a title="" href="#">
				<img width="1920" alt="" style="background: url(<?php echo $CFG->wwwroot;?>/themechange/images/banner2.png) no-repeat center;" src="<?php echo $CFG->wwwroot;?>/themechange/images/alpha.png">
			</a>
			</div>
			<div style="background-color:#707070">
			<a title="" href="#">
				<img width="1920" alt="" style="background: url(<?php echo $CFG->wwwroot;?>/themechange/images/banner3.jpg) no-repeat center;" src="<?php echo $CFG->wwwroot;?>/themechange/images/alpha.png"> 
			</a>
			</div>
		</div>
		<ul class="flex-direction-nav">
			<li><a class="flex-prev" href="javascript:;">Previous</a></li>
			<li><a class="flex-next" href="javascript:;">Next</a></li>
		</ul>
		<ol id="bannerCtrl" class="flex-control-nav flex-control-paging">
			<li><a>1</a></li>
			<li><a>2</a></li>
			<li><a>2</a></li>
		</ol>
	</div>
</header>
<div style="width:100%; height:40px"></div>

<div id="page" class="container-fluid">
    
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
        <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p>
        <?php
        echo $html->footnote;
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </footer>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
</body>

<script type="text/javascript">
$(function() {
	var bannerSlider = new Slider($('#banner_tabs'), {
		time: 3000,
		delay: 300,
		event: 'hover',
		auto: true,
		mode: 'fade',
		controller: $('#bannerCtrl'),
		activeControllerCls: 'active'
	});
	$('#banner_tabs .flex-prev').click(function() {
		bannerSlider.prev()
	});
	$('#banner_tabs .flex-next').click(function() {
		bannerSlider.next()
	});
})
</script>
</html>
