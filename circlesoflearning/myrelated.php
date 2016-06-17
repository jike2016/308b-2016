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

function html_head()
{
	global $CFG;
	$current_page = $_SESSION['pageid'];
	unset ($_SESSION['pageid']);
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
	$contents .= myrelated($current_page - 1);
	$relatedCount_page = get_relatedpage_count();
	$contents .= '
				<!--分页按钮-->
					<div class="paginationbox">
						<ul class="pagination">
							<li>
								<a href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page=1">首页</a>
							</li>
							<li>
								<a href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
							</li>';
					$contents .= get_related_page($relatedCount_page->page,$current_page);
					$contents .= '<li>
								<a href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page='.($current_page < $relatedCount_page->page ? ($current_page + 1): $relatedCount_page->page).'">下一页</a>
							</li>
							<li>
								<a href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page='.$relatedCount_page->page.'">尾页</a>
							</li>
						</ul>
					</div>
				<!--分页按钮 end-->
			</body>
		</html>';
	return $contents;
}

//  获取总页码
function get_relatedpage_count()
{
	global $DB;
	global $USER;
	$relatedCount_page = new stdClass();
	$relatedcount = $DB->get_records_sql('SELECT * FROM mdl_blog_related_me_my WHERE authorid = '.$USER->id);
	$count = count($relatedcount);
	$relatedCount_page->count = $count;
	$page = ceil($count/10);
	$relatedCount_page->page = ($page <= 1 ? 1: $page);
	return $relatedCount_page;
}

/** Star  获取页码  朱子武  20160317*/
function get_related_page($relatedCount,$current_page=0)
{
	global $CFG;
	$res = '';

	$numstart = ($relatedCount > 5)?(($current_page < $relatedCount - 2)?(($current_page > 2)?($current_page - 2):1):($relatedCount - 4)):1;
	$numend = ($relatedCount > 5)?(($current_page < $relatedCount - 2)?(($current_page > 2)?($current_page + 2):5):($relatedCount)):$relatedCount;

	for($num = $numstart; $num <= $numend; $num ++)
	{
		if($num == $current_page)
		{

			$res .= '<li><a class = "pagination_li_active" href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page='.$num.'">'.$num.'</a></li>';
		}
		else
		{
			$res .= '<li><a href="'.$CFG->wwwroot .'/circlesoflearning/myrelated.php?page='.$num.'">'.$num.'</a></li>';
		}
	}
	return $res;
}
/** Star  获取页码  朱子武  20160317*/

/** START 添加与我相关 朱子武 20160310 */
function myrelated($current_page)
{

	global $DB;
	global $USER;
	global $CFG;

	$page = $current_page * 10;
	$myrelatedresult = $DB->get_records_sql('SELECT a.*, b.firstname, b.lastname FROM mdl_blog_related_me_my a JOIN mdl_user b ON a.userid = b.id WHERE authorid = '.$USER->id.' ORDER BY relatedtime DESC LIMIT '.$page.',10');

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
						<a style="text-decoration:none;">'.switch_value($value->relatedtype).'了你的动态</a>
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
