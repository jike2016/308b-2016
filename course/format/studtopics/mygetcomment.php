<?php
    //@author岑霄 2016-01-10 17:40 去数据库获取视频的时间进度
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $mycomment=$_GET['mycomment'];
	$courseid=$_GET['courseid'];
    global $DB;
	global $USER;


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

?>
