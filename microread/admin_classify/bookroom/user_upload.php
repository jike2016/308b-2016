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
require_once('../../../org_classify/org.class.php');

global $DB;
global $USER;
$org = new org();
$admin_nodeID = $org->get_nodeid_with_userid($USER->id);//获取当前分级管理员所在的单位级别
$org_allUsers = $org->select_node_detailed_all_not_self($admin_nodeID);//获取单位之下的所有人员，除分级管理员自身
$org_allUserIDs = array_column($org_allUsers,'user_id');
$org_allUserIDStr = implode(',',$org_allUserIDs);

//是否有查询条件
if(isset($_POST['keyword'])&&$_POST['keyword']){
	$sql= 'and a.name LIKE \'%'.$_POST['keyword'].'%\'';
}
else{
	$sql='';
}
//如果还没有查过总记录数则查询
if(isset($_POST['sumnum'])){
	$sumnum = $_POST['sumnum'];
}
else{
	$sql2 = 'select count(*) as sumnum from mdl_ebook_user_upload_my a where a.uploaderid in ('.$org_allUserIDStr.')'.$sql;
	$sumnum = $DB->get_record_sql($sql2);
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$ebooks = $DB->get_records_sql('select 
	a.id,
	a.admin_check,
	a.name,
	a.summary,
	a.url,
	a.pictrueurl,
	a.timecreated,
	a.suffix,
	a.size,
	b.firstname
	from mdl_ebook_user_upload_my a 
	join mdl_user b on a.uploaderid=b.id
	where a.uploaderid in ('.$org_allUserIDStr.')
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
			<!--<li><a class="add" href="bookroom/ebook_add.php" target="navTab"><span>添加电子书</span></a></li>
			<li><a class="delete" href="bookroom/ebook_post_handler.php?title=delete&ebookid={ebookid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/ebook_edit.php?ebookid={ebookid}" target="navTab"><span>修改</span></a></li>

		</ul>
	</div>-->
	<table class="table" width="100%" layoutH="60">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="120" align="center">电子书名</th>
				<th width="90" align="center">封面</th>
				<th align="center">简介</th>
				<th width="120" align="center">上传时间</th>
				<th width="80" align="center">格式</th>
				<th width="80" align="center">大小</th>
				<th width="80" align="center">上传者</th>
				<th width="100" align="center">下载</th>
				<!--start zxf 章管理-->
				<th width="80" align="center">状态</th>
				<th width="120" align="center">审核</th>
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			foreach($ebooks as $ebook){
				if($ebook->admin_check==0){
					$ebook_status='未审核';
					$links='<a href="bookroom/user_upload_post_handler.php?title=pass&ebookid='.$ebook->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“通过”吗?"><span>通过</span></a> | 
					<a href="bookroom/user_upload_post_handler.php?title=unpass&ebookid='.$ebook->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“不通过”吗?"><span>不通过</span></a>';
				}
				elseif($ebook->admin_check==1){
					$ebook_status='已通过';
					$links='';
				}
				else{
					$ebook_status='未通过';
					$links='';
				}
				echo '
				<tr target="ebookid" rel="'.$ebook->id.'" >
					<td>'.$offset.'</td>
					<td>'.$ebook->name.'</td>
					<td><img src="'.$ebook->pictrueurl.'" height="80" width="60" /></td>
					<td>'.$ebook->summary.'</td>
					<td>'.userdate($ebook->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$ebook->suffix.'</td>
					<td>'.$ebook->size.'</td>
					<td>'.$ebook->firstname.'</td>
					<td><a href="'.$ebook->url.'">(右键另存为)</a></td>
					<td>'.$ebook_status.'</td>
					<td>'.$links.'
					</td>
					
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
