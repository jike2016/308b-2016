<?php
/** Start zxf 处理分类CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加分类
            $newecategory=new stdClass();
            $newecategory->name= $_POST['name'];
            $newecategory->parent= $_POST['parent'];
            global $DB;
            $existid=$DB->get_records_sql('select * from mdl_doc_categories_my as a where a.name="'.$newecategory->name.'"');
            if($existid)
            {
                failure('该类已存在');
            }
            else
            {
                $DB->insert_record('doc_categories_my',$newecategory,true);
                success('添加成功','doccategory','closeCurrent');
            }

            break;
        case "edit"://编辑分类
            $newecategory=new stdClass();
            $newecategory->id= $_GET['categoryid'];
            /**strat zxf 排除自己查重名**/
            $mycategory=$DB->get_record_sql('select * from mdl_doc_categories_my where id='.$newecategory->id);
            if($mycategory->name!=$_POST['name'])
            {
                $sql='select * from mdl_doc_categories_my as a where a.name="'.$_POST['name'].'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该类已存在');
                }
                else
                {
                    $newecategory->name= $_POST['name'];
                    $newecategory->parent= $_POST['parent'];
                    $parent=$DB->get_record_sql('select *from mdl_doc_categories_my where id='.$_POST['parent']);
                    if($parent->parent!=0){
                        $sql='update mdl_doc_categories_my set parent=-1  where parent='.$_GET['categoryid'];
                        $DB->execute($sql);
                    }
                    $DB->update_record('doc_categories_my', $newecategory);
                    success('修改成功','doccategory','closeCurrent');
                }
            }
            else
            {
                $newecategory->name= $_POST['name'];
                $newecategory->parent= $_POST['parent'];
                $parent=$DB->get_record_sql('select *from mdl_doc_categories_my where id='.$_POST['parent']);
                if($parent->parent!=0){
                    $sql='update mdl_doc_categories_my set parent=-1  where parent='.$_GET['categoryid'];
                    $DB->execute($sql);
                }
                $DB->update_record('doc_categories_my', $newecategory);
                success('修改成功','doccategory','closeCurrent');
            }



            /**end zxf 排除自己查重名**/
            break;
        case "delete"://删除
            $recommendcategorynum=$DB->get_record_sql('select count(*) as number from mdl_doc_category_recommend_my where categoryid='.$_GET['categoryid']);
            if($recommendcategorynum->number>0) {
                $unusecategorynum=$DB->get_record_sql('select count(*) as number from mdl_doc_category_recommend_my where categoryid=-1');
                if($unusecategorynum->number>0){
                    $sql='update mdl_doc_category_recommend_my set categoryid=0,docid=-1 where categoryid='.$_GET['categoryid'];
                }
                else{
                    $sql='update mdl_doc_category_recommend_my set categoryid=-1,docid=-1 where categoryid='.$_GET['categoryid'];
                }
                $DB->execute($sql);
            }
            $docs=$DB->get_records_sql('select * from mdl_doc_my where categoryid='.$_GET['categoryid']);
            foreach($docs as $doc){
                $newcategory=new stdClass();
                $newcategory->id=$doc->id;
                $newcategory->categoryid=-1;
                $DB->update_record('doc_my',$newcategory);
            }
            $DB->delete_records("doc_categories_my", array("id" =>$_GET['categoryid']));
            success('删除成功','doccategory','');
            break;
    }
}
else{
    failure('操作失败');
}

?>
