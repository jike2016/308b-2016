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
	$sumnum =3 ;
    $sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$author_recommend_doc1=$DB->get_record_sql('select *from mdl_doc_my where id in(select docid1 from mdl_doc_recommend_authorlist_my where id='.$_GET['authorrecommendid'].')');//作者数量少基本不会有第二页。。。所以先不做分页了
$author_recommend_doc2=$DB->get_record_sql('select *from mdl_doc_my where id in(select docid2 from mdl_doc_recommend_authorlist_my where id='.$_GET['authorrecommendid'].')');//作者数量少基本不会有第二页。。。所以先不做分页了
$author_recommend_doc3=$DB->get_record_sql('select *from mdl_doc_my where id in(select docid3 from mdl_doc_recommend_authorlist_my where id='.$_GET['authorrecommendid'].')');//作者数量少基本不会有第二页。。。所以先不做分页了
$author_recommend_docs=array('doc1'=>$author_recommend_doc1,'doc2'=>$author_recommend_doc2,'doc3'=>$author_recommend_doc3);
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
            <li><a class="edit" href="docroom/authorrecommendlistdoc_edit.php?authorrecommenddocid={authorrecommenddocid}&authorrecommendid=<?php echo $_GET['authorrecommendid']?>"  target="dialog"><span>设置</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="80" align="center">序号</th>
            <th width="120" align="center">文档名称</th>
            <th width="80" align="center">图片</th>
            <th align="center">简介</th>
            <th width="80" align="center">上传时间</th>
            <th width="80" align="center">格式</th>
            <th width="80" align="center">大小</th>
			<th width="80" align="center">下载</th>
        </tr>
        </thead>
        <tbody>
       
        <?php
		$offset++;
        foreach($author_recommend_docs as $author_recommend_doc){
                $str= '
				<tr target="authorrecommenddocid" rel="'.$offset.'" align="center">
				<td  width="60">'.$offset.'</td>
				<td>'.$author_recommend_doc->name.'</td>
					<td><img src="'.$author_recommend_doc->pictrueurl.'" height="200" width="150" /></td>
					<td>'.$author_recommend_doc->summary.'</td>';
                if($author_recommend_doc->timecreated==null){
                    $str=$str.'<td></td>';
                }
                else{
                    $str=$str.'
					<td>'.userdate($author_recommend_doc->timecreated,'%Y-%m-%d %H:%M').'</td>';
                }
                $str=$str.'
					<td>'.$author_recommend_doc->suffix.'</td>
					<td>'.$author_recommend_doc->size.'</td>
					
				';
				if($author_recommend_doc->url!=null){
					$str.='<td><a href="'.$author_recommend_doc->url.'">(右键另存为)</a></td></tr>';
				}else{
					$str.='<td></td></tr>';
				}
            echo $str;

			$offset++;
        }
        ?>

        <?php /**end zxf 查询分类全部基本信息**/?>
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
