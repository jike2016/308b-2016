<?php
/** Start cx 编辑推荐榜 20160427*/
require_once('../../../config.php');
global $DB;
$pictagrecommendid = $_GET['pictagrecommendid'];
$pictagrecommend = $DB->get_record_sql('select * from mdl_pic_recommended_search where id='.$pictagrecommendid.';');
?>
<div class="pageContent">
	<form method="post" enctype="multipart/form-data" action="picroom/pictagrecommend_post_handler.php?title=edit&pictagrecommendid=<?php echo $pictagrecommendid;?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
	<!--<form method="post" enctype="multipart/form-data" action="picroom/pictagrecommend_post_handler.php?title=edit&pictagrecommendid=<?php echo $pictagrecommendid;?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);">-->
		<div class="pageFormContent" layoutH="56">
			<p style="margin: 20px 20px 0px 20px">
				<?php
				echo '<label>第'.$pictagrecommendid.'名</label>';
				?>
			</p>
			<p  style="margin: 20px 20px 0px 20px">
				<label>搜索词：</label>
				<input name="name" type="text" class="required" value="<?php echo $pictagrecommend->name ?>"/>
			</p>
			<p  style="margin: 20px 20px 0px 20px">
				<label>图片上传220*180：(jpg ,png ,bmp ,gif)</label>
				<input name="picurl" type="file"/>
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
