<script>
	//解锁屏幕
	$('.lockpage').hide();
	$('.dropdown-menu li').on('click', function(){   //下拉菜单动作
		$(this).parent().parent().parent().children('.classkinds').val($(this).text());
	});	

	var documentlist_first_son_id=1;
	var documentlist_second_son_id=1;
	// var documentlist_third_son_id;
	var personid=<?php echo $_GET['personid'];?>;
	$('#documentlist-first-son li').on('click', function(){   //文档_子类型下拉表单 1 动作
		documentlist_first_son_id = $(this).val();       //获取文档_子类型下拉菜单 1选择项的id
	});	
	$('#documentlist-second-son li').on('click', function(){   //文档_子类型下拉表单 2 动作
		documentlist_second_son_id = $(this).val();       //获取文档_子类型下拉菜单 2选择项的id
	});

	
	
	$(".search").on('click', function(){   //搜索按钮动作
		$('.lockpage').show();
		// var treenodeactiveid = $(".curSelectedNode").attr("id");  //获取激活的树节点的id	
		// var documentlistactive_id = $("#documentlist .li_active").val(); //获取文档类型下拉菜单选择项的id		
		//判断类型，输出页面documentlist_first_son_id
		if(personid==null){
			alert('请选择组织架构');
			$('.lockpage').hide();
		}
		else if(documentlist_first_son_id==1){
			$(".table-box").load('person/learnledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);	
		}
		else if(documentlist_first_son_id==2){
			$(".table-box").load('person/quizledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);	
		}
		else if(documentlist_first_son_id==3){
			$(".table-box").load('person/missionledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);	
		}
		
		// alert("1:"+documentlist_first_son_id+"    2:"+documentlist_second_son_id+" personid="+personid);
		//alert("激活的树节点:  "+treenodeactiveid+"\r台账类型:  "+documentlistactive_id+"\r统计类型:  "+documentlist_first_son_id+"\r文档_子类型2:  "+documentlist_second_son_id+"\r文档_子类型3:  "+documentlist_third_son_id);
	});
</script>
<div class="dropdownlist-box">
			<!--文档_子类型下拉菜单 1-->
	<div class="dropdownlist">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="学习统计" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-first-son" class="dropdown-menu dropdown-menu-right">
					<li id="documentlist-first-son-1" value="1"><a>学习统计</a></li>
					<li id="documentlist-first-son-2" value="2"><a>考试统计</a></li>
					<li id="documentlist-first-son-3" value="3"><a>台账任务统计</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档子类型下拉菜单 1end-->

	<!--文档_子类型下拉菜单 2-->
	<div class="dropdownlist">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="周" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-second-son" class="dropdown-menu dropdown-menu-right">
					<li id="documentlist-second-son-1" value="1"><a>周</a></li>
					<li id="documentlist-second-son-2" value="2"><a>月</a></li>
					<li id="documentlist-second-son-3" value="3"><a>总</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 2end-->

	

	<button class="btn btn-primary search">搜索</button>
		</div>
		
		<!--<div class="dropdownlist-son-box"></div>-->
		
		<div class="table-box">
			
		</div>