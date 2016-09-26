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
$categoryrs_recommend_docs=$DB->get_records_sql('select a.id ,b.id as bid ,b.name,b.summary,b.pictrueurl,b.size,b.suffix,b.timecreated,b.uploaderid,b.url from mdl_doc_category_recommend_my a LEFT JOIN mdl_doc_my b on a.docid=b.id where a.categoryid='.$_GET['categoryid'].' order by a.id');//作者数量少基本不会有第二页。。。所以先不做分页了
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
            <li><a class="edit" href="docroom/categoryrecommendlistdoc_edit.php?categoryrecommenddocid={categoryrecommenddocid}" target="dialog"><span>设置</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="20" align="center">序号</th>
            <th width="120" align="center">文档名称</th>
            <th width="80" align="center">图片</th>
            <th align="center">简介</th>
            <th width="80" align="center">上传时间</th>
            <th width="80" align="center">格式</th>
            <th width="80" align="center">大小</th>
        </tr>
        </thead>
        <tbody>
       
        <?php
		$offset++;
        foreach($categoryrs_recommend_docs as $categoryrs_recommend_doc){
                $str= '
				<tr target="categoryrecommenddocid" rel="'.$categoryrs_recommend_doc->id.'" align="center">
				<td  width="60">'.$offset.'</td>
				<td>'.$categoryrs_recommend_doc->name.'</td>
					<td><img src="'.$categoryrs_recommend_doc->pictrueurl.'" height="120" width="90" /></td>
					<td>'.$categoryrs_recommend_doc->summary.'</td>';
                if($categoryrs_recommend_doc->timecreated==null){
                    $str=$str.'<td></td>';
                }
                else{
                    $str=$str.'
					<td>'.userdate($categoryrs_recommend_doc->timecreated,'%Y-%m-%d %H:%M').'</td>';
                }
                $str=$str.'
					<td>'.$categoryrs_recommend_doc->suffix.'</td>
					<td>'.$categoryrs_recommend_doc->size.'</td>
					</tr>
				';
            echo $str;

			$offset++;
        }
        ?>

        <?php /**end zxf 查询分类全部基本信息**/?>
        </tbody>
    </table>
</div>
