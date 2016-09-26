<?php
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org/org.class.php');
require_once($CFG->dirroot . '/user/my_role_conf.class.php');

global $DB;
global $USER;
$role = new my_role_conf();
/** Start 权限判断 只允许超级管理员、慕课管理员 进入 */
$courseAdminFlag = false;//慕课管理员标志
if($DB->record_exists('role_assignments', array('roleid' => $role->get_courseadmin_role(),'userid' => $USER->id))){
	$courseAdminFlag = true;
}
if( !$courseAdminFlag && ($USER->id != 2) ){
	redirect($CFG->wwwroot);
}
/** end */

//查询当前用户id在组织架构内的位置，输出下级树(后面要做成权限赋予型)
$org = new org();
if($courseAdminFlag){//如果是慕课管理员角色（因为慕课管理员没有单位所属）
	$orgid = $org->get_root_node_id();
}else{
	$orgid = $org->get_nodeid_with_userid($USER->id);
}

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
</HEAD>

<BODY>
<div class="lockpage">
	<img src="pix/loading.jpg"/>
</div>
<!--导航条-->
<nav class="navstyle navbar-fixed-top">
	<div class="nav-main">
		<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
		<ul class="nav-main-li">
			<a href="<?php echo $CFG->wwwroot;?>">
				<li class="li-normol">首页</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/microread/">
				<li class="li-normol">微阅</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/course/index.php">
				<li class="li-normol">微课</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">
				<li class="li-normol">直播</li>
			</a>
		</ul>
		<div class="usermenu-box">

		</div>
	</div>
</nav>
<!--导航条 end-->
<div class="main">
	<div class="zTreeDemoBackground left">
		<ul id="tree" class="ztree"></ul> <!--important 显示文件树的地方-->
	</div>
	
	<div class="right">
		
	</div>	
</div>
</BODY>
</HTML>
