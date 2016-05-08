<?php
/** Start zxf 处理推荐榜CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "edit"://编辑推荐
			$newrecommendlist=new stdClass();
			$newrecommendlist->id= $_GET['recommendid'];
			$newrecommendlist->docid= $_POST['docid'];
			$DB->update_record('doc_recommendlist_my', $newrecommendlist);
			success('修改成功','docrecommendlist','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}

?>

