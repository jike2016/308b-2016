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
if(!(isset($_POST['keyword'])&&$_POST['keyword'])&&!(isset($_POST['selectcategory'])&&$_POST['selectcategory'])&&!(isset($_POST['selectauthor'])&&$_POST['selectauthor'])&&!(isset($_POST['selectuploader'])&&$_POST['selectuploader'])){
	$sql='';
}
else{
	if(isset($_POST['keyword'])&&$_POST['keyword']){
		$sql['keyword']= 'a.word_name LIKE \'%'.$_POST['keyword'].'%\'';
	}
	if(isset($_POST['selectcategory'])&&$_POST['selectcategory']){
		$sql['selectcategory']= 'a.categoryid='.$_POST['selectcategory'];
	}
	if(isset($_POST['selectuploader'])&&$_POST['selectuploader']){
	 	$sql['selectuploader']= 'a.create_userid='.$_POST['selectuploader'];
	}
	require_once('../dealselect.php');
	$sql=join_sql_select($sql);
}
//如果还没有查过总记录数则查询
if(isset($_POST['sumnum'])){
	$sumnum = $_POST['sumnum'];
}
else{
	$sumnum = $DB->get_record_sql('select count(*) as sumnum from mdl_dokuwiki_word_my a '.$sql);
	$sumnum = $sumnum->sumnum;
}
//查询当前页记录
$offset = ($pagenummy-1)*$numPerPage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
$words = $DB->get_records_sql('select
	a.id,a.word_name,b.`name` as category,c.firstname,a.create_time
	FROM mdl_dokuwiki_word_my a 
	join mdl_dokuwiki_categories_my b on a.categoryid=b.id
	left join mdl_user c on a.create_userid = c.id
	'.$sql.'
	ORDER BY a.create_time desc
	limit '.$offset.','.$numPerPage.';');
//查询没有分类或者没有作者的书
$errorwords = $DB->get_records_sql('select
	a.id,a.word_name,c.firstname,a.create_time
	FROM mdl_dokuwiki_word_my a 
	left join mdl_user c on a.create_userid = c.id
	where a.categoryid=0');
//查询所有的分类
$allcategories=$DB->get_records_sql('select * from mdl_dokuwiki_categories_my');
//查询所有的上传用户
$alluploaders=$DB->get_records_sql('select d.id,d.firstname as name from mdl_dokuwiki_word_my a left join mdl_user d on a.create_userid = d.id group by a.create_userid');
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
	<!--div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="bookroom/ebook_add.php" target="navTab"><span>添加电子书</span></a></li>
			<li><a class="delete" href="bookroom/ebook_post_handler.php?title=delete&ebookid={ebookid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="bookroom/ebook_edit.php?ebookid={ebookid}" target="navTab"><span>修改</span></a></li>
			<li><a class="edit" href="application/word_category.php?title=editcategory&wordid={wordid}" target="dialog"><span>设置分类</span></a></li>
		</ul>
	</div-->
	<table class="table" width="900px" layoutH="138">
		<thead>
			<tr>
				<th width="40" align="center">序号</th>
				<th width="200" align="center">词条</th>
				<th width="150" align="center">分类</th>
				<th width="100" align="center">作者</th>
				<th width="150" align="center">创建时间</th>
				<!--start zxf 章管理-->
				<!--<th width="80" align="center">操作</th>-->
				<!--end zxf 章管理-->
			</tr>
		</thead>
		<tbody>
		<?php
		/**START cx 循环输出当前页电子书*/
			foreach($errorwords as $word){
				echo '
				<tr target="wordid" rel="'.$word->id.'" >
					<td>-1</td>
					<td><a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/moodle/dokuwiki/doku.php?id='.$word->word_name.'">'.$word->word_name.'</a></td>
					<td>(无分类)<a style="display:block;float:right;margin-left:-22px" title="设置分类" target="dialog" href="application/word_category.php?title=editcategory&wordid='.$word->id.'" class="btnEdit">设置分类</a></td>
					<td>'.$word->firstname.'</td>
					<td>'.userdate($word->create_time,'%Y-%m-%d %H:%M').'</td>
					<!--<td><a title="编辑" target="navTab" href="" class="btnEdit">编辑</a></td>-->
				</tr>
				';
			}
			$offset++;
			foreach($words as $word){
				echo '
				<tr target="wordid" rel="'.$word->id.'" >
					<td>'.$offset.'</td>
					<td><a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/moodle/dokuwiki/doku.php?id='.$word->word_name.'">'.$word->word_name.'</a></td>
					<td>'.$word->category.'<a style="display:block;float:right;margin-left:-22px" title="设置分类" target="dialog" href="application/word_category.php?title=editcategory&wordid='.$word->id.'" class="btnEdit">设置分类</a></td>
					<td>'.$word->firstname.'</td>
					<td>'.userdate($word->create_time,'%Y-%m-%d %H:%M').'</td>
					<!--<td><a title="编辑" target="navTab" href="" class="btnEdit">编辑</a></td>-->
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
