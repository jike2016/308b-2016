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
			{ id:1, pId:0, name:"父节点1", open:true},
			{ id:11, pId:1, name:"叶子节点1-1"},
			{ id:12, pId:1, name:"叶子节点1-2"},
			{ id:13, pId:1, name:"叶子节点1-3", open:true},
			{ id:131, pId:13, name:"叶子节点1-3-1"},
			{ id:2, pId:0, name:"父节点2", open:true},
			{ id:21, pId:2, name:"叶子节点2-1"},
			{ id:22, pId:2, name:"叶子节点2-2"},
			{ id:23, pId:2, name:"叶子节点2-3"},
			{ id:3, pId:0, name:"父节点3", open:true},
			{ id:31, pId:3, name:"叶子节点3 - 1"},
			{ id:32, pId:3, name:"叶子节点3 - 2"},
			{ id:33, pId:3, name:"叶子节点3 - 3"},
			{ id:4, pId:0, name:"父节点4", open:true},
			{ id:41, pId:4, name:"叶子节点3 - 1"},
			{ id:10, pId:0, name:"父节点10", open:true},
			{ id:101, pId:10, name:"叶子节点10 - 1"},
			{ id:11, pId:0, name:"父节点11", open:true},
			{ id:111, pId:11, name:"叶子节点11 - 1"}
		];

		var log;	
		
		//文档架构的增加、重命名、删除节点功能函数
		function beforeDrag(treeId, treeNodes) {
			return false;
		}
		
		function beforeEditName(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.selectNode(treeNode);
			return confirm("进入节点 -- " + treeNode.name + " 的编辑状态吗？");
		}
		
		function beforeRemove(treeId, treeNode) {
			var zTree = $.fn.zTree.getZTreeObj("treeDemo");
			zTree.selectNode(treeNode);
			return confirm("确认删除 节点 -- " + treeNode.name + " 吗？");
		}
		
		function beforeRename(treeId, treeNode, newName, isCancel) {
			if (newName.length == 0) {
				alert("节点名称不能为空.");
				var zTree = $.fn.zTree.getZTreeObj("treeDemo");
				setTimeout(function(){zTree.editName(treeNode)}, 10);
				return false;
			}
			return true;
		}
		
			
		var newCount = 1;
		
		function addHoverDom(treeId, treeNode) {
			var sObj = $("#" + treeNode.tId + "_span");
			
			if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
			
			var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
				+ "' title='增加节点' onfocus='this.blur();'></span>";
			sObj.after(addStr);
			
			var btn = $("#addBtn_"+treeNode.tId);
			if (btn) btn.bind("click", function(){
				var zTree = $.fn.zTree.getZTreeObj("treeDemo");
				zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:"new node" + (newCount++)});
				return false;
			});
		};
		
		function removeHoverDom(treeId, treeNode) {
			$("#addBtn_"+treeNode.tId).unbind().remove();
		};
		//文档架构的增加、重命名、删除节点功能函数 end
		
		
		function beforeClick(treeId, treeNode, clickFlag) {   //click为true时不执行，默认为执行
			showLog(); 
			return (treeNode.click == true);
		}
		
		
		function showLog(str) {  //文件树节点点击事件与时间，显示在id为log的控件中
			if (!log) log = $("#log");
			log.append("<tr><td class='td1'><input type='checkbox'></td><td class='td2'></td><td class='td3'>2016-1-2</td></tr>");
			$("tbody .td2").load('ajax-test.html');
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
			
			$("#treeDemo a").click(function(){
				$("#treeDemo a").removeClass("tree-a-active");
				$(this).addClass("tree-a-active");
			})
		});