<?php

/**
 * Start 学习圈 朱子武 20160316
 */

if(empty($CFG))
{
//	require_once(dirname(dirname(__FILE__)).'/config.php');
	require("../config.php");
}

echo html_head();

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
			</head>
			<body>';
	$contents .= myrelated();
	$contents .= '
			</body>
		</html>';
	return $contents;
}

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
