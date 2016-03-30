<style>

	table th {text-align: center;}
	table {margin-top: 15px;}
	
	.table > thead > tr > th { padding: 4px;}
	.table > thead > tr > th, .table > tbody > tr > th { vertical-align: middle;}
	.checkexam {height: 24px; width: 100px; padding: 0px;}
	.no {width: 10%;}
	.courseName {width: 30%;}
	.state {width: 20%;}
	.completeTime {width: 30%;}

</style>

<?php 

require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once("$CFG->libdir/formslib.php");

$mission_name     = optional_param('mission_name', 0, PARAM_TEXT);
$confirm    = optional_param('confirm', false, PARAM_BOOL);
require_login();//要求登录

$PAGE->set_pagelayout('incourseforstudent');//设置layout
$PAGE->set_title('学习任务完成情况');
$PAGE->set_heading('学习任务完成情况');

$missionID = '';//任务ID
$missionName = '';//任务名称
if(isset($_GET['id'])){
	$missionID = $_GET['id'];//任务id
	$mission = $DB->get_record_sql("select mm.id,mm.mission_name,mm.required_course_id,mm.optional_course_id from mdl_mission_my mm where mm.id = $missionID");
	$missionName = $mission->mission_name;//任务名称
}


//***START 页面*************************
echo $OUTPUT->header();
echo "<h2>$missionName 完成情况</h2>";

show_complete();//任务完成状态

echo $OUTPUT->footer();
//***END 页面***************************





/** START 任务完成状态 */
function show_complete(){

	if(isset($_GET['id'])){

		$missionID = $_GET['id'];//任务id

		global $DB;
		global $USER;

		$mission = $DB->get_record_sql("select mm.id,mm.mission_name,mm.required_course_id,mm.optional_course_id from mdl_mission_my mm where mm.id = $missionID");

		if($mission){//有任务记录
			$missionName = $mission->mission_name;//任务名称
			$requiredCouresID = $mission->required_course_id;//必修课
			$optionalCouresID = $mission->optional_course_id;//选修课

			$requiredCoures = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($requiredCouresID)");//必修课程
			$optionalCoures = $DB->get_records_sql("select c.id,c.fullname from mdl_course c where c.id in ($optionalCouresID)");//选修课程

			//START 输出必修课任务状态
			echo ' <div>
                    <h4>必修课</h4>
                </div>';
			echo_courseStateTable($requiredCoures);
			//END 输出必修课任务状态

			//START 输出选修课任务状态
			echo '<div>
                    <h4>选修课</h4>
               </div>';
			echo_courseStateTable($optionalCoures);
			//END 输出选修课任务状态

		}//END 有任务记录

	}
	else{
		echo '任务详情还需审核、、、';
	}
}
/** END 任务完成状态 */


/** START 表格输出 */
function echo_courseStateTable($courses){

	$table = new html_table();//定义表格
	$table->attributes['class'] = 'collection';
	$table->head = array(
		'序号',
		'课程名称',
		'完成状态',
		'完成时间'
	);
	$table->colclasses = array('no', 'courseName', 'state','completeTime');//定义列的绑定名

	global $DB;
	global $USER;
	global $CFG;

	$no = 1;//序号
	foreach($courses as $course){

		$courseName = $course->fullname;
		//为课程添加链接
		$courseName = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" target="_blank" >'.$courseName.'</a>';

//		$state = '未完成';
		$state = courseCompeleteRate($course);
		$completeTime = '——';//完成时间

		$completeState = $DB->get_record_sql("select c.id,c.timecompleted from mdl_course_completion_crit_compl c where c.userid = $USER->id and c.course = $course->id");
		if($completeState){//如果有记录
//			$state = '完成';
			$state = '100%';
			$time = $completeState->timecompleted;
			$completeTime = userdate($time,'%Y-%m-%d %H:%M');
		}

		//将各字段的数据填充到表格相应的位置中
		$row = array($no,$courseName,$state,$completeTime);//绑定数据
		$table->data[] = $row;

		$no++;//序号自增
	}

	echo $htmltable = html_writer::table($table);
}
/** END 表格输出 */

/**Start 课程完成率 徐东威 20160310
 * @param	$course 课程
 * @return  $completeRate 完成进度
 */
function courseCompeleteRate($course){

	global $DB;
	global $USER;

//	$completeRate = '无';//课程完成率
	$completeRate = '0%';//课程完成率,这里是台账任务，默认都会设置进度跟踪，所以这里初始为‘0%’
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
	return $completeRate;
}
/** End */



