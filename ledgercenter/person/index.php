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
	.dropdownlist-box .dropdownlist1 {  float: left;  width: 210px;  height: 36px;  margin: 13px 10px;  }
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

<script>
	//解锁屏幕
	$('.lockpage').hide();
	$('.dropdown-menu li').on('click', function(){   //下拉菜单动作
		$(this).parent().parent().parent().children('.classkinds').val($(this).text());
	});	

	var documentlist_first_son_id=1;
	var documentlist_second_son_id=1;
//	var documentlist_third_son_id = 1;//课程id
	var personid=<?php echo $_GET['personid'];?>;

	$('#documentlist-first-son li').on('click', function(){   //下拉表单 动作
		documentlist_first_son_id = $(this).val();       //获取下拉菜单 选择项的id
		if(documentlist_first_son_id  != 1){
			$('#courseTest').css('display','none');//隐藏课程下拉框
		}else{
			$('#courseTest').css('display','block');//显示课程下拉框
		}
	});
	$('#documentlist-first-son li').on('click', function(){   //文档_子类型下拉表单 1 动作
		documentlist_first_son_id = $(this).val();       //获取文档_子类型下拉菜单 1选择项的id
	});	
	$('#documentlist-second-son li').on('click', function(){   //文档_子类型下拉表单 2 动作
		documentlist_second_son_id = $(this).val();       //获取文档_子类型下拉菜单 2选择项的id
	});
	$('#documentlist-third-son li').on('click', function(){   //文档_子类型下拉表单 3 动作
		documentlist_third_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});

	//时间字符串转换为时间戳 dateStr = 2016-05-25 12:12:12
	function get_unix_time(dateStr)
	{
		var newstr = dateStr.replace(/-/g,'/');
		var date =  new Date(newstr);
		var time_str = date.getTime().toString();
		return time_str.substr(0, 10);
	}

	$(".search").on('click', function(){   //搜索按钮动作

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

		$('.lockpage').show();
		// var treenodeactiveid = $(".curSelectedNode").attr("id");  //获取激活的树节点的id
		// var documentlistactive_id = $("#documentlist .li_active").val(); //获取文档类型下拉菜单选择项的id
		//判断类型，输出页面documentlist_first_son_id
		if(personid==null){
			alert('请选择组织架构');
			$('.lockpage').hide();
		}
		else if(documentlist_first_son_id==1){//学习任务
//			$(".table-box").load('person/learnledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);
			$(".table-box").load('person/learnledger.php?start_time='+start_time+'&end_time='+end_time+'&courseid='+documentlist_third_son_id+'&personid='+personid);
		}
		else if(documentlist_first_son_id==2){//考试统计
//			$(".table-box").load('person/quizledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);
			$(".table-box").load('person/quizledger.php?start_time='+start_time+'&end_time='+end_time+'&personid='+personid);
		}
		else if(documentlist_first_son_id==3){//台账任务
//			$(".table-box").load('person/missionledger.php?timeid='+documentlist_second_son_id+'&personid='+personid);
			$(".table-box").load('person/missionledger.php?start_time='+start_time+'&end_time='+end_time+'&personid='+personid);
		}
		else if(documentlist_first_son_id==4){//微阅统计
			$(".table-box").load('person/microreadledger.php?start_time='+start_time+'&end_time='+end_time+'&personid='+personid);
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
					<li id="documentlist-first-son-4" value="4"><a>微阅统计</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--文档子类型下拉菜单 1 end-->

<!--Start 将周月总选项去掉 xwd-->
 	<!--文档_子类型下拉菜单 2-->
<!--	<div class="dropdownlist">-->
<!--		<div class="input-group">-->
<!--			<input type="text" class="form-control classkinds" value="周" readOnly="true" style="background-color:#ffffff;">-->
<!--			<div class="input-group-btn">-->
<!--				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>-->
<!--				<ul id="documentlist-second-son" class="dropdown-menu dropdown-menu-right">-->
<!--					<li id="documentlist-second-son-1" value="1"><a>周</a></li>-->
<!--					<li id="documentlist-second-son-2" value="2"><a>月</a></li>-->
<!--					<li id="documentlist-second-son-3" value="3"><a>总</a></li>-->
<!--				</ul>-->
<!--			</div>-->
<!--		</div>-->
<!--	</div>-->
	<!--文档_子类型下拉菜单 2end-->
<!--End 将周月总选项去掉 xwd-->

<!--Start 添加时间日期控件 xwd-->
	<div class="dropdownlist1">
		<div class="input-group">
			<div class="timetitle">开始时间：</div>
			<div style="float: right" ><input type="text" id="start_time" /></div>
		</div>
	</div>

	<div class="dropdownlist1">
		<div class="input-group">
			<div class="timetitle">结束时间：</div>
			<div style="float: right" ><input type="text" id="end_time" /></div>
		</div>
	</div>
<!--End 添加时间日期控件 xwd-->

	<!--start 课程下拉菜单-->
	<div class="dropdownlist" id="courseTest">
		<div class="input-group">
			<input type="text" class="form-control classkinds" value="全部课程" readOnly="true" style="background-color:#ffffff;">
			<div class="input-group-btn">
				<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
				<ul id="documentlist-third-son" class="dropdown-menu dropdown-menu-right">
					<li value="1"><a>全部课程</a></li>
					<?php
						require_once("../../config.php");
						global $DB;
						$useid = $_GET['personid'];
						$courses=$DB->get_records_sql('select
											c.id,c.fullname
											from mdl_user_enrolments a
											join mdl_enrol b on b.id=a.enrolid
											join mdl_course c on c.id=b.courseid
											where a.userid='.$useid.'
											GROUP BY courseid ORDER BY a.timecreated DESC');
						$index = 1;
						foreach($courses as $course){
							echo '<li id="documentlist-third-son-'.$index.'" value="'.$course->id.'"><a>'.$course->fullname.'</a></li>';
							$index++;
						}
					?>
				</ul>
			</div>
		</div>
	</div>
	<!--end 课程下拉菜单-->

	<button class="btn btn-primary search">搜索</button>
</div>
		
		<!--<div class="dropdownlist-son-box"></div>-->
		
		<div class="table-box">
			
		</div>