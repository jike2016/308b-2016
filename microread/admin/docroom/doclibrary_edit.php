<?php
require_once('../../../config.php');
$doclibraryid = $_GET['doclibraryid'];
$doclibrary = $DB->get_record_sql('select * from mdl_doc_my where id='.$doclibraryid.';');
?>

<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="docroom/doclibrary_post_handler.php?title=edit&doclibraryid=<?php echo $doclibraryid;?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);"">
        <div layoutH="56">
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>文档名称：</label>
                <input name="name" type="text" size="30" value="<?php echo $doclibrary->name; ?>" class="required"/>
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
                    if($category->id==$doclibrary->categoryid)
                        $selectoption=$selectoption.'<option value="'.$category->id.'" selected="selected">'.$category->name.'</option> ';
                    else
                        $selectoption=$selectoption.'<option value="'.$category->id.'">'.$category->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询所有分类**/
                ?>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>作者：</label>
                <?php
                /**start zxf 查询所有分类**/
                require_once("../../../config.php");
                global $DB;
                $authors=$DB->get_records_sql('select * from mdl_doc_author_my');
                $selectoption='<select name="authorid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($authors as $author){
                    if($author->id==$doclibrary->authorid)
                        $selectoption=$selectoption.'<option value="'.$author->id.'" selected="selected">'.$author->name.'</option>';
                    else
                        $selectoption=$selectoption.'<option value="'.$author->id.'">'.$author->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询所有分类**/?>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>简介：</label>
                <textarea name="summary" cols="80" rows="5" class="required"><?php echo $doclibrary->summary?></textarea>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片：</label>
                <input name="pictrueurl" type="file" class=""/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>上传文档：</label>
                <input name="url" type="file" class=""/>
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
