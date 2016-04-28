<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$ebookid = $_GET['ebookid'];
$ebook = $DB->get_record_sql('select * from mdl_ebook_my where id='.$ebookid.';');

 ?>
<div class="pageContent">
	<form method="post" action="bookroom/ebook_post_handler.php?title=edit&ebookid=<?php echo $ebookid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<label>电子书名称：</label>
				<input name="name" type="text" size="30" value="<?php echo $ebook->name;?>"/>
			</p>
			<p>
				<label>图片：</label>
				<input name="pictrueurl" type="text" size="30" value="<?php echo $ebook->pictrueurl;?>" />
			</p>
			<p>
				<label>分类：</label>
				<input name="categoryid" type="text" size="30" value="<?php echo $ebook->categoryid;?>" />
			</p>
			<p>
				<label>作者：</label>
				<input name="author" type="text" size="30" value="<?php echo $ebook->author;?>" />
			</p>
			<p>
				<label>简介：</label>
				<input name="summary" type="text" size="30" value="<?php echo $ebook->summary;?>" />
			</p>
			<p>
				<label>总字数：</label>
				<input name="wordcount" type="text" size="30" value="<?php echo $ebook->wordcount;?>" />
			</p>
			<p>
				<label>上传电子书：</label>
				<input name="url" type="text" size="30" value="<?php echo $ebook->url;?>"/>
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
