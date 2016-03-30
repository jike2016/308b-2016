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
    <title>column5<?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/bootstrap-theme.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/style.css" rel='stylesheet' type="text/css">
	<link href="<?php echo $CFG->wwwroot;?>/themechange/css/style_courseindex.css" rel='stylesheet' type="text/css">
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

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
	
        <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
            <div class="row-fluid">
			<!--11月12日v0.1内容开始-->
				<div>
					<div style="width:100%; height:30px;"></div>

<div style="width:1200px; margin:auto; height:1000px">

	<div style="float:left; width:18%; padding:0px">    	
		<?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
             ?>
        <div style=" border:1px solid #ccc; width:100%;">
		
        </div>
        
        
		<div style="width:100%; margin:10px 0px;">

		</div>
                       
    </div>
    <!--left-->
    <div style="float:right; width:79%;">
    	<div style="float:left;">
        	<p>全部课程</p>
        </div>
        <div style="float:right; width:37%;">
        	<div class="btn-group" role="group" style="float:left">
				<button type="button" class="btn greenbutton">全部</button>
  				<button type="button" class="btn greenbutton">正在进行</button>
  				<button type="button" class="btn greenbutton">即将开始</button>
			</div>
            <div style="float:left; margin:5px 4px 5px 25px;"><p>排序:</p></div>
            <div class="dropdown" style="float:right">
  				<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">默认<span class="caret"></span>
  				</button>
  				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
    				<li><a href="#">默认</a></li>
    				<li><a href="#">按热门</a></li>
    				<li><a href="#">按最新</a></li>
  				</ul>
			</div>
        </div>    
        
        <div style="width:100%; height:129px; margin:50px 0px 20px 0px; background-color:#fff;">
        	<div style="float:left; margin:2px 2px"><img src="<?php echo $CFG->wwwroot;?>/themechange/images/c-1.jpg"></div>
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
        
        <div style="width:100%; height:129px; margin:20px 0px; background-color:#fff;">
        	<div style="float:left; margin:2px 2px"><img src="<?php echo $CFG->wwwroot;?>/themechange/images/c-3.jpg"></div>
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
        <div style="width:100%; height:129px; margin:50px 0px 20px 0px; background-color:#fff;">
        	<div style="float:left; margin:2px 2px"><img src="<?php echo $CFG->wwwroot;?>/themechange/images/c-1.jpg"></div>
            <div style="float:right; width:75%; margin:3px 5px;">
            	<div style="width:100%; height:23px;"><p style="font-size:18px;">唐诗经典</p></div>
                <div style="width:100%; height:23px;"><a href="#" class="agreen">浙江大学</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="agreen">胡可先、陶然</a></div>
                <div style="width:100%; height:30px; margin:5px 0px;">
                	<button class="btn btn-warning btn-sm active week">
                	<p><span class="glyphicon glyphicon-time" style="color:#fff;">&nbsp;</span></p><p>2015-10-8开课</p>
                	</button>
                </div>
                <div style="width:100%; height:20px; margin:5px 0px;">
                	<button class="btn btn-info" style="float:right;">查看课程</button>
                </div>
            </div>
    	</div>
        
        <div style="width:100%; height:129px; margin:20px 0px; background-color:#fff;">
        	<div style="float:left; margin:2px 2px"><img src="<?php echo $CFG->wwwroot;?>/themechange/images/c-3.jpg"></div>
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
    </div><!--right-->   
</div>
				</div>
			<!--11月12日v0.1内容结束-->
                <section id="region-main" class="<?php echo $regionmain; ?>">
                   
                </section>
                <?php
 				echo $OUTPUT->blocks('side-pre', $sidepre); 
				?>
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
<script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery-1.11.3.min.js"></script>
<script src="<?php echo $CFG->wwwroot;?>/themechange/js/bootstrap.min.js"></script>
</html>
