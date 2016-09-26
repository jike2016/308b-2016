<?php
global $CFG;
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org_classify/org.class.php');
require_once($CFG->dirroot . '/user/my_role_conf.class.php');


//判断权限，只允许xxx管理员查看
global $USER;
global  $DB;
//if($USER->id != 2){
//	redirect('../');
//}

$org = new org();
$role_conf = new my_role_conf();

//如果是慕课管理员角色，不用审查他所在的单位，而是直接设定为根单位
if($DB->record_exists('role_assignments', array('roleid' => $role_conf->get_courseadmin_role(),'userid' => $USER->id))){
	$noteid = $DB->get_record_sql("select o.id from mdl_org o where o.parent = -1");
	$root_id = $noteid->id;
}
//如果是 分级管理员
else if($DB->record_exists('role_assignments', array('roleid' => $role_conf->get_gradingadmin_role(),'userid' => $USER->id))) {
	if(!$DB->record_exists("org_link_user",array('user_id'=>$USER->id))){//当账号所属的单位被删除时
		echo "<script>alert('您当前的账号未分配所属单位，请联系系统管理员');</script>";
		exit();
	}
	// 根据用户获得用户所在组织的根节点
	$root_id = $org -> get_nodeid_with_userid($USER->id);
}
else{
	redirect('../');
}

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
<?php //require_once("../theme/more/layout/includes/header.php"); ?>
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
<!--		<input type="button" class="btn btn-primary" id="addroot" value="增加上级组织" style=" margin-top: 10px;height: 28px;-->
<!--    padding: 4px 10px;margin-left: 10px"/>-->
		<ul id="treeDemo" class="ztree"></ul> <!--important 显示文件树的地方-->
	</div>
	<div class='right-top'>
		
<!--		<button id="check_all" class="btn btn-primary">查看所有人员</button>-->
			<button id="check_assigned" class="btn btn-primary">查看所有已分配人员</button>
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

<!--<!--底部导航条-->
<!--<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php //require_once("../theme/more/layout/includes/bottom_info.php"); ?>
<!--<!--底部导航条 end-->

</body>
</html>