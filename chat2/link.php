<?php


	require("../config.php");
	
	require_login();
	//wepeng 20160309 设置用户名和ID在cookie中
	setcookie("wepeng_userID", $USER->id);
	setcookie("wepeng_userName", fullname($USER, true));
	
	require_once($CFG->dirroot . '/org/org.class.php');
	
	$org = new org();
	//添加根节点
	if($org->get_root_node_id() === False)
	{
		$root_id = $org->add_root_node("root");
	} else {
		$root_id = $org->get_root_node_id();
	}
	$wepeng_tree = $org->show_node_tree_user($root_id)['tree'];
	file_put_contents( $USER->id."_".$USER->id.".txt" , $wepeng_tree);
	
	header("Location:index.php");