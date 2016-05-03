<?php
/** Start zxf 处理分类CURD*/
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加分类
            $newchapter=new stdClass();
            $newchapter->name= $_POST['name'];
            $newchapter->ebookid= $_POST['ebookid'];
            $newchapter->chapterorder= $_POST['chapterorder'];
            global $DB;
            $existid=$DB->get_records_sql('select * from mdl_ebook_chapter_my as a where a.chapterorder='. $newchapter->chapterorder.' and a.ebookid='. $newchapter->ebookid);
            if($existid!=null)
            {
                failure('该序号已存在');
            }
            else
            {
                $DB->insert_record('ebook_chapter_my',$newchapter,true);
                success('添加成功','closeCurrent');
            }

            break;
        case "edit"://编辑分类
            $newchapter=new stdClass();
            $newchapter->id= $_GET['chapterid'];
            /**strat zxf 排除自己查重名**/
            $mycategory=$DB->get_record_sql('select * from mdl_ebook_chapter_my where id='.$newchapter->id);
            if($mycategory->chapterorder!=$_POST['chapterorder'])
            {
                $sql='select * from mdl_ebook_chapter_my as a where a.ebookid="'.$_POST['ebookid'].'" and a.chapterorder="'.$_POST['chapterorder'].'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该序号已存在');
                }
                else
                {
                    $newchapter->ebookid= $_POST['ebookid'];
                    $newchapter->name= $_POST['name'];
                    $newchapter->chapterorder= $_POST['chapterorder'];
                    $DB->update_record('ebook_chapter_my', $newchapter);
                    success('修改成功','closeCurrent');
                }
            }
            else
            {
                $newchapter->ebookid= $_POST['ebookid'];
                $newchapter->name= $_POST['name'];
                $newchapter->chapterorder= $_POST['chapterorder'];
                $DB->update_record('ebook_chapter_my', $newchapter);
                success('修改成功','closeCurrent');
            }



            /**end zxf 排除自己查重名**/
            break;
        case "delete"://删除
            $DB->delete_records("ebook_chapter_my", array("id" =>$_GET['chapterid']));
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
		"navTabId":"ebookchapter",
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

