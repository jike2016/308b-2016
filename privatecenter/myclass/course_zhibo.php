<script>
$('.lockpage').hide();
</script>
<style>
	.classinfo .slearn { float: right; margin: 10px 0px 0px 15px; width: 120px; height: 50px;}

	.kinds {margin-bottom: 5px;}

	.classinfo-box {min-height: 560px; padding: 0px;}
	.footer-box {background-color: #F0F0F0;  border: 1px solid #ccc; border-top: 0px;}
	.footer { background-color: #F0F0F0; width: 60%; margin: auto; height: 48px; padding: 15px 0px;}
	.footer p {display: inline-block; color: #5E5E5E;}
	.footer .p-14-red {color: #C9302C;}
	.footer .right,.center {float: right;}
	.footer .right{margin-right: 60px;}
	.footer a {margin-right: 40px;}

	table td {text-align: center;}
	table {margin-top: 0px;}
	table thead {background-color: #F0F0F0; padding: 5px;}
	table thead tr {height: 50px; color: #777777;}
	.table > thead > tr > td { padding: 4px;}
	.table > thead > tr > td, .table > tbody > tr > td { vertical-align: middle;}
	.td1 {width: 5%;}
	.td2 {width: 20%;}
	.td3 {width: 20%;}
	.td4 {width: 15%;}
	.td5 {width: 25%;}
	.td6 {width: 15%;}
</style>

<script type="text/javascript">

	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		page--;
		alert(page);
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myclass/course_zhibo.php?page="+page);
		<?php if(isset($_GET['categoryid']) && $_GET['categoryid']) echo '+"&categoryid='.$_GET['categoryid'].'"'; ?>
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		alert(page);
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myclass/course_zhibo.php?page="+page);
		<?php if(isset($_GET['categoryid']) && $_GET['categoryid']) echo '+"&categoryid='.$_GET['categoryid'].'"'; ?>
	});

</script>



<?php
require_once("../../config.php");
// require_once($CFG->dirroot. '/course/lib.php');
$page = optional_param('page', 1, PARAM_INT);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
require_once($CFG->libdir. '/coursecatlib.php');

global $DB;
global $USER;

echo_class($page,$categoryid);//输出直播列表

/** START  输出直播列表*/
function echo_class($page,$categoryid){

	$offset = ($page-1)*10;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	global $DB;
	global $USER;

	echo '
	<div class="classinfo-box">
		<table class="table table-hover">
			<thead>
				<tr>
					<td class="td1">序号</td>
					<td class="td2">直播名称</td>
					<td class="td3">开始时间</td>
					<td class="td4">讲课人</td>
					<td class="td5">所属课程</td>
					<td class="td6"></td>
				</tr>
			</thead>
			<tbody>';

	//START 搜索直播数据,分页查询10条记录
	if($categoryid==0){//无分类
		$courses = $DB->get_records_sql("
					select
					d.id,d.name,d.timeopen,d.teacher,d.teachername,c.fullname,f.id as moduleid
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					join mdl_openmeetings d on d.course=c.id
					join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=23
					where a.userid= $USER->id
					GROUP BY d.id
					limit $offset,10
		");
	}
	else{ //有分类
		$courses = $DB->get_records_sql("
					select
					d.id,d.name,d.timeopen,d.teacher,d.teachername,c.fullname,f.id as moduleid
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					join mdl_openmeetings d on d.course=c.id
					join mdl_course_link_categories g on g.mdl_course_id=c.id and g.mdl_course_categories_id= $categoryid
					join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=23
					where a.userid= $USER->id
					GROUP BY d.id
					limit $offset,10
		");
	}
	//END 搜索直播数据 分页

	//START 直播数量
	if($categoryid==0){//无分类
		$coursescount = $DB->get_record_sql("
					select
					count(DISTINCT d.id) as record_count
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					join mdl_openmeetings d on d.course=c.id
					join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=23
					where a.userid= $USER->id
		");
	}
	else{ //有分类
		$coursescount = $DB->get_record_sql("
					select
					count(DISTINCT d.id) as record_count
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					join mdl_openmeetings d on d.course=c.id
					join mdl_course_link_categories g on g.mdl_course_id=c.id and g.mdl_course_categories_id= $categoryid
					join mdl_course_modules f on f.course=c.id and f.instance=d.id and f.module=23
					where a.userid= $USER->id
		");
	}
	//END 直播数量


	//START 输出直播
	$no = ($page-1)*10+1;//序号
	foreach($courses as $course){

		/** START 岑霄 更改老师名字*/
		// $teacher = $DB->get_record_sql("select u.id,u.lastname,u.firstname from mdl_user u where u.id = $course->teacher");//获取直播教师
		// $teachername = $teacher->lastname . $teacher->firstname;
		$teachername =  $course->teachername;
		/** END */
		echo '<tr>
				<td>'.$no.'</td>
				<td>'.$course->name.'</td>
				<td>'.userdate($course->timeopen,'%Y-%m-%d %H:%M').'</td>
				<td>'.$teachername.'</td>
				<td>'.$course->fullname.'</td>
				<td><a href="../mod/openmeetings/view.php?id='.$course->moduleid.'" target="_blank"><button class="btn btn-info checkexam">查看直播</button></a></td>
			</tr>';
		$no++;

	}
	//END 输出课程

	echo '</tbody>
		</table>
	</div>';

	echo_end($page,$coursescount);//输出上下页按钮
}
/** END  输出直播列表*/


/** START 输出上下页按钮等
 * @param  $page 页码
 * @param  $count 中的 $count->record_count 总的记录数
 */
function echo_end($page,$count){

	$total = $count->record_count;
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




//==============================================
/*原代码
 <div class="classinfo">
	<div class="imgbox">
		<img src="img/1.jpg" />
	</div>
	<div class="img-info">
		<a href="#">
			<p class="img-info-title">Abby学摄影</p>
		</a>
		<p class="img-info-article">卡图摄像教育中心</p>
		<p class="img-info-article"><span class="glyphicon glyphicon-user"></span>&nbsp;66666</p>
	</div>
	<button class="btn btn-success">开始学习</button>
	<div class="img-info-tech">
		<p class="img-info-tech-title">观看进度</p>
		<p>0%</p>
	</div>
	<!--div class="img-info-tech">
		<p class="img-info-tech-title">课时/学分</p>
		<p>12/24</p>
	</div-->
	<div class="img-info-tech">
		<p class="img-info-tech-title">分类</p>
		<p>实用技能</p>
	</div>
	<div class="img-info-tech">
		<p class="img-info-tech-title">主讲</p>
		<p>钟振星</p>
	</div>
</div> */


?>

