<script>
$('.lockpage').hide();
</script>
<style>
	table td {text-align: center;}
	table {margin-top: 15px;}
	table thead {background-color: #f0f0f0; padding: 5px;}
	.table > thead > tr > td { padding: 4px;}
	.table > thead > tr > td, .table > tbody > tr > td { vertical-align: middle;}
	.checkexam {height: 24px; width: 100px; padding: 0px;}
	.td1 {width: 10%;}
	.td2 {width: 20%;}
	.td3 {width: 20%;}
	.td4 {width: 30%;}
	.td5 {width: 10%;}
	.td6 {width: 10%;}
</style>

<script type="text/javascript">

	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mynote/note_personal_record.php?page="+page);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mynote/note_personal_record.php?page="+page);
	});

	//删除
	function  delete_note(noteid){
		$('.lockpage').show();
//		alert(noteid);
		$.ajax({
			url: "../privatecenter/mynote/note_delete.php",
			data: { noteid: noteid},
			success: function(msg){
				if(msg == 1){
					// alert('删除成功');
					var page=parseInt($('#pageid').text());
					$('.delete_btn').parent().parent().parent().parent('.table-hover').parent('.classinfo-box').parent('.maininfo-box').load("mynote/note_personal_record.php?page="+page);
				}else{
					alert('删除失败');
					$('.lockpage').hide();
				}
			}
		});
	}

</script>


<?php
require_once("../../config.php");
$page = optional_param('page', 1, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
global $DB;
global $USER;
//输出笔记列表
echo_notes($categoryid,$page);


/**START  输出笔记列表*/
function echo_notes($categoryid,$page){

	$offset=($page-1)*10;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	echo '<div class="classinfo-box">
			<table class="table table-hover">
				<thead>
					<tr>
						<td class="td1">序号</td>
						<td class="td2">时间</td>
						<td class="td3">标题</td>
						<td class="td4"></td>
						<td class="td5"></td>
						<td class="td6"></td>
					</tr>
				</thead>
				<tbody>';

	global $DB;
	global $USER;

	$userID = $USER->id;
	$notes = $DB->get_records_sql("select * from mdl_note_my m where m.userid = $userID and m.notetype = 2 order by time desc limit $offset,10");//分页查询，获取课程笔记中的10条记录
	$notescount = $DB->get_record_sql("select count(*) as record_count  from mdl_note_my m where m.userid = $userID and m.notetype = 2 ");//分页查询，获取课程笔记中的10条记录
	$no = ($page-1)*10+1;//序号
	foreach($notes as $note){

		echo '<tr>
				<td>'.$no.'</td>
				<td>'.userdate($note->time,'%Y-%m-%d %H:%M').'</td>
				<td>'.$note->title.'</td>
				<td></td>
				
				<td>
					<a href="../mod/notemy/edit.php?id='.$note->id.'&action=details" target="_blank"><button class="btn btn-info checkexam">查看</button></a>
				</td>
				<td>
					<button class="btn btn-info checkexam delete_btn" onclick="delete_note('.$note->id.')">删除</button>
				</td>
			</tr>';

		$no++;
	}

	echo '		</tbody>
			</table>
		</div>';

	echo_end($page,$notescount);//输出上下页按钮等

}
/**END  输出笔记列表*/


/** START 输出上下页按钮等
 * @param  $page 页码
 * @param  $count 中的 $count->record_count 总的记录数
 */
function echo_end($page,$count){

	$total = $count->record_count;//总记录数
	$pagenum=ceil($total/10);//总页数
	echo '
	<div class="footer-box">
		<div class="footer">';
	if($page==1&&($pagenum==1||$pagenum==0)){
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	elseif($page==1){//第一页
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	elseif($page==$pagenum){//最后一页
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	}
	else{
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	if($pagenum==0){
		echo '
				<div class="center">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
			</div>
		</div>
		';
	}
	else{
		echo '
				<div class="center">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">'.$pagenum.'</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>第</p>
					<p id="pageid" class="p-14-red">'.$page.'</p>
					<p>页</p>
				</div>
			</div>
		</div>
		';
	}
}


?>


