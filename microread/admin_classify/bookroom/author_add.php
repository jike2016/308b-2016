
<div class="pageContent">
    <form method="post" action="bookroom/author_post_handler.php?title=add"  enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <dl>
                <dt>作者姓名：</dt>
                <dd><input name="name" type="text" size="30" value="" class="required"/></dd>
            </dl>
			
            <dl style="height:50px">
                <dt>简介：</dt>
                <dd><textarea name="summary" cols="60" rows="3" class="required"></textarea></dd>
             </dl>
			 <dl>
				<dt>作者头像：(jpg ,png ,gif)</dt>
				<dd><input name="pictrueurl" type="file" class=""/></dd>
			 </dl>
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
