<?php
require_once ("../../config.php");
/** START cx开关判断 20160515*/
global $DB;
if(!$DB->record_exists('microread_upload_switch_my',array('id'=>1,'upload_switch'=>1))){
	echo '上传功能已关闭，3秒后页面将自动跳转';
	//等待3秒后跳转
	header("refresh:3;url=http://".$_SERVER['HTTP_HOST']."/moodle/microread/picroom");
	exit;
}
/** End 开关判断*/
if(isset($_POST['hasupload'])&&$_POST['hasupload']==1){
	//处理上传数据
	$currenttime=time();
	$ranknum = rand(100, 2001);//随机数
	$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
	$picfilestr=strtolower($picfilestr);//全小写
	$filepath = $_SERVER['DOCUMENT_ROOT']."/microread_files/picture/user_upload/".$currenttime.$ranknum.$picfilestr;
	move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],$filepath);
	//zxf start 2016/5/12 用户上传图片 加水印
	require_once('../admin/water.php');
	img_water_mark($filepath,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
	//zxf end 2016/5/12 用户上传图片 加水印
	$newepic=new stdClass();
	global $USER;
	$newepic->uploaderid= $USER->id;
	$newepic->admin_check= 0;
	$newepic->name= $_POST['picname'];
	$newepic->picurl= 'http://'.$_SERVER['HTTP_HOST']."/microread_files/picture/user_upload/".$currenttime.$ranknum.$picfilestr;
	$newepic->timecreated= $currenttime;
	$newepic->suffix= $picfilestr;
	if(($_FILES["pictrueurl"]["size"] / 1024)<=0.1){
        $newepic->size='0.1KB';
    }
    else {
        $newepic->size = number_format(($_FILES["pictrueurl"]["size"] / 1024), 1) . 'KB';
    }
	$newepic=$DB->insert_record('pic_user_upload_my',$newepic,true);
	echo '上传成功!请等待管理员审核，3秒后页面将自动跳转';
	//等待3秒后跳转
	header("refresh:3;url=http://".$_SERVER['HTTP_HOST']."/moodle/microread/picroom");
	exit;
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>图库用户上传页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/image-upload.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script>
		$(document).ready(function(){ 
			$("#upload_form").bind("submit", function(){ 
				var txt_picurl = document.getElementById("pictrueurl").value;
				$("#picLabel").text("");
				var isSuccess = 1; 
				if(!picnamecheck(txt_picurl)){
					$("#picLabel").text("支持图片格式：(jpg,gif,png)");
					$("#picLabel").css({"color":"red"}); 
					isSuccess = 0; 
				}
				if(isSuccess == 0) 
				{ 
					return false; 
				} 
			}) 
		}) 
		function picnamecheck(filepath){
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
			var tp ="jpg,gif,png,JPG,GIF,PNG";
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
	<body>
		<div class="wrap">
			<!-- 顶部导航 -->
			<?php
				require_once ("../common/pic_head_login.php");//微阅登录导航栏：首页、微阅、微课、、、、
			?>
<!--			<div class="navbar navbar-fixed-top">-->
<!--				<div class="user-block dropdown">-->
<!--						--><?php //
//							if($USER->id!=0){
//								echo '<button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
//									<a href="#" class="username">'.fullname($USER, true).'</a>
//									<a href="#" class="userimg">'.$OUTPUT->user_picture($USER,array('link' => false,'visibletoscreenreaders' => false)).'</a>
//									</button>';
//							}
//							else{
//								echo '<a class="nav-a login " href="'.$CFG->wwwroot.'/login/index.php"><img src="../img/denglu.png" style="padding:10px 30px 0px 0px"></a>';
//							}
//						?>
<!--					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">-->
<!--						--><?php //
//						echo '<li><a href="'.new moodle_url('/privatecenter/').'">个人中心</a></li>
//								<li role="separator" class="divider"></li>
//								<li><a href="'.new moodle_url('/message/').'">消息</a></li>
//								<li role="separator" class="divider"></li>
//								<li><a href="user_upload.php">上传图片</a></li>
//								<li role="separator" class="divider"></li>
//								<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>';
//						?>
<!--					</ul>-->
<!---->
<!--				</div>-->
<!--				<div class="search-box">-->
<!--					<div class="search-top">-->
<!--						<a href="index.php"><img src="../img/logo.png" alt="" class="logoimg" /></a>-->
<!--						<form action="#" method="get" id="search-form">-->
<!--							<input type="text" name="word" id="search-word" value="--><?php //echo $word?><!--">-->
<!--							<input type="submit" value="图库搜索" id="search-submit">-->
<!--						</form>-->
<!--					</div>-->
<!--					<div class="search-relative">-->
<!--						<div class="search-relative-left"></div>-->
<!---->
<!--					</div>-->
<!--				</div>-->
<!--			</div>-->
			<!-- 顶部导航 end -->

			<!--页面主体-->
			<form id="upload_form" id="image-upload.php" enctype="multipart/form-data" action="" method="post"/>
			<input type="hidden" value="1" name="hasupload"/>
			<div class="main">
				<div class="left">
					<p class="height-adjust">图片:<span style="color:red">*</span>(jpg,gif,png)</p>
					
					<p class="height-more">图片描述:</p>
				</div>
				<div class="right">
					<div class="height-adjust">
						<input id="pictrueurl" name="pictrueurl" type="file" class="form-control1" /><label id="picLabel"><!--图片-->
					</div>
					
					<div class="height-more">
						<textarea id="picname" name="picname" class="form-control"></textarea><!--简介-->
					</div>
					<button id="submit" class="btn btn-info">上传</button>
					<button id="cancel" class="btn btn-danger">取消</button>
				</div>
				
			</div>
			</form>
			<!--页面主体 end-->
			
		</div>
	</body>
</html>
