<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
$ebookid = $_GET['ebookid'];
$ebook = $DB->get_record_sql('select * from mdl_ebook_my where id='.$ebookid.';');
 ?>
<div class="pageContent">
	<form method="post" enctype="multipart/form-data" action="bookroom/ebook_post_handler.php?title=edit&ebookid=<?php echo $ebookid;?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);"">
		<div layoutH="56">
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>电子书名称：</label>
				<input name="name" type="text" size="30" value="<?php echo $ebook->name;?>" class="required"/>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>图片：(jpg ,png , gif)(150*220)</label>
				<input name="pictrueurl" type="file" class="" />
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>分类：</label>
				<?php
				/**start zxf 查询所有分类**/
				require_once("../../../config.php");
				global $DB;
				$categories=$DB->get_records_sql('select *from mdl_ebook_categories_my');
				$selectoption='<select name="categoryid" class="required combox" class="required"><option value="">请选择</option>';
				foreach($categories as $category){
					if($category->id==$ebook->categoryid)
						$selectoption=$selectoption.'<option value="'.$category->id.'" selected="selected">'.$category->name.'</option> ';
					else
						$selectoption=$selectoption.'<option value="'.$category->id.'">'.$category->name.'</option>';
					}

				$selectoption=$selectoption.'</select>';
				echo $selectoption;
				 /**end zxf 查询所有分类**/?>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>作者：</label>
				<?php
				/**start zxf 查询所有分类**/
				require_once("../../../config.php");
				global $DB;
				$authors=$DB->get_records_sql('select *from mdl_ebook_author_my');
				$selectoption='<select name="authorid" class="required combox" class="required"><option value="">请选择</option>';
				foreach($authors as $author){
					if($author->id==$ebook->authorid)
						$selectoption=$selectoption.'<option value="'.$author->id.'" selected="selected">'.$author->name.'</option> ';
					else
						$selectoption=$selectoption.'<option value="'.$author->id.'">'.$author->name.'</option>';
				}
				$selectoption=$selectoption.'</select>';
				echo $selectoption;
				/**end zxf 查询所有分类**/?>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>简介：</label>
				<textarea name="summary" cols="80" rows="2" class="required" ><?php echo $ebook->summary;?></textarea>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px" class="required">
				<label>总字数：</label>
				<input name="wordcount" type="text" size="30" value="6" class="required" value="<?php echo $ebook->wordcount;?>"/>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
				<label>上传电子书：(pdf,txt,rar,zip)</label>
				<input name="url" type="file" class=""/>
			</p>
			<p class="pageFormContent" style="margin: 20px 20px">
			<label>标签选择：</label>
				<?php 
					require_once("../../tagmylib.php");
					$alltags = gettagmylist();
					$tag_selecteds = gettagmy_selected('mdl_ebook_my',$ebookid);
					foreach($alltags as $tagmy){
						$n=false;
						foreach($tag_selecteds as $tag_selected){
							if($tagmy->id==$tag_selected->tagid){
								echo '<label><input type="checkbox" name="tagmy[]" value="'.$tagmy->id.'" checked="checked" s/>'.$tagmy->tagname.'</label>';
								$n=true;
							}
						}
						if($n==false){
							echo '<label><input type="checkbox" name="tagmy[]" value="'.$tagmy->id.'" />'.$tagmy->tagname.'</label>';
						}
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

