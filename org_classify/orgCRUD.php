<?php
/** START 朱子武 添加组织机构 20160229*/
global $CFG;
global $USER;
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org_classify/org.class.php');

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
//		$res = $org->select_node_detailed($treeNodeid);
		$res = $org->select_node_detailed_no_adminself($treeNodeid,$USER->id);//去掉当前管理员自身的记录
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

		if($org->move_node($org->get_node($treeNodeid), 'down'))
		{
			echo '1';
		} else {
			echo '0';
		}
		break;
	case 'CheckAll': // 查看所有用户
		global $DB;
		$result = $DB->get_records_sql('select a.id, a.username, a.firstname, a.lastname, b.org_id, c.name from mdl_user as a LEFT JOIN mdl_org_link_user as b on a.id = b.user_id left join mdl_org c on b.org_id = c.id where a.id != 2 GROUP BY a.username');
		$re = Array();
		foreach($result as $value)
		{
			if(!$value->name)
			{
				$value->name = '未分配';
				$value->org_id = '0';
			}
			$re[] = $value;
		}
		echo json_encode($re);
	break;
	case 'CheckAssigned': // 查看已分配单位的人员

		$nodeIDsStr = $org->get_noteID_by_adminUserID($USER->id);
		global $DB;
		$sql = 'select a.id, a.username, a.firstname, a.lastname, b.org_id, c.name from mdl_user as a RIGHT JOIN mdl_org_link_user as b on a.id = b.user_id RIGHT join mdl_org c on b.org_id = c.id where a.id != 2 and a.id != '.$USER->id.' and c.id in ('.$nodeIDsStr.') GROUP BY a.username';
		$result = $DB->get_records_sql($sql);
		$re = Array();
		foreach($result as $value)
		{
			$re[] = $value;
		}
		echo json_encode($re);
		break;
	case 'searchuser': // 搜索
		$searchtext = $_GET['searchtext'];
		$nodeIDsStr = $org->get_noteID_by_adminUserID($USER->id);
		$nodeIDsArray = explode(',',$nodeIDsStr);

		global $DB;
		$sql = 'select a.id, a.username, a.firstname, a.lastname, b.org_id, c.name from mdl_user as a LEFT JOIN mdl_org_link_user as b on a.id = b.user_id LEFT join mdl_org c on b.org_id = c.id where a.id != 2 AND (a.username LIKE "%'.$searchtext.'%" OR firstname like "%'.$searchtext.'%" OR lastname like "%'.$searchtext.'%") ORDER BY a.username';
		$result = $DB->get_records_sql($sql);

		$re = Array();
		foreach($result as $value)
		{
			if(!$value->name)
			{
				$value->name = '未分配';
				$value->org_id = '0';
			}
			else if(!in_array($value->org_id,$nodeIDsArray)) {//筛去已分配单位，但不在管理范围内的账户
				continue;
			}
			$re[] = $value;
		}
		echo json_encode($re);
		break;
	case 'addroot':
		$org->addRoot();
		echo '1';
		break;
	case 'moveNodeAnyWhere':
		$moveid = $_GET['moveid'];
		$newparentid = $_GET['newparentid'];
		if(isset($moveid) && isset($newparentid))
		{
			if($moveid != $newparentid)
			{
				$org->moveNodeAnyWhere($moveid, $newparentid);
				echo '1';
			}
			else
			{
				echo '0';
			}

		}
		else
		{
			echo '0';
		}

		break;
	case 'click_unit_user': // 分级管理员台账任务，提取单位人员
		$treeNodeid = $_GET['treeNodeid'];
		$remove_role = $_GET['remove_role'];
		$res = $org->select_node_detailed_no_unit_no_gradad($treeNodeid,$remove_role);
		echo json_encode($res);
		break;
	default: // 其他
		echo '0';
}

