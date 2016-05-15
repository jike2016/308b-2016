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
if(!(isset($_POST['keyword'])&&$_POST['keyword'])&&!(isset($_POST['selectcategory'])&&$_POST['selectcategory'])&&!(isset($_POST['selectauthor'])&&$_POST['selectauthor'])&&!(isset($_POST['selectuploader'])&&$_POST['selectuploader'])){
	$sql='';
}
else{
	if(isset($_POST['keyword'])&&$_POST['keyword']){
		$sql['keyword']= 'a.name LIKE \'%'.$_POST['keyword'].'%\'';
	}
	if(isset($_POST['selectcategory'])&&$_POST['selectcategory']){
		$sql['selectcategory']= 'a.categoryid='.$_POST['selectcategory'];
	}
	if(isset($_POST['selectauthor'])&&$_POST['selectauthor']){
		$sql['selectauthor']= 'a.authorid='.$_POST['selectauthor'];
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
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_ebook_my a '.$sql.'');
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$ebooks = $DB->get_records_sql('select 
	a.id,a.name,a.authorid,a.summary,a.url,a.pictrueurl,a.timecreated,a.wordcount,a.suffix,a.size,b.name as categoryname, c.`name` as authorname, d.firstname 
	FROM mdl_ebook_my a 
	join mdl_ebook_categories_my b on a.categoryid=b.id 
	join mdl_ebook_author_my c on a.authorid=c.id
	join mdl_user d on a.uploaderid=d.id
	'.$sql.'
	ORDER BY timecreated desc
	limit '.$offset.','.$numPerPage.';');
//查询没有分类或者没有作者的书
$errorebooks = $DB->get_records_sql('select a.id,a.name,a.authorid,a.summary,a.url,a.pictrueurl,a.timecreated,a.wordcount,a.suffix,a.size, b.firstname
	from mdl_ebook_my a join mdl_user b on a.uploaderid=b.id where a.authorid=-1 or a.categoryid=-1');
//查询所有的分类
$allcategories=$DB->get_records_sql('select *from mdl_ebook_categories_my');
//查询所有的作者
$allauthors=$DB->get_records_sql('select *from mdl_ebook_author_my');
//查询所有的上传者
$alluploaders=$DB->get_records_sql('select d.id,d.firstname as name from mdl_ebook_my a left join mdl_user d on a.uploaderid = d.id group by uploaderid');
?>

<form id="pagerForm" method="post" action="">
	<input type="hidden" name="status" value="${param.status}">
	<input type="hidden" name="keyword" value="<?php if(isset($_POST['keyword']))echo $_POST['keyword'];?>" />
	<input type="hidden" name="selectcategory" value="<?php if(isset($_POST['selectcategory']))echo $_POST['selectcategory'];?>" />
	<input type="hidden" name="selectauthor" value="<?php if(isset($_POST['selectauthor']))echo $_POST['selectauthor'];?>" />
	<input type="hidden" name="selectuploader" value="<?php if(isset($_POST['selectuploader']))echo $_POST['selectuploader'];?>" />
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
					<div class="buttonActive"><div class="buttonContent"><button type="submit">查询</button></div></div>
					<label>电子书名：</label>
					<input type="text" name="keyword" value="<?php  if(isset($_POST['keyword'])&&$_POST['keyword']) echo $_POST['keyword']?>" />
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
					<select name="selectauthor">
						<option value="">所有作者</option>
						<?php //以下拉框的形式显示所有分类
						if(isset($_POST['selectauthor'])&&$_POST['selectauthor']){
							foreach($allauthors as $author){
								if($_POST['selectauthor']==$author->id)
									echo	'<option value="'.$author->id.'" selected="selected">'.$author->name.'</option>';
								else
									echo	'<option value="'.$author->id.'">'.$author->name.'</option>';
							}
						}
						else{
							foreach($allauthors as $author){
								echo	'<option value="'.$author->id.'">'.$author->name.'</option>';
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
			<li><a class="add" href="bookroom/ebook_add.php" target="navTab"><span>添加电子书</span></a></li>
			<li><a class="delete" href="bookroom/ebook_post_handler.php?title=delete&ebookid={ebookid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/ebook_edit.php?ebookid={ebookid}" target="navTab"><span>修改</span></a></li>

		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="80" align="center">电子书名</th>
				<th width="90" align="center">图片</th>
				<th width="80" align="center">分类</th>
				<th width="80" align="center">作者</th>
				<th align="center">简介</th>
				<th width="120" align="center">上传时间</th>
				<th width="80" align="center">格式</th>
				<th width="80" align="center">总字数</th>
				<th width="80" align="center">大小</th>
				<th width="80" align="center">上传者</th>
				<th width="80" align="center">下载</th>
				<!--start zxf 章管理-->
				<th width="80" align="center">章管理</th>
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			foreach($errorebooks as $ebook){
				echo '
				<tr target="ebookid" rel="'.$ebook->id.'" >
					<td>-1</td>
					<td>'.$ebook->name.'</td>
					<td><img src="'.$ebook->pictrueurl.'" height="80" width="60" /></td>';
				if($ebook->categoryid==0)
					echo "<td>(无分类)</td>";
				else
					echo '<td>'.$ebook->categoryid.'</td>';
				if($ebook->authorid==0)
					echo "<td>(无作者)</td>";
				else
					echo '<td>'.$ebook->authorid.'</td>';
				echo '
					<td>'.$ebook->summary.'</td>
					<td>'.userdate($ebook->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$ebook->suffix.'</td>
					<td>'.$ebook->wordcount.'</td>
					<td>'.$ebook->size.'</td>
					<td>'.$ebook->firstname.'</td>
					<td><a href="'.$ebook->url.'">(右键另存为)</a></td>
					<td><a class="button" href="bookroom/chapter.php?ebookid='.$ebook->id.'" target="navTab" rel="ebookchapter"><span>章节管理</span></a></td>
				</tr>
				';
			}
			$offset++;
			foreach($ebooks as $ebook){
				echo '
				<tr target="ebookid" rel="'.$ebook->id.'" >
					<td>'.$offset.'</td>
					<td>'.$ebook->name.'</td>
					<td><img src="'.$ebook->pictrueurl.'" height="80" width="60" /></td>
					<td>'.$ebook->categoryname.'</td>
					<td>'.$ebook->authorname.'</td>
					<td>'.$ebook->summary.'</td>
					<td>'.userdate($ebook->timecreated,'%Y-%m-%d %H:%M').'</td>
					<td>'.$ebook->suffix.'</td>
					<td>'.$ebook->wordcount.'</td>
					<td>'.$ebook->size.'</td>
					<td>'.$ebook->firstname.'</td>
					<td><a href="'.$ebook->url.'">(右键另存为)</a></td>
					<td><a class="button" href="bookroom/chapter.php?ebookid='.$ebook->id.'" target="navTab" rel="ebookchapter"><span>章节管理</span></a></td>
					
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
