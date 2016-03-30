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
    <link rel="stylesheet" href="../moodle/theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="../moodle/theme/more/style/navstyle.css" /> <!--全局-->
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<header role="banner">
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="nav-mainbox">              	
        	<div class="navbar-form navbar-left">
            	<div class="logobox" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
					<img src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png">
				</div>
                
				<div class="btn-group btn-group-justified" >
                    <div class="btn-group" role="group">
                        <a href="<?php echo $CFG->wwwroot;?>"><button type="button" class="btn lgfont">首页</button></a>
                    </div>                
                    <div class="btn-group" role="group">
                        <a href="<?php echo $CFG->wwwroot;?>/mod/forum/view.php?id=1"><button type="button" class="btn lgfont">微阅</button></a>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="<?php echo $CFG->wwwroot;?>/course/index.php"><button type="button" class="btn lgfont">微课</button></a>
                    </div>
                    <div class="btn-group" role="group">
                        <a href="#"><button type="button" class="btn lgfont">直播</button></a> 
                    </div>              
				</div>
            </div>
            
            <div class="navbar-form navbar-right" role="search">
  				<div class="form-group">   				      
  				 	<?php echo $OUTPUT->user_menu(); ?>
                    <!--button class="btn btn-default lgfont"><span class="glyphicon glyphicon-search"></span></button>
                    <input type="text" class="form-control" placeholder="搜索" -->                     
  				</div>               
			</div>
		</div>      
	</nav>
	
    <div class="clear"></div>
    <div class="loginmask"></div>
    
    <div id="loginalert"  style="margin-top:100px;">
    	<div class="pd20 loginpd">
    		<h3 style="padding:20px 0px 5px 0px"><i class="closealert fr"></i>
        		<div class="clear"></div>
        	</h3>
        	<div class="loginwrap">
				<div class="loginh">
                	<div class="fl">会员登录</div>
              	</div>
          		<h3>
                	<span class="login_warning">用户名或密码错误</span>
            		<div class="clear"></div>
          		</h3>
          		<div class="clear"></div>
          		<form action="<?php echo $CFG->wwwroot;?>/login/index.php" method="post" id="login">
            		<div class="logininput">
              			<input style="height:50px" type="text" name="username" id="username" class="loginusername" value="用户名" />
              			<input style="height:50px" type="text" class="loginuserpasswordt" value="密码" />
              			<input style="height:50px; display:none" type="password" name="password" id="password" class="loginuserpasswordp" style="display:none" />
            		</div>
            		<div class="loginbtn">
              			<div class="loginsubmit fl">
                			<input type="submit" value="登录" style="margin:auto; height:50px;" />
                        </div>
              			<div class="fr1"><a href="#">忘记密码?</a></div>
              			<div class="clear"></div>
            		</div>
          		</form>
        	</div>
      	</div>
	</div>
</header>

<div style="height:40px;"></div>

<div id="page" class="container-fluid">
    <?php echo $OUTPUT->full_header(); ?>
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
                <?php echo $OUTPUT->blocks('side-pre', $sidepre); ?><!--左边 -->
            </div>
        </div>
        <?php echo $OUTPUT->blocks('side-post', $sidepost); ?><!--右边 -->
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
