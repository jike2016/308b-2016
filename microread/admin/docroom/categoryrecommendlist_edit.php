<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
global $DB;
$categoryrecommendid = $_GET['categoryrecommendid'];
$categoryrecommend = $DB->get_record_sql('select * from mdl_doc_category_recommend_my where id='.$categoryrecommendid.';');

?>
<div class="pageContent">
    <form method="post" action="docroom/categoryrecommendlist_post_handler.php?title=edit&oldcategoryid=<?php echo $categoryrecommend->categoryid;?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p  style="margin: 20px 20px">
                <label>分类：</label>
                <?php
                /**start zxf 查询所有分类**/
                $categories=$DB->get_records_sql('select * from mdl_doc_categories_my');
                $selectoption='<select name="categoryid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($categories as $category){
                    if($category->id==$categoryrecommend->categoryid)
                        $selectoption=$selectoption.'<option value="'.$category->id.'" selected="selected">'.$category->name.'</option> ';
                    else
                        $selectoption=$selectoption.'<option value="'.$category->id.'">'.$category->name.'</option>';
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
