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
	.td2 {width: 30%;}
	.td3 {width: 50%;}
	.td4 {width: 10%;}
</style>

<script type="text/javascript">

	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page = parseInt($('#pageid').text());//获取当前页码
		page--;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mycollection/collection_class_record.php?page="+page);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page = parseInt($('#pageid').text());
		page++;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("mycollection/collection_class_record.php?page="+page);
	});

	function collectiondelete(myid) {
		$('.lockpage').show();
		$.ajax({
			url: "../privatecenter/mycollection/collectiondelete.php",
			data: { myid: myid},
			success: function(msg){
			if(msg=='1'){
//				location.reload();
				var page = parseInt($('#pageid').text());
				$('.checkexam').parent().parent().parent().parent('.table-hover').parent('.classinfo-box').parent('.maininfo-box').load("mycollection/collection_class_record.php?page="+page);
			}else{
				alert('删除失败');
				$('.lockpage').hide();
			}
		}
	});
}
</script>

<?php
/**  START 朱子武 20160226 从数据库获取我的收藏*/
require_once("../../config.php");

$page = optional_param('page', 1, PARAM_INT);

/** START 朱子武 获取用户收藏页面 20160227*/
function my_get_collection($current_page)
{
	$my_page = $current_page * 10;
	global $DB;
	global $USER;
	$my_collection = $DB->get_records_sql('SELECT id, userid, url, title, collectiontime FROM mdl_collection_my WHERE userid = ? ORDER BY collectiontime DESC LIMIT '.$my_page.',10', array($USER->id));
//	$num = 1;
	$num = $current_page * 10 +1;//序号
	foreach($my_collection as $value)
	{
		echo '<tr>
				<td>'.$num.'</td>
				<td>'.userdate($value->collectiontime,'%Y-%m-%d %H:%M').'</td>
				<td><a href="'.$value->url.'" target = "_blank">'.$value->title.'</a></td>
				<td>
					<button class="btn btn-info checkexam" onclick="collectiondelete(\''.$value->id.'\')">删除</button>
				</td>
			</tr>';
		$num ++;
	}
}

function collection_page($page)
{
	global $DB;
	global $USER;
//	$collectioncount = $DB->get_records_sql("SELECT id FROM mdl_collection_my WHERE userid = ?", array($USER->id));
//	$total = count($collectioncount);
	$collectioncount = $DB->get_record_sql("SELECT count(id) as record_count FROM mdl_collection_my WHERE userid = ?", array($USER->id));
	$total = $collectioncount->record_count;//总记录数

	$pagenum = ceil($total / 10);//总页数
	echo '
	<div class="footer-box">
		<div class="footer">';
	if ($page == 1 && ($pagenum == 1 || $pagenum == 0)) {
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	} elseif ($page == 1) {//第一页
		echo '
		<a class="" style="color:#777; text-decoration:none">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	} elseif ($page == $pagenum) {//最后一页
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="" style="color:#777; text-decoration:none">下一页</a>';
	} else {
		echo '
		<a class="pre-btn" href="#">上一页</a>
		<a class="next-btn" href="#">下一页</a>';
	}
	if ($pagenum == 0) {
		echo '
				<div class="center">
					<p>第</p>
					<p id="pageid" class="p-14-red">' . $pagenum . '</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">' . $pagenum . '</p>
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
	} else {
		echo '
				<div class="center">
					<p>每页显示</p>
					<p class="p-14-red">10</p>
					<p>条</p>
				</div>
				<div class="right">
					<p>共</p>
					<p class="p-14-red">' . $pagenum . '</p>
					<p>页</p>
				</div>
				<div class="right">
					<p>第</p>
					<p id="pageid" class="p-14-red">' . $page . '</p>
					<p>页</p>
				</div>
			</div>
		</div>
		';
	}
}

/**---my_get_collection  END---*/
echo '
<div class="classinfo-box">
	<table class="table table-hover">
		<thead>
			<tr>
				<td class="td1">序号</td>
				<td class="td2">时间</td>
				<td class="td3">标题</td>
				<td class="td4">操作</td>
			</tr>
		</thead>
		<tbody>';
my_get_collection($page - 1);
echo'
		</tbody>
	</table>
</div>';
collection_page($page);
?>

<!--<div class="footer-box">-->
<!--	<div class="footer">-->
<!--		<a href="#">上一页</a>-->
<!--		<a href="#">下一页</a>-->
<!--		<div class="center">-->
<!--			<p>第</p>-->
<!--			<p class="p-14-red">1</p>-->
<!--			<p>页</p>-->
<!--		</div>-->
<!--		<div class="right">-->
<!--			<p>共</p>-->
<!--			<p class="p-14-red">10</p>-->
<!--			<p>页</p>-->
<!--		</div>-->
<!--		<div class="right">-->
<!--			<p>每页显示</p>-->
<!--			<p class="p-14-red">10</p>-->
<!--			<p>条</p>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->
