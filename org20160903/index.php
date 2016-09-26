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

		$(document).ready(function(){
			$("#submitadd").click(function () {
				var tableObj = document.getElementById("police-body");
				var length = tableObj.rows.length;
				var arrayObj = [];
				for (var i = 0; i < length; i++) {  //遍历Table的所有Row
					var checkbox = document.getElementsByClassName("users")[i];
//					console.log(checkbox);
					if(checkbox.checked==true){
						var checkboxp = checkbox.value;//此为该复选框所在的行的id
						arrayObj.push(checkboxp);
					}
				}
//				console.log(arrayObj);
				if(arrayObj.length > 0)
				{
					$.ajax({
						url: "../org/dutypolice.php",
						type: "POST",
						data: { arrayObj:arrayObj, action:'submitadd', id: $('#userid').val()},
						success: function(msg){
							msg = JSON.parse(msg);
							console.log(msg);
							if (msg.status == 200)
							{
								alert('添加成功');
							}
							else {
								alert('添加失败');
							}
							$('.lockpage').hide();
						}
					});
				}
			});
			$("#submitdelete").click(function () {
				var tableObj = document.getElementById("police-body");
				var length = tableObj.rows.length;
				var arrayObj = [];
				for (var i = 0; i < length; i++) {  //遍历Table的所有Row
					var checkbox = document.getElementsByClassName("users")[i];
//					console.log(checkbox);
					if(checkbox.checked==true){
						var checkboxp = checkbox.value;//此为该复选框所在的行的id
						arrayObj.push(checkboxp);
					}
				}
				if(arrayObj.length > 0)
				{
					$.ajax({
						url: "../org/dutypolice.php",
						type: "POST",
						data: { arrayObj:arrayObj, action:'submitdelete', id: $('#userid').val()},
						success: function(msg){
							msg = JSON.parse(msg);
							console.log(msg);
							if (msg.status == 200)
							{
								alert('删除成功');
							}
							else {
								alert('删除失败');
							}
							$('.lockpage').hide();
						}
					});
				}
			});
			$(".table-box-alert .close-btn").click(function(){
				$(".table-box-alert").hide();
				$(".cover-bg").hide();
			});
			$(".cover-bg").click(function(){
				$(".table-box-alert").hide();
				$(".cover-bg").hide();
			});

			//点击查看
			$("#show-police").click(function () {
				$('.lockpage').show();
				$('#submitadd').hide();
				$('#submitdelete').show();
				$("#check-police").removeClass('active');
				$(this).addClass('active');
				$('#police-body').empty();
				var userid = $('#userid').val();
				$.ajax({
					url: "../org/dutypolice.php",
					type: "POST",
					dataType:"json",
					data: {action:'showchildrenpolice', id:userid},
					success: function(msg) {
						if (msg.status == 200)
						{
							var str = '';
							$.each(msg.data, function(commentIndex, comment){
								str += '<tr><td><input type="checkbox" name="user" class="users" value="'+comment.id+'"></td><td>'+comment.lastname+comment.firstname+'</td><td>'+comment.data+'</td></tr>';
							});
							$("#police tbody").append(str);
						}
						$('.lockpage').hide();
					}
				});
			})

			//点击选择
			$("#check-police").click(function () {
				$('.lockpage').show();
				$('#submitdelete').hide();
				$('#submitadd').show();
				$("#show-police").removeClass('active');
				$(this).addClass('active');
				$('#police-body').empty();
				var orgid = $('#orgid').val();
				$.ajax({
					url: "../org/dutypolice.php",
					type: "POST",
					dataType:"json",
					data: {action:'getchildrenpolice', id:orgid},
					success: function(msg) {
						if (msg.status == 200)
						{
							var str = '';
							$.each(msg.data, function(commentIndex, comment){
								str += '<tr><td><input type="checkbox" name="user" class="users" value="'+comment.id+'"></td><td>'+comment.lastname+comment.firstname+'</td><td>'+comment.data+'</td></tr>';
							});
							$("#police tbody").append(str);
						}
						$('.lockpage').hide();
					}
				});
			})

		})

	</script>

	<style>
		.cover-bg {position: fixed; z-index: 10; background-color: rgba(0,0,0,0.3); width: 100%; height: 100%; display: none;}
		.table-box-alert {font-family: "微软雅黑";border-top:2px solid #8C8C8C;position: fixed;  z-index: 10;top:50%;left:50%; display: none;margin-top:-300px;margin-left:-500px;width:1000px;height:600px;background-color:#FFFFFF;}
		.table-box-title {margin: 0px;font-weight: bold; font-size: 14px;color: #C4C4C4; line-height: 40px; height: 40px; padding: 0px 15px;border-bottom: 1px solid #F0F0F0;}
		.table-box-title h3 {margin: 0px; font-size: 20px;line-height: 40px; }
		.table-box-title h3 .t {color: #0078E7;cursor: pointer ;margin-right: 15px}
		.table-box-title h3 .t.active {color: #ff3333}
		.table-box-main {height: 508px; width: 100%; overflow-y: auto; padding: 15px;}
		.table-box-main .table {text-align: center;}
		.table-box-main .table thead {font-weight: bold;}
		.close-btn {float: right; font-size: 14px;cursor: pointer;}
		.close-btn:hover {color: #777777;}
		.submit-box {height: 50px;width: 100%; text-align: center;line-height: 50px;border-top: 1px solid #F0F0F0;}
	</style>
	
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
<!--<form action="../org/dutypolice.php" method="POST">-->
<div class="cover-bg"></div>
	<input type="hidden" value="" id="userid">
	<input type="hidden" value="" id="orgid">
<div class="table-box-alert">
	<div class="table-box-title">
		<h3 class="conversation-box-title"><span id="check-police" class="t active">选择人员</span><span id="show-police" class="t">查看人员</span><span class="close-btn">X</span></h3>
	</div>
	<div class="table-box-main">
		<table class="table table-bordered table-hover" id="police">
			<thead>
			<tr>
				<td><input type="checkbox">全选</td>
				<td>姓名</td>
				<td>职务</td>
			</tr>
			</thead>
			<tbody id="police-body">
			</tbody>
		</table>
	</div>
	<div class="submit-box">
		<button class="btn btn-primary" id="submitadd">确认添加</button>
		<button class="btn btn-danger" style="display: none" id="submitdelete">删除人员</button>
	</div>
</div>
<!--</form>-->

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