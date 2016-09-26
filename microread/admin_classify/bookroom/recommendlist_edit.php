<?php
/** Start cx 编辑推荐榜 20160427*/
require_once('../../../config.php');
global $DB;
$recommendid = $_GET['recommendid'];
$recommend = $DB->get_record_sql('select * from mdl_ebook_recommendlist_my where id='.$recommendid.';');
$ebookoks = $DB->get_records_sql('select * from mdl_ebook_my');
?>
<div class="pageContent">
	<form method="post" action="bookroom/recommendlist_post_handler.php?title=edit&recommendid=<?php echo $recommendid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<?php
				echo '<label>第'.$recommendid.'名：</label>';
				/**start zxf 选择推荐电子书**/
				$selectoption='<select name="ebookid" class="required combox" class="required"><option value="">请选择</option>';
					foreach($ebookoks as $ebook){
						if($recommend->ebookid==$ebook->id)
							$selectoption=$selectoption.'<option value="'.$ebook->id.'" selected="selected">'.$ebook->name.'</option> ';
						else
							$selectoption=$selectoption.'<option value="'.$ebook->id.'">'.$ebook->name.'</option>';
					}
				$selectoption=$selectoption.'</select>';
				echo $selectoption;
				/**end zxf 选择推荐电子书**/?>
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
