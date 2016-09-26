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
	$sql2 = 'select count(*) as sumnum from mdl_doc_user_upload_my a where a.upload_userid in ('.$org_allUserIDStr.')'.$sql;
	$sumnum = $DB->get_record_sql($sql2);
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$docs = $DB->get_records_sql('select 
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
	from mdl_doc_user_upload_my a 
	join mdl_user b on a.upload_userid=b.id
	where a.upload_userid in ('.$org_allUserIDStr.')
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
	
	<table class="table" width="100%" layoutH="60">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="80" align="center">文档名称</th>
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
			foreach($docs as $doc){
				if($doc->admin_check==0){
					$doc_status='未审核';
					/**Start cx 审核成功后跳到指定页面 增加参数20160723*/
					$links='<a href="docroom/user_upload_post_handler.php?title=pass&docid='.$doc->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“通过”吗?"><span>通过</span></a> | 
					<a href="docroom/user_upload_post_handler.php?title=unpass&docid='.$doc->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“不通过”吗?"><span>不通过</span></a>';
					/**End cx 审核成功后跳到指定页面 20160723*/
				}
				elseif($doc->admin_check==1){
					$doc_status='已通过';
					$links='';
				}
				else{
					$doc_status='未通过';
					$links='';
				}
				echo '
				<tr target="docid" rel="'.$doc->id.'" >
					<td>'.$offset.'</td>
					<td>'.$doc->name.'</td>
					<td><img src="'.$doc->pictrueurl.'" height="80" width="60" /></td>
					<td>'.$doc->summary.'</td>
					<td>'.userdate($doc->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$doc->suffix.'</td>
					<td>'.$doc->size.'</td>
					<td>'.$doc->firstname.'</td>
					<td><a href="'.$doc->url.'">(右键另存为)</a></td>
					<td>'.$doc_status.'</td>
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
