<?php
require_once ("../../config.php");
require_login();
global $DB;
global $USER;
$bookclasses = $DB->get_records_sql("select e.id,e.name from mdl_doc_categories_my e where e.parent = 0");//获取顶级分类

if(isset($_POST['hasupload'])&&$_POST['hasupload']==1){
	//处理数据
	$currenttime=time();
	$ranknum = rand(100, 200);//随机数
	$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
	$picfilestr=strtolower($picfilestr);//全小写
	$urlfilestr=strrchr($_FILES['docurl']['name'],'.');//url后缀名
	$urlfilestr=strtolower($urlfilestr);
	// move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docpic/" . $currenttime.$_FILES["pictrueurl"]["name"]);
	move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docpic/" . $currenttime.$ranknum.$picfilestr);
	move_uploaded_file($_FILES["docurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docfordownload/" . $currenttime.$ranknum.$urlfilestr);
	$newdoc=new stdClass();
	$newdoc->upload_userid= $USER->id;
	$newdoc->admin_check= 0;
	$newdoc->name= $_POST['docname'];
	$newdoc->summary= $_POST['summary'];
	$newdoc->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/user_upload/docfordownload/'. $currenttime.$ranknum.$urlfilestr;
	$newdoc->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/user_upload/docpic/'. $currenttime.$ranknum.$picfilestr;
	$newdoc->timecreated= $currenttime;
	$newdoc->suffix = strrchr($_FILES['docurl']['name'],'.');
	$newdoc->size= number_format(($_FILES["docurl"]["size"] / 1048576),1).'MB';
	$docid=$DB->insert_record('doc_user_upload_my',$newdoc,true);
	echo '上传成功!请等待管理员审核，5秒后页面将自动跳转';
	//等待3秒后跳转
	header("refresh:3;url=http://".$_SERVER['HTTP_HOST']."/moodle/microread/bookroom");
	exit;
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>用户上传文档</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/bookroom_upload.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>

		<script>
			$(document).ready(function() {
				//搜索选项下拉框
				$('#searchtype a').click(function() {
					$('#searchtypebtn').text($(this).text());
					$('#searchtypebtn').append('<span class="caret"></span>');
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

		<script type="text/javascript"> 

		$(document).ready(function(){ 
			$("#upload_form").bind("submit", function(){ 
				var txt_docname = $.trim(document.getElementById("docname").value);
				var txt_picurl = document.getElementById("pictrueurl").value;
				var txt_docurl = document.getElementById("docurl").value;
				$("#nameLabel").text("") 
				$("#docLabel").text("") 
				$("#picLabel").text("");
				var isSuccess = 1; 
				var tp ="jpg,gif,bmp,png,JPG,GIF,BMP,PNG";
				var doc = "doc,docx,xls,xlsx,ppt,pptx,txt,pdf";
				if(txt_docname.length == 0) 
				{ 
					$("#nameLabel").text("请输入名称！") 
					$("#nameLabel").css({"color":"red"}); 
					isSuccess = 0; 
				} 
				if(txt_docurl.length == 0) 
				{ 
					$("#docLabel").text("请上传文档！") 
					$("#docLabel").css({"color":"red"}); 
					isSuccess = 0; 
				}
				else{
					var size_doc = document.getElementById("docurl").files[0].size;
					if(size_doc>20971520){
						$("#docLabel").text("已超过20MB！") 
						$("#docLabel").css({"color":"red"}); 
						isSuccess = 0; 
					}
					else if(!picnamecheck(txt_docurl,doc)){//文件格式
						$("#docLabel").text("文件格式：doc,docx,xls,xlsx,ppt,pptx,\ntxt,pdf！") 
						$("#docLabel").css({"color":"red"}); 
						isSuccess = 0; 
					}
				}
				
				if(!picnamecheck(txt_picurl,tp)){
					$("#picLabel").text("请上传图片(jpg,gif,png,bmp)");
					$("#picLabel").css({"color":"red"}); 
					isSuccess = 0; 
				}
				if(isSuccess == 0) 
				{ 
					return false; 
				} 
			}) 
		}) 
		function picnamecheck(filepath,tp){
			//获取欲上传的文件路径
			// var filepath = document.getElementById("file1").value; 
			//为了避免转义反斜杠出问题，这里将对其进行转换
			var re = /(\\+)/g; 
			var filename=filepath.replace(re,"#");
			//对路径字符串进行剪切截取
			var one=filename.split("#");
			//获取数组中最后一个，即文件名
			var two=one[one.length-1];
			//再对文件名进行截取，以取得后缀名
			var three=two.split(".");
			 //获取截取的最后一个字符串，即为后缀名
			var last=three[three.length-1];
			//添加需要判断的后缀名类型
			// var tp ="jpg,gif,bmp,png,JPG,GIF,BMP,PNG";
			//返回符合条件的后缀名在字符串中的位置
			var rs=tp.indexOf(last);
			//如果返回的结果大于或等于0，说明包含允许上传的文件类型
			if(rs>=0){
				return true;
			}else{
				// alert("您选择的上传文件不是有效的图片文件！");
				return false;
			}
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
	<body id="uploadpage">
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
		<form id="upload_form" enctype="multipart/form-data" action="" method="post"/>
		<input type="hidden" value="1" name="hasupload"/>
		<div class="main">
			<div class="left">
				<p class="height-adjust">文档名称:  <span style="color:red">*</span></br><label id="nameLabel"></label></p>
				<p class="height-adjust">图片:(jpg,bmp,gif,png) </br><label id="picLabel"></p>
				<p class="height-more">简介:</p>
				<p class="height-adjust">上传文档（小于20MB）: <span style="color:red">*</span> <label id="docLabel"></label></p>
				
			</div>
			<div class="right">
				<div class="height-adjust">
					<input id="docname" name="docname" type="text" class="form-control1" /><!--电子书名称-->
				</div>
				<div id="pictrueurl_div" class="height-adjust">
					<input id="pictrueurl" name="pictrueurl" type="file" class="form-control1" /><!--图片-->
				</div>
				<div class="height-more">
					<textarea id="summary" name="summary" class="form-control"></textarea><!--简介-->
				</div>
				<div id="docurl_div" class="height-adjust">
					<input id="docurl" name="docurl" type="file" class="form-control1" /><!--上传电子书-->
				</div>
				<input type="submit" class="btn btn-danger" value="上传"/>
			
				<!--<button type="button" onclick="jump();"  class="btn btn-info"">取消</button> -->

			</div>
		</div>
		</form>
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
