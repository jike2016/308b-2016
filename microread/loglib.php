<?php
//微阅日志

/**
 * 添加书籍查看日志
 * @param $action 动作
 * @param $ID
 * @param $target 1:书库 2：文库 3：图库
 */
function add_microreadviewlog($action,$ID,$target)
{
	global $DB;
	global $USER;

	if($USER){
		require_login();
	}
	$log = new stdClass();
	$log->action = $action;
	$log->target = $target;
	$log->contextid = $ID;
	$log->userid = $USER->id;
	$log->timecreated = time();
	$DB->insert_record("microread_log",$log,true);

}

/**
 * 去掉被删除的书籍记录
 */
function del_microreadviewlog(){
	global $DB;
	//在D:\WWW\moodle\admin\my_microread_rank.php 中实现了
}



?>