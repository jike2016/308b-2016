<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="docroom/doclibrary_post_handler.php?title=add" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);">
        <div layoutH="56">
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>文档名称：</label>
                <input name="name" type="text" size="30" value="" class="required"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>分类：</label>
                <?php
                /**start zxf 查询所有分类**/
                require_once("../../../config.php");
                global $DB;
                $categories=$DB->get_records_sql('select * from mdl_doc_categories_my');
                $selectoption='<select name="categoryid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($categories as $category){
                    $selectoption=$selectoption.'<option value="'.$category->id.'">'.$category->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询所有分类**/
                ?>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>简介：</label>
                <textarea name="summary" cols="80" rows="5" class="required"></textarea>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片：(jpg ,png ,gif)</label>
                <input name="pictrueurl" type="file"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>上传文档：(doc,docx,ppt,pptx,xls,</br>xlsx,txt,pdf)</label>
                <input name="url" type="file" class="required"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px;">
                <label>标签选择：</label>
                <?php
                require_once("doctagmylib.php");
                $alltags = gettagmylist();
                foreach($alltags as $tagmy){
                    echo '<label><input type="checkbox" name="tagmy[]" value="'.$tagmy->id.'" />'.$tagmy->name.'</label>';
                }
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
