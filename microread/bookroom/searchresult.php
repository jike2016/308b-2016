<?php
require_once ("../../config.php");

$page = optional_param('page', 1, PARAM_INT);//分页页号
$prePageNum = 12;//每页显示的记录数

if(isset($_GET["searchType"]) && $_GET["searchType"] != null){//搜索类型
	$searchType = $_GET["searchType"];
}
if(isset($_GET["searchParam"]) && $_GET["searchParam"] != null){//搜索参数
	$searchParam = $_GET["searchParam"];
}

global $DB;
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_ebook_categories_my e where e.parent = 0");//获取顶级分类
switch($searchType){
	case '全部':
		$sql = "where em.name like '%$searchParam%' or ea.name like '%$searchParam%' or em.summary like '%$searchParam%' or u.firstname like '%$searchParam%'";
		break;
	case '标题':
		$sql = "where em.name like '%$searchParam%'";
		break;
	case '作者':
		$sql = "where ea.name like '%$searchParam%'";
		break;
	case '上传者':
		$sql = "where u.firstname like '%$searchParam%'";
		break;
	default:
		break;
}

//查询结果
$index = ($page-1)*$prePageNum;//从第几条记录开始
$searchResults = $DB->get_records_sql("select em.*,ea.name as authorname,u.firstname as uploadername from mdl_ebook_my em
								left join mdl_ebook_author_my ea on em.authorid = ea.id
								left join mdl_user u on em.uploaderid = u.id
								$sql
								order by em.timecreated desc
								limit $index,$prePageNum ");

//如果还没有设置过查询结果的数量
if(isset($_POST["searchcount"])){
	$searchcount = $_POST['searchcount'];
}else{
	$searchResultsCount = $DB->get_record_sql("select count(1) as totalcount from mdl_ebook_my em
												left join mdl_ebook_author_my ea on em.authorid = ea.id
												left join mdl_user u on em.uploaderid = u.id
												$sql
											");
	$searchcount = $searchResultsCount->totalcount;
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>书库搜索结果页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookroom_searchresult.css" />
		<link rel="stylesheet" href="../css/bookroomallpage.css" />	
		
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

		<script>

			//搜索选项下拉框
			$(document).ready(function() {
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('<span class="caret"></span>');
				});
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
				var searchType = document.getElementById("searchtypebtn");//获取查询参数
				var searchParam = document.getElementById("searchParam");//获取选项
				window.location.href="searchresult.php?searchType="+searchType.textContent+"&searchParam="+searchParam.value;
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
	<body id="bookroom_searchresult">

		<form id="pagerForm" method="post" action="">
			<input type="hidden" name="searchcount" value="<?php echo $searchcount;?>" />
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
									<li><a href="user_upload.php">上传电子书</a></li>
									<li role="separator" class="divider"></li>
									<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>
								</ul>
							</div>';
					};
				?>
			</div>
		</div>
		
		<div class="header-banner">
			<a href="index.php"><img  src="../img/shuku_logo.png"/></a>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ($searchType != '')?$searchType :'全部'; ?><span class="caret"></span></button>
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
					<input id="searchParam" type="text" class="form-control"  value="<?php echo ($searchParam != '')?$searchParam:'';  ?>">
			    </div><!-- /input-group -->
			    <button onclick="search()" id="search_btn" class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
			    
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
			<?php echo ($searchcount == 0)?'<p style="margin-top: 30px; text-align: center">暂无相关书籍</p>':''; ?>
			<?php
				foreach($searchResults as $searchResult){
					echo '<div class="book-block">
								<a href="bookindex.php?bookid='.$searchResult->id.'" target="_blank"><img src="'.$searchResult->pictrueurl.'" width="105" height="150"/></a>
								<div class="book-info-box">
									<a href="bookindex.php?bookid='.$searchResult->id.'" target="_blank"><p class="bookname">'.$searchResult->name.'</p></a>
									<p class="writer">作者：<a href="bookauthor.php?authorid='.$searchResult->authorid.'" target="_blank" >'.$searchResult->authorname.'</a>&nbsp;&nbsp;（上传者：'.$searchResult->uploadername.'）</p>
									<p class="bookinfo">'.substr($searchResult->summary,0,270).'...'.'</p>
									<p>';

					//获取书籍的标签
					$tags = $DB->get_records_sql("select tm.id,tm.tagname from mdl_tag_link tl
								left join mdl_tag_my tm on tl.tagid = tm.id
								where tl.link_id = $searchResult->id
								and tl.link_name = 'mdl_ebook_my'");
					foreach($tags as $tag){
						echo '<a class="tips">'.$tag->tagname.'</a>';
					}
					echo '			</p>
								</div>
							</div>';
				}
			?>
		</div>
		<!--页面主体 end-->

		<!--分页-->
		<div style="clear: both;"></div>
		<div class="paging" style="text-align: center;">
			<nav>
				<ul class="pagination" <?php echo ($searchcount == 0)?'style="display: none;"':'style=""'; ?> >
					<li>
						<a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>">
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
						<a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&page=<?php echo $prepage; ?>" aria-label="Previous">
							<span aria-hidden="true">上一页</span>
						</a>
					</li>
					<?php
						$totalpage = ceil($searchcount/$prePageNum);
						for($i=1;$i<=$totalpage;$i++){
							if($page == $i) {
								echo ' <li><a class="active" href="searchresult.php?searchType=' . $searchType . '&searchParam=' . $searchParam . '&page=' . $i . '">' . $i . '</a></li>';
							}else{
								echo ' <li><a class="" href="searchresult.php?searchType=' . $searchType . '&searchParam=' . $searchParam . '&page=' . $i . '">' . $i . '</a></li>';
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
						<a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&page=<?php echo $nextpage; ?>" aria-label="Next">
							<span aria-hidden="true">下一页</span>
						</a>
					</li>
					<li>
						<a href="searchresult.php?searchType=<?php echo $searchType; ?>&searchParam=<?php echo $searchParam; ?>&page=<?php if(ceil($searchcount/$prePageNum)==0){echo 1;}else{echo ceil($searchcount/$prePageNum);}  ?>">
							<span aria-hidden="true">尾页</span>
						</a>
					</li>
				</ul>
			</nav>
		</div>
		<!--分页 end-->
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
