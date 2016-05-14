<?php

require_once ("../../config.php");
global $DB;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

//获取推荐分类
$doccategoryrecomends = $DB->get_records_sql("select dc.id as `index`,dcm.`name` as categoryname,dc.docid,dm.* from mdl_doc_category_recommend_my dc
												left join mdl_doc_categories_my dcm on dc.categoryid = dcm.id
												left join mdl_doc_my dm on dc.docid = dm.id");
//获取推荐贡献者
$doccontributorsrecomends = $DB->get_records_sql("select dr.*,u.firstname as contribuname from mdl_doc_recommend_authorlist_my dr
													left join mdl_user u on dr.userid = u.id");
//Start 热门贡献榜
$doccontributorslists = $DB->get_records_sql("select dm.uploaderid,count(1) as rankcount,u.firstname as uploadusername from mdl_doc_my dm
												left join mdl_microread_log ml on dm.id = ml.contextid
												left join mdl_user u on u.id = dm.uploaderid
												where ml.action = 'view'
												and ml.target = 2
												group by dm.uploaderid
												order by rankcount desc
												limit 0,10");
if(count($doccontributorslists)<10){
	$i = 10 - count($doccontributorslists);
	for($i;$i>0;$i--){
		$doccontributorslists[] = '';
	}
}
//End 热门贡献榜

//Start 热门排行榜
$weektime = time()-3600*24*7;//一周前
$monthtime = time()-3600*24*30;//一月前

$weekranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_microread_log ml
									left join mdl_doc_my dm on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									and ml.timecreated > $weektime
									group by ml.contextid
									order by rankcount desc
									limit 0,10 ");
$monthranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_microread_log ml
									left join mdl_doc_my dm on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									and ml.timecreated > $monthtime
									group by ml.contextid
									order by rankcount desc
									limit 0,10");
$totalranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_microread_log ml
									left join mdl_doc_my dm on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									group by ml.contextid
									order by rankcount desc
									limit 0,10");
$weekrankarray = array();//显示书名信息
$monthrankarray = array();
$totalrankarray = array();
$weekhrefarray = array();//链接路径
$monthhrefarray = array();
$totalhrefarray = array();

foreach($weekranks as $weekrank ){
	$weekrankarray[] = $weekrank->docname.$weekrank->doctype.' -- '.$weekrank->rankcount;
	$weekhrefarray[] = 'onlineread.php?docid='.$weekrank->contextid;
}
if(count($weekrankarray)<10){
	$i = 10 - count($weekrankarray);
	for($i;$i>0;$i--){
		$weekrankarray[] = ' ';
		$weekhrefarray[] = '#';
	}
}
foreach($monthranks as $monthrank ){
	$monthrankarray[] = $monthrank->docname.$monthrank->doctype.' -- '.$monthrank->rankcount;
	$monthhrefarray[] = 'onlineread.php?docid='.$monthrank->contextid;
}
if(count($monthrankarray)<10){
	$i = 10 - count($monthrankarray);
	for($i;$i>0;$i--){
		$monthrankarray[] = ' ';
		$monthhrefarray[] = '#';
	}
}
foreach($totalranks as $totalrank ){
	$totalrankarray[] = $totalrank->docname.$totalrank->doctype.' -- '.$totalrank->rankcount;
	$totalhrefarray[] = 'onlineread.php?docid='.$totalrank->contextid;
}
if(count($totalrankarray)<10){
	$i = 10 - count($totalrankarray);
	for($i;$i>0;$i--){
		$totalrankarray[] = ' ';
		$totalhrefarray[] = '#';
	}
}

$weekrankStr = '"';
$weekrankStr .= implode('","',$weekrankarray);
$weekrankStr .= '"';
$weekhrefStr = '"';
$weekhrefStr .= implode('","',$weekhrefarray);
$weekhrefStr .= '"';

$monthrankStr = '"';
$monthrankStr .= implode('","',$monthrankarray);
$monthrankStr .= '"';
$monthhrefStr = '"';
$monthhrefStr .= implode('","',$monthhrefarray);
$monthhrefStr .= '"';

$totalrankStr = '"';
$totalrankStr .= implode('","',$totalrankarray);
$totalrankStr .= '"';
$totalhrefStr = '"';
$totalhrefStr .= implode('","',$totalhrefarray);
$totalhrefStr .= '"';

//End 热门排行榜

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

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>文库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/docindex.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/rank.js" ></script>
		<script>
			var moothrank = new Array(<?php echo $monthrankStr; ?>); //月书单
			var moothrank_href = new Array(<?php echo $monthhrefStr; ?>); //月书单链接

			var weekrank = new Array(<?php echo $weekrankStr; ?>); //周书单
			var weekrank_href = new Array(<?php echo $weekhrefStr; ?>); //周书单链接

			var totalrank = new Array(<?php echo $totalrankStr; ?>); //总书单
			var totalrank_href = new Array(<?php echo $totalhrefStr; ?>); //总书单
		</script>

		<script>
			$(document).ready(function() {
				//搜索选项下拉框
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('&nbsp;<span class="caret"></span>');
				});
//				//单选组合
//				$("input[type='radio'][name='optionsRadios']").removeAttr("checked");
//				$("input[type='radio'][id='optionsRadios-"+searchDocType+"']").attr("checked","checked");

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
	<body id="articleindex">
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
			<a href="#"><img  src="../img/logo_WenKu.png"/></a>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
					<div class="input-group-btn">
						<button type="button" id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo ($searchType != '')?$searchType :'全部&nbsp;'; ?><span class="caret"></span></button>
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
			<!--权威发布-->
			<div class="authority-release">
				<?php
					$index = 0;
					foreach($doccategoryrecomends as $doccategoryrecomend){
						if($index%4==0){
							echo '<p class="title">'.$doccategoryrecomend->categoryname.'></p>
							<!--第一行-->
							<div class="banner">';
						}
						if($index%4==0) {
							echo '<div class="articleblock frist ">
											<a href="onlineread.php?docid='.$doccategoryrecomend->id.'"><img src="'.$doccategoryrecomend->pictrueurl.'" width="272" height="125" /></a>
											<div>
												<a href="onlineread.php?docid='.$doccategoryrecomend->id.'">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
						}else{
							echo '<div class="articleblock">
											<a href="onlineread.php?docid='.$doccategoryrecomend->id.'"><img src="'.$doccategoryrecomend->pictrueurl.'"  width="272" height="125" /></a>
											<div>
												<a href="onlineread.php?docid='.$doccategoryrecomend->id.'">'.$doccategoryrecomend->name.'</a>
												<p>'.$doccategoryrecomend->summary	.'</p>
											</div>
										</div>';
						}
						if((($index+1)%4)==0){
							echo '<div style="clear: both;"></div>
									</div>
									<!--第一行 end-->';
						}
						$index++;
					}

				?>
				<div style="clear: both;"></div>
			</div>
			<!--权威发布 end-->
			
			<!--贡献作者推荐榜-->
			<div class="contribution-writer">
				<p class="title">贡献作者推荐榜></p>
				<!--第一行-->
				<?php
					$index = 0;
					foreach($doccontributorsrecomends as $doccontributorsrecomend){
						//统计当前贡献者的文献数
						$doccount = $DB->get_record_sql("select count(1) as docnum from mdl_doc_my dm where dm.uploaderid = $doccontributorsrecomend->userid");
						if(($index%4)==0){
							echo '<div class="writerblock frist">';
						}else{
							echo '<div class="writerblock">';
						}
						echo '<div class="userinfo-box">
									<div class="userimg">
										<img src="'.getUserIcon($doccontributorsrecomend->userid).'" width="64" height="64"/>
									</div>
									<div class="userinfo">
										<p class="name">'.$doccontributorsrecomend->contribuname	.'</p>
										<p class="articlenum">'.$doccount->docnum.'</p>
										<p class="articlenumw">篇文档</p>
									</div>
								</div>
								<div class="articlelist">';
						//获取其3篇文章
						if($doccontributorsrecomend->docid1 != null && $doccontributorsrecomend->docid1 != -1){
							$doc1 = $DB->get_record_sql("select * from mdl_doc_my dm where dm.id = $doccontributorsrecomend->docid1");
							$doctype = imagechoise($doc1->suffix);
							echo '<a href="#">
										<p class="pa">
											<a class="ca" href="onlineread.php?docid='.$doc1->id.'"><span class="ic '.$doctype.'"></span>&nbsp;'.$doc1->name.'</a>
											<span class="score">4.3分</span>
										</p>
									</a>';
						}
						if($doccontributorsrecomend->docid2 != null && $doccontributorsrecomend->docid2 != -1){
							$doc2 = $DB->get_record_sql("select * from mdl_doc_my dm where dm.id = $doccontributorsrecomend->docid2");
							$doctype = imagechoise($doc2->suffix);
							echo '<a href="#">
										<p class="pa">
											<a class="ca" href="onlineread.php?docid='.$doc2->id.'"><span class="ic '.$doctype.'"></span>&nbsp;'.$doc2->name.'</a>
											<span class="score">4.3分</span>
										</p>
									</a>';
						}
						if($doccontributorsrecomend->docid3 != null && $doccontributorsrecomend->docid3 != -1){
							$doc3 = $DB->get_record_sql("select * from mdl_doc_my dm where dm.id = $doccontributorsrecomend->docid3");
							$doctype = imagechoise($doc3->suffix);
							echo '<a href="#">
										<p class="pa">
											<a class="ca" href="onlineread.php?docid='.$doc3->id.'"><span class="ic '.$doctype.'"></span>&nbsp;'.$doc3->name.'</a>
											<span class="score">4.3分</span>
										</p>
									</a>';
						}

						echo'
								</div>
							</div>';
						$index++;
					}
					//文件类型判断
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

				?>
				<!--第一行 end-->

			</div>
			<!--贡献作者推荐榜 end-->

			<div style="clear: both;"></div>
			
			<!--热门作者榜、评分榜、热门排行榜-->
			<div class="rank-box">
				<!--热门作者榜-->
				<div class="popular-writer">
					<div class="title">热门贡献榜</div>
					<div class="ranklist">
						<?php
							$no = 1;
							foreach($doccontributorslists as $doccontributorslist){
								if($no<4){
									echo '<div class="ranklist-block">
												<div class="w-num"><a class="ranknum top3">'.$no.'</a></div>
												<div class="w-name"><a class="writername">'.$doccontributorslist->uploadusername.'&nbsp;--&nbsp;'.$doccontributorslist->rankcount.'</a></div>
											</div>';
								}else{
									echo '<div class="ranklist-block">
												<div class="w-num"><a class="ranknum">'.$no.'</a></div>
												<div class="w-name"><a class="writername">'.$doccontributorslist->uploadusername.'&nbsp;--&nbsp;'.$doccontributorslist->rankcount.'</a></div>
											</div>';
								}
								$no++;
							}
						?>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门作者榜 end-->
				
				<!--评分榜-->
				<div class="score">
					<div class="title">评分榜</div>
					<div class="ranklist">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--评分榜 end-->
				
				<!--热门排行榜-->
				<div class="top-charts">
					<div class="title hotrank">热门排行榜</div>
					<div class="title week">周</div>
					<div class="title mooth">月</div>					
					<div class="title total">总</div>
					<div style="clear: both;"></div>
					<div class="ranklist final">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname"></a>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门排行榜 end-->
			</div>
			<!--热门作者榜、评分榜、热门排行榜 end-->			
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
