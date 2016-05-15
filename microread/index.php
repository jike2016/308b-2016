<?php
require_once ("../config.php");
global $USER;
//搜索广告栏
global $DB;
$ads = $DB->get_records_sql('select * from mdl_microread_indexad_my where picurl != "" ORDER BY id');
$picsearchs = $DB->get_records_sql('select * from mdl_pic_recommended_search');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>微阅首页</title>
		<link rel="stylesheet" href="css/bootstrap.css" />
		<link rel="stylesheet" href="css/weiyuepage.css" />
		<link rel="stylesheet" href="css/weiyueindex.css" />

		<script type="text/javascript" src="js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="js/bootstrap.min.js" ></script>
		
		<!--控制图片轮转js文件-->
		<script type="text/javascript" src="js/Imagerotation/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="js/Imagerotation/roundabout.js" ></script>
		<script type="text/javascript" src="js/Imagerotation/roundabout_shapes.js" ></script>
		<script type="text/javascript" src="js/Imagerotation/gallery_init.js" ></script>
		<!--控制图片轮转js文件 end-->
		
		<!-- 轮播广告 -->
		<script src="js/slider.js"></script>
		<script type="text/javascript">
		$(function() {
			var bannerSlider = new Slider($('#banner_tabs'), {
				time: 3000,
				delay: 400,
				event: 'hover',
				auto: true,
				mode: 'fade',
				controller: $('#bannerCtrl'),
				activeControllerCls: 'active'
			});
			$('#banner_tabs .flex-prev').click(function() {
				bannerSlider.prev()
			});
			$('#banner_tabs .flex-next').click(function() {
				bannerSlider.next()
			});
		})
		</script>
		<!-- 轮播广告 end-->
		<script>
			$(document).ready(function() {
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('&nbsp;<span class="caret"></span>');
				});
			});
			//搜索
			function search(){
				var searchType = document.getElementById("searchtypebtn");//获取查询参数
				var searchParam = document.getElementById("searchParam");//获取选项
				if(searchType.textContent.indexOf("书籍")==0){
					window.location.href="bookroom/searchresult.php?searchType=全部&searchParam="+searchParam.value;
				}
				else if(searchType.textContent.indexOf("文档")==0){
				
					window.location.href="docroom/searchresult.php?searchType=全部&searchParam="+searchParam.value+"&searchDocType=all";
				}
				else if(searchType.textContent.indexOf("图片")==0){
					window.location.href="picroom/image-search.php?word="+searchParam.value;
				}
				
				
				// window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value;
			}
		</script>
	</head>
	<body id="weiyueindex">
		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<div class="a-box">
					<a class="nav-a frist"  href="<?php echo $CFG->wwwroot; ?>">首页</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/microread/">微阅</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/course/index.php">微课</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/privatecenter/index.php?class=zhibo">直播</a>
					<?php if($USER->id==0)echo '<a class="nav-a login" href="'.$CFG->wwwroot.'/login/index.php"><img src="img/denglu.png"></a>';?>
				</div>
				<?php 
				
					if($USER->id!=0){
						echo '<div id="usermenu" class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									<a href="#" class="username">'.fullname($USER, true).'</a>
									<a href="#" class="userimg">'.$OUTPUT->user_picture($USER,array('link' => false,'visibletoscreenreaders' => false)).'</a>
								</button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
									<li><a href="'.new moodle_url('/privatecenter/').'">个人中心</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="'.new moodle_url('/message/').'">消息</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="user_upload.php">上传电子书</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>
								</ul>
							</div>';
					};
				?>
			</div>
		</div>
		
		<div class="header-banner">
			<a href="index.php"><img  src="img/logo_weiyue.png"/></a>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
			     	<div class="input-group-btn">
			        	<button id="searchtypebtn" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">书籍&nbsp;<span class="caret"></span></button>
			        	<ul id="searchtype" class="dropdown-menu">
			          		<li><a id="book" href="#">书籍</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a id="literature" href="#">文档</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a id="paper" href="#">图片</a></li>
			        	</ul>
			      	</div><!-- /btn-group -->
			      	<input id="searchParam" type="text" class="form-control" >
			    </div><!-- /input-group -->
			    <button onclick="search()" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>		    
			</div>
			<!--搜索框组 end-->
		</div>
		<!--顶部导航 end-->
		
		<!--书本分类-->
		<div class="bookclassified">
			<div class="bookclassified-center">	
				<a href="index.php" class="kinds">首页</a>			
				<div class="line"></div>
				<a href="bookroom/" class="kinds">书库</a>
				<div class="line"></div>
				<a href="docroom/" class="kinds">文库</a>
				<div class="line"></div>
				<a href="picroom" class="kinds">图库</a>
			</div>
		</div>
		<!--书本分类 end-->
		
		<!-- 轮播广告 -->
		<div id="banner_tabs" class="flexslider">
			<ul class="slides">
				<?php 
				foreach($ads as $ad){
					echo '
						<li>
							<a title="" target="_blank" href="'.$ad->linkurl.'">
								<img width="1920" alt="" style="background: url('.$ad->picurl.') no-repeat center;" src="img/alpha.png">
							</a>
						</li>';
				}
				?>
			</ul>
			<ul class="flex-direction-nav">
				<li><a class="flex-prev" href="javascript:;">Previous</a></li>
				<li><a class="flex-next" href="javascript:;">Next</a></li>
			</ul>
			<ol id="bannerCtrl" class="flex-control-nav flex-control-paging">
				<?php 
				$n=1;
				foreach($ads as $ad){
					echo "<li><a>$n</a></li>";
					$n++;
				}
				?>
			</ol>
		</div>
		<!-- 轮播广告 end-->
		
		<!--页面主体-->
		<div class="main">			
			<!--推荐阅读和排行榜-->
			<div class="recomend-box">
				<p class="title">书库&nbsp;·&nbsp;推荐阅读></p>
				<!--推荐阅读-->
				<div class="recomendread">
					<div>
						<section id="gallery">
						    <div class="container_image">
						        <ul id="myRoundabout">
						            <li id="0"><a href="#" target="_blank" title="图片"><img src="img/tushu_1.jpg" alt='图片' style="border: 0"></a></li>   
						            <li id="1"><a href="#" target="_blank" title="图片"><img src="img/tushu_2.jpg" alt='图片' style="border: 0"></a></li>   
						            <li id="2"><a href="#" target="_blank" title="图片"><img src="img/tushu_3.jpg" alt='图片' style="border: 0"></a></li>   
						            <li id="3"><a href="#" target="_blank" title="图片"><img src="img/tushu_4.jpg" alt='图片' style="border: 0"></a></li>  
						            <li id="4"><a href="#" target="_blank" title="图片"><img src="img/tushu_5.jpg" alt='图片' style="border: 0"></a></li> 
						        </ul>
						    </div>
						</section>
					</div>
					<div class="book-introduce">
						<p class="bookname">基于生物质的环境友好材料</p>
						<p class="writer">作者：张建华著</p>
						<p class="bookinfo">《私·念念不忘》，中国首部私密文学主题书，由青春畅销作家九夜茴主编，并携手韩寒、桐华、辛夷坞、孙睿、春树、苏小懒、明前雨后等80后先锋作家共同打造，也成为2011年度最具重量级的收官之作。他们借《私》的写作平台.</p>
					</div>
				</div>
				<!--推荐阅读 end-->
				
				<!--排行榜-->
				<div class="recomendtop10">
					<div class="recomendtitle">
						<p>推荐排行榜</p>
					</div>
					<div class="ranklist">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
					</div>
				</div>
				<!--排行榜 end-->
				<div style="clear: both;"></div>
			</div>
			<!--推荐阅读和排行榜 end-->
			
			<div style="clear: both;"></div>
			
			<!--贡献作者推荐榜-->
			<div class="contribution-writer">
				<p class="title">文库&nbsp;·&nbsp;贡献作者推荐榜></p>
				<!--第一行-->
				<div class="writerblock frist">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-doc"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-xls"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-doc"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-ppt"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-pdf"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-txt"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				<!--第一行 end-->
				
				<!--第二行-->
				<div class="writerblock frist">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				<!--第二行 end-->
			</div>
			<!--贡献作者推荐榜 end-->	
			
			<div style="clear: both;"></div>
			
			<!--图库首页-->
			<div class="imagebanner">
				<p class="title">图库&nbsp;·&nbsp;美图推荐></p>
				<div class="imagebox">
					<?php
					$n=1;
					foreach($picsearchs as $search){
						if($n%5==0){
							echo '<a href="image-search.php?word='.$search->name.'">
									<div class="imagebg final" style="background-image: url('.$search->picurl.');">
										<div class="imageinfo"><p>'.$search->name.'</p></div>
									</div>
								</a>';
							if($n==5){
								echo '<div style="clear: both;"></div>';
							}
						}
						else{
							echo '<a href="image-search.php?word='.$search->name.'">
									<div class="imagebg" style="background-image: url('.$search->picurl.');">
										<div class="imageinfo"><p>'.$search->name.'</p></div>
									</div>
								</a>';
						}
						$n++;
						
					}
					?>
					
				</div>
			</div>
			<!--图库首页 end-->
		</div>
		<!--页面主体 end-->
		
		<!--右下角按钮-->
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
			<a class="elevator-weixin" style="cursor:pointer"></a>
			<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
			<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
			<a class="elevator-top" href="#"></a>
		</div>
		<!--右下角按钮 end-->
		
		<div style="clear: both;"></div>		
		<!--底部导航条-->
		<nav class="bottomnav">
			<div class="whiteline"></div>
			<p>
				<a>电子书友链</a>
				<a>QQ：54250413230</a>
				<a>版权声明</a>
				<a>意见反馈</a>
				<a>客服电话（0771-536780）</a>
			</p>
			<p>
				<a>单位编号：1101081827</a>
				<a>防城慕课网</a>
				<a>桂ICP证：060172号</a>
				<a>网络视听许可证：0110438号</a>
			</p>
			<p>Copyright &nbsp;1999-2012&nbsp;&nbsp;防城慕课</p>
		</nav>
		<!--底部导航条 end-->
	</body>
</html>
