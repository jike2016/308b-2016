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
addbookviewlog('view',$bookid,1);//添加日志记录

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
		<script>
			//搜索选项下拉框
			$(document).ready(function() {
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('&nbsp;<span class="caret"></span>');
				});
			});
			//回车事件
			document.onkeydown = function (e) {
				var theEvent = window.event || e;
				var code = theEvent.keyCode || theEvent.which;
				if (  $('#searchParam').val() != '' &&  code == 13) {
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
	<body id="bookroom_readonline">
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
						<button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">全部&nbsp;<span class="caret"></span></button>
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
