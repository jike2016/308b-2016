<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license Modified MIT License
 * @link https://blueimp.net/ajax/
 */

// Suppress errors.
//error_reporting(E_ALL);
error_reporting(0);
//include './../config.php';

//wepeng 20160305 判断是否是ajax
/*
if(empty($CFG))
	{
		require("../config.php");
	}*/
	//require("../config.php");
//wepeng 20160309 是否已设置cookie，否跳转到设置界面

Global $wepeng_tree;
if(!isset($_GET['ajax']))
{
/*
	if(empty($CFG))
	{
		require("../config.php");
	}
	require_login();
	//wepeng 20160308 设置用户名和ID在cookie中 24小时之后过期
	//setcookie("wepeng_userID", $USER->id, time()+3600*24);
	//setcookie("wepeng_userName", fullname($USER, true), time()+3600*24);
	
	require_once($CFG->dirroot . '/org/org.class.php');
	
	$org = new org();
	//添加根节点
	if($org->get_root_node_id() === False)
	{
		$root_id = $org->add_root_node("root");
	} else {
		$root_id = $org->get_root_node_id();
	}*/
	if(!isset($_COOKIE['wepeng_userID']))
	{
		header("Location:link.php");
		exit;
	}
	while(!file_exists($_COOKIE['wepeng_userID']."_".$_COOKIE['wepeng_userID'].".txt"))
	{
		sleep(1);
	}
	$wepeng_tree = file_get_contents( $_COOKIE['wepeng_userID']."_".$_COOKIE['wepeng_userID'].".txt");
	//$wepeng_tree = $org->show_node_tree_user($root_id)['tree'];
} else {
	$wepeng_tree = '';
}

//echo $wepeng_tree;
//exit;

// Path to the chat directory:
define('AJAX_CHAT_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');

// Include custom libraries and initialization code:
require(AJAX_CHAT_PATH.'lib/custom.php');

// Include Class libraries:
require(AJAX_CHAT_PATH.'lib/classes.php');

// Initialize the chat:
$ajaxChat = new CustomAJAXChat();
?>