<?php
/** Start zxf 处理分类CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "edit"://编辑首页推荐分类榜
            /**start zxf 判断选中的分类已经在推荐名单中**/
            $exitflag=$DB->get_record_sql('select * from mdl_doc_category_recommend_my where categoryid='.$_POST['categoryid']);
            if($exitflag!=null){
                failure('已经存在');
            }
            else{
                $sql='update mdl_doc_category_recommend_my set categoryid='. $_POST['categoryid'].',docid=-1 where categoryid='.$_GET['oldcategoryid'];
                $DB->execute($sql);
                success('修改成功','doccategoryrecommendlist','closeCurrent');
            }
            break;
    }
}
else{
    failure('操作失败');
}
?>

