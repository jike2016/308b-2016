<?php
    //@author岑霄 2016-01-10 17:40 去数据库获取视频的时间进度
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $URLparam=$_GET['url'];
	$videotime=$_GET['videotime'];
    global $DB;
	global $USER;
	//file_put_contents('a.txt',$URLparam.$videotime,FILE_APPEND);
	if($DB->record_exists('videoseek_my', array('videourl'=>$URLparam,'userid'=>$USER->id))){
		//更新数据
		$response=$DB->get_record('videoseek_my',array('videourl'=>$URLparam,'userid'=>$USER->id));
		$DB->update_record('videoseek_my', array('id'=>$response->id,'videotime'=>$videotime,'timecreated'=>time()));
	}
    else{
		//插入数据
		$DB->insert_record('videoseek_my',array('videourl'=>$URLparam,'userid'=>$USER->id,'videotime'=>$videotime,'timecreated'=>time()),true);
	}
    
   
	//echo 'a';

?>



