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
if(!(isset($_POST['keyword'])&&$_POST['keyword'])&&!(isset($_POST['selectuploader'])&&$_POST['selectuploader'])){
    $sql='';
}
else{
    if(isset($_POST['keyword'])&&$_POST['keyword']){
        $sql['keyword']= 'a.name LIKE \'%'.$_POST['keyword'].'%\'';
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
    $sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_pic_my a '.$sql.'');
    $sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

$pictrues = $DB->get_records_sql('select
	a.id,a.name,a.picurl,a.timeuploaded,a.suffix,a.size,d.firstname as uploadername ,group_concat(c.name separator \'<br>\') as tags
	FROM mdl_pic_my a
	left join mdl_pic_tag_link_my b on b.picid=a.id
	left join mdl_pic_tag_my c on c.id=b.tagid
	left join mdl_user d on a.uploaderid = d.id
	'.$sql.'
	group by a.id
	ORDER BY timeuploaded desc
	limit '.$offset.','.$numPerPage.';');
//$errorelibrarys = $DB->get_records_sql('select * from mdl_pic_my');
$alluploaders=$DB->get_records_sql('select d.id,d.firstname as name from mdl_pic_my a left join mdl_user d on a.uploaderid = d.id group by uploaderid');
?>

<form id="pagerForm" method="post" action="">
    <input type="hidden" name="status" value="${param.status}">
    <input type="hidden" name="keyword" value="<?php if(isset($_POST['keyword']))echo $_POST['keyword'];?>" />
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
                        <label>图片描述：</label>
                        <input type="text" name="keyword" value="<?php  if(isset($_POST['keyword'])&&$_POST['keyword']) echo $_POST['keyword']?>"/>
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
            <li><a class="add" href="picroom/picture_add.php" target="navTab"><span>添加图片</span></a></li>
            <li><a class="delete" href="picroom/picture_post_handler.php?title=delete&pictureid={pictureid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="picroom/picture_edit.php?pictureid={pictureid}" target="navTab"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="50%" layoutH="138">
        <thead>
        <tr>
            <th width="40" align="center">序号</th>
			<th width="80" align="center">图片</th>
            <th width="120" align="center">图片描述</th>
			<th width="80" align="center">标签</th>
            <th width="80" align="center">上传时间</th>
            <th width="80" align="center">格式</th>
            <th width="80" align="center">大小</th>
            <th width="80" align="center">上传者</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $offset++;
        foreach($pictrues as $pictrue){
            echo '
				<tr target="pictureid" rel="'.$pictrue->id.'">
					<td>'.$offset.'</td>
					<td><img src="'.$pictrue->picurl.'" height="120" width="90" /></td>
					<td>'.$pictrue->name.'</td>
					<td>'.$pictrue->tags.'</td>
					<td>'.userdate($pictrue->timeuploaded,'%Y-%m-%d %H:%M').'</td>
					<td>'.$pictrue->suffix.'</td>
					<td>'.$pictrue->size.'</td>
					<td>'.$pictrue->uploadername.'</td>
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


