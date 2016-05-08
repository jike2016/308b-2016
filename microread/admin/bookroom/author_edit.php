<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$authorid = $_GET['authorid'];
$author = $DB->get_record_sql('select * from mdl_ebook_author_my where id='.$authorid.';');

?>
<div class="pageContent">
    <form method="post" action="bookroom/author_post_handler.php?title=edit&authorid=<?php echo $authorid;?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>作者姓名：</label>
                <input name="name" type="text" size="30" value="<?php echo $author->name;?>" class="required"/>
            </p>
			<p>
				<label>作者头像：(jpg ,png ,bmp ,gif)(150*220)</label>
				<input name="pictrueurl" type="file" class=""/>
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
