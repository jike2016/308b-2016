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

echo html_head();

function get_contents_all($content_page, $userid=null, $entryid=null, $static= null)
{
	global $DB;
	global $OUTPUT;
	global $CFG;
	global $USER;
	$contents = '';
	$my_page = $content_page * 5;
	if($userid)
	{
		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id WHERE a.publishstate = "publish" AND userid = '.$userid.' ORDER BY a.created DESC LIMIT '.$my_page.',5');
	}
//	elseif($static=='private')
//	{
//		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id ORDER BY a.created DESC LIMIT '.$my_page.',10');
//	}
	elseif($entryid)
	{
		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id WHERE a.publishstate = "publish" AND a.id = '.$entryid);
	}
	else
	{
		$allresult = $DB->get_records_sql('SELECT a.id, a.userid, a.originalblogid, a.pictures, a.summary, a.created, b.firstname, b.lastname, b.username FROM mdl_circles_of_learning a JOIN mdl_user b ON a.userid = b.id WHERE a.publishstate = "publish" ORDER BY a.created DESC LIMIT '.$my_page.',5');
	}
	/** Start 添加判断 如果没有数据 提醒用户 朱子武 20160318 */
	if(!count($allresult))
	{
		if($entryid)
		{
			$contents .= '<div><p>您查看的原文已经管理员被删除了，发个<a href = "'.$CFG->wwwroot.'/circlesoflearning/edit.php">心情</a>试试？</p></div>';
		}
		else
		{
			$contents .= '<div><p>暂时没有动态，发个<a href = "'.$CFG->wwwroot.'/circlesoflearning/edit.php">心情</a>试试？</p></div>';
		}
		return $contents;
	}
/** End 添加判断 如果没有数据 提醒用户 朱子武 20160318 */
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
							<div class="blog-left">
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
							<div class="blog-right">
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
										<img src="'.$pictureValue.'" data-pic-big="" alt="" />
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
			if($entryid){
				$contents .= '<ul class="trends-bottom list-unstyled">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">点赞('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn a-active"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
							<li class="delete-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">删除</a></li>
					      </ul>';
			}else{
				$contents .= '<ul class="trends-bottom list-unstyled">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">点赞('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
							<li class="delete-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">删除</a></li>
					      </ul>';
			}
			
		}
		else
		{
			if($entryid){
				$contents .= '<ul class="trends-bottom list-unstyled nodelete">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">点赞('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn a-active"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
						  </ul>';
			}else{
				$contents .= '<ul class="trends-bottom list-unstyled nodelete">
							<li class="like-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">点赞('.my_get_blog_like_count($blogValue->id).')</a></li>
							<li class="comment-btn"><a href="javascript:void(0)">评论('.$current->count.')</a></li>
							<li class="forward-btn" value = "'.$blogValue->id.'"><a href="javascript:void(0)">转发('.my_get_blog_forwarded_count($blogValue->id).')</a></li>
						  </ul>';
			}
			
		}
		if($entryid){
			$contents .= '<!--评论以及评论列表-->
						<div class="comment-banner" style="display:block">
							<div class="commentbox">
								<div class="mycomment">
									<textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
									<button id="commentBtnClick" class="commentBtnClick btn btn-info" value="'.$blogValue->id.'">发表评论</button>
								</div>';
		}else{
			$contents .= '<!--评论以及评论列表-->
						<div class="comment-banner" >
							<div class="commentbox">
								<div class="mycomment">
									<textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
									<button id="commentBtnClick" class="commentBtnClick btn btn-info" value="'.$blogValue->id.'">发表评论</button>
								</div>';
		}
		

//		$current_page = $_SESSION['pageid'];



		if($entryid)
		{
			$contents .=	my_get_blog_evaluation($blogValue->id, $content_page);
//			my_get_blog_evaluation_count();
			$contents .= '
						</div>
						<!--分页按钮-->
					<div class="paginationbox">
						<ul class="pagination">
							<li>
								<a href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogValue->id.'&page=1">首页</a>
							</li>
							<li>
						 		<a href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogValue->id.'&page='.($content_page <= 1 ? 1: $content_page - 1).'">上一页</a>
							</li>';
			$contents .= my_get_blog_evaluation_current_count($current->page, $blogValue->id,$content_page);
			$contents .= '
							<li>
							  	<a href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogValue->id.'&page='.($content_page < $current->page ? ($content_page + 1): $current->page).'">下一页</a>
							</li>
							<li>
							  	<a href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogValue->id.'&page='.$current->page.'">尾页</a>
							</li>
						</ul>
					</div>
					<!--分页按钮 end-->
					</div>
				<!--评论以及评论列表 end-->
			</li>';

		}
		else
		{
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
							<a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?entryid='.$blogValue->id.'">查看更多</a>
						</div>
						<!--评论以及评论列表 end-->
					</li>';
			}
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
function my_get_blog_evaluation_current_count($count_page, $blogid,$content_page)
{
	global $CFG;
	$res = '';
	for($num = 1; $num <= $count_page; $num ++)
	{
		 /** Start 添加按钮高亮效果 朱子武 20160318*/
		if($num == $content_page)
		{

			$res .= '<li><a class = "pagination_li_active" href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogid.'&page='.$num.'">'.$num.'</a></li>';
		}
		else
		{

			$res .= '<li><a href="'.$CFG->wwwroot.'/circlesoflearning/index.php?entryid='.$blogid.'&page='.$num.'">'.$num.'</a></li>';
		}
		/** End 添加按钮高亮效果 朱子武 20160318*/
	}
	return $res;
}

//   获取博客评价
function my_get_blog_evaluation($blogid, $current_page = 0)
{

	$my_page = $current_page * 5;
//	if($my_page < 0) return '';
	global $DB;
	global $OUTPUT;
	$evaluation = $DB->get_records_sql('SELECT a.id, a.userid, a.content, b.firstname, b.lastname, a.timecreated FROM mdl_learning_comments a JOIN mdl_user b ON a.userid = b.id WHERE itemid = ? ORDER BY timecreated DESC LIMIT '.$my_page.',5', array($blogid));

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
						<!-- Start 修正评论时间BUG zzwu 20160318-->
						<p class="time">时间：'.userdate($value->timecreated,'%Y-%m-%d %H:%M').'</p>
						<!-- End 修正评论时间BUG zzwu 20160318-->
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
				<script type="text/javascript" src="js/maincontents.js"></script>
			</head>
			<body>';
	$contents .= contents();
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
	$entryid = $_GET['entryid'];
	$static = $_GET['static'];
//	$currentCount_page = $_GET['page'];
	$str = '	<div class="trends-box">
				<!-- 学习动态 -->
				<ul class="trends-block list-unstyled">
					'.get_contents_all($current_page - 1, $userid, $entryid, $static).get_page($userid, $current_page, $entryid, $static).'
				</ul>
				</div>
		';
	return $str;
}

function get_page($userid=null, $current_page, $entryid=null, $static=null)
{
	if($entryid) return '';
	$currentCount_page = get_blog_count($userid);

	global $CFG;
	$contents = '';
	$contents .= '<!--分页按钮-->
				<div class="paginationbox">
					<ul class="pagination">';
	if($userid)
	{
		$contents .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page=1&userid='.$userid.'">首页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page <= 1 ? 1: $current_page - 1).'&userid='.$userid.'">上一页</a>
						</li>';
	}else
	{
		$contents .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page=1">首页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
						</li>';
	}


//	$contents .= my_get_blog_evaluation_current_count($current->page, $blogValue->id);
	$contents .= get_blog_page($currentCount_page->page, $userid, $current_page);
	if($userid)
	{
		$contents .= '	<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.($current_page < $currentCount_page->page ? ($current_page + 1): $currentCount_page->page).'&userid='.$userid.'">下一页</a>
						</li>
						<li>
						  <a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$currentCount_page->page.'&userid='.$userid.'">尾页</a>
						</li>';
	}
	else
	{
		$contents .= '	<li>
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
		$blogResult = $DB->get_records_sql('SELECT id FROM mdl_circles_of_learning WHERE publishstate = "publish" AND userid = '.$userid);
	}
	else
	{
		$blogResult = $DB->get_records_sql('SELECT id FROM mdl_circles_of_learning WHERE publishstate = "publish"');
	}
	$count = count($blogResult);
	$blogCount_page->count = $count;
	$page = ceil($count/5);
	$blogCount_page->page = ($page <= 1 ? 1: $page);
	return $blogCount_page;
}
/** End  获取微博条数  朱子武  20160317*/

/** Star  获取微博页码  朱子武  20160317*/
function get_blog_page($blogCount, $userid=null, $current_page=0)
{
	global $CFG;
	$res = '';
	if($userid)
	{
		for($num = 1; $num <= $blogCount; $num ++)
		{
			/** Start 添加翻页高亮效果 朱子武 20160318  */
			if($num == $current_page)
			{
				$res .= '<li><a class="pagination_li_active" href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'&userid='.$userid.'">'.$num.'</a></li>';
			}
			else
			{
				$res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'&userid='.$userid.'">'.$num.'</a></li>';
			}
			/** End 添加翻页高亮效果 朱子武 20160318  */
		}
	}else
	{
		for($num = 1; $num <= $blogCount; $num ++)
		{
			/** Start 添加翻页高亮效果 朱子武 20160318  */
			if($num == $current_page)
			{
				$res .= '<li><a class="pagination_li_active" href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'">'.$num.'</a></li>';
			}
			else{
				$res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/index.php?page='.$num.'">'.$num.'</a></li>';
			}
			/** End 添加翻页高亮效果 朱子武 20160318  */
		}
	}
	return $res;
}
/** Star  获取微博页码  朱子武  20160317*/