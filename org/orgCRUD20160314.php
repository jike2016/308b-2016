<?php
/** START 朱子武 添加组织机构 20160229*/

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org/org.class.php');

$org = new org();

$type = $_GET['type'];

switch($type)
{
	case 'rename': // 重命名
		$treeNodeid = $_GET['treeNodeid'];
		$treeNodename = $_GET['treeNodename'];
		$org->rename_node($treeNodeid,$treeNodename);
		echo '1';
		break;
	case 'delete': // 删除
		$treeNodeid = $_GET['treeNodeid'];
		$org->del_node($treeNodeid);
		echo '1';
		break;
	case 'add': // 添加
	{
		$treeNodeid = $_GET['treeNodeid'];
		$treeNodename = $_GET['treeNodename'];
		$pos = $_GET['pos'];
		$root_node = $org->get_node($treeNodeid);
		$id = $org->insert_node($root_node, $treeNodename, $pos);
		echo '1';
		break;
	}
	case 'click': // 点击
		$treeNodeid = $_GET['treeNodeid'];
		$res = $org->select_node_detailed($treeNodeid);
		echo json_encode($res);
		break;
	case 'click_all_user': // 获取所有用户
		$treeNodeid = $_GET['treeNodeid'];
		$res = $org->select_node_detailed_all($treeNodeid);
		echo json_encode($res);
		break;
	case 'click_all_user_not_self': // 获取所有用户(不包括自己)
		$treeNodeid = $_GET['treeNodeid'];
		$res = $org->select_node_detailed_all_not_self($treeNodeid);
		echo json_encode($res);
		break;
	case 'userdelete': // 删除用户
		$arrayObj = $_GET['arrayObj'];
		foreach($arrayObj as $valueid)
		{
			$org->del_user_node($valueid);
		}
		echo '1';
		break;
	case 'userAdd': // 添加用户
		$res = $org->select_no_user_detailed();
		echo $res;
		break;
	case 'useraddconfirm': // 确认添加
		$treeNodeid = $_GET['treeNodeid'];
		$arrayObj = $_GET['arrayObj'];
		foreach($arrayObj as $valueid)
		{
			$org->add_user($treeNodeid, $valueid);
		}
		echo '1';
		break;
	case 'uplevel': // 上移
		$treeNodeid = $_GET['treeNodeid'];
//		if($org->move_node_id($treeNodeid, $action='up'))
		if($org->move_node($org->get_node($treeNodeid), 'up'))
		{
			echo '1';
		} else {
			echo '0';
		}
		break;
	case 'downlevel': // 下移
		$treeNodeid = $_GET['treeNodeid'];

//		if($org->move_node_id($treeNodeid, $action='down'))
		if($org->move_node($org->get_node($treeNodeid), 'down'))
		{
			echo '1';
		} else {
			echo '0';
		}
		break;
	default: // 其他
		echo '0';
}

/*
if($type == 'rename')
{
	$treeNodeid = $_GET['treeNodeid'];
	$treeNodename = $_GET['treeNodename'];
	$org->rename_node($treeNodeid,$treeNodename);
	echo '1';
}

elseif($type == 'delete')
{
	$treeNodeid = $_GET['treeNodeid'];
	$org->del_node($treeNodeid);
	echo '1';
}

elseif($type == 'add')
{
//	data: { treeNodeid: treeNode.id, treeNodename: newName, pos:pos，type: 'add'}
	$treeNodeid = $_GET['treeNodeid'];
	$treeNodename = $_GET['treeNodename'];
	$pos = $_GET['pos'];
	$root_node = $org->get_node($treeNodeid);
	$id = $org->insert_node($root_node, $treeNodename, $pos);
	echo '1';
}
elseif($type == 'click')
{
	//'SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10'

	//$root_node = $org->get_node($treeNodeid);
	$treeNodeid = $_GET['treeNodeid'];
	$res = $org->select_node_detailed($treeNodeid);
	echo json_encode($res);
}
elseif($type == 'click_all_user')
{
	//'SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10'

	//$root_node = $org->get_node($treeNodeid);
	$treeNodeid = $_GET['treeNodeid'];
	$res = $org->select_node_detailed_all($treeNodeid);
	echo json_encode($res);
}
elseif($type == 'userdelete')
{
	$arrayObj = $_GET['arrayObj'];
	foreach($arrayObj as $valueid)
	{
		$org->del_user_node($valueid);
	}
	echo '1';
}

elseif($type == 'userAdd')
{
	$res = $org->select_no_user_detailed();
	echo $res;
}
elseif($type == 'useraddconfirm')
{
	$treeNodeid = $_GET['treeNodeid'];
	$arrayObj = $_GET['arrayObj'];
	foreach($arrayObj as $valueid)
	{
		$org->add_user($treeNodeid, $valueid);
	}
	echo '1';
}

else
{
	echo '0';
}
*/

/*
//添加子节点1
$root_node = $org->get_node($root_id);
$id = $org->insert_node($root_node, '21', 'down');

//添加子节点2
$root_node = $org->get_node($id);
$id = $org->insert_node($root_node,  '16', 'down');

//添加子节点2的子节点1
$root_node = $org->get_node($id);
$id = $org->insert_node($root_node, '190', 'down');

//添加子节点2的子节点1的兄弟节点
$id_node = $org->get_node($id);
$id = $org->insert_node($id_node, '181');

//添加子节点2的子节点1的兄弟节点
$id_node = $org->get_node($id);
$id = $org->insert_node($id_node, '191', 'down');

//添加子节点2的子节点1的兄弟节点
$id_node = $org->get_node($id);
$id = $org->insert_node($id_node, '191', 'down');
*/

//$org->rename_node(40,"测试");
//$org->del_node(41);

/** ---END---*/
