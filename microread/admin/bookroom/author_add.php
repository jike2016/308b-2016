
<div class="pageContent">
    <form method="post" action="bookroom/author_post_handler.php?title=add" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);"">
        <div class="pageFormContent" layoutH="56">
            <p>
                <label>作者姓名：</label>
                <input name="name" type="text" size="30" value="" class="required"/>
            </p>
			<p>
				<label>作者头像：</label>
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
