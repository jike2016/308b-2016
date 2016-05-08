<?php
/** Start zxf 处理分类CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "edit"://编辑首页推荐作者文档
            $newauthorrecommenddoc=new stdClass();
            /**start zxf 判断选中的文档已经在推荐名单中**/
            $exitflag=$DB->get_record_sql('select * from mdl_doc_recommend_authorlist_my where id='.$_GET['authorrecommenddid'].' and docid1='.$_POST['docid'].' or docid2='.$_POST['docid'].' or docid3='.$_POST['docid']);
            if($exitflag!=null){
                failure('已经存在');
            }
            else{
                $newauthorrecommenddoc->id=$_GET['authorrecommenddid'];
                $newauthorrecommenddoc->$_GET['docid']=$_POST['docid'];
                $DB->update_record('doc_recommend_authorlist_my',$newauthorrecommenddoc);
                success('修改成功','categoryrecommendlistdoc','closeCurrent');
            }
            /**end zxf 判断选中的文档已经在推荐名单中**/
            break;
    }
}
else{
    failure('操作失败');
}
?>

