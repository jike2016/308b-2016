<?php
require_once('../config.php');
global $CFG;
?>

<link rel="stylesheet" href="css/ad_board.css" />

<script src="<?php $CFG->wwwroot?>/moodle/theme/more/js/jquery-1.11.3.min.js"></script><!--全局-->
<script type="text/javascript">

	$(function(){
//		$('#photoimg').die('click').live('change', function(){
//		$('#upload_btn').click(function(){
//			var status = $("#up_status");
//			var btn = $("#up_btn");
//			$("#imageform").ajaxForm({
//			target: '#preview',
//			beforeSubmit:function(){
//				status.show();
//				btn.hide();
//			},
//			success:function(){
//				status.hide();
//				btn.show();
//			},
//			error:function(){
//				status.hide();
//				btn.show();
//			} }).submit();
//		});

		//保存
		$('#pic1_save').click(function(){
			$("#imageform1").submit();
		});
		$('#pic2_save').click(function(){
			$("#imageform2").submit();
		});
		$('#pic3_save').click(function(){
			$("#imageform3").submit();
		});
		$('#pic4_save').click(function(){
			$("#imageform4").submit();
		});
		$('#pic5_save').click(function(){
			$("#imageform5").submit();
		});

		//删除
		$('#pic1_del').click(function(){
			$.ajax({
				url: "../uploadindexpicture/delete.php",
				data: { picID: 1},
				success: function(msg){
					if(msg == 1){
						 alert('删除成功');
						window.location.href='index.php';
					}else{
						alert('删除失败');
					}
				}
			});
		});
		$('#pic2_del').click(function(){
			$.ajax({
				url: "../uploadindexpicture/delete.php",
				data: { picID: 2},
				success: function(msg){
					if(msg == 1){
						alert('删除成功');
						window.location.href='index.php';
					}else{
						alert('删除失败');
					}
				}
			});
		});
		$('#pic3_del').click(function(){
			$.ajax({
				url: "../uploadindexpicture/delete.php",
				data: { picID: 3},
				success: function(msg){
					if(msg == 1){
						alert('删除成功');
						window.location.href='index.php';

					}else{
						alert('删除失败');
					}
				}
			});
		});
		$('#pic4_del').click(function(){
			$.ajax({
				url: "../uploadindexpicture/delete.php",
				data: { picID: 4},
				success: function(msg){
					if(msg == 1){
						alert('删除成功');
						window.location.href='index.php';
					}else{
						alert('删除失败');
					}
				}
			});
		});
		$('#pic5_del').click(function(){
			$.ajax({
				url: "../uploadindexpicture/delete.php",
				data: { picID: 5},
				success: function(msg){
					if(msg == 1){
						alert('删除成功');
						window.location.href='index.php';
					}else{
						alert('删除失败');
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
$PAGE->set_title('首页广告编辑');
$PAGE->set_heading('首页广告编辑');

echo $OUTPUT->header();//输出layout文件



$str = '<div id="ad_board_main">
			<div class="title">
				<h3><span class="glyphicon glyphicon-cog"></span>&nbsp;首页滚动广告板设置</h3>
				<h5>（图片宽度需要控制在1024像素，高度控制在460像素左右）</h5>
			</div>

			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>序号</td>
						<td>图片</td>
						<td>链接</td>
						<td>颜色</td>
						<td>操作</td>
					</tr>
				</thead>
				<tbody>';



$pictrues = $DB->get_records_sql("select * from mdl_index_picture");

$i = 1;
foreach($pictrues as $pictrue) {

	$url = $pictrue->pictureurl;
	$link = $pictrue->picturelink;
	$color = $pictrue->picturecolor;

	$str .= '	<tr>
					<form id="imageform'.$i.'" method="post" enctype="multipart/form-data" action="upload.php">
						<td>'.$i.'</td>
						<td class="td2">
							<div class="bannerimg-box">
								<img id="img'.$i.'" src="'.$url.'"/>
							</div>
							<div id="up_btn" class="btn btn-info">
									<span>上传图片</span>
									<input id="photoimg" name="photoimg" type="file">
							</div>
						</td>
						<td>
							<input id="picLink" name="picLink" class="form-control" placeholder="请输入图片链接" value="'.$link.'"/>
						</td>
						<td>
							<input id="picBackcolor" name="picBackcolor" class="form-control" placeholder="背景板颜色，如：#ffffff" value="'.$color.'"/>
							<input id="id" name="id" type="hidden" value="'.$i.'"/>
						</td>
					</form>

						<td>
							<button id="pic'.$i.'_save" class="btn btn-info">保存</button>
							<button id="pic'.$i.'_del"class="btn btn-danger">删除</button>
						</td>
					</tr>';


	$i++;
}

$str .= '
				</tbody>
			</table>
		<div>';

echo $str;


echo $OUTPUT->footer();//输出左右和底部
