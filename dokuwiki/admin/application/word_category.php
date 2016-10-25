<?php
/** Start cx 编辑分类 20160930*/
require_once('../../../config.php');
$wordid = $_GET['wordid'];
$word = $DB->get_record_sql('select * from mdl_dokuwiki_word_my where id='.$wordid.';');
?>
<div class="pageContent">
	<form method="post" action="application/word_post_handler.php?title=editcategory&wordid=<?php echo $wordid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<label>词条名称：</label><label><?php echo $word->word_name;?></label>
			</p>
			<p>
				<label>分类：</label>
				<?php
				/**start cx 输出所有分类 20160930**/
				require_once("../../../config.php");
				global $DB;
				$categories=$DB->get_records_sql('select * from mdl_dokuwiki_categories_my order by parent,id');
				$selectoption='<select name="categoryid" class="required combox" class="required">
                <option value="0">无</option>';
				foreach($categories as $category){
					if($category->id==$word->categoryid)
						$selectoption=$selectoption.'<option value="'.$category->id.'" selected="selected">'.$category->name.'</option>';
					else
						$selectoption=$selectoption.'<option value="'.$category->id.'">'.$category->name.'</option>';
				}
				$selectoption=$selectoption.'</select>';
				echo $selectoption;
				/**end cx 输出所有分类 20160930**/
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
