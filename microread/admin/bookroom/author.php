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
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_ebook_author_my a '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$authors=$DB->get_records_sql('select *from mdl_ebook_author_my a '.$sql);//作者数量少基本不会有第二页。。。所以先不做分页了
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
                 
                    <td>
                        作者名：<input type="text" name="keyword" />
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
            <li><a class="add" href="bookroom/author_add.php" target="dialog"><span>添加</span></a></li>
            <li><a class="delete" href="bookroom/author_post_handler.php?title=delete&authorid={authorid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="bookroom/author_edit.php?authorid={authorid}" target="dialog"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="30%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="80">序号</th>
            <th width="120">作者姓名</th>
			<th width="120">作者头像</th>
        </tr>
        </thead>
        <tbody>
       
        <?php
		$offset++;
        foreach($authors as $author){
            echo '
				<tr target="authorid" rel="'.$author->id.'" align="center">
				<td>'.$offset.'</td>
				<td>'.$author->name.'</td>
				<td><img src="'.$author->pictrueurl.'" height="150px" width="100px" /></td>
				</tr>
				';
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
