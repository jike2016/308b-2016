<?php
require_once ("../config.php");
global $USER;
//搜索广告栏
global $DB;
$ads = $DB->get_records_sql('select * from mdl_microread_indexad_my where picurl != "" ORDER BY id');
$picsearchs = $DB->get_records_sql('select * from mdl_pic_recommended_search');

$recommends = $DB->get_records_sql("select em.*,er.*,ea.`name` as authorname from mdl_ebook_recommendlist_my er
                                     left join mdl_ebook_my em on er.ebookid = em.id
                                    left join mdl_ebook_author_my ea on em.authorid = ea.id");
//Start 书库 推荐阅读
$recommendbooknames = array();
$recommendwriters = array();
$recommendbookinfos = array();
$recommendbookhrefs = array();//链接路径
for($i=1;$i<6;$i++){
	$recommendbooknames[]  = $recommends[$i]->name;
	$recommendwriters[]  = $recommends[$i]->authorname;
	$recommendbookinfos[]  = mb_substr($recommends[$i]->summary,0,146,"utf-8").'...';
	$recommendbookhrefs[]  = 'bookroom/bookindex.php?bookid='.$recommends[$i]->ebookid;
}
$recommendbooknameStr = '"';
$recommendbooknameStr .= implode('","',$recommendbooknames);
$recommendbooknameStr .= '"';

$recommendwriterStr = '"';
$recommendwriterStr .= implode('","',$recommendwriters);
$recommendwriterStr .= '"';

$recommendbookinfoStr = '"';
$recommendbookinfoStr .= implode('","',$recommendbookinfos);
$recommendbookinfoStr .= '"';

$recommendbookhrefStr = '"';
$recommendbookhrefStr .= implode('","',$recommendbookhrefs);
$recommendbookhrefStr .= '"';
//End 书库 推荐阅读

//Start 获取贡献者推荐
$doccontributorsrecomends = $DB->get_records_sql("select dr.*,u.firstname as contribuname from mdl_doc_recommend_authorlist_my dr
													left join mdl_user u on dr.userid = u.id");
//End 获取贡献者推荐


/** Start 截取用户头像字符串*/
function getUserIcon($userid)
{
	global $OUTPUT;
	global $DB;
	$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
	$str1 = $OUTPUT->user_picture($user,array('link' => false,'visibletoscreenreaders' => false));
	$str=substr($str1,10);//去除前面
	$n=strpos($str,'"');//寻找位置
	if ($n) $str=substr($str,0,$n);//删除后面
	return $str;
}
/** End 截取用户头像字符串*/

/** Start 文件类型判断 */
function imagechoise($type){
	$type = strtolower($type);
	$doctype = '';
	switch($type){
		case '.txt':
			$doctype = 'ic-txt';
			break;
		case '.pdf':
			$doctype = 'ic-pdf';
			break;
		case '.doc':
		case '.docx':
			$doctype = 'ic-doc';
			break;
		case '.xls':
		case '.xlsx':
			$doctype = 'ic-xls';
			break;
		case '.ppt':
		case '.pptx':
			$doctype = 'ic-ppt';
			break;
	}
	return $doctype;
}
/** End  文件类型判断 */

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

		<!-- 书库 推荐阅读 -->
		<script>
			var bookname = new Array(<?php echo $recommendbooknameStr; ?>);//书名数组
			var writer = new Array(<?php echo $recommendwriterStr; ?>); //作者数组
			var bookinfo = new Array(<?php echo $recommendbookinfoStr; ?>); //书本介绍文字数组
			var bookhref = new Array(<?php echo $recommendbookhrefStr; ?>); //书本链接
		</script>
		<!-- 书库 推荐阅读 -->

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
			//回车事件
			document.onkeydown = function (e) {
				var theEvent = window.event || e;
				var code = theEvent.keyCode || theEvent.which;
				if ( $('#searchParam').val() != '' && code == 13) {
					$("#search_btn").click();
				}
			}
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
		<?php
			require_once ("common/micro_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
		?>
		
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
			    <button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
			</div>
			<!--搜索框组 end-->
		</div>
		<!--顶部导航 end-->
		
		<!--书本分类-->
		<div class="bookclassified">
			<div class="bookclassified-center">	
				<!--<a href="index.php" class="kinds">首页</a>	-->		
				<div class="line"></div>
				<a href="bookroom/" class="kinds">书库</a>
				<div class="line"></div>
				<a href="docroom/" class="kinds">文库</a>
				<div class="line"></div>
				<a href="picroom" class="kinds" target="_blank">图库</a>
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
									<?php
										if($recommends!=null){
											for($i=1;$i<=5;$i++){
												echo ' <li id="'.($i-1).'"><a href="bookindex.php?bookid='.$recommends[$i]->ebookid.'" target="_blank" title="图片"> <img src="'.$recommends[$i]->pictrueurl.'" alt=\'图片\' style="border: 0"  ></a></li>';
											}
										}
									?>
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
						<?php
							if($recommends !=null){
								$no = 1;
								foreach($recommends as $recommend){
									$bookname = (strlen($recommend->name) > 36)?mb_substr($recommend->name,0,12,"utf-8").'...':$recommend->name;
									$bookname = '《'.$bookname.'》';
									if($no<4){
										echo ' <div class="ranklist-block">
												<a class="ranknum top3">'.$no.'</a>
												<a class="bookname" href="bookroom/bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
											</div>';
									}elseif($no==10){
										echo ' <div class="ranklist-block">
												<a class="ranknum top10">'.$no.'</a>
												<a class="bookname" href="bookroom/bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
											</div>';
									}else{
										echo ' <div class="ranklist-block">
												<a class="ranknum">'.$no.'</a>
												<a class="bookname" href="bookroom/bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
											</div>';
									}
									$no++;
								}
							}
						?>
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
				<?php
					$index = 0;
					foreach($doccontributorsrecomends as $doccontributorsrecomend){
						//统计当前贡献者的文献数
						$doccount = $DB->get_record_sql("select count(1) as docnum from mdl_doc_my dm where dm.uploaderid = $doccontributorsrecomend->userid");
						if(($index%4)==0){
							echo '<div class="writerblock frist">';
						}else{
							echo '<div class="writerblock">';
						}
						echo '<a href="docroom/doccontributor.php?contributorid='.$doccontributorsrecomend->userid.'">
									<div class="userinfo-box">
										<div class="userimg">
											<img src="'.getUserIcon($doccontributorsrecomend->userid).'" width="64" height="64"/>
										</div>
										<div class="userinfo">
											<p class="name">'.$doccontributorsrecomend->contribuname	.'</p>
											<p class="articlenum">'.$doccount->docnum.'</p>
											<p class="articlenumw">篇文档</p>
										</div>
									</div>
									</a>
									<div class="articlelist">';
						//获取其3篇文档
						$docids = array();
						$docids[] = $doccontributorsrecomend->docid1;
						$docids[] = $doccontributorsrecomend->docid2;
						$docids[] = $doccontributorsrecomend->docid3;
						for($i=0;$i<3;$i++){
							if($docids[$i] && $docids[$i] != -1){
								//获取文档信息
								$doc1 = $DB->get_record_sql("select dm.*,ds.sumscore from mdl_doc_my dm
																left join mdl_doc_sumscore_my ds on dm.id = ds.docid
																where dm.id = $docids[$i] ");
								if($doc1){
									$doctype = imagechoise($doc1->suffix);
									echo '<a href="#">
											<p class="pa">
												<a class="ca" href="docroom/onlineread.php?docid='.$doc1->id.'"><span class="ic '.$doctype.'"></span>&nbsp;'.$doc1->name.'</a>
												<span class="score">';
									echo ($doc1->sumscore=="")?0:($doc1->sumscore);
									echo '分</span>
											</p>
										</a>';
								}
							}
						}
						echo'
									</div>
								</div>';
						$index++;
					}
				?>
				<!--第一行 end-->

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
							echo '<a href="picroom/image-search.php?word='.$search->name.'">
									<div class="imagebg final" style="background-image: url('.$search->picurl.');">
										<div class="imageinfo"><p>'.$search->name.'</p></div>
									</div>
								</a>';
							if($n==5){
								echo '<div style="clear: both;"></div>';
							}
						}
						else{
							echo '<a href="picroom/image-search.php?word='.$search->name.'">
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

		<!--页面右下角按钮 Start-->
		<?php
			require_once ("common/all_note_chat.php");//右下角链接：笔记、聊天、收藏、、、、
		?>
		<!--页面右下角按钮 end-->
		
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
