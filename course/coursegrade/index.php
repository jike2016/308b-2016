<!DOCTYPE html>
<html >

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
		
		<title>
			课程评分页面
		</title>
		<link rel="stylesheet" href="css/bootstrap.css" type="text/css"/>		
		<link rel="stylesheet" href="css/scorepage.css" type="text/css">
		<link rel="stylesheet" href="css/scorepagebanner.css" type="text/css">	
		<link rel="stylesheet" href="../../theme/more/style/navstyle.css" type="text/css">	
		<link rel="stylesheet" href="../../theme/more/style/alertstyle.css" type="text/css">
		<style>.navstyle-bottom {
    margin-top: 0px;
} </style>
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="js/scorepage.js"></script>
	</head>

	<body>

	<?php

/**  START 朱子武 20160226 从数据库获取评分评论*/
	require_once("../../config.php");
	$PAGE->set_pagelayout('usermenu');//设置layout
	echo $OUTPUT->header();//输出layout文件

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
	/** START 朱子武 获取课程评分星星 20160226*/
	function my_get_glyphicon_star($num)
	{
		for($i = 0; $i < ceil($num/2); $i++)
		{
			echo'<span class="glyphicon glyphicon-star"></span>';
		}
	}
	/** --- my_get_glyphicon_star END ---*/

	/** START 朱子武 获取课程评分 20160226*/
	function my_get_course_score($courseid)
	{
		global $DB;
		$mysumscore = 0.0;
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
			$mysumscore = 10.0;
		}

		return $mysumscore;
	}
	/** ---END---*/

	/**获取学生的数量
	 * 朱子武
	 * @param $courseid
	 * @return mixed 返回选课的学生人数
	 */
	function my_get_course_student_count($courseid){

		global $DB;
		$studentsNum = $DB->get_records_sql('SELECT b.courseid,COUNT(*) num FROM mdl_user_enrolments a JOIN mdl_enrol b WHERE b.courseid='.$courseid.' AND b.id=a.enrolid');

		return $studentsNum[$courseid]->num;
	}
	/** --- my_get_course_student_count END---*/

	/**获取summary中的图片
	 * @param $course
	 * @return string 返回的例子：src="http://localhost/moodle/pluginfile.php/235/course/summary/shibajiewuzhongquanhui.png"
	 */
	function my_get_course_formatted_summary_pix($courseid, $coursesummary, $coursesummaryformat) {
		global $CFG;
		require_once($CFG->libdir. '/filelib.php');

		$context = context_course::instance($courseid);
		$my_course_summary_pix=my_search_summary_pix($coursesummary);
		$summary = file_rewrite_pluginfile_urls($my_course_summary_pix, 'pluginfile.php', $context->id, 'course', 'summary', null);
		$summary = format_text($summary, $coursesummaryformat,null, $courseid);
		return $summary;
	}
	/** --- my_get_course_formatted_summary_pix END---*/

	/** START 朱子武 截取字符串 20160228*/
	function my_search_summary_pix($coursesummary){
		$pix_start='src="';
		$pix_end='"';
		$start_position=strpos($coursesummary,$pix_start);
		if($start_position==false){return '';}
		$end_position=strpos($coursesummary,$pix_end,$start_position+5);
		$my_pix=substr($coursesummary,$start_position,$end_position-$start_position+1);
		return $my_pix;
	}

	/** --- my_search_summary_pix END---*/

	/** START 朱子武 获取课程详细 20160228*/
	function my_get_course_detailed($courseid)
	{
		global $DB;
		$course_detailed = $DB->get_records_sql('SELECT id, fullname, shortname, summary, summaryformat FROM mdl_course WHERE id ='.$courseid.'');

		echo '<div class="banner-left">
						<!--<img src="img/1.jpg" />-->
						<img '.my_get_course_formatted_summary_pix($course_detailed[$courseid]->id, $course_detailed[$courseid]->summary, $course_detailed[$courseid]->summaryformat).'/>

					</div>
					<div class="banner-right">
						<div class="path">
							<a href="#"></a>
							<i class="path-split"></i><a href="#"></a>
							<i class="path-split"></i><a href="#"></a>
							<i class="path-split"></i><span></span>
						</div>

						<div class="hd">
							<h2 class="l">'.$course_detailed[$courseid]->fullname.'</h2>
						</div>

						<div class="learnernum">
							<p class="p1">'.my_get_course_student_count($courseid).'</p>
							<p class="p2">学习人数</p>
						</div>
					</div>';
	}
	/** ---my_get_course_detailed END ---*/

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

	$courseid = $_GET['id'];
	$current_page = $_GET['page'];

	echo '<nav class="navstyle navbar-fixed-top">
	<div class="nav-main">
		<img id="logo" src="'.$CFG->wwwroot.'/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor=\'pointer\'" onClick="document.location='.$CFG->wwwroot.';">
		<ul class="nav-main-li">
			<a href="'.$CFG->wwwroot.'">
				<li class="li-normol">首页</li>
			</a>
			<a href="'.$CFG->wwwroot.'/mod/forum/view.php?id=1">
				<li class="li-normol">微阅</li>
			</a>
			<a href="'.$CFG->wwwroot.'/course/index.php">
				<li class="li-normol">微课</li>
			</a>
			<a href="'.$CFG->wwwroot.'/privatecenter/index.php?class=zhibo">
				<li class="li-normol">直播</li>
			</a>
		</ul>
		<div class="usermenu-box">
			'.$OUTPUT->user_menu().'					
		</div>
	</div>
</nav>';
	echo'
		<div id="main">
			<div class="course-infos" >
				<div class="w pr">';

	my_get_course_detailed($courseid);

				echo '
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
		$count_page = my_get_course_evaluation_count($courseid);

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
									$mysumscore = my_get_course_score($courseid);
							echo'	<div class="star-box">
										<p>';
										my_get_glyphicon_star($mysumscore);
							echo'
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
<!--底部导航条-->
	<nav class="navstyle-bottom navbar-static-bottom"></nav>
	<!--底部导航条 end-->
		
		<div id="J_GotoTop" class="elevator">
			<!--<a class="elevator-msg" href="#" target="_blank" id="feedBack"></a>
				<div class="elevator-app-box">
				</div>
			</a>-->
		</div>

		<div class="mask"></div>
		';
			echo $OUTPUT->footer();//输出左右和底部
	?>
	
	</body>

</html>