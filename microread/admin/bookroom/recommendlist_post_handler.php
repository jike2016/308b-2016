<?php
require_once('../lib/lib.php');
/** Start zxf 处理分类CURD*/
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "edit"://编辑分类
			$newrecommendlist=new stdClass();
			$newrecommendlist->id= $_GET['recommendid'];
			$newrecommendlist->ebookid= $_POST['ebookid'];
			$DB->update_record('ebook_recommendlist_my', $newrecommendlist);
			success('修改成功','ebookrecommendlist','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}


?>

