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
    
    <link rel="stylesheet" href="../moodle/theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="../moodle/theme/more/style/navstyle.css" /><!-- 全局-->
    <link rel="stylesheet" href="../moodle/theme/more/style/indexpage/index-course-style.css" />
    <link rel="stylesheet" href="../moodle/theme/more/style/indexpage/index-style.css" />  

    <script src="../moodle/theme/more/js/jquery-1.11.3.min.js"></script>
	<script src="../moodle/theme/more/js/bootstrap.min.js"></script>
	<script src="../moodle/theme/more/js/slider.js"></script>
	<script src="../moodle/theme/more/js/jquery.easing.min.js"></script>
	<script src="../moodle/theme/more/js/custom.js"></script>
    <script>
		$(document).ready(function() {
			$('.sort-item').click(function() {
				$('.sort-item').removeClass('active');
				$(this).addClass('active');
			});
		});
	</script>
    <script>
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
	<script>  
		//判断当前浏览器	
		if(navigator.userAgent.indexOf("MSIE")>0){
			if(navigator.userAgent.indexOf("MSIE 6.0")>0){   
				// alert("ie6");    
				window.location.href="download_firefox.html";
			}   
			if(navigator.userAgent.indexOf("MSIE 7.0")>0){  
				// alert("ie7");   
				window.location.href="download_firefox.html";
			}   
			if(navigator.userAgent.indexOf("MSIE 8.0")>0){
				window.location.href="download_firefox.html";
			}   
			if(navigator.userAgent.indexOf("MSIE 9.0")>0){
				// alert("ie8");  
				window.location.href="download_firefox.html";
			}   
		}
        var isChrome=navigator.userAgent.indexOf("Firefox") != -1?true:false;;
        if(!isChrome) {
			//document.write('Chrome: '+isChrome);
			//window.location.href="download_chrome.html";
			//window.location.href="download_firefox.html";
		}	      
</script>
<script>
$(document).ready(function(){
	//聊天室 START 20160314
	//适配不同大小偏移值
	var winW=$(window).width();
	var winH=$(window).height();
	var leftval = (winW-900)/2;	
	var topval = (winH-600)/3;	
	$('.chat-box').css({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
	//适配不同大小偏移值 end	
	var chatbox=false;
	$('.elevator-weixin').click(function(){
		if(chatbox==false){
			$('.chat-box1').append('<iframe src="chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
			chatbox=true;
		}
		$('.chat-box1').show();	
	})
	$('#chat-close').click(function(){
		$('.chat-box1').hide();
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
	//笔记20160314
	var note_personal = false
	$('#mynote-btn').click(function(){
		if(note_personal == false)
		{
			$('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_personal.php" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
			note_personal = true;
		}
						
		$('.chat-box2').show();	
		
	})
	//笔记
	$('#chat-close2').click(function(){
		$('.chat-box2').hide();
	})

	//网站搜索
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
		var search_type = $("#search_type").text();
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
});

//回车事件
document.onkeydown = function (e) {
	var theEvent = window.event || e;
	var code = theEvent.keyCode || theEvent.which;
	if ( $('#search_param').val() != '' && code == 13) {
		$("#search_btn").click();
	}
}

</script>

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>
<header role="banner">
	<nav class="navstyle navbar-fixed-top">
			<div class="nav-main">
				<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
				<ul class="nav-main-li">
					<a href="<?php echo $CFG->wwwroot;?>">
						<li class="li-normol">首页</li>
					</a>
					<a href="<?php echo $CFG->wwwroot;?>/microread/">
						<li class="li-normol">微阅</li>
					</a>
					<a href="<?php echo $CFG->wwwroot;?>/course/index.php">
						<li class="li-normol">微课</li>
					</a>
					<a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">
						<li class="li-normol">直播</li>
					</a>
				</ul>

				<!--div class="search-box">
					<input type="text" class="form-control" placeholder="搜索">
					<button class="btn btn-default "><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
				</div-->
				<div class="usermenu-box">
					<?php echo $OUTPUT->user_menu(); ?>
				</div>

				<div class="search-box">
					<div class="input-group">
						<div class="input-group-btn">
							<button type="button" id="search_type" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #000000;">课程<span class="caret"></span></button>
							<ul class="dropdown-menu">
								<li><a href="#">课程</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="#">书籍</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="#">文档</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="#">图片</a></li>
							</ul>
						</div><!-- /btn-group -->
						<input type="text"  id="search_param" class="form-control" >
					</div><!-- /input-group -->
					<button class="btn btn-default searchbtn" id="search_btn" ><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
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
    
	<!--11月2日新加内容结束-->
	
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
</header>
<!--11月12日v0.1内容开始-->

<!--2016年1月10日 主页内容改变 郑栩基-->
<!--课程四大分类-->
		<div class="divison">
			<h1>开始学习</h1>
			<p>打开你感兴趣的课程，开始课程之路吧，路漫漫其修远兮</p>
		</div>
		
		
	

                    <?php
                    //echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                   // echo $OUTPUT->course_content_footer();
                    ?>
               
                <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
            
        <?php //echo $OUTPUT->blocks('side-post', $sidepost); ?>
    	<!------全部课程@end------>
		<?php global $DB; global $CFG; $result_course_Count = $DB->get_records_sql('SELECT id FROM mdl_course where visible=1');
		$course_count = count($result_course_Count)>0 ? (count($result_course_Count)-1) : 0;
		echo '<div class="btn-all-course-container">
				<a class="btn btn-default btn-all-course" href="'.$CFG->wwwroot.'/course/index.php" role="button">全部课程 <span>('.$course_count.')</span></a>
			 </div>' ?>
		<!------全部课程@end------>

		<!--
    	<div class="divison2"></div>
		-->
		<!-- 学习排行榜开始 毛英东 20160414-->
		<div class="charts-wrap">
			<h1 class="charts">学习排行榜</h1>
			<div class="course-charts-container">
				<ul class="course-charts list-unstyled">
					<?php
						global $DB;
						$study_rank_users = $DB -> get_records_sql('select r.id, u.*, r.complete_count, r.complete_time from mdl_user as u,   mdl_course_index_rank_my as r where r.userid=u.id order by r.complete_count desc, r.complete_time asc limit 0 , 10');
						$rank_i = 1;
						foreach($study_rank_users as $rank_user ){
							$str1 = $OUTPUT->user_picture($study_rank_users[$rank_user->id],array('link' => false,'visibletoscreenreaders' => false));
							echo '<li class="col-lg-2dot4">
								<a href="#">
									<!-- '.$rank_user->id.' -->
									'.$str1.'
									<h2>NO.'.$rank_i .' '.$rank_user->firstname.'</h2>
								</a>
							</li>';
							$rank_i++;
						}
					?>
				</ul>
			</div>
		</div>
		<!-- 学习排行榜 结束 -->
		
		<!--底部导航条-->
		<nav class="navstyle-bottom navbar-static-bottom"></nav>
		<!--底部导航条 end-->
		<?php 
			if(isloggedin()){
				echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
					<a class="elevator-weixin" style="cursor:pointer"></a>
					<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
					<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
					<a class="elevator-top" href="#"></a>
					</div>';
			}
			else{
				echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-top" href="#"></a>
					</div>';
			}
			?>
		
		
		<div class="chat-box chat-box1">
			<div class="chat-head">
				<p>聊天室</p>
				<p id="chat-close" class="close">x</p>
			</div>
		</div>
		<div class="chat-box chat-box2">
				<div class="chat-head">
					<p>个人笔记</p>
					<p id="chat-close2" class="close">x</p>
				</div>
			</div>
		<div class="mask"></div>
   
   <?php echo $OUTPUT->standard_end_of_body_html() ?>
  

</body>
</html>
