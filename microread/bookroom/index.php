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
for($i=1;$i<6;$i++){
    $recommendbooknames[]  = $recommends[$i]->name;
    $recommendwriters[]  = $recommends[$i]->authorname;
    $recommendbookinfos[]  = $recommends[$i]->summary;
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
//End 更新推荐书目录

//Start 热门排行榜
$weektime = time()-3600*24*7;//一周前
$monthtime = time()-3600*24*30;//一月前

$weekranks = $DB->get_records_sql("select count(1) as rank ,m.target,m.contextid,e.name as bookname from mdl_microread_log m
                                    left join mdl_ebook_my e on m.contextid = e.id
                                    where  m.target = 1 and m.action = 'view' and m.timecreated> $weektime
                                    group by m.contextid
                                    order by rank desc
                                    limit 0,10");
$monthranks = $DB->get_records_sql("select count(1) as rank ,m.target,m.contextid,e.name as bookname from mdl_microread_log m
                                    left join mdl_ebook_my e on m.contextid = e.id
                                    where  m.target = 1 and m.action = 'view' and m.timecreated> $monthtime
                                    group by m.contextid
                                    order by rank desc
                                    limit 0,10");
$totalranks = $DB->get_records_sql("select count(1) as rank ,m.target,m.contextid,e.name as bookname from mdl_microread_log m
                                    left join mdl_ebook_my e on m.contextid = e.id
                                    where  m.target = 1 and m.action = 'view'
                                    group by m.contextid
                                    order by rank desc
                                    limit 0,10");
$weekrankarray = array();
$monthrankarray = array();
$totalrankarray = array();
foreach($weekranks as $weekrank ){
    $weekrankarray[] = '《'.$weekrank->bookname.'》'.' -- '.$weekrank->rank;
}
if(count($weekrankarray)<10){
    $i = 10 - count($weekrankarray);
    for($i;$i>0;$i--){
        $weekrankarray[] = ' ';
    }
}
foreach($monthranks as $monthrank ){
    $monthrankarray[] = '《'.$monthrank->bookname.'》'.' -- '.$monthrank->rank;
}
if(count($monthrankarray)<10){
    $i = 10 - count($monthrankarray);
    for($i;$i>0;$i--){
        $monthrankarray[] = ' ';
    }
}
foreach($totalranks as $totalrank ){
    $totalrankarray[] = '《'.$totalrank->bookname.'》'.' -- '.$totalrank->rank;
}
if(count($totalrankarray)<10){
    $i = 10 - count($totalrankarray);
    for($i;$i>0;$i--){
        $totalrankarray[] = ' ';
    }
}

$weekrankStr = '"';
$weekrankStr .= implode('","',$weekrankarray);
$weekrankStr .= '"';

$monthrankStr = '"';
$monthrankStr .= implode('","',$monthrankarray);
$monthrankStr .= '"';

$totalrankStr = '"';
$totalrankStr .= implode('","',$totalrankarray);
$totalrankStr .= '"';

//End 热门排行榜


?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>书库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroom.css" />

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

            //热门排行榜
            var moothrank = new Array(<?php echo $monthrankStr; ?>); //月书单
            var weekrank = new Array(<?php echo $weekrankStr; ?>); //周书单
            var totalrank = new Array(<?php echo $totalrankStr; ?>); //总书单
		</script>
	</head>
	<body id="bookroom">
		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<a class="frist" href="<?php echo $CFG->wwwroot; ?>">首页</a>
				<a href="<?php echo $CFG->wwwroot; ?>/mod/forum/view.php?id=1">微阅</a>
				<a href="<?php echo $CFG->wwwroot; ?>/course/index.php">微课</a>
				<a href="<?php echo $CFG->wwwroot; ?>/privatecenter/index.php?class=zhibo">直播</a>
				<a class="login" href="<?php echo $CFG->wwwroot; ?>/login/index.php"><img src="../img/denglu.png"></a>
			</div>
		</div>
		
		<div class="header-banner">
			<img  src="../img/shuku_logo.png"/>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
			     	<div class="input-group-btn">
			        	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">图书&nbsp;<span class="caret"></span></button>
			        	<ul class="dropdown-menu">
			          		<li><a href="#">图书</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a href="#">文献</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a href="#">论文</a></li>
			        	</ul>
			      	</div><!-- /btn-group -->
			      	<input type="text" class="form-control" >
			    </div><!-- /input-group -->
			    <button class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
			    
			    <div class="radio">
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
			    		全部字段
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
			    		标题
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		主讲人
			  		</label>
				</div>
			    
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
                                    <img src="'.$book->pictrueurl.'" />
                                    <div class="book-info-box">
                                        <p class="bookname">'.$book->name.'</p>
                                        <p class="writer">作者：'.$book->authorname.'</p>
                                        <p class="bookinfo">'.$book->summary.'</p>
                                        <p>';
                            $tags = $DB->get_records_sql("select tm.id,tm.tagname from mdl_tag_link tl
                                                left join mdl_tag_my tm on tl.tagid = tm.id
                                                where tl.link_id = $book->id");
                            if($tags != null){
                                foreach($tags as $tag){
                                    echo '<a class="tips">'.$tag->tagname.'</a>';
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
                                                echo ' <li id="'.($i-1).'"><a href="#" target="_blank" title="图片"> <img src="'.$recommends[$i]->pictrueurl.'" alt=\'图片\' style="border: 0"></a></li>';
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
                                        <a class="bookname">'.$recommend->name.'</a>
                                    </div>';
                                }else{
                                    echo ' <div class="ranklist-block">
                                        <a class="ranknum">'.$no.'</a>
                                        <a class="bookname">'.$recommend->name.'</a>
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
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">1</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">2</a></div>
							<div class="w-name"><a class="writername">张&nbsp;&nbsp;&nbsp;华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">3</a></div>
							<div class="w-name"><a class="writername">李子红</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">4</a></div>
							<div class="w-name"><a class="writername">张大炮</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">5</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">6</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">7</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">8</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">9</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top10">10</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门作者榜 end-->
				
				<!--评分榜-->
				<div class="score">
					<div class="title">评分榜</div>
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
