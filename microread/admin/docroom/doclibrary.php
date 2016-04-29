<?php

$numPerPage=20;//每页显示行数
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
    $sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_doc_my a '.$sql.'');
    $sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$doclibrarys = $DB->get_records_sql('select
	a.id,a.name,a.authorid,a.summary,a.url,a.pictrueurl,a.timecreated,a.suffix,a.size,b.name as categoryname,c.name as authorname,d.firstname as uploadername
	FROM mdl_doc_my a
	join mdl_doc_categories_my b on a.categoryid=b.id
	join mdl_doc_author_my c on a.authorid = c.id
	join mdl_user d on a.uploaderid = d.id
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

<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="" method="post">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td>
                        文档名称：<input type="text" name="keyword" />
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
            <li><a class="add" href="docroom/doclibrary_add.php" target="navTab"><span>添加文档资料</span></a></li>
            <li><a class="delete" href="docroom/doclibrary_post_handler.php?title=delete&ebookid={ebookid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="docroom/doclibrary_edit.php?ebookid={ebookid}" target="navTab"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="138">
        <thead>
        <tr>
            <th width="80" align="center">序号</th>
            <th width="120" align="center">文档名称</th>
            <th width="80" align="center">图片</th>
            <th width="150" align="center">分类</th>
            <th width="100" align="center">作者</th>
            <th align="center">简介</th>
            <th width="80" align="center">上传时间</th>
            <th width="80" align="center">格式</th>
            <th width="80" align="center">大小</th>
            <th width="80" align="center">上传者</th>
        </tr>
        </thead>
        <tbody>
        <?php
        /**START cx 循环输出当前页电子书*/
        $offset++;
        foreach($doclibrarys as $doclibrary){
            echo '
				<tr target="ebookid" rel="'.$doclibrary->id.'">
					<td>'.$offset.'</td>
					<td>'.$doclibrary->name.'</td>
					<td>'.$doclibrary->pictrueurl.'</td>
					<td>'.$doclibrary->categoryname.'</td>
					<td>'.$doclibrary->authorname.'</td>
					<td>'.$doclibrary->summary.'</td>
					<td>'.userdate($doclibrary->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$doclibrary->suffix.'</td>
					<td>'.$doclibrary->size.'</td>
					<td>'.$doclibrary->uploadername.'</td>
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


