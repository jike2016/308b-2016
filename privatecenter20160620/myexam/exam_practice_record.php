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

<script>
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myexam/exam_practice_record.php?page="+page
			<?php if(isset($_GET['categoryid']) && $_GET['categoryid']) echo '+"&categoryid='.$_GET['categoryid'].'"'; ?>);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myexam/exam_practice_record.php?page="+page
			<?php if(isset($_GET['categoryid']) && $_GET['categoryid']) echo '+"&categoryid='.$_GET['categoryid'].'"'; ?>);
	});
</script>

<?php
/** 在线练习 徐东威 2016-02-29 */
require_once("../../config.php");
$page = optional_param('page', 1, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
global $DB;
global $USER;

echo_quiz($categoryid,$page);//输出在线练习列表


/**START 输出在线练习列表 */
function echo_quiz($categoryid,$page){

	$offset=($page-1)*10;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	global $DB;
	global $USER;

	echo '<div class="classinfo-box">
			<table class="table table-hover">
				<thead>
					<tr>
						<td class="td1">序号</td>
						<td class="td2">时间</td>
						<td class="td3">课程</td>
						<td class="td4">试卷名称</td>
						<td class="td5"></td>
						<td class="td6"></td>
					</tr>
				</thead>
				<tbody>';

	//START 获取考试记录,分页查询10条记录
	if($categoryid==0){
		$quizs = $DB->get_records_sql('
		select
		d.id,c.fullname,d.name,d.timeopen,f.id as moduleid
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=16
		where a.userid= '.$USER->id.' and d.typeofquiz=3
		GROUP BY d.id
		limit '.$offset.',10
		');
		//重新统计总数
	}
	else{
		$quizs = $DB->get_records_sql("
		select
		d.id,c.fullname,d.name,d.timeopen,f.id as moduleid
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_course_link_categories g on g.mdl_course_id=c.id and g.mdl_course_categories_id= $categoryid
		join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=16
		where a.userid=$USER->id and d.typeofquiz=3
		GROUP BY d.id
		limit $offset,10
		");
	}
	//END 获取考试记录,分页查询10条记录

	//START 获取考试记录‘数量’
	if($categoryid==0){
		$quizscount = $DB->get_record_sql('
		select
		count(DISTINCT d.id) as record_count
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=16
		where a.userid= '.$USER->id.' and d.typeofquiz=3
		');
		//重新统计总数
	}
	else{
		$quizscount = $DB->get_record_sql("
		select
		count(DISTINCT d.id) as record_count
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		join mdl_quiz d on d.course=c.id
		join mdl_course_link_categories g on g.mdl_course_id=c.id and g.mdl_course_categories_id= $categoryid
		join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=16
		where a.userid=$USER->id and d.typeofquiz=3
		");
	}
	//END 获取考试记录‘数量’

	$n=($page-1)*10+1;//序号
	foreach($quizs as $quiz){
		//输出
		echo '<tr>
				<td>'.$n.'</td>
				<td>'.userdate($quiz->timeopen,'%Y-%m-%d %H:%M').'</td>
				<td>'.$quiz->fullname.'</td>
				<td>'.$quiz->name.'</td>
				<td></td>
				<td><a href="../mod/quiz/view.php?id='.$quiz->moduleid.'" target="_blank"><button class="btn btn-info checkexam">查看练习</button></a></td>
			</tr>';

		$n++;
	}

	echo '	</tbody>
				</table>
			</div>';

	echo_end($page,$quizscount);//输出上下页按钮

}
/**END 输出练习列表 */


/** START 输出上下页按钮等
 * @param  $page 页码
 * @param  $count 中的 $count->record_count 总的记录数
 */
function echo_end($page,$count){
	//页尾
	$total=$count->record_count;//总记录数
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
/**END 输出上下页按钮 */

?>


