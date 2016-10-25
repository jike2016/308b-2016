<?php 
/**Start cx 审核页面 20160929*/
if(isset($_GET['pageNum'])) {
	$pagenummy = $_GET['pageNum'];//获取当前页数
}
elseif(isset($_POST['pageNum'])){
	$pagenummy = $_POST['pageNum'];//获取当前页数
}
else{
	$pagenummy=1;
}
require_once('../../../config.php');
global $DB;
//如果还没有查过总记录数则查询
if(isset($_POST['sumnum'])){
	$sumnum = $_POST['sumnum'];
}
else{
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_dokuwiki_review_my a '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$numPerPage=20;//每页显示行数
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$words = $DB->get_records_sql('select 
	a.id,
	a.entryid,
	a.entryurl,
	a.entrytype,
	a.entrystate,
	a.submittime,
	b.firstname
	from mdl_dokuwiki_review_my a 
	join mdl_user b on a.userid=b.id
	ORDER BY submittime desc
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
	<table class="table" width="900px" layoutH="60">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="200" align="center">词条</th>
				<th width="100" align="center">提交类型</th>
				<th width="100" align="center">提交用户</th>
				<th width="150" align="center">提交时间</th>
				<th width="100" align="center">对比当前版本</th>
				<th  width="150" align="center">审阅状态</th>
				<th width="120" align="center">操作</th>
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			$offset++;
			foreach($words as $word){
				if($word->entrystate==0){
					$word_status= '<td style="color:#0000f5">未审核</td>';
					$links='<a href="application/examine_manage_post_handler.php?title=pass&wordid='.$word->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“通过”吗?"><span>通过</span></a> | 
					<a href="application/examine_manage_post_handler.php?title=unpass&wordid='.$word->id.'&pageNum='.$pagenummy.'" target="ajaxTodo" title="确定“不通过”吗?"><span>不通过</span></a>';
				}
				elseif($word->entrystate==1){
					$word_status= '<td style="color:#3bb132">已通过</td>';
					$links='';
				}
				else{
					$word_status='<td style="color:#f50b0b">未通过</td>';
					$links='';
				}
				if($word->entrytype==0){
					$word_type='新词条';
				}
				else{
					$word_type='修改词条';
				}
				echo '
				<tr target="wordid" rel="'.$word->id.'" >
					<td>'.$offset.'</td>
					<td><a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/'.$word->entryurl.'">'.$word->entryid.'</a></td>
					<td>'.$word_type.'</td>	
					
					<td>'.$word->firstname.'</td>
					<td>'.userdate($word->submittime,'%Y-%m-%d %H:%M').'</td>
					<td><a target="_blank" href="../doku.php?id='.$word->entryid.'&rev='.$word->submittime.'&do=diff">对比</a></td>
					'.$word_status.'
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
