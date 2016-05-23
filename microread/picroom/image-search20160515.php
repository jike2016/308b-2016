<?php
if(!isset($_GET['word'])||!$_GET['word']||ctype_space($_GET['word'])){
	header("location: index.php");
	exit;
}
require_once ("../../config.php");
global $USER;
global $DB;
//拆分关键词搜索
$word =$_GET['word'];
$sql='name like \'%'.mb_substr($word,0,1,"utf-8").'%\'';
for($i=1;$i<mb_strlen($word,'utf8');$i++){
	$sql .= 'or name like \'%'.mb_substr($word,$i,1,"utf-8").'%\'';
}
 $relatedwords = $DB->get_records_sql("select * from mdl_pic_tag_my where $sql limit 15");
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>图库搜索页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/image-search.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/jquery.infinitescroll.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/image-search.js" ></script>
	</head>
	<body>
		<div class="wrap">
			<!-- 顶部导航 -->
			<div class="navbar navbar-fixed-top">
				<div class="user-block dropdown">
						<?php 
							if($USER->id!=0){
								echo '<button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									<a href="#" class="username">'.fullname($USER, true).'</a>
									<a href="#" class="userimg">'.$OUTPUT->user_picture($USER,array('link' => false,'visibletoscreenreaders' => false)).'</a>
									</button>';
							}
							else{
								echo '<a class="nav-a login " href="'.$CFG->wwwroot.'/login/index.php"><img src="../img/denglu.png" style="padding:10px 30px 0px 0px"></a>';
							}
						?>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<?php 
						echo '<li><a href="'.new moodle_url('/privatecenter/').'">个人中心</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="'.new moodle_url('/message/').'">消息</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="image-upload.php">上传图片</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="'.new moodle_url('/login/logout.php', array('sesskey' => sesskey())).'">退出</a></li>';
						?>
					</ul>

				</div>
				<div class="search-box">
					<div class="search-top">
						<a href="index.php"><img src="../img/logo.png" alt="" class="logoimg" /></a>
						<form action="#" method="get" id="search-form">
							<input type="text" name="word" id="search-word" value="<?php echo $word?>">
							<input type="submit" value="图库搜索" id="search-submit">
						</form>
					</div>
					<div class="search-relative">
						<div class="search-relative-left">相关搜索：</div>
						
						<ul class="list-unstyled">
							<?php
								foreach($relatedwords as $relateword){
									echo '<li><a href="image-search.php?word='.$relateword->name.'">'.$relateword->name.'</a></li>';
								}
							?>
						</ul>
					</div>
				</div>
			</div>
			<!-- 顶部导航 end -->

			<!-- 主体 -->
			<div class="main">
				<ul class="img-list">
					<li class="img-item">
						<a href="#">
							<img />
							<div class="img-label">
								<span class="img-title"></span>
								<span class="img-size"></span>
							</div>
						</a>
					</li>
	            </ul>
				<div class="loading"></div>
			</div>
			<!-- ajax加载图片的请求连接 -->
    		<div class="img-more"><a href="get_pics.php?word=<?php echo $word;?>&page="></a></div>
			<!-- 主体 end -->
		</div>
	</body>
</html>