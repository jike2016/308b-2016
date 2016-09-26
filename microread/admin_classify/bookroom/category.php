<form id="pagerForm" method="post" action="demo_page1.html">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="${model.numPerPage}" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
</form>


<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="" method="post">
	<div class="searchBar">
		<!--<ul class="searchContent">
			<li>
				<label>我的客户：</label>
				<input type="text"/>
			</li>
			<li>
			<select class="combox" name="province">
				<option value="">所有省市</option>
				<option value="北京">北京</option>
				<option value="上海">上海</option>
				<option value="天津">天津</option>
				<option value="重庆">重庆</option>
				<option value="广东">广东</option>
			</select>
			</li>
		</ul>
		-->
		
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="bookroom/category_add.php" target="dialog"><span>添加</span></a></li>
			<li><a class="delete" href="bookroom/category_post_handler.php?title=delete&categoryid={categoryid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/category_edit.php?categoryid={categoryid}" target="dialog"><span>修改</span></a></li>
		
			
		</ul>
	</div>
	<table class="table" width="30%" layoutH="138">
		<thead>
			<tr align="center">
				<th width="40">序号</th>
				<th width="120">分类名称</th>
				<th width="120">上级分类</th>
			</tr>
		</thead>
		<tbody>
			<?php /**satrt zxf 查询分类全部基本信息**/?>
			<?php
			require_once("../../../config.php");
			global $DB;
			$categories=$DB->get_records_sql('select * from mdl_ebook_categories_my');
			$n=1;
			foreach($categories as $category){
			$showstr= '
				<tr target="categoryid" rel="'.$category->id.'" align="center">
				<td>'.$n.'</td>
				<td>'.$category->name.'</td>
				';
				if(!$category->parent)
				{
					$showstr=$showstr.'<td>（顶级分类）</td></tr>';
				}
				else
				{
					$parentcategories=$DB->get_records_sql('select * from mdl_ebook_categories_my where id='.$category->parent);
					$showstr=$showstr.'<td>'.$parentcategories[$category->parent]->name.'</td></tr>';
				}
				echo $showstr;
				$n++;
			}
			?>

			<?php /**end zxf 查询分类全部基本信息**/?>
		</tbody>
	</table>
</div>
