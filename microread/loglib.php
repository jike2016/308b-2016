<?php

//添加书籍查看日志
function addbookviewlog($bookid)
{
	global $DB;
	global $USER;

	if($USER){
		require_login();
	}
	$log = new stdClass();
	$log->action = 'view';
	$log->target = 1;
	$log->contextid = $bookid;
	$log->userid = $USER->id;
	$log->timecreated = time();
	$DB->insert_record("microread_log",$log,true);

}





?>