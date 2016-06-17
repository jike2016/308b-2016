<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$sectionid = $_GET['sectionid'];
$section = $DB->get_record_sql('select * from mdl_ebook_section_my where id='.$sectionid.';');

?>
<div class="pageContent" xmlns="http://www.w3.org/1999/html">
    <form method="post" enctype="multipart/form-data" action="bookroom/section_post_handler.php?title=edit&sectionid=<?php echo $sectionid; ?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);">
    <div  layoutH="56" style="margin: 20px 20px 0px">
        <p class="pageFormContent">
            <label>节名称：</label>
            <input name="chapterid" type="hidden" size="30" value="<?php echo $_GET['chapterid'];?>"/>
            <input name="name" type="text" size="30" value="<?php echo $section->name?>" class="required"/>
        </p>
        <p class="pageFormContent">
            <label>节排序：</label>
            <input name="sectionorder" type="text" size="30" value="<?php echo $section->sectionorder?>" class="required digits"/>
        </p>
        <p class="pageFormContent">
            <?php

            $showstr='<label>选择节内容类型：</label>
            <select name="type" class="required" onchange="checkField(this.value)">
                <option value="">请选择</option>';
            if($section->type==1) {
                $showstr=$showstr.'
                <option value="1" selected="selected">文本</option>
                <option value="2">pdf</option>
              </select>';
                $showstr=$showstr.'
                <p id="text" style="display:block" class="pageFormContent">
                <label>输入文本内容：</label>
                <textarea class="editor" name="text" rows="30" cols="150"
                          upImgUrl="bookroom/section_upload_image.php?title=addimg"upImgExt="jpg,jpeg,gif,png" >'. $section->text.'</textarea>';
                $showstr=$showstr.'
                <p id="pdf" style="display:none" class="pageFormContent">
                    <label>上传pdf：</label>
                    <input name="pdf_url" type="file" value="'.$section->pdfurl.'"/>
                 </p>';
            }
            else{
                $showstr=$showstr.'
                <option value="1">文本</option>
                <option value="2" selected="selected">pdf</option>
              </select>';
                $showstr=$showstr.'
                <p id="text" style="display:none" class="pageFormContent">
                <label>输入文本内容：</label>
                <textarea class="editor" name="text" rows="30" cols="150"
                          upImgUrl="bookroom/section_upload_image.php?title=addimg" upImgExt="jpg,jpeg,gif,png">'. $section->text.'</textarea>';
                $showstr=$showstr.'
                <p id="pdf" style="display:block" class="pageFormContent">
                    <label>上传pdf：</label>
                    <input name="pdf_url" type="file" value="'.$section->pdfurl.'"/>
                 </p>
                 <p><label name="temppdfurl">'.$section->pdfurl.'</label></p>';
            }
            echo $showstr;
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
<script>
    function checkField(val)
    {

        if(val==1)
        {
            document.getElementById("text").style.display="block";
            document.getElementById("pdf").style.display="none";
        }
        else if(val==2)
        {
            document.getElementById("text").style.display="none";
            document.getElementById("pdf").style.display="block";
        }
    }
</script>
