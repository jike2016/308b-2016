<!DOCTYPE html>
<?php
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号

if(isset($_GET["bookclassid"]) && $_GET["bookclassid"] != null){//顶级分类
	$bookclassid = $_GET["bookclassid"];
}

$booksecondclassid = '';//要显示的二级分类
if(isset($_GET["booksecondclassid"]) && $_GET["booksecondclassid"] != null){
	$booksecondclassid = $_GET["booksecondclassid"];
}

global $DB;
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");//获取顶级分类
$bookclassnow = $bookclasses[$bookclassid];//当前顶级分类对象
//获取当前顶级分类下面的二级分类
$booksecondclasses = $DB->get_records_sql("select ec.id,ec.`name`,count(*) as num from mdl_ebook_categories_my ec
											left join mdl_ebook_my e on ec.id = e.categoryid
											where ec.parent = $bookclassid
											group by e.categoryid ");

//从首页跳转到二级分页，显示二级分页的所有书籍
if($booksecondclassid == '' ){
	$index = ($page-1)*12;//从第几条记录开始
	$books = $DB->get_records_sql("select e.*,ea.`name` as authorname from mdl_ebook_categories_my ec
									left join mdl_ebook_my e on ec.id = e.categoryid
									left join mdl_ebook_author_my ea on e.authorid = ea.id
									where ec.parent = $bookclassid
									or e.categoryid = $bookclassid
									order by e.timecreated desc,e.categoryid asc
									limit $index,12");
}

if($booksecondclassid != ''){//获取二级分类对应的书
	$index = ($page-1)*12;//从第几条记录开始
	$books = $DB->get_records_sql("select e.*,ea.`name` as authorname from mdl_ebook_my e
								left join mdl_ebook_author_my ea on e.authorid = ea.id
								where e.categoryid = $booksecondclassid
								order by e.timecreated desc
								limit $index,12");
}

//如果还没有查过总记录数则查询
if(isset($_POST['totalcount'])) {
	$totalcount = $_POST['totalcount'];
}
else{
	if($booksecondclassid != '') {//选择二级分类
		$record = $DB->get_record_sql("select count(*) as count from mdl_ebook_my e
									left join mdl_ebook_author_my ea on e.authorid = ea.id
									where e.categoryid = $booksecondclassid");
		$totalcount = $record->count;
	}
	if($booksecondclassid == '') {//未选着二级分类
		$record = $DB->get_record_sql("select count(*) as count from mdl_ebook_categories_my ec
										left join mdl_ebook_my e on ec.id = e.categoryid
										where ec.parent = $bookclassid
										or e.categoryid = $bookclassid");
		$totalcount = $record->count;
	}
}

//如果还没有设置过顶级书籍的数量
if(isset($_POST['toptotalcount'])) {
	$toptotalcount = $_POST['toptotalcount'];
}else{
	$topcount = $DB->get_records_sql("select e.*,ea.`name` as authorname from mdl_ebook_categories_my ec
									left join mdl_ebook_my e on ec.id = e.categoryid
									left join mdl_ebook_author_my ea on e.authorid = ea.id
									where ec.parent = $bookclassid
									or e.categoryid = $bookclassid
									");
	$toptotalcount = count($topcount);
}

?>


<html>
	<head>
		<meta charset="UTF-8">
		<title>书库分类页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroom_classify.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

		<script>
			//搜索选项下拉框
			$(document).ready(function() {
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
				});
			});
			//搜索
			function search(){
				var searchType = document.getElementById("searchtypebtn");//获取查询参数
				var searchParam = document.getElementById("searchParam");//获取选项
				window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value;
			}

		</script>
	</head>
	<body id="bookroom_classify">

		<form id="pagerForm" method="post" action="">
			<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
			<input type="hidden" name="toptotalcount" value="<?php echo $toptotalcount;?>" /><!-- 顶级分类的书籍数量 -->
		</form>

		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<a class="frist" href="<?php echo $CFG->wwwroot; ?>">首页</a>
				<a href="<?php echo $CFG->wwwroot; ?>/mod/forum/view.php?id=1">微阅</a>
				<a href="<?php echo $CFG->wwwroot; ?>/course/index.php">微课</a>
				<a href="<?php echo $CFG->wwwroot; ?>/privatecenter/index.php?class=zhibo">直播</a>
				<a class="login" href="#"><img src="../img/denglu.png"></a>
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
				<button onclick="search()" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>

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
			<!--分类面板-->
			<div class="classifiedbanner">
				<div class="classified-block total">
					<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>"><?php echo $bookclassnow->name; ?></a>
					<span><?php echo $toptotalcount; ?></span>
				</div>
				<?php
					foreach($booksecondclasses as $booksecondclass){
						echo '<div class="classified-block">
									<a href="classify.php?bookclassid='.$bookclassid.'&booksecondclassid='.$booksecondclass->id.'">'.$booksecondclass->name.'</a>
									<span>'.$booksecondclass->num.'</span>
								</div>';
					}
				?>
			</div>
			<!--分类面板  end-->
			
			<!--书本列表面板-->
			<div class="booklistbanner">
				<!--第一排-->
				<?php
					foreach($books as $book){
						echo '<a href="bookindex.php?bookid='.$book->id.'">
									<div class="book-block">
										<div class="imgbox">
											<img src="'.$book->pictrueurl.'" width="130" height="190"/>
										</div>
										<div class="bookinfo">
											'.$book->name.'
											<p>'.$book->authorname.'（1900-1920）</p>
										</div>
									</div>
								</a>';
					}
				?>
				<!--第一排 end-->

				<!--分页-->
				<div style="clear: both;"></div>
				<div class="paging">
				<nav>
				  	<ul class="pagination">
				  		<li>
				     	 	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>">
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
				     	 	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
				       	 		<span aria-hidden="true">上一页</span>
				      		</a>
				    	</li>
						<?php
							$totalpage = ceil($totalcount/12);
							for($i=1;$i<=$totalpage;$i++){
								echo ' <li><a href="classify.php?bookclassid='.$bookclassid.'&booksecondclassid='.$booksecondclassid.'&page='.$i.'">'.$i.'</a></li>';
							}
						?>

					    <li>
							<?php
								if(($page+1)>= ceil($totalcount/12) ){
									$nextpage = ceil($totalcount/12);
								}else{
									$nextpage = $page+1;
								}
							?>
					      	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
					       		<span aria-hidden="true">下一页</span>
					      	</a>
				    	</li>
				    	<li>
				     	 	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>&page=<?php echo ceil($totalcount/12); ?>">
				       	 		<span aria-hidden="true">尾页</span>
				      		</a>
				    	</li>
				  	</ul>
				</nav>
				</div>
				<!--分页 end-->
			</div>
			<!--书本列表面板 end-->
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
