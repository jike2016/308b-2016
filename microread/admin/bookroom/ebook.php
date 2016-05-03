<?php 
$numPerPage=10;//每页显示行数
if(isset($_POST['pageNum'])){
	$pagenummy = $_POST['pageNum'];//获取当前页数
}
else{
	$pagenummy=1;
}
require_once('../../../config.php');
global $DB;
//是否有查询条件
if(isset($_POST['keyword'])&&$_POST['keyword']){
	$sql= 'where a.name LIKE \'%'.$_POST['keyword'].'%\'';
}
else{
	$sql='';
}
//如果还没有查过总记录数则查询
if(isset($_POST['sumnum'])){
	$sumnum = $_POST['sumnum'];
}
else{
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_ebook_my a '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$ebooks = $DB->get_records_sql('select 
	a.id,a.name,a.authorid,a.summary,a.url,a.pictrueurl,a.timecreated,a.wordcount,a.suffix,a.size,b.name as categoryname, c.`name` as authorname
	FROM mdl_ebook_my a 
	join mdl_ebook_categories_my b on a.categoryid=b.id 
	join mdl_ebook_author_my c on a.authorid=c.id
	'.$sql.'
	ORDER BY timecreated desc
	limit '.$offset.','.$numPerPage.';');
//查询没有分类或者没有作者的书
$errorebooks = $DB->get_records_sql('select * from mdl_ebook_my where authorid=0 or categoryid=0');
?>

<form id="pagerForm" method="post" action="">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keyword" value="<?php if(isset($_POST['keyword']))echo $_POST['keyword'];?>" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="<?php echo $numPerPage;?>" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
	<input type="hidden" name="sumnum" value="<?php echo $sumnum;?>" />
</form>
<!--
<style>

.grid .gridTbody td div {
    display: block;
    overflow: hidden;
    height: 200px;
    white-space: nowrap;
    line-height: 21px;
}
</style>-->
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
		<table class="searchContent">
			<tr>
				<!--<td>
					我的客户：<input type="text" name="keyword" />
				</td>
				<td>
					<select class="combox" name="province">
						<option value="">所有省市</option>
						<option value="北京">北京</option>
						<option value="上海">上海</option>
						<option value="天津">天津</option>
						<option value="重庆">重庆</option>
						<option value="广东">广东</option>
					</select>
				</td>
				<td>
					建档日期：<input type="text" class="date" readonly="true" />
				</td>-->
				<td>
					电子书名：<input type="text" name="keyword" />
					<div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div>
				</td>
			</tr>
		</table>
		<div class="subBar">
			<ul>
				<!--<li><div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div></li>-->
				<!--<li><a class="button" href="demo_page6.html" target="dialog" mask="true" title="查询框"><span>高级检索</span></a></li>-->
			</ul>
		</div>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="bookroom/ebook_add.php" target="navTab"><span>添加电子书</span></a></li>
			<li><a class="delete" href="bookroom/ebook_post_handler.php?title=delete&ebookid={ebookid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/ebook_edit.php?ebookid={ebookid}" target="navTab"><span>修改</span></a></li>

		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="80" align="center">电子书名</th>
				<th width="90" align="center">图片</th>
				<th width="80" align="center">分类</th>
				<th width="80" align="center">作者</th>
				<th align="center">简介</th>
				<th width="120" align="center">上传时间</th>
				<th width="80" align="center">格式</th>
				<th width="80" align="center">总字数</th>
				<th width="80" align="center">大小</th>
				<!--start zxf 章管理-->
				<th width="80" align="center">章管理</th>
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			foreach($errorebooks as $ebook){
				echo '
				<tr target="ebookid" rel="'.$ebook->id.'" >
					<td>-1</td>
					<td>'.$ebook->name.'</td>
					<td><img src="'.$ebook->pictrueurl.'" height="200" width="150" /></td>';
				if($ebook->categoryid==0)
					echo "<td>(无分类)</td>";
				else
					echo '<td>'.$ebook->categoryid.'</td>';
				if($ebook->authorid==0)
					echo "<td>(无作者)</td>";
				else
					echo '<td>'.$ebook->authorid.'</td>';
				echo '
					<td>'.$ebook->summary.'</td>
					<td>'.userdate($ebook->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$ebook->suffix.'</td>
					<td>'.$ebook->wordcount.'</td>
					<td>'.$ebook->size.'</td>
				</tr>
				';
			}
			$offset++;
			foreach($ebooks as $ebook){
				echo '
				<tr target="ebookid" rel="'.$ebook->id.'" >
					<td>'.$offset.'</td>
					<td>'.$ebook->name.'</td>
					<td><img src="'.$ebook->pictrueurl.'" height="200" width="150" /></td>
					<td>'.$ebook->categoryname.'</td>
					<td>'.$ebook->authorname.'</td>
					<td>'.$ebook->summary.'</td>
					<td>'.userdate($ebook->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$ebook->suffix.'</td>
					<td>'.$ebook->wordcount.'</td>
					<td>'.$ebook->size.'</td>
					<td><a class="button" href="bookroom/chapter.php?ebookid='.$ebook->id.'" target="navTab" rel="ebookchapter"><span>章节管理</span></a></td>
					
				</tr>
				';
				$offset++;
			}
			
		/** End */
		?>
		</tbody>
	</table>
	<div class="panelBar">
		<div class="pages">
			<span>每页显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="<?php echo $numPerPage;?>"><?php echo $numPerPage;?></option>
			</select>
			<span>条，共<?php echo $sumnum;?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="<?php echo $sumnum;?>" numPerPage="<?php echo $numPerPage;?>" pageNumShown="10" currentPage="<?php echo $pagenummy;?>"></div>

	</div>
</div>
