<?php 
$numPerPage=100;//每页显示行数
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
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_doc_recommend_authorlist_my  '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$author_recommends=$DB->get_records_sql('select a.id ,a.userid,b.firstname as name from mdl_doc_recommend_authorlist_my as a LEFT JOIN mdl_user as b on a.userid=b.id group by a.id ');//查询上传者推荐表和firstname
?>
<form id="pagerForm" method="post" action="">
    <input type="hidden" name="status" value="${param.status}">
   	<input type="hidden" name="keyword" value="<?php if(isset($_POST['keyword']))echo $_POST['keyword'];?>" />
	<input type="hidden" name="pageNum" value="1" />
	<input type="hidden" name="numPerPage" value="<?php echo $numPerPage;?>" />
	<input type="hidden" name="orderField" value="${param.orderField}" />
	<input type="hidden" name="sumnum" value="<?php echo $sumnum;?>" />
</form>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="edit" href="docroom/authorrecommendlist_edit.php?authorrecommendid={authorrecommendid}" target="dialog"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="20%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="40">序号</th>
            <th width="80">推荐作者姓名</th>
			<th width="80">推荐作者文档管理</th>
        </tr>
        </thead>
        <tbody>
       
        <?php
		$offset++;
        foreach($author_recommends as $author_recommend){
            if($author_recommend->userid==-1){
                echo '
				<tr target="authorrecommendid" rel="'.$author_recommend->id.'" align="center">
				<td  width="40">'.$offset.'</td>
				<td  width="80">无</td>
				<td  width="80">无</td>
				</tr>
				';
            }
            else{
                echo '
				<tr target="authorrecommendid" rel="'.$author_recommend->id.'" align="center">
				<td  width="40">'.$offset.'</td>
				<td  width="80">'.$author_recommend->name.'</td>
				<td  width="80"><a class="button" href="docroom/authorrecommendlistdoc.php?authorrecommendid='.$author_recommend->id.'" target="navTab" rel="categoryrecommendlistdoc"><span>推荐分类文档管理</span></a></td>
				</tr>
				';
            }

			$offset++;
        }
        ?>

        <?php /**end zxf 查询全部基本信息**/?>
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
