<?php
//文库首页
require_once ("../../config.php");
global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

//获取推荐分类
$doccategoryrecomends = $DB->get_records_sql("select dc.id as `index`,dcm.id as recomendscategoryid,dcm.`name` as categoryname,dc.docid,dm.* from mdl_doc_category_recommend_my dc
												left join mdl_doc_categories_my dcm on dc.categoryid = dcm.id
												left join mdl_doc_my dm on dc.docid = dm.id");
//获取贡献者推荐
$doccontributorsrecomends = $DB->get_records_sql("select dr.*,u.firstname as contribuname from mdl_doc_recommend_authorlist_my dr
													left join mdl_user u on dr.userid = u.id");
//Start 热门贡献榜
$doccontributorslists = $DB->get_records_sql("select dr.* from mdl_doc_contributor_rank_my dr ");
if(count($doccontributorslists)<10){
	$i = 10 - count($doccontributorslists);
	for($i;$i>0;$i--){
		$doccontributorslists[] = '';
	}
}
//End 热门贡献榜

//Start 热门排行榜
$weektime = time()-3600*24*7;//一周前
$monthtime = time()-3600*24*30;//一月前

$weekranks = $DB->get_records_sql("select dh.contextid,dh.rankcount,dh.`name` as docname,dh.suffix as doctype from mdl_doc_hot_rank_my dh
									where dh.ranktype = 1 ");
$monthranks = $DB->get_records_sql("select dh.contextid,dh.rankcount,dh.`name` as docname,dh.suffix as doctype from mdl_doc_hot_rank_my dh
									where dh.ranktype = 2 ");
$totalranks = $DB->get_records_sql("select dh.contextid,dh.rankcount,dh.`name` as docname,dh.suffix as doctype from mdl_doc_hot_rank_my dh
									where dh.ranktype = 3 ");
$weekrankarray = array();//显示书名信息
$monthrankarray = array();
$totalrankarray = array();
$weekhrefarray = array();//链接路径
$monthhrefarray = array();
$totalhrefarray = array();

foreach($weekranks as $weekrank ){
	if($weekrank->contextid != 0){
		$docnamelen = strlen($weekrank->docname);
		if($docnamelen > 36 ){
			$docname = mb_substr($weekrank->docname,0,12,"utf-8").'...';
		}else{
			$docname = $weekrank->docname.$weekrank->doctype;
		}
		$weekrankarray[] = $docname.' -- '.$weekrank->rankcount;
		$weekhrefarray[] = 'onlineread.php?docid='.$weekrank->contextid;
	}
}
if(count($weekrankarray)<10){
	$i = 10 - count($weekrankarray);
	for($i;$i>0;$i--){
		$weekrankarray[] = ' ';
		$weekhrefarray[] = '#';
	}
}
foreach($monthranks as $monthrank ){
	if($monthrank->contextid != 0) {
		$docnamelen = strlen($monthrank->docname);
		if($docnamelen > 36 ){
			$docname = mb_substr($monthrank->docname,0,12,"utf-8").'...';
		}else{
			$docname = $monthrank->docname.$monthrank->doctype;
		}
		$monthrankarray[] = $docname. ' -- ' . $monthrank->rankcount;
		$monthhrefarray[] = 'onlineread.php?docid=' . $monthrank->contextid;
	}
}
if(count($monthrankarray)<10){
	$i = 10 - count($monthrankarray);
	for($i;$i>0;$i--){
		$monthrankarray[] = ' ';
		$monthhrefarray[] = '#';
	}
}
foreach($totalranks as $totalrank ){
	if($totalrank->contextid != 0){
		$docnamelen = strlen($totalrank->docname);
		if($docnamelen > 36 ){
			$docname = mb_substr($totalrank->docname,0,12,"utf-8").'...';
		}else{
			$docname = $totalrank->docname.$totalrank->doctype;
		}
		$totalrankarray[] = $docname.' -- '.$totalrank->rankcount;
		$totalhrefarray[] = 'onlineread.php?docid='.$totalrank->contextid;
	}
}
if(count($totalrankarray)<10){
	$i = 10 - count($totalrankarray);
	for($i;$i>0;$i--){
		$totalrankarray[] = ' ';
		$totalhrefarray[] = '#';
	}
}

$weekrankStr = '"';
$weekrankStr .= implode('","',$weekrankarray);
$weekrankStr .= '"';
$weekhrefStr = '"';
$weekhrefStr .= implode('","',$weekhrefarray);
$weekhrefStr .= '"';

$monthrankStr = '"';
$monthrankStr .= implode('","',$monthrankarray);
$monthrankStr .= '"';
$monthhrefStr = '"';
$monthhrefStr .= implode('","',$monthhrefarray);
$monthhrefStr .= '"';

$totalrankStr = '"';
$totalrankStr .= implode('","',$totalrankarray);
$totalrankStr .= '"';
$totalhrefStr = '"';
$totalhrefStr .= implode('","',$totalhrefarray);
$totalhrefStr .= '"';

//End 热门排行榜

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
/** End 文件类型判断 */

/** Start 评分榜 */
$scoretables = $DB->get_records_sql("select dm.*,ds.sumscore from mdl_doc_my dm
							left join mdl_doc_sumscore_my ds on dm.id = ds.docid
							order by ds.sumscore desc
							limit 0,10");
if(count($scoretables)<10){
	$i = 10 - count($scoretables);
	for($i;$i>0;$i--){
		$scoretables[] = '';
	}
}
/** End 评分榜 */

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>文库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/docindex.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/rank.js" ></script>
		<script>
			var moothrank = new Array(<?php echo $monthrankStr; ?>); //月书单
			var moothrank_href = new Array(<?php echo $monthhrefStr; ?>); //月书单链接

			var weekrank = new Array(<?php echo $weekrankStr; ?>); //周书单
			var weekrank_href = new Array(<?php echo $weekhrefStr; ?>); //周书单链接

			var totalrank = new Array(<?php echo $totalrankStr; ?>); //总书单
			var totalrank_href = new Array(<?php echo $totalhrefStr; ?>); //总书单
		</script>

	</head>
	<body id="articleindex">
		<!--顶部导航-->
		<?php
			require_once ("../common/doc_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			require_once ("../common/doc_head_search.php");//文库搜索栏
		?>
		<!--顶部导航 end-->
		
		<!--书本分类-->
		<div class="bookclassified">
			<div class="bookclassified-center">
				
				<!-- 书本分类按钮 -->
<!--				<div class="btn-group" style="float: left;">-->
<!--				  	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--				  		<img src="../img/tushuFenlei.png">-->
<!--				  	</a>-->
<!--				  	<ul class="dropdown-menu">-->
<!--				    	<li><a href="#">现代</a></li>-->
<!--				    	<li role="separator" class="divider"></li>-->
<!--				    	<li><a href="#">军事</a></li>-->
<!--				    	<li role="separator" class="divider"></li>-->
<!--				    	<li><a href="#">战争</a></li>-->
<!--				    	<li role="separator" class="divider"></li>-->
<!--				    	<li><a href="#">科技</a></li>-->
<!--				  	</ul>-->
<!--				</div>-->
				<!-- 书本分类按钮 end-->
				<?php
					if($docclasses != null){
						foreach($docclasses as $docclass){
							echo '<div class="line"></div>
									<a href="classify.php?docclassid='.$docclass->id.'" class="kinds">'.$docclass->name.'</a>';
						}
					}
				?>
			</div>
		</div>
		<!--书本分类 end-->
		
		<!--页面主体-->
		<div class="main">
			<!--权威发布-->
			<div class="authority-release">
				<?php
					$index = 0;
					foreach($doccategoryrecomends as $doccategoryrecomend){
						if($index%4==0){
							echo '<p class="title"><a href="classify.php?docclassid='.$doccategoryrecomend->recomendscategoryid.'"  style="text-decoration:none;" >'.$doccategoryrecomend->categoryname.'></a><a href="classify.php?docclassid='.$doccategoryrecomend->recomendscategoryid.'"><span style="float:right;font-size:16px;margin-top:14px;">更多》</span></a></p>
							<!--第一行-->
							<div class="banner">';
						}
						if($index%4==0) {
							if($doccategoryrecomend->docid != -1){
								echo '<div class="articleblock frist ">
											<a href="onlineread.php?docid='.$doccategoryrecomend->id.'"><img src="'.$doccategoryrecomend->pictrueurl.'" width="272" height="125" /></a>
											<div>
												<a href="onlineread.php?docid='.$doccategoryrecomend->id.'">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
							}else{//如果没设置文档
								echo '<div class="articleblock frist ">
											<a href=""><img src="'.$doccategoryrecomend->pictrueurl.'" width="272" height="125" /></a>
											<div>
												<a href="">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
							}

						}else{
							if($doccategoryrecomend->docid != -1){
								echo '<div class="articleblock">
											<a href="onlineread.php?docid='.$doccategoryrecomend->id.'"><img src="'.$doccategoryrecomend->pictrueurl.'"  width="272" height="125" /></a>
											<div>
												<a href="onlineread.php?docid='.$doccategoryrecomend->id.'">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
							}else{//如果没设置文档
								echo '<div class="articleblock">
											<a href=""><img src="'.$doccategoryrecomend->pictrueurl.'"  width="272" height="125" /></a>
											<div>
												<a href="">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
							}
						}
						if((($index+1)%4)==0){
							echo '<div style="clear: both;"></div>
									</div>
									<!--第一行 end-->';
						}
						$index++;
					}

				?>
				<div style="clear: both;"></div>
			</div>
			<!--权威发布 end-->
			
			<!--贡献作者推荐榜-->
			<div class="contribution-writer">
				<p class="title">贡献作者推荐榜></p>
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
						echo '<a href="doccontributor.php?contributorid='.$doccontributorsrecomend->userid.'">
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
											<a class="ca" href="onlineread.php?docid='.$doc1->id.'"><span class="ic '.$doctype.'"></span>&nbsp;'.$doc1->name.'</a>
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
			
			<!--热门作者榜、评分榜、热门排行榜-->
			<div class="rank-box">
				<!--热门作者榜-->
				<div class="popular-writer">
					<div class="title">热门贡献榜</div>
					<div class="ranklist">
						<?php
							$no = 1;
							foreach($doccontributorslists as $doccontributorslist){
								if($no<4){
									echo '<div class="ranklist-block">
												<div class="w-num"><a class="ranknum top3">'.$no.'</a></div>
												<div class="w-name">';
									if($doccontributorslist->uploaderid == 0){
										echo '<a class="writername" href="#" ></a>';
									}else{
										echo '<a class="writername" href="doccontributor.php?contributorid='.$doccontributorslist->uploaderid.'" target="_blank" >'.$doccontributorslist->uploadusername.'&nbsp;--&nbsp;'.$doccontributorslist->rankcount.'</a>';
									}
									echo '</div>
											</div>';
								}elseif($no==10){
									echo '<div class="ranklist-block">
												<div class="w-num"><a class="ranknum top10">'.$no.'</a></div>
												<div class="w-name">';
									if($doccontributorslist->uploaderid == 0){
										echo '<a class="writername" href="#" ></a>';
									}else{
										echo '<a class="writername" href="doccontributor.php?contributorid='.$doccontributorslist->uploaderid.'" target="_blank" >'.$doccontributorslist->uploadusername.'&nbsp;--&nbsp;'.$doccontributorslist->rankcount.'</a>';
									}
									echo '</div>
											</div>';
								}else{
									echo '<div class="ranklist-block">
												<div class="w-num"><a class="ranknum">'.$no.'</a></div>
												<div class="w-name">';
									if($doccontributorslist->uploaderid == 0){
										echo '<a class="writername" href="#" ></a>';
									}else{
										echo '<a class="writername" href="doccontributor.php?contributorid='.$doccontributorslist->uploaderid.'" target="_blank" >'.$doccontributorslist->uploadusername.'&nbsp;--&nbsp;'.$doccontributorslist->rankcount.'</a>';
									}
									echo '</div>
											</div>';
								}
								$no++;
							}
						?>
					</div>
					<div class="more-box"><a class="more" href="#"></a></div>
				</div>
				<!--热门作者榜 end-->
				
				<!--评分榜-->
				<div class="score">
					<div class="title">评分榜</div>
					<div class="ranklist">
						<?php
							$no = 1;
							foreach($scoretables as $scoretable){
								$doctype = imagechoise($scoretable->suffix);
								if($no<4){
									echo '<div class="ranklist-block">
											<a class="ranknum top3">'.$no.'</a>';
								}elseif($no==10){
									echo '<div class="ranklist-block">
											<a class="ranknum top10">'.$no.'</a>';
								}else{
									echo '<div class="ranklist-block">
											<a class="ranknum">'.$no.'</a>';
								}
								if($scoretable != ''){
									$score = ($scoretable->sumscore == '')?0:$scoretable->sumscore;//分数处理
									$docnamelen = strlen($scoretable->name);
									if($docnamelen > 36 ){
										$docname = mb_substr($scoretable->name,0,12,"utf-8").'...';
									}else{
										$docname = $scoretable->name.$scoretable->suffix;
									}
									echo '<a class="bookname" href="onlineread.php?docid='.$scoretable->id.'" ><span class="ic '.$doctype.'"></span>&nbsp;'.$docname.'&nbsp;--&nbsp;'.$score.'分</a>';
								}else{
									echo '<a class="bookname" href="" ></a>';
								}

								echo '</div>';
								$no++;
							}
						?>
					</div>
					<div class="more-box"><a class="more" href="#"></a></div>
				</div>
				<!--评分榜 end-->
				
				<!--热门排行榜-->
				<div class="top-charts">
					<div class="title hotrank">热门排行榜</div>
					<div class="title week">周</div>
					<div class="title mooth">月</div>					
					<div class="title total">总</div>
					<div style="clear: both;"></div>
					<div class="ranklist final">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname"></a>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#"></a></div>
				</div>
				<!--热门排行榜 end-->
			</div>
			<!--热门作者榜、评分榜、热门排行榜 end-->			
		</div>
		<!--页面主体 end-->

		<!--页面右下角按钮 Start-->
		<?php
			require_once ("../common/all_note_chat.php");//右下角链接：笔记、聊天、收藏、、、、
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
