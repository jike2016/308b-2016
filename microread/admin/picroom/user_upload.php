<?php 
$numPerPage=10;//每页显示行数
/**Start cx 审核成功后跳到指定页面 增加判断get参数 20160723*/
if(isset($_GET['pageNum'])) {
	$pagenummy = $_GET['pageNum'];//获取当前页数
}
elseif(isset($_POST['pageNum'])){
	$pagenummy = $_POST['pageNum'];//获取当前页数
}
/**End cx 审核成功后跳到指定页面 20160723*/
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
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_pic_user_upload_my a '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$pics = $DB->get_records_sql('
	select 
	a.id,
	a.admin_check,
	a.name,
	a.picurl,
	a.timecreated,
	a.suffix,
	a.size,
	b.firstname
	from mdl_pic_user_upload_my a 
	join mdl_user b on a.uploaderid=b.id
	'.$sql.'
	ORDER BY timecreated desc
	limit '.$offset.','.$numPerPage.';');
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

</div>
<div class="pageContent">
	<!--<div class="panelBar">
		<ul class="toolBar">
			<!--<li><a class="add" href="bookroom/pic_add.php" target="navTab"><span>添加电子书</span></a></li>
			<li><a class="delete" href="bookroom/pic_post_handler.php?title=delete&picid={picid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/pic_edit.php?picid={picid}" target="navTab"><span>修改</span></a></li>

		</ul>
	</div>-->
	<table class="table" width="50%" layoutH="60">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="80" align="center">图片</th>
				<th width="120" align="center">图片描述</th>
				<th width="120" align="center">上传时间</th>
				<th width="80" align="center">格式</th>
				<th width="80" align="center">大小</th>
				<th width="80" align="center">上传者</th>
				<!--start zxf 章管理-->
				<th width="80" align="center">状态</th>
				<th width="120" align="center">审核</th>
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			foreach($pics as $pic){
				if($pic->admin_check==0){
					$pic_status='未审核';
					/**Start cx 审核成功后跳到指定页面 增加参数20160723*/
					$links='<a href="picroom/user_upload_post_handler.php?title=pass&picid='.$pic->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“通过”吗?"><span>通过</span></a> | 
					<a href="picroom/user_upload_post_handler.php?title=unpass&picid='.$pic->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“不通过”吗?"><span>不通过</span></a>';
					/**End cx 审核成功后跳到指定页面 20160723*/
				}
				elseif($pic->admin_check==1){
					$pic_status='已通过';
					$links='';
				}
				else{
					$pic_status='未通过';
					$links='';
				}
				echo '
				<tr target="picid" rel="'.$pic->id.'" >
					<td>'.$offset.'</td>
					<td><img src="'.$pic->picurl.'" height="120" width="90" /></td>
					<td>'.$pic->name.'</td>
					<td>'.userdate($pic->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$pic->suffix.'</td>
					<td>'.$pic->size.'</td>
					<td>'.$pic->firstname.'</td>
					<td>'.$pic_status.'</td>
					<td>'.$links.'</td>
					
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
