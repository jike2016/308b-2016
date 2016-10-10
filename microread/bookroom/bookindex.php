<?php
require_once ("../../config.php");

if(isset($_GET["bookid"]) && $_GET["bookid"] != null){//阅读的书籍id
	$bookid = $_GET["bookid"];
}

require_once ("../loglib.php");
add_microreadviewlog('view',$bookid,1);//添加日志记录

global $DB;
global $USER;
//查找章节的阅读历史
$readHistory = $DB->get_record_sql("select * from mdl_ebook_user_read_my eu
									where eu.userid = $USER->id and eu.ebookid = $bookid ");
if($readHistory){
	$readStr = '继续阅读';
}else{
	$readStr = '在线阅读';
}

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
								where tl.link_id = $book->id
								and tl.link_name = 'mdl_ebook_my'");

//查询各章节
$bookchapters = $DB->get_records_sql("select * from mdl_ebook_chapter_my e
										where e.ebookid = $bookid
										order by e.chapterorder");
//Start 相关书籍
$tagsStr = '';
foreach($tags as $tag){
	$tagsStr .= $tag->id.',';
}
$tagsStrsql = substr($tagsStr,0,strlen($tagsStr)-1);
$recomendbooks = array();
if($tagsStrsql){
	$recomendbooks = $DB->get_records_sql("select em.*,ea.`name` as authorname from mdl_ebook_my em
										left join mdl_tag_link tl on tl.link_id = em.id
										left join mdl_ebook_author_my ea on em.authorid = ea.id
										where tl.tagid in ($tagsStrsql)
										and tl.link_name = 'mdl_ebook_my'
										order by em.timecreated desc");
}
//End 相关书籍

//    获取评价数目页数  20150512
function my_get_book_evaluation_count($bookid)
{
	global $DB;
	$evaluation = $DB->get_records_sql('SELECT id FROM mdl_ebook_comment_my WHERE ebookid = ? ', array($bookid));
	$mycount = count($evaluation);
	$mycount = ceil($mycount/10);
	return ($mycount <= 1 ? 1: $mycount);
}
//	    输出页码   20150512
function my_get_book_evaluation_current_count($count_page, $bookid, $current_page)
{
	global $CFG;
	/** Start 设置评论数的显示页码（只显示5页)*/
	$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
	$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
	for($num = $numstart; $num <= $numend; $num ++)
	{
		if($num == $current_page)
		{
			//  这里需要修改样式标示当前页
			echo '<li><a class="active" href="'.$CFG->wwwroot.'/microread/bookroom/bookindex.php?bookid='.$bookid.'&page='.$num.'">'.$num.'</a></li>';
		}
		else
		{
			echo'<li><a href="'.$CFG->wwwroot.'/microread/bookroom/bookindex.php?bookid='.$bookid.'&page='.$num.'">'.$num.'</a></li>';
		}
	}
}

/** START zzwu 获取文档评价 20160512*/
function my_get_book_evaluation($bookid, $current_page)
{
	$my_page = $current_page * 10;
	global $DB;
	global $OUTPUT;
	$evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_ebook_comment_my a JOIN mdl_user b ON a.userid = b.id WHERE ebookid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($bookid));

	$evaluationhtml = '';
	foreach($evaluation as $value)
	{
		$userobject = new stdClass();
		$userobject->metadata = array();
		$user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
		$userobject->metadata['useravatar'] = $OUTPUT->user_picture (
			$user,
			array(
				'link' => false,
				'visibletoscreenreaders' => false
			)
		);

		$userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
		$evaluationhtml .= '<div class="comment container">
					<div class="comment-l">
						<div class="Learnerimg-box">
							'.$userobject->metadata['useravatar'].'
						</div>
					</div>
					<div class="comment-r">
						<p class="name">'.$value->lastname.$value->firstname.'</p>
						<p class="commentinfo">
							'.$value->comment.'
						</p>
						<p class="time">时间：'.userdate($value->commenttime,'%Y-%m-%d %H:%M').'</p>
					</div>
				</div>';
	}
	echo $evaluationhtml;
}
/** START zzwu 获取文档评价 20160512*/

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
		$(document).ready(function() {
			//评价
			var str = "star";
			var star = "#star";
			var starid1;
			var starid2;
			$('.score .starbox span').click(function() {
				$('.score .starbox span').removeClass('active');
				starid1 = $(this).attr("id");
				for(var i= 1; i<6; i++)
				{
					starid2 = str+i;
					if(starid2 ==starid1 )
					{
						/**   START 书库评分  zzwu 20160512*/
						var score = i * 2;
						$.ajax({
							//D:\WWW\moodle\microread\bookroom\bookscoreandcomment.php
							url: "./bookscoreandcomment.php",
							data: { score: score, ebookid: getQueryString('bookid'), type:'score' },
							success: function(msg){
								if(msg=='1'){
									for(var j=1;j<=score*0.5;j++)
									{
										var starid = star + j;
										$(starid).addClass('active');
									}
								}
								else{
									// alert(msg);
									msg=='2'?alert('评分失败,一个用户只能对一本书评分一次'):alert('评分失败');
								}
							}
						});
						/**   END 书库评分  zzwu 20160512*/
					}
				}
				$('.score #commentword').text("您已评价：");
				$('.score .co').show(1000);
			})
			//评价 end

//			/**   START 书本评论 zzwu 20160512 */
//			$('#score-btn').click(function() {
//				var mycomment = $(this).parent().parent().children(".form-control").val();
//				if(mycomment == ""){
//					alert('请输入评论内容');
//				}
//				else{
//					$.ajax({
//						url: "./bookscoreandcomment.php",
//						data: { comment: mycomment,  ebookid: getQueryString('bookid'), type:'comment' },
//						success: function(msg){
//							if(msg=='1'){
//								location.reload();
//							}
//						}
//					});
//				}
//			});
//			/**   END 书本评论 zzwu 20160512 */

			/**   START 书本评论 zzwu 20160512 */
			$('#score-btn').click(function() {
				var mycomment = $(this).parent().parent().children(".form-control").val();
				var textmy = mycomment;
				textmy = textmy.replace(/[\ |\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,"");
				if(textmy.length <= 10){
					alert('评论内容不能少于10个汉字');
				}
				else{
					$.ajax({
						url: "./bookscoreandcomment.php",
						data: { comment: mycomment,  ebookid: getQueryString('bookid'), type:'comment' },
						success: function(msg){
							if(msg=='1'){
								location.reload();
							}
							else if(msg=='2')
							{
								alert('评论失败，评论内容重复！')
							}
							else {
								alert('评论失败，一分钟內只能评论一次！')
							}
						}
					});
				}
			});
			/**   END 书本评论 zzwu 20160512 */
		})
		
		/**  START 获取书本id zzwu 20160512*/
		function getQueryString(name) {
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
			var r = window.location.search.substr(1).match(reg);
			if (r != null) return unescape(r[2]); return null;
		}
		/**  END 获取书本id zzwu 20160512*/
		
	</script>
</head>
<body id="bookindex">
<!--顶部导航-->
<?php
	require_once ("../common/book_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
	require_once ("../common/book_head_search.php");//书库搜索栏
?>
<!--顶部导航 end-->

<!--书本分类-->
<?php
	require_once ("../common/book_head_classify.php");//书库搜索栏
?>
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
			<p class="titleword infobanner" style="float: left;width: 5%;">简介：&nbsp;&nbsp;</p><p class="titleinfo infobanner" style="float: right; width: 95%;" ><?php echo substr($book->summary,0,645).'...'; ?></p><br />
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
				<a href="onlineread.php?bookid=<?php echo $book->id; ?>" target="_blank" class="functionbtn"><?php echo $readStr; ?></a>
				<a href="<?php echo $book->url; ?>" download="" class="functionbtn">下载此书</a>
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
	<!--书籍介绍 end-->


	<div class="bookcatalog">

		<!--书籍目录-->
		<div class="catalog">
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
										<ul style="font-size:14px;" >';
					foreach($booksections as $booksection){
						echo '<li style="list-style-type:none;" ><a href="onlineread.php?bookid='.$bookid.'&booksectionid='.$booksection->id.'" target="_blank">'.$booksection->name.'</a></li>';
					}
					echo '</ul>
								</p>';

				}
			?>
		</div>
		<!--书籍目录 end-->

		<!-- START 获取评论内容 zzwu 20150512-->
		<!--评论-->
		<div class="commentbox">
			<p class="title">您的评论</p>
			<textarea class="form-control" id="comment-text" placeholder="写下评论支持文档贡献者~"></textarea>
			<p><button id="score-btn" class="btn btn-info">发表评论</button></p>
			<div style="clear: both;"></div>
		</div>
		<!-- END 获取评论内容 zzwu 20150512-->

		<div id="commmentlist">
			<!-- START zzwu 显示评价内容 20150512 -->
			<?php $current_page = isset($_GET['page']) ? $_GET['page'] : 1; my_get_book_evaluation($_GET['bookid'], $current_page-1)?>
			<!-- END zzwu 显示评价内容 20150512 -->
		</div>
		<!--分页-->
		<div style="clear: both;"></div>
		<div class="paging">
			<nav>
				<ul class="pagination">
					<!-- START 修改分页 zzwu 20160512-->
					<?php global $CFG; $count_page = my_get_book_evaluation_count($bookid);?>
					<li>

						<a href="<?php echo $CFG->wwwroot; ?>/microread/bookroom/bookindex.php?bookid=<?php echo $bookid;?>&page=1">
							<span aria-hidden="true">首页</span>
						</a>
					</li>
					<li>
						<a aria-label="Previous" href="<?php echo $CFG->wwwroot; ?>/microread/bookroom/bookindex.php?bookid=<?php echo $bookid;?>&page=<?php echo ($current_page <= 1 ? 1: $current_page - 1); ?>">
							<span aria-hidden="true">上一页</span>
						</a>
					</li>

					<?php my_get_book_evaluation_current_count($count_page, $bookid, $current_page); ?>
					<li>
						<a aria-label="Next" href="<?php echo $CFG->wwwroot; ?>/microread/bookroom/bookindex.php?bookid=<?php echo $bookid;?>&page=<?php echo ($current_page < $count_page ? ($current_page + 1): $count_page); ?>">
							<span aria-hidden="true">下一页</span>
						</a>
					</li>
					<li>
						<a href="<?php echo $CFG->wwwroot; ?>/microread/bookroom/bookindex.php?bookid=<?php echo $bookid;?>&page=<?php echo $count_page; ?>">
							<span aria-hidden="true">尾页</span>
						</a>
					</li>
					<!-- START 修改分页 zzwu 20160512-->
				</ul>
			</nav>
		</div>
		<!--分页 end-->
		<!--评论 end-->
	</div>

	<!--相关图书-->
	<div class="recomendread">
		<div class="score">
			<p id="commentword">评价文档：</p>
			<p class="starbox">
				<span id="star1" class="glyphicon glyphicon-star active"></span>
				<span id="star2" class="glyphicon glyphicon-star"></span>
				<span id="star3" class="glyphicon glyphicon-star"></span>
				<span id="star4" class="glyphicon glyphicon-star"></span>
				<span id="star5" class="glyphicon glyphicon-star"></span>
			</p>
			<p class="co">&nbsp;已评价</p>
		</div>
		<p class="title">
			相关图书
		</p>
		<?php
			$num = 0;
			foreach($recomendbooks as $recomendbook){
				if($bookid == $recomendbook->id){//去掉当前课程
					continue;
				}
				if($num == 4){//只提供4本书的相关推荐
					break;
				}
				echo '<div class="bookbox">
										<a href="bookindex.php?bookid='.$recomendbook->id.'"><img src="'.$recomendbook->pictrueurl.'" width="68" height="100" /></a>
										<div class="bookinfobox">
											<a href="bookindex.php?bookid='.$recomendbook->id.'"><p class="bookname">'.$recomendbook->name.'</p></a>
											<p>'.$recomendbook->authorname.'</p>
											<p>'.mb_substr($recomendbook->summary,0,96,'utf-8').'...'.'</p>
											<p>'.userdate($recomendbook->timecreated,'%Y年%m月%d日').'</p>
										</div>
									</div>';
				$num++;
			}
		?>
	</div>
	<!--相关图书 end-->
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
</html>
