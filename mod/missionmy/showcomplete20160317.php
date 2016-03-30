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
$PAGE->set_title('台账任务完成情况');
$PAGE->set_heading('台账任务完成情况');

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
                    <h4>必修课任务</h4>
                </div>';
			echo_courseStateTable($requiredCoures);
			//END 输出必修课任务状态

			//START 输出选修课任务状态
			echo '<div>
                    <h4>选修课任务</h4>
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
		'状态',
		'完成时间'
	);
	$table->colclasses = array('no', 'courseName', 'state','completeTime');//定义列的绑定名

	global $DB;
	global $USER;

	$no = 1;//序号
	foreach($courses as $course){

		$courseName = $course->fullname;
		$state = '未完成';
		$completeTime = '——';//完成时间

		$completeState = $DB->get_record_sql("select c.id,c.timecompleted from mdl_course_completion_crit_compl c where c.userid = $USER->id and c.course = $course->id");
		if($completeState){//如果有记录
			$state = '完成';
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



