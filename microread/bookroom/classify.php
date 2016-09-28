<!DOCTYPE html>
<?php
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$prePageNum = 12;//每页显示的记录数

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
$booksecondclasses = $DB->get_records_sql("select ec.id,ec.`name`,count(*) as num,e.id as numflag from mdl_ebook_categories_my ec
											left join mdl_ebook_my e on ec.id = e.categoryid
											where ec.parent = $bookclassid
											group by e.categoryid ");

//从首页跳转到二级分页，显示二级分页的所有书籍
if($booksecondclassid == '' ){
	$index = ($page-1)*$prePageNum;//从第几条记录开始
	$books = $DB->get_records_sql("select e.*,ea.`name` as authorname from mdl_ebook_categories_my ec
									left join mdl_ebook_my e on ec.id = e.categoryid
									left join mdl_ebook_author_my ea on e.authorid = ea.id
									where ec.parent = $bookclassid
									or e.categoryid = $bookclassid
									order by e.timecreated desc,e.categoryid asc
									limit $index,$prePageNum");
}

if($booksecondclassid != ''){//获取二级分类对应的书
	$index = ($page-1)*$prePageNum;//从第几条记录开始
	$books = $DB->get_records_sql("select e.*,ea.`name` as authorname from mdl_ebook_my e
								left join mdl_ebook_author_my ea on e.authorid = ea.id
								where e.categoryid = $booksecondclassid
								order by e.timecreated desc
								limit $index,$prePageNum");
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
		<link rel="stylesheet" href="../css/bookroomallpage.css" />
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

	</head>
	<body id="bookroom_classify">

		<form id="pagerForm" method="post" action="">
			<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
			<input type="hidden" name="toptotalcount" value="<?php echo $toptotalcount;?>" /><!-- 顶级分类的书籍数量 -->
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
									<a href="classify.php?bookclassid='.$bookclassid.'&booksecondclassid='.$booksecondclass->id.'">'.$booksecondclass->name.'</a>';
						if($booksecondclass->numflag != null){
							echo "<span>$booksecondclass->num</span>";
						}else{
							echo "<span>0</span>";
						}
						echo '</div>';
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
											<p>'.$book->authorname.'（'.userdate($book->timecreated,'%Y-%m-%d').'）</p>
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
					<?php echo ($totalcount == 0)?'<p>暂无相关书籍</p>':''; ?>
				  	<ul class="pagination" <?php echo ($totalcount == 0)?'style="display: none;"':'style=""'; ?> >
				  		<li>
				     	 	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>">
				       	 		<span aria-hidden="true">首页</span>
				      		</a>
				    	</li>
						<?php
							$param = '&bookclassid='.$bookclassid.'&booksecondclassid='.$booksecondclassid;
							$totalpage = ceil($totalcount/$prePageNum);
							echo echo_end($page,$totalpage,$param);//输出上下页及页码按钮
						?>
				    	<li>
				     	 	<a href="classify.php?bookclassid=<?php echo $bookclassid; ?>&booksecondclassid=<?php echo $booksecondclassid; ?>&page=<?php if(ceil($totalcount/$prePageNum)==0){echo 1;}else{echo ceil($totalcount/$prePageNum);} ?>">
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

		<?php
			/** START 输出上下页及页码按钮等
			 * @param $currentpage 当前页码
			 * @param $totalpage  总页数
			 * @param $param 参数
			 * @return String
			 */
			function echo_end($currentpage,$totalpage,$param){

				$html = '<li>
								<a href="classify.php?page='.(($currentpage-1)<1?1:($currentpage-1)).$param.'" aria-label="Previous">
									<span aria-hidden="true">上一页</span>
								</a>
							</li>';
				$html .= echo_end_pageList($totalpage,$currentpage,$param);
				$html .= '<li>
								<a href="classify.php?page='.(($currentpage+1)>$totalpage?$totalpage:($currentpage+1)).$param.'" aria-label="Next">
									<span aria-hidden="true">下一页</span>
								</a>
							</li>';

				return $html;
			}

			/** 输出页码 */
			function echo_end_pageList($count_page,$current_page,$param)
			{
				/** Start 设置评论数的显示页码（只显示5页） */
				$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
				$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
				/** End 设置评论数的显示页码（只显示5页）*/
				$output = '';
				for($num = $numstart; $num <= $numend; $num ++) {
					if ($num == $current_page) {
						//  修改当前页样式标示
						$output .=  ' <li><a class="active" href="classify.php?page='.$num.$param.'">'.$num .'</a></li>';
					} else {
						$output .=  ' <li><a href="classify.php?page='.$num.$param.'">'.$num .'</a></li>';
					}
				}
				return $output;
			}
		?>

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
