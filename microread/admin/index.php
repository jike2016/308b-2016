<?php 
//检查登陆
require_once('../../config.php');
require_once('../../user/my_role_conf.class.php');
require_login();
global $USER;
global $CFG;
global $DB;
/**start  权限判断 只允许超级管理员、慕课管理员 */
$role =  new my_role_conf();
if(!$DB->record_exists("role_assignments",array('userid'=>$USER->id,'roleid'=>$role->get_courseadmin_role()))
	&& ($USER->id!=2) ){
	redirect(new moodle_url('/index.php'));
}
/** end */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>微阅后台管理</title>

<link href="themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
<link href="themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
<link href="uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
<!--[if IE]>
<link href="themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
<![endif]-->

<!--[if lt IE 9]><script src="js/speedup.js" type="text/javascript"></script><script src="js/jquery-1.11.3.min.js" type="text/javascript"></script><![endif]-->
<!--[if gte IE 9]><!--><script src="js/jquery-2.1.4.min.js" type="text/javascript"></script><!--<![endif]-->

<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.validate.js" type="text/javascript"></script>
<script src="js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="xheditor/xheditor-1.2.2.min.js" type="text/javascript"></script>
<script src="xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script src="uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>

<!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
<script type="text/javascript" src="chart/raphael.js"></script>
<script type="text/javascript" src="chart/g.raphael.js"></script>
<script type="text/javascript" src="chart/g.bar.js"></script>
<script type="text/javascript" src="chart/g.line.js"></script>
<script type="text/javascript" src="chart/g.pie.js"></script>
<script type="text/javascript" src="chart/g.dot.js"></script>

<script src="js/dwz.core.js" type="text/javascript"></script>
<script src="js/dwz.util.date.js" type="text/javascript"></script>
<script src="js/dwz.validate.method.js" type="text/javascript"></script>
<script src="js/dwz.barDrag.js" type="text/javascript"></script>
<script src="js/dwz.drag.js" type="text/javascript"></script>
<script src="js/dwz.tree.js" type="text/javascript"></script>
<script src="js/dwz.accordion.js" type="text/javascript"></script>
<script src="js/dwz.ui.js" type="text/javascript"></script>
<script src="js/dwz.theme.js" type="text/javascript"></script>
<script src="js/dwz.switchEnv.js" type="text/javascript"></script>
<script src="js/dwz.alertMsg.js" type="text/javascript"></script>
<script src="js/dwz.contextmenu.js" type="text/javascript"></script>
<script src="js/dwz.navTab.js" type="text/javascript"></script>
<script src="js/dwz.tab.js" type="text/javascript"></script>
<script src="js/dwz.resize.js" type="text/javascript"></script>
<script src="js/dwz.dialog.js" type="text/javascript"></script>
<script src="js/dwz.dialogDrag.js" type="text/javascript"></script>
<script src="js/dwz.sortDrag.js" type="text/javascript"></script>
<script src="js/dwz.cssTable.js" type="text/javascript"></script>
<script src="js/dwz.stable.js" type="text/javascript"></script>
<script src="js/dwz.taskBar.js" type="text/javascript"></script>
<script src="js/dwz.ajax.js" type="text/javascript"></script>
<script src="js/dwz.pagination.js" type="text/javascript"></script>
<script src="js/dwz.database.js" type="text/javascript"></script>
<script src="js/dwz.datepicker.js" type="text/javascript"></script>
<script src="js/dwz.effects.js" type="text/javascript"></script>
<script src="js/dwz.panel.js" type="text/javascript"></script>
<script src="js/dwz.checkbox.js" type="text/javascript"></script>
<script src="js/dwz.history.js" type="text/javascript"></script>
<script src="js/dwz.combox.js" type="text/javascript"></script>
<script src="js/dwz.print.js" type="text/javascript"></script>

<!-- 可以用dwz.min.js替换前面全部dwz.*.js (注意：替换时下面dwz.regional.zh.js还需要引入)
<script src="bin/dwz.min.js" type="text/javascript"></script>
-->
<script src="js/dwz.regional.zh.js" type="text/javascript"></script>

<script type="text/javascript">
$(function(){
	DWZ.init("dwz.frag.xml", {
		loginUrl:"login_dialog.html", loginTitle:"登录",	// 弹出登录对话框
//		loginUrl:"login.html",	// 跳到登录页面
		statusCode:{ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
		keys: {statusCode:"statusCode", message:"message"}, //【可选】
		ui:{hideMode:'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"themes"}); // themeBase 相对于index页面的主题base路径
		}
	});
});

</script>
</head>

<body>
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="#">标志</a>
				<p style="font-family: 华文彩云; font-size: 50px;color: #d0d0d0" >微阅后台管理</p>
				<ul class="nav">
					<li><a><?php echo fullname($USER, true);?></a></li>
					<li><?php echo '<a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a>';?></li>
				</ul>
			</div>

			<!-- navMenu -->
			
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>主菜单</h2><div>收缩</div></div>

				<div class="accordion" fillSpace="sidebar">
					<div class="accordionHeader">
						<h2><span>Folder</span>微阅管理</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a>书库管理</a>
								<ul>
									<li><a href="bookroom/ebook.php" target="navTab" rel="ebook" fresh="false">电子书管理</a></li>
									<li><a href="bookroom/category.php" target="navTab" rel="ebookcategory" fresh="false">分类管理</a></li>
									<li><a href="bookroom/author.php" target="navTab" rel="ebookauthor" fresh="false">作者管理</a></li>
									<li><a href="bookroom/recommendlist.php" target="navTab" rel="ebookrecommendlist" fresh="false">首页推荐榜管理</a></li>
									<li><a href="bookroom/user_upload.php" target="navTab" rel="ebookuser_upload" fresh="false">用户上传电子书审核</a></li>
								</ul>
							</li>
							<li><a>文库管理</a>
								<ul>
									<li><a href="docroom/doclibrary.php" target="navTab" rel="doclibrary">文档管理</a></li>
									<li><a href="docroom/category.php" target="navTab" rel="doccategory">分类管理</a></li>
									<li><a href="docroom/tag.php" target="navTab" rel="doctag">标签管理</a></li>
									<li><a href="docroom/categoryrecommendlist.php" target="navTab" rel="doccategoryrecommendlist">首页分类推荐管理</a></li>
									<li><a href="docroom/authorrecommendlist.php" target="navTab" rel="docauthorrecommendlist">首页贡献作者推荐管理</a></li>
									<li><a href="docroom/recommendlist.php" target="navTab" rel="docrecommendlist">首页推荐榜管理</a></li>
									<li><a href="docroom/user_upload.php" target="navTab" rel="docuser_upload" fresh="false">用户上传文档审核</a></li>
								</ul>
							</li>
							<li><a>图库管理</a>
								<ul>
									<li><a href="picroom/picture.php" target="navTab" rel="picture">图片管理</a></li>
									<li><a href="picroom/tag.php" target="navTab" rel="pictag">标签管理</a></li>
									<li><a href="picroom/pictagrecommend.php" target="navTab" rel="pictagcommend">首页推荐搜索词管理</a></li>
									<li><a href="picroom/picindexbg.php" target="navTab" rel="picindexbg">首页背景图管理</a></li>
									<li><a href="picroom/user_upload.php" target="navTab" rel="picuser_upload" fresh="false">用户上传图片审核</a></li>
								</ul>
							</li>
							<li><a href="indexadvertising/advertising.php" target="navTab" rel="advertising">微阅首页广告栏管理</a></li>
							<li><a href="../upload_switch.php" target="_blank" rel="upload_switch">微阅用户上传全局开关</a></li>
							
						</ul>
					</div>
		
					
				
				</div>
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader" >
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList" >
					<li><a href="javascript:;">我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div class="accountInfo"></div>
						<div class="pageFormContent" layoutH="80" style="margin-right:230px"></div>

						<div style="width:230px;position: absolute;top:60px;right:0" layoutH="80">
							<iframe width="100%" height="430" class="share_self"  frameborder="0" scrolling="no" src=""></iframe>
						</div>
					</div>
					
				</div>
			</div>
		</div>

	</div>

</body>
</html>