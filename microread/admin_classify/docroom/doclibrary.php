<?php

$numPerPage=10;//每页显示行数
if(isset($_POST['pageNum'])){
    $pagenummy = $_POST['pageNum'];//获取当前页数
}
else{
    $pagenummy=1;
}
require_once('../../../config.php');
global $DB;

//是否有查询条件
if(!(isset($_POST['keyword'])&&$_POST['keyword'])&&!(isset($_POST['selectcategory'])&&$_POST['selectcategory'])&&!(isset($_POST['selectuploader'])&&$_POST['selectuploader'])){
	  $sql='';
}
else{
	 if(isset($_POST['keyword'])&&$_POST['keyword']){
		 $sql['keyword']= 'a.name LIKE \'%'.$_POST['keyword'].'%\'';
	 }
	 if(isset($_POST['selectcategory'])&&$_POST['selectcategory']){
		 $sql['selectcategory']= 'a.categoryid='.$_POST['selectcategory'];
	 }
	 if(isset($_POST['selectuploader'])&&$_POST['selectuploader']){
		 $sql['selectuploader']= 'a.uploaderid='.$_POST['selectuploader'];
	 }
	 require_once('../dealselect.php');
	 $sql=join_sql_select($sql);
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
	a.id,a.name,a.summary,a.url,a.pictrueurl,a.timecreated,a.suffix,a.size,b.name as categoryname,d.firstname as uploadername
	FROM mdl_doc_my a
	left join mdl_doc_categories_my b on a.categoryid=b.id
	left join mdl_user d on a.uploaderid = d.id
	'.$sql.'
	ORDER BY timecreated desc
	limit '.$offset.','.$numPerPage.';');
//查询没有分类或者没有作者的书
$errorelibrarys = $DB->get_records_sql('select a.id,a.name,a.pictrueurl,a.url,a.categoryid,a.summary,a.timecreated,a.suffix,a.size,b.firstname as uploadername from mdl_doc_my a left JOIN mdl_user b on a.uploaderid=b.id where a.categoryid=-1');
//查询所有的分类
$allcategories=$DB->get_records_sql('select *from mdl_doc_categories_my');
//查询所有的上传者
$alluploaders=$DB->get_records_sql('select d.id,d.firstname as name from mdl_doc_my a left join mdl_user d on a.uploaderid = d.id group by uploaderid');
?>

<form id="pagerForm" method="post" action="">
    <input type="hidden" name="status" value="${param.status}">
    <input type="hidden" name="keyword" value="<?php if(isset($_POST['keyword']))echo $_POST['keyword'];?>" />
    <input type="hidden" name="selectcategory" value="<?php if(isset($_POST['selectcategory']))echo $_POST['selectcategory'];?>" />
    <input type="hidden" name="selectuploader" value="<?php if(isset($_POST['selectuploader']))echo $_POST['selectuploader'];?>" />
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
                        <div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div>
                        <label>文档名称：</label>
                        <input type="text" name="keyword" value="<?php  if(isset($_POST['keyword'])&&$_POST['keyword']) echo $_POST['keyword']?>"/>
                        <select name="selectcategory">
                            <option value="">所有分类</option>
                            <?php //以下拉框的形式显示所有分类
                            if(isset($_POST['selectcategory'])&&$_POST['selectcategory']){
                                foreach($allcategories as $category){
                                    if($_POST['selectcategory']==$category->id)
                                    echo	'<option value="'.$category->id.'" selected="selected">'.$category->name.'</option>';
                                    else
                                        echo	'<option value="'.$category->id.'">'.$category->name.'</option>';
                                }
                            }
                            else{
                                foreach($allcategories as $category){
                                    echo	'<option value="'.$category->id.'">'.$category->name.'</option>';
                                }
                            }
                            ?>
                        </select>
                        <select name="selectuploader">
                            <option value="">所有上传者</option>
                            <?php //以下拉框的形式显示所有上传者
                            if(isset($_POST['selectuploader'])&&$_POST['selectuploader']){
                                foreach($alluploaders as $alluploader){
                                    if($_POST['selectuploader']==$alluploader->id)
                                        echo	'<option value="'.$alluploader->id.'" selected="selected">'.$alluploader->name.'</option>';
                                    else
                                        echo	'<option value="'.$alluploader->id.'">'.$alluploader->name.'</option>';
                                }
                            }
                            else{
                                foreach($alluploaders as $alluploader){
                                    echo	'<option value="'.$alluploader->id.'">'.$alluploader->name.'</option>';
                                }
                            }
                            ?>
                        </select>
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
            <li><a class="add" href="docroom/doclibrary_add.php" target="navTab"><span>添加文档</span></a></li>
            <li><a class="delete" href="docroom/doclibrary_post_handler.php?title=delete&doclibraryid={doclibraryid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="docroom/doclibrary_edit.php?doclibraryid={doclibraryid}" target="navTab"><span>修改</span></a></li>
			<li><a class="delete" href="docroom/doclibrary_post_handler.php?title=restart_service" target="ajaxTodo" title="确定要重启吗?"><span>重启文档转换服务</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="138">
        <thead>
        <tr>
            <th width="40" align="center">序号</th>
            <th width="120" align="center">文档名称</th>
            <th width="80" align="center">图片</th>
            <th width="150" align="center">分类</th>
            <th align="center">简介</th>
            <th width="120" align="center">上传时间</th>
            <th width="80" align="center">格式</th>
            <th width="80" align="center">大小</th>
            <th width="80" align="center">上传者</th>
			<th width="80" align="center">下载</th>
        </tr>
        </thead>
        <tbody>
        <?php
        /**START cx 循环输出当前页文档*/
        foreach($errorelibrarys as $errorelibrary){
            echo '
				<tr target="doclibraryid" rel="'.$errorelibrary->id.'" >
					<td>-1</td>
					<td>'.$errorelibrary->name.'</td>
					<td><img src="'.$errorelibrary->pictrueurl.'" height="80" width="60" /></td>';
            if($errorelibrary->categoryid==-1)
                echo "<td>(无分类)</td>";
            else
                echo '<td>'.$errorelibrary->categoryid.'</td>';
            echo '
					<td>'.$errorelibrary->summary.'</td>
					<td>'.userdate($errorelibrary->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$errorelibrary->suffix.'</td>
					<td>'.$errorelibrary->size.'</td>
					<td>'.$errorelibrary->uploadername.'</td>
					<td><a href="'.$errorelibrary->url.'">(右键另存为)</a></td>
				</tr>
				';
        }
        $offset++;
        foreach($doclibrarys as $doclibrary){
            echo '
				<tr target="doclibraryid" rel="'.$doclibrary->id.'">
					<td>'.$offset.'</td>
					<td>'.$doclibrary->name.'</td>
					<td><img src="'.$doclibrary->pictrueurl.'" height="80" width="60" /></td>
					<td>'.$doclibrary->categoryname.'</td>
					<td>'.$doclibrary->summary.'</td>
					<td>'.userdate($doclibrary->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$doclibrary->suffix.'</td>
					<td>'.$doclibrary->size.'</td>
					<td>'.$doclibrary->uploadername.'</td>
					<td><a href="'.$doclibrary->url.'">(右键另存为)</a></td>
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


