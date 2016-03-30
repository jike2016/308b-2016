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
    <title>column4<?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>
    
    
	<!--<link href="<?php echo $CFG->wwwroot;?>/themechange/css/style.css" rel='stylesheet' type="text/css">
    <link href="<?php echo $CFG->wwwroot;?>/themechange/css/style_index.css" rel='stylesheet' type="text/css"-->
    <link href="../moodle/theme/clean/style/bootstrap.css" rel='stylesheet' type="text/css">
	 
    <link rel="stylesheet" href="../moodle/theme/clean/style/style1.css" />
	<link rel="stylesheet" href="../moodle/theme/clean/style/class.css" />
    <link rel="stylesheet" href="../moodle/theme/clean/style/index-style.css" /> 
    <link rel="stylesheet" href="../moodle/theme/clean/style/style.css" />   
    <link href="../moodle/theme/clean/style/coursecategory.css" rel="stylesheet" type="text/css" /><!--coursecategory.php   对导航条参生影响，暂时定位为全局-->
    <link href="../moodle/theme/clean/style/alertstyle.css" rel="stylesheet" type="text/css" />
    
    

    <script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery-1.11.3.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/bootstrap.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/slider.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/jquery.easing.min.js"></script>
	<script src="<?php echo $CFG->wwwroot;?>/themechange/js/custom.js"></script>
    <script>
		$(document).ready(function() {
			$('.sort-item').click(function() {
				$('.sort-item').removeClass('active');
				$(this).addClass('active');
			});
		});
	</script>

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<header role="banner">
	<nav class="navbar navbar-inverse navbar-fixed-top" style="margin:0px; padding:0px">
		<div style="width:100%; margin:auto"> 
               	
        <form class="navbar-form navbar-left" style="width:65%; margin:0px 0px 0px 5%">
            <div  style="box-sizing: content-box;float:left; width:15%;  padding:0px 2%;margin-top: 5px;" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
				<img src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" style="width:100%;" width:100%;>
			</div>
			<div class="btn-group btn-group-justified" style="float:left;width:70%; " >
				<div class="btn-group" role="group">
    				<a href="<?php echo $CFG->wwwroot;?>"><button type="button" class="btn lgfont">首页</button></a>
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
            
            <form class="navbar-form navbar-right" role="search" style=" width:25%;margin:0px 5% 0px 0px;">
  				<div class="form-group" style=" margin:8px 0px; float:right">
    			<input type="text" class="form-control" style="box-sizing: content-box;height:30px;" placeholder="Search">       
  				 <?php echo $OUTPUT->user_menu(); ?>
  				</div>
                
			</form>
		</div>      
	</nav>
	
<!--11月2日新加内容开始-->
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
      <h3><span class="login_warning">用户名或密码错误</span>
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
<div style="height:80px;"></div>
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
<!--11月12日v0.1内容开始-->

<!--2016年1月10日 主页内容改变 郑栩基-->
<!--课程四大分类-->
		<div class="divison">
			<h1>开始学习</h1>
			<p>打开你感兴趣的课程，开始课程之路吧，路漫漫其修远兮</p>
		</div>
		<div class="catagory">
			<div class="catagory-item" style="">
				<a href="#" class="catagory-title">
					<span class="catagory-icon">            
            			<img src="../moodle/theme/clean/pix/Home_Catagory_1.png" width="72" height="75">
        			</span>
					<h3>军事业务</h3>
				</a>

				<div class="catagory-list">
					<a href="#">业务培训 (0)</a>
					<a href="#">业务知识培训 (0)</a>
					<a href="#">军队建设 (0)</a>
					<a href="#">计算机技术 (2)</a>
				</div>
			</div>

			<div class="catagory-item" style="">
				<a href="#" class="catagory-title">
					<span class="catagory-icon">            
            			<img src="../moodle/theme/clean/pix/Home_Catagory_2.png" width="72" height="75">
        			</span>
					<h3>政治工作</h3>
				</a>

				<div class="catagory-list">
					<a href="#">政治学习 (1)</a>
					<a href="#">思想教育 (1)</a>
					<a href="#">军队建设 (0)</a>
				</div>
			</div>

			<div class="catagory-item" style="">
				<a href="#" class="catagory-title">
					<span class="catagory-icon">       
            			<img src="../moodle/theme/clean/pix/Home_Catagory_3.png" width="72" height="75">
        			</span>
					<h3>后勤保障</h3>
				</a>

				<div class="catagory-list">
					<a href="#">职称培训 (0)</a>
					<a href="#">职称评定 (1)</a>
				</div>
			</div>
			<div class="catagory-item" style="margin-right:0;">
				<a href="#" class="catagory-title">
					<span class="catagory-icon">         
            			<img src="../moodle/theme/clean/pix/Home_Catagory_4.png" width="72" height="75">
       		 		</span>
					<h3>综合应用</h3>
				</a>
				<div class="catagory-list">
					<a href="#">数学 (1)</a>
					<a href="#">实用英语口语 (0)</a>
					<a href="#">代数 (0)</a>
					<a href="#">物理 (1)</a>
					<a href="#">历史 (1)</a>
					<a href="#">上下五千年 (0)</a>
					<a href="#">艺术 (0)</a>
				</div>
			</div>

			<div class="clr"></div>
		</div>
		
		<div class="course-content">
			<div class="course-tool-bar clearfix">
				<div class="tool-left l">
					<a href="javascript:void(0)" class="sort-item">最新</a>
					<a href="javascript:void(0)" class="sort-item active">最热</a>
				</div>

				<div class="tool-right r">
					<span class="tool-item tool-pager">
                             <span class="pager-num">
                            <b class="pager-cur">1</b>/<em class="pager-total">24</em>
                        </span>
					<a href="javascript:void(0)" class="pager-action pager-prev hide-text disabled">上一页</a>

					<a href="#" class="pager-action pager-next hide-text">下一页</a>
					</span>
				</div>
			</div>

			<div class="course-list">

				<!--课程列表-->
				<div class="js-course-lists">
					<ul>
						<!--第一行-->
						<li class="course-one linefrist">
							<a href="#" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="HTML+CSS基础课程" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>HTML+CSS基础课程</span></h5>
								<div class="tips">
									<p class="text-ellipsis">8小时带领大家步步深入学习标签的基础知识，掌握各种样式的基本用法。</p>

									<span class="l ml20"> 215913人学习</span>
								</div>
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="#" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="Java入门第一季" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>Java入门第一季</span></h5>
								<div class="tips">
									<p class="text-ellipsis">本课程会从Java环境搭建、基础语法开始，带你进入神秘的进入Java世界</p>

									<span class="l ml20">208477人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="#" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="PS入门教程——新手过招" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>PS入门教程——新手过招</span></h5>
								<div class="tips">
									<p class="text-ellipsis">PS的基本使用方法，带你入门。</p>

									<span class="l ml20">173025人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="#" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="C语言入门" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>C语言入门</span></h5>
								<div class="tips">
									<p class="text-ellipsis">进入编程世界的必修课-C语言</p>

									<span class="l ml20">156672人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>
						<!--第一行end-->

						<!--第二行-->
						<li class="course-one  linefrist">
							<a href="#" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="JavaScript入门篇" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>JavaScript入门篇</span></h5>
								<div class="tips">
									<p class="text-ellipsis">做为一名Web攻城狮的必备技术，让您从糊涂到明白，快速认识JavaScript。</p>

									<span class="l ml20">129024人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/159" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="PS大神通关教程" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>PS大神通关教程</span></h5>
								<div class="tips">
									<p class="text-ellipsis">一路的升级打怪，让你实现ps菜鸟到大神的炫酷逆袭。</p>

									<span class="l ml20">126556人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/54" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="PHP入门篇" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>PHP入门篇</span></h5>
								<div class="tips">
									<p class="text-ellipsis">3小时轻松帮您快速掌握PHP语言基础知识，为后续PHP进级课程学习打下基础。</p>

									<span class="l ml20">113749人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/96" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="Android攻城狮的第一门课（入门篇）" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>Android攻城狮的第一门课（入门篇）</span></h5>
								<div class="tips">
									<p class="text-ellipsis">想快速进入Android开发领域的程序猿的首选课程</p>

									<span class="l ml20"> 112014人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>
						<!--第二行end-->

						<!--第三行-->
						<li class="course-one  linefrist">
							<a href="/view/124" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="Java入门第二季" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>Java入门第二季</span></h5>
								<div class="tips">
									<p class="text-ellipsis">掌握面向对象的基本原则以及 Java 面向对象编程基本实现原理</p>

									<span class="l ml20">106835人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/10" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="JavaScript进阶篇" src="../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>JavaScript进阶篇</span></h5>
								<div class="tips">
									<p class="text-ellipsis">JavaScript带您进入网页动态交互世界，为网页增色，为技术加分。</p>

									<span class="l ml20"> 97853人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/11" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="jQuery基础课程" src=" ../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>jQuery基础课程</span></h5>
								<div class="tips">
									<p class="text-ellipsis">加入jQuery基础课程学习，有效提高网站前端开发速度！</p>

									<span class="l ml20">94608人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>

						<li class="course-one">
							<a href="/view/177" target="_self">
								<div class="course-list-img">
									<img width="240" height="135" alt="Python入门" src=" ../moodle/theme/clean/pix/1.jpg">
								</div>
								<h5><span>Python入门</span></h5>
								<div class="tips">
									<p class="text-ellipsis">未来您要会的优雅、明确、简单语言</p>

									<span class="l ml20">92464人学习</span>
								</div>
								<!--span class="time-label">1小时 | 高级</span-->
								<b class="follow-label">跟我学</b>
							</a>
						</li>
						<!--第三行end-->
					</ul>
				</div>
				<!--课程列表end-->

				<!--换页按钮+-->
				<div class="page">
					<span class="disabled_page">首页</span>
					<span class="disabled_page">上一页</span>
					<a href="javascript:void(0)" class="active">1</a>
					<a href="/course/list?page=2">2</a><a href="/course/list?page=3">3</a>
					<a href="/course/list?page=4">4</a><a href="/course/list?page=5">5</a>
					<a href="/course/list?page=6">6</a><a href="/course/list?page=7">7</a>
					<a href="/course/list?page=2">下一页</a><a href="/course/list?page=24">尾页</a>
				</div>
				<!--换页按钮end-->
			</div>
		</div>
		
		<div class="divison2"></div>
		<div class="Learningcharts">
			<div class="lc-title"><h2>学习排行榜&nbsp;<span class="glyphicon glyphicon-signal"></span></h2></div>
			<div class="Learner">
				<!--第一名-->
				<div class="num-1">
					<div class="left">
						<p class="headimg"><img src=" ../moodle/theme/clean/pix/head.jpg"></p>
						<p class="num">NO.1</p>
					</div>
					<div class="right">
						<p class="learnername">张三丰</p>
						<div class="Learnerimg-box">
						<img src=" ../moodle/theme/clean/pix/learnner.jpg" alt="依米暖暖">
						</div>						
					</div>									
				</div>
				<!--第一名end-->
				
				<!--第二名-->
				<div class="num-1 num-2">
					<div class="left">
						<p class="num">NO.2</p>
					</div>
					<div class="right">
						<p class="learnername">张三丰</p>
						<div class="Learnerimg-box">
						<img src=" ../moodle/theme/clean/pix/learnner.jpg" alt="依米暖暖">
						</div>						
					</div>									
				</div>
				<!--第二名end-->
				
				<!--第三名-->
				<div class="num-1 num-3">
					<div class="left">
						<p class="num">NO.3</p>
					</div>
					<div class="right">
						<p class="learnername">张三丰</p>
						<div class="Learnerimg-box">
						<img src=" ../moodle/theme/clean/pix/learnner.jpg" alt="依米暖暖">
						</div>						
					</div>									
				</div>
				<!--第三名end-->
				
				<!--第四名-->
				<div class="num-1 normal">
					<div class="left">
						<p class="num">NO.4</p>
					</div>
					<div class="right">
						<p class="learnername">张三丰</p>
						<div class="Learnerimg-box">
						<img src=" ../moodle/theme/clean/pix/learnner.jpg" alt="依米暖暖">
						</div>						
					</div>									
				</div>
				<!--第四名end-->
				
				<!--第五名-->
				<div class="num-1 normal">
					<div class="left">
						<p class="num">NO.5</p>
					</div>
					<div class="right">
						<p class="learnername">张三丰</p>
						<div class="Learnerimg-box">
						<img src=" ../moodle/theme/clean/pix/learnner.jpg" alt="依米暖暖">
						</div>						
					</div>									
				</div>
				<!--第五名end-->								
			</div>
		</div>

		<div id="footer">
			<div class="waper">
				<div class="footerwaper clearfix">
					<div class="followus r">
					</div>
					<div class="footer_intro l">
						<div class="footer_link">
							<ul>
								<li><a href="http://www.imooc.com/" target="_blank">网站首页</a></li>
								<li><a href="http://www.imooc.com/about/job" target="_blank">人才招聘</a></li>
								<li> <a href="http://www.imooc.com/about/contact" target="_blank">联系我们</a></li>
								<li><a href="http://yun.imooc.com/" target="_blank">慕课云</a></li>
								<li><a href="http://www.imooc.com/about/us" target="_blank">关于我们</a></li>
								<li> <a href="http://www.imooc.com/about/recruit" target="_blank">讲师招募</a></li>
								<li> <a href="http://www.imooc.com/user/feedback" target="_blank">意见反馈</a></li>
								<li> <a href="http://www.imooc.com/about/friendly" target="_blank">友情链接</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-top" href="#" id="feedBack">
				<div class="elevator-app-box">
				</div>
			</a>
			<a class="elevator-msg" href="#"  id="feedBack">
				<div class="elevator-app-box">
				</div>
			</a>
		</div>
		
		<div class="mask"></div>


<!--2016年1月10日 主页内容改变 郑栩基 end-->

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
