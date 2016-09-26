<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
global $DB;
$categoryrecommenddocid = $_GET['categoryrecommenddocid'];
$categoryrecommenddoc = $DB->get_record_sql('select * from mdl_doc_category_recommend_my where id='.$categoryrecommenddocid.';');

?>
<div class="pageContent">
    <form method="post" action="docroom/categoryrecommendlistdoc_post_handler.php?title=edit&categoryrecommenddocid=<?php echo $categoryrecommenddocid;?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p  style="margin: 20px 20px">
                <label>分类：</label>
                <?php
                /**start zxf 查询所有分类**/
                $docs=$DB->get_records_sql('select * from mdl_doc_my where categoryid='.$categoryrecommenddoc->categoryid.' order by timecreated desc');
                $selectoption='<select name="docid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($docs as $doc){
                    if($doc->id==$categoryrecommenddoc->docid)
                        $selectoption=$selectoption.'<option value="'.$doc->id.'" selected="selected">'.$doc->name.'</option> ';
                    else
                        $selectoption=$selectoption.'<option value="'.$doc->id.'">'.$doc->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询所有分类**/
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
