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
			<li><a class="edit" href="picroom/pictagrecommend_edit.php?pictagrecommendid={pictagrecommendid}" target="dialog"><span>设置</span></a></li>
		
			
		</ul>
	</div>
	<table class="table" width="30%" layoutH="138">
		<thead>
		<tr align="center">
			<th width="40">排名</th>
			<th width="120">搜索词</th>
			<th width="120">搜索背景图</th>
		</tr>
		</thead>
		<tbody>
		<?php /**satrt zxf 查询分类全部基本信息**/?>
		<?php
		require_once("../../../config.php");
		global $DB;
		$pictagrecommends=$DB->get_records_sql('select * from mdl_pic_recommended_search');
		foreach($pictagrecommends as $pictagrecommend){
			echo '
				<tr target="pictagrecommendid" rel="'.$pictagrecommend->id.'" align="center">
				<td>第'.$pictagrecommend->id.'名</td>
				<td>'.$pictagrecommend->name.'</td>
				<td><img src="'.$pictagrecommend->picurl.'" height="69" width="60" /></td>
				</tr>
				';
		}
		?>

		<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
</div>
