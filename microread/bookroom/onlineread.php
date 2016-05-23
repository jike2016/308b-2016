<?php
require_once ("../../config.php");

if(isset($_GET["bookid"]) && $_GET["bookid"] != null){//阅读的书籍id
	$bookid = $_GET["bookid"];
}
if(isset($_GET["bookchapterid"]) && $_GET["bookchapterid"] != null){//书籍的章id
	$bookchapterid = $_GET["bookchapterid"];
}
if(isset($_GET["booksectionid"]) && $_GET["booksectionid"] != null){//书籍的节id
	$booksectionid = $_GET["booksectionid"];
}

require_once ("../loglib.php");
add_microreadviewlog('view',$bookid,1);//添加日志记录

global $DB;
global $USER;
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");//获取顶级分类

//查询书籍信息
$book = $DB->get_record_sql("select e.*,ea.`name` as authorname,ec.id as bookclassid,ec.`name` as categoryname from mdl_ebook_my e
								left join mdl_ebook_author_my ea on e.authorid = ea.id
								left join mdl_ebook_categories_my ec on e.categoryid = ec.id
								where e.id = $bookid");

//获取当前书籍的所属分类级
$booktopclass = $DB->get_record_sql("select ec2.* from mdl_ebook_categories_my ec1
									left join mdl_ebook_categories_my ec2 on ec1.parent = ec2.id
									where ec1.id = $book->categoryid");
//获取当前书籍的章序列
$bookchapterids = $DB->get_records_sql("select * from mdl_ebook_chapter_my ec
										where ec.ebookid = $bookid
										order by ec.chapterorder");

//获取全书的节，按顺序排列
$booktotalsections = $DB->get_records_sql("select es.id,es.name from mdl_ebook_chapter_my ec
											left join mdl_ebook_section_my es on ec.id = es.chapterid
											where ec.ebookid = $bookid
											order by ec.chapterorder,es.sectionorder");

$showchapter = '0';//要显示的章
$showSection = '0';//要显示的节

//获取上次阅读历史
$readHistory = $DB->get_record_sql("select * from mdl_ebook_user_read_my eu
									where eu.userid = $USER->id and eu.ebookid = $bookid ");
//如果没有选择特定的查看章节
if($booksectionid == null){

//在线阅读链接的内容显示
//判断是否有阅读记录
	//没有记录就从头开始
	//有记录就从记录处读取显示

	if($readHistory == null){
		if($bookchapterids != null){
			$showchapter = current($bookchapterids);//获取书籍的第一章
			$bookchapterid = $showchapter->id;//第一章的id

			//获取第一章的各节序列
			$booksections = $DB->get_records_sql("select * from mdl_ebook_section_my es
													where es.chapterid = $bookchapterid
													order by es.sectionorder");
			$showSection = current($booksections);//获取第一章的第一节
		}
	}
	else{
		$showSection = $DB->get_record_sql("select * from mdl_ebook_section_my es where es.id = $readHistory->sectionid");//获取阅读历史记录
		$chapterid = $showSection->chapterid;
		$showchapter = $DB->get_record_sql("select * from mdl_ebook_chapter_my ec where ec.id = $chapterid");//获取显示的章
	}

}else{//显示选定的章节
	$showSection = $DB->get_record_sql("select * from mdl_ebook_section_my es where es.id = $booksectionid");//获取指定的章节
	$chapterid = $showSection->chapterid;
	$showchapter = $DB->get_record_sql("select * from mdl_ebook_chapter_my ec where ec.id = $chapterid");//获取显示的章
}

$showSectionid = $showSection->id;//当前的显示节
//$lastsectionid = 0;//上一节
//$nextsectionid = 0;//下一节
foreach($booktotalsections as $booksection){
	if($booksection->id == $showSectionid){
		break;
	}
	$lastsectionid = $booksection->id;//上一节
}
$flag = 0;
foreach($booktotalsections as $booksection){

	if($flag){
		$nextsectionid = $booksection->id;//下一节
		break;
	}
	if($booksection->id == $showSectionid){
		$flag = 1;
	}

}

//2、用当前页面所在的章节去更新阅读记录
//有记录更新
//无则创建
if($readHistory == null) {
	$newReadHistory = new stdClass();
	$newReadHistory->userid = $USER->id;
	$newReadHistory->ebookid = $bookid;
	$newReadHistory->sectionid = $showSection->id;
	$newReadHistory->timecreated = time();
	if($showSection->id != ''){
		$updateHistory = $DB->insert_record("ebook_user_read_my", $newReadHistory, true);
	}
}else{
	$updateHistory = $DB->update_record("ebook_user_read_my", array('id'=>$readHistory->id, 'sectionid'=>$showSection->id,'timecreated'=>time()));
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>书库在线阅读页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroom_read.css" />
		<link rel="stylesheet" href="../css/bookroomallpage.css" />
		<style>
			.pdfobject-container {
				width: 100%;
				max-width: 1200px;
				height: 800px;
				margin: 2em 0;
			}
			.pdfobject { border: solid 1px #666; }
		</style>
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/pdfobject.min.js" ></script>

	</head>
	<body id="bookroom_readonline">
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
		
		<!--页面主体-->
		<div class="main">
			<div class="book-box">
				<div class="head-info">
					<?php
						if($booktopclass->name != null) {//如果有顶级分类
							echo '<a href="#">'.$booktopclass->name.'</a>
									<span>&nbsp;>&nbsp;</span>';
						}
					?>
					<a href="#"><?php echo $book->categoryname; ?></a>
					<span>&nbsp;>&nbsp;</span>
					<a href="#"><?php echo $book->name; ?></a>
					<span>&nbsp;>&nbsp;</span>
					<a href="#" class="chaptertitle"><?php echo $showchapter->name; ?></a>
				</div>
				<p class="title"><?php echo $showSection->name; ?></p>
				<div id="pdf" class="article">
					<?php
						if($showSection->type == 1){//文本格式
							echo $showSection->text;
						}elseif($showSection->type == 2){//pdf格式
//							 echo $showSection->pdfurl;
						}
					?>
				</div>
				<div class="bottom-info">
					<a href="onlineread.php?bookid=<?php echo $bookid; ?>&booksectionid=<?php echo $lastsectionid; ?>">上一页</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="bookindex.php?bookid=<?php echo $bookid; ?>">章节目录</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="onlineread.php?bookid=<?php echo $bookid; ?>&booksectionid=<?php echo $nextsectionid; ?>">下一页</a>
				</div>
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
	<?php
		if($showSection->type == 2){//pdf格式
			echo '<script>
				var options = {
					pdfOpenParams: {
						pagemode: "thumbs",
						navpanes: 0,
						toolbar: 0,
						statusbar: 0,
						view: "FitV"
					}
				};
				PDFObject.embed("'.$showSection->pdfurl.'", "#pdf", options);
				</script>';
			// echo $showSection->pdfurl;
		}
	?>
	
</html>
