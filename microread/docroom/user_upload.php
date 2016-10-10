<?php
require_once ("../../config.php");
require_login();
global $DB;
/** START cx开关判断 20160515*/
if(!$DB->record_exists('microread_upload_switch_my',array('id'=>1,'upload_switch'=>1))){
	echo '上传功能已关闭，3秒后页面将自动跳转';
	//等待3秒后跳转
	header("refresh:3;url=http://".$_SERVER['HTTP_HOST']."/moodle/microread/docroom");
	exit;
}
/** End 开关判断*/
global $USER;
//获取文档的顶级分类
$docclasses = $DB->get_records_sql("select * from mdl_doc_categories_my dc where dc.parent = 0");

if(isset($_POST['hasupload'])&&$_POST['hasupload']==1){
	//处理数据
	$currenttime=time();
	$ranknum = rand(100, 200);//随机数
	$newdoc=new stdClass();
	if(isset($_FILES['pictrueurl'])) {//上传图片
		if ($_FILES["pictrueurl"]["error"] > 0) {
			$newdoc->pictrueurl= '/moodle/microread/img/doc_default.jpg';
		}
		else{
			$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
			$picfilestr=strtolower($picfilestr);//全小写
			move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docpic/" . $currenttime.$ranknum.$picfilestr);
			//zxf start 2016/5/12 用户上传图片 加水印
			require_once('../admin/water.php');
			img_water_mark('../../../microread_files/doclibrary/user_upload/docpic/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
			//zxf end 2016/5/12 用户上传图片 加水印

			$newdoc->pictrueurl= '/microread_files/doclibrary/user_upload/docpic/'. $currenttime.$ranknum.$picfilestr;
		}
	}
	else{
		$newdoc->pictrueurl= '/moodle/microread/img/doc_default.jpg';
	}
	$urlfilestr=strrchr($_FILES['docurl']['name'],'.');//url后缀名
	$urlfilestr=strtolower($urlfilestr);
	// move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docpic/" . $currenttime.$_FILES["pictrueurl"]["name"]);

	move_uploaded_file($_FILES["docurl"]["tmp_name"],"../../../microread_files/doclibrary/user_upload/docfordownload/" . $currenttime.$ranknum.$urlfilestr);
	$newdoc->upload_userid= $USER->id;
	$newdoc->admin_check= 0;
	$newdoc->name= $_POST['docname'];
	$newdoc->summary= $_POST['summary'];
	$newdoc->url= '/microread_files/doclibrary/user_upload/docfordownload/'. $currenttime.$ranknum.$urlfilestr;
//	$newdoc->pictrueurl= '/microread_files/doclibrary/user_upload/docpic/'. $currenttime.$ranknum.$picfilestr;
	$newdoc->timecreated= $currenttime;
	$newdoc->suffix = strrchr($_FILES['docurl']['name'],'.');
	$newdoc->size= number_format(($_FILES["docurl"]["size"] / 1048576),1).'MB';
	$docid=$DB->insert_record('doc_user_upload_my',$newdoc,true);
	echo '上传成功!请等待管理员审核，3秒后页面将自动跳转';
	//等待3秒后跳转
	header("refresh:3;url=http://".$_SERVER['HTTP_HOST']."/moodle/microread/docroom");
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

 	</head>
	<body id="uploadpage">
		<!--顶部导航-->
		<?php
			require_once ("../common/doc_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			require_once ("../common/doc_head_search.php");//文库搜索栏
		?>
		<!--顶部导航 end-->
		
		<!--书本分类-->
		<?php
			require_once ("../common/doc_head_classify.php");//文库搜索栏
		?>
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
