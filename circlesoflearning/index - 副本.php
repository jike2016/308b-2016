<?php

/**
 * Start 学习圈 朱子武 20160316
 */

if(empty($CFG))
{
//	require_once(dirname(dirname(__FILE__)).'/config.php');
	require("../config.php");
}

$page      = optional_param('page', 1, PARAM_INT);
$_SESSION['pageid'] = $page;

//echo contents();
//echo myconcerned();
//echo myrelated();
echo html_head();

function get_contents_all($content_page, $userid=null)
{
	global $DB;
	global $OUTPUT;
	global $CFG;
	global $USER;
	$contents = '';
	$content_page = $content_page * 10;
	if($userid)
	{
		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.subject, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id WHERE a.module = "blog" AND userid = '.$userid.' ORDER BY a.created DESC LIMIT '.$content_page.',10');
	}
	else
	{
		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.subject, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id WHERE a.module = "blog" ORDER BY a.created DESC LIMIT '.$content_page.',10');
	}
	foreach($allresult as $blogValue)
	{
		$user = $DB->get_record('user', array('id' => $blogValue->userid), '*', MUST_EXIST);
		$userIcon = $OUTPUT->user_picture (
			$user,
			array(
				'link' => false,
				'visibletoscreenreaders' => false
			)
		);
		$userIcon = str_replace('width="35" height="35"', " ", $userIcon);

		$contents .= '<li>
						<!-- 顶部 开始 -->
						<div class="trends-top">
							<!-- 左侧 -->
							<div class="col-lg-1">
								<!-- 头像 -->
								<a class="portarit" href="'.$CFG->wwwroot .'/circlesoflearning/index.php?userid='.$blogValue->userid.'">
									<!--<img src=" images/learnner.jpg" alt="" />-->
									'.$userIcon.'
								</a>
								<!-- 头像  end-->

								<!-- 关注 -->
								<div id="add-attendtion">
									<a id="followUser-btn" onclick="followUser('.$blogValue->userid.')" style="cursor:pointer"><span class="glyphicon glyphicon-plus"></span>&nbsp;'.follow_or_not($blogValue->userid).'</a>
								</div>
								<!-- 关注 end-->
							</div>

							<!-- 右侧 -->
							<div class="col-lg-11">
								<div class="user-info">
									<!-- 昵称 -->
									<a class="nickname" href="'.$CFG->wwwroot .'/circlesoflearning/index.php?userid='.$blogValue->userid.'">'.$blogValue->lastname.$blogValue->firstname.'</a>
								</div>

								<div class="trends-content">
								'.$blogValue->summary.'
								</div>
								';
		if(!$blogValue->pictures == '')
		{

			$pictures = (Array)json_decode($blogValue->pictures);
			if(count($pictures))
			{
				$contents .= '<!--显示图片代码-->
								<div class="trends-content">';
				foreach($pictures as $pictureValue)
				{
					$contents .= '	<div class="thumb">
										<!-- 缩略图 -->
										<img src="'.$pictureValue->name.'" data-pic-big="" alt="" />
									</div>
									<!-- 大图 -->
									<div class="pic-big">
										<img src="'.$pictureValue->name.'" alt="" />
									</div>';
				}
				$contents .= '		</div>
								<!--显示图片代码 end-->';
			}
		}

		$contents .= '			<div class="trends-date">
									'.userdate($blogValue->created,'%Y-%m-%d %H:%M').'
								</div>
							</div>
						</div>
						<!-- 顶部 结束 -->

						<!-- 底部 -->';

		$current = my_get_blog_evaluation_count($blogValue->id);

		if(($blogValue->userid == $USER->id) || ($USER->id == '2'))	{
			$contents .= '<ul class="trends-bottom list-unstyled">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">喜欢('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
							<li class="delete-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">删除</a></li>
					      </ul>';
		}
		else
		{
			$contents .= '<ul class="trends-bottom list-unstyled nodelete">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">喜欢('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
						  </ul>
';
		}

		$contents .= '<!--评论以及评论列表-->
						<div class="comment-banner">
							<div class="commentbox">
								<div class="mycomment">
									<textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
									<button id="commentBtnClick" class="commentBtnClick btn btn-info" value="'.$blogValue->id.'">发表评论</button>
								</div>';

//		$current_page = $_SESSION['pageid'];

		$contents .=	my_get_blog_evaluation($blogValue->id);

		if($current->count <=5)
		{
			$contents .= '
						</div>
					</div>
				<!--评论以及评论列表 end-->
			</li>';
		}
		else
		{
			$contents .= '
						</div>
							<a href="#">查看更多</a>
						</div>
						<!--评论以及评论列表 end-->
					</li>';
		}

	}
	return $contents;
}

/**  START  获取是否已关注 朱子武 20160311*/
function follow_or_not($concernid)
{
	global $DB;
	global $USER;
	$res = '';
	$myresult = $DB->get_records_sql('SELECT id FROM mdl_blog_concern_me_my WHERE concernid = ? AND userid = ?', array($concernid,$USER->id));
	if(count($myresult))
	{
		$res = '取消';
	}
	else
	{
		$res = '关注';
	}
	return $res;
}
/**  END  获取是否已关注 朱子武 20160311*/

/**  START  添加喜欢数  朱子武 20160309*/
function my_get_blog_like_count($blogid)
{
	global $DB;
	$res = '0';
	$mylikecount = $DB->get_records_sql('SELECT id, blogid, likecount FROM mdl_blog_like_count_my WHERE blogid = ?', array($blogid));
	foreach($mylikecount as $value)
	{
		$res = $value->likecount;
	}
	return $res;
}
/**  END  添加喜欢数  朱子武 20160309*/

/**  START  添加转发数  朱子武 20160309*/
function my_get_blog_forwarded_count($blogid)
{
	global $DB;
	$res = '0';
	$originalid = '';
	$my_original = $DB->get_records_sql('SELECT id, originalblogid FROM mdl_circles_of_learning WHERE id = '.$blogid);
	foreach($my_original as $value)
	{
		$originalid = $value->originalblogid;
	}

	if($originalid)
	{
		$my_original_count = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = '.$originalid);
		foreach($my_original_count as $original_value)
		{
			$res = $original_value->forwardedcount;
		}
	}

	return $res;
}
/**  END  添加转发数  朱子武 20160309*/

//    获取评价数目页数
function my_get_blog_evaluation_count($blogid)
{
	global $DB;
	$evaluation = $DB->get_records_sql('SELECT id FROM mdl_learning_comments WHERE itemid = '.$blogid);
	$mycount = count($evaluation);
	$evaluationSum = new stdClass();
	$evaluationSum->count = $mycount;
	$mycount = ceil($mycount/5);
	$evaluationSum->page = ($mycount <= 1 ? 1: $mycount);
	return $evaluationSum;

//	return 1;
}

//    输出页码
function my_get_blog_evaluation_current_count($count_page, $blogid)
{
	global $CFG;
	$res = '';
	for($num = 1; $num <= $count_page; $num ++)
	{
		 $res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'">'.$num.'</a></li>';
	}
	return $res;
}

//   获取博客评价
function my_get_blog_evaluation($blogid)
{

//	$my_page = $current_page * 10;
//	if($my_page < 0) return '';
	global $DB;
	global $OUTPUT;
	$evaluation = $DB->get_records_sql('SELECT a.id, a.userid, a.content, b.firstname, b.lastname, a.timecreated FROM mdl_learning_comments a JOIN mdl_user b ON a.userid = b.id WHERE itemid = ? ORDER BY timecreated DESC LIMIT 0,5', array($blogid));

	$res = '';
	foreach($evaluation as $value)
	{
		$user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
		$userIcon = $OUTPUT->user_picture (
			$user,
			array(
				'link' => false,
				'visibletoscreenreaders' => false
			)
		);
		$userIcon = str_replace("width=\"35\" height=\"35\"", " ", $userIcon);
		$res .= '<!--评论内容1-->
				<div class="comment container">
					<div class="comment-l">
						<div class="Learnerimg-box">
							'.$userIcon.'
						</div>
					</div>
					<div class="comment-r">
						<p class="name">'.$value->lastname.$value->firstname.'</p>
						<p class="commentinfo">
							'.$value->content.'
						</p>
						<p class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</p>
					</div>
				</div>
			  <!--评论内容1 end-->
			';
	}
	return $res;
}

function html_head()
{
	$contents = '
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8">
				<title></title>
				<link rel="stylesheet" href="css/bootstrap.css" />
				<link rel="stylesheet" href="css/all-content.css" />
				<link rel="stylesheet" href="css/blog.css" / type="text/css"> <!--全局-->
				<script type="text/javascript" src="js/jquery-1.11.3.min.js" ></script>
				<!--<script type="text/javascript" src="js/maincontents.js" ></script>-->
				<script>
				$(document).ready(function(){
//						$("body").bind("click", function(){
//							$(".trends-bottom li").removeClass("a-active");
//							$(".comment-banner").hide();
//						});

						$(".people-box").click(function() {
							 var userid = $(this).children(".name").attr("value");
							window.open(window.location.protocol+"//"+window.location.host+"/moodle/circlesoflearning/index.php?userid="+userid);
						});

						<!-- Start 搜索按钮点击事件  朱子武 20160315-->
						$(".submit").click(function(){
						var nameval = $(this).siblings(".search_key").val();
							searchDataFromTable(nameval);
						});
						<!-- End 搜索按钮点击事件  朱子武 20160315-->

						// 点击缩略图放大
						$(".trends-content>.thumb").click(function () {
							$(this).hide().next(".pic-big").fadeIn();
						});
						// 点击大图缩小
						$(".trends-content>.pic-big").click(function () {
							$(this).hide().prev(".thumb").fadeIn();
						});

						$(".like-btn").click(function () {     //点赞动作
							$(".comment-banner").hide();
							$(".trends-bottom li").removeClass("a-active");
							var valueid = $(this).val();
					//							alert(valueid);
							$.ajax({
								 url: "../circlesoflearning/blogbackground.php",
								 data: {blogid: valueid, relatedtype:"3", type:"related" },
								 success: function(msg){
								 if(msg=="1"){
									// alert("点赞成功");
									location.reload();
								 }
								 else{
									 msg=="2" ? alert("你已经为它点赞了，无需再次点赞") :alert("点赞失败");
								 }
							   }
							 });
							$(this).addClass("a-active");
							$(this).parent().siblings(".comment-banner").fadeOut();
						});

						$(".comment-btn").click(function () {  //评论动作
							$(".comment-banner").hide();
							$(this).addClass("a-active");
							$(this).parent().siblings(".comment-banner").fadeIn();
						});

						$(".forward-btn").click(function () {  //转发动作
							$(".comment-banner").hide();
							$(".trends-bottom li").removeClass("a-active");
							var valueid = $(this).val();
					//							alert(valueid);
							$.ajax({
								url: "../circlesoflearning/blogbackground.php",
								data: {blogid: valueid, relatedtype:"2", type:"related" },
								success: function(msg){
								if(msg=="1"){
									// alert("转发成功");
									location.reload();
								}
							}
							});
							$(this).parent().siblings(".comment-banner").fadeOut();
						});

						$(".delete-btn").click(function () {   //删除动作
							$(".comment-banner").hide();
							$(".trends-bottom li").removeClass("a-active");
							var valueid = $(this).val();
							$.ajax({
								url: "../circlesoflearning/blogbackground.php",
								data: {blogid: valueid, type:"delete" },
								success: function(msg){
								if(msg=="1"){
									location.reload();
								}
								else if(msg=="2")
								{
									alert("您没有权限进行操作");
								}
							}
							});
							$(this).parent().siblings(".comment-banner").fadeOut();
						});

						/** Start 添加评论按钮点击事件 朱子武 20160315*/
						$(".commentBtnClick").click(function() {
							var mytext =$(this).parent().children(".form-control").val();
							var valueid = $(this).val();
							if(mytext==""){
								alert("请输入评论内容");
							}
							else{
					//							alert(valueid);
								$.ajax({
									url: "../circlesoflearning/blogbackground.php",
									data: { mycomment: mytext, blogid: valueid, type:"comment"},
									success: function(msg){
									if(msg=="1"){
										location.reload();
					//											window.location.href=window.location.href+"&page=1";
									}else{
										alert("评论失败");
									}
								}
							});
							}
						});
						/** End 添加评论按钮点击事件 朱子武 20160315*/
					})

					function followUser(value)
					{
//						var btn = document.getElementById("followUser-btn");//根据id获取button节点
						$.ajax({
							url: "../circlesoflearning/blogbackground.php",
							data: {concernid: value, type:"concern" },
							success: function(msg){
								if(msg=="1"){
									//alert("关注成功");
									location.reload();
								}
								else if(msg=="2"){
									location.reload();
								}
								else
								{
									alert("关注失败");
								}
							}
						});
					}

					/** Start 搜索学员信息 朱子武 20160315*/
				 function searchDataFromTable(searchtext)
				 {
	//             alert("dsgfhsdjfsdgj");
					 $.ajax({
						 url: "../circlesoflearning/blogbackground.php",
						 dataType:"json",
						 data: {type: "searchuser", searchtext: searchtext},
						 success: function(msg) {
							 $(".concerned-list").children().remove();
							 $.each(msg, function(commentIndex, comment){
								 var user_name = comment["lastname"] + comment["firstname"];
								 var concernID = comment["concernid"];
								 var userIcon = comment["userIcon"];
								   addDataInBox(concernID, user_name, userIcon);
							 });
							 $(".people-box").click(function()
							 {
								 var userid = $(this).children(".name").attr("value");
								 window.open(window.location.protocol+"//"+window.location.host+"/moodle/circlesoflearning/index.php?userid="+userid);
							});
						 }
					 });
				 }
				 /** End 搜索学员信息 朱子武 20160315*/

				/** Start 显示学员信息 朱子武 20160315*/
				 function addDataInBox(concernID, userName, userIcon)
				 {
					$(".concerned-list").append(\'<div class="people-box" style="cursor: pointer;"><div class="Learnerimg-box"><img src ="\'+userIcon+\'"></div><div class="line"></div><p class="name" value = \'+concernID+\'>\'+userName+\'</p></div>\');
				 }
				/** End 显示学员信息 朱子武 20160315*/
				</script>
			</head>
			<body>';
//	$contents .= contents();
//	$contents .= myconcerned();
	$contents .= myrelated();
	$contents .= '
			</body>
		</html>';
	return $contents;
}

function contents()
{

	$current_page = $_SESSION['pageid'];
	unset ($_SESSION['pageid']);
	$userid = $_GET['userid'];
//	$currentCount_page = $_GET['page'];
	$str = '	<div class="trends-box">
				<!-- 学习动态 -->
				<ul class="trends-block list-unstyled">
					'.get_contents_all($current_page - 1, $userid).get_page($userid, $current_page).'
				</ul>
				</div>
		';
	return $str;
}

function get_page($userid=null, $current_page)
{
	$currentCount_page = get_blog_count($userid);

	global $CFG;
	$contents = '';
	$contents .= '<!--分页按钮-->
				<div class="paginationbox">
					<ul class="pagination">';
	$contents1 = '';
	if($userid)
	{
		$contents1 .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page=1&userid='.$userid.'">首页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page <= 1 ? 1: $current_page - 1).'&userid='.$userid.'">上一页</a>
						</li>';
	}else
	{
		$contents1 .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page=1">首页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
						</li>';
	}


//	$contents .= my_get_blog_evaluation_current_count($current->page, $blogValue->id);
	$contents .= get_blog_page($currentCount_page->page, $userid);
	if($userid)
	{
		$contents1 .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page < $currentCount_page->page ? ($current_page + 1): $currentCount_page->page).'&userid='.$userid.'">下一页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$currentCount_page->page.'&userid='.$userid.'">尾页</a>
						</li>';
	}
	else
	{
		$contents1 .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page < $currentCount_page->page ? ($current_page + 1): $currentCount_page->page).'">下一页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$currentCount_page->page.'">尾页</a>
						</li>';
	}
	$contents .= '</ul>
				</div>
			<!--分页按钮 end-->';
	return $contents;
}

/** Start  获取微博条数  朱子武  20160317*/
function get_blog_count($userid=null)
{
	global $DB;
	$blogResult = array();
	$blogCount_page = new stdClass();
	if($userid)
	{
		$blogResult = $DB->get_records_sql('SELECT id FROM mdl_circles_of_learning WHERE userid = '.$userid);
	}
	else
	{
		$blogResult = $DB->get_records_sql('SELECT id FROM mdl_circles_of_learning');
	}
	$count = count($blogResult);
	$blogCount_page->count = $count;
	$page = ceil($count/10);
	$blogCount_page->page = ($page <= 1 ? 1: $page);
	return $blogCount_page;
}
/** End  获取微博条数  朱子武  20160317*/

/** Star  获取微博页码  朱子武  20160317*/
function get_blog_page($blogCount, $userid=null)
{
	global $CFG;
	$res = '';
	if($userid)
	{
		for($num = 1; $num <= $blogCount; $num ++)
		{
			$res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'&userid='.$userid.'">'.$num.'</a></li>';
		}
	}else
	{
		for($num = 1; $num <= $blogCount; $num ++)
		{
			$res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'">'.$num.'</a></li>';
		}
	}
	return $res;
}
/** Star  获取微博页码  朱子武  20160317*/

/** START 添加我的关注 朱子武 20160310 */
function myconcerned()
{
	global $DB;
	global $USER;
	global $OUTPUT;
	global $CFG;
	/** Star 修改数据库，添加排序  朱子武 20160315*/
	$myconcernedResult = $DB->get_records_sql('SELECT a.*, b.firstname, b.lastname FROM mdl_blog_concern_me_my a JOIN mdl_user b ON a.concernid = b.id WHERE userid = '.$USER->id.' ORDER BY b.lastname');

	$res = '
		<!--关注的人列表-->

		<div style="height:40px; width:100%;text-align:center;"><img src="xuexiquan.png" style="height:100%;margin: auto; "></div>

		 <!-- Start 添加搜索框 朱子武 20160315-->
        <div style="width: 100%; height: 40px" >
            <input style="float: right;" type="submit" class="submit" value="搜索">
            <input style="float: right; height:30px" type="text" class="search_key" value="搜索名称/账号" onclick="this.value=\'\';focus()"/>
        </div>
        <!-- End 添加搜索框 朱子武 20160315-->

		<div class="concerned-list">
		';

	if(count($myconcernedResult))
	{
		foreach($myconcernedResult as $value)
		{
			$userobject = new stdClass();
			$userobject->metadata = array();
			$user = $DB->get_record('user', array('id' => $value->concernid), '*', MUST_EXIST);
			$userobject->metadata['useravatar'] = $OUTPUT->user_picture (
				$user,
				array(
					'link' => false,
					'visibletoscreenreaders' => false
				)
			);
			$userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);

			$res .= '<div class="people-box" style="cursor: pointer;">
						<div class="Learnerimg-box">'.$userobject->metadata['useravatar'].'</div>
						<div class="line"></div>
						<p class="name" value = '.$value->concernid.'>'.$value->lastname.$value->firstname.'</p>
					</div>';
		}
	}
	else
	{
		$res .= '<p class="name">没有更多数据了</p>';
	}

	$res .= '</div>
				<!--关注的人列表 end-->
	<div style="clear:both;"></div><!--使高度自适应-->';


	return $res;
}
/** END 添加我的关注 朱子武 20160310 */

/** START 添加与我相关 朱子武 20160310 */
function myrelated()
{

	global $DB;
	global $USER;
	global $CFG;

	$myrelatedresult = $DB->get_records_sql("SELECT a.*, b.firstname, b.lastname FROM mdl_blog_related_me_my a JOIN mdl_user b ON a.userid = b.id WHERE authorid = ? ORDER BY relatedtime DESC ",array($USER->id));

//    $evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($course->id));

	$res = '
<!--与我相关-->
		<div style="height:40px; width:100%;text-align:center;"><img src="xuexiquan.png" style="height:100%;margin: auto; "></div>
		<div class="about-myself">';

	if(count($myrelatedresult))
	{
		foreach($myrelatedresult as $value)
		{
			$res .= '<div class="info">
						<a class="usename" href="'.$CFG->wwwroot.'/user/view.php?id='.$value->authorid.'&course=1">'.$value->lastname.$value->firstname.'</a>
						<p class="time">'.userdate($value->relatedtime,'%Y-%m-%d %H:%M').'</p>
						<div class="divison_line"></div>
						<a href="'.$CFG->wwwroot.'/user/view.php?id='.$value->authorid.'&course=1">'.$value->lastname.$value->firstname.'</a>
						<a style="text-decoration:none;">在</a>
						<a style="text-decoration:none;">'.userdate($value->relatedtime,'%Y-%m-%d %H:%M').'</a>
						<a style="text-decoration:none;">'.switch_value($value->relatedtype).'</a>
						<a style="text-decoration:none;">了你的博客</a>
						<a href="'.$CFG->wwwroot.$value->blogurl.'">'.$value->blogtitle.'</a>
					</div>';
		}
	}
	else
	{
		$res .= '<div class="info">
						<a class="usename" >没有更多信息</a>
					</div>';
	}

	$res .= '</div>
				<!--与我相关 end-->
				<div style="clear:both;"></div><!--使高度自适应-->';

	return $res;
}

/** END 添加与我相关 朱子武 20160310 */

/** SATRT 判断与我相关类型 朱子武 20160310*/
function switch_value($relatedtype)
{
	$str = '';
	switch($relatedtype)
	{
		case 1: // 评论
			$str = '评论';
			break;
		case 2: // 转发
			$str = '转发';
			break;
		case 3: // 点赞
			$str = '点赞';
			break;
		default:
			break;
	}
	return $str;
}
/** END 判断与我相关类型 朱子武 20160310*/
