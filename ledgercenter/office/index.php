<?php 
require_once("../../config.php");
global $DB;
$missions = $DB -> get_records_sql('select id,mission_name from mdl_mission_my order by id desc');
$output1 = '';
$output2 = '';
$firstmissionname='';
/** START20160325 赋初值，否则js嵌套代码输出会出错 岑霄*/
$firstmissionid='0';
/* End */
foreach ($missions as $mission){
	if($output2 == ''){
		$firstmissionname=$mission->mission_name;
		$firstmissionid=$mission->id;
	}
	$output2 .= '<li id="documentlist-fourth-son-1" value="'.$mission->id.'"><a>'.$mission->mission_name.'</a></li>';
}
$output1 = '<input type="text" class="form-control classkinds" value="'.$firstmissionname.'" readOnly="true" style="background-color:#ffffff;">
	<div class="input-group-btn">
	<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
	<ul id="documentlist-fourth-son" class="dropdown-menu dropdown-menu-right">';
?>
<script>
	//解锁屏幕
	$('.lockpage').hide();
	
	$('.dropdown-menu li').on('click', function(){   //下拉菜单动作
		$(this).parent().parent().parent().children('.classkinds').val($(this).text());
	});	
	
	document.getElementById("mymission").style.display="none";
	var documentlist_first_son_id=1;
	var documentlist_second_son_id=1;
	var documentlist_third_son_id=1;
	var documentlist_fourth_son_id=<?php echo $firstmissionid;?>;
	//alert("<?php echo $firstmissionname;?>");
	var orgid=<?php echo $_GET['orgid'];?>;
	
	$('#documentlist-first-son li').on('click', function(){   //文档_子类型下拉表单 1 动作
		documentlist_first_son_id = $(this).val();       //获取文档_子类型下拉菜单 1选择项的id
	});	
	$('#documentlist-second-son li').on('click', function(){   //文档_子类型下拉表单 2 动作
		documentlist_second_son_id = $(this).val();       //获取文档_子类型下拉菜单 2选择项的id
		if(documentlist_second_son_id==1){//学习统计
			document.getElementById("mytime").style.display="";
			document.getElementById("mymission").style.display="none";
			document.getElementById("start_time_content").style.display="";
			document.getElementById("end_time_content").style.display="";
		}
		else{
			document.getElementById("mytime").style.display="none";
			document.getElementById("mymission").style.display="";
			document.getElementById("start_time_content").style.display="none";
			document.getElementById("end_time_content").style.display="none";
		}
	});
	$('#documentlist-third-son li').on('click', function(){   //文档_子类型下拉表单 3动作
		documentlist_third_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});
	$('#documentlist-fourth-son li').on('click', function(){   //文档_子类型下拉表单 3动作
		documentlist_fourth_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});

	//Start 时间字符串转换为时间戳 dateStr = 2016-05-25 12:12:12 xdw
	function get_unix_time(dateStr)
	{
		var newstr = dateStr.replace(/-/g,'/');
		var date =  new Date(newstr);
		var time_str = date.getTime().toString();
		return time_str.substr(0, 10);
	}
	//end 时间字符串转换为时间戳 dateStr = 2016-05-25 12:12:12 xdw
	
	$(".search").on('click', function(){   //搜索按钮动作
		$('.lockpage').show();
		// var treenodeactiveid = $(".curSelectedNode").attr("id");  //获取激活的树节点的id	
		 // alert(documentlist_fourth_son_id+'f');

		//Start 添加时间段 xdw
		var start_time = $('#start_time').val();//开始时间
		var end_time = $('#end_time').val();//结束时间
		if(start_time=='' || end_time==''){
			alert('开始、结束时间不能为空！');
			return;
		}
		start_time = get_unix_time(start_time);
		end_time = get_unix_time(end_time);
		if(start_time >= end_time){
			alert('结束时间不能小于开始时间！');
			return;
		}
		//End 添加时间段 xdw

		if(orgid==null){
			alert('请选择组织架构');
			$('.lockpage').hide();
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==1){
//			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);
			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==2){
			$(".table-box").load('office/officemission.php?missionid='+documentlist_fourth_son_id+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==1){
//			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);
			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==2){
			$(".table-box").load('office/personmission.php?missionid='+documentlist_fourth_son_id+'&orgid='+orgid);
		}
		
		//alert("1:"+documentlist_first_son_id+"    2:"+documentlist_second_son_id+"   3:"+documentlist_third_son_id)
		// else if(documentlist_first_son_id==1){
			// $(".table-box").load('personledger/loginledger.php');	
		// }
		//alert("激活的树节点:  "+treenodeactiveid+"\r台账类型:  "+documentlistactive_id+"\r统计类型:  "+documentlist_first_son_id+"\r文档_子类型2:  "+documentlist_second_son_id+"\r文档_子类型3:  "+documentlist_third_son_id);
	});
</script>

<!--Start 时间日期控件  xdw -->
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
<!--End 时间日期控件  xwd -->




<div class="dropdownlist-box">
			<!--文档_子类型下拉菜单 1-->
	<div class="dropdownlist">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="单位台账" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-first-son" class="dropdown-menu dropdown-menu-right">
					<li id="documentlist-first-son-1" value="1"><a>单位台账</a></li>
					<li id="documentlist-first-son-2" value="2"><a>个人台账</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档子类型下拉菜单 1end-->

	<!--文档_子类型下拉菜单 2-->
	<div class="dropdownlist" >
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="学习统计" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-second-son" class="dropdown-menu dropdown-menu-right">
					<li id="documentlist-second-son-1" value="1"><a>学习统计</a></li>
					<li id="documentlist-second-son-2" value="2"><a>台账任务统计</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 2end-->
<!--文档_子类型下拉菜单 3-->
	<div class="dropdownlist" id="mytime">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="周" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-third-son" class="dropdown-menu dropdown-menu-right">
					<li id="documentlist-third-son-1" value="1"><a>周</a></li>
					<li id="documentlist-third-son-2" value="2"><a>月</a></li>
					<li id="documentlist-third-son-3" value="3"><a>总</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 3end-->

	<div class="dropdownlist" id="mymission">
		<div class="input-group">
			
				<?php 
					echo $output1;
					echo $output2;
				?>
					
				</ul>
			</div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 3end-->

<!--Start 添加时间日期控件 xwd-->
<div class="dropdownlist" id="start_time_content">
	<div class="input-group">
		<div>开始时间：</div>
		<div><input type="text" id="start_time" /></div>
	</div>
</div>
<div class="dropdownlist" id="end_time_content">
	<div class="input-group">
		<div>结束时间：</div>
		<div><input type="text" id="end_time" /></div>
	</div>
</div>
<!--End 添加时间日期控件 xwd-->
	

	<button class="btn btn-primary search">搜索</button>
		</div>
		
		<!--<div class="dropdownlist-son-box"></div>-->
		
		<div class="table-box">
			
		</div>