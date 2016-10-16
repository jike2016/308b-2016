<?php
require_once("../config.php");

global $DB;
global $USER;
global $CFG;

?>

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
<!--	<link rel="stylesheet" href="css/navstyle.css" />-->

	<!--锁屏-->
	<style>
	.lockpage {z-index: 10000;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #000;opacity: 0.4;filter: alpha(opacity=40); text-align: center;vertical-align:middle; display: none;}
	.lockpage img {width: 60px;position:absolute;top:40%;}
	</style>

	<!-- start	时间控件-->
	<?php require_once("mybookdata/time_plug/time_plug_js.php"); ?>
	<!-- end 时间控件-->
	
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

	<script>
		$(document).ready(function() {
			//导航条列表样式控制 start
			$('.navRight li').removeClass('active');
//			$('.navRight .mod_privatecenter').addClass('active');
			//导航条列表样式控制 end
		});
	</script>
	
	<body>
		<!--锁屏-->
		<div class="lockpage">
			<img src="img/loading.jpg"/>
		</div>

<?php
/**Start 获取勋章数 徐东威 20160313  */
require_once("../lib/badgeslib.php");
require_once("../badges/renderer.php");
/**End */
//require_once($CFG->libdir. '/coursecatlib.php');
require_login();//要求登录
//判断权限
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

//停用
$old2= '<!--导航条-->
		<nav class="navstyle navbar-fixed-top">
			<div class="nav-main">
				<img id="logo" src="img/Home_Logo.png" onMouseOver="this.style.cursor=\'pointer\'" onClick="document.location=\''.$CFG->wwwroot.'\'">
				<ul class="nav-main-li">
					<a href="'.$CFG->wwwroot.'">
						<li class="li-normol">首页</li>
					</a>
					<a href="'.$CFG->wwwroot.'/microread/">
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
//停用
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
	global $DB;

	$mymadles = $DB->get_records_sql("select bi.badgeid from mdl_badge_issued bi where bi.userid = $USER->id");//获取用户的勋章
	if($mymadles){
		return count($mymadles);//返回勋章数
	}
	return 0;//没有勋章
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


/** ========= 页面输出 ============================= */
require_once("../theme/more/layout/includes/header.php");//导航条

echo '<!--个人信息展示板-->
		<div class="personalbanner">
			<div class="main-box">
				<div class="l-box">
					'.$userobject->metadata['useravatar'].'
				</div>
				<div class="c-box">
					<p class="top-info"><a href="#">'.$userobject->metadata['userfullname'].'</a>，'.my_get_date().'</p>
					<p class="bottom-info">'.$userobject->metadata['description'].'</p>
				</div>
				<div class="r-box">
					<div class="box">
						<p>证书</p>
						<p class="num">'.get_mymadle().'</p>
					</div>
				</div>
			</div>
		</div>
		<!--个人信息展示板 end-->';

?>

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

		<!--添加蒙版显示学习圈图片 Start-->
		<style>
			.shadow{
				width: 100%;
				height: 100%;
				position: fixed;
				top: 0;
				left: 0;
				z-index: 1031;
				background:  rgba(0,0,0,0.5);
				cursor: pointer;
			}
			.img-box{
				width: 100%;
				height: 100%;
				position: fixed;
				top: 0;
				z-index: 1032;
				cursor: pointer;
			}
			.img-inner{
				height: 100%;
				display:inline-block;
				margin: auto;
				position: absolute;
				top: 0;
				left: 0;
				bottom: 0;
				right: 0;
				padding: 100px;
				z-index: 1032;
				background-color: transparent;
			}
			/*加载动画*/
			.img-inner>img:nth-child(1){
				width: 100px;
				height: 100px;
				display: block;
				margin: 0 auto;
				/*position: absolute;*/
				/*top: 0;*/
				opacity: 1;
				-webkit-transition: opacity 0.5s;
				-moz-transition: opacity 0.5s;
				-ms-transition: opacity 0.5s;
				-o-transition: opacity 0.5s;
				transition: opacity 0.5s;
			}
			/*图片*/
			.img-inner>img:nth-child(2){
				max-width: 100%;
				max-height: 100%;
				display: block;
				margin: 0 auto;
				opacity: 0;
				position: relative;
				top: -100px;
				-webkit-transition: opacity 0.8s;
				-moz-transition: opacity 0.8s;
				-ms-transition: opacity 0.5s;
				-o-transition: opacity 0.5s;
				transition: opacity 0.8s;
			}
			.close-i{
				width: 50px;
				height: 50px;
				position: fixed;
				top: 10px;
				right: 10px;
				z-index: 1032;
				cursor: pointer;
			}
			.close-i img{
				width: 50px;
				height: 50px;
			}
		</style>

		<div class="shadow" style="display: none"></div>
		<div class="img-box" style="display: none">
			<div class="img-inner">
				<img class="img-thumbnail" src="img/loading.jpg" alt="">
				<img class="img-thumbnail" src="" onload="$(this).css('opacity',1);$('.img-inner img:nth-child(1)').css('opacity','0')" alt="">
			</div>
			<div class="close-i">
				<img src="../circlesoflearning/images/imgbox-close.png" alt="">
			</div>
		</div>
		<script>
			$('.close-i, .shadow, .img-box').on('click',function(){
				$('.shadow, .img-box').hide();
				$('.img-inner img:nth-child(1)').css('opacity',1);
				$('.img-inner img:nth-child(2)').css('opacity',0);
			})
		</script>
		<!--添加蒙版显示学习圈图片 end-->

		<!--底部导航条-->
		<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
		<?php require_once("../theme/more/layout/includes/bottom_info.php"); ?>
		<!--底部导航条 end-->

		<!--右下角按钮-->
<!--		--><?php //require_once("../theme/more/layout/includes/link_button.php"); ?>
		<!--右下角按钮 end-->

	</body>

</html>