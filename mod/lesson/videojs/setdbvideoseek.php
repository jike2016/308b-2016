<?php
    //@author��� 2016-01-10 17:40 ȥ���ݿ��ȡ��Ƶ��ʱ�����
    //require_once($CFG->dirroot."\config.php");
    require_once("../../../config.php");
    $URLparam=$_GET['url'];
	$videotime=$_GET['videotime'];
    global $DB;
	global $USER;
	//file_put_contents('a.txt',$URLparam.$videotime,FILE_APPEND);
	if($DB->record_exists('videoseek_my', array('videourl'=>$URLparam,'userid'=>$USER->id))){
		//��������
		$response=$DB->get_record('videoseek_my',array('videourl'=>$URLparam,'userid'=>$USER->id));
		$DB->update_record('videoseek_my', array('id'=>$response->id,'videotime'=>$videotime,'timecreated'=>time()));
	}
    else{
		//��������
		$DB->insert_record('videoseek_my',array('videourl'=>$URLparam,'userid'=>$USER->id,'videotime'=>$videotime,'timecreated'=>time()),true);
	}
    
   
	//echo 'a';

?>



