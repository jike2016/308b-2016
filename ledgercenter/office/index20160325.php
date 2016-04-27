<?php 
require_once("../../config.php");
global $DB;
$missions = $DB -> get_records_sql('select id,mission_name from mdl_mission_my order by id desc');
$output1 = '';
$output2 = '';
$firstmissionname='';
$firstmissionid='';
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
		}
		else{
			document.getElementById("mytime").style.display="none";
			document.getElementById("mymission").style.display="";
		}
	});
	$('#documentlist-third-son li').on('click', function(){   //文档_子类型下拉表单 3动作
		documentlist_third_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});
	$('#documentlist-fourth-son li').on('click', function(){   //文档_子类型下拉表单 3动作
		documentlist_fourth_son_id = $(this).val();       //获取文档_子类型下拉菜单 3选择项的id
	});
	
	
	$(".search").on('click', function(){   //搜索按钮动作
		$('.lockpage').show();
		// var treenodeactiveid = $(".curSelectedNode").attr("id");  //获取激活的树节点的id	
		 // alert(documentlist_fourth_son_id+'f');		
		if(orgid==null){
			alert('请选择组织架构');
			$('.lockpage').hide();
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==1){
			$(".table-box").load('office/officelearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);	
		}
		else if(documentlist_first_son_id==1&&documentlist_second_son_id==2){
			$(".table-box").load('office/officemission.php?missionid='+documentlist_fourth_son_id+'&orgid='+orgid);
		}
		else if(documentlist_first_son_id==2&&documentlist_second_son_id==1){
			$(".table-box").load('office/personlearn.php?timeid='+documentlist_third_son_id+'&orgid='+orgid);	
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
	<!--文档_子类型下拉菜单 3-->
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
	

	<button class="btn btn-primary search">搜索</button>
		</div>
		
		<!--<div class="dropdownlist-son-box"></div>-->
		
		<div class="table-box">
			
		</div>