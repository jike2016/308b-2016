<?php
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$totalflag = optional_param('totalflag', false, PARAM_INT);//分页页号

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
	$index = ($page-1)*12;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid = $docthirdclassid order by dm.categoryid,dm.timecreated desc limit $index,12");
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
	$index = ($page-1)*12;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc limit $index,12");
	if(!$totalflag){
		$docscount = $DB->get_records_sql("select * from mdl_doc_my dm where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc");
		$totalcount = count($docscount);
	}
}elseif($docclassid != null){//显示顶级分类及以下的文档

	$docsecondclassesarray = array();//二级分类
	foreach($docsecondclassesnow as $docsecondclass){
		$docsecondclassesarray[] = $docsecondclass->id;
	}
	$docsecondclassStr = implode(',',$docsecondclassesarray);//获取当前文档的二级分类id字符串
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
	$index = ($page-1)*12;//从第几条记录开始
	$docs = $DB->get_records_sql("select dm.*,dc.`name` as categoryname from mdl_doc_my dm left join mdl_doc_categories_my dc on dm.categoryid = dc.id where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc limit $index,12");
	if(!$totalflag){
		$docscount = $DB->get_records_sql("select * from mdl_doc_my dm where dm.categoryid in ($rangeID) order by dm.categoryid,dm.timecreated desc");
		$totalcount = count($docscount);
	}
}



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
<div class="header">
	<div class="header-center">
		<div class="a-box">
			<a class="nav-a frist" href="#">首页</a>
			<a class="nav-a" href="#">微阅</a>
			<a class="nav-a" href="#">微课</a>
			<a class="nav-a" href="#">直播</a>
			<!--a class="nav-a login" href="#"><img src="../img/denglu.png"</a-->
		</div>

		<div id="usermenu" class="dropdown">
			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<a href="#" class="username">王大锤</a>
				<a href="#" class="userimg"><img src="../img/user.jpg" style="width: 40px;"></a>
			</button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href="#">个人中心</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="#">台账</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="#">Something</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="#">Separated</a></li>
			</ul>
		</div>
	</div>
</div>

<div class="header-banner">
	<a href="index.php"><img  src="../img/logo_WenKu.png"/></a>
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
				<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
				全部
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
				DOC
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
				PPT
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
				TXT
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
				PDF
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
				XLS
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
									<h3 class="itemtit1"><span></span><a href="classify.php?docclassid='.$docclass->id.'&docsecondclassid='.$docsecondclass->id.'">'.$docsecondclass->name.'</a></h3>
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
									echo '<a href="classify.php?docclassid='.$docclass->id.'&docsecondclassid='.$docsecondclass->id.'&docthirdclassid='.$docthirdclass->id.'">'.$docthirdclass->name.'</a>';
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
								<td class="docname"><img src="'.$doc->pictrueurl.'" width="40" height="52"><div><a href="#">'.$doc->name.'</a></div></td>
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
				<ul class="pagination">
					<li>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>">
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
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
							<span aria-hidden="true">上一页</span>
						</a>
					</li>
					<?php
						$totalpage = ceil($totalcount/12);
						for($i=1;$i<=$totalpage;$i++){
							if($page == $i){
								echo ' <li><a class="active" href="classify.php?docclassid='.$docclassid.'&docsecondclassid='.$docsecondclassid.'&docthirdclassid='.$docthirdclassid.'&page='.$i.'">'.$i.'</a></li>';
							}else{
								echo ' <li><a class="" href="classify.php?docclassid='.$docclassid.'&docsecondclassid='.$docsecondclassid.'&docthirdclassid='.$docthirdclassid.'&page='.$i.'">'.$i.'</a></li>';
							}
						}
					?>
					<li>
						<?php
							if(ceil($totalcount/12)==0){
								$nextpage = 1;
							}elseif(($page+1)>= ceil($totalcount/12) ){
								$nextpage = ceil($totalcount/12);
							}else{
								$nextpage = $page+1;
							}
						?>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
							<span aria-hidden="true">下一页</span>
						</a>
					</li>
					<li>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>&page=<?php if(ceil($totalcount/12)==0){echo 1;}else{echo ceil($totalcount/12);} ?>">
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


<!--右下角按钮-->
<div id="J_GotoTop" class="elevator">
	<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
	<a class="elevator-weixin" style="cursor:pointer"></a>
	<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
	<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
	<a class="elevator-top" href="#"></a>
</div>
<!--右下角按钮 end-->

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
