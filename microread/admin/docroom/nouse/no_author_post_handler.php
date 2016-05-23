<?php
/** Start zxf 处理分类CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加分类
			$newauthor=new stdClass();
			if(isset($_FILES['pictrueurl'])){//上传图片
				if ($_FILES["pictrueurl"]["error"] > 0){
					// failure('上传图片失败');
					// exit;
				}
				else{
					$currenttime=time();
					move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/doclibrary/authorpicurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
					$newauthor->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/authorpicurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
				}
			}
            $newauthor->name= $_POST['name'];
            global $DB;
            $DB->insert_record('doc_author_my',$newauthor,true);
            success('添加成功','docauthor','closeCurrent');
            break;
        case "edit"://编辑分类
            $newauthor=new stdClass();
			if(isset($_FILES['pictrueurl'])){//上传图片
				if ($_FILES["pictrueurl"]["error"] > 0){
					// failure('上传图片失败');
					// exit;
				}
				else{
					$currenttime=time();
					move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/doclibrary/authorpicurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
					$newauthor->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/authorpicurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
				}
			}
			$newauthor->id= $_GET['authorid'];
			$newauthor->name= $_POST['name'];
            $DB->update_record('doc_author_my', $newauthor);
            success('修改成功','docauthor','closeCurrent');
            break;
        case "delete"://删除
			$ebooks = $DB->get_records_sql('select id from mdl_ebook_my where authorid='.$_GET['authorid']);
			foreach($ebooks as $ebook){
				$newebook=new stdClass();
				$newebook->id = $ebook->id;
				$newebook->authorid = 0;
				$DB->update_record('doc_my', $newebook);
			}
            $DB->delete_records("doc_author_my", array("id" =>$_GET['authorid']));
            success('删除成功','docauthor','');
            break;
    }
}
else{
    failure('操作失败');
}
?>

