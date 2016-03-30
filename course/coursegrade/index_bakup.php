<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
		<title>
			课程评分页面
		</title>
		<link rel="stylesheet" href="css/bootstrap.css" type="text/css"/>		
		<link rel="stylesheet" href="css/scorepage.css" type="text/css">
		<link rel="stylesheet" href="css/scorepagebanner.css" type="text/css">		
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="js/scorepage.js"></script>
	</head>

	<body>

	<?php
/**  START 朱子武 20160226 从数据库获取评分评论*/
	require_once("../../config.php");
	//    获取评价数目页数
	function my_get_course_evaluation_count($courseid)
	{
		global $DB;
		$evaluation = $DB->get_records_sql('SELECT id as mycount FROM mdl_score_course_my WHERE courseid = ? ', array($courseid));
		$mycount = count($evaluation);
		$mycount = ceil($mycount/10);
		return ($mycount <= 1 ? 1: $mycount);
	}
//	    输出页码
	function my_get_course_evaluation_current_count($count_page, $courseid)
	{
		for($num = 1; $num <= $count_page; $num ++)
		{
			echo'<li><a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$num.'">'.$num.'</a></li>';
		}
	}
	/** START 朱子武 获取课程评分 20160226*/
	function my_get_course_score($courseid)
	{
		global $DB;
		$myscore = $DB->get_records_sql('SELECT id, sumscore FROM mdl_score_course_sum_my WHERE courseid = ?', array($courseid));
		if(count($myscore))
		{
			foreach($myscore as $value)
			{
				$mysumscore = $value->sumscore;
			}
			echo'<p class="score-num" >'.$mysumscore.'</p>';
		}
		else
		{
			echo'<p class="score-num" >10.0</p>';
		}
	}
	/** ---END---*/

	/** START 朱子武 获取课程评价 20160226*/
	function my_get_course_evaluation($courseid, $current_page)
	{
	$my_page = $current_page * 10;
	global $DB;
	global $OUTPUT;
	$evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, scoretime FROM mdl_score_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY scoretime DESC LIMIT '.$my_page.',10', array($courseid));

		foreach($evaluation as $value)
		{
		$userobject = new stdClass();
		$userobject->metadata = array();
		$user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
		$userobject->metadata['useravatar'] = $OUTPUT->user_picture (
				$user,
				array(
						'link' => false,
						'visibletoscreenreaders' => false
				)
		);

		$userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
		echo '<div class="evaluation">
								<div class="evaluation-con">
									<a href="#" class="img-box">
									'.$userobject->metadata['useravatar'].'
										<!--<img src="img/533e564d0001308602000200-100-100.jpg" alt="anananan007">-->
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="#" class="username">'.$value->lastname.$value->firstname.'</a>
										</div>
										<!--user-info end-->
										<p class="content">'.$value->comment.'</p>
										<div class="info">
											<span class="time">时间：'.userdate($value->scoretime,'%Y-%m-%d %H:%M').'</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->
							';
		}
	}
/** ---END---*/
	echo'
		<div id="main">
			<div class="course-infos" >
				<div class="w pr">
					
					<div class="banner-left">
						<img src="img/1.jpg" />
					</div>
					
					<div class="banner-right">
						<div class="path">
							<a href="#">课程</a>
							<i class="path-split">\</i><a href="#">前端开发</a>
							<i class="path-split">\</i><a href="#">HTML/CSS</a>
							<i class="path-split">\</i><span>HTML+CSS基础课程</span>
						</div>
						
						<div class="hd">
							<h2 class="l">HTML+CSS基础课程</h2>
						</div>
						
						<div class="learnernum">						
							<p class="p1">218056</p>
							<p class="p2">学习人数</p>
						</div>
					</div>											
				</div>
			</div>
			
			<div class="course-info-main clearfix w has-progress">
				<div class="content-wrap clearfix">				
					<div class="content">
						<div class="scoreinfo">
							<p>满意度评分</p>
							<p id="comment-star" class="star-box">
								<span id="star1" class="glyphicon glyphicon-star"></span>
								<span id="star2" class="glyphicon glyphicon-star"></span>
								<span id="star3" class="glyphicon glyphicon-star"></span>
								<span id="star4" class="glyphicon glyphicon-star"></span>
								<span id="star5" class="glyphicon glyphicon-star"></span>
							</p>
						</div>
						<div class="evaluation-list" >													
							<div class="mycomment">
								<textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
								<button id="score-btn" class="btn btn-info">发表评论</button>
							</div>
							<!--evaluation-info end-->';
	/**  START 朱子武 20160226 从数据库中获取评论数据 */
		$courseid = $_GET['id'];
		$count_page = my_get_course_evaluation_count($courseid);

        $current_page = $_GET['page'];
        my_get_course_evaluation($courseid, $current_page - 1);
						echo'<div class="paginationbox">
								<ul class="pagination">
									<li>
								      <a href="../../course/coursegrade/index.php?id='.$courseid.'&page=1">首页</a>
								    </li>
								    <li>
								      <a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>

								    </li>
								    ';
								    my_get_course_evaluation_current_count($count_page, $courseid);
							echo'<li>
								      <a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>

								    </li>
								    <li>
								      <a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$count_page.'">尾页</a>
								    </li>
								</ul>
							</div>';

	/** ---END---*/
		echo'</div>
						<!--evaluation-list end-->
						
					</div>
					<!--content end-->
				
					<div class="aside r">
						<div class="bd">							
							<div class="box mb40">
								<div class="score-box">
									<p class="score-title">满意度评分</p>';
									my_get_course_score($courseid);
							echo'	<div class="star-box">
										<p>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
										</p>
									</div>									
								</div>								
							</div>

						</div>
												
					</div>
				</div>

				<div class="clear"></div>

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
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" href="#" target="_blank" id="feedBack"></a>
				<div class="elevator-app-box">
				</div>
			</a>
		</div>

		<div class="mask"></div>
		';
	?>
	</body>

</html>