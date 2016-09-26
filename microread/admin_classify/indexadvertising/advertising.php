<form id="pagerForm" method="post" action="demo_page1.html">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>


<div class="pageHeader">
	
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<!--<li><a class="add" href="bookroom/category_add.php" target="navTab"><span>添加</span></a></li>-->
			<li><a class="delete" href="indexadvertising/advertising_post_handler.php?title=delete&advertisingid={advertisingid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="indexadvertising/advertising_edit.php?advertisingid={advertisingid}" target="dialog"><span>设置</span></a></li>


		</ul>
	</div>
	<table class="table" width="30%" layoutH="138">
		<thead>
		<tr align="center">
			<th width="40">排序</th>
			<th width="120">广告图片</th>
			<th width="120">链接网址</th>
		</tr>
		</thead>
		<tbody>
		<?php /**satrt zxf 查询分类全部基本信息**/?>
		<?php
		require_once("../../../config.php");
		global $DB;
		$advertisings=$DB->get_records_sql('select * from mdl_microread_indexad_my ');
		foreach($advertisings as $advertising){
			echo '
				<tr target="advertisingid" rel="'.$advertising->id.'" align="center">
				<td>'.$advertising->id.'</td>
				<td><img src="'.$advertising->picurl.'" height="80" width="200"></td>
				<td>'.$advertising->linkurl.'</td>
				</tr>
				';
		}
		?>

		<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
	
</div>
