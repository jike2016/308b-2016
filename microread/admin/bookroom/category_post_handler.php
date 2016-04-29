<?php
/** Start zxf 处理分类CURD*/
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加分类
            $newecategory=new stdClass();
            $newecategory->name= $_POST['name'];
            $newecategory->parent= $_POST['parent'];
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
            /**strat zxf 排除自己查重名**/
            $mycategory=$DB->get_record_sql('select * from mdl_ebook_categories_my where id='.$newecategory->id);
            if($mycategory->name!=$_POST['name'])
            {
                $sql='select * from mdl_ebook_categories_my as a where a.name="'.$_POST['name'].'" and a.name="'.$mycategory->name.'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该类已存在');
                }
                else
                {
                    $newecategory->name= $_POST['name'];
                    $newecategory->parent= $_POST['parent'];
                    $DB->update_record('ebook_categories_my', $newecategory);
                    success('修改成功','closeCurrent');
                }
            }
            else
            {
                $newecategory->name= $_POST['name'];
                $newecategory->parent= $_POST['parent'];
                $DB->update_record('ebook_categories_my', $newecategory);
                success('修改成功','closeCurrent');
            }



            /**end zxf 排除自己查重名**/
            break;
        case "delete"://删除
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

