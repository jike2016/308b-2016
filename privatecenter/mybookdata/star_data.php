<?php
/**
 * 个人中心》台账数据》课程统计》星评查看 页面
 */
require_once("../../config.php");
$page = optional_param('page', 1, PARAM_INT);
$start_time = optional_param('start_time', 0, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 0, PARAM_TEXT);//结束时间
// $categoryid = optional_param('categoryid', 0, PARAM_INT);
global $DB;
global $USER;
?>

<link rel="stylesheet" href="css/personal-maininfo-head-box.css" />
<style>
	.table {border-bottom:1px solid #DDDDDD ; }
	.table .td3_text { color:#000000}
	.pagination-box {width: 100%;  text-align: center;}
	.pagination-box nav {margin: auto;}
	.td1 {width: 10%;}
	.td2 {width: 15%;}
	.td3 {width: 20%;}
	.td4 {width: 30%;}
	.td5 {width: 25%;}
	.table tr td{text-align: center;}
	.table tr .td3_text  p{margin: auto;}
	.table tr .td3_text p{ width: 100%;overflow: hidden; /*自动隐藏文字*/text-overflow: ellipsis;/*文字隐藏后添加省略号*/white-space: nowrap;/*强制不换行*/width: 13em;/*不允许出现半汉字截断*/}
	#time_plug{display: none;}/*隐藏时间控件*/
</style>
<link rel="stylesheet" href="css/personal-footer.css" />

<script>
	$('.lockpage').hide();
	var time_param = '';
	if(time_flag){
		time_param = 'start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>';
	}
	$("#return-index").click(function(){
		$('.lockpage').show();
//		$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index.php');
//		$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.maininfo-box-index').parent('.myclass').parent('.right-banner').load('mybookdata/index.php');
		$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/course_index.php?'+time_param);
	})
	
	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
//		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/star_data.php?page="+page);
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/star_data.php?page="+page+'&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		// alert(page);
//		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/star_data.php?page="+page);
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/star_data.php?page="+page+'&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>');
	});
</script>

<?php
echo_stars($page,$start_time,$end_time);//输出笔记列表

/**STATR  输出笔记列表*/
function echo_stars($page,$start_time,$end_time){
	$numofpage=15;
	$offset=($page-1)*$numofpage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
	
	global $DB;
	global $USER;

	$userID = $USER->id;
	// $notes = $DB->get_records_sql("select * from mdl_note_my m where m.userid = $userID order by time desc limit $offset,$numofpage");//分页查询，获取课程笔记中的10条记录
	$starts = $DB->get_records_sql("select a.id,a.score,a.`comment`,a.scoretime,b.fullname  from mdl_score_course_my a join mdl_course b on b.id=a.courseid where userid=$userID and scoretime between $start_time and $end_time order by scoretime desc limit $offset,$numofpage");
	$startscount = $DB->get_record_sql("select count(*) as record_count from mdl_score_course_my where userid=$userID and scoretime between $start_time and $end_time ");
	$no = ($page-1)*$numofpage+1;//序号
	echo'
		<div class="maininfo-box">
		<div class="head-box">
		<div class="a-box"><a id="return-index"><span class="glyphicon glyphicon-menu-left"></span>返回</a></div>
		<h3>星评台账&nbsp;:&nbsp;&nbsp;</h3>
		<h3 id="num">'.$startscount->record_count.'</h3>
	</div>

	
	<div class="table-box">
		<table class="table">
			<thead>
				<tr class="active">
					<td class="td1">序号</td>
					<td class="td2">时间</td>
					<td class="td3">分数</td>
					<td class="td4">内容</td>
					<td class="td5">课程</td>
				</tr>
			</thead>
			<tbody>
	';
	foreach($starts as $start){
		echo '<tr>
				<td>'.$no.'</td>
				<td>'.userdate($start->scoretime,'%Y-%m-%d %H:%M').'</td>
				<td>'.$start->score.' 分</td>
				<td class="td3_text"><p>'.mb_substr(strip_tags($start->comment),0,30,'utf-8').'</p></td>
				<td>'.$start->fullname.'</td>
			</tr>';
		$no++;
	}

	echo '		</tbody>
			</table>
		</div>
		</div>';

	 echo_end($page,$startscount,$numofpage);//输出上下页按钮

}
/**END  输出笔记列表*/
function echo_end($page,$count,$numofpage){

	$total = $count->record_count;//总记录数
	$pagenum=ceil($total/$numofpage);//总页数
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
					<p class="p-14-red">'.$numofpage.'</p>
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
					<p class="p-14-red">'.$numofpage.'</p>
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

