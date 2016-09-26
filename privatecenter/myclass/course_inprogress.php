<script>
$('.lockpage').hide();
</script>
<style>
	.classinfo-box {height:550px; overflow-y: scroll;}
	.classinfo .slearn { float: right; margin: 10px 0px 0px 15px; width: 100px; height:40px;}
</style>
<link rel="stylesheet" href="css/personal-footer.css" />

<script type="text/javascript">

	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		page--;
		// alert(page);
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myclass/course_inprogress.php?page="+page);
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		// alert(page);
		$(this).parent('.footer').parent('.footer-box').parent('.maininfo-box').load("myclass/course_inprogress.php?page="+page);
	});

</script>


<?php
require_once("../../config.php");
// require_once($CFG->dirroot. '/course/lib.php');
$page = optional_param('page', 1, PARAM_INT);
require_once($CFG->libdir. '/coursecatlib.php');

global $DB;
global $USER;

echo_class($page,$CFG);//输出课程列表

/** START  输出课程列表*/
function echo_class($page,$CFG){

	$offset = ($page-1)*2;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。

	global $DB;
	global $USER;

	//搜索并减去已完成的课程，分页查询10条记录
	$courses = $DB->get_records_sql('
					select
					c.id,c.category,c.fullname,c.summary
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					where a.userid='.$USER->id.' and c.id not in(
						select
						course
						from
						mdl_course_completion_crit_compl
						where userid='.$USER->id.'
					)
					GROUP BY courseid ORDER BY a.timecreated DESC
					limit '.$offset.',10
	');

	//获取课程的‘数量’
	$coursescount = $DB->get_record_sql('
					select
					count(DISTINCT courseid) as record_count
					from mdl_user_enrolments a
					join mdl_enrol b on b.id=a.enrolid
					join mdl_course c on c.id=b.courseid
					where a.userid='.$USER->id.' and c.id not in(
						select
						course
						from
						mdl_course_completion_crit_compl
						where userid='.$USER->id.'
					)
	');

	//统计选课人数
	coursecat::my_calculate_num_of_course($courses);

echo '<div class="classinfo-box">';
	//START 输出课程
	//$no = ($page-1)*10+1;//序号
	foreach($courses as $course){
		/**Start 课程完成率 徐东威 20160310*/
		$completeRate = '无';//课程完成率
		//获取课程的进度跟踪启停状态 enablecompletion = 1 为开启状态
		$openState = $DB->get_record_sql("select c.enablecompletion from mdl_course c where c.id = $course->id ");
		//如果课程开启了活动
		if($openState->enablecompletion == 1 ){
			//开启了进度的活动数，其中除去那些不设为进度跟踪的活动（处理方式：如果该课程有活动，但没有一个是设置为进度跟踪的，那么就让其显示为‘无统计’）
			$activeCount = $DB->get_record_sql("select count(*) as count from mdl_course_modules cm  where cm.course = $course->id and cm.`completion` in (1,2) ");
			//如果设置有开启进度的活动，则求完成率
			if($activeCount->count != 0){
				//完成的活动数
				$completeCount = $DB->get_record_sql("select  count(*) as count from mdl_course_modules_completion cmc
												where cmc.userid = $USER->id
												and cmc.coursemoduleid in (select cm.id from mdl_course_modules cm  where cm.course = $course->id and cm.`completion` in (1,2) )
												and cmc.completionstate = 1");
				$completeRate = round($completeCount->count / $activeCount->count, 2) * 100;//求完成率
				$completeRate .= '%';
			}
		}
		/** End */
		
		echo '
				<div class="classinfo">
			
			<div class="imgbox">
				<img '.coursecat::my_get_course_formatted_summary_pix(new course_in_list($course)).' />
			</div>
			<div class="img-info">
				<a href="#">
					<p class="img-info-title">'.$course->fullname.'</p>
				</a>
				<p class="img-info-article img-info-article-sin">'.mb_substr(strip_tags($course->summary),0,15,'utf-8').'</p>
				<p class="img-info-article"><span class="glyphicon glyphicon-user"></span>&nbsp;'.$course->studnum.'</p>
			</div>
			<a href="'.$CFG->wwwroot. '/course/view.php?id='.$course->id.'"><button class="btn btn-info slearn">继续学习</button></a>
			<div class="img-info-tech">
				<p class="img-info-tech-title">学习进度</p>
				<p>'.$completeRate.'</p>
			</div>
			<div class="img-info-tech">
		<p class="img-info-tech-title">分类</p>
		<p>'.my_courseType($course->id).'</p>
	</div>
		</div>
			';
		//$no++;
	}
	//END 输出课程
echo '</div>';
	echo_end($page,$coursescount);//输出上下页按钮
}
/** END  输出课程列表*/

/** Start 输出课程的性质（必修、选修、自选） 徐东威 20160314
 * 获取该用户的所有任务，从中划分出必修和选修两个数组再进行查找
 * 备注：根据需求，这里将同为必修和选修性质的课程统一定为‘必修’
 * @param $courseID 课程ID
 * @return $type 课程性质
 */
function my_courseType($courseID){

	global $DB;
	global $USER;

	$flag = 0;
	$type = '自选课';//课程类型
	$missions = $DB->get_records_sql("select mm.id,mm.required_course_id,mm.optional_course_id from mdl_mission_my mm where mm.id in (select mum.mission_id from mdl_mission_user_my mum where mum.user_id = $USER->id)");
	$required_course_ids = array();//必修课数组
	$optional_course_ids = array();//选修课数组
	foreach($missions as $mission){
		$temp1 = array();
		$temp2 = array();
		$temp1 = explode(',',$mission->required_course_id);//将课程的id转化为数组
		$temp2 = explode(',',$mission->optional_course_id);
		foreach($temp1 as $temp){
			$required_course_ids[] = $temp;
		}
		foreach($temp2 as $temp){
			$optional_course_ids[] = $temp;
		}
	}
	//先判断是否是必修课
	foreach($required_course_ids as $required_course_id){
		if($courseID == $required_course_id){
			$flag = 1;
			$type = '必修课';
			break;
		}
	}
	//如果不是必修课，再判断是否是选修课
	if($flag != 1){
		foreach($optional_course_ids as $optional_course_id){
			if($courseID == $optional_course_id){
				$flag = 1;
				$type = '选修课';
				break;
			}
		}
	}

	return $type;
}

/** START 输出上下页按钮等
 * @param  $page 页码
 * @param  $count 中的 $count->record_count 总的记录数
 */
function echo_end($page,$count){

	$total = $count->record_count;//记录数
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

