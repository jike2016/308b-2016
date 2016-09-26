<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
global $DB;
$authorrecommendid = $_GET['authorrecommendid'];
$authorrecommenddoc = $DB->get_record_sql('select * from mdl_doc_recommend_authorlist_my where id='.$authorrecommendid.';');

?>
<div class="pageContent">
    <form method="post" action="docroom/authorrecommendlistdoc_post_handler.php?title=edit&authorrecommenddid=<?php echo $authorrecommendid;?>&docid=<?php echo 'docid'.$_GET['authorrecommenddocid'];?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p  style="margin: 20px 20px">
                <label>文档：</label>
                <?php
                /**start zxf 查询该作者的所有上传文档**/
                $docs=$DB->get_records_sql('select id,name from mdl_doc_my where uploaderid='.$authorrecommenddoc->userid.' order by timecreated desc');
                $selectoption='<select name="docid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($docs as $doc){
                    $b='docid'.$_GET['authorrecommenddocid'];
                    if($doc->id==($authorrecommenddoc->$b))
                        $selectoption=$selectoption.'<option value="'.$doc->id.'" selected="selected">'.$doc->name.'</option> ';
                    else
                        $selectoption=$selectoption.'<option value="'.$doc->id.'">'.$doc->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询该作者的所有文档**/
                ?>
            </p>
        </div>
        <div class="formBar">
            <ul>
                <!--<li><a class="buttonActive" href="javascript:;"><span>保存</span></a></li>-->
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                <li>
                    <div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
                </li>
            </ul>
        </div>
    </form>
</div>
