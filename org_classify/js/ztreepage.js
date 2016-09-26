var org_user_id = 0;

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
		beforeClick: beforeClick
	}
};

var log;

var newCount = 1;

function beforeClick(treeId, treeNode, clickFlag) {   //click为true时不执行，默认为执行
	$('.lockpage').show();
	var orgid = treeNode.id;
	org_user_id = orgid;
	$.ajax({
		url: "../org_classify/orgCRUD.php",
		dataType:"json",
		data: { treeNodeid: treeNode.id, type: 'click'},
		success: function(msg) {
			$("#log").children().remove();
			$.each(msg, function(commentIndex, comment){
				var user_name = comment['lastname'] + comment['firstname'];
				var username = comment['username'];
				var userid = comment['user_id'];
				var orgname = comment['name'];
				showLog(userid, user_name, username, orgname);

			});
			$('.lockpage').hide();
		}
	});
	DisplayAndHiddenBtn("addBtn", "display");
	DisplayAndHiddenBtn("deleteBtn", "display");
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	return (treeNode.click == true);
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
			url: "../org_classify/orgCRUD.php",
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
		url: "../org_classify/orgCRUD.php",
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
		url: "../org_classify/orgCRUD.php",
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
		url: "../org_classify/orgCRUD.php",
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
		url: "../org_classify/orgCRUD.php",
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
		url: "../org_classify/orgCRUD.php",
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
			url: "../org_classify/orgCRUD.php",
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
	
	DisplayAndHiddenBtn("addBtn_confirm", "none");
	DisplayAndHiddenBtn("addBtn", "none");
	DisplayAndHiddenBtn("deleteBtn", "none");

});