<?php
    //@author岑霄 2016-01-10 17:40 去数据库获取视频的时间进度
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $URLparam=$_GET['url'];
    global $DB;
	global $USER;
	if($DB->record_exists('videoseek_my', array('videourl'=>$URLparam,'userid'=>$USER->id))){
		$response=$DB->get_record('videoseek_my',array('videourl'=>$URLparam,'userid'=>$USER->id));
		echo $response->videotime;
	}
    else{
		echo 0;
	}
    
   
	//echo 'abc123';

?>



