<?php
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$prePageNum = 10;//每页显示的条数

if(isset($_GET["authorid"]) && $_GET["authorid"] != null){//作者id
	$authorid = $_GET["authorid"];
}
if(isset($_GET["totalcount"]) && $_GET["totalcount"] != null){//书籍总数
	$totalcount = $_GET["totalcount"];
}

global $DB;
//获取顶级分类
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");
//获取作者信息
$authorinfo = $DB->get_record_sql("select * from mdl_ebook_author_my ae where ae.id = $authorid");
//浏览数量
$browsenum = $DB->get_record_sql("select count(1) as num from mdl_ebook_my e
									left join mdl_microread_log m on e.id = m.contextid
									where m.action = 'view' and m.target = 1
									and e.authorid = $authorid ");
//获取此作者的书籍信息
$index = ($page-1)*$prePageNum;
$books = $DB->get_records_sql("select * from mdl_ebook_my e
								where e.authorid = $authorid
								order by e.timecreated desc
								limit $index,$prePageNum ");

//获取热门书籍
$hotbooks = $DB->get_records_sql("select e.id,e.`name`,count(1) as rankcount from mdl_ebook_my e
									left join mdl_microread_log m on e.id = m.contextid
									where m.action = 'view' and m.target = 1
									and e.authorid = $authorid
									group by m.contextid
									order by rankcount desc
									limit 0,5 ");

//如果还没有查过总记录数则查询
if(isset($_POST['totalcount'])) {
	$totalcount = $_POST['totalcount'];
}
else{
	$bookstotal = $DB->get_record_sql("select count(1) as count from mdl_ebook_my e where e.authorid = $authorid ");
	$totalcount = $bookstotal->count;
}



?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>书库作者页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroomallpage.css" />
		<link rel="stylesheet" href="../css/bookroom_author.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

	</head>
	<body>

	<form id="pagerForm" method="post" action="">
		<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
	</form>

		<!--顶部导航-->
		<?php
			require_once ("../common/book_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			require_once ("../common/book_head_search.php");//书库搜索栏
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
		
		<!-- 作者banner -->
		<div class="banner">
			<div class="author-content">
				<div class="col-lg-1 col-lg-offset-1 col-sm-1 col-sm-offset-1 author-left">
					<img src="<?php echo $authorinfo->pictrueurl; ?>" alt="" width="70" height="70" />
					<a id="author-name"><?php echo $authorinfo->name; ?></a>
				</div>
				<div class="col-lg-10 col-sm-10 author-right">
					<p class="author-achieve">
						<span class="num"><?php echo $totalcount; ?></span>篇原创作品<span class="separator">|</span><span class="num"><?php echo $browsenum->num; ?></span>人浏览
					</p>
					<p class="author-intro"><?php echo $authorinfo->summary; ?></p>
				</div>
			</div>
		</div>
		<!-- 作者banner end -->
			
		<!--页面主体-->
		<div class="main">
			<div class="book-hot col-lg-2">
				<h2>热门书籍</h2>
				<ul class="list-unstyled">
					<?php
						foreach($hotbooks as $hotbook){
							echo '<li><a href="bookindex.php?bookid='.$hotbook->id.'" title="'.$hotbook->name.'" target="_blank">'.$hotbook->name.'</a></li>';
						}
					?>
				</ul>
			</div>
			<div class="book-list col-lg-10">
				<?php
					foreach($books as $book){
						echo '<div class="book-block">
									<a href="bookindex.php?bookid='.$book->id.'" target="_blank"><img src="'.$book->pictrueurl.'" width="102" height="150" /></a>
									<div class="book-info-box">
										<a href="bookindex.php?bookid='.$book->id.'" target="_blank"><p class="bookname">'.$book->name.'</p></a>
										<p class="writer">作者：'.$authorinfo->name.'</p>
										<p class="bookinfo">'.mb_substr($book->summary,0,50,'utf-8').'...</p>
										<p>';
						//获取书籍的标签
						$tags = $DB->get_records_sql("select tm.id,tm.tagname from mdl_tag_my tm
														left join mdl_tag_link tl on tm.id = tl.tagid
														where tl.link_name = 'mdl_ebook_my'
														and tl.link_id = $book->id ");
						$num = 1;
						foreach($tags as $tag){
							if($num == 7){ break;}
							echo '<a class="tips">'.$tag->tagname.'</a>';
							$num++;
						}
						echo '</p>
									</div>
								</div>';
					}
				?>

				<!--分页-->
				<div style="clear: both;"></div>
					<div class="paging text-center">
					<nav>
						<?php echo ($totalcount == 0)?'<p>暂无相关书籍</p>':''; ?>
						<ul class="pagination" <?php echo ($totalcount == 0)?'style="display: none;"':'style=""'; ?> >
							<li>
								<a href="bookauthor.php?authorid=<?php echo $authorid; ?>">
									<span aria-hidden="true">首页</span>
								</a>
							</li>
							<li>
								<?php
									if(($page-1)<= 0){
										$prepage = 1;
									}else{
										$prepage = $page-1;
									}
								?>
								<a href="bookauthor.php?authorid=<?php echo $authorid; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
									<span aria-hidden="true">上一页</span>
								</a>
							</li>
							<?php
								$totalpage = ceil($totalcount/$prePageNum);
								for($i=1;$i<=$totalpage;$i++){
									if($page == $i){
										echo ' <li><a class="active" href="bookauthor.php?authorid='.$authorid.'&page='.$i.'">'.$i.'</a></li>';
									}else{
										echo ' <li><a class="" href="bookauthor.php?authorid='.$authorid.'&page='.$i.'">'.$i.'</a></li>';
									}
								}
							?>

							<li>
								<?php
									if(ceil($totalcount/$prePageNum)==0){
										$nextpage = 1;
									}elseif(($page+1)>= ceil($totalcount/$prePageNum) ){
										$nextpage = ceil($totalcount/$prePageNum);
									}else{
										$nextpage = $page+1;
									}
								?>
								<a href="bookauthor.php?authorid=<?php echo $authorid; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
									<span aria-hidden="true">下一页</span>
								</a>
							</li>
							<li>
								<a href="bookauthor.php?authorid=<?php echo $authorid; ?>&page=<?php if(ceil($totalcount/$prePageNum)==0){echo 1;}else{echo ceil($totalcount/$prePageNum);} ?>">
									<span aria-hidden="true">尾页</span>
								</a>
							</li>
						</ul>
					</nav>
				</div>
				<!--分页 end-->
			</div>
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
