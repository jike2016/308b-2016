<?php

if(empty($CFG))
{
	require("../config.php");
}

class org 
{
	//存储组织架构的表
	private $table = 'mdl_org';
	//存储组织架构与moodle中用户对应的表
	//一个用户可以属于多个组
	private $link_user_table =  'mdl_org_link_user';
	private $conn;
	
	/**
	* 构造函数(需要修改)
	*
	*/
	function __construct()
	{
		$charset = "utf8"; 
		$this->conn = new mysqli('localhost', 'root', 'root', 'moodle');
		$sql="SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary";
		$this->conn->query($sql);
	}
	
	
	/**
	* 给组添加用户
	*
	* $name:  节点的名称
	*
	* 返回值：参数错误返回false(注意0和false的区别)
	*/
	function add_user($id, $user_id)
	{
		//后期还需要加上用户和组的ID是否存在的判断
		$sql = "SELECT *  FROM `".$this->link_user_table."` WHERE `org_id` = '$id' AND `user_id` = '$user_id'";
		$res = $this->conn->query($sql)->fetch_array(MYSQLI_ASSOC);
		//不存在则添加
		if(empty($res))
		{
			$sql = "INSERT INTO `".$this->link_user_table."` (`id`, `org_id`, `user_id`) VALUES (NULL, '$id', '$user_id')";
			$this->conn->query($sql);
		}
	}
	
	/**
	* 添加根节点
	*
	* $name:  节点的名称
	*
	* 返回值：参数错误返回false(注意0和false的区别)
	*/
	function add_root_node($name)
	{
		$sql = "INSERT INTO `".$this->table."` (`id`, `name`, `level`, `lft`, `rgt`, `parent`,  `rank`) VALUES (NULL, '$name', '1','1', '2', '-1', '0')";
		$this->conn->query($sql);
		return $this->conn->insert_id;
	}
	
	/**
	* 获取根节点ID
	*
	* 返回值：存在返回节点ID，不存在则返回False
	*/
	function get_root_node_id()
	{
		$sql = "SELECT * FROM  `".$this->table."` WHERE  `parent` = -1";
		$res = $this->conn->query($sql)->fetch_array(MYSQLI_ASSOC);
		if(!empty($res))
		{
			return $res['id'];
		} else {
			return False;
		}
	}



	/** wepeng 20160304
	 *  交换两个节点的位置（包括子节点）
	 * 要求，$node1['lft'] < $node2['lft']
	 *
	 * $node1:第一个节点的信息
	 * $node2:第二个节点的信息
	 *
	 * 返回值：True or False
	 */
	function switch_node($node1, $node2)
	{
//		if(($node1['lft'] >= $node2['lft']) || ($node1['level'] != $node2['level']) )
//		{
//			return False;
//		}
		if(($node1['parent'] != $node2['parent']) || ($node1['level'] != $node2['level']) )
		{
			return False;
		}
		//交换rank值
		$sql = "update `".$this->table."` set `rank`='".$node1['rank']."' where `id`='".$node2['id']."'";
		$this->conn->query($sql);
		$sql = "update `".$this->table."` set `rank`='".$node2['rank']."' where `id`='".$node1['id']."'";
		$this->conn->query($sql);
		//$node2 每个节点需要减多少(多减100000用于生成临时树)
//		$num = 100000;
//		$sub = $node2['lft'] - $node1['lft'] + $num;
//		//$node1 每个节点需要加多少
//		$add = $this->get_all_num($node2)*2;
//		//更新node2（临时树）
//		$sql = "update `".$this->table."` set `lft`=`lft`-$sub,`rgt`=`rgt`-$sub where `lft`>='".$node2['lft']."' and `rgt`<='".$node2['rgt']."'";
//		$this->conn->query($sql);
//		//更新node1
//		$sql = "update `".$this->table."` set `lft`=`lft`+$add,`rgt`=`rgt`+$add where `lft`>='".$node1['lft']."' and `rgt`<='".$node1['rgt']."'";
//		$this->conn->query($sql);
//		//更新node2
//		$sql = "update `".$this->table."` set `lft`=`lft`+$num,`rgt`=`rgt`+$num where `lft`>='".($node2['lft']-$sub)."' and `rgt`<='".($node2['rgt']-$sub)."'";
//		$this->conn->query($sql);
		return True;
	}

	/** wepeng 20160304
	 *  移动当前节点的位置
	 *
	 * $node:当前节点的信息
	 * $action: 往上（up），往下（down）
	 *
	 * 返回值：成功或失败
	 */
	function move_node($node, $action = 'up')
	{
		//获取父节点信息
		$parent = $this->get_node($node['parent']);
		if($action == 'up')
		{
			//往上移动
			$sql = "select * from `".$this->table."` where `level`='".$node['level']."' and `rank`<'".$node['rank']."' and `lft`>'".$parent['lft']."' and `rgt`<'".$parent['rgt']."' order by `rank` desc limit 1";
			$result = $this->conn->query($sql);
			$node1 = $result->fetch_array(MYSQLI_ASSOC);
			if(empty($node1) || !$this->switch_node($node1, $node))
			{
				return false;
			}
			//switch_node($node1, $node);
		} else {
			//往下移动
			$sql = "select * from `".$this->table."` where `level`='".$node['level']."' and `rank`>'".$node['rank']."' and `lft`>'".$parent['lft']."' and `rgt`<'".$parent['rgt']."' order by `rank` asc limit 1";
			$result = $this->conn->query($sql);
			$node1 = $result->fetch_array(MYSQLI_ASSOC);
			if(empty($node1) || !$this->switch_node($node, $node1))
			{
				return false;
			}
			//switch_node($node, $node1);
		}
		return True;
	}


	/** wepeng 20160304
	 * 获取当前节点的子节点（down）的最大排序值加1
	 *
	 * $node:前一个节点的信息
	 *
	 * 返回值：最大排序值加1
	 */
	function get_max_rank($node)
	{
//		$sql = "select max(`rank`), id from `".$this->table."` where `level`='".($node['level']+1)."' and `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."'";
		$sql = "select max(`rank`), id from `".$this->table."` where `level`='".($node['level']+1)."' and `parent`='".$node['id']."'";
		$result = $this->conn->query($sql);
		$res = $result->fetch_array(MYSQLI_ASSOC);
		return $res["max(`rank`)"]+1;
	}

	/** wepeng 20160304
	 * 插入一个新的节点
	 *
	 * $pre_node:前一个节点的信息
	 * $name:  节点的名称
	 * $pos: 位置，有兄弟节点（right），子节点（down）
	 *
	 * 返回值：参数错误返回false(注意0和false的区别)
	 */
	function insert_node($pre_node, $name, $pos='right', $rank=0)
	{
		if($pos == 'right') //添加平行节点,目前无法添加rank
		{
			$sql = "update `".$this->table."` set `lft`=`lft`+2 where `lft`>'".$pre_node['rgt']."'";
			$this->conn->query($sql);
			$sql = "update `".$this->table."` set `rgt`=`rgt`+2 where `rgt`>'".$pre_node['rgt']."'";
			$this->conn->query($sql);
			$sql = "INSERT INTO `".$this->table."` (`id`, `name`, `level`, `lft`, `rgt`, `parent`, `rank`) VALUES (NULL, '$name', '".($pre_node['level'])."','".($pre_node['rgt']+1)."', '".($pre_node['rgt']+2)."', '".$pre_node['parent']."', '$rank')";
			$this->conn->query($sql);
		} else {  //添加子节点
			$sql = "update `".$this->table."` set `lft`=`lft`+2 where `lft`>'".$pre_node['lft']."'";
			$this->conn->query($sql);
			$sql = "update `".$this->table."` set `rgt`=`rgt`+2 where `rgt`>'".$pre_node['lft']."'";
			$this->conn->query($sql);
			//20160304 添加get_max_rank函数
			$sql = "INSERT INTO `".$this->table."` (`id`, `name`, `level`, `lft`, `rgt`, `parent`, `rank`) VALUES (NULL, '$name', '".($pre_node['level']+1)."','".($pre_node['lft']+1)."', '".($pre_node['lft']+2)."', '".$pre_node['id']."', '".$this->get_max_rank($pre_node)."')";
			$this->conn->query($sql);
		}
		return $this->conn->insert_id;
	}

	/**
	* 插入一个新的节点
	*
	* $pre_node:前一个节点的信息
	* $name:  节点的名称
	* $pos: 位置，有兄弟节点（right），子节点（down）
	*
	* 返回值：参数错误返回false(注意0和false的区别)
	*
	function insert_node($pre_node, $name, $pos='right', $rank=0)
	{
		if($pos == 'right') //添加平行节点
		{
			$sql = "update `".$this->table."` set `lft`=`lft`+2 where `lft`>'".$pre_node['rgt']."'";
			$this->conn->query($sql);
			$sql = "update `".$this->table."` set `rgt`=`rgt`+2 where `rgt`>'".$pre_node['rgt']."'";
			$this->conn->query($sql);
			$sql = "INSERT INTO `".$this->table."` (`id`, `name`, `level`, `lft`, `rgt`, `parent`, `rank`) VALUES (NULL, '$name', '".($pre_node['level'])."','".($pre_node['rgt']+1)."', '".($pre_node['rgt']+2)."', '".$pre_node['parent']."', '$rank')";
			$this->conn->query($sql);
		} else {  //添加子节点
			$sql = "update `".$this->table."` set `lft`=`lft`+2 where `lft`>'".$pre_node['lft']."'";
			$this->conn->query($sql);
			$sql = "update `".$this->table."` set `rgt`=`rgt`+2 where `rgt`>'".$pre_node['lft']."'";
			$this->conn->query($sql);
			$sql = "INSERT INTO `".$this->table."` (`id`, `name`, `level`, `lft`, `rgt`, `parent`, `rank`) VALUES (NULL, '$name', '".($pre_node['level']+1)."','".($pre_node['lft']+1)."', '".($pre_node['lft']+2)."', '".$pre_node['id']."', '$rank')";
			$this->conn->query($sql);
		}
		return $this->conn->insert_id;
	}
	 */
	
	/**
	* 获取两个节点的路径
	*
	* $node1: 节点1
	* $node2: 节点1
	* 
	* 返回值：不存在路径则返回空数组
	*/
	function get_path($node1, $node2)
	{
		$sql = "select * from `".$this->table."` where `lft` between '".$node1['lft']."' and '".$node2['lft']."' and `rgt` between '".$node1['rgt']."' and '".$node2['rgt']."' order by `lft`";
		$result = $this->conn->query($sql);
		$re = Array();
		while($res= $result->fetch_array(MYSQLI_ASSOC))
		{
			$re[] = $res;
		}
		return $re;
	}
	
	/**
	* 获取当前节点下的所有节点
	*
	* $node: 节点
	* 
	* 返回值：所有节点的数组(没有则为空数组)
	*/
	function get_all_child($node)
	{
//		$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
//		考虑排序 朱子武  20160306
		$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `rank`";
		$result = $this->conn->query($sql);
		$re = Array();
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			$re[] = $res;
		}
		return $re;
	}
	
	/**
	* 获取当前节点下的所有节点的数目（包括当前节点）
	*
	* $node: 节点
	* 
	* 返回值：包括当前节点的节点数
	*/
	function get_all_num($node)
	{
		return ($node['rgt']-$node['lft']+1)/2;
	}
	
	/**
	* 根据ID获取当前节点的信息
	*
	* $node: 节点
	* 
	* 返回值：节点信息(不存在则返回空)
	*/
	function get_node($nodeId)
	{
		$sql = "select * from `".$this->table."` where `id`='$nodeId'";
		return $this->conn->query($sql)->fetch_array(MYSQLI_ASSOC);
	}

	/**
	 * 根据ID获取不在当前节点的用户信息
	 *
	 * $node: 节点
	 *
	 * 返回值：用户信息(不存在则返回空)
	 */
	function select_no_user_detailed()
	{
		$sql = "SELECT a.id, a.firstname, a.lastname FROM mdl_user AS a WHERE  a.id NOT IN (SELECT user_id FROM mdl_org_link_user) AND a.id != 2";
//		$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$nodeId."";
//		$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
		$result = $this->conn->query($sql);
//		global $DB;
//		$result = $DB->get_records_sql("SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ?", array($nodeId));

		$re = Array();
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			$re[] = $res;
		}
		return json_encode($re);
//		return $re;
	}

	/**
	 * 根据ID获取当前节点的用户信息
	 *
	 * $node: 节点
	 *
	 * 返回值：用户信息(不存在则返回空)
	 */
	function select_node_detailed($nodeId)
	{
		$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$nodeId."";
		//$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
		$result = $this->conn->query($sql);
//		global $DB;
//		$result = $DB->get_records_sql("SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ?", array($nodeId));

		$re = Array();
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			$re[] = $res;
		}
//		$s = json_encode($result);
//		foreach($result as $value)
//		{
//			$c = json_encode($value);
//		}
//		return json_encode($re);
		return $re;
	}

	/**
	 * 根据ID获取当前节点的信息修改名字
	 *
	 * $node: 节点
	 *
	 * 返回值：节点信息(不存在则返回空)
	 */
	function rename_node($nodeId, $name)
	{
		$sql = "update ".$this->table." set `name` = '".$name."' where `id` = '".$nodeId."'";
		$this->conn->query($sql);
	}

	/**
	 * 删除用户
	 *
	 * $nodeId: 用户的ID
	 *
	 * 返回值：无
	 */
	function del_user_node($userId)
	{
		$sql = "delete from `".$this->link_user_table."`  where `user_id`='".$userId."'";
		$this->conn->query($sql);
	}
	
	/**
	* 删除节点
	*
	* $nodeId: 节点的ID
	* 
	* 返回值：无
	*/
	function del_node($nodeId)
	{
		$node = $this->get_node($nodeId);
		if(empty($node))
		{
			return;
		}

		$resultNode = $this->get_all_child($node);
		foreach($resultNode as $key=>$row)
		{
			//删除本节点所有用户
			$sql = "delete from `".$this->link_user_table."`  where `org_id`='".$row['id']."'";
			$this->conn->query($sql);
		}

		//如果删除的节点是叶子节点
		if($node['lft']+1 == $node['lft'])
		{
			//后续节点lft减2
			$sql = "update `".$this->table."` set `lft`=`lft`-2 where `lft`>'".$node['rgt']."'";
			$this->conn->query($sql);
			//后续节点rgt减2
			$sql = "update `".$this->table."` set `rgt`=`rgt`-2 where `rgt`>'".$node['rgt']."'";
			$this->conn->query($sql);
			//删除本节点
			$sql = "delete from `".$this->table."`  where `id`='".$nodeId."'";
			$this->conn->query($sql);
		} else {
			//删除所有子节点
			$sql = "delete from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
			$result = $this->conn->query($sql);
			//中间缺少的数
			$number = $node['rgt'] - $node['lft'] + 1;
			//后续节点lft减中间缺少的数
			$sql = "update `".$this->table."` set `lft`=`lft`-$number where `lft`>'".$node['rgt']."'";
			$this->conn->query($sql);
			//后续节点rgt减中间缺少的数
			$sql = "update `".$this->table."` set `rgt`=`rgt`-$number where `rgt`>'".$node['rgt']."'";
			$this->conn->query($sql);
			//删除本节点
			$sql = "delete from `".$this->table."`  where `id`='".$nodeId."'";
			$this->conn->query($sql);
		}
		//删除本节点所有用户
		$sql = "delete from `".$this->link_user_table."`  where `org_id`='".$nodeId."'";
		$this->conn->query($sql);
	}
	
	/**
	* 显示节点树(未考虑排序)
	*
	* $nodeId: 节点的ID
	* 
	* 返回值：字符串
	*/
	function show_node_tree($nodeId)
	{
		$node = $this->get_node($nodeId);
		//显示本节点
		//echo '<div style="margin-left:'.($node['level']*12).'px">'.$node['name'].'</div>';
		$node_tree['name'] = $node['name'];
		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);
		// 显示树的每个节点
		$node_tree['tree'] = "{ id:'".$node['id']."', pId:'".$node['parent']."', name:'".$node['name']."', open:true},";
		$flag = true;
		foreach($tree as $key=>$row)
		{
			if($flag)
			{
				$flag = false;
				$pre_level = $row['level'];
			}
			// 现在可以显示缩进了
			//echo '<div style="margin-left:'.($row['level']*12).'px">'.$row['name'].'(ID:'.$row['id'].')</div>';
			//if($root_level+1 == $row['level'])
			//{
			//	$node_tree['tree'] .= str_replace('{template_text}', $row['name'], $template);
			//} else if($node['lft']+1 == $node['lft'])
			//上一个节点为叶子节点且本节点也为叶子节点
			if($pre_level == $row['level'])
			{
				//级别相同
				if($row['lft']+1 == $row['rgt'])
				{
					//叶子节点
//					$node_tree['tree'] .= "{text : '".$row['name']."',leaf : true},";
					$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."'},";
				} else {
					//不是叶子节点
//					$node_tree['tree'] .= "{text : '".$row['name']."',expanded : false,children : [";
					$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
				}
				//$node_tree['tree'] .= ",{text : '".$row['name']."',expanded : false";
			} else {
				//级别不相同
				//echo "$pre_level:".$row['level']."\r\n";
				if($pre_level < $row['level'])
				{
					//下一级目录
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
//						$node_tree['tree'] .= "{text : '".$row['name']."',leaf : true},";
						$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."'},";
					} else {
						//不是叶子节点
//						$node_tree['tree'] .= "{text : '".$row['name']."',expanded : false,children : [";
						$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					}
				} else {
					//上几级目录
//					for($i=0; $i<$pre_level-$row['level']; $i++)
//					{
//						$node_tree['tree'] .= "}";
//					}
//					$node_tree['tree'] .= "},";
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
//						$node_tree['tree'] .= "{text : '".$row['name']."',leaf : true},";
						$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."'},";
					} else {
						//不是叶子节点
//						$node_tree['tree'] .= "{text : '".$row['name']."',expanded : false,children : [";
						$node_tree['tree'].="{ id:'".$row['id']."', pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					}
				}
			}

			$pre_level = $row['level'];
		}
		$node_tree['tree'] = substr($node_tree['tree'],0,strlen($node_tree['tree'])-1);
		return $node_tree;
	}

	/**
	 * 显示组织和用户(未考虑排序)
	 *
	 * $nodeId: 节点的ID
	 *
	 * 返回值：字符串
	 */
	function show_node_tree_user000000($nodeId)
	{
		$node = $this->get_node($nodeId);
		//显示本节点
		$node_tree['name'] = $node['name'];
		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);
		// 显示树的每个节点
		$node_tree['tree'] = "{ id:'".$node['id']."',userid:0, pId:'".$node['parent']."', name:'".$node['name']."', open:true},";
		$flag = true;
		foreach($tree as $key=>$row)
		{
			if($flag)
			{
				$flag = false;
				$pre_level = $row['level'];
			}
			//上一个节点为叶子节点且本节点也为叶子节点
			if($pre_level == $row['level'])
			{
				//级别相同
				if($row['lft']+1 == $row['rgt'])
				{
					//叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";
//					获取叶子节点所有用户
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."'},";
					}
				} else {
					//不是叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
				}
			} else {
				//级别不相同
				if($pre_level < $row['level'])
				{
					//下一级目录
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."'},";
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					}
				}
				else
				{
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."'},";
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					}
				}
			}

			$pre_level = $row['level'];
		}
		$node_tree['tree'] = substr($node_tree['tree'],0,strlen($node_tree['tree'])-1);
		return $node_tree;
	}

	/**
	 * 显示组织和用户(未考虑排序)
	 *
	 * $nodeId: 节点的ID
	 *
	 * 返回值：字符串
	 */
	function show_node_tree_user($nodeId)
	{
		global $CFG;
		$node = $this->get_node($nodeId);
		//显示本节点
		$node_tree['name'] = $node['name'];
		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);
		// 显示树的每个节点
//		{ id:111, pId:11, name:"叶子节点111",},

		$node_tree['tree'] = "{ id:'".$node['id']."',userid:0, pId:'".$node['parent']."', name:'".$node['name']."', open:true},";
		$user_tree = $this->select_node_detailed($node['id']);
		foreach($user_tree as $value)
		{
			$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$node['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
		}
		$flag = true;
		foreach($tree as $key=>$row)
		{
			if($flag)
			{
				$flag = false;
				$pre_level = $row['level'];
			}
			//上一个节点为叶子节点且本节点也为叶子节点
			if($pre_level == $row['level'])
			{
				//级别相同
				if($row['lft']+1 == $row['rgt'])
				{
					//叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";
//					获取叶子节点所有用户
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
					}
				} else {
					//不是叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
					}
				}
			} else {
				//级别不相同
				if($pre_level < $row['level'])
				{
					//下一级目录
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				}
				else
				{
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							$node_tree['tree'].="{id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				}
			}

			$pre_level = $row['level'];
		}
		$node_tree['tree'] = substr($node_tree['tree'],0,strlen($node_tree['tree'])-1);
		return $node_tree;
	}

	function get_nodeid_with_userid($userid)
	{
		$sql = "SELECT id, org_id, user_id FROM ".$this->link_user_table." WHERE user_id = ".$userid."";
		$result = $this->conn->query($sql);
		$re = Array();
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			$re[] = $res;
		}
		$res = '';
		foreach($re as $value)
		{
			$res = $value["org_id"];
		}
		return $res;
	}

	//筛除单位账号 朱子武
	function show_node_tree_user_no_office($nodeId)
	{
		global $DB;
		global $USER;
		global $CFG;

		$node = $this->get_node($nodeId);
		//显示本节点
		$node_tree['name'] = $node['name'];
		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);
		// 显示树的每个节点
		$node_tree['tree'] = "{ id:'".$node['id']."',userid:0, pId:'".$node['parent']."', name:'".$node['name']."', open:true},";
		$user_tree = $this->select_node_detailed($node['id']);
		foreach($user_tree as $value)
		{
			//获取当前用户，判断是否是单位角色
//			if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
			if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
				continue;
			}
			else
			{
				$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$node['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
			}
		}
		$flag = true;
		foreach($tree as $key=>$row)
		{
			if($flag)
			{
				$flag = false;
				$pre_level = $row['level'];
			}
			//上一个节点为叶子节点且本节点也为叶子节点
			if($pre_level == $row['level'])
			{
				//级别相同
				if($row['lft']+1 == $row['rgt'])
				{
					//叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";
//					获取叶子节点所有用户
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						//获取当前用户，判断是否是单位角色
//						if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
						if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
							continue;
						}
						else
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				} else {
					//不是叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						//获取用户，判断是否是单位角色
//						if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
						if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
							continue;
						}
						else
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				}
			} else {
				//级别不相同
				if($pre_level < $row['level'])
				{
					//下一级目录
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							else
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							else
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					}
				}
				else
				{
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							else
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							else
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					}
				}
			}

			$pre_level = $row['level'];
		}
		$node_tree['tree'] = substr($node_tree['tree'],0,strlen($node_tree['tree'])-1);
		return $node_tree;
	}

	/**
	 * 根据ID获取当前节点以及往下节点的用户信息
	 *
	 * $node: 节点
	 *
	 * 返回值：用户信息(不存在则返回空)
	 */
	function select_node_detailed_all($nodeId)
	{
		global $DB;
		$node = $this->get_node($nodeId);

		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);

		$re = Array();

		$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$node['id']."";
		//$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
		$result = $this->conn->query($sql);
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$res['user_id'])){
			// 判断是否是超级管理员或者单位用户 start 20160306 朱子武
//			if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14")){
				continue;
			}
			else
			{
				$re[] = $res;
			}
		}

		foreach($tree as $key=>$row)
		{
			$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$row['id']."";
			//$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
			$result = $this->conn->query($sql);
			while($res = $result->fetch_array(MYSQLI_ASSOC))
			{
				if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$res['user_id'])){
					continue;
				}
				else
				{
					$re[] = $res;
				}
			}
		}
		return $re;
	}

	/**
	 * 根据ID获取当前节点以及往下节点的用户信息(不包括自己)
	 *
	 * $node: 节点
	 *
	 * 返回值：用户信息(不存在则返回空)
	 */
	function select_node_detailed_all_not_self($nodeId)
	{
		global $DB;
		global $USER;
		$node = $this->get_node($nodeId);

		// 获取 $root_id 节点的所有子孙节点
		$tree = $this->get_all_child($node);

		$re = Array();

		$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$node['id']."";
		//$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
		$result = $this->conn->query($sql);
		while($res = $result->fetch_array(MYSQLI_ASSOC))
		{
			if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$res['user_id'])){
				continue;
			}
			elseif($USER->id == $res['user_id'])
			{
				continue;
			}
			else
			{
				$re[] = $res;
			}
		}

		foreach($tree as $key=>$row)
		{
			$sql = "SELECT a.id, a.org_id, a.user_id, b.firstname, b.lastname FROM ".$this->link_user_table." a JOIN mdl_user b ON a.user_id = b.id WHERE org_id = ".$row['id']."";
			//$sql = "select * from `".$this->table."` where `lft`>'".$node['lft']."' and `rgt`<'".$node['rgt']."' order by `lft`";
			$result = $this->conn->query($sql);
			while($res = $result->fetch_array(MYSQLI_ASSOC))
			{
				if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$res['user_id'])){
					continue;
				}
				elseif($USER->id == $res['user_id'])
				{
					continue;
				}
				else
				{
					$re[] = $res;
				}
			}
		}
		return $re;
	}
	
	/**
	 * 显示节点树(集体学习)
	 *
	 * $nodeId: 节点的ID
	 *
	 * 返回值：字符串
	 */
	function show_node_tree_collective_learning($nodeId, $courseid)
	{
		global $DB;
		global $USER;
		global $CFG;

		$userArray = $DB->get_records_sql("select b.userid from mdl_enrol a join mdl_user_enrolments b where a.courseid =".$courseid." and a.id = b.enrolid and b.userid!=".$USER->id);

		$userValue_id = array();
		foreach($userArray as $userValue)
		{
			$userValue_id[] = $userValue->userid;
		}

		$node = $this->get_node($nodeId);
		//显示本节点
		$node_tree['name'] = $node['name'];
		// 获取 $node 节点的所有子孙节点
		$tree = $this->get_all_child($node);
		// 显示树的每个节点
		$node_tree['tree'] = "{ id:'".$node['id']."',userid:0, pId:'".$node['parent']."', name:'".$node['name']."', open:true},";
		$user_tree = $this->select_node_detailed($node['id']);
		foreach($user_tree as $value)
		{
			//获取当前用户，判断是否是单位角色
//			if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
			if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
				continue;
			}
			// 筛选出是否在集体学习的人员
			elseif(in_array($value['user_id'],$userValue_id,true))
			{
				$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$node['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
			}
		}
		$flag = true;
		foreach($tree as $key=>$row)
		{
			if($flag)
			{
				$flag = false;
				$pre_level = $row['level'];
			}
			//上一个节点为叶子节点且本节点也为叶子节点
			if($pre_level == $row['level'])
			{
				//级别相同
				if($row['lft']+1 == $row['rgt'])
				{
					//叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";
//					获取叶子节点所有用户
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						//获取当前用户，判断是否是单位角色
//						if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
						if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
							continue;
						}
						// 筛选出是否在集体学习的人员
						elseif(in_array($value['user_id'],$userValue_id,true))
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				} else {
					//不是叶子节点
					$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
					$user_tree = $this->select_node_detailed($row['id']);
					foreach($user_tree as $value)
					{
						//获取用户，判断是否是单位角色
//						if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
						if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
							continue;
						}
						// 筛选出是否在集体学习的人员
						elseif(in_array($value['user_id'],$userValue_id,true))
						{
							$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
						}
					}
				}
			} else {
				//级别不相同
				if($pre_level < $row['level'])
				{
					//下一级目录
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							// 筛选出是否在集体学习的人员
							elseif(in_array($value['user_id'],$userValue_id,true))
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							// 筛选出是否在集体学习的人员
							elseif(in_array($value['user_id'],$userValue_id,true))
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					}
				}
				else
				{
					if($row['lft']+1 == $row['rgt'])
					{
						//叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."'},";

						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							// 筛选出是否在集体学习的人员
							elseif(in_array($value['user_id'],$userValue_id,true))
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					} else {
						//不是叶子节点
						$node_tree['tree'].="{ id:'".$row['id']."',userid:0, pId:'".$row['parent']."', name:'".$row['name']."', open:true},";
						$user_tree = $this->select_node_detailed($row['id']);
						foreach($user_tree as $value)
						{
							//获取当前用户，判断是否是单位角色
//							if(!$DB->get_records('role_assignments', array('roleid' => 14,'userid' => $value['user_id']))){
							if($DB->get_records_sql("select id from mdl_role_assignments where roleid = 14 and userid = ".$value['user_id'])){
								continue;
							}
							// 筛选出是否在集体学习的人员
							elseif(in_array($value['user_id'],$userValue_id,true))
							{
								$node_tree['tree'].="{ id:0, userid:'".$value['user_id']."', pId:'".$row['id']."', name:'".$value['lastname'].$value['firstname']."',icon:'".$CFG->wwwroot."/org/zTreeStyle/img/diy/person.png'},";
							}
						}
					}
				}
			}

			$pre_level = $row['level'];
		}
		$node_tree['tree'] = substr($node_tree['tree'],0,strlen($node_tree['tree'])-1);
		return $node_tree;
	}
};
?>

