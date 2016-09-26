var setting = {
			data: {
				simpleData: {
					enable: true
				}
			}
		};

		var zNodes =[
			{ id:1, pId:0, name:"父节点1", open:true,},
			{ id:11, pId:1, name:"父节点11",open:true},
			{ id:111, pId:11, name:"叶子节点111"},
			{ id:112, pId:11, name:"叶子节点112"},
			{ id:113, pId:11, name:"叶子节点113"},
			{ id:114, pId:11, name:"叶子节点114"},
			{ id:12, pId:1, name:"父节点12 ", open:true},
			{ id:121, pId:12, name:"叶子节点121"},
			{ id:122, pId:12, name:"叶子节点122"},
			{ id:123, pId:12, name:"叶子节点123"},
			{ id:124, pId:12, name:"叶子节点124"},
			{ id:2, pId:0, name:"父节点2 ", open:true},
			{ id:21, pId:2, name:"叶子节点124"}
		];
		
		
		
		$(document).ready(function(){
			$.fn.zTree.init($("#tree"), setting, zNodes);			
			$("#tree a").click(function(){
				if($(this).children("span").hasClass("ico_docu"))
					$(".right").load('son_doc.html');
				else
					$(".right").load('parent_doc.html');
			})
		});

		