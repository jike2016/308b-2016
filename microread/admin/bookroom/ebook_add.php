
<div class="pageContent">
	<form method="post" enctype="multipart/form-data" action="bookroom/ebook_post_handler.php?title=add" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<label>电子书名称：</label>
				<input name="name" type="text" size="30" value="1"/>
			</p>
			<p>
				<label>图片：</label>
				<input name="pictrueurl" type="file" />
			</p>
			<p>
				<label>分类：</label>
				<input name="categoryid" type="text" size="30" value="3" />
			</p>
			<p>
				<label>作者：</label>
				<input name="author" type="text" size="30" value="4" />
			</p>
			<p>
				<label>简介：</label>
				<input name="summary" type="text" size="30" value="5" />
			</p>
			<p>
				<label>总字数：</label>
				<input name="wordcount" type="text" size="30" value="6" />
			</p>
			<p>
				<label>上传电子书：</label>
				<input name="url" type="file" />
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
