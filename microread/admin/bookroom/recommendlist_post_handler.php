<?php
/** Start zxf 处理分类CURD*/
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "edit"://编辑分类
			$newrecommendlist=new stdClass();
			$newrecommendlist->id= $_GET['recommendid'];
			$newrecommendlist->ebookid= $_POST['ebookid'];
			$DB->update_record('ebook_recommendlist_my', $newrecommendlist);
			success('修改成功','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}
function success($message,$callbackType){
	echo '{
		"statusCode":"200",
		"message":"'.$message.'",
		"navTabId":"recommendlist",
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

