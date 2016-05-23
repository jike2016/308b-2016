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

	<script>
		$(document).ready(function() {
			//搜索选项下拉框
			$('#searchtype a').click(function() {
				$('#searchtypebtn').text($(this).text());
				$('#searchtypebtn').append('<span class="caret"></span>');
			});
			//单选组合
//			$("input[type='radio'][name='optionsRadios']").removeAttr("checked");
//			$("input[type='radio'][id='optionsRadios-"+searchDocType+"']").attr("checked","checked");

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
			var searchType = document.getElementById("searchtypebtn");//获取选项
			var searchParam = document.getElementById("searchParam");//获取查询参数
			var searchDocTypes = document.getElementsByName("optionsRadios");//获取文档类型
			var searchDocType = '';
			for(var i =0;i<searchDocTypes.length;i++){
				if(searchDocTypes[i].checked){
					searchDocType = searchDocTypes[i].value;
				}
			}
			window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value+"&searchDocType="+searchDocType;
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
<body id="article_classify">

<form id="pagerForm" method="post" action="">
	<input type="hidden" name="totalcount" value="<?php echo $totalcount;?>" />
	<input type="hidden" name="totalflag" value="<?php echo $totalflag;?>" />
</form>
<!--顶部导航-->
<div class="header">
	<div class="header-center">
		<div class="a-box">
			<a class="nav-a frist"  href="<?php echo $CFG->wwwroot; ?>">首页</a>
			<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/microread/index.php">微阅</a>
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
											<li><a href="user_upload.php">上传文档</a></li>
											<li role="separator" class="divider"></li>
											<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>
										</ul>
									</div>';
			};
		?>
	</div>
</div>

<div class="header-banner">
	<a href="index.php"><img  src="../img/logo_WenKu.png"/></a>
	<!--搜索框组-->
	<div class="search-box">
		<div class="input-group">
			<div class="input-group-btn">
				<button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ($searchType != '')?$searchType :'全部'; ?><span class="caret"></span></button>
				<ul id="searchtype" class="dropdown-menu">
					<li><a href="#">全部</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">标题</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">上传者</a></li>
				</ul>
			</div><!-- /btn-group -->
			<input id="searchParam" type="text" class="form-control" >
		</div><!-- /input-group -->
		<button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>

		<div id="searchDocType" class="radio">
			<label>
				<input type="radio" checked="checked" name="optionsRadios" id="optionsRadios-all" value="all">
				全部
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios-doc" value="doc">
				DOC
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios-ppt" value="ppt">
				PPT
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios-txt" value="txt">
				TXT
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios-pdf" value="pdf">
				PDF
			</label>
			<label>
				<input type="radio" name="optionsRadios" id="optionsRadios-xls" value="xls">
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
						$totalpage = ceil($totalcount/$prePageNum);
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
							if(ceil($totalcount/$prePageNum)==0){
								$nextpage = 1;
							}elseif(($page+1)>= ceil($totalcount/$prePageNum) ){
								$nextpage = ceil($totalcount/$prePageNum);
							}else{
								$nextpage = $page+1;
							}
						?>
						<a href="classify.php?docclassid=<?php echo $docclassid; ?>&docsecondclassid=<?php echo $docsecondclassid; ?>&docthirdclassid=<?php echo $docthirdclassid; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
							<span aria-hidden="true">下一页</span>
						</a>
					</li>
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

<div style="clear: both;"></div>


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
