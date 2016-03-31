<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>
			港城慕课-个人中心
		</title>
	</head>
	<link rel="stylesheet" href="css/bootstrap.css" />
	<link rel="stylesheet" href="css/personal-style.css" />
	<link rel="stylesheet" href="css/navstyle.css" />
	<!--锁屏-->
	<style>
	.lockpage {z-index: 10000;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #000;opacity: 0.4;filter: alpha(opacity=40); text-align: center;vertical-align:middle; display: none;}
	.lockpage img {width: 60px;position:absolute;top:40%;}
	</style>
	
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/allpage.js"></script>
	<!--图表js插件-->
	<script type="text/javascript" src="js/highcharts.js" ></script>
	<script type="text/javascript" src="js/exporting.js" ></script>
	<!--图表js插件 end-->
	
	<!--饼状图js文件-->
	<script type="text/javascript" src="js/jsapi.js"></script>
	<script type="text/javascript" src="js/corechart.js"></script>
	<script type="text/javascript" src="js/jquery.gvChart-1.0.1.min.js"></script>
	<!--饼状图js文件 end-->
	
	
	<body>
		<!--锁屏-->
		<div class="lockpage">
			<img src="img/loading.jpg"/>
		</div>

		<!--导航条-->	
<?php
require_once("../config.php");

/**Start 获取勋章数 徐东威 20160313  */
require_once("../lib/badgeslib.php");
require_once("../badges/renderer.php");
/**End */
//require_once($CFG->libdir. '/coursecatlib.php');
require_login();//要求登录
//判断权限
global $DB;
global $USER;
global $CFG;
$userobject = new stdClass();
$userobject->metadata = array();
$userobject->metadata['userfullname'] = fullname($USER, true);
$userobject->metadata['useravatar'] = $OUTPUT->user_picture (
	$USER,
	array(
		'link' => false,
		'visibletoscreenreaders' => false
	)
);
$userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
//获取自述
$description = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
$userobject->metadata['description'] = $description->description;
$a= userdate(time(),'%Y-%m-%d %H:%M');

echo '
		<!--导航条-->
		<nav class="navstyle navbar-fixed-top">
			<div class="nav-main">
				<img id="logo" src="img/Home_Logo.png" onMouseOver="this.style.cursor=\'pointer\'" onClick="document.location='.$CFG->wwwroot.';">
				<ul class="nav-main-li">
					<a href="'.$CFG->wwwroot.'">
						<li class="li-normol">首页</li>
					</a>
					<a href="'.$CFG->wwwroot.'/mod/forum/view.php?id=1">
						<li class="li-normol">微阅</li>
					</a>
					<a href="'.$CFG->wwwroot.'/course/index.php">
						<li class="li-normol">微课</li>
					</a>
					<a href="'.$CFG->wwwroot.'/privatecenter/index.php?class=zhibo">
						<li class="li-normol">直播</li>
					</a>
				</ul>								
			</div>
		</nav>
		<!--导航条 end-->

		<!--个人资料-->
		<div class="personal-main">
			<div class="nav-main-left">
				<div class="outbox">
					<div class="Learnerimg-box">
						'.$userobject->metadata['useravatar'].'
					</div>
				</div>
					
				<div class="myword">
					<div class="hello">
						<p>'.$userobject->metadata['userfullname'].'</p>
							<p>,'.my_get_date().'</p>
					</div>
					<p class="words">'.$userobject->metadata['description'].'</p>
				</div>
			</div>
				
			<div class="nav-main-right">					
				<!--20160313暂时不需要
				<div class="nav-main-right-block">
					<p>等级</p>
					<p>副连一年</p>
				</div>					
				<div class="vline"></div>-->
				<div class="nav-main-right-block">
					<p>证书</p>
					<p>'.get_mymadle().'</p>
				</div>
			</div>
		</div>
		<!--个人资料 end';
$old= '
		<nav class="navbar navbar-inverse navbar-fixed-top">
			
			<div class="nav-main">
				<div class="nav-main-left">
					<div class="outbox">
						<div class="Learnerimg-box">
							'.$userobject->metadata['useravatar'].'
						</div>
					</div>
					
					<div class="myword">
						<div class="hello">
							<p>'.$userobject->metadata['userfullname'].'</p>
							<p>,'.my_get_date().'</p>
						</div>
						<p class="words">'.$userobject->metadata['description'].'</p>
					</div>
				</div>
				
				<div class="nav-main-right">
					<div class="nav-main-right-block a-box">
						<a href="'.$CFG->wwwroot.'">&nbsp;&nbsp;<span class="glyphicon glyphicon-home"></span>&nbsp;返回首页</a>
					</div>
					<!-- 20160313暂时不需要
					<div class="nav-main-right-block">
						<p>等级</p>
						<p>副连一年</p>
					</div>					
					<div class="vline"></div>-->
					<div class="nav-main-right-block">
						<p>证书</p>
						<p>'.get_mymadle().'</p>
					</div>
				</div>
			</div>
		</nav>
';

/**Start 获取勋章数 徐东威 20160313*/
function get_mymadle(){
	
	global $USER;
	
	$records = badges_get_badges(1, 0, 'name', 'DESC', 0, 100, $USER->id);//备注：这里的参数有些写死了
	$badges             = new badge_collection($records);
	$medalcount = $badges->badges;//勋章记录数组
	
	return count($medalcount);
}
/**End */

function my_get_date(){
	$date = date("H");
	if(6<=$date&&$date<=11)
		return '上午好！';
	elseif(12<=$date&&$date<=13)
		return '中午好！';
	elseif(14<=$date&&$date<=19)
		return '下午好！';
	elseif((20<=$date&&$date<=24)||(0<=$date&&$date<=5))
		return '晚上好！';
}
?>
		<!--导航条end-->

		<!--主体内容-->
		<div class="main">
			<!--左边菜单-->
			<div class="left-menu">
		
				
				<div id="mycourse" class="menubtn menubtn-active"><h4><span class="glyphicon glyphicon-book"></span>&nbsp;我的课程</h4></div>				
				<div id="myexam" class="menubtn"><h4><span class="glyphicon glyphicon-time"></span>&nbsp;我的考试</h4></div>
				<div id="mynote" class="menubtn"><h4><span class="glyphicon glyphicon-edit"></span>&nbsp;我的笔记</h4></div>
				<div id="mymedal" class="menubtn"><h4><span class="glyphicon glyphicon-tasks"></span>&nbsp;我的证书</h4></div>
				<div id="mymission"  class="menubtn"><h4><span class="glyphicon glyphicon-bookmark"></span>&nbsp;学习任务</h4></div>
				<div id="mybookdata" class="menubtn"><h4><span class="glyphicon glyphicon-list-alt"></span>&nbsp;台账数据</h4></div>
				<div id="learning_circles" class="menubtn"><h4><span class="glyphicon glyphicon-education"></span>&nbsp;学习圈</h4></div>
				<!--a href="../blog/" style="text-decoration:none;""><div class="menubtn"><h4><span class="glyphicon glyphicon-education"></span>&nbsp;学习圈</h4></div>	</a-->
				<div id="mycollection" class="menubtn"><h4><span class="glyphicon glyphicon-heart-empty"></span>&nbsp;我的收藏</h4></div>				
				<div id="personaldata" class="menubtn"><h4><span class="glyphicon glyphicon-user"></span>&nbsp;个人资料</h4></div>
			</div>
			<!--左边菜单end-->


			<!--右边内容-->
			<div class="right-banner">

			</div>			
			<!--右边内容end-->
		</div>
		<!--主体内容end-->
		
	</body>

</html>