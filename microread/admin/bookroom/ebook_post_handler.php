<?php 
/** Start CX 处理电子书CURD*/
/**获取上传的文件，并转存路径 */
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "add"://添加电子书
			add_ebook();	
			break;
		case "edit"://编辑
			edit_ebook();
			break;
		case "delete"://删除
			$DB->delete_records("ebook_my", array("id" =>$_GET['ebookid']));
			success('删除成功','');
			break;
	}
}
else{
	failure('操作失败');
}
function edit_ebook(){
	$newebook=new stdClass();
	$newebook->id= $_GET['ebookid'];
	$newebook->name= $_POST['name'];
	$newebook->categoryid= $_POST['categoryid'];
	$newebook->authorid= $_POST['authorid'];
	$newebook->summary= $_POST['summary'];
	$newebook->wordcount= $_POST['wordcount'];
	if(isset($_FILES['pictrueurl'])){//上传图片
		if ($_FILES["pictrueurl"]["error"] > 0){
			// failure('上传图片失败');
			// exit;
		}
		else{
			$currenttime=time();
			move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/ebook/pictrueurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
			$newebook->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/pictrueurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
		}
	}
	if(isset($_FILES['url'])){//上传下载的文件
		if ($_FILES["url"]["error"] > 0){
			// failure('上传文件失败');
			// exit;
		}
		else{
			move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/ebook/ebookurl_fordownload/" . $currenttime.$_FILES["url"]["name"]);
			$newebook->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/ebookurl_fordownload/'. $currenttime.$_FILES["url"]["name"];
			$newebook->suffix= $_FILES["url"]["type"];
			$newebook->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
		}
	}
	global $DB;
	$DB->update_record('ebook_my', $newebook);
	success('添加成功','closeCurrent');
}
function add_ebook(){
	if(isset($_FILES['pictrueurl'])){//上传图片
		if ($_FILES["pictrueurl"]["error"] > 0){
			failure('上传图片失败');
			exit;
		}
		else{
			$currenttime=time();
			move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/ebook/pictrueurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
			if ($_FILES["url"]["error"] > 0){//上传下载的文件
				failure('上传文件失败');
				exit;
			}
			else{
				move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/ebook/ebookurl_fordownload/" . $currenttime.$_FILES["url"]["name"]);
				$newebook=new stdClass();
				$newebook->name= $_POST['name'];
				$newebook->categoryid= $_POST['categoryid'];
				$newebook->authorid= $_POST['authorid'];
				$newebook->summary= $_POST['summary'];
				$newebook->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/ebookurl_fordownload/'. $currenttime.$_FILES["url"]["name"];
				$newebook->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/pictrueurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
				$newebook->timecreated= $currenttime;
				$newebook->wordcount= $_POST['wordcount'];
				$newebook->suffix= $_FILES["url"]["type"];
				$newebook->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
				global $DB;
				$DB->insert_record('ebook_my',$newebook,true);
				success('添加成功','closeCurrent');
			}
		}
	}
	else{
		// echo "Invalid file";
		failure('没有上传图片');
	}
	
}
function success($message,$callbackType){
	echo '{
		"statusCode":"200",
		"message":"'.$message.'",
		"navTabId":"ebook",
		"rel":"",
		"callbackType":"'.$callbackType.'",
		"forwardUrl":"",
		"confirmMsg":""
	}';
}
function failure($message){
	echo '{
		"statusCode":"300",
		"message":"'.$message.'",
		"navTabId":"",
		"rel":"",
		"callbackType":"",
		"forwardUrl":"",
		"confirmMsg":""
	}';
}
?>

