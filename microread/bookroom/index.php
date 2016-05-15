<!DOCTYPE html>

<?php
require_once ("../../config.php");
global $USER;
global $DB;

$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");//获取电子书的顶级分类
$books = $DB->get_records_sql("select e.*,ea.name as authorname from mdl_ebook_my e
                                left join  mdl_ebook_author_my ea on e.authorid = ea.id
                                order by e.timecreated  desc  limit 0,6  ");//获取最新的书籍

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
    $recommendbookinfos[]  = $recommends[$i]->summary;
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

//$weekranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_microread_log m
//                                    left join mdl_ebook_my e on m.contextid = e.id
//                                    where  m.target = 1 and m.action = 'view' and m.timecreated> $weektime
//                                    group by m.contextid
//                                    order by rank desc
//                                    limit 0,10");
$weekranks = $DB->get_records_sql("select eh.contextid,eh.`name` as bookname,eh.rankcount as rank from mdl_ebook_hot_rank_my eh
									where eh.ranktype = 1 ");
//$monthranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_microread_log m
//                                    left join mdl_ebook_my e on m.contextid = e.id
//                                    where  m.target = 1 and m.action = 'view' and m.timecreated> $monthtime
//                                    group by m.contextid
//                                    order by rank desc
//                                    limit 0,10");
$monthranks = $DB->get_records_sql("select eh.contextid,eh.`name` as bookname,eh.rankcount as rank from mdl_ebook_hot_rank_my eh
									where eh.ranktype = 2 ");
//$totalranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_microread_log m
//                                    left join mdl_ebook_my e on m.contextid = e.id
//                                    where  m.target = 1 and m.action = 'view'
//                                    group by m.contextid
//                                    order by rank desc
//                                    limit 0,10");
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
    $weekrankarray[] = '《'.$weekrank->bookname.'》'.' -- '.$weekrank->rank;
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
    $monthrankarray[] = '《'.$monthrank->bookname.'》'.' -- '.$monthrank->rank;
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
    $totalrankarray[] = '《'.$totalrank->bookname.'》'.' -- '.$totalrank->rank;
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
		<script>
			//搜索选项下拉框
			$(document).ready(function() {
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('<span class="caret"></span>');
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
				window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value;
			}
		</script>
		<script>
		$(document).ready(function(){
			//聊天室 START 20160314
			//适配不同大小偏移值
			var winW=$(window).width();
			var winH=$(window).height();
			var leftval = (winW-900)/2;
			var topval = (winH-600)/3;
			$('.chat-box').css({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
			//适配不同大小偏移值 end
			var chatbox=false;
			$('.elevator-weixin').click(function(){
				if(chatbox==false){
					$('.chat-box1').append('<iframe src="<?php echo $CFG->wwwroot;?>/chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
					chatbox=true;
				}
				$('.chat-box1').show();
			})
			$('#chat-close').click(function(){
				$('.chat-box1').hide();
				//alert("关闭的top: " +$('.chat-box').offset().top);
			})
			//聊天室 End
			//收藏按钮
			$('#collection-btn').click(function()
			{
				$.ajax({
					url: "<?php echo $CFG->wwwroot;?>/privatecenter/mycollection/collectionpage.php",
					data: {mytitle: document.title, myurl: window.location.href },
					success: function(msg){
						if(msg=='1'){
							alert('收藏成功，可去个人中心查看')
						}
						else{
							msg=='2' ? alert('您已经收藏过了，请去个人中心查看收藏结果') :alert('收藏失败');
						}
					}
				});
			});
			//点赞按钮
			$('#like-btn').click(function()
			{
				$.ajax({
					url: "<?php echo $CFG->wwwroot;?>/like/courselike.php",
					data: {mytitle: document.title, myurl: window.location.href },
					success: function(msg){
						// alert(msg);
						if(msg=='1'){
							alert('点赞成功')
						}
						else{
							msg=='2' ? alert('你已经点赞了，不能再次点赞') :alert('点赞失败');
						}
					}
				});
			});
			//笔记20160314
			var note_personal = false
			$('#mynote-btn').click(function(){
				if(note_personal == false)
				{
					$('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_personal.php" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
					note_personal = true;
				}

				$('.chat-box2').show();

			})
			//笔记
			$('#chat-close2').click(function(){
				$('.chat-box2').hide();
			})

		});
		</script>
	</head>
	<body id="bookroom">
		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<div class="a-box">
					<a class="nav-a frist"  href="<?php echo $CFG->wwwroot; ?>">首页</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/mod/forum/view.php?id=1">微阅</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/course/index.php">微课</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/privatecenter/index.php?class=zhibo">直播</a>
					<?php if($USER->id==0)echo '<a class="nav-a login" href="'.$CFG->wwwroot.'/login/index.php"><img src="../img/denglu.png"></a>';?>
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
			<a href="index.php"><img  src="../img/shuku_logo.png"/></a>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">全部<span class="caret"></span></button>
						<ul id="searchtype" class="dropdown-menu">
							<li><a id="bookall" href="#">全部</a></li>
							<li role="separator" class="divider"></li>
							<li><a id="booktitle" href="#">标题</a></li>
							<li role="separator" class="divider"></li>
							<li><a id="bookauthor" href="#">作者</a></li>
							<li role="separator" class="divider"></li>
							<li><a id="bookuploader" href="#">上传者</a></li>
						</ul>
					</div><!-- /btn-group -->
					<input id="searchParam" type="text" class="form-control" >
				</div><!-- /input-group -->
				<button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>

				<!--			    <div class="radio">-->
				<!--			  		<label>-->
				<!--			    		<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">-->
				<!--			    		全部字段-->
				<!--			  		</label>-->
				<!--			  		<label>-->
				<!--			    		<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">-->
				<!--			    		标题-->
				<!--			  		</label>-->
				<!--			  		<label>-->
				<!--			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">-->
				<!--			    		主讲人-->
				<!--			  		</label>-->
				<!--				</div>-->

			</div>
			<!--搜索框组 end-->
		</div>
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
				if($bookclasses != null){
					foreach($bookclasses as $bookclass){
						echo '<div class="line"></div>
											<a href="classify.php?bookclassid='.$bookclass->id.'" class="kinds">'.$bookclass->name.'</a>';
					}
				}
				?>
			</div>
		</div>
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
                                        <p class="writer">作者：'.$book->authorname.'</p>
                                        <p class="bookinfo">'.substr($book->summary,0,270).'...'.'</p>
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
				<div class="more-btn"><a>更多</a></div>
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
                                if($no<4){
                                    echo ' <div class="ranklist-block">
                                        <a class="ranknum top3">'.$no.'</a>
                                        <a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$recommend->name.'</a>
                                    </div>';
                                }elseif($no==10){
									echo ' <div class="ranklist-block">
                                        <a class="ranknum top10">'.$no.'</a>
                                        <a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$recommend->name.'</a>
                                    </div>';
								}else{
                                    echo ' <div class="ranklist-block">
                                        <a class="ranknum">'.$no.'</a>
                                        <a class="bookname" href="bookindex.php?bookid='.$recommend->ebookid.'" >'.$recommend->name.'</a>
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
													<div class="w-name"><a class="writername" href="#">'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a></div>
												</div>';
									}elseif($no==10){
										echo ' <div class="ranklist-block">
													<div class="w-num"><a class="ranknum top10">'.$no.'</a></div>
													<div class="w-name"><a class="writername" href="#">'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a></div>
												</div>';
									}else{
										echo ' <div class="ranklist-block">
													<div class="w-num"><a class="ranknum">'.$no.'</a></div>
													<div class="w-name"><a class="writername" href="#">'.$authorrank->authorname.'&nbsp;--&nbsp;'.$authorrank->rank.'</a></div>
												</div>';
									}
									$no++;
								}
							}
						?>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
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
											<a class="ranknum top3">'.$no.'</a>
											<a class="bookname" href="bookindex.php?bookid='.$scoretable->id.'" >'.$scoretable->name.'&nbsp;--&nbsp;'.$scoretable->sumscore.'分</a>
										</div>';
								}elseif($no==10){
									echo '<div class="ranklist-block">
												<a class="ranknum top10">'.$no.'</a>
												<a class="bookname" href="bookindex.php?bookid='.$scoretable->id.'" >'.$scoretable->name.'&nbsp;--&nbsp;'.$scoretable->sumscore.'分</a>
											</div>';
								}
								else{
									echo '<div class="ranklist-block">
												<a class="ranknum">'.$no.'</a>
												<a class="bookname" href="bookindex.php?bookid='.$scoretable->id.'" >'.$scoretable->name.'&nbsp;--&nbsp;'.$scoretable->sumscore.'分</a>
											</div>';
								}
								$no++;
							}
						?>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
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
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门排行榜 end-->
			</div>
			<!--热门作者榜、评分榜、热门排行榜 end-->
		</div>
		<!--页面主体 end-->
		
		<!--右下角按钮-->
		<?php 
			if(isloggedin()){
				echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
					<a class="elevator-weixin" style="cursor:pointer"></a>
					<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
					<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
					<a class="elevator-top" href="#"></a>
					</div>';
			}
			else{
				echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-top" href="#"></a>
					</div>';
			}
			?>
		
		<div class="chat-box chat-box1">
		<div class="chat-head">
			<p>聊天室</p>
			<p id="chat-close" class="close">x</p>
		</div>
		</div>
		<div class="chat-box chat-box2">
			<div class="chat-head">
				<p>个人笔记</p>
				<p id="chat-close2" class="close">x</p>
			</div>
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
