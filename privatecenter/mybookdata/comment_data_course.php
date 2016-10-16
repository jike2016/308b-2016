<?php
/**
 * 个人中心》台账数据》课程统计》单课程评论查看 页面
 */
require_once("../../config.php");
$page = optional_param('page', 1, PARAM_INT);
$courseid = optional_param('courseid', 1, PARAM_INT);
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
	.td3 {width: 25%;}
	.td4 {width: 22.5%;}
	.td5 {width: 22.5%;}
	.table tr td{text-align: center;}
	.table tr .td3_text  p{margin: auto;}
	.table tr .td3_text p{ width: 100%;overflow: hidden; /*自动隐藏文字*/text-overflow: ellipsis;/*文字隐藏后添加省略号*/white-space: nowrap;/*强制不换行*/width: 13em;/*不允许出现半汉字截断*/}
	#time_plug{display: none;}/*隐藏时间控件*/
</style>
<link rel="stylesheet" href="css/personal-footer.css" />

<?php
$coursename = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
echo_comments_course($page,$courseid,$coursename,$start_time,$end_time);//输出笔记列表

/**STATR  输出笔记列表*/
function echo_comments_course($page,$courseid,$coursename,$start_time,$end_time){
	$numofpage=15;
	$offset=($page-1)*$numofpage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
	
	global $DB;
	global $USER;

	$userID = $USER->id;
	// $notes = $DB->get_records_sql("select * from mdl_note_my m where m.userid = $userID order by time desc limit $offset,$numofpage");//分页查询，获取课程笔记中的10条记录
	//添加自定义字段，防止moodle 数据封装时因键值相同丢弃数据
	$sql = "select CONCAT('article',commenttime) as myfield,commenttime,articleid,null as courseid,null as modid,`comment` from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course=$courseid where a.userid=$userID and commenttime between $start_time and $end_time
			union ALL
			select CONCAT('course',commenttime) as myfield,commenttime,null as articleid,courseid,null as modid,`comment` from mdl_comment_course_my where courseid=$courseid and userid=$userID and commenttime between $start_time and $end_time
			union ALL
			select CONCAT('video',commenttime) as myfield,commenttime,null as articleid,null as courseid, modid,`comment` from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course=$courseid where a.userid=$userID and commenttime between $start_time and $end_time
			ORDER BY commenttime desc
			limit $offset,$numofpage
			";
	$comments= $DB->get_records_sql($sql);
	$sql = 'select count(1) as commentcount from
				(select commenttime from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course='.$courseid.' where a.userid='.$USER->id.' and commenttime between '.$start_time.' and '.$end_time.'
				union ALL
				select commenttime from mdl_comment_course_my where courseid='.$courseid.' and userid='.$USER->id.' and commenttime between '.$start_time.' and '.$end_time.'
				union ALL
				select commenttime from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course='.$courseid.' where a.userid='.$USER->id.' and commenttime between '.$start_time.' and '.$end_time.'
				ORDER BY commenttime desc)
			as temp ';
	$commentscount = $DB->get_record_sql($sql);
	$no = ($page-1)*$numofpage+1;//序号
	echo'
		<div class="maininfo-box">
		<div class="head-box">
		<div class="a-box"><a id="return-index"><span class="glyphicon glyphicon-menu-left"></span>返回</a></div>
		<h3>课程评论台账&nbsp;:&nbsp;&nbsp;</h3>
		<h3 id="num">'.$commentscount->commentcount.'</h3>
	</div>

	
	<div class="table-box">
		<table class="table">
			<thead>
				<tr class="active">
					<td class="td1">序号</td>
					<td class="td2">时间</td>
					<td class="td3">内容</td>
					<td class="td4">章节</td>
					<td class="td5">课程</td>
				</tr>
			</thead>
			<tbody>
	';
	//////////////////////////////////////////////////////////
	foreach($comments as $comment){
		if(isset($comment->courseid)){//课程评论
			$course = $DB->get_record_sql('select fullname from mdl_course where id ='.$comment->courseid);
			$commenttype='无';
		}
		elseif(isset($comment->modid)){//视频
			$course = $DB->get_record_sql('select c.name,b.fullname from mdl_course_modules a join mdl_course b on b.id = a.course join mdl_lesson c on c.id = a.instance where a.id='.$comment->modid);
			$commenttype=$course->name;
		}
		elseif(isset($comment->articleid)){//文章评论
			$course = $DB->get_record_sql('select c.name,b.fullname from mdl_course_modules a join mdl_course b on b.id = a.course join mdl_page c on c.id = a.instance where a.id='.$comment->articleid);
			$commenttype=$course->name;
		}
		echo '<tr>
				<td>'.$no.'</td>
				<td>'.userdate($comment->commenttime,'%Y-%m-%d %H:%M').'</td>
				<td class="td3_text"><p>'.mb_substr(strip_tags($comment->comment),0,30,'utf-8').'</p></td>
				<td>'.$commenttype.'</td>
				<td>'.$course->fullname.'</td>
			</tr>';
		$no++;
	}

	echo '		</tbody>
			</table>
		</div>
		</div>';

	 echo_end($page,$commentscount->commentcount,$numofpage);//输出上下页按钮

}
/**END  输出笔记列表*/
function echo_end($page,$count,$numofpage){

	$total = $count;//总记录数
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

<script>
	$('.lockpage').hide();
	var time_param = '';
	if(time_flag){
		time_param = 'start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>';
	}
	$("#return-index").click(function(){
		$('.lockpage').show();
		<!--$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid=<?php echo $courseid;?>');-->
		<!--$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid=<?php echo $courseid;?>');-->
		$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid=<?php echo $courseid;?>&'+time_param);
	})
	
	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
//		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php //echo $courseid;?>//");
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php echo $courseid;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>");
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		// alert(page);
//		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php //echo $courseid;?>//");
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php echo $courseid;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>");
	});
</script>