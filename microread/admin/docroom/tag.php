<form id="pagerForm" method="post" action="demo_page1.html">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="docroom/tag_add.php" target="dialog"><span>添加</span></a></li>
			<li><a class="delete" href="docroom/tag_post_handler.php?title=delete&tagid={tagid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="docroom/tag_edit.php?tagid={tagid}" target="dialog"><span>修改</span></a></li>
		</ul>
	</div>
	<table class="table" width="30%" layoutH="78">
		<thead>
			<tr align="center">
				<th width="40">序号</th>
				<th>标签名称</th>
			</tr>
		</thead>
		<tbody>
			<?php /**satrt zxf 查询分类全部基本信息**/?>
			<?php
			require_once("../../../config.php");
			global $DB;
			$tags=$DB->get_records_sql('select * from mdl_doc_tag_my');
			$n=1;
			foreach($tags as $tag){
			$showstr= '
				<tr target="tagid" rel="'.$tag->id.'" align="center">
				<td>'.$n.'</td>
				<td>'.$tag->name.'</td>
				</tr>';
				echo $showstr;
				$n++;
			}
			?>
			<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20">20</option>

			</select>
			<span>条，共<?php echo count($tags);?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="" numPerPage="20" pageNumShown="20" currentPage="1"></div>

	</div>
</div>
