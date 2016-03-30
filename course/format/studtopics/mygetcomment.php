<?php
    //@author岑霄 2016-01-10 17:40 去数据库获取视频的时间进度
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $mycomment=$_GET['mycomment'];
	$courseid=$_GET['courseid'];
    global $DB;
	global $USER;
	$DB->insert_record('comment_course_my',array('courseid'=>$courseid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
	echo '1';
?>
