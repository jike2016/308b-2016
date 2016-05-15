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
			<!--<li><a class="delete" href="bookroom/recommendlist_post_handler.php?title=delete&recommendid={recommendid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>-->
			<li><a class="edit" href="docroom/recommendlist_edit.php?recommendid={recommendid}" target="dialog"><span>修改</span></a></li>
		
			
		</ul>
	</div>
	<table class="table" width="30%" layoutH="138">
		<thead>
		<tr align="center">
			<th width="50">排名</th>
			<th >文档名称</th>
		</tr>
		</thead>
		<tbody>
		<?php /**satrt zxf 查询分类全部基本信息**/?>
		<?php
		require_once("../../../config.php");
		global $DB;
		$recommends=$DB->get_records_sql('select a.id,b.name from mdl_doc_recommendlist_my as a LEFT JOIN mdl_doc_my b on a.docid=b.id order by a.id');
		foreach($recommends as $recommend){
			echo '
				<tr target="recommendid" rel="'.$recommend->id.'" align="center">
				<td>第'.$recommend->id.'名</td>
				<td>'.$recommend->name.'</td>
				</tr>
				';
		}
		?>

		<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
</div>
