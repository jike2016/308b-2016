<!--Start 时间日期控件  -->
<link rel="stylesheet" href="../ledgercenter/css/jquery-ui.css" />
<style type="text/css">
	a{color:#007bc4/*#424242*/; text-decoration:none;}
	a:hover{text-decoration:underline}
	ol,ul{list-style:none}
	body{font:12px/18px Tahoma, Helvetica, Arial, Verdana, "\5b8b\4f53", sans-serif; color:#51555C;}
	img{border:none}
	input{width:200px; height:20px; line-height:20px; padding:2px; border:1px solid #d3d3d3}
	pre{padding:6px 0 0 0; color:#666; line-height:20px; background:#f7f7f7}

	.ui-timepicker-div .ui-widget-header { margin-bottom: 8px;}
	.ui-timepicker-div dl { text-align: left; }
	.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
	.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
	.ui-timepicker-div td { font-size: 90%; }
	.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
	.ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label,.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px}
</style>
<script type="text/javascript" >

	jqmin(document).ready(function(){
		jqmin('#start_time').datetimepicker({
			showSecond: true,
//			showMillisec: true,//显示毫秒
			timeFormat: 'hh:mm:ss'
		});
		jqmin('#end_time').datetimepicker({
			showSecond: true,
//			showMillisec: true,//显示毫秒
			timeFormat: 'hh:mm:ss'
		});
	});
</script>
<!--End 时间日期控件  -->

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

		alert($('#start_time').val());
		alert($('#end_time').val());

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
			<div>开始时间：</div>
			<div><input type="text" id="start_time" /></div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 2end-->

	<!--文档_子类型下拉菜单 3-->
	<div class="dropdownlist">
		<div class="input-group">
			<div>结束时间：</div>
			<div><input type="text" id="end_time" /></div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 3end-->

	<button class="btn btn-primary search">搜索</button>
</div>
		
		<!--<div class="dropdownlist-son-box"></div>-->
		
		<div class="table-box">
			
		</div>