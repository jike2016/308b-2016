<div class="pageContent" >
    <form method="post" enctype="multipart/form-data" action="bookroom/section_post_handler.php?title=add" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);"">
    <div  layoutH="56" style="margin: 20px 20px 0px 20px">
            <p class="pageFormContent">
                <label>节名称：</label>
                <input name="chapterid" type="hidden" size="30" value="<?php echo $_GET['chapterid'];?>" class="required"/>
                <input name="name" type="text" size="30" value="" class="required"/>
            </p>
            <p  class="pageFormContent">
                <label>节排序：</label>
                <input name="sectionorder" type="text" size="30" value="" class="required digits" />
            </p>
            <p class="pageFormContent">
                <label>选择节内容类型：</label>
                <select name="type" class="required" onchange="checkField(this.value)">
                <option value="">请选择</option>
                <option value="1">文本</option>
                <option value="2">pdf</option>
                </select>
            </p>
            <p id="text" style="display: none" class="pageFormContent">
                <label>输入文本内容：</label>
                <textarea class="editor" name="text" rows="8" cols="100"
                          upLinkUrl="upload.php" upLinkExt="zip,rar,txt"
                          upImgUrl="upload.php" upImgExt="jpg,jpeg,gif,png"
                          upFlashUrl="upload.php" upFlashExt="swf"
                          upMediaUrl="upload.php">
                </textarea>
            </p>
            <p id="pdf" style="display: none" class="pageFormContent">
                <label>上传pdf：</label>
                <input name="pdf_url" type="file"/>
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
