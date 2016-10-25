<?php
/**
 * 港城百科分类页,显示单个分类的所有词条
 *
 * @author  cx
 * @date  20160930
 */

require_once("../config.php");
if(!isset($_GET['categoryid'])||!$_GET['categoryid']){
	//没有参数则跳转主页
	redirect(new moodle_url('/dokuwiki/index.php'));
}

//查询一级分类所有菜单menu
global $DB;
$first_categories = $DB->get_records_sql('select id,`name` from mdl_dokuwiki_categories_my where parent=0');
$current_category = $DB->get_record_sql('select id,`name`,parent from mdl_dokuwiki_categories_my where id='.$_GET['categoryid']);
if($current_category->parent == 0){
	$pre_category = $current_category->id;
}else{
	$pre_category = $current_category->parent;
}
//查询分类所有词条
$words = $DB->get_records_sql('select id,word_name from mdl_dokuwiki_word_my where categoryid='.$_GET['categoryid'].' order by create_time desc');

require_once('../user/my_role_conf.class.php');//引入角色配置
global $USER;
//已登录moodle，慕课管理员角色也用admin登录,登录doku,
$role_conf = new my_role_conf();
//判断是否是慕课管理员
$result = $DB->record_exists('role_assignments', array('roleid' => $role_conf->get_courseadmin_role(),'userid' => $USER->id));
$isadmin_my = false;
if($USER->id == 2 || $result){//是超级管理员或者慕课管理员
	$isadmin_my = true;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
	<head>
		<meta charset="UTF-8">
		<title>港城百科</title>
		<link href="lib/tpl/dokuwiki/css/bootstrap.css" rel="stylesheet">
		<link href="lib/tpl/dokuwiki/css/wiki.css" rel="stylesheet">
		<link href="lib/tpl/dokuwiki/css/wiki_Classification_of_entry.css" rel="stylesheet" />
		<script type="text/javascript" src="lib/tpl/dokuwiki/js/jquery-1.11.3.min.js"></script>
		<script>
			$(document).ready(function(){
				//页面滚动改变导航条
				$(function() {
					var win = $(window); //得到窗口对象
					var sc = $(document); //得到document文档对象。
					win.scroll(function() {
						if(sc.scrollTop() >= 145) {
							$(".nav-wiki-roll").fadeIn(300);
							$('.to-top').show();
						} else {
							$(".nav-wiki-roll").fadeOut(300);
							$('.to-top').hide();
						}
						$('.functionbtn-box').css('top',sc.scrollTop());
						
					})
				})
				//页面滚动改变导航条 end
				
				//右侧功能键
				$('.f-btn').mouseover(function(){
					$(this).children('.tips-box').show();
				})
				$('.f-btn').mouseout(function(){
					$(this).children('.tips-box').hide();
				})
				//右侧功能键 end
				
				//导航菜单
				$('.type_dropdown_menu').mouseover(function(){
					$(this).parent('li').css('background-color','#2e80d2');
				})
				$('.type_dropdown_menu').mouseout(function(){
					$(this).parent('li').css('background-color','');
				})
				
				//针对a标签的添加title动作
				$('.table tbody tr td a').each(function(){
					$(this).attr('title',$(this).text());
				});
				
			})
		</script>
	</head>
	<body>
	<!--导航条 滚动后-->
	<div class="nav-wiki-roll">
		<div class="center">
			<a href="doku.php"><img class="logo" src="lib/tpl/dokuwiki/img/Home_Logo1.png" /><a/>
			<form style='width:500px;float:right' action="doku.php?id=start" accept-charset="utf-8"  id="dw__search" method="get" role="search">
				<button type="submit" title="搜索" class="search-btn"><span class="glyphicon glyphicon-search"></button>
				<input type="hidden" name="do" value="search">
				<input class="form-control" type="text" placeholder="请输入关键字..." id="qsearch__in" accesskey="f" name="id" class="edit" title="[F]"/>
			</form>
		</div>
	</div>
	<!--导航条 滚动后 end-->

	<!--导航条 滚动前-->
	<div id="nav-wiki" class="nav-wiki">
			<div class="center">
				<p class="user-box">
					<a class="user" href="#">
						<?php
							global $USER;
							if($USER->id == 0){
								echo '<a href="../login">登录</a>';
							}else{
								echo fullname($USER, true);
							}
						?>
					</a>
					<?php
						/**START CX权限-页面元素输出判断20161006*/
						if($isadmin_my){
							//echo '<a class="manager" href="doku.php?id=start&amp;do=admin"><span class="glyphicon glyphicon-cog"></span>&nbsp;后台管理</a>';
							echo '<a class="manager" href="admin"><span class="glyphicon glyphicon-cog"></span>&nbsp;后台管理</a>';
						}
						/**END*/
					?>

				</p>
				<div>
					<a href="doku.php"><img class="logo" src="lib/tpl/dokuwiki/img/Home_Logo1.png" /></a>
					<div class="input-box">
						<form action="doku.php?id=start" accept-charset="utf-8"  id="dw__search" method="get" role="search">
							<input type="hidden" name="do" value="search">
							<input class="form-control" type="text" placeholder="请输入关键字..." id="qsearch__in" accesskey="f" name="id" class="edit" title="[F]"/>
							<button type="submit" title="搜索" class="search-btn">搜索</button>
							<div class="admin-box">
								<!--<a href="#">词条审核</a>
								<a href="doku.php?do=index">分类管理</a>								
								<a href="#">媒体管理器</a>
								<a href="#">最近更改</a>-->
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

	<div class="nav-wiki-menu">
		<div class="center">
			<ul class="menu">
				<li><a href="../">首页</a></li>
				<li class="type"><a href="doku.php">百科&nbsp;<span class="glyphicon glyphicon-chevron-down" style="font-size: 12px;margin-right: -12px;"></span></a>
					<ul class="type_dropdown_menu">
						<?php
						foreach($first_categories as $category){
							echo '<li><a href="categories.php?categoryid='.$category->id.'">'.$category->name.'</a></li>';
						}
						?>
					</ul>
				</li>
				<li><a class="nav-btn" href="../course">微课</a></li>
				<li><a class="nav-btn" href="../microread">微阅</a>
				<li><a class="nav-btn" href="../privatecenter">直播</a></li>

			</ul>
		</div>
	</div>
	<!--导航条 滚动前end-->

	<!--背景板-->
	<div class="bg-banner"></div>
	<!--背景板 end-->

	<div class="main">
			<!--一级分类板块-->
			<div class="first_classification">
				<p><a href="categories.php?categoryid=<?php echo $pre_category;?>"><<返回</a></p>
				<h2 class="title"><?php echo $current_category->name;?></h2>
				<table class="table">
					<tbody>
						<?php
							$n = 1;
							foreach ($words as $word){
								//5个一行
								if($n % 5 -1  == 0 || $n == 1){
									$output_my .= '<tr>';
								}
								$output_my .= '<td><a href="http://'.$_SERVER['HTTP_HOST'].'/moodle/dokuwiki/doku.php?id='.$word->word_name.'">'.$word->word_name.'</a></td>';
								if($n % 5 == 0){
									$output_my .= '</tr>';
								}
								$n++ ;
							}
							echo $output_my;
						?>

					</tbody>
				</table>
				
				<div class="clear-float"></div>
			</div>
			<p class="more" style="color:#C0C0C0;">已为您显示该分类的所有词条</p>
		<!--一级分类板块 end-->

			
			<!--右侧功能键-->
			<div class="functionbtn-box">
				<!--回到顶部-->
				<a href="#nav-wiki">
				<div class="f-btn to-top">
					<img src="lib/tpl/dokuwiki/img/totop.png" />
					<div class="tips-box">
						<div class="tips-bg"></div>
						<div class="tips-txt">回到顶部</div>
					</div>
				</div>
				</a>
			</div>
			<!--右侧功能键 end-->
		</div>
			
		
	</body>
</html>
