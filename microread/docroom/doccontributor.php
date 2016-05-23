<?php
//贡献者页面
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$prePageNum = 5;//每页显示的条数

if(isset($_GET["contributorid"]) && $_GET["contributorid"] != null){//贡献者id
	$contributorid = $_GET["contributorid"];
}
if(isset($_GET["totalcount"]) && $_GET["totalcount"] != null){//书籍总数
	$totalcount = $_GET["totalcount"];
}

global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");
//获取贡献者信息
$contributorinfo = $DB->get_record_sql("select * from mdl_user u where u.id = $contributorid ");
//获取浏览数量
$browsenum = $DB->get_record_sql("select count(1) as num from mdl_doc_my d
									left join mdl_microread_log m on d.id = m.contextid
									where m.action = 'view' and m.target = 2
									and d.uploaderid = $contributorid ");
//获取此贡献者的文档信息
$index = ($page-1)*$prePageNum;
$docs = $DB->get_records_sql("select d.*,dc.`name` as categoryname from mdl_doc_my d
								left join mdl_doc_categories_my dc on d.categoryid = dc.id
								where d.uploaderid = $contributorid
								order by d.timecreated desc
								limit $index,$prePageNum ");
//获取热门文档
$hotdocs = $DB->get_records_sql("select d.id,d.`name`,count(1) as rankcount from mdl_doc_my d
									left join mdl_microread_log m on d.id = m.contextid
									where m.action = 'view' and m.target = 2
									and d.uploaderid = $contributorid
									group by m.contextid
									order by rankcount desc
									limit 0,5");

//如果还没有查过总记录数则查询
if(isset($_POST['totalcount'])) {
	$totalcount = $_POST['totalcount'];
}
else{
	$docstotal = $DB->get_record_sql("select count(1) as count from mdl_doc_my d where d.uploaderid = $contributorid ");
	$totalcount = $docstotal->count;
}

/** Start 截取用户头像字符串*/
function getUserIcon($userid)
{
	global $OUTPUT;
	global $DB;
	$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
	$str1 = $OUTPUT->user_picture($user,array('link' => false,'visibletoscreenreaders' => false));
	$str=substr($str1,10);//去除前面
	$n=strpos($str,'"');//寻找位置
	if ($n) $str=substr($str,0,$n);//删除后面
	return $str;
}
/** End 截取用户头像字符串*/

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
		<title>文库贡献者页</title>
		
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/menu.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/doc_author.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/3c-menu.js" ></script>

	</head>
	<body>
		<form id="pagerForm" method="post" action="">
			<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
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

		<!-- 贡献者banner -->
		<div class="banner">
			<div class="author-content">
				<div class="col-lg-1 col-lg-offset-1 col-sm-1 col-sm-offset-1 author-left">
					<img src="<?php echo getUserIcon($contributorinfo->id); ?>" alt="" width="70" height="70" />
					<a id="author-name"><?php echo $contributorinfo->firstname; ?></a>
				</div>
				<div class="col-lg-10 col-sm-10 author-right">
					<p class="author-achieve">
						<span class="num"><?php echo $totalcount; ?></span>篇上传作品<span class="separator">|</span><span class="num"><?php echo $browsenum->num; ?></span>人浏览
					</p>
					<p class="author-intro"><?php echo $contributorinfo->description; ?></p>
				</div>
			</div>
		</div>
		<!-- 贡献者banner end -->
		
		<!--页面主体-->
		<div class="main">
			<!-- 热门文档 -->
			<div class="doc-hot col-lg-2">
				<h2>热门文档</h2>
				<ul class="list-unstyled">
					<?php
						foreach($hotdocs as $hotdoc){
							echo '<li><a href="onlineread.php?docid='.$hotdoc->id.'" title="'.$hotdoc->name.'" target="_blank">'.$hotdoc->name.'</a></li>';
						}
					?>
				</ul>
			</div>
			<!-- 热门文档end -->
			<div class="book-list col-lg-10">
				<!--书本列表面板-->
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
											<td class="docname"><img src="../img/'.imagetype($doc->suffix).'.png" width="40" heigth="52" ><div><a href="onlineread.php?docid='.$doc->id.'" target="_blank" >'.$doc->name.'</a></div></td>
											<td class="docinfo">'.strip_tags($doc->summary).'</td>
											<td><a href="#">'.$doc->categoryname.'</a></td>
											<td class="uploadtime">'.userdate($doc->timecreated,"%Y年%m月%d日").'</td>
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
							<a href="doccontributor.php?contributorid=<?php echo $contributorid; ?>">
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
							<a href="doccontributor.php?contributorid=<?php echo $contributorid; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
								<span aria-hidden="true">上一页</span>
							</a>
						</li>
						<?php
							$totalpage = ceil($totalcount/$prePageNum);
							for($i=1;$i<=$totalpage;$i++){
								if($page == $i){
									echo ' <li><a class="active" href="doccontributor.php?contributorid='.$contributorid.'&page='.$i.'">'.$i.'</a></li>';
								}else{
									echo ' <li><a class="" href="doccontributor.php?contributorid='.$contributorid.'&page='.$i.'">'.$i.'</a></li>';
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
							<a href="doccontributor.php?contributorid=<?php echo $contributorid; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
								<span aria-hidden="true">下一页</span>
							</a>
						</li>
						<li>
							<a href="doccontributor.php?contributorid=<?php echo $contributorid; ?>&page=<?php if(ceil($totalcount/$prePageNum)==0){echo 1;}else{echo ceil($totalcount/$prePageNum);} ?>">
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
