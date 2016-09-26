<?php
/** 首页课程分类定制编辑 */

require_once('../config.php');
global $CFG;
?>

<link rel="stylesheet" href="css/ad_board.css" />

<script src="<?php $CFG->wwwroot?>/moodle/theme/more/js/jquery-1.11.3.min.js"></script><!--全局-->
<script type="text/javascript">

	$(function(){
		//保存
		$('#btn_save').click(function(){
			//获取各分类
			var categoryID = new Array();
			for(var i=0;i<5;i++){
				categoryID[i] = $('.select_category')[i].value;
			}
//			console.log(categoryID);
			$.ajax({
				url: "save.php",
				data: { categoryID: categoryID},
				success: function(msg){
					if(msg == 1){
						alert('保存成功');
						window.location.href='index.php';
					}else{
						alert('保存失败');
					}
				}
			});
		});
	});
</script>

<?php

global $DB;

//检查登陆
require_login();

$PAGE->set_pagelayout('ad_board');//设置layout
$PAGE->set_title('首页课程分类编辑');
$PAGE->set_heading('首页课程分类编辑');

echo $OUTPUT->header();//输出layout文件

//获取定制的课程分类
$sql = "select ic.*,cc.`name` from mdl_index_course_category ic
		left join mdl_course_categories cc on ic.course_category_id = cc.id";
$categorys = $DB->get_records_sql($sql);

//获取所有课程分类
$sql = "select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder";
$categoryAll = $DB->get_records_sql($sql);

$str = '<div id="ad_board_main">
			<div class="title">
				<h3><span class="glyphicon glyphicon-cog"></span>&nbsp;首页课程分类编辑</h3>
				<h5>（请选择课程分类）</h5>
			</div>

			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>序号</td>
						<td>课程分类</td>
					</tr>
				</thead>
				<tbody>';

$i = 1;
foreach($categorys as $category) {

	$str .= '	<tr>
					<td>'.$i.'</td>
					<td>
						<select class="form-control select_category" >
							<option value ="" >请选择课程分类</option>';
	foreach($categoryAll as $temp){
		if($temp->id == $category->course_category_id){
			$str .= ' <option value ="'.$temp->id.'" selected="selected" >'.$temp->name.'</option>';
		}else{
			$str .= ' <option value ="'.$temp->id.'">'.$temp->name.'</option>';
		}
	}
	$str .= '
						</select>
					</td>
				</tr>';
	$i++;
}

$str .= '
				</tbody>
			</table>
			<button id="btn_save" class="btn btn-info">保存</button>
		<div>';

echo $str;

echo $OUTPUT->footer();//输出左右和底部


