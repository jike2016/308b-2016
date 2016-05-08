<?php
/** Start cx 编辑推荐榜 20160427*/
require_once('../../../config.php');
global $DB;
$recommendid = $_GET['recommendid'];
$recommend = $DB->get_record_sql('select * from mdl_doc_recommendlist_my where id='.$recommendid.';');
$docs = $DB->get_records_sql('select * from mdl_doc_my');
?>
<div class="pageContent">
	<form method="post" action="docroom/recommendlist_post_handler.php?title=edit&recommendid=<?php echo $recommendid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<?php
				echo '<label>第'.$recommendid.'名：</label>';
				/**start zxf 选择推荐电子书**/
				$selectoption='<select name="docid" class="required combox" class="required"><option value="">请选择</option>';
					foreach($docs as $doc){
						if($recommend->ebookid==$doc->id)
							$selectoption=$selectoption.'<option value="'.$doc->id.'" selected="selected">'.$doc->name.'</option> ';
						else
							$selectoption=$selectoption.'<option value="'.$doc->id.'">'.$doc->name.'</option>';
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
