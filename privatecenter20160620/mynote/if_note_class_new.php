<!DOCTYPE html>
<?php
	require_once("../../config.php");
	global $DB;
	$courseid = '';
	$noteTitle = '';
	$coursename = '';
	//获取url 传递的参数，用于填充课程笔记的信息
	if(isset($_GET['noteTitle'])){
		$noteTitle = $_GET['noteTitle'];
	}
	if(isset($_GET['courseid'])){
		$courseid = $_GET['courseid'];
		$coursename = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
		// echo "<script type='text/javascript'>select_defult('$courseid','$coursename->fullname');</script>";
	}
 ?>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>	
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<script type="text/javascript" src="../js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<style>
			body{font-family: "微软雅黑";}
		    .maininfo {border: 1px solid #CCCCCC;}
			.note_class_new_title {width: 100%;height: 50px;border-bottom: 2px solid #F0F0F0;  padding: 10px 20px;}
			.note_class_new_title p {font-size: 20px; color:#777;}
			.note_class_new_info {width: 100%; height: 50px; padding: 8px 20px;}
			.note_class_new_info p {float: left; margin: 7px 20px 0px 0px;}
			.note_class_new_info .title {width: 250px; float: left;}
			
			.dropdownlist{float:left;width: 150px;height: 36px; margin-right: 20px;}
			.dropdownlist .dropdown-menu {min-width: 150px;}
			.write-box {width: 100%; height: 400px; padding: 0px 20px; margin-top: 10px;}
			.write-box textarea {width: 100%; height: 350px; }
			.write-box .btn {float: right; margin: 10px 0px 0px 15px;}
			
			.lockpage {z-index: 10000;position: fixed;top: 0;left: 0;width: 100%;height: 100%;background: #000;opacity: 0.4;filter: alpha(opacity=40); text-align: center;vertical-align:middle; display: none;}
			.lockpage img {width: 60px;position:absolute;top:40%;}
		</style>
		

		<script type="text/javascript">

		var courseid = <?php echo $courseid;?>;
		// alert(courseid);
	
	


	$(document).ready(function() {
		$('#classkindslist li').on('click', function(){ //课程排序下拉菜单
				 // alert('1231213');
				$(this).parent().parent().parent().children('.classkinds').val($(this).text());
				courseid = $(this).val();
			});

		/** start 保存课程笔记 */
		$('#noteSave_btn').click(function() {
			$('.lockpage').show();
			var notetitle =$('#notetitle').val();//title
			//var notecourseid =$('#classkindslist').val();//courseid
			var notetext =$(this).parent().children('.form-control').val();

			$.ajax({
			 url: "note_class_save.php",
			 data: { noteTitle: notetitle, courseId: courseid ,noteText:notetext },
				 success: function(msg){
					 if(msg == 1){
						  alert('创建成功');
					 }else{
						  alert('请输入标题并选择课程');
					 }
					 $('.lockpage').hide();
				 }
			 });
		});
		/** end 保存课程笔记 */

		/** start 取消创建 */
		$('#noteCancer_btn').click(function(){

			$('#notetitle').val('');
			$('#courseid').val('');
			$('#textarea').val('');
		});
		/** end 取消创建 */
		/**  */
		// alert('<?php echo $coursename->fullname;?>');
		$('.classkinds').val('<?php echo $coursename->fullname;?>');
		$('#notetitle').val('<?php echo $noteTitle;?>');
	});
	

</script>
	</head>
	<body>
	<!--锁屏-->
		<div class="lockpage">
			<img src="../img/loading.jpg"/>
		</div>
<?php
require_once("../../config.php");

echo_note_new();//新建课程笔记

//新建课程笔记
function echo_note_new(){
	global $DB;
	global $USER;
	

	$userID = $USER->id;

	echo '	<div class="note_class_new_title">
				<p>记笔记</p>
			</div>

			<div class="note_class_new_info">
				<p>标题</p>
				<input class="form-control title" id="notetitle" placeholder="请输入标题内容..."  value=""/>
			</div>

			<div class="note_class_new_info">
				<p>针对</p>

				<!--课程类型下拉表单-->
				<div class="dropdownlist">
					<div class="input-group">
						<input  type="text" readonly="true" id="courseid" class="form-control classkinds" value="">
						<div class="input-group-btn">
							<button class="btn  dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
							<ul id="classkindslist" class="dropdown-menu dropdown-menu-right">';

	//获取用户所选的课程信息
	$courses = $DB->get_records_sql('select
									c.id,c.category,c.fullname,c.summary
									from mdl_user_enrolments a
									join mdl_enrol b on b.id=a.enrolid
									join mdl_course c on c.id=b.courseid
									where a.userid='.$userID.'
									GROUP BY courseid ORDER BY a.timecreated DESC');
	foreach($courses as $course){
		echo '<li value="'.$course->id.'"><a>'.$course->fullname.'</a></li>';
	}

	echo '					</ul>
						</div>
					</div>
				</div>
				<!--课程类型下拉表单end-->

				<p>作笔记</p>
			</div>

			<div class="write-box">
				<p>笔记内容</p>
				<textarea class="form-control" id="textarea" placeholder="在此记录你的想法..."></textarea>
				<button class="btn btn-success" id="noteSave_btn">保存</button>
			</div>';

}


?>
	</body>

</html>