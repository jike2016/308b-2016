<?php

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org/org.class.php');

//判断权限，只允许超级管理员查看
global $USER;
if($USER->id != 2){
	redirect('../');
}



$org = new org();

//添加根节点
if($org->get_root_node_id() === False)
{
	$root_id = $org->add_root_node("root");
} else {
	$root_id = $org->get_root_node_id();
}

//$root_node = $org->get_node($root_id);

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
//显示整棵树

$tree = $org->show_node_tree($root_id);
?>


<!DOCTYPE html>
<html>
<head>
	<title>组织架构管理</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" href="zTreeStyle/zTreeStyle.css" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.css" />
	<link rel="stylesheet" href="css/treepage.css" type="text/css">
	
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->
	<style>
	.lockpage {z-index: 10000;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #000;opacity: 0.4;filter: alpha(opacity=40); text-align: center;vertical-align:middle; display: none;}
	.lockpage img {width: 60px;position:absolute;top:40%;}
	</style>
	
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="js/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="js/jquery.ztree.exedit.js"></script>
	<script type="text/javascript" src="js/ztreepage.js"></script>
	<script type="text/javascript">
		var zNodes =[
			<?php echo $tree['tree'];?>
		];

	</script>
</head>

<body>
<div class="lockpage">
	<img src="pix/loading.jpg"/>
</div>
<!--导航条-->
<nav class="navstyle navbar-fixed-top">
	<div class="nav-main">
		<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
		<ul class="nav-main-li">
			<a href="<?php echo $CFG->wwwroot;?>">
				<li class="li-normol">首页</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/mod/forum/view.php?id=1">
				<li class="li-normol">微阅</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/course/index.php">
				<li class="li-normol">微课</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">
				<li class="li-normol">直播</li>
			</a>
		</ul>
		<div class="usermenu-box">
							
		</div>
	</div>
</nav>
		<!--导航条 end-->


<div class="main">
	<div class="zTreeDemoBackground left">
		<input type="button" class="btn btn-primary" id="addroot" value="增加上级组织" style=" margin-top: 10px;height: 28px;
    padding: 4px 10px;margin-left: 10px"/>
		<ul id="treeDemo" class="ztree"></ul> <!--important 显示文件树的地方-->
	</div>
	<div class='right-top'>
		
		<button id="check_all" class="btn btn-primary">查看所有人员</button>
			<button id="check_assigned" class="btn btn-primary">查看已分配人员</button>
			<button id="check_not_assigned" class="btn btn-primary">查看未分配人员</button>
			<div style="float:right">
			<input type="text" id="search_key" class="search_key" value="搜索名称/账号" onclick="this.value='';focus()"/>
		<input type="submit" class="submit btn btn-primary" value="搜索">
		</div>
	</div>
	<div class="right">
		<div class="table-box">
			<table class="table table-striped table-bordered">
				<thead><tr><td>选择</td><td>用户名</td><td>名称</td><td>所在单位</td></tr></thead>
				<tbody id = "log">
				</tbody>
			</table>
		</div>
		
		<div class="btn-box">
			

			<button id="addBtn" class="btn btn-primary">添加</button>
			<button id="addBtn_confirm" class="btn btn-primary">确认添加</button>
			<button id="deleteBtn" class="btn btn-primary">删除</button>

			

		</div>
	</div>	
</div>
</body>
</html>