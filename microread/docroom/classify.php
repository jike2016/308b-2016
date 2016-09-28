<?php
//文档分类页面
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$totalflag = optional_param('totalflag', false, PARAM_INT);//分页页号
$prePageNum = 12;//每页显示记录数

if(isset($_GET["docclassid"]) && $_GET["docclassid"] != null){//顶级分类
	$docclassid = $_GET["docclassid"];
}
$docsecondclassid = '';//要显示的二级分类
if(isset($_GET["docsecondclassid"]) && $_GET["docsecondclassid"] != null){
	$docsecondclassid = $_GET["docsecondclassid"];
}
$docthirdclassid = '';//要显示的三级分类
if(isset($_GET["docthirdclassid"]) && $_GET["docthirdclassid"] != null){
	$docthirdclassid = $_GET["docthirdclassid"];
}

global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");
$docclassnow = $docclasses[$docclassid];//获取当前顶级分类
//获取当前顶级分类下的二级分类
$docsecondclassesnow = $DB->get_records_sql("select dc2.* from mdl_doc_categories_my dc1
										left join mdl_doc_categories_my dc2 on dc1.id = dc2.parent
										where dc1.id = $docclassid");

//如果还没有设置过顶级书籍的数量
if(isset($_POST['totalcount'])) {
	$totalcount = $_POST['totalcount'];
	$totalflag = true;
}

//获取显示的文档
if($docthirdclassid != ''){//显示三级分类的文档
	$index = ($page-1)*$prePageNum;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid = $docthirdclassid order by dm.categoryid,dm.timecreated desc limit $index,$prePageNum");
	if(!$totalflag){
		$docscount = $DB->get_records_sql("select * from mdl_doc_my dm where dm.categoryid = $docthirdclassid order by dm.categoryid,dm.timecreated desc");
		$totalcount = count($docscount);
	}
}elseif($docsecondclassid != ''){//显示二级分类及以下的文档
	$docthirdclassesnow = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = $docsecondclassid");
	$docthirdclassesarray = array();
	foreach($docthirdclassesnow as $docthirdclass){
		$docthirdclassesarray[] = $docthirdclass->id;
	}
	$docthirdclassStr = implode(',',$docthirdclassesarray);//获取当前文档的三级分类id字符串
	$rangeID = $docsecondclassid;
	$rangeID .= ($docthirdclassStr != '')?','.$docthirdclassStr:'';
	$index = ($page-1)*$prePageNum;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc limit $index,$prePageNum");
	if(!$totalflag){
		$docscount = $DB->get_records_sql("select * from mdl_doc_my dm where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc");
		$totalcount = count($docscount);
	}
}elseif($docclassid != null){//显示顶级分类及以下的文档

	$docsecondclassesarray = array();//二级分类
	foreach($docsecondclassesnow as $docsecondclass){
		$docsecondclassesarray[] = $docsecondclass->id;
	}
	$docsecondclassStr = implode(',',$docsecondclassesarray);//获取当前顶级分类下的二级分类id字符串
	//获取当前顶级分类下的三级分类
	if($docsecondclassStr != null){
		$docthirdclassesnow = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent in ($docsecondclassStr)");
		$docthirdclassesarray = array();
		foreach($docthirdclassesnow as $docthirdclass){
			$docthirdclassesarray[] = $docthirdclass->id;
		}
		$docthirdclassStr = implode(',',$docthirdclassesarray);//获取当前文档的三级分类id字符串
	}
	$rangeID = $docclassid;
	$rangeID .= ($docsecondclassStr != '')?','.$docsecondclassStr:'';
	$rangeID .= ($docthirdclassStr != '')?','.$docthirdclassStr:'';
	$index = ($page-1)*$prePageNum;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc limit $index,$prePageNum");
	if(!$totalflag){
		$docscount = $DB->get_records_sql("select * from mdl_doc_my dm where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc");
		$totalcount = count($docscount);
	}
}

//Start 文件类型判断
function imagetype($type){
	$type = strtolower($type);
	$doctype = '';
	switch($type){
		case '.txt':
			$doctype = 'txt';
			break;
		case '.pdf':
			$doctype = 'pdf';
			break;
		case '.doc':
		case '.docx':
			$doctype = 'word';
			break;
		case '.xls':
		case '.xlsx':
			$doctype = 'xls';
			break;
		case '.ppt':
		case '.pptx':
			$doctype = 'ppt';
			break;
	}
	return $doctype;
}
//End 文件类型判断

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>文库分类页</title>

	<link rel="stylesheet" href="../css/bootstrap.css" />
	<link rel="stylesheet" href="../css/menu.css" />
	<link rel="stylesheet" href="../css/docallpage.css" />
	<link rel="stylesheet" href="../css/doc_classify.css" />

	<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
	<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
	<script type="text/javascript" src="../js/3c-menu.js" ></script>

</head>
<body id="article_classify">

<form id="pagerForm" method="post" action="">
	<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
	<input type="hidden" name="totalflag" value="<?php echo $totalflag;?>" />
</form>
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
<!--		<div class="btn-group" style="float: left;">-->
<!--			<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--				<img src="../img/tushuFenlei.png">-->
<!--			</a>-->
<!--			<ul class="dropdown-menu">-->
<!--				<li><a href="#">现代</a></li>-->
<!--				<li role="separator" class="divider"></li>-->
<!--				<li><a href="#">军事</a></li>-->
<!--				<li role="separator" class="divider"></li>-->
<!--				<li><a href="#">战争</a></li>-->
<!--				<li role="separator" class="divider"></li>-->
<!--				<li><a href="#">科技</a></li>-->
<!--			</ul>-->
<!--		</div>-->
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
	<!--分类面板-->
	<div class="classifiedbanner">
		<div class="mallCategory">
			<div class="catList">
				<h2><a href="classify.php?docclassid=<?php echo $docclassid; ?>"><?php echo $docclassnow->name;?></a></h2>
				<ul class="clearfix">
					<?php
						foreach($docsecondclassesnow as $docsecondclass){
							if($docsecondclass->id != null){
								echo '<li class="J_MenuItem">
									<h3 class="itemtit1"><span></span><a href="classify.php?docclassid='.$docclassid.'&docsecondclassid='.$docsecondclass->id.'">'.$docsecondclass->name.'</a></h3>
								</li>';
							}

						}
					?>
				</ul>
			</div>

			<div class="border">
				<ul>
					<li class="mask-top"></li>
					<li class="mask-bottom"></li>
				</ul>
			</div>
			<div class="cat-subcategory">
				<div class="shadow">
					<?php
						foreach($docsecondclassesnow as $docsecondclass){
							if($docsecondclass->id != null){
								//获取文档的三级分类
								$docthirdclasses = $DB->get_records_sql("select dc2.* from mdl_doc_categories_my dc1
																	left join mdl_doc_categories_my dc2 on dc1.id = dc2.parent
																	where dc1.id = $docsecondclass->id");
								echo '<div class="entity-main">
										<ul class="shadow-left">
											<li>';
								foreach($docthirdclasses as $docthirdclass){
									echo '<a href="classify.php?docclassid='.$docclassid.'&docsecondclassid='.$docsecondclass->id.'&docthirdclassid='.$docthirdclass->id.'">'.$docthirdclass->name.'</a>';
								}
								echo'		</li>
										</ul>
									</div>';
							}

						}

					?>

				</div>
			</div>
		</div>
	</div>
	<!--分类面板  end-->

	<!--书本列表面板-->
	<div class="booklistbanner">
		<table class="table table table-striped">
			<thead>
			<td class="docname">名称</td>
			<td class="docinfo">简介</td>
			<td class="doctype">分类</td>
			<td class="uploadtime">上传时间</td>
			<td class="download">下载</td>
			</thead>
			<tbody>
			<?php
				foreach($docs as $doc){
					echo '<tr>
								<td class="docname"><img src="../img/'.imagetype($doc->suffix).'.png" width="40" height="52"><div><a href="onlineread.php?docid='.$doc->id.'" target="_blank" >'.$doc->name.'</a></div></td>
								<td class="docinfo">'.$doc->summary.'</td>
								<td><a href="#">'.$doc->categoryname.'</a></td>
								<td class="uploadtime">'.userdate($doc->timecreated,'%Y年%m月%d日').'</td>
								<td><a href="'.$doc->url.'" download="" ><button class="btn btn-info">下载</button></a></td>
							</tr>';
				}
			?>
			</tbody>
		</table>

		<!--分页-->
		<div style="clear: both;"></div>
		<div class="paging">
			<nav>
				<?php echo ($totalcount == 0)?'<p>暂无相关文档</p>':''; ?>
				<ul class="pagination" <?php echo ($totalcount == 0)?'style="display: none;"':'style=""'; ?> >
					<li>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>">
							<span aria-hidden="true">首页</span>
						</a>
					</li>
					<?php
						$param = '&docclassid='.$docclassid.'&docsecondclassid='.$docsecondclassid.'&docthirdclassid='.$docthirdclassid;
						$totalpage = ceil($totalcount/$prePageNum);
						echo echo_end($page,$totalpage,$param);//输出上下页及页码按钮
					?>
					<li>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>&page=<?php if(ceil($totalcount/$prePageNum)==0){echo 1;}else{echo ceil($totalcount/$prePageNum);} ?>">
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

<div style="clear: both;"></div>

<!--页面右下角按钮 Start-->
<?php
	require_once ("../common/all_note_chat.php");//右下角链接：笔记、聊天、收藏、、、、
?>
<!--页面右下角按钮 end-->

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
