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
			<li><a class="add" href="docroom/category_add.php" target="dialog"><span>添加</span></a></li>
			<li><a class="delete" href="docroom/category_post_handler.php?title=delete&categoryid={categoryid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="docroom/category_edit.php?categoryid={categoryid}" target="dialog"><span>修改</span></a></li>
		</ul>
	</div>
	<table class="table" width="40%" layoutH="138">
		<thead>
			<tr align="center">
				<th width="80">序号</th>
				<th width="120">分类名称</th>
				<th width="120">上级分类</th>
				<th width="120">分类级别</th>
			</tr>
		</thead>
		<tbody>
			<?php /**satrt zxf 查询分类全部基本信息**/?>
			<?php
			require_once("../../../config.php");
			global $DB;
			$categories=$DB->get_records_sql('select * from mdl_doc_categories_my order by parent');
			$n=1;
			foreach($categories as $category){
				$showstr= '<tr target="categoryid" rel="'.$category->id.'" align="center">';
				if($category->parent==-1){
					$showstr=$showstr.'<td>-1</td>
									   <td>'.$category->name.'</td>
									   <td>无</td>
									   <td>无</td>';
				}
				else{
					$showstr=$showstr.'
					<td>'.$n.'</td>
					<td>'.$category->name.'</td>
					';
					if(!$category->parent)
					{
						$showstr=$showstr.'<td>（顶级分类）</td><td>1</td></tr>';
					}
					else
					{
						$parentcategory=$DB->get_record_sql('select * from mdl_doc_categories_my where id='.$category->parent);
						$showstr=$showstr.'<td>'.$parentcategory->name.'</td>';
						if($parentcategory->parent==0){
							$showstr=$showstr.'<td>2</td>';
						}
						else{
							$showstr=$showstr.'<td>3</td>';
						}
					}
					$showstr=$showstr.'</tr>';
					$n++;
				}
				echo $showstr;
			}
			?>

			<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="100">100</option>

			</select>
			<span>条，共<?php echo count($categories);?>条</span>
		</div>
		<div class="pagination" targetType="navTab" totalCount="" numPerPage="100" pageNumShown="10" currentPage="1">
		</div>
	</div>
</div>
