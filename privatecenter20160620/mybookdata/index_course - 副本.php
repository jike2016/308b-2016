<style>
	.maininfo-box {width: 100%;background-color: #FFFFFF; min-height: 665px; border: 1px solid #ccc; padding: 0px 20px 20px 20px;}
	/***下拉菜单 以及 饼状图**/
	.dropdownlist { float: left; margin-top: 15px; margin-left: 20px;width: 150px;}
	.dropdownlist .dropdown-menu {min-width: 150px;}
	.learnreportkinds{width: 170px;}
	.head-box1 {width: 100%; height: 60px;}
	.head-box1 h3 {float: left;}
	.head-box2 {width: 100%;height: 60px;}
	/***下拉菜单 以及 饼状图 @end**/
	
	.maininfo-box h3 {color: #777777; font-size: 23px;}
	.maininfo-box .line {width: 100%; height: 2px; background-color: #CCCCCC;}
	.bookdata-box {width:100%;height: 50px; margin-top: 15px;}
	.bookdata-box p {font-size: 16px; color: #777777; float: left; margin: 12px 0px 0px 50px;}
	.bookdata-box .score {font-size: 25px; color: #449D44;margin: 5px 0px 0px 0px;}
	
	.learningsituation-box {width: 100%; margin-top: 20px;}
	
	.learningsituation-box h5,.learningsituation-box h3 {float: left;}
	.learningsituation-box h5 {font-size: 14px; color: #CCCCCC;margin-left: 20px; margin-top: 30px;}
	.highcharts-button{display: none;}
	text tspan{font-family: "微软雅黑";}
	
	.data-box:hover {background-color: #F0F0F0; cursor: pointer;}
</style>
<?php
	require_once("../../config.php");
	$courseid = optional_param('courseid', 1, PARAM_INT);
	global $DB;
	global $USER;
	$coursename = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
	echo '
		<div class="maininfo-box">
	<!--新加下拉菜单 修改-->
	<div class="head-box1">
		<h3>学习报告</h3>
		
		<!--学习报告类型下拉表单-->
		<div class="dropdownlist">
			<div class="input-group">
				<input type="text" class="form-control learnreportkinds" style="background-color:#ffffff;cursor:pointer" readOnly="true" value="'.$coursename->fullname.'">
				<div class="input-group-btn">
					<button class="btn btn-info  dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul id="learnreportkindslist" class="dropdown-menu dropdown-menu-right" style="cursor:pointer">
						<li value="1"><a>全部</a></li>';
	$courses=$DB->get_records_sql('select
		c.id,c.fullname
		from mdl_user_enrolments a
		join mdl_enrol b on b.id=a.enrolid
		join mdl_course c on c.id=b.courseid
		where a.userid='.$USER->id.' 
		GROUP BY courseid ORDER BY a.timecreated DESC');
	foreach($courses as $course){
		echo '<li value="'.$course->id.'"><a>'.$course->fullname.'</a></li>';
	}
	echo '
					</ul>
				</div>
			</div>
		</div>
		<!--学习报告类型下拉表单end-->
	</div>
	<!--新加下拉菜单 修改 end-->
	';
	$notes = $DB->get_record_sql('select count(1)as recordcount from mdl_note_my where notetype=1 and userid='.$USER->id.' and courseid='.$courseid);
	$comments = $DB->get_records_sql('select commenttime from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course='.$courseid.' where a.userid='.$USER->id.'
		union ALL
		select commenttime from mdl_comment_course_my where courseid='.$courseid.' and userid='.$USER->id.'
		union ALL
		select commenttime from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course='.$courseid.' where a.userid='.$USER->id.'
		ORDER BY commenttime desc');
	// $like_course = $DB->get_record_sql('select count(1) as count from mdl_course_like_my where userid='.$USER->id);
	
	echo '
		<div class="line"></div>
	<div class="bookdata-box">		
		<div id="note-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程笔记：</p>
			<p class="score">'.$notes->recordcount.'</p>
		</div>
		
		<div id="comment-data" class="data-box" style="float: left;width:20%; height:50px; border-right:1px solid #CCCCCC; float:left;">
			<p>课程评论：</p>
			<p class="score">'.count($comments).'</p>
		</div>
		
	</div>
	';
	
?>
<script type="text/javascript">
	$('.lockpage').hide();

	$(function() {
		$('#Histogram').highcharts({
			
			title: {   //正标题
				text: ' '
			},
			subtitle: { //副标题
				text: ' '
			},
			xAxis: {  //X轴文本
				categories: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: { //Y轴提示
					text: '课时',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {  //数据点提示
				valueSuffix: '课时'
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			credits: {  
				enabled: false
			},
			series: [{ //数据
				name: '1800 年',
				data: [107, 31, 400, 203, 2, 2, 2]
			}]
		});
	});
</script>

<script>
	$(document).ready(function(){		
		//课程排序下拉菜单点击局部刷新事件
		$('#learnreportkindslist li').click(function(){  
			$('.lockpage').show();		
			$(this).parent().parent().parent().children('.learnreportkinds').val($(this).text());
			// $(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index2.html');
			if($(this).val()==1){
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index.php');
			}
			else{
				$(this).parent().parent().parent().parent().parent('.head-box1').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid='+$(this).val());
			}
		});	
		//课程排序下拉菜单点击局部刷新事件 end
		
		
		$('#note-data').click(function(){      //查看笔记数据详情
			$('.lockpage').show();
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/note_data_course.php?courseid=<?php echo $courseid;?>");
		});	
		$('#comment-data').click(function(){   //查看评论数据详情
			$('.lockpage').show();
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load("mybookdata/comment_data_course.php?courseid=<?php echo $courseid;?>");
		});	
		$('#like-data').click(function(){      //查看点赞数据详情
			$(this).parent().parent('.maininfo-box').parent('.right-banner').load('mybookdata/like-data.html');
		});	
	})
</script>


	
	
	
	
	<div class="learningsituation-box">
		<div class="head-box2"><h3>七日学习情况</h3><h5>单位：课时</h5></div>
		
		<!--折线图-->
		<div id="Histogram" style="width: 100%; height: 400px; margin: 0 auto"></div>
		<!--折线图 end-->
	</div>
</div>