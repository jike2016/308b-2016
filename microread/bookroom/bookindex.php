<?php
require_once ("../../config.php");

if(isset($_GET["bookid"]) && $_GET["bookid"] != null){//阅读的书籍id
	$bookid = $_GET["bookid"];
}

require_once ("../loglib.php");
addbookviewlog($bookid);//添加日志记录

global $DB;

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
		<title><?php echo $book->name; ?></title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/bookindex.css" />
		<link rel="stylesheet" href="../css/bookroomallpage.css" />
		
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
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

	<body id="bookindex">
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
			    		<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
			    		全部字段
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
			    		标题
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		主讲人
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
						<a href="onlineread.php?bookid=<?php echo $book->id; ?>" target="_blank" class="functionbtn">在线阅读</a>
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
			
			<!--相关图书-->
			<div class="recomendread">
				<p class="title">
					相关图书
				</p>
				<div class="bookbox">
					<img src="img/tushu_1.jpg" />
					<div class="bookinfobox">
						<a href="#"><p class="bookname">是开发商了敬爱分三份了敬爱分三份了敬爱分三份了敬爱分三份</p></a>
						<p>宋总</p>
						<p>即可萨菲隆出版社</p>
						<p>2013年3月</p>
					</div>
				</div>
				<div class="bookbox">
					<img src="img/tushu_2.jpg" />
					<div class="bookinfobox">
						<a href="#"><p class="bookname">是开发商了敬爱分三份了敬爱分三份了敬爱分三份了敬爱分三份</p></a>
						<p>宋总</p>
						<p>即可萨菲隆出版社</p>
						<p>2013年3月</p>
					</div>
				</div>
				<div class="bookbox">
					<img src="img/tushu_3.jpg" />
					<div class="bookinfobox">
						<a href="#"><p class="bookname">是开发商了敬爱分三份了敬爱分三份了敬爱分三份了敬爱分三份</p></a>
						<p>宋总</p>
						<p>即可萨菲隆出版社</p>
						<p>2013年3月</p>
					</div>
				</div>
				<div class="bookbox">
					<img src="img/tushu_4.jpg" />
					<div class="bookinfobox">
						<a href="#"><p class="bookname">是开发商了敬爱分三份了敬爱分三份了敬爱分三份了敬爱分三份</p></a>
						<p>宋总</p>
						<p>即可萨菲隆出版社</p>
						<p>2013年3月</p>
					</div>
				</div>
			</div>
			<!--相关图书 end-->
			
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
