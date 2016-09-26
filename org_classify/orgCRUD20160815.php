<?php
/** START 朱子武 添加组织机构 20160229*/
global $CFG;
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org_classify/org.class.php');

$org = new org();

$type = $_GET['type'];

switch($type)
{
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
	case 'CheckAssigned': // 查看已分配任务的学员
		global $DB;
		$result = $DB->get_records_sql('select a.id, a.username, a.firstname, a.lastname, b.org_id, c.name from mdl_user as a RIGHT JOIN mdl_org_link_user as b on a.id = b.user_id RIGHT join mdl_org c on b.org_id = c.id where a.id != 2 GROUP BY a.username');
		$re = Array();
		foreach($result as $value)
		{
			$re[] = $value;
		}
		echo json_encode($re);
		break;
	case 'searchuser': // 搜索
		$searchtext = $_GET['searchtext'];
		global $DB;
		$result = $DB->get_records_sql('select a.id, a.username, a.firstname, a.lastname, b.org_id, c.name from mdl_user as a LEFT JOIN mdl_org_link_user as b on a.id = b.user_id LEFT join mdl_org c on b.org_id = c.id where a.id != 2 AND (a.username LIKE "%'.$searchtext.'%" OR firstname like "%'.$searchtext.'%" OR lastname like "%'.$searchtext.'%") ORDER BY a.username');

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
	default: // 其他
		echo '0';
}
