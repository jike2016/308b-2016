<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$categoryid = $_GET['categoryid'];
$category = $DB->get_record_sql('select * from mdl_ebook_categories_my where id='.$categoryid.';');

?>
<div class="pageContent">
    <form method="post" action="bookroom/category_post_handler.php?title=edit&categoryid=<?php echo $categoryid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>分类名称：</label>
                <input name="name" type="text" size="30" value="<?php echo $category->name;?>" class="required"/>
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
