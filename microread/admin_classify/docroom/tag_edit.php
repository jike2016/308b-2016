<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$tagid = $_GET['tagid'];
$tag = $DB->get_record_sql('select * from mdl_doc_tag_my where id='.$tagid.';');

?>
<div class="pageContent">
    <form method="post" action="docroom/tag_post_handler.php?title=edit&tagid=<?php echo $tagid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>分类名称：</label>
                <input name="name" type="text" size="30" value="<?php echo $tag->name;?>" class="required"/>
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
