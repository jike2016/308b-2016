<?php
    //@author岑霄 2016-01-10 17:40 去数据库获取视频的时间进度
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $mycomment=$_GET['mycomment'];
	$courseid=$_GET['courseid'];
    global $DB;
	global $USER;

	$course = $DB->get_record_sql("SELECT * from mdl_course c WHERE c.id = $courseid ");
	$commentresult = $DB->get_records_sql(sprintf('SELECT id, comment, commenttime FROM mdl_comment_course_my WHERE courseid = '.$courseid.' AND userid = '.$USER->id.' ORDER BY commenttime DESC LIMIT 1'));
	$commentallresult = $DB->get_records_sql(sprintf('SELECT id, comment, courseid, userid FROM mdl_comment_course_my ORDER BY commenttime DESC'));
	$similarresult = new stdClass();
	if(count($commentallresult))
	{
		foreach($commentallresult as $commentallresultvalue)
		{
			similar_text($commentallresultvalue->comment, $_GET['mycomment'], $percent);
			if($percent > $similarresult->percent)
			{
				$similarresult->percent = $percent;
				$similarresult->userid = $commentallresultvalue->userid;
				$similarresult->courseid = $commentallresultvalue->courseid;
			}
		}
	}
	if(count($commentresult))
	{
		foreach($commentresult as $commentresultvalue)
		{
//			similar_text($commentresultvalue->comment, $_GET['mycomment'], $percent);

			if($commentresultvalue->commenttime + 60 > time())
			{
				echo '0';
			}elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
			{
				echo '2';
			}elseif($similarresult->percent > 90 && $similarresult->courseid == $courseid)
			{
				echo '2';
			}else
			{
				$DB->insert_record('comment_course_my',array('courseid'=>$courseid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
				echo '1';
			}
		}
	}elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
	{
		echo '2';
	}elseif($similarresult->percent > 90 && $similarresult->courseid == $courseid)
	{
		echo '2';
	}
	else
	{
		$DB->insert_record('comment_course_my',array('courseid'=>$courseid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
		echo '1';
	}


	/** start   获取课程评价 20160729 */
	function my_get_course_evaluation($course, $current_page)
	{
		$my_page = $current_page * 10;
		global $DB;
		global $OUTPUT;
		//只获取刚刚评论的那条数据
		$evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',1', array($course->id));

		$output = '';
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
			$output .= '
					 <div class="evaluation">
						  <div class="evaluation-con">
							 <a href="#" class="img-box">
							 '.$userobject->metadata['useravatar'].'
							 </a>
								 <div class="content-box">
									 <div class="user-info clearfix">
										<a href="#" class="username">'.$value->lastname.$value->firstname.'</a>
									 </div>
									 <!--user-info end-->
								 <p class="content">'.$value->comment.'</p>
									 <div class="info">
									 <span class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</span>
									 </div>
								 </div>
						  <!--content end-->
						  </div>
			<!--evaluation-con end-->
					 </div>
			';
		}
		return $output;
	}

?>

