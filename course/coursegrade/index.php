<!DOCTYPE html>
<html >

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
		
		<title>课程评分xxx</title>
		<link rel="stylesheet" href="css/bootstrap.css" type="text/css"/>		
<!--		<link rel="stylesheet" href="css/scorepage.css" type="text/css">-->
<!--		<link rel="stylesheet" href="css/scorepagebanner.css" type="text/css">-->
<!--		<link rel="stylesheet" href="../../theme/more/style/navstyle.css" type="text/css">	-->
<!--		<link rel="stylesheet" href="../../theme/more/style/alertstyle.css" type="text/css">-->
		<link rel="stylesheet" href="../../theme/more/style/QQface.css" /><!-- 2016.3.25 毛英东 添加表情CSS -->
		<style> </style>

		<link rel="stylesheet" href="css/coursescore.css" type="text/css">

		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="js/scorepage.js"></script>
		<script src="../../theme/more/js/jquery.qqFace.js"></script><!-- 2016.3.25 毛英东 添加表情 -->
<!--		<script src="../../theme/more/js/bootstrap.min.js"></script><!--全局-->
	</head>

	<body>

<?php

/**  START 朱子武 20160226 从数据库获取评分评论*/
	require_once("../../config.php");
	$PAGE->set_title('课程评分');
//	$PAGE->set_pagelayout('usermenu');//设置layout
	$PAGE->set_pagelayout('course_score');//设置layout
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

	//	  输出页码
	function my_get_course_evaluation_current_count($count_page, $courseid,$current_page)
	{
		/** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
		$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
		$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
//	for($num = $numstart; $num <= $numend; $num ++)
		/** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
		for($num = $numstart; $num <= $numend; $num ++)
		{
			if($num == $current_page)
			{
				//  这里需要修改样式标示当前页
				echo'<li><a class="pagination_li_active" href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$num.'">'.$num.'</a></li>';
			}
			else
			{
				echo'<li><a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$num.'">'.$num.'</a></li>';
			}
		}
	}

	//	  输出页码
	function my_get_course_evaluation_current_count2($count_page, $courseid,$current_page)
	{
		/** Start 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
		$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
		$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
//	for($num = $numstart; $num <= $numend; $num ++)
		/** End 设置评论数的显示页码（只显示5页） 朱子武 20160327*/
		$page_num = '';
		for($num = $numstart; $num <= $numend; $num ++)
		{
			if($num == $current_page)
			{
				//  这里需要修改样式标示当前页
				$page_num = '<a class="active" href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$num.'">'.$num.'</a>';
			}
			else
			{
				$page_num = '<a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$num.'">'.$num.'</a>';
			}
			return $page_num;
		}
	}

	/** START 朱子武 获取课程评分星星 20160226*/
	function my_get_glyphicon_star($num)
	{ 	$output = '';
		for($i = 0; $i < ceil($num/2); $i++)
		{
			$output .= '<span class="glyphicon glyphicon-star"></span>';
		}
		return $output;
	}
	/** --- my_get_glyphicon_star END ---*/

	/** START  获取课程评分 */
	function my_get_course_score2($courseid)
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
		}
		else
		{
			$mysumscore = 10.0;
		}

		return $mysumscore;
	}
	/** ---END---*/

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

	/** START 获取课程详细 */
	function my_get_course_detailed2($courseid)
	{
		global $DB;
		global $CFG;
		$course_detailed = $DB->get_records_sql('SELECT id, fullname, shortname, summary, summaryformat FROM mdl_course WHERE id ='.$courseid.'');

		$output = '';
		$output .= '
				<div class="banner">
					<div class="maininfo">
						<div class="l-b">
							<img style="width: 348px;height: 247px;" '.my_get_course_formatted_summary_pix($course_detailed[$courseid]->id, $course_detailed[$courseid]->summary, $course_detailed[$courseid]->summaryformat).' width="348" height="247" />
						</div>
						<div class="r-b">
							<a href="'.$CFG->wwwroot.'/course/view.php?id='.$courseid.'" ><h3 class="title">'.$course_detailed[$courseid]->fullname.'</h3></a><h3 class="title-btn"></h3>
							<div style="clear: both;"></div>
							<p>课程介绍</p>
							<p class="courseinfo">'.mb_substr(strip_tags($course_detailed[$courseid]->summary),0,196,'UTF-8').'</p>
							<p class="num">满意度评分：
							<span>';
		$mysumscore = my_get_course_score2($courseid);//满意度
		$output .=  $mysumscore.'
							</span>&nbsp;分</p>
							<p class="starbox">';
		$output .= my_get_glyphicon_star($mysumscore);//星评数
		$output .= '
							</p>
						</div>
					</div>
				</div>';
		return $output;
	}
	/** ---my_get_course_detailed END ---*/

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

	global $DB;
	$course = $DB->get_record_sql("select * from mdl_course c where c.id = $courseid");

	echo '<!--主板块-->
			<div class="coursetypebox">
			<!--课程信息板块-->
			'.my_get_course_detailed2($courseid).'
			<!--课程信息板块 end-->
			</div>';
	echo '<div class="main">
		<!--课程评价板块-->
		<div class="main-box">
			<div class="scoreinfo">
				<p class="title">对本课程打分：</p>
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
					<textarea class="form-control" id="comment-text" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
                    <img src="../../theme/more/img/emotion.png" class="pull-left emotion" style="width:25px;height:25px;margin-top:4px;cursor:pointer;margin-left: 25px">
					<button id="score-btn" class="btn btn-primary">发表评论</button>
					<div class="division"></div>
				</div>
				<div class="division"></div>
				<!--评论内容-->';

	/**  START 朱子武 20160226 从数据库中获取评论数据 */
	$count_page = my_get_course_evaluation_count($courseid);
	my_get_course_evaluation($courseid, $current_page - 1);

	echo '		<!--评论内容 end-->
         		<!--分页-->
				<div class="paginationbox">
					<a href="../../course/coursegrade/index.php?id='.$courseid.'&page=1">首页</a>
		        	<a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.($current_page <= 1 ? 1: $current_page - 1).'">上一页</a>
		        	'. my_get_course_evaluation_current_count2($count_page, $courseid,$current_page).'
		        	<a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.($current_page < $count_page ? ($current_page + 1): $count_page).'">下一页</a>
		        	<a href="../../course/coursegrade/index.php?id='.$courseid.'&page='.$count_page.'">尾页</a>
				</div>
				<!--分页 end-->
			</div>
		</div>
		<!--课程评价板块 end-->
	</div>

	<!--主板块 end-->';

	//课程笔记隐藏参数设置
		echo '<button id="hiddencourseid" value="'.$course->id.'" style="display: none;"></button>
			  <button id="hiddencoursefullname" value="'.$course->fullname.'" style="display: none;"></button>';
	//end 课程笔记隐藏参数设置

	echo $OUTPUT->footer();//输出左右和底部
?>
	<!-- 2016.3.25 毛英东 添加表情-->
	<script>
		$(function(){
			$('.emotion').qqFace({
				id : 'facebox',
				assign:'comment-text',
				path:'../../theme/more/img/arclist/'	//表情存放的路径
			});
		});

		$('.content-box .content').each(
			function(){
				var str = $(this).html();
				str = str.replace(/\[(微笑|撇嘴|色|发呆|流泪|害羞|闭嘴|睡|大哭|尴尬|发怒|调皮|呲牙|惊讶|难过|冷汗|抓狂|吐|偷笑|可爱|白眼|傲慢|饥饿|困|惊恐|流汗|憨笑|大兵|奋斗|咒骂|疑问|嘘|晕|折磨|衰|敲打|再见|擦汗|抠鼻|糗大了|坏笑|左哼哼|右哼哼|哈欠|鄙视|快哭了|委屈|阴险|亲亲|吓|可怜|拥抱|月亮|太阳|炸弹|骷髅|菜刀|猪头|西瓜|咖啡|饭|爱心|强|弱|握手|胜利|抱拳|勾引|OK|NO|玫瑰|凋谢|红唇|飞吻|示爱)\]/g, function(w,word){
					return '<img src="../../theme/more/img/arclist/'+ em_obj[word] + '.gif" border="0" />';
				});
				$(this).html(str);
			}
		);
	</script>
	<!-- end  2016.3.25 毛英东 添加表情 -->
	</body>

</html>