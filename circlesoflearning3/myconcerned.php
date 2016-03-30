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
				<script type="text/javascript" src="js/jquery-1.11.3.min.js" ></script>
				<!--<script type="text/javascript" src="js/maincontents.js" ></script>-->
				<script>
				$(document).ready(function(){
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
					})



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
	$contents .= myconcerned();
	$contents .= '
			</body>
		</html>';
	return $contents;
}

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
