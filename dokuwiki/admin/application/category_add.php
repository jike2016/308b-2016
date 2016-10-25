
<div class="pageContent">
    <form method="post" action="application/category_post_handler.php?title=add" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>分类名称：</label>
                <input name="name" type="text" size="30" value="" class="required"/>
            </p>
            <p>
                <label>上级分类：</label>
                <?php
                /**start cx 将分类表中的顶级父类选择出来 20160929**/
					require_once("../../../config.php");
					global $DB;
					$topcategories=$DB->get_records_sql('select * from mdl_dokuwiki_categories_my where parent=0');
					$selectoption='<select name="parent" class="required combox" class="required">
					<option value="0">无</option>';
					foreach($topcategories as $topcategory){
						$selectoption=$selectoption.'<option value="'.$topcategory->id.'">'.$topcategory->name.'</option>';
					}
					$selectoption=$selectoption.'</select>';
					echo $selectoption;
                /**end cx 将分类表中的顶级父类选择出来**/
                ?>

            </p>
        </div>
        <div class="formBar">
            <ul>
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                <li>
                    <div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
                </li>
            </ul>
        </div>
    </form>
</div>
