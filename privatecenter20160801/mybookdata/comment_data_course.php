
<style>
	.maininfo-box {width: 100%;background-color: #FFFFFF; min-height: 615px; border: 1px solid #ccc; padding: 10px 20px 20px 20px;}
	.head-box {width: 100%; height: 80px;border-bottom: 2px solid #CCCCCC;}
	.head-box h3 {color: #777777; margin: 15px 0px 0px 0px; float: left;}
	.head-box .a-box {width: 100%; height: 20px; padding: 0px;}
	.head-box .a-box #return-index {color: #777777; text-decoration: none; cursor: pointer; font-size: 16px;}
	.head-box .a-box #return-index:hover {color: #18BBED;}
	.head-box #num {color: #10ADF3;}
	
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
	/*******分页*******/
	.footer-box {background-color: #F0F0F0;  border: 1px solid #ccc; border-top: 0px;}
	.footer { background-color: #F0F0F0; width: 60%; margin: auto; height: 48px; padding: 15px 0px;}
	.footer p {display: inline-block; color: #5E5E5E;}
	.footer .p-14-red {color: #C9302C;}
	.footer .right,.center {float: right;}
	.footer .right{margin-right: 60px;}
	.footer a {margin-right: 40px;}	
	/*******分页 @end*******/
	
	.table tr .td3_text  p{margin: auto;}
	.table tr .td3_text p{ width: 100%;overflow: hidden; /*自动隐藏文字*/text-overflow: ellipsis;/*文字隐藏后添加省略号*/white-space: nowrap;/*强制不换行*/width: 13em;/*不允许出现半汉字截断*/}

</style>


<?php
require_once("../../config.php");
$page = optional_param('page', 1, PARAM_INT);
$courseid = optional_param('courseid', 1, PARAM_INT);
// $categoryid = optional_param('categoryid', 0, PARAM_INT);
global $DB;
global $USER;
$coursename = $DB->get_record_sql('select fullname from mdl_course where id='.$courseid);
echo_comments_course($page,$courseid,$coursename);//输出笔记列表

/**STATR  输出笔记列表*/
function echo_comments_course($page,$courseid,$coursename){
	$numofpage=15;
	$offset=($page-1)*$numofpage;//获取limit的第一个参数的值 offset ，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。
	
	global $DB;
	global $USER;

	$userID = $USER->id;
	// $notes = $DB->get_records_sql("select * from mdl_note_my m where m.userid = $userID order by time desc limit $offset,$numofpage");//分页查询，获取课程笔记中的10条记录
	$comments= $DB->get_records_sql("select commenttime,articleid,null as courseid,null as modid,`comment` from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course=$courseid where a.userid=$userID
		union ALL
		select commenttime,null as articleid,courseid,null as modid,`comment` from mdl_comment_course_my where courseid=$courseid and userid=$userID
		union ALL
		select commenttime,null as articleid,null as courseid, modid,`comment` from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course=$courseid where a.userid=$userID
		ORDER BY commenttime desc
		limit $offset,$numofpage
		");
	$commentscount = $DB->get_records_sql("select commenttime,articleid,null as courseid,null as modid,`comment` from mdl_comment_article_my a join mdl_course_modules b on b.id=a.articleid and b.course=$courseid where a.userid=$userID
		union ALL
		select commenttime,null as articleid,courseid,null as modid,`comment` from mdl_comment_course_my where courseid=$courseid and userid=$userID
		union ALL
		select commenttime,null as articleid,null as courseid, modid,`comment` from mdl_comment_video_my a join mdl_course_modules b on b.id=a.modid and b.course=$courseid where a.userid=$userID
		");
	$no = ($page-1)*$numofpage+1;//序号
	echo'
		<div class="maininfo-box">
		<div class="head-box">
		<div class="a-box"><a id="return-index"><span class="glyphicon glyphicon-menu-left"></span>返回</a></div>
		<h3>课程评论台账&nbsp;:&nbsp;&nbsp;</h3>
		<h3 id="num">'.count($commentscount).'</h3>
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

	 echo_end($page,count($commentscount),$numofpage);//输出上下页按钮

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
	$("#return-index").click(function(){
		$('.lockpage').show();
		<!--$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.right-banner').load('mybookdata/index_course.php?courseid=<?php echo $courseid;?>');-->
		$(this).parent().parent('.head-box').parent('.maininfo-box').parent('.maininfo-box-index').load('mybookdata/index_course.php?courseid=<?php echo $courseid;?>');
	})
	
	//上下页的跳转
	$('.pre-btn').click(function() {  //上一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());//获取当前页码
		//alert(page);
		page--;
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php echo $courseid;?>");
	});
	$('.next-btn').click(function() {  //下一页
		$('.lockpage').show();
		var page=parseInt($('#pageid').text());
		page++;
		// alert(page);
		$(this).parent('.footer').parent('.footer-box').parent().load("mybookdata/comment_data_course.php?page="+page+"&courseid=<?php echo $courseid;?>");
	});
</script>