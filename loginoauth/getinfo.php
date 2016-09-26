<?php
require('../config.php');
//$allow_action_list = ["get_user_info","is_alive"];//可调用函数
$allow_action_list = [
	"get_user_info",
	"is_alive",
	"get_org_name_current_user",
	"can_permission",
	'get_approve','get_organize_user',
	'get_org_user',
	'get_user_only_org',
	'get_duty',
	'get_user_by_duty',
	'get_duty_by_userid',
	'get_office_by_org_id',
	'get_org_user_id',
	'get_id_by_username',
	'get_user_name_by_id',
	'get_org_name_by_id',
	'get_org_all',
	'get_org',
	'get_up_police',
	'get_user_icon'
];//将此行更新到目标文件的首部
if(in_array($_GET['action'],$allow_action_list))
	$_GET['action']();
function get_user_info()
{
	global $DB;
	$allow_get_list=["username","firstname","lastname","email","id"];//这里限定可以获取的字段
	if (isset($_GET['accesstoken']) && !empty($_GET['accesstoken']) && isset($_GET['field']) && !empty($_GET['field']))//field为获取的字段
	{
		if(in_array($_GET['field'],$allow_get_list))
		{
			$accesstoken=$_GET['accesstoken'];
			$field=$_GET['field'];
			$result=$DB->get_records_sql("select t.`".$field."` from mdl_user as t inner join mdl_accesstoken as t1 on t.id=t1.user_id where t1.access_token='".$accesstoken."'");
			foreach($result as $key=>$value)
				echo $value->$field;
		}
	}
}
//
function is_alive()
{
	global $DB;
	if (isset($_GET['accesstoken']) && !empty($_GET['accesstoken']) && isset($_GET['user_id']) && !empty($_GET['user_id']))
	{
		$access_token=$_GET['accesstoken'];
		$user_id=$_GET['user_id'];
		$time=date("Y-m-d h:i:s",time()-3600);
		if($DB->get_records_sql("select * from mdl_accesstoken where access_token='".$access_token."' and user_id=".$user_id." and start_time>='$time'"))
		{
			echo "alive";
		}
		else
		{
			echo "expired";
		}
	}
}

//获取当前用户所在组织名字
function get_org_name_current_user()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$accesstoken = $_GET['accesstoken'];
		$result=$DB->get_records_sql('SELECT t.name FROM mdl_org t JOIN mdl_org_link_user t1 ON t.id=t1.org_id WHERE t1.user_id IN (SELECT user_id FROM mdl_accesstoken WHERE access_token = "'.$accesstoken.'")');
		if(count($result))
		{
			//	格式化输出数据
			$value = current($result);
			$res = sprintf_result($value->name);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

//获取权限
function can_permission() // 2、绩效管理员 3、部门领导
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$accesstoken = $_GET['accesstoken'];
		$result = $DB->get_records_sql(sprintf('SELECT a.roleid, a.userid, b.user_id  FROM mdl_role_assignments AS a JOIN mdl_accesstoken AS b ON b.user_id = a.userid WHERE b.access_token = "'.$accesstoken.'"'));
		if(count($result))
		{
			$value = current($result);
			switch ($value->roleid)
			{
				case '14': // 单位账号
					$user = $DB->get_records_sql('SELECT a.id, a.firstname FROM mdl_user AS a JOIN mdl_accesstoken AS b ON a.id = b.user_id WHERE a.firstname LIKE "%绩效管理员%" AND b.access_token="'.$accesstoken.'"');
					if(count($user))
					{
						echo '2'; // 没有评分权限
					}
					else
					{
						echo '4'; //拥有评分权限
					}
					break;
				case '15':
					echo '3';
					break;
				default:
					echo '0';
					break;
			}
		}
		else
		{
			echo '0';
		}
	}
}

// 获取审批对象
function get_approve()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$accesstoken = $_GET['accesstoken'];
		$result = $DB->get_records_sql(sprintf('SELECT e.user_id FROM mdl_org_link_user AS e JOIN mdl_role_assignments AS f ON f.userid=e.user_id WHERE f.roleid=14 AND org_id IN(SELECT a.parent FROM mdl_org AS a JOIN mdl_org_link_user AS b ON a.id = b.org_id WHERE b.user_id IN (SELECT c.userid FROM mdl_role_assignments AS c JOIN mdl_accesstoken AS d ON c.userid = d.user_id WHERE d.access_token="'.$accesstoken.'"))'));
		if(count($result))
		{
			//	格式化输出数据
			$value = current($result);
			$res = sprintf_result($value->user_id);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}

}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160805
 * 功能     ：获取考核对象
 */
function get_organize_user()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$accesstoken = $_GET['accesstoken'];
		switch ($_GET['type'])
		{
			case 'self': // 获取本部门所有人员(不包括单位账号)
				$result = $DB->get_records_sql(sprintf('SELECT c.user_id, u.firstname FROM mdl_org_link_user AS c JOIN mdl_user AS u ON u.id = c.user_id WHERE c.org_id IN (SELECT a.org_id FROM mdl_org_link_user AS a JOIN mdl_accesstoken AS b ON a.user_id=b.user_id WHERE b.access_token="'.$accesstoken.'") AND c.user_id NOT IN (SELECT d.userid FROM mdl_role_assignments AS d WHERE d.roleid = 14)'));
				if(count($result))
				{
					//	格式化输出数据
					$res = sprintf_result(serialize($result));
					echo json_encode($res);
				}
				else  
				{
					//	格式化输出数据
					$res = sprintf_result('', 403);
					echo json_encode($res);
				}
				break;
			case 'lower': // 获取下级部门所有人员(不包括单位账号)
				$result = $DB->get_records_sql(sprintf('SELECT a.user_id, u.firstname FROM mdl_org_link_user AS a JOIN mdl_user AS u ON u.id = a.user_id WHERE a.org_id IN (SELECT b.id FROM mdl_org AS b WHERE b.parent IN (SELECT d.org_id FROM mdl_org_link_user AS d JOIN mdl_accesstoken AS e ON e.user_id = d.user_id WHERE e.access_token="'.$accesstoken.'")) AND a.user_id NOT IN (SELECT r.userid FROM mdl_role_assignments AS r WHERE r.roleid = 14)'));
				if(count($result))
				{
					//	格式化输出数据
					$res = sprintf_result(serialize($result));
					echo json_encode($res);
				}
				else
				{
					//	格式化输出数据
					$res = sprintf_result('', 403);
					echo json_encode($res);
				}
				break;
			default:
				break;
		}	
	}
}

//获取组织下的所有用户
function get_org_user()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']&&isset($_GET['org_id']) &&!empty($_GET['org_id'])))
	{
		$org_id = $_GET['org_id'];
		$result = $DB->get_records_sql(sprintf('SELECT b.id,b.firstname FROM mdl_org_link_user AS a JOIN mdl_user AS b ON a.user_id=b.id WHERE org_id = '.$org_id));
		if(count($result))
		{
			//	格式化输出数据
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160816
 * 功能     ：获取当前用户所在部门的所有成员
 * 返回值   ：如果没有找到则返回false，找到则返回人员数组
 */
function get_user_only_org()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$result = $DB->get_records_sql(sprintf('SELECT b.id, b.firstname FROM mdl_org_link_user AS a JOIN mdl_user AS b ON a.user_id=b.id WHERE a.org_id IN (SELECT org_id FROM mdl_org_link_user AS c JOIN mdl_accesstoken AS d ON c.user_id=d.user_id WHERE d.access_token="'.$_GET['accesstoken'].'")'));
		if(count($result))
		{
//			格式化输出数据
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

function sprintf_result($data, $status = 200 )
{
	switch ($status)
	{
		case 200:
			$result = [
				'status'=>'200',
				'message'=>'success',
				'data'=>$data
			];
			break;
		default:
			$result = [
				'status'=>'403',
				'message'=>'error',
				'data'=>$data
			];
			break;
	}
	return $result;
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160819
 * 功能     ：获取职务
 * 返回值   ：如果没有找到则返回false，找到则返回职务数组
 */
function get_duty()
{
	global $DB;
	$result = $DB->get_records_sql(sprintf('SELECT user_id FROM mdl_accesstoken WHERE user_id = 2 AND access_token="'.$_GET['accesstoken'].'"'));
	if (count($result))
	{
		$dutys = $DB->get_records_sql(sprintf('SELECT param1 FROM mdl_user_info_field WHERE shortname = "zhiwu"'));
		if(count($dutys))
		{
			$duty = current($dutys)->param1;
			$arr = explode("\n",$duty);//"\n"作为分隔切成数组
			$dutyArr = [];
			//除去数组中的空格
			foreach($arr as $val)
			{
				if (isset($val) AND $val)
				{
					array_push($dutyArr, $val);
				}
			}
			//			格式化输出数据
			$res = sprintf_result(serialize($dutyArr));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
	else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}

/** 通过职务名称获取用户 */
function get_user_by_duty()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$result = $DB->get_records_sql(sprintf('SELECT userid FROM mdl_user_info_data WHERE `data` = "'.$_GET['duty'].'"'));
		if(count($result))
		{
//			格式化输出数据
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

/** 通过用户id获取职务 */
function get_duty_by_userid()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$result = $DB->get_records_sql(sprintf('SELECT `data` FROM mdl_user_info_data WHERE fieldid = 3 AND userid = '.$_GET['id']));
		if(count($result))
		{
//			格式化输出数据
			$res = sprintf_result(current($result)->data);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
	else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160823
 * 功能     ：根据组织ID获取组织下的单位账号（除去绩效管理员）
 */
function get_office_by_org_id()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$result = $DB->get_records_sql('SELECT a.user_id, b.firstname FROM  mdl_org_link_user AS a JOIN mdl_user AS b ON a.user_id=b.id WHERE a.org_id='.$_GET["id"].' AND a.user_id IN (SELECT c.userid FROM mdl_role_assignments AS c WHERE c.roleid = 14) AND b.firstname NOT LIKE "%绩效管理员%"');
		if(count($result))
		{
//			格式化输出数据
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
	else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}

function get_org_user_id()
{
	global $DB;
	if(isset($_GET['accesstoken']) &&!empty($_GET['accesstoken']))
	{
		$result=$DB->get_records_sql('SELECT t.name, t1.org_id FROM mdl_org t JOIN mdl_org_link_user t1 ON t.id=t1.org_id WHERE t1.user_id='.$_GET['id']);
		if(count($result))
		{
			//	格式化输出数据
//			$value = $result;
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160825
 * 功能     ：获取用户id
 *
 * $id      ：用户的名字
 *
 * 返回值   ：存在则返回用户id，不存在则返回-1；
 */
function get_id_by_username()
{
	global $DB;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$result=$DB->get_records_sql(sprintf('SELECT id FROM mdl_user WHERE username="%s" OR firstname="%s"', $_GET['id'], $_GET['id']));
		if(count($result))
		{
			//	格式化输出数据
			$value = current($result);
			$res = sprintf_result($value->id);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

function get_user_name_by_id()
{
	global $DB;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$result=$DB->get_records_sql(sprintf('SELECT firstname FROM mdl_user WHERE id=%s', $_GET['id']));
		if(count($result))
		{
			//	格式化输出数据
			$value = current($result);
			$res = sprintf_result($value->firstname);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

function get_org_name_by_id()
{
	global $DB;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$result=$DB->get_records_sql(sprintf('SELECT `name` FROM mdl_org WHERE id = %s', $_GET['id']));
		if(count($result))
		{
			//	格式化输出数据
			$value = current($result);
			$res = sprintf_result($value->name);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160830
 * 功能     ：获取组织
 * return   ： 组织数组（名字和ID）
 */
function get_org_all()
{
	global $DB;
	$authority = $DB->get_records_sql(sprintf('SELECT user_id FROM mdl_accesstoken WHERE user_id = 2 AND access_token="'.$_GET['accesstoken'].'"'));
	if (count($authority))
	{
		$result = $DB->get_records_sql(sprintf('SELECT * FROM mdl_org'));
		if (count($result))
		{
			//	格式化输出数据
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
	else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160830
 * 功能     ：获取当前用户所在部门及其子部门
 * $org_id  : 用户ID
 * 返回值   ：如果没有找到则返回false，找到则返回部门数组
 */
function get_org()
{
	global $DB;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$org = $DB->get_records_sql(sprintf('SELECT a.* FROM mdl_org AS a JOIN mdl_org_link_user AS b ON a.id=b.org_id WHERE b.user_id=%s', $_GET['id']));
		if(count($org))
		{
			//	格式化输出数据
			$value = current($org);
			$result = $DB->get_records_sql(sprintf('SELECT id, `name` FROM mdl_org WHERE lft>=%s AND rgt<=%s ORDER BY rank ASC', $value->lft, $value->rgt));
			$res = sprintf_result(serialize($result));
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
}

/**
 * 作者     ：zzwu
 * 修改时间 ：20160901
 * 功能     ：通过user_id获取上级警长
 */
function get_up_police()
{
	global $DB;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$result = $DB->get_records_sql(sprintf('SELECT police_id FROM mdl_police_connection WHERE user_id = '. $_GET['id']));
		if (count($result)){
			//	格式化输出数据
			$res = sprintf_result(current($result)->police_id);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}
	else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}

function get_user_icon()
{
	global $DB;
	global $OUTPUT;
	if(isset($_GET['accesstoken']) && !empty($_GET['accesstoken']))
	{
		$users = $DB->get_records_sql(sprintf('SELECT user_id FROM mdl_accesstoken WHERE access_token="%s"', $_GET['accesstoken']));
		if(count($users))
		{
			$user = $DB->get_record('user', array('id' => current($users)->user_id), '*', MUST_EXIST);
			$result = $OUTPUT->user_picture($user,array('link' => false,'visibletoscreenreaders' => false));
			$str=substr($result,10);//去除前面
			$n=strpos($str,'"');//寻找位置
			if ($n) $str=substr($str,0,$n);//删除后面
			//	格式化输出数据
//			$value = current($org);
//			$result = $DB->get_records_sql(sprintf('SELECT id, `name` FROM mdl_org WHERE lft>=%s AND rgt<=%s ORDER BY rank ASC', $value->lft, $value->rgt));
			$res = sprintf_result($str);
			echo json_encode($res);
		}
		else
		{
			//	格式化输出数据
			$res = sprintf_result('', 403);
			echo json_encode($res);
		}
	}else
	{
		//	格式化输出数据
		$res = sprintf_result('', 403);
		echo json_encode($res);
	}
}