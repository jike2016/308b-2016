<?php
require_once ("../../config.php");

if(isset($_GET["bookid"]) && $_GET["bookid"] != null){//阅读的书籍id
	$bookid = $_GET["bookid"];
}

require_once ("../loglib.php");
addbookviewlog($bookid);//添加日志记录

global $DB;
global $USER;
//查找阅读历史
$readHistory = $DB->get_record_sql("select * from mdl_ebook_user_read_my eu
									where eu.userid = $USER->id and eu.ebookid = $bookid ");
if($readHistory){
	$readStr = '继续阅读';
}else{
	$readStr = '在线阅读';
}

$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");//获取顶级分类
//查询书籍信息
$book = $DB->get_record_sql("select e.*,ea.`name` as authorname,ec.id as bookclassid,ec.`name` as categoryname from mdl_ebook_my e
								left join mdl_ebook_author_my ea on e.authorid = ea.id
								left join mdl_ebook_categories_my ec on e.categoryid = ec.id
								where e.id = $bookid");
//查询书籍的所属顶级分类
$booktopclass = $DB->get_record_sql("select ec2.* from mdl_ebook_categories_my ec1
									left join mdl_ebook_categories_my ec2 on ec1.parent = ec2.id
									where ec1.id = $book->bookclassid");
if($booktopclass->name != null){//如果有顶级分类
	$bookclasslist = $booktopclass->name.'&nbsp;>&nbsp;'.$book->categoryname;
}else{//如果当前分类已经是顶级分类
	$bookclasslist = $book->categoryname;
}

//获取书籍的标签
$tags = $DB->get_records_sql("select tm.id,tm.tagname from mdl_tag_link tl
								left join mdl_tag_my tm on tl.tagid = tm.id
								where tl.link_id = $book->id");

//查询各章节
$bookchapters = $DB->get_records_sql("select * from mdl_ebook_chapter_my e
										where e.ebookid = $bookid
										order by e.chapterorder");

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>书库分类页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookindex.css" />

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

	<body id="bookindex">
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
			<p class="bookname">
				<?php echo $book->name; ?>
			</p>

			<!--书籍介绍-->
			<div class="bookbanner">
				<div class="imgbox">
					<img src="<?php echo $book->pictrueurl; ?>" width="150" height="220" />
				</div>
				<div class="bookinfo">
					<p class="titleword">作者：&nbsp;&nbsp;</p><p class="titleinfo"><?php echo $book->authorname; ?></p><br />
					<p class="titleword">简介：&nbsp;&nbsp;</p><p class="titleinfo"><?php echo $book->summary; ?></p><br />
					<p class="titleword">所属分类：&nbsp;&nbsp;</p>
						<?php
							if($booktopclass->name != null) {//如果是二级分类，则会有顶级分类
								echo '<a href="classify.php?bookclassid='.$booktopclass->id .'";  class="classify" >'.$booktopclass->name.'</a> &nbsp;>&nbsp;';
								echo '<a href="classify.php?bookclassid='.$booktopclass->id  .'&booksecondclassid='.$book->bookclassid.'";  class="classify" >'.$book->categoryname.'</a>';
							}else{//如果是顶级分类
								echo '<a href="classify.php?bookclassid='.$book->bookclassid.'" class="classify" >'.$book->categoryname.'</a>';
							}
						?>
					<br />
					<p class="titleword">字数：&nbsp;&nbsp;</p><?php echo $book->wordcount; ?><br />
					<p class="titleword">标签：&nbsp;&nbsp;</p>
					<?php
						foreach($tags as $tag){
							echo '<a class="tips">'.$tag->tagname.'</a>&nbsp;&nbsp;';
						}
					?>
					<br />
					<div class="btnbox">
						<a href="onlineread.php?bookid=<?php echo $book->id; ?>" target="_blank" class="functionbtn"><?php echo $readStr; ?></a>
						<a href="<?php echo $book->url; ?>" download="" class="functionbtn">下载此书</a>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			<!--书籍介绍 end-->

			<!--书籍目录-->
			<div class="bookcatalog">
				<p class="title">
					目录
				</p>
				<?php
					foreach($bookchapters as $bookchapter){

						$booksections = $DB->get_records_sql("select * from mdl_ebook_section_my es
																where es.chapterid = $bookchapter->id
																order by es.sectionorder");
						echo '<p>
									<a href="#">'.$bookchapter->name.'</a>
									<ul>';
						foreach($booksections as $booksection){
							echo '<li><a href="onlineread.php?bookid='.$bookid.'&booksectionid='.$booksection->id.'" target="_blank">'.$booksection->name.'</a></li>';
						}
						echo '</ul>
							</p>';

					}
				?>
			</div>
			<!--书籍目录 end-->
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
