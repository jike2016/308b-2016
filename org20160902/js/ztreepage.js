var org_user_id = 0;
var beforetreeid = 0;
var MoveTest = {
	//errorMsg: "放错了...请选择正确的类别！",
	curTarget: null,
	curTmpTarget: null,
	noSel: function() {
		try {
			window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
		} catch(e){}
	},
	innerTree: function(treeId, treeNodes, targetNode) {
		return targetNode!=null && targetNode.isParent && targetNode.tId == treeNodes[0].parentTId;
	},
	dragMove: function(e, treeId, treeNodes) {
		var p = null, pId = 'dom_' + treeNodes[0].pId;
		if (e.target.id == pId) {
			p = $(e.target);
		} else {
			p = $(e.target).parent('#' + pId);
			if (!p.get(0)) {
				p = null;
			}
		}

		$('.domBtnDiv .active').removeClass('active');
		if (p) {
			p.addClass('active');
		}
	},
	dropTree2Dom: function(e, treeId, treeNodes, targetNode, moveType) {
		var domId = "dom_" + treeNodes[0].getParentNode().id;
		if (moveType == null && (domId == e.target.id || $(e.target).parents("#" + domId).length > 0)) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.removeNode(treeNodes[0]);

			var newDom = $("span[domId=" + treeNodes[0].id + "]");
			if (newDom.length > 0) {
				newDom.removeClass("domBtn_Disabled");
				newDom.addClass("domBtn");
			} else {
				$("#" + domId).append("<span class='domBtn' domId='" + treeNodes[0].id + "'>" + treeNodes[0].name + "</span>");
			}
			MoveTest.updateType();
		} else if ( $(e.target).parents(".domBtnDiv").length > 0) {
			//alert(MoveTest.errorMsg);
		}
	},
	dom2Tree: function(e, treeId, treeNode) {
		var target = MoveTest.curTarget, tmpTarget = MoveTest.curTmpTarget;
		if (!target) return;
		var zTree = $.fn.zTree.getZTreeObj("treeDemo"), parentNode;
		if (treeNode != null && treeNode.isParent && "dom_" + treeNode.id == target.parent().attr("id")) {
			parentNode = treeNode;
		} else if (treeNode != null && !treeNode.isParent && "dom_" + treeNode.getParentNode().id == target.parent().attr("id")) {
			parentNode = treeNode.getParentNode();
		}

		if (tmpTarget) tmpTarget.remove();
		var nodes = zTree.addNodes(parentNode, {id:target.attr("domId"), name: target.text()});
		zTree.selectNode(nodes[0]);
		MoveTest.updateType();
		MoveTest.curTarget = null;
		MoveTest.curTmpTarget = null;
	},
	updateType: function() {
		var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
			nodes = zTree.getNodes();
		for (var i=0, l=nodes.length; i<l; i++) {
			var num = nodes[i].children ? nodes[i].children.length : 0;
			nodes[i].name = nodes[i].name.replace(/ \(.*\)/gi, "") + " (" + num + ")";
			zTree.updateNode(nodes[i]);
		}
	},
	bindDom: function() {
		$(".domBtnDiv").bind("mousedown", MoveTest.bindMouseDown);
	},
	bindMouseDown: function(e) {
		var target = e.target;
		if (target!=null && target.className=="domBtn") {
			var doc = $(document), target = $(target),
				docScrollTop = doc.scrollTop(),
				docScrollLeft = doc.scrollLeft();
			target.addClass("domBtn_Disabled");
			target.removeClass("domBtn");
			curDom = $("<span class='dom_tmp domBtn'>" + target.text() + "</span>");
			curDom.appendTo("body");

			curDom.css({
				"top": (e.clientY + docScrollTop + 3) + "px",
				"left": (e.clientX + docScrollLeft + 3) + "px"
			});
			MoveTest.curTarget = target;
			MoveTest.curTmpTarget = curDom;

			doc.bind("mousemove", MoveTest.bindMouseMove);
			doc.bind("mouseup", MoveTest.bindMouseUp);
			doc.bind("selectstart", MoveTest.docSelect);
		}
		if(e.preventDefault) {
			e.preventDefault();
		}
	},
	bindMouseMove: function(e) {
		MoveTest.noSel();
		var doc = $(document),
			docScrollTop = doc.scrollTop(),
			docScrollLeft = doc.scrollLeft(),
			tmpTarget = MoveTest.curTmpTarget;
		if (tmpTarget) {
			tmpTarget.css({
				"top": (e.clientY + docScrollTop + 3) + "px",
				"left": (e.clientX + docScrollLeft + 3) + "px"
			});
		}
		return false;
	},
	bindMouseUp: function(e) {
		var doc = $(document);
		doc.unbind("mousemove", MoveTest.bindMouseMove);
		doc.unbind("mouseup", MoveTest.bindMouseUp);
		doc.unbind("selectstart", MoveTest.docSelect);

		var target = MoveTest.curTarget, tmpTarget = MoveTest.curTmpTarget;
		if (tmpTarget) tmpTarget.remove();

		if ($(e.target).parents("#treeDemo").length == 0) {
			if (target) {
				target.removeClass("domBtn_Disabled");
				target.addClass("domBtn");
			}
			MoveTest.curTarget = null;
			MoveTest.curTmpTarget = null;
		}
	},
	bindSelect: function() {
		return false;
	}
};

var setting = {
	data: {
		key: {
			title:"t",
			parent: true,
			leaf: true
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
		beforeRename: beforeRename,
		beforeDrag: MoveTest.dragTree2Dom,
		onDrop: MoveTest.dropTree2Dom,
		onDragMove: MoveTest.dragMove,
		onMouseUp: zTreeOnMouseUp,
		onMouseDown: zTreeOnMouseDown
	},

	edit: {
		enable: true,
		editNameSelectAll: true,
		drag: {
			inner: MoveTest.innerTree
		}
	},

	view: {
		addHoverDom: addHoverDom,
		removeHoverDom: removeHoverDom,
		selectedMulti: false
	}
};

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

// 移动到任意位置
function moveNodeAnyWhere(moveid, newparentid)
{
	$.ajax({
		url: "../org/orgCRUD.php",
		data: { moveid: moveid, newparentid:newparentid, type: 'moveNodeAnyWhere'},
		success: function(msg){
			if(msg == 1){
				location.reload();
			}
			else {
				alert('移动失败');
				$('.lockpage').hide();
			}

		}
	});
}

function zTreeOnMouseUp(event, treeId, treeNode) {
	newtreeNodeid = treeNode ? treeNode.id : 0;
	if(newtreeNodeid && !(beforetreeid == newtreeNodeid) && beforetreeid)
	{
		$('.lockpage').show();
		moveNodeAnyWhere(beforetreeid, newtreeNodeid);
	}
};

function zTreeOnMouseDown(event, treeId, treeNode) {
	beforetreeid = treeNode.id;
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
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['user_id'];
				var orgname = comment['name'];
				var orgid = comment['org_id'];
				var jingzhang = comment['jingzhang'];
				showLogwithjingzhang(userid, user_name, username, orgname, jingzhang, orgid);

			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn", "display");
	DisplayAndHiddenBtn("deleteBtn", "display");
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	return (treeNode.click == true);
}

function showLogwithjingzhang(userid,user_name, username, orgname, jingzhang, orgid) {  //文件树节点点击事件与时间，显示在id为log的控件中 20160831
	if (!log) log = $("#log");
	var userurl = window.location.protocol+'//'+window.location.host+'/moodle/user/editadvanced.php?id='+userid+'&course=1';
	/** Star 修改table的显示内容 朱子武 20160314 */
	var str = "";
	if (1 == jingzhang)
	{
		str = "<tr id='"+userid+"'><td class='td1'><input name ='checkbox' type='checkbox'></td><td>"+username+"</td><td class='td2'><a target='_blank' href='"+userurl+"'>"+user_name+"</a><a  class=‘btn-box' style='float: right; cursor: pointer' onclick='addchildrenpolice("+userid+", "+orgid+")'>选择直属干警</a></td><td>"+orgname+"</td></tr>";
	}
	else
	{
		str = "<tr id='"+userid+"'><td class='td1'><input name ='checkbox' type='checkbox'></td><td>"+username+"</td><td class='td2'><a target='_blank' href='"+userurl+"'>"+user_name+"</a></td><td>"+orgname+"</td></tr>";
	}
	log.append(str);
}

function addchildrenpolice(user_id, orgid) {
	// alert(user_id);
	$("#police tbody").empty();
	$('.lockpage').show();
	$(".cover-bg").show();
	$(".table-box-alert").show();

	$.ajax({
		url: "../org/dutypolice.php",
		type: "POST",
		dataType:"json",
		data: {action:'getchildrenpolice', id:orgid},
		success: function(msg) {
			// console.log(msg.data);
			// $("#police tbody").empty();
			if (msg.status == 200)
			{
				$('#userid').val(user_id);
				var str = '';
				$.each(msg.data, function(commentIndex, comment){
					str += '<tr><td><input type="checkbox" name="user" class="users" value="'+comment.id+'"></td><td>'+comment.lastname+comment.firstname+'</td><td>'+comment.data+'</td></tr>';
				});
				$("#police tbody").append(str);
			}
			$('.lockpage').hide();
		}
	});
}

function showLog(userid,user_name, username, orgname) {  //文件树节点点击事件与时间，显示在id为log的控件中
	if (!log) log = $("#log");
	var userurl = window.location.protocol+'//'+window.location.host+'/moodle/user/editadvanced.php?id='+userid+'&course=1';
	/** Star 修改table的显示内容 朱子武 20160314 */
	log.append("<tr id='"+userid+"'><td class='td1'><input name ='checkbox' type='checkbox'></td><td>"+username+"</td><td class='td2'><a target='_blank' href='"+userurl+"'>"+user_name+"</a></td><td>"+orgname+"</td></tr>");

}

/** Start 不显示多选框 朱子武 20160314 */
function showLogNotinput(userid,user_name, username, orgname) {  //文件树节点点击事件与时间，显示在id为log的控件中
	if (!log) log = $("#log");
	var userurl = window.location.protocol+'//'+window.location.host+'/moodle/user/editadvanced.php?id='+userid+'&course=1';
	/** Star 修改table的显示内容 朱子武 20160314 */
	log.append("<tr id='"+userid+"'><td class='td1'></td><td>"+username+"</td><td class='td2'><a target='_blank' href='"+userurl+"'>"+user_name+"</a></td><td>"+orgname+"</td></tr>");
}
/** End 不显示多选框 朱子武 20160314 */

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
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['id'];
				var orgname = '未分配';
				showLog(userid,user_name, username, orgname);
			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn_confirm", "display");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");
}

/** Star 查看未分配的学员信息 朱子武 20160314*/
function CheckNotAssignedDataFromTable(){
	$.ajax({
		url: "../org/orgCRUD.php",
		dataType:"json",
		data: {type: 'userAdd'},
		success: function(msg) {
			$("#log").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['id'];
				var orgname = '未分配';
				showLogNotinput(userid,user_name, username, orgname);
			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");
}
/** End 查看未分配的学员信息 朱子武 20160314*/

/** Star 查看已分配的学员信息 朱子武 20160314*/
function CheckAssignedDataFromTable()
{
	$.ajax({
		url: "../org/orgCRUD.php",
		dataType:"json",
		data: {type: 'CheckAssigned'},
		success: function(msg) {
			$("#log").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['id'];
				var orgname = comment['name'];
				showLog(userid,user_name, username, orgname);
			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "display");
}
/** End 查看已分配的学员信息 朱子武 20160314*/

/** Star 查看全部的学员信息 朱子武 20160314*/
function CheckAllDataFromTable()
{
	$.ajax({
		url: "../org/orgCRUD.php",
		dataType:"json",
		data: {type: 'CheckAll'},
		success: function(msg) {
//				 alert(123);
//				 alert(msg);
			$("#log").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['id'];
				var orgname = comment['name'];
				showLogNotinput(userid,user_name, username, orgname);
			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");
}
/** End 查看全部的学员信息 朱子武 20160314*/

/** Start 搜索学员信息 朱子武 20160314*/
function searchDataFromTable(searchtext)
{
	$.ajax({
		url: "../org/orgCRUD.php",
		dataType:"json",
		data: {type: 'searchuser', searchtext: searchtext},
		success: function(msg) {
			$("#log").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['id'];
				var orgname = comment['name'];
				showLog(userid,user_name, username, orgname);
			});

			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");
}
/** End 搜索学员信息 朱子武 20160314*/

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

	$("#check_all").click(function(){
		$('.lockpage').show();
		CheckAllDataFromTable();
	})
	$("#check_assigned").click(function(){
		$('.lockpage').show();
		CheckAssignedDataFromTable();
	})
	$("#check_not_assigned").click(function(){
		$('.lockpage').show();
		CheckNotAssignedDataFromTable();
	})

	$('.submit').click(function(){
		$('.lockpage').show();
		var nameval = $(this).siblings('.search_key').val();
		// alert(nameval);
		searchDataFromTable(nameval);
	})
	$("#search_key").keyup(function(){
		if(event.keyCode == 13){
			//这里写你要执行的事件;
			$('.lockpage').show();
			// alert();
			var nameval = $(this).val();
			// alert(nameval);
			searchDataFromTable(nameval);
		}
	});

	//   添加上级目录   20160515 zzwu
	$('#addroot').click(function(){
		var se=confirm("此操作将会增加一个最上级根组织，如果选择确定，将会添加，如果选择取消，将不添加，请确认操作！该操作如果执行将无法撤销");
		if (se==true)
		{
			$.ajax({
				url: "../org/orgCRUD.php",
				data: {type: 'addroot'},
				success: function(msg){
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
		else
		{
			//alert("你按下的是【取消】");
		}
	});


	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");

});