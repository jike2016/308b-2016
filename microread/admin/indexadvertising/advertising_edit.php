<?php
/** Start zxf 编辑微阅首页广告栏 20160427*/
require_once('../../../config.php');
global $DB;
$advertisingid = $_GET['advertisingid'];
$adverting = $DB->get_record_sql('select * from mdl_microread_indexad_my where id='.$advertisingid.';');
?>
<div class="pageContent">
	<form method="post" action="indexadvertising/advertising_post_handler.php?title=edit&advertisingid=<?php echo $advertisingid;?>" class="pageForm required-validate" enctype="multipart/form-data" onsubmit="return iframeCallback(this, dialogAjaxDone);">
		<div class="pageFormContent" layoutH="56">
			<p>
				<?php
				echo '<label>序号：'.$advertisingid.'</label>';
				?>
			</p>
			<p>
				<label>广告图：</label>
				<input name="picurl" type="file" />
			</p>
			<p>
				<label>图片链接：</label>
				<input name="linkurl" type="text" value="<?php echo $adverting->linkurl?>" class="required" style="width:200px;"/>
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
