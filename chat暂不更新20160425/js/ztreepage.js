var setting = {
	data: {
		simpleData: {
				enable: true
			}
		},
		callback: {
			beforeClick: beforeClick
		}
	};

	var zNodes =[
		{ id:1, pId:0, name:"父节点1", open:true},
		{ id:11, pId:1, name:"父节点11",open:true},
		{ id:111, pId:11, name:"叶子节点111"},
		{ id:112, pId:11, name:"叶子节点112"},
		{ id:113, pId:11, name:"叶子节点113"},
		{ id:114, pId:11, name:"叶子节点114"},
		{ id:12, pId:1, name:"父节点12", open:true},
		{ id:121, pId:12, name:"叶子节点121"},
		{ id:122, pId:12, name:"叶子节点122"},
		{ id:123, pId:12, name:"叶子节点123"},
		{ id:124, pId:12, name:"叶子节点124"},
		{ id:2, pId:0, name:"父节点2 ", open:true},
		{ id:21, pId:2, name:"叶子节点124"}
	];
		
	var treenode_id;    //全局变量存储树节点id
	var treenode_name;  //全局变量存储树节点name

	function beforeClick(treeId, treeNode, clickFlag) {  
		treenode_id = treeNode.id;
		treenode_name = treeNode.name;
		//return (treeNode.click == true);
	}
		
	$(document).ready(function(){
		$.fn.zTree.init($("#treeDemo"), setting, zNodes);
		
	
		$('#show').click(function(){   //弹出窗口
			//$('.mask').fadeIn(100);
			$('.reset').slideDown(200);
		});
		$('.close').click(function(){
			//$('.mask').fadeOut(100);
			$('.reset').slideUp(200);
		});			
			
		$('#add').on('click', function(){      //添加按钮动作
			$(this).parent().parent().parent().children('.form-control').val($(this).text());
			$('#select').append('<option value='+treenode_id+'>'+treenode_name+'</option>');
		});
			
		//适配不同大小窗口
		var winW=$(window).width();
		var winH=$(window).height();
		$("#main").height(winH-300);
		
		var mainheight = $("#main").height();
		mainheight = mainheight*0.4;
		$("#select").height(mainheight);
		//适配不同大小窗口 end
	});