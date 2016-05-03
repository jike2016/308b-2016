<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$chapterid = $_GET['chapterid'];
$chapter = $DB->get_record_sql('select * from mdl_ebook_chapter_my where id='.$chapterid.';');

?>
<div class="pageContent">
    <form method="post" action="bookroom/chapter_post_handler.php?title=edit&chapterid=<?php echo $chapterid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>章名称：</label>
                <input name="name" type="text" size="30" value="<?php echo $chapter->name;?>" class="required"/>
                <input name="ebookid" type="hidden" size="30" value="<?php echo $chapter->ebookid;?>" class="required"/>
            </p>
            <p>
                <label>章排序：</label>
                <input name="chapterorder" type="text" size="30" value="<?php echo $chapter->chapterorder;?>" class="required digits"/>

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
