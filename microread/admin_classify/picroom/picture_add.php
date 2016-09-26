<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="picroom/picture_post_handler.php?title=add" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);">
        <div layoutH="56">
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片名称：</label>
                <input name="name" type="text" size="30" value="" class="required"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片上传：(jpg ,png ,gif)</label>
                <input name="picurl" type="file" class="required"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px;">
                <label>标签选择：</label>
                <?php
                require_once("pictagmylib.php");
                $alltags = getpictagmylist();
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
