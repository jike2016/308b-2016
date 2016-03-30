<?php
//require_once($CFG->wwwroot.'/config.php');
require_once('../config.php');

global $CFG;
global $DB;

$path = "uploads/";

$extArr = array("jpg", "png", "gif");

if(isset($_POST) && $_SERVER['REQUEST_METHOD'] == "POST"){
	$name = $_FILES['photoimg']['name'];
	$size = $_FILES['photoimg']['size'];
	$picID = $_POST['id'];//图片id
	$picBackcolor = $_POST['picBackcolor'];//背景色
	$picLink = $_POST['picLink'];//链接



	if(empty($name)){ //如果没选择上传图片
		$newid = $DB->update_record('index_picture', array('id'=>$picID,'Picturelink'=>$picLink, 'Picturecolor'=>$picBackcolor));
		$url = "$CFG->wwwroot/uploadindexpicture/index.php";
		echo "<script type='text/javascript'> window.location.href='$url'; </script>";
		exit;
	}else{ //如果选择上传图片

		$ext = extend($name);
		if(!in_array($ext,$extArr)){
			echo '图片格式错误！';
			exit;
		}
		if($size>(1000*1024*1024)){
			echo '图片大小不能超过100KB';
			exit;
		}
		$image_name = time().rand(100,999).".".$ext;
		$tmp = $_FILES['photoimg']['tmp_name'];
		if(move_uploaded_file($tmp, $path.$image_name)){//文件上传
			echo '<img src="'.$path.$image_name.'" class="preview">';
			//更新数据库
			$picURL = "$CFG->wwwroot/uploadindexpicture/uploads/$image_name";
			$newid = $DB->update_record('index_picture', array('id'=>$picID, 'Pictureurl'=>$picURL, 'Picturelink'=>$picLink, 'Picturecolor'=>$picBackcolor));

			$url = "$CFG->wwwroot/uploadindexpicture/index.php";
			echo "<script type='text/javascript'> window.location.href='$url'; </script>";

		}else{
			echo '上传出错了！';
		}
		exit;
	}

}
exit;


function extend($file_name){
	$extend = pathinfo($file_name);
	$extend = strtolower($extend["extension"]);
	return $extend;
}
?>