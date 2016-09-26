<?php
/** Start zxf 处理分类CURD*/
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加分类
            $newecategory=new stdClass();
            $newecategory->name= $_POST['name'];
            global $DB;
            $existid=$DB->get_records_sql('select * from mdl_ebook_categories_my as a where a.name="'.$newecategory->name.'"');
            if($existid)
            {
                failure('该类已存在');
            }
            else
            {
                $DB->insert_record('ebook_categories_my',$newecategory,true);
                success('添加成功','closeCurrent');
            }

            break;
        case "edit"://编辑分类
            $newecategory=new stdClass();
            $newecategory->id= $_GET['categoryid'];
            $newecategory->name= $_POST['name'];
            $existid=$DB->get_records_sql('select * from mdl_ebook_categories_my as a where a.name="'.$newecategory->name.'"');
            if($existid)
            {
                failure('该类已存在');
            }
            else
            {
                $DB->update_record('ebook_categories_my', $newecategory);
                success('修改成功','closeCurrent');
            }

            break;
        case "delete"://删除
			//把此分类的ebook的分类置为0
			$ebooks = $DB->get_records_sql('select id from mdl_ebook_my where categoryid='.$_GET['categoryid']);
			foreach($ebooks as $ebook){
				$newebook=new stdClass();
				$newebook->id = $ebook->id;
				$newebook->categoryid = 0;
				$DB->update_record('ebook_my', $newebook);
			}
			
            $DB->delete_records("ebook_categories_my", array("id" =>$_GET['categoryid']));
            success('删除成功','');
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
		"navTabId":"category",
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

