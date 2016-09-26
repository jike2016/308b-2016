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
	<script type="text/javascript">
	 var org_user_id = 0;
	var setting = {
			data: {
				key: {
					title:"t"
				},
				simpleData: {
					enable: true
				}
			},
			callback: {
				beforeClick: beforeClick,
				beforeDrag: beforeDrag,
				beforeEditName: beforeEditName,
				beforeRemove: beforeRemove,
				beforeRename: beforeRename
			},
			
			edit: {
				enable: true,
				editNameSelectAll: true
			},
			
			view: {
				addHoverDom: addHoverDom,
				removeHoverDom: removeHoverDom,
				selectedMulti: false
			}
		};
		
		

		var zNodes =[
			<?php echo $tree['tree'];?>
		];

		var log;	
		
		//文档架构的增加、重命名、删除节点功能函数
		function beforeDrag(treeId, treeNodes) {
			return false;
		}
		
		function beforeEditName(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.selectNode(treeNode);
			return confirm("进入编辑状态吗？");
		}
		
		function beforeRemove(treeId, treeNode) {
			$('.lockpage').show();
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.selectNode(treeNode);
			$.ajax({
				url: "../org/orgCRUD.php",
				data: { treeNodeid: treeNode.id, type: 'delete'},
				success: function(msg){
					if(msg == 1){
						location.reload();
					}
					else{
						alert('删除失败');
						$('.lockpage').hide();
					}
				}
			});
			
			return true;
		}
		
		function beforeRename(treeId, treeNode, newName, isCancel) {
			$('.lockpage').show();
			if (newName.length == 0) {
				alert("节点名称不能为空.");
				var zTree = $.fn.zTree.getZTreeObj("treeDemo");
				setTimeout(function(){zTree.editName(treeNode)}, 10);
				return false;
			}
			
			$.ajax({
				url: "../org/orgCRUD.php",
				data: { treeNodeid: treeNode.id, treeNodename: newName, type: 'rename'},
				success: function(msg){
					if(msg == 1){
						location.reload();
					}
					else{
						alert('重命名失败');
						$('.lockpage').hide();
						
					}
				}
			});
			
			return true;
		}
		
			
		var newCount = 1;
		
/*		function addHoverDom(treeId, treeNode) {
			var sObj = $("#" + treeNode.tId + "_span");
			
			if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length> 0) return;
			
			var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
				+ "' title='添加单位' onfocus='this.blur();'></span>";
			sObj.after(addStr);
			
			var btn = $("#addBtn_"+treeNode.tId);
			if (btn) btn.bind("click", function(){
				var zTree = $.fn.zTree.getZTreeObj("treeDemo");
				
				var newName = "newnode" + (newCount++);
				$.ajax({
				url: "../org/orgCRUD.php",
				data: { treeNodeid: treeNode.id, treeNodename: newName, pos:'down', type: 'add'},
				success: function(msg){
						if(msg == 1){
							location.reload();
						}
						else{
							alert('添加失败');
						}
					}
				});
				
				zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:newName});
				
				return false;
			});
		};
		
		function removeHoverDom(treeId, treeNode)
		{
			$("#addBtn_"+treeNode.tId).unbind().remove();
		};
		*/
	 function addHoverDom(treeId, treeNode) {
		 addBtn(treeId, treeNode);
		 uplevel(treeId, treeNode);
		 downlevel(treeId, treeNode);
	 };

	 // 上移
	 function uplevel(treeId, treeNode)
	 {
		 var sObj = $("#" + treeNode.tId + "_span");

		 if (treeNode.editNameFlag || $("#uplevel_"+treeNode.tId).length> 0) return;

		 var addStr = "<span class='button uplevel' id='uplevel_" + treeNode.tId
			 + "' title='上移' onfocus='this.blur();'></span>";
		 sObj.after(addStr);

		 var btn = $("#uplevel_"+treeNode.tId);
		 if (btn) btn.bind("click", function(){
			 $('.lockpage').show();
			 var zTree = $.fn.zTree.getZTreeObj("treeDemo");

			 $.ajax({
				 url: "../org/orgCRUD.php",
				 data: { treeNodeid: treeNode.id, type: 'uplevel'},
				 success: function(msg){
//					 alert(msg);
					 if(msg == 1){
						 location.reload();
					 }
					 else {
						 alert('移动失败');
						 $('.lockpage').hide();
					 }
					 
				 }
			 });

			 return false;
		 });
	 }

	 // 下移
	 function downlevel(treeId, treeNode)
	 {
		 var sObj = $("#" + treeNode.tId + "_span");

		 if (treeNode.editNameFlag || $("#downlevel_"+treeNode.tId).length> 0) return;

		 var addStr = "<span class='button downlevel' id='downlevel_" + treeNode.tId
			 + "' title='下移' onfocus='this.blur();'></span>";
		 sObj.after(addStr);

		 var btn = $("#downlevel_"+treeNode.tId);
		 if (btn) btn.bind("click", function(){
			 $('.lockpage').show();
			 var zTree = $.fn.zTree.getZTreeObj("treeDemo");

			 $.ajax({
				 url: "../org/orgCRUD.php",
				 data: { treeNodeid: treeNode.id, type: 'downlevel'},
				 success: function(msg){
//					 alert(msg);
					 if(msg == 1){
						 location.reload();
					 }
					 else {
						 alert('移动失败');
						 $('.lockpage').hide();
					 }
				 }
			 });

			 return false;
		 });
	 }

	 // 添加节点
	 function addBtn(treeId, treeNode)
	 {
		 var sObj = $("#" + treeNode.tId + "_span");

		 if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length> 0) return;

		 var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
			 + "' title='添加单位' onfocus='this.blur();'></span>";
		 sObj.after(addStr);

		 var btn = $("#addBtn_"+treeNode.tId);
		 if (btn) btn.bind("click", function(){
			 $('.lockpage').show();
			 var zTree = $.fn.zTree.getZTreeObj("treeDemo");

			 var newName = "newnode" + (newCount++);
			 $.ajax({
				 url: "../org/orgCRUD.php",
				 data: { treeNodeid: treeNode.id, treeNodename: newName, pos:'down', type: 'add'},
				 success: function(msg){
					 if(msg == 1){
						 location.reload();
					 }
					 else{
						 alert('添加失败');
					 }
				 }
			 });

			 zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:newName});

			 return false;
		 });
	 }

	 function removeHoverDom(treeId, treeNode)
	 {

	 	$("#addBtn_"+treeNode.tId).unbind().remove();

		$("#uplevel_"+treeNode.tId).unbind().remove();

		$("#downlevel_"+treeNode.tId).unbind().remove();

	 };
		//文档架构的增加、重命名、删除节点功能函数 end
		
		
		function beforeClick(treeId, treeNode, clickFlag) {   //click为true时不执行，默认为执行
			$('.lockpage').show();
			var orgid = treeNode.id;
			org_user_id = orgid;
			$.ajax({
				url: "../org/orgCRUD.php",
				dataType:"json",
				data: { treeNodeid: treeNode.id, type: 'click'},
				success: function(msg) {
					$("#log").children().remove();
					$.each(msg, function(commentIndex, comment){
						var username = comment['lastname'] + comment['firstname'];
						var userid = comment['user_id'];
						showLog(userid, username);
						
					});
					$('.lockpage').hide();
				}
			});
			DisplayAndHiddenBtn("addBtn", "display");
			DisplayAndHiddenBtn("deleteBtn", "display");			
			DisplayAndHiddenBtn("addBtn_confirm", "none");
			return (treeNode.click == true);
		}
		
		function showLog(userid, username) {  //文件树节点点击事件与时间，显示在id为log的控件中
			if (!log) log = $("#log");
			var userurl = window.location.protocol+'//'+window.location.host+'/moodle/user/editadvanced.php?id='+userid+'&course=1';
			log.append("<tr id='"+userid+"'><td class='td1'><input name ='checkbox' type='checkbox'></td><td class='td2'><a target='_blank' href='"+userurl+"'>"+username+"</a></td></tr>");

		}

	function GetInfoFromTable() {
		var tableObj = document.getElementById("log");
		var length = tableObj.rows.length;
		var arrayObj = new Array();
		for (var i = 0; i <length; i++) {  //遍历Table的所有Row
			var checkbox = document.getElementsByName("checkbox")[i];

			if(checkbox.checked==true){
				var checkboxp = checkbox.parentElement.parentElement.id;//此为该复选框所在的行的id
				arrayObj.push(checkboxp);
			}
		}
		
		if(arrayObj.length > 0)
		{
			$.ajax({
				url: "../org/orgCRUD.php",
				data: { arrayObj:arrayObj, type: 'userdelete'},
				success: function(msg){
					if(msg == 1){
						location.reload();
					}
					else{
						alert('删除失败');
					}
					$('.lockpage').hide();
				}
			});
		}
	}

	function AddDataFromTable(){
		$.ajax({
			url: "../org/orgCRUD.php",
			dataType:"json",
			data: {type: 'userAdd'},
			success: function(msg) {
				$("#log").children().remove();
				$.each(msg, function(commentIndex, comment){
					var username = comment['lastname'] + comment['firstname'];
					var userid = comment['id'];
					showLog(userid, username);
				});
				$('.lockpage').hide();
			}
		});
		DisplayAndHiddenBtn("addBtn_confirm", "display");
		DisplayAndHiddenBtn("addBtn", "none");
		DisplayAndHiddenBtn("deleteBtn", "none");
	}

	 function AddConfirmDataFromTable() {
		 var tableObj = document.getElementById("log");
		 var length = tableObj.rows.length;
		 var arrayObj = new Array();
		 for (var i = 0; i <length; i++) {  //遍历Table的所有Row
			 var checkbox = document.getElementsByName("checkbox")[i];

			 if(checkbox.checked==true)
			 {
				 var checkboxp = checkbox.parentElement.parentElement.id;//此为该复选框所在的行的id
				 arrayObj.push(checkboxp);
			 }
		 }

		 if(arrayObj.length > 0)
		 {
			 $.ajax({
				 url: "../org/orgCRUD.php",
				 data: {treeNodeid: org_user_id, arrayObj:arrayObj, type: 'useraddconfirm'},
				 success: function(msg){
//						alert(msg);
					 if(msg == 1){
						 location.reload();
					 }
					 else{
						 alert('添加失败');
					 }
					 $('.lockpage').hide();
				 }
			 });
		 }
	 }

	 function DisplayAndHiddenBtn(btnId, type) {
		 var currentBtn = document.getElementById(btnId);
		 if (type == "display")
		 {
			 currentBtn.disabled=false;
		 }
		 else if (type == "none")
		 {
			 currentBtn.disabled=true;
		 }
	 }

	$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			$("#treeDemo a").click(function(){
				$("#treeDemo a").removeClass("tree-a-active");
				$(this).addClass("tree-a-active");
				//$(".table-box").load('ajax-test.html');
			})
			$("#deleteBtn").click(function(){
				$('.lockpage').show();
				GetInfoFromTable();
			})
			$("#addBtn").click(function(){
				$('.lockpage').show();
				AddDataFromTable();
			})
			$("#addBtn_confirm").click(function(){
				$('.lockpage').show();
				AddConfirmDataFromTable();
			})
			DisplayAndHiddenBtn("addBtn_confirm", "none");
			DisplayAndHiddenBtn("addBtn", "none");
			DisplayAndHiddenBtn("deleteBtn", "none");
		});
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
		<ul id="treeDemo" class="ztree"></ul> <!--important 显示文件树的地方-->
	</div>
	
	<div class="right">
		<div class="table-box">
			<table class="table table-striped table-bordered">
				<thead><tr><td>选择</td><td>名称</td></tr></thead>
				<tbody id = "log">
				</tbody>
			</table>
		</div>
		
		<div class="btn-box">
			<button id="addBtn" class="btn btn-danger">添加</button>
			<button id="addBtn_confirm" class="btn btn-danger">确认添加</button>
			<button id="deleteBtn" class="btn btn-danger">删除</button>
		</div>
	</div>	
</div>
</body>
</html>