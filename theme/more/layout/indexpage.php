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


	<!-- start	新增-->
	<link rel="stylesheet" href="../moodle/theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
	<link href="../moodle/theme/more/style/indexpage/indexpage.css" rel="stylesheet" type="text/css"/>
	<link href="../moodle/theme/more/style/alertstyle.css" rel="stylesheet" type="text/css"/>

	<script src="../moodle/theme/more/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
	<!-- end 新增-->

	<script src="../moodle/theme/more/js/slider.js"></script>
	<script src="../moodle/theme/more/js/jquery.easing.min.js"></script>
	<script src="../moodle/theme/more/js/custom.js"></script>

	<style>
		/* start 用户昵称*/
		.usermenu .moodle-actionmenu .toggle-display .userbutton .usertext {
			width: 80px;
			font-size: 14px;
			line-height:1.4em;
			text-align: left;
		}
		.title-p{
			font-size: 10px !important;
			position:relative;
			line-height:1.4em;
			/* 3 times the line-height to show 3 lines */
			height:2.8em;
			overflow:hidden;}
		.title-p::after {
			content:"...";
			font-weight:bold;
			position:absolute;
			bottom:0;
			right:0;
			padding:0 0px 1px 45px;
			background:url(<?php echo $CFG->wwwroot;?>/theme/more/img/ellipsis_bg.png) repeat-y;
		}
		/* end 用户昵称*/
		.nav-bottom {margin-top: 0px !important;}
	</style>
	<script>
		$(document).ready(function() {
			//start 导航条用户名称字数控制
			var num = $('.usertext').text();
			if (num.length > 10) {
				$('.usertext').addClass('title-p');
			}
			//end
		});
	</script>
	<!-- start 新增-->
	<!-- 轮播广告 -->
	<script type="text/javascript">
		$(function() {
			var bannerSlider = new Slider($('#banner'), {
				time: 3000,
				delay: 400,
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
	<!-- 轮播广告 end-->
	<script type="text/javascript">
		$(function(){
			//导航菜单动作
			$('.fenlei ul .l1').mouseenter(function(){$('.fenlei ul li').css('background','#ffffff');
				$('.fenleiright').fadeOut(0,0.8);
				$(this).stop().animate().siblings().stop().animate();
				$(this).siblings().css('background','#F5F5F5');
				$('.fenleiright1').fadeTo(0,0.8).stop().animate({'width':'289px'},300);
			}).mouseleave(function(){
				$('.fenlei ul li').stop().animate()
			});

			$('.fenlei ul .l2').mouseenter(function(){ $('.fenlei ul li').css('background','#ffffff');
				$('.fenleiright').fadeOut(0,0.8);
				//$(this).stop().animate({'height':'157px'},300).siblings().stop().animate({'height':'44px'},300);
				$(this).stop().animate().siblings().stop().animate();
				$(this).siblings().css('background','#F5F5F5');
				$('.fenleiright2').fadeTo(0,0.8).stop().animate({'width':'289px'},300);
			}).mouseleave(function(){
				$('.fenlei ul li').stop().animate()

			});

			$('.fenlei ul .l3').mouseenter(function(){$('.fenlei ul li').css('background','#ffffff');
				$('.fenleiright').fadeOut(0,0.8);
				$(this).stop().animate().siblings().stop().animate();
				$(this).siblings().css('background','#F5F5F5');
				$('.fenleiright3').fadeTo(0,0.8).stop().animate({'width':'289px'},300);
			}).mouseleave(function(){
				$('.fenlei ul li').stop().animate()

			});

			$('.fenlei ul .l4').mouseenter(function(){$('.fenlei ul li').css('background','#ffffff');
				$('.fenleiright').fadeOut(0,0.8);
				$(this).stop().animate().siblings().stop().animate();
				$(this).siblings().css('background','#F5F5F5');
				$('.fenleiright4').fadeTo(0,0.8).stop().animate({'width':'289px'},300);
			}).mouseleave(function(){
				$('.fenlei ul li').stop().animate()

			});

			$('.fenlei ul .l5').mouseenter(function(){$('.fenlei ul li').css('background','#ffffff');
				$('.fenleiright').fadeOut(0,0.8);
				$(this).stop().animate().siblings().stop().animate();
				$(this).siblings().css('background','#F5F5F5');
				$('.fenleiright5').fadeTo(0,0.8).stop().animate({'width':'289px'},300);
			}).mouseleave(function(){
				$('.fenlei ul li').stop().animate()

			});
			//导航菜单动作end
			$('.navLeft').mouseleave(function(){
				$('.fenleiright').stop().animate({'width':'0px'},300);$('.fenlei ul li').css('background','#ffffff');
			})
		})
	</script>
	<script>
		$(document).ready(function() {
			$('.login ul li a').css("color","#000");
			$('.usertext').css("color","#000");
			$('#searchtype a').click(function() {
				$('#searchtypebtn').text($(this).text());
				$('#searchtypebtn').append('<span class="caret"></span>');
			});
			$('.course').mouseover(function(){
				$(this).children('a').children('.hidediv').show();
				$(this).children('.coursetips').removeClass('m-t');
			})
			$('.course').mouseout(function(){
				$(this).children('a').children('.hidediv').hide();
				$(this).children('.coursetips').addClass('m-t');
			});
			//start 网站搜索
			$('.dropdown-toggle').click(function(){
				if($('.search-box .dropdown-menu').hasClass('show'))
					$('.search-box .dropdown-menu').removeClass('show');
				else
					$('.search-box .dropdown-menu').addClass('show');
			});
			$('.search-box .dropdown-menu li').click(function(){
				$('.search-box .dropdown-toggle').text($(this).children('a').text());
				$('.search-box .dropdown-toggle').append('<span class="caret"></span>');
				$('.search-box .dropdown-menu').removeClass('show');
			});
			$("#search_btn").click(function(){
				var search_type = $("#searchtypebtn").text();
				var search_param = $("#search_param").val();
				switch(search_type){
					case '课程':
						window.open( "<?php echo $CFG->wwwroot;?>/course/mysearch.php?searchType=课程名&searchParam="+search_param);
						break;
					case '书籍':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/bookroom/searchresult.php?searchType=标题&searchParam="+search_param);
						break;
					case '文档':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/docroom/searchresult.php?searchType=标题&searchParam="+search_param);
						break;
					case '图片':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/picroom/image-search.php?word="+search_param);
						break;
					default:
						break;
				}
			});
			//end 网站搜索

		});

		//start 回车事件
		document.onkeydown = function (e) {
			var theEvent = window.event || e;
			var code = theEvent.keyCode || theEvent.which;
			if ( $('#search_param').val() != '' && code == 13) {
				$("#search_btn").click();
			}
		}
		//end 回车事件
	</script>
	<!-- end 新增-->

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<header role="banner">
	<!-- start 新增-->
	<nav>
		<div class="center">
			<div class="l-box">
				<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo1.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
			</div>
			<div class="r-box">
				<div class="a-box">
					<?php echo $OUTPUT->user_menu(); ?>
				</div>
				<button class="btn btn-info searchbtn" id="search_btn" ><span class="glyphicon glyphicon-search"></span></button>
				<input class="form-control search" id="search_param" placeholder="请输入关键词..." />

				<!--下拉菜单-->
				<div class="btn-group">
					<button id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">课程<span class="caret"></span></button>
					<ul id="searchtype" class="dropdown-menu">
						<li><a href="#">课程</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">文档</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">图片</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">书籍</a></li>
					</ul>
				</div>
				<!--下拉菜单 end-->

			</div>
		</div>
	</nav>

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
						<div class="fr1"><a href="#">忘记密码?</a><a href="<?php echo $CFG->wwwroot;?>/register/index.php">用户注册</a></div>
						<div class="clear"></div>
					</div>
				</form>
			</div>
		</div>
	</div>


	<div id="navOut">
		<div class="nav">
			<div class="navLeft">
				<p>全部课程</p>
				<div class="fenlei">
					<ul>
						<?php
							$category_two_html_echo = my_print_category_one();//输出一级分类,同时返回二级分类的hmtl代码以备下文输出

							//输出一级分类
							function my_print_category_one(){
								global $CFG;
								$category_two_html = '';//二级分类html
								$categorys = my_category_one();//获取一级分类
								$output = '';
								$n = 1;
								foreach ($categorys as $category) {
									if($category->id == null){
										continue;
									}
									$category_twos = my_category_two($category->id);//获取二级分类
									$output .= '<li class="l'.$n.'">
													<dl class="fenleiLeft">
														<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$category->id.'"><dt>'.$category->name.'</dt></a>
														<dd>';
									$category_two_html .= ' <!--分类'.$n.'-->
	            									  <div class="fenleiright fenleiright'.$n.'">';
									$category_two_count = 0;
									$category_two_html .= '<dl class="flright">
															 <dd>';
									foreach($category_twos as $category_two){
										$category_two_count++;
										if($category_two_count<=3){
											$output .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$category->id.'&dep2categoryid='.$category_two->id.'">'.$category_two->name.'</a>';
										}
										$category_two_html .= '<a href="'.$CFG->wwwroot.'/course/index.php?dep1categoryid='.$category->id.'&dep2categoryid='.$category_two->id.'">
																	'.$category_two->name.'
															   </a>';
									}
									$category_two_html .= '		</dd>
															</dl>
															</div>
	            											<!--分类'.$n.' end-->';
									$output .= '
														</dd>
													</dl>
												</li>';
									$n++;
								}
								echo $output;//输出一级分类
								return $category_two_html;//返回二级分类HTML
							}

							//一级分类
							function my_category_one(){
								global $DB;
								$sql = "select ic.course_category_id as id,cc.`name` from mdl_index_course_category ic
										left join mdl_course_categories cc on ic.course_category_id = cc.id";
								$categorys = $DB->get_records_sql($sql);
//								$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder;');
								return $categorys;
							}
							//二级分类
							function my_category_two($categoryID){
								global $DB;
								$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=2 and visible=1 and parent='.$categoryID.' ORDER BY sortorder;');
								return $categorys;
							}

						?>
					</ul>
				</div>
				<?php
					echo $category_two_html_echo;//输出二级分类（这是在输出一级分类时生成的html）
				?>

			</div>
			<ul class="navRight">
				<li><a class="active" href="<?php echo $CFG->wwwroot;?>">首页</a></li>
				<li><a href="<?php echo $CFG->wwwroot;?>/course/index.php">微课</a></li>
				<li><a href="<?php echo $CFG->wwwroot;?>/microread/">微阅</a></li>
				<li><a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">直播</a></li>
				<li><a href="#"></a></li>
			</ul>
		</div>
	</div>
	<!-- 轮播广告 -->
	<div id="banner" class="flexslider">
		<ul class="slides">
			<?php
				global $DB;
				$pictures = $DB->get_records_sql("select * from mdl_index_picture ");

				$picCount = 0;//图片的数量
				foreach($pictures as $picture){
					if($picture->pictureurl != null){
						$str = '<li style="background-color:'.$picture->picturecolor.'" >
									<a title="" target="_blank" href="'.$picture->picturelink.'">
										<img width="1920" alt="" style="background: url('.$picture->pictureurl.') no-repeat center;"  src="'. $CFG->wwwroot.'/theme/more/pix/indexpage/alpha.png">
									</a>
								</li>';
						echo $str;
						$picCount = $picCount+1;
					}
				}
			?>
		</ul>
		<!--ul class="flex-direction-nav">
			<li><a class="flex-prev" href="javascript:;">Previous</a></li>
			<li><a class="flex-next" href="javascript:;">Next</a></li>
		</ul-->
		<ol id="bannerCtrl" class="flex-control-nav flex-control-paging">
			<?php
				for($picCount;$picCount>0;$picCount--){
					echo '<li><a>1</a></li>';
				}
			?>
		</ol>
	</div>
	<!-- 轮播广告 end-->
	<!-- end 新增-->

	<!--
	<nav class="navstyle navbar-fixed-top">

			<div class="nav-main">
				<img id="logo" src="<?php //echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
				<ul class="nav-main-li">
					<a href="<?php //echo $CFG->wwwroot;?>">
						<li class="li-normol">首页</li>
					</a>
					<a href="<?php //echo $CFG->wwwroot;?>/microread/">
						<li class="li-normol">微阅</li>
					</a>
					<a href="<?php //echo $CFG->wwwroot;?>/course/index.php">
						<li class="li-normol">微课</li>
					</a>
					<a href="<?php //echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">
						<li class="li-normol">直播</li>
					</a>
				</ul>

				<div class="search-box">
					<input type="text" class="form-control" placeholder="搜索">
					<button class="btn btn-default "><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
				</div>
				<div class="usermenu-box">
					<?php //echo $OUTPUT->user_menu(); ?>
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
              			<div class="fr1"><a href="#">忘记密码?</a> <a href="register/index.php">用户注册</a></div>
					
              			<div class="clear"></div>
            		</div>
          		</form>
        	</div>
      	</div>
	</div>
	-->
    
	<!--11月2日新加内容结束-->

	<!--
	<div id="banner_tabs" class="flexslider">
		<div class="slides">
			<?php
				global $DB;
				$pictures = $DB->get_records_sql("select * from mdl_index_picture ");

				$picCount = 0;//图片的数量
				foreach($pictures as $picture){

					if($picture->pictureurl != null){
						$str = '<div style="background-color:'.$picture->picturecolor.'">
								<a title="" target="_blank" href="'.$picture->picturelink.'">
									<img width="1920" alt="" style="background: url('.$picture->pictureurl.') no-repeat center;" src="'. $CFG->wwwroot.'/theme/more/pix/indexpage/alpha.png">
								</a>
							</div>';
						echo $str;
						$picCount = $picCount+1;
					}

				}
			?>

		</div>
		<ul class="flex-direction-nav">
			<li><a class="flex-prev" href="javascript:;">Previous</a></li>
			<li><a class="flex-next" href="javascript:;">Next</a></li>
		</ul>
		<ol id="bannerCtrl" class="flex-control-nav flex-control-paging">
			<?php
				for($picCount;$picCount>0;$picCount--){
					echo '<li><a>1</a></li>';
				}
			?>
		</ol>
	</div>
	-->
</header>

<!--2016年1月10日 主页内容改变 郑栩基-->
<!--课程四大分类-->

		<?php
			//echo $OUTPUT->course_content_header();
			echo $OUTPUT->main_content();
		   // echo $OUTPUT->course_content_footer();
		?>
               
        <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
        <?php //echo $OUTPUT->blocks('side-post', $sidepost); ?>

		<!--底部导航条-->
		<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
		<?php require_once("includes/bottom_info.php"); ?>
		<!--底部导航条 end-->

		<!--右下角按钮-->
		<?php require_once("includes/link_button.php"); ?>
		<!--右下角按钮 end-->

<?php echo $OUTPUT->standard_end_of_body_html() ?>

</body>
</html>
