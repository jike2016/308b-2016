<?php
/**
 * DokuWiki Default Template 2012
 *
 * @link     http://dokuwiki.org/template
 * @author   Anika Henke <anika@selfthinker.org>
 * @author   Clarence Lee <clarencedglee@gmail.com>
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */
if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */
header('X-UA-Compatible: IE=edge,chrome=1');

$id_my='start';
$word_name='港城百科';
if(isset($_GET["id"])&&$_GET["id"]){
	$id_my=$_GET["id"];//词条url名称
	$word_name=$id_my;
	if($id_my=='start'){
		$word_name='港城百科';
	}
}
$rev_my = '';//编辑历史版本的按钮用
if(isset($_GET["rev"])&&$_GET["rev"]){
	$rev_my = '&rev='.$_GET["rev"];
}
//查询百科一级分类
global $DB;
$first_categories = $DB->get_records_sql('select id,`name` from mdl_dokuwiki_categories_my where parent=0');
//判断超级管理员或慕课管理员
$isadmin_my = auth_ismanager();
?>
<!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset="utf-8" />
    <title><?php tpl_pagetitle() ?> _港城百科</title>
	<link href="lib/tpl/dokuwiki/css/bootstrap.css" rel="stylesheet">
	<link href="lib/tpl/dokuwiki/css/wiki.css" rel="stylesheet">
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
		})
	</script>
	
<!--dokuwiki 原样式--> 
<!--<meta name="generator" content="DokuWiki"/>
<meta name="robots" content="noindex,nofollow"/>
<link rel="search" type="application/opensearchdescription+xml" href="/moodle/dokuwiki/lib/exe/opensearch.php" title="港城百科"/>
<link rel="start" href="/moodle/dokuwiki/"/>
<link rel="contents" href="/moodle/dokuwiki/doku.php?id=start&amp;do=index" title="网站地图"/>
<link rel="alternate" type="application/rss+xml" title="最近更改" href="/moodle/dokuwiki/feed.php"/>
<link rel="alternate" type="application/rss+xml" title="当前命名空间" href="/moodle/dokuwiki/feed.php?mode=list&amp;ns="/>
<link rel="alternate" type="text/html" title="纯HTML" href="/moodle/dokuwiki/doku.php?do=export_xhtml&amp;id=start"/>
<link rel="alternate" type="text/plain" title="Wiki Markup 语言" href="/moodle/dokuwiki/doku.php?do=export_raw&amp;id=start"/>
<link rel="stylesheet" type="text/css" href="/moodle/dokuwiki/lib/exe/css.php?t=dokuwiki&amp;tseed=e09aa6794814ecae2fa05e1eddb7985b"/>
<script type="text/javascript">/*<![CDATA[*/var NS='';var SIG=' --- //[[dsddfgsg@qq.com|呵呵呵]] 2016/09/28 16:42//';var JSINFO = {"id":"start","namespace":""};
/*!]]>*/</script>
<script type="text/javascript" charset="utf-8" src="/moodle/dokuwiki/lib/exe/js.php?t=dokuwiki&amp;tseed=e09aa6794814ecae2fa05e1eddb7985b"></script>
<!--dokuwiki 原样式-->



</head>

<body>
<!--导航条 滚动后-->
		<div class="nav-wiki-roll">
			<div class="center">
				<img class="logo" src="lib/tpl/dokuwiki/img/Home_Logo1.png" />
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
					<img class="logo" src="lib/tpl/dokuwiki/img/Home_Logo1.png" />
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
				<!--a class="nav-btn" href="../">首页</a>
				<a class="nav-btn" href="../course">微课</a>
				<a class="nav-btn" href="../microread">微阅</a>
				<a class="nav-btn" href="../privatecenter">直播</a>
				<a class="nav-btn" href="doku.php">百科</a-->
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
			<h1 class="title"><?php echo $word_name;?>
				<?php
				if(!isset($_GET["do"])){
					echo '<a href="doku.php?id='.$id_my.'&do=edit" class="edit_btn"><span class="glyphicon glyphicon-edit"></span>&nbsp;编辑</a>';
				}
				?>

			</h1>
			<div class="wrapper group">
            <!-- ********** CONTENT ********** -->
				<div id="dokuwiki__content">
					<div class="pad group">
						<div class="page group">
							<?php tpl_flush() ?>
							<?php tpl_includeFile('pageheader.html') ?>
							<!-- wikipage start -->
							<?php tpl_content() ?>
							<!-- wikipage stop -->
							<?php tpl_includeFile('pagefooter.html') ?>
						</div>
						<?php tpl_flush() ?>
					</div>
				</div><!-- /content -->
				<hr class="a11y" />
			</div><!-- /wrapper -->
		
		
			<!--右侧功能键-->
			<div class="functionbtn-box">
				<!--显示页面-->
				<a href="doku.php?id=<?php echo $id_my;?>">
				<div class="f-btn showpage">
					<img src="lib/tpl/dokuwiki/img/showpage.png" />
					<div class="tips-box">
						<div class="tips-bg"></div>
						<div class="tips-txt">显示词条</div>
					</div>
				</div>
				</a>
				<!--编辑本页-->
				<a href="doku.php?id=<?php echo $id_my.'&do=edit'.$rev_my;?>">
				<div class="f-btn edit">
					<img src="lib/tpl/dokuwiki/img/edit.png" />
					<div class="tips-box">
						<div class="tips-bg"></div>
						<div class="tips-txt">编辑词条</div>
					</div>
				</div>
				</a>

				<?php
				/**START CX权限-页面元素输出判断20161006*/
				if($isadmin_my){
					//echo '<a class="manager" href="doku.php?id=start&amp;do=admin"><span class="glyphicon glyphicon-cog"></span>&nbsp;后台管理</a>';
					echo '	<!--修订记录-->
						<a href="doku.php?id='.$id_my.'&do=revisions">
							<div class="f-btn record">
								<img src="lib/tpl/dokuwiki/img/record.png" />
								<div class="tips-box">
									<div class="tips-bg"></div>
									<div class="tips-txt">修订记录</div>
								</div>
							</div>
						</a>';
				}
				/**END*/
				?>
				
				<!--页面重命名
				<a href="#">
				<div class="f-btn rename">
					<img src="lib/tpl/dokuwiki/img/rename.png" />
					<div class="tips-box">
						<div class="tips-bg"></div>
						<div class="tips-txt">页面重命名</div>
					</div>
				</div>		
				</a>-->

				<!--创建该页面
				<a href="#">
				<div class="f-btn buildpage">
					<img src="lib/tpl/dokuwiki/img/buildpage.png" />
					<div class="tips-box">
						<div class="tips-bg"></div>
						<div class="tips-txt">创建词条</div>
					</div>
				</div>
				</a>-->
				
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
