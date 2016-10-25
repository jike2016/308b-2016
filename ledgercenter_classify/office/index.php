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
	if(mb_strlen($mission->mission_name,"UTF-8") > 10){
		$output2 .= '<li id="documentlist-fourth-son-1" value="'.$mission->id.'"><a class="a_overflow" title="'.$mission->mission_name.'">'.mb_substr($mission->mission_name,0,10,"UTF-8").'...</a></li>';
	}else{
		$output2 .= '<li id="documentlist-fourth-son-1" value="'.$mission->id.'"><a >'.$mission->mission_name.'</a></li>';
	}
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
	var documentlist_third_son_id = 1;
	var documentlist_fourth_son_id=<?php echo $firstmissionid;?>;
	//alert("<?php echo $firstmissionname;?>");
	var orgid=<?php echo $_GET['orgid'];?>;
	var documentlist_course_id = 1;//课程id

	$('#documentlist-first-son li').on('click', function(){   //下拉表单 1 动作 （单位/个人）
		documentlist_first_son_id = $(this).val();       //获取选项 id
	});
	$('#documentlist-second-son li').on('click', function(){   //下拉表单 2 动作 （学习统计/台账任务统计/微阅统计……）
		documentlist_second_son_id = $(this).val();       //获取选项 id
		if(documentlist_second_son_id==1){//学习统计
//			document.getElementById("mytime").style.display="";
			document.getElementById("mymission").style.display="none";
			document.getElementById("start_time_content").style.display="";
			document.getElementById("end_time_content").style.display="";
			$('#courseTest').css('display','block');//显示课程下拉框
		}
		else if(documentlist_second_son_id==2){//台账任务统计
//			document.getElementById("mytime").style.display="none";
			document.getElementById("mymission").style.display="";
			document.getElementById("start_time_content").style.display="none";
			document.getElementById("end_time_content").style.display="none";
			$('#courseTest').css('display','none');//隐藏课程下拉框
		}
		else if(documentlist_second_son_id==3){//微阅统计
//			document.getElementById("mytime").style.display="";
			document.getElementById("mymission").style.display="none";
			document.getElementById("start_time_content").style.display="";
			document.getElementById("end_time_content").style.display="";
			$('#courseTest').css('display','none');//隐藏课程下拉框
		}
	});
	$('#documentlist-third-son li').on('click', function(){   //文档_子类型下拉表单 3动作
		documentlist_third_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});
	$('#documentlist-fourth-son li').on('click', function(){   //台账任务 下拉表单
		documentlist_fourth_son_id = $(this).val();       //获取选择项的id
	});
	$('#documentlist-course li').on('click', function(){ //课程选项 下拉表单
		documentlist_course_id = $(this).val();       //获取 课程id
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
		/*if(documentlist_second_son_id != 2){
			var start_time = $('#start_time').val();//开始时间
			var end_time = $('#end_time').val();//结束时间
			if(start_time=='' || end_time==''){
				alert('开始、结束时间不能为空！');
				$('.lockpage').hide();
				return;
			}
			start_time = get_unix_time(start_time);
			end_time = get_unix_time(end_time);
			if(start_time >= end_time){
				alert('结束时间不能小于开始时间！');
				$('.lockpage').hide();
				return;
			}
		}*/
		if(documentlist_second_son_id != 2){
			var start_time = $('#start_time').val();//开始时间
			var end_time = $('#end_time').val();//结束时间

			if(start_time != ''){
				start_time = get_unix_time(start_time);
			}else{
				start_time = 0;
			}
			if(end_time != ''){
				end_time = get_unix_time(end_time);
			}else{
				end_time = 0;
			}
//			console.log(start_time+"--"+end_time);
			if((start_time!=0 && end_time!=0) && (start_time >= end_time)){
				alert('结束时间不能小于开始时间！');
				$('.lockpage').hide();
				return;
			}
		}
		//End 添加时间段 xdw

		if(orgid==null){
			alert('请选择组织架构');
			$('.lockpage').hide();
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==1){//单位单位》学习统计
//			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);
//			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&courseid='+documentlist_course_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==2){//单位单位》台账任务统计
			$(".table-box").load('office/officemission.php?missionid='+documentlist_fourth_son_id+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==3){//单位单位》微阅统计
			$(".table-box").load('office/officemicroread.php?start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==1){//单位个人》学习统计
//			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);
//			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&courseid='+documentlist_course_id+'&start_time='+start_time+'&end_time='+end_time+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==2){//单位个人》台账任务统计
			$(".table-box").load('office/personmission.php?missionid='+documentlist_fourth_son_id+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==3){//单位个人》微阅统计
			$(".table-box").load('office/personmicroread.php?orgid='+orgid+'&start_time='+start_time+'&end_time='+end_time);
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
	input{width:140px; height:30px; line-height:20px; padding:2px; border:1px solid #d3d3d3}
	pre{padding:6px 0 0 0; color:#666; line-height:20px; background:#f7f7f7}

	.ui-timepicker-div .ui-widget-header { margin-bottom: 8px;}
	.ui-timepicker-div dl { text-align: left; }
	.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
	.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
	.ui-timepicker-div td { font-size: 90%; }
	.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
	.ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label,.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px}
	.dropdownlist-box .dropdownlist1 {  float: left;  width: 210px;  height: 36px; }
	.timetitle{float: left; font-size: 14px; margin-top: 5px}
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



<div class="dropdownlist-cover">
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
					<li id="documentlist-second-son-3" value="3"><a>微阅统计</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档_子类型下拉菜单 2end-->
	<!--start 课程下拉菜单-->
	<div class="dropdownlist" id="courseTest">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="全部课程" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-course" class="dropdown-menu dropdown-menu-right">
					<li value="1"><a>全部课程</a></li>
					<?php
					require_once("../lib/my_lib.php");
					$orgid = $_GET['orgid'];
					$courses = get_org_course($orgid);
					$index = 1;
					foreach($courses as $course){
						if(mb_strlen($course->fullname,'UTF-8') > 10){
							echo '<li id="documentlist-third-son-'.$index.'" value="'.$course->course_id.'"><a class="a_overflow" title="'.$course->fullname.'" >'.mb_substr($course->fullname,0,10,"UTF-8").'...</a></li>';
						}else{
							echo '<li id="documentlist-third-son-'.$index.'" value="'.$course->course_id.'"><a >'.$course->fullname.'</a></li>';
						}
						$index++;
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<!--end 课程下拉菜单-->

<!--文档_子类型下拉菜单 3-->
<!--	<div class="dropdownlist" id="mytime">-->
<!--		<div class="input-group">-->
<!--			<input type="text" class="form-control classkinds" value="周" readOnly="true" style="background-color:#ffffff;">-->
<!--			<div class="input-group-btn">-->
<!--				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>-->
<!--				<ul id="documentlist-third-son" class="dropdown-menu dropdown-menu-right">-->
<!--					<li id="documentlist-third-son-1" value="1"><a>周</a></li>-->
<!--					<li id="documentlist-third-son-2" value="2"><a>月</a></li>-->
<!--					<li id="documentlist-third-son-3" value="3"><a>总</a></li>-->
<!--				</ul>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
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
<div class="dropdownlist1" id="start_time_content">
	<div class="input-group">
		<div class="timetitle">开始时间：</div>
		<div style="float: right"><input type="text" id="start_time" /></div>
	</div>
</div>
<div class="dropdownlist1" id="end_time_content">
	<div class="input-group">
		<div  class="timetitle">结束时间：</div>
		<div style="float: right"><input type="text" id="end_time"  /></div>
	</div>
</div>
<!--End 添加时间日期控件 xwd-->

	<button class="btn btn-primary search">搜索</button>
</div>
</div>

<!--<div class="dropdownlist-son-box"></div>-->

<div class="table-box">

</div>