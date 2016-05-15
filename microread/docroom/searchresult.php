<?php
//搜索结果页面
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$prePageNum = 12;//每页显示的记录数

if(isset($_GET["searchType"]) && $_GET["searchType"] != null){//搜索类型
	$searchType = $_GET["searchType"];
}
if(isset($_GET["searchParam"]) && $_GET["searchParam"] != null){//搜索参数
	$searchParam = $_GET["searchParam"];
}
if(isset($_GET["searchDocType"]) && $_GET["searchDocType"] != null){//搜索文档类型
	$searchDocType = $_GET["searchDocType"];
	echo "<script>var searchDocType = '$searchDocType'; </script>";
}

global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

//搜索选项
switch($searchType){
	case '全部':
		$sql = "where ( dm.name like '%$searchParam%' or da.name like '%$searchParam%' or dm.summary like '%$searchParam%' or u.firstname like '%$searchParam%' )";
		break;
	case '标题':
		$sql = "where dm.name like '%$searchParam%'";
		break;
	case '作者':
		$sql = "where da.name like '%$searchParam%'";
		break;
	case '上传者':
		$sql = "where u.firstname like '%$searchParam%'";
		break;
	default:
		break;
}
//搜索文档类型
switch($searchDocType){
	case 'all':
		$sql .= "";
		break;
	case 'doc':
		$sql .= " and ( dm.suffix = '.doc' or dm.suffix = '.docx' ) ";
		break;
	case 'ppt':
		$sql .= " and ( dm.suffix = '.ppt' or dm.suffix = '.pptx' ) ";
		break;
	case 'txt':
		$sql .= " and dm.suffix = '.txt'";
		break;
	case 'pdf':
		$sql .= " and ( dm.suffix = '.pdf' or dm.suffix = 'pdfx' ) ";
		break;
	case 'xls':
		$sql .= " and ( dm.suffix = '.xls' or dm.suffix = '.xlsx' ) ";
		break;
	default:
		break;
}

//查询结果
$index = ($page-1)*$prePageNum;//从第几条记录开始
$searchResults = $DB->get_records_sql("select dm.*,da.`name` as authorname,u.firstname as uploadername from mdl_doc_my dm
										left join mdl_doc_author_my da on dm.authorid = da.id
										left join mdl_user u on dm.uploaderid = u.id
										$sql
										order by dm.timecreated desc
										limit $index,$prePageNum");

//获取相关搜索
$relateSearchs = $DB->get_records_sql("select * from mdl_doc_tag_my dt where dt.`name` like '%$searchParam%'");

//如果还没有设置过查询结果的数量
if(isset($_POST["searchcount"])){
	$searchcount = $_POST['searchcount'];
}else{
	$searchResultsCount = $DB->get_record_sql("select count(1) as totalcount from mdl_doc_my dm
													left join mdl_doc_author_my da on dm.authorid = da.id
													left join mdl_user u on dm.uploaderid = u.id
													$sql
												");
	$searchcount = $searchResultsCount->totalcount;
}

//Start 文件类型判断
function imagechoise($type){
	$type = strtolower($type);
	$doctype = '';
	switch($type){
		case '.txt':
			$doctype = 'ic-txt';
			break;
		case '.pdf':
			$doctype = 'ic-pdf';
			break;
		case '.doc':
		case '.docx':
			$doctype = 'ic-doc';
			break;
		case '.xls':
		case '.xlsx':
			$doctype = 'ic-xls';
			break;
		case '.ppt':
		case '.pptx':
			$doctype = 'ic-ppt';
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
		<title>文库搜索结果页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/docsearch.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

		<script>
			$(document).ready(function() {
				//搜索选项下拉框
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('<span class="caret"></span>');
				});
				//单选组合
				$("input[type='radio'][id='optionsRadios-"+searchDocType+"']").attr("checked","checked");//将保留前页面选项
				//没有任何选项时，设置默认项
				var test = $("input[type='radio'][name='optionsRadios']:checked");
				//alert(test.attr("checked"));
				if('undefined' == typeof test.attr("checked")){
					$("input[type='radio'][id='optionsRadios-all']").attr("checked","checked");
				}

			});

			//回车事件
			document.onkeydown = function (e) {
				var theEvent = window.event || e;
				var code = theEvent.keyCode || theEvent.which;
				if ( code == 13) {
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
	<body id="articlesearch">

		<form id="pagerForm" method="post" action="">
			<input type="hidden" name="searchcount" value="<?php echo $searchcount;?>" />
		</form>

		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<div class="a-box">
					<a class="nav-a frist"  href="<?php echo $CFG->wwwroot; ?>">首页</a>
					<a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/mod/forum/view.php?id=1">微阅</a>
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
			          		<li><a href="#">作者</a></li>
							<li role="separator" class="divider"></li>
							<li><a href="#">上传者</a></li>
			        	</ul>
			      	</div><!-- /btn-group -->
			      	<input id="searchParam" type="text" class="form-control" value="<?php echo ($searchParam != '')?$searchParam:'';  ?>" >
			    </div><!-- /input-group -->
			    <button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
			    
			    <div id="searchDocType" class="radio">
			    	<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios-all" value="all">
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
		
		<!--页面主体-->
		<div class="main">
			<div class="search-blockbox">
				<?php
					foreach($searchResults as $searchResult){
						//获取文档标签
						$tags = $DB->get_records_sql("select dt.* from mdl_doc_tag_link_my dl
														left join mdl_doc_tag_my dt on dl.tagid = dt.id
														where dl.docid = $searchResult->id");
						//获取所属分类
						$category = $DB->get_record_sql("select * from mdl_doc_categories_my dc where dc.id = $searchResult->categoryid");

						echo '<div class="search-block">
									<p class="articlename"><span class="ic '.imagechoise($searchResult->suffix).'"></span>&nbsp;<a href="onlineread.php?docid='.$searchResult->id.'" target="_blank">'.$searchResult->name.'</a></p>
									<p class="articleinfo">'.$searchResult->summary.'</p>
									<p>';
						foreach($tags as $tag){
							echo '<a href="#" class="tips">'.$tag->name.'</a>';
						}
						echo '      </p>
									<span>'.userdate($searchResult->timecreated,"%Y-%m-%d").'</span>
									<span>l</span>
									<span>'.$category->name.'</span>
									<span>l</span>
									<span><a href="#">贡献者:'.$searchResult->uploadername.'</a></span>
									<span><a class="downloadbtn" href="'.$searchResult->url.'" download="" >下载</a></span>
								</div>	';
					}
				?>

				<!--分页-->
				<div style="clear: both;"></div>
				<div class="paging">
					<nav>
						  <?php echo ($searchcount == 0)?'占无相关文档':''; ?>
						  <ul class="pagination" <?php echo ($searchcount == 0)?'style="display: none;"':'style=""'; ?> >
							  <li>
								  <a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&searchDocType=<?php echo $searchDocType; ?>">
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
								  <a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&searchDocType=<?php echo $searchDocType; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
									  <span aria-hidden="true">上一页</span>
								  </a>
							  </li>
							  <?php
							  $totalpage = ceil($searchcount/$prePageNum);
							  for($i=1;$i<=$totalpage;$i++){
								  if($page == $i) {
									  echo ' <li><a class="active" href="searchresult.php?searchType=' . $searchType . '&searchParam=' . $searchParam . '&searchDocType=' . $searchDocType . '&page=' . $i . '">' . $i . '</a></li>';
								  }else{
									  echo ' <li><a class="" href="searchresult.php?searchType=' . $searchType . '&searchParam=' . $searchParam . '&searchDocType=' . $searchDocType. '&page=' . $i . '">' . $i . '</a></li>';
								  }
							  }
							  ?>

							  <li>
								  <?php
								  if(ceil($searchcount/$prePageNum)==0){
									  $nextpage = 1;
								  }elseif(($page+1)>= ceil($searchcount/$prePageNum) ){
									  $nextpage = ceil($searchcount/$prePageNum);
								  }else{
									  $nextpage = $page+1;
								  }
								  ?>
								  <a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&searchDocType=<?php echo $searchDocType; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
									  <span aria-hidden="true">下一页</span>
								  </a>
							  </li>
							  <li>
								  <a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&searchDocType=<?php echo $searchDocType; ?>&page=<?php if(ceil($searchcount/$prePageNum)==0){echo 1;}else{echo ceil($searchcount/$prePageNum);}  ?>">
									  <span aria-hidden="true">尾页</span>
								  </a>
							  </li>
						</ul>
					</nav>
				</div>
				<!--分页 end-->
			</div>
		
			<div class="banner-right">
				<a href="user_upload.php" style="text-decoration:none;"><button class="btn btn-block"><span class="glyphicon glyphicon-open"></span>&nbsp;上传我的文档</button></a>
				<p class="title">相关搜索</p>
				<div class="relatedsearch">
					<?php
						foreach($relateSearchs as $relateSearch ){
							echo '<a class="tips" href="#">'.$relateSearch->name.'</a>';
						}
					?>
				</div>
			</div>
		</div>
		<!--页面主体 end-->



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