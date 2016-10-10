<!DOCTYPE html>

<?php
require_once ("../../config.php");
global $USER;
global $DB;

//获取电子书的顶级分类
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");
//获取最新的书籍
$books = $DB->get_records_sql("select e.*,ea.name as authorname from mdl_ebook_my e
                                left join  mdl_ebook_author_my ea on e.authorid = ea.id
                                order by e.timecreated  desc  limit 0,6  ");
//推荐阅读
$recommends = $DB->get_records_sql("select em.*,er.*,ea.`name` as authorname from mdl_ebook_recommendlist_my er
                                     left join mdl_ebook_my em on er.ebookid = em.id
                                    left join mdl_ebook_author_my ea on em.authorid = ea.id");
//Start 更新推荐书目录
$recommendbooknames = array();
$recommendwriters = array();
$recommendbookinfos = array();
$recommendbookhrefs = array();//链接路径
for($i=1;$i<6;$i++){
    $recommendbooknames[]  = $recommends[$i]->name;
    $recommendwriters[]  = $recommends[$i]->authorname;
    $recommendbookinfos[]  = mb_substr($recommends[$i]->summary,0,146,"utf-8").'...';
	$recommendbookhrefs[]  = 'bookindex.php?bookid='.$recommends[$i]->ebookid;
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
//End 更新推荐书目录

//Start 热门排行榜
$weektime = time()-3600*24*7;//一周前
$monthtime = time()-3600*24*30;//一月前
//周记录
$weekranks = $DB->get_records_sql("select eh.contextid,eh.`name` as bookname,eh.rankcount as rank from mdl_ebook_hot_rank_my eh
									where eh.ranktype = 1 ");
//月记录
$monthranks = $DB->get_records_sql("select eh.contextid,eh.`name` as bookname,eh.rankcount as rank from mdl_ebook_hot_rank_my eh
									where eh.ranktype = 2 ");
//总记录
$totalranks = $DB->get_records_sql("select eh.contextid,eh.`name` as bookname,eh.rankcount as rank from mdl_ebook_hot_rank_my eh
									where eh.ranktype = 3 ");

$weekrankarray = array();//显示书名信息
$monthrankarray = array();
$totalrankarray = array();
$weekhrefarray = array();//链接路径
$monthhrefarray = array();
$totalhrefarray = array();
foreach($weekranks as $weekrank ){
	if($weekrank->contextid == 0){
		break;
	}
	$bookname = (strlen($weekrank->bookname) > 36)?mb_substr($weekrank->bookname,0,12,"utf-8").'...':$weekrank->bookname;
    $weekrankarray[] = '《'.$bookname.'》'.' -- '.$weekrank->rank;
	$weekhrefarray[] = 'bookindex.php?bookid='.$weekrank->contextid;
}
if(count($weekrankarray)<10){
    $i = 10 - count($weekrankarray);
    for($i;$i>0;$i--){
        $weekrankarray[] = ' ';
		$weekhrefarray[] = '#';
    }
}
foreach($monthranks as $monthrank ){
	if($monthrank->contextid == 0){
		break;
	}
	$bookname = (strlen($monthrank->bookname) > 36)?mb_substr($monthrank->bookname,0,12,"utf-8").'...':$monthrank->bookname;
    $monthrankarray[] = '《'.$bookname.'》'.' -- '.$monthrank->rank;
	$monthhrefarray[] = 'bookindex.php?bookid='.$monthrank->contextid;
}
if(count($monthrankarray)<10){
    $i = 10 - count($monthrankarray);
    for($i;$i>0;$i--){
        $monthrankarray[] = ' ';
		$monthhrefarray[] = '#';
    }
}
foreach($totalranks as $totalrank ){
	if($totalrank->contextid == 0){
		break;
	}
	$bookname = (strlen($totalrank->bookname) > 36)?mb_substr($totalrank->bookname,0,12,"utf-8").'...':$totalrank->bookname;
    $totalrankarray[] = '《'.$bookname.'》'.' -- '.$totalrank->rank;
	$totalhrefarray[] = 'bookindex.php?bookid='.$totalrank->contextid;
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

//Start 热门作者
$authorranks = $DB->get_records_sql("select er.authorid as id,er.authorname,er.rankcount as rank from mdl_ebook_author_rank_my er");
if(count($authorranks)<10){
	$i = 10 - count($authorranks);
	for($i;$i>0;$i--){
		$authorranks[] = '';
	}
}
//End 热门作者

//Start 评分榜
$scoretables = $DB->get_records_sql("select em.*,es.sumscore from mdl_ebook_my em
										left join  mdl_ebook_sumscore_my es on em.id = es.ebookid
										order by es.sumscore desc
										limit 0,10");
if(count($scoretables)<10){
	$i = 10 - count($scoretables);
	for($i;$i>0;$i--){
		$scoretables[] = '';
	}
}

//End 评分榜

?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>书库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroom.css" />
		<link rel="stylesheet" href="../css/bookroomallpage.css" />	
		<style>
		
		</style>
		
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/rank.js" ></script>

		<!--控制图片轮转js文件-->
		<script type="text/javascript" src="../js/Imagerotation/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="../js/Imagerotation/roundabout.js" ></script>
		<script type="text/javascript" src="../js/Imagerotation/roundabout_shapes.js" ></script>
		<script type="text/javascript" src="../js/Imagerotation/gallery_init.js" ></script>
		<!--控制图片轮转js文件 end-->

		<script>
            // 更新推荐书目录
            var bookname = new Array(<?php echo $recommendbooknameStr; ?>);//书名数组
            var writer = new Array(<?php echo $recommendwriterStr; ?>); //作者数组
            var bookinfo = new Array(<?php echo $recommendbookinfoStr; ?>); //书本介绍文字数组
			var bookhref = new Array(<?php echo $recommendbookhrefStr; ?>); //书本链接

            //热门排行榜
            var moothrank = new Array(<?php echo $monthrankStr; ?>); //月书单
            var weekrank = new Array(<?php echo $weekrankStr; ?>); //周书单
            var totalrank = new Array(<?php echo $totalrankStr; ?>); //总书单
            var moothrank_href = new Array(<?php echo $monthhrefStr; ?>); //月书单链接
            var weekrank_href = new Array(<?php echo $weekhrefStr; ?>); //周书单链接
            var totalrank_href = new Array(<?php echo $totalhrefStr; ?>); //总书单链接
		</script>

	</head>
	<body id="bookroom">
		<!--顶部导航-->
		<?php
			require_once ("../common/book_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			require_once ("../common/book_head_search.php");//书库搜索栏
		?>
		<!--顶部导航 end-->

		<!--书本分类-->
		<?php
			require_once ("../common/book_head_classify.php");//书库搜索栏
		?>
		<!--书本分类 end-->
		
		<!--页面主体-->
		<div class="main">
			<div class="upload">
				<p>最新上传</p>
			</div>
			
			<!--书单列表-->
			<div>
                <?php
                    if($books != null){
                        foreach($books as $book){
                            echo '<div class="book-block">
                                    <a href="bookindex.php?bookid='.$book->id.'"><img src="'.$book->pictrueurl.'" width="150" height="220" /></a>
                                    <div class="book-info-box">
                                        <a href="bookindex.php?bookid='.$book->id.'"><p class="bookname">'.$book->name.'</p></a>
                                        <a href="bookauthor.php?authorid='.$book->authorid.'" target="_blank"><p class="writer">作者：'.$book->authorname.'</p></a>
                                        <p class="bookinfo">'.mb_substr($book->summary,0,100,"utf-8").'...</p>
                                        <p>';
                            $tags = $DB->get_records_sql("select tm.id,tm.tagname from mdl_tag_link tl
                                                left join mdl_tag_my tm on tl.tagid = tm.id
                                                where tl.link_id = $book->id
                                                and tl.link_name = 'mdl_ebook_my'");
                            if($tags != null){
								$num = 0;
                                foreach($tags as $tag){
									if($num == 3){
										break;
									}
                                    echo '<a class="tips">'.$tag->tagname.'</a>';
									$num++;
                                }
                            }
                            echo '</p>
                                    </div>
                                </div>';
                        }
                    }
                ?>
			</div>
			<!--书单列表 end-->
			
			<div style="clear: both;"></div>
			
			<!--更多-->
			<div class="morebook">
				<p><img src="../img/gengduo.png"></p>
				<div class="x-line"></div>
				<div class="more-btn"><a></a></div>
				<div class="x-line"></div>
			</div>
			<!--更多 end-->
			
			<!--推荐阅读和排行榜-->
			<div class="recomend-box">
				<!--推荐阅读-->
				<div class="recomendread">
					<div class="title">
						<p>&nbsp;&nbsp;推荐阅读</p>
					</div>
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
					<div class="title">
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
											<a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
										</div>';
									}elseif($no==10){
										echo ' <div class="ranklist-block">
											<a class="ranknum top10">'.$no.'</a>
											<a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
										</div>';
									}else{
										echo ' <div class="ranklist-block">
											<a class="ranknum">'.$no.'</a>
											<a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$bookname.'</a>
										</div>';
									}
									$no++;
								}
							}
                        ?>
					</div>
				</div>
				<!--排行榜 end-->
			</div>
			<!--推荐阅读和排行榜 end-->
			
			<div style="clear: both;"></div>
			
			<!--热门作者榜、评分榜、热门排行榜-->
			<div class="rank-box">
				<!--热门作者榜-->
				<div class="popular-writer">
					<div class="title">热门作者榜</div>
					<div class="ranklist">
						<?php
							if($authorranks !=null){
								$no = 1;
								foreach($authorranks as $authorrank){
									if($no<4){
										echo '<div class="ranklist-block">
													<div class="w-num"><a class="ranknum top3">'.$no.'</a></div>
													<div class="w-name">';
										if($authorrank->id == 0){
											echo '<a class="writername" href="#" >'.$authorrank->authorname.$authorrank->rank.'</a>';
										}else{
											echo '<a class="writername" href="bookauthor.php?authorid='.$authorrank->id.'" target="_blank" >'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a>';
										}
										echo '</div>
												</div>';
									}elseif($no==10){
										echo ' <div class="ranklist-block">
													<div class="w-num"><a class="ranknum top10">'.$no.'</a></div>
													<div class="w-name">';
										if($authorrank->id == 0){
											echo '<a class="writername" href="#" >'.$authorrank->authorname.$authorrank->rank.'</a>';
										}else{
											echo '<a class="writername" href="bookauthor.php?authorid='.$authorrank->id.'" target="_blank" >'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a>';
										}
										echo '</div>
												</div>';
									}else{
										echo ' <div class="ranklist-block">
													<div class="w-num"><a class="ranknum">'.$no.'</a></div>
													<div class="w-name">';
										if($authorrank->id == 0){
											echo '<a class="writername" href="#" >'.$authorrank->authorname.$authorrank->rank.'</a>';
										}else{
											echo '<a class="writername" href="bookauthor.php?authorid='.$authorrank->id.'" target="_blank" >'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a>';
										}
										echo '</div>
												</div>';
									}
									$no++;
								}
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
								if($no<4){
									echo '<div class="ranklist-block">
											<a class="ranknum top3">'.$no.'</a>';
								}elseif($no==10){
									echo '<div class="ranklist-block">
												<a class="ranknum top10">'.$no.'</a>';
								}
								else{
									echo '<div class="ranklist-block">
											<a class="ranknum">'.$no.'</a>';
								}
								if($scoretable != ''){
									$score = ($scoretable->sumscore == '')?0:$scoretable->sumscore;
									$bookname = (strlen($scoretable->name) > 36 )?mb_substr($scoretable->name,0,12,"utf-8").'...':$scoretable->name;
									echo '<a class="bookname" href="bookindex.php?bookid='.$scoretable->id.'" >《'.$bookname.'》&nbsp;--&nbsp;'.$score.'分</a>';
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
