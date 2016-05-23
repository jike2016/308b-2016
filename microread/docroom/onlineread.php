<?php
//在线阅读页面
require_once ("../../config.php");

if(isset($_GET["docid"]) && $_GET["docid"] != null){//阅读的书籍id
	$docid = $_GET["docid"];
}else{
	exit;
}

require_once ("../loglib.php");
add_microreadviewlog('view',$docid,2);//添加日志记录

global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

//获取文档信息
$doc = $DB->get_record_sql("select dm.*,u.firstname as uploadername from mdl_doc_my dm
							left join mdl_user u on dm.uploaderid = u.id
							where dm.id = $docid");
//获取swf文件路劲，为下文中的文档查看器脚本赋值
echo "<script>var swfurl = '$doc->swfurl';</script>";//注意拼接的格式

//获取阅读数量
$readcount = $DB->get_record_sql("select count(1) as readcount from mdl_microread_log ml
									where ml.action = 'view'
									and ml.contextid = $docid
									and ml.target = 2");
//获取所属分类
$docclassification = $DB->get_record_sql("select dc1.id,dc1.`name`,dc2.id as secondid,dc2.`name` as secondname,dc3.id as topid,dc3.`name`as topname from mdl_doc_categories_my dc1
									left join mdl_doc_categories_my dc2 on dc1.parent = dc2.id
									left join mdl_doc_categories_my dc3 on dc2.parent = dc3.id
									where dc1.id = $doc->categoryid");
//获取标签,获取相关文档推荐
$tags = $DB->get_records_sql("select dtl.* from mdl_doc_tag_link_my dtl where dtl.docid = $docid");
$stagidarray = array();
foreach($tags as $tag){
	$stagidarray[] = $tag->tagid;
}
$tagStr = implode(',',$stagidarray);
if($tagStr != ''){
	$docrecomends = $DB->get_records_sql("select dm.* from mdl_doc_tag_link_my dt
											left join mdl_doc_my dm on dt.docid = dm.id
											where dt.tagid in ($tagStr)
											and dm.id != $docid
											order by dm.timecreated desc
											limit 0,4");
}

//文件类型的判断
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

//    获取评价数目页数  20150512
function my_get_doc_evaluation_count($docid)
{
	global $DB;
	$evaluation = $DB->get_records_sql('SELECT id FROM mdl_doc_comment_my WHERE docid = ? ', array($docid));
	$mycount = count($evaluation);
	$mycount = ceil($mycount/10);
	return ($mycount <= 1 ? 1: $mycount);
}
//	    输出页码   20150512
function my_get_doc_evaluation_current_count($count_page, $docid, $current_page)
{
	global $CFG;
	/** Start 设置评论数的显示页码（只显示5页)*/
	$numstart = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page - 2):1):($count_page - 4)):1;
	$numend = ($count_page > 5)?(($current_page < $count_page - 2)?(($current_page > 2)?($current_page + 2):5):($count_page)):$count_page;
	for($num = $numstart; $num <= $numend; $num ++)
	{
		if($num == $current_page)
		{
			echo '<li><a class="active" href="'.$CFG->wwwroot.'/microread/docroom/onlineread.php?docid='.$docid.'&page='.$num.'">'.$num.'</a></li>';
		}
		else
		{
			echo'<li><a href="'.$CFG->wwwroot.'/microread/docroom/onlineread.php?docid='.$docid.'&page='.$num.'">'.$num.'</a></li>';
		}
	}
}

/** START zzwu 获取文档评价 20160512*/
function my_get_doc_evaluation($docid, $current_page)
{
	$my_page = $current_page * 10;
	global $DB;
	global $OUTPUT;
	$evaluation = $DB->get_records_sql('SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_doc_comment_my a JOIN mdl_user b ON a.userid = b.id WHERE docid = ? ORDER BY commenttime DESC LIMIT '.$my_page.',10', array($docid));

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

/** START zzwu 获取总分和评价人数 20160512*/
function get_doc_sumcore_count($docid)
{
	global $DB;
	$docresult = $DB->get_records_sql('SELECT id, sumscore, scorecount FROM mdl_doc_sumscore_my  WHERE docid = '.$docid);
	$docsumcore = new stdClass();
	foreach($docresult as $value)
	{
		$docsumcore->sumscore = $value->sumscore;
		$docsumcore->scorecount = $value->scorecount;
	}
	return $docsumcore;
}


?>



<!doctype html>
<html>
<head>
    <title><?php echo $doc->name;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />
    <link rel="stylesheet" href="../css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../css/flexpaper.css" />
    <link rel="stylesheet" href="../css/docallpage.css" />
    <link rel="stylesheet" href="../css/docread.css" />
    <script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../js/flexpaper.js"></script>
	<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="../js/flexpaper_handlers.js"></script>

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
							url: "./docscoreandcomment.php",
							data: { score: score, docid: getQueryString('docid'), type:'score' },
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
									msg=='2'?alert('评分失败,一个用户只能对一篇文档评分一次'):alert('评分失败');
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
			
			/**   START 文档评论 zzwu 20160512 */
			$('#score-btn').click(function() {
				var mycomment = $(this).parent().parent().children(".form-control").val();
				if(mycomment == ""){
					alert('请输入评论内容');
				}
				else{
					$.ajax({
						url: "./docscoreandcomment.php",
						data: { comment: mycomment,  docid: getQueryString('docid'), type:'comment' },
						success: function(msg){
							if(msg=='1'){
								location.reload();
							}
						}
					});
				}
			});
			/**   END 文档评论 zzwu 20160512 */

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
<body id="docread">
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
<!--			<div class="btn-group" style="float: left;">-->
<!--			  	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--			  		<img src="../img/tushuFenlei.png">-->
<!--			  	</a>-->
<!--			  	<ul class="dropdown-menu">-->
<!--			    	<li><a href="#">现代</a></li>-->
<!--			    	<li role="separator" class="divider"></li>-->
<!--			    	<li><a href="#">军事</a></li>-->
<!--			    	<li role="separator" class="divider"></li>-->
<!--			    	<li><a href="#">战争</a></li>-->
<!--			    	<li role="separator" class="divider"></li>-->
<!--			    	<li><a href="#">科技</a></li>-->
<!--			  	</ul>-->
<!--			</div>-->
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
	
	<div class="main">
		<div class="banner-left">
			<p class="type">
				<a href="index.php">慕课文库</a>
				<?php
					//如果有三个级别的标题，则：id是三级，secondid是二级，thirdid是顶级
					//如果有二个级别的标题，则：id是二级，secondid是顶级，thirdid为null
					//如果只有一个级别的标题，则：id是顶级，secondid是null，thirdid为null
					if($docclassification->secondid == null){//表示当前分类只有一个级别标题
						echo '&nbsp;>&nbsp;<a class="currentpage" href="classify.php?docclassid='.$docclassification->id.'" >'.$docclassification->name.'</a>';
					}else{
						if($docclassification->topid == null){//表示当前分类只有二个级别标题
							echo '&nbsp;>&nbsp;<a href="classify.php?docclassid='.$docclassification->secondid.'" >'.$docclassification->secondname.'</a>';
							echo '&nbsp;>&nbsp;<a class="currentpage" href="classify.php?docclassid='.$docclassification->secondid.'&docsecondclassid='.$docclassification->id.'" >'.$docclassification->name.'</a>';
						}else{//当前分类有三个级别标题
							echo '&nbsp;>&nbsp;<a href="classify.php?docclassid='.$docclassification->topid.'" >'.$docclassification->topname.'</a>';
							echo '&nbsp;>&nbsp;<a href="classify.php?docclassid='.$docclassification->topid.'&docsecondclassid='.$docclassification->secondid.'" >'.$docclassification->secondname.'</a>';
							echo '&nbsp;>&nbsp;<a class="currentpage" href="classify.php?docclassid='.$docclassification->topid.'&docsecondclassid='.$docclassification->secondid.'&docthirdclassid='.$docclassification->id.'" >'.$docclassification->name.'</a>';
						}
					}

				?>
			</p>
			
			<p class="docname"><span class="ic <?php echo imagechoise($doc->suffix); ?>"></span>&nbsp;<?php echo $doc->name;?></p>
			
			<p class="docinfo">
				<a class="writer" href="#"><span class="ic ic-user"></span>&nbsp;<?php echo $doc->uploadername;?></a>&nbsp;
				<a>上传于</a>&nbsp;
				<a><?php echo userdate($doc->timecreated,'%Y-%m-%d'); ?></a>&nbsp;&nbsp;&nbsp;l&nbsp;&nbsp;
				<a class="starbox">
					<?php $sumcore = get_doc_sumcore_count($_GET['docid']);
					for($i = 1; $i <= (int)ceil($sumcore->sumscore/2); $i ++)
					{
						echo '<span class="glyphicon glyphicon-star active"></span>';
					}
					for($i = (int)ceil($sumcore->sumscore); $i <= 5; $i ++)
					{
						echo '<span class="glyphicon glyphicon-star"></span>';
					}
					?>
				</a>
				<a>（<?php echo $sumcore->scorecount > 0 ? $sumcore->scorecount : 0; ?>人评价）</a>&nbsp;&nbsp;l&nbsp;&nbsp;
				<a><?php echo $readcount->readcount; ?>人阅读</a>
			</p>
			
			<!--阅读器-->
			<div id="documentViewer" class="flexpaper_viewer"></div>
			<!--阅读器 end-->			
			
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
				<?php $current_page = isset($_GET['page']) ? $_GET['page'] : 1; my_get_doc_evaluation($_GET['docid'], $current_page-1)?>
				<!-- END zzwu 显示评价内容 20150512 -->
			</div>
			<!--评论 end-->

			<!--分页-->
			<div style="clear: both;"></div>
			<div class="paging">
			<nav>
			  	<ul class="pagination">
			  		<!-- START 修改分页 zzwu 20160512-->
					<?php global $CFG; $count_page = my_get_doc_evaluation_count($docid);?>
			  		<li>

					<a href="<?php echo $CFG->wwwroot; ?>/microread/docroom/onlineread.php?docid=<?php echo $docid;?>&page=1">
			       	 		<span aria-hidden="true">首页</span>
			      		</a>
			    	</li>
			    	<li>
			     	 	<a aria-label="Previous" href="<?php echo $CFG->wwwroot; ?>/microread/docroom/onlineread.php?docid=<?php echo $docid;?>&page=<?php echo ($current_page <= 1 ? 1: $current_page - 1); ?>">
			       	 		<span aria-hidden="true">上一页</span>
			      		</a>
			    	</li>

					<?php my_get_doc_evaluation_current_count($count_page,$docid, $current_page); ?>
				    <li>
				      	<a aria-label="Next" href="<?php echo $CFG->wwwroot; ?>/microread/docroom/onlineread.php?docid=<?php echo $docid;?>&page=<?php echo ($current_page < $count_page ? ($current_page + 1): $count_page); ?>">
				       		<span aria-hidden="true">下一页</span>
				      	</a>
			    	</li>
			    	<li>
			     	 	<a href="<?php echo $CFG->wwwroot; ?>/microread/docroom/onlineread.php?docid=<?php echo $docid;?>&page=<?php echo $count_page; ?>">
			       	 		<span aria-hidden="true">尾页</span>
			      		</a>
			    	</li>
					<!-- START 修改分页 zzwu 20160512-->
			  	</ul>
			</nav>
			</div>
			<!--分页 end-->
		</div>		
		<div class="banner-right">
			<a href="<?php echo $doc->url; ?>" download="" style="text-decoration:none;" ><button class="btn btn-block downloadbtn"><span class="glyphicon glyphicon-download-alt"></span>&nbsp;下载此文档</button></a>
			<div class="score">
				<p id="commentword">评价文档：</p>
				<p class="starbox">
					<span id="star1" class="glyphicon glyphicon-star active"></span>
					<span id="star2" class="glyphicon glyphicon-star"></span>
					<span id="star3" class="glyphicon glyphicon-star"></span>
					<span id="star4" class="glyphicon glyphicon-star"></span>
					<span id="star5" class="glyphicon glyphicon-star"></span>
				</p>
				<p class="co">&nbsp;您已评价</p>
			</div>
			
			<div class="recommenddoc">
				<p class="title">相关文档推荐</p>
				<?php
					foreach($docrecomends as $docrecomend){
						$doctype2 = imagechoise($docrecomend->suffix);
						echo '<p><a href="onlineread.php?docid='.$docrecomend->id.'"><span class="ic '.$doctype2.'"></span>&nbsp;'.$docrecomend->name.'</a></p>';
					}

				?>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
	    var startDocument = "Paper";
//		var swfurl = "";//这个值在大概20行附近给出：echo "<script>var swfurl = $doc->swfurl;<\/script>";
	    $('#documentViewer').FlexPaperViewer(
	           { config : {

	                SWFFile : swfurl,
	                Scale : 0.6,
	                ZoomTransition : 'easeOut',
	                ZoomTime : 0.5,
	                ZoomInterval : 0.2,
	                FitPageOnLoad : true,
	                FitWidthOnLoad : true,//自适应宽度
	                FullScreenAsMaxWindow : false,
	                ProgressiveLoading : false,
	                MinZoomSize : 0.2,
	                MaxZoomSize : 5,
	                SearchMatchAll : false,
	                InitViewMode : 'Portrait',
	                RenderingOrder : 'flash',
	                StartAtPage : '',
					//ProgressiveLoading: true,//当设置为true的时候，展示文档时不会加载完整个文档，而是逐步加载，但是需要将文档转化为9以上的flash版本（使用pdf2swf的时候使用-T 9 标签）。
					
	                ViewModeToolsVisible : true,
	                ZoomToolsVisible : true,
	                NavToolsVisible : true,
	                CursorToolsVisible : true,
	                SearchToolsVisible : true,
	                WMode : 'window',
	                localeChain: 'zh_CN'
	            }}
	    	);
	</script>

	<!--页面右下角按钮 Start-->
	<?php
		require_once ("../common/all_note_chat.php");//右下角链接：笔记、聊天、收藏、、、、
	?>
	<!--页面右下角按钮 end-->
	
	<!--底部导航条-->
	<div style="clear: both;"></div>
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