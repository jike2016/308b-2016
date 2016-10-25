<?php
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org_classify/org.class.php');
require_once($CFG->dirroot . '/user/my_role_conf.class.php');

global $DB;
global $USER;
/**start  权限判断 只允许 '单位角色'、或者是'分级管理角色' */
$role = new my_role_conf();
if(!$DB->record_exists('role_assignments', array('roleid' => $role->get_unit_role(),'userid' => $USER->id)) &&
	!$DB->record_exists('role_assignments', array('roleid' => $role->get_gradingadmin_role(),'userid' => $USER->id)) ){
		redirect($CFG->wwwroot);
}
if(!$DB->record_exists("org_link_user",array('user_id'=>$USER->id))){//当账号所属的单位被删除时
	echo "<script>alert('您当前的账号未分配所属单位，请联系系统管理员');</script>";
	exit();
}
/**end 权限判断  */
//其他能查看台账的条件如某个人
// elseif(){
	
// }
//查询当前用户id在组织架构内的位置，输出下级树(后面要做成权限赋予型)
$org = new org();
$orgid = $org->get_nodeid_with_userid($USER->id);
// $orgid = $org->get_nodeid_with_userid(12);

//$tree = $org->show_node_tree_user_no_office($orgid);
//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('comment_data.php');
$tree = $org->show_node_tree_user_no_office_no_grading($orgid,$remove_role);

?>

<!DOCTYPE html>
<HTML>
<HEAD>
	<TITLE>台账数据中心</TITLE>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	
	<link rel="stylesheet" href="zTreeStyle/zTreeStyle.css" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.css" />
	<link rel="stylesheet" href="css/treepage.css" type="text/css">
	<link href="css/ledgercenter.css" rel="stylesheet" type="text/css"/>
	
	<link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->
	<!--锁屏-->
	<style>
	.lockpage {z-index: 10000;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #000;opacity: 0.4;filter: alpha(opacity=40); text-align: center;vertical-align:middle; display: none;}
	.lockpage img {width: 60px;position:absolute;top:40%;}
	</style>

	<!--Start 时间日期控件 xdw -->
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript">
		var jqmin = jQuery.noConflict();
	</script>
	<script type="text/javascript" src="js/jquery-ui.js"></script>
	<script type="text/javascript" src="js/jquery-ui-slide.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
	<!--End 时间日期控件 xdw -->
	
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="js/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="js/jquery.ztree.exedit.js"></script>
<!--	<SCRIPT type="text/javascript" src="js/ztreepage.js"></SCRIPT>-->
	<script type="text/javascript" src="js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="js/jsapi.js"></script>
	<script type="text/javascript" src="js/corechart.js"></script>
	<script type="text/javascript" src="js/jquery.gvChart-1.0.1.min.js"></script>
	<script type="text/javascript" src="js/highcharts.js"></script>
	<script type="text/javascript" src="js/exporting.js"></script>
	<script>
		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);
					
			$('.dropdown-menu li').on('click', function(){   //下拉菜单动作
				$(this).parent().parent().parent().children('.classkinds').val($(this).text());
			});	
			
			var documentlistid; //存储文档类型下拉菜单的id
			
			$("#documentlist li").on('click',function(){ //获取文档类型下拉菜单的id
				documentlistid=$(this).attr("id");
				$(this).addClass("li_active");
				$(".dropdownlist-son-box").load('documentlist-son-page/first-son-page.html');
			});

			//控制下拉菜单宽度
			$(document).on('click','.btn-info.dropdown-toggle',function(){
				$('.main .right .dropdownlist-box .dropdown-menu').css('width',$('.dropdownlist').width());
			})
			//控制下拉菜单宽度
			
		});
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
				 beforeClick: beforeClick
			}
			
		};

		var zNodes =[
			<?php echo $tree['tree'];?>
		];
		
		function beforeClick(treeId, treeNode, clickFlag) {   //click为true时不执行，默认为执行
//			alert(treeNode.userid);
//			alert(treeNode.id);
			$('.lockpage').show();
			if(treeNode.userid == 0 && treeNode.id != 0)
				$(".right").load('office/index.php?orgid='+treeNode.id);
			else if(treeNode.userid != 0 && treeNode.id == 0)
				$(".right").load('person/index.php?personid='+treeNode.userid);

			 // return (treeNode.click == true);
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#tree"), setting, zNodes);			
			// $("#tree a").click(function(){
				// if($(this).children("span").hasClass("ico_docu"))
					// $(".right").load('person/index.php');
				// else
					// $(".right").load('office/index.php');
			// })
		});
	</script>
	<script>
		//导航栏搜索 start
		$(document).ready(function() {
			$('.login ul li a').css("color","#000");
			$('.usertext').css("color","#000");
			$('#searchtype a').click(function() {
				$('#searchtypebtn').text($(this).text());
				$('#searchtypebtn').append('<span class="caret"></span>');
			});
			//start 网站搜索
			$('.dropdown-toggle').click(function(){
				if($('.search-box .dropdown-menu').hasClass('show'))
					$('.search-box .dropdown-menu').removeClass('show');
				else
					$('.search-box .dropdown-menu').addClass('show');
			});
			$('.search-box .dropdown-menu li').click(function(){
				$('.search-box .dropdown-toggle').text($(this).children('a').text());
				$('.search-box .dropdown-toggle').append('<span class="caret"></span>');
				$('.search-box .dropdown-menu').removeClass('show');
			});
			$("#search_btn").click(function(){
				var search_type = $("#searchtypebtn").text();
				var search_param = $("#search_param").val();
				switch(search_type){
					case '课程':
						window.open( "<?php echo $CFG->wwwroot;?>/course/mysearch.php?searchType=课程名&searchParam="+search_param);
						break;
					case '书籍':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/bookroom/searchresult.php?searchType=标题&searchParam="+search_param);
						break;
					case '文档':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/docroom/searchresult.php?searchType=标题&searchParam="+search_param);
						break;
					case '图片':
						window.open( "<?php echo $CFG->wwwroot;?>/microread/picroom/image-search.php?word="+search_param);
						break;
					default:
						break;
				}
			});
			//end 网站搜索

		});

		//start 回车事件
		document.onkeydown = function (e) {
			var theEvent = window.event || e;
			var code = theEvent.keyCode || theEvent.which;
			if ( $('#search_param').val() != '' && code == 13) {
				$("#search_btn").click();
			}
		}
		//end 回车事件
		//导航栏搜索 end
	</script>
</HEAD>

<BODY>
<div class="lockpage">
	<img src="pix/loading.jpg"/>
</div>
<!--顶部导航条-->
<div class="nav navbar navbar-fixed-top">
	<div class="center">
		<div class="l-box">
			<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo1.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">

			<ul class="navRight">
				<li><a href="<?php echo $CFG->wwwroot;?>">首页</a></li>
				<li class="mod_course"><a href="<?php echo $CFG->wwwroot;?>/course/index.php">微课</a></li>
				<li class="mod_microread"><a href="<?php echo $CFG->wwwroot;?>/microread/">微阅</a></li>
				<li class="mod_zhibo"><a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">直播</a></li>
				<li class="mod_privatecenter"><a href="#"></a></li>
			</ul>
		</div>
		<div class="r-box">

			<button class="btn btn-info searchbtn" id="search_btn" ><span class="glyphicon glyphicon-search"></span></button>
			<input class="form-control search" id="search_param" placeholder="请输入关键词..." />

			<!--下拉菜单-->
			<div class="btn-group">
				<button id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">课程<span class="caret"></span></button>
				<ul id="searchtype" class="dropdown-menu">
					<li><a href="#">课程</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">文档</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">图片</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">书籍</a></li>
				</ul>
			</div>
			<!--下拉菜单 end-->

		</div>
	</div>
</div>
<!--顶部导航条 end-->


<div class="main">
	<div class="zTreeDemoBackground left">
		<ul id="tree" class="ztree"></ul> <!--important 显示文件树的地方-->
	</div>
	
	<div class="right">
		
	</div>	
</div>
</BODY>
</HTML>