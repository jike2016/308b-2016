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
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_doc_category_recommend_my  '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$categoryrs_recommends=$DB->get_records_sql('select a.id ,a.categoryid,b.name from mdl_doc_category_recommend_my a LEFT JOIN mdl_doc_categories_my b on a.categoryid=b.id group by categoryid ORDER BY a.id');//作者数量少基本不会有第二页。。。所以先不做分页了
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
            <li><a class="edit" href="docroom/categoryrecommendlist_edit.php?categoryrecommendid={categoryrecommendid}" target="dialog"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="30%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="20">序号</th>
            <th width="80">推荐分类名称</th>
			<th width="90">推荐分类文档管理</th>
        </tr>
        </thead>
        <tbody>
       
        <?php
		$offset++;
        foreach($categoryrs_recommends as $categoryrs_recommend){
            if($categoryrs_recommend->categoryid==-1||$categoryrs_recommend->categoryid==0){
                echo '
				<tr target="categoryrecommendid" rel="'.$categoryrs_recommend->id.'" align="center">
				<td  width="60">'.$offset.'</td>
				<td  width="80">无</td>
				<td  width="80">无</td>
				</tr>
				';
            }
            else{
                echo '
				<tr target="categoryrecommendid" rel="'.$categoryrs_recommend->id.'" align="center">
				<td  width="60">'.$offset.'</td>
				<td  width="80">'.$categoryrs_recommend->name.'</td>
				<td  width="80"><a class="button" href="docroom/categoryrecommendlistdoc.php?categoryid='.$categoryrs_recommend->categoryid.'" target="navTab" rel="categoryrecommendlistdoc"><span>推荐分类文档管理</span></a></td>
				</tr>
				';
            }

			$offset++;
        }
        ?>

        <?php /**end zxf 查询分类全部基本信息**/?>
        </tbody>
    </table>
</div>
