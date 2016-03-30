<script>
$('.lockpage').hide();
</script>
<style>
    .maininfo {border: 1px solid #CCCCCC;}
	.note_class_new_title {width: 100%;height: 50px;border-bottom: 2px solid #F0F0F0;  padding: 10px 20px;}
	.note_class_new_title p {font-size: 20px; color:#777;}
	.note_class_new_info {width: 100%; height: 50px; padding: 8px 20px; margin-top: 20px; margin-bottom: 30px;}
	.note_class_new_info p {float: left; margin: 7px 20px 0px 0px;}
	.note_class_new_info .title {width: 250px; float: left;}
	
	.dropdownlist{float:left;width: 150px;height: 36px; margin-right: 20px;}
	.dropdownlist .dropdown-menu {min-width: 150px;}
	.write-box {width: 100%; height: 400px; padding: 0px 20px; margin-top: 10px;}
	.write-box textarea {width: 100%; height: 310px; }
	.write-box .btn {float: right; margin: 10px 0px 0px 15px;}
</style>

<script type="text/javascript">

	//保存
	$(document).ready(function() {
		$('#noteSave_btn').click(function() {
			$('.lockpage').show();
			var notetitle =$('#notetitle').val();//title
			var notetext =$(this).parent().children('.form-control').val();
//			alert(notetitle);
//			alert(notetext);

			$.ajax({
				url: "../privatecenter/mynote/note_personal_save.php",
				data: { noteTitle: notetitle ,noteText:notetext },
				success: function(msg){
					if(msg == 1){
						// alert('创建成功');

					}else{
						alert('创建失败(请确认笔记标题不为空)');
					}
					$('.lockpage').hide();
				}
			});
		});

		//取消
		$('#noteCancer_btn').click(function(){

			$('#notetitle').val('');
			$('#textarea').val('');
		});

	});
</script>


<?php
require_once("../../config.php");
global $DB;
global $USER;

echo_note_new();//新建课程笔记

//新建课程笔记
function echo_note_new()
{

	global $DB;
	global $USER;

	$userID = $USER->id;

	echo '<div class="note_class_new_title">
			<p>记笔记</p>
		</div>

		<div class="note_class_new_info">
			<p>标题</p>
			<input class="form-control title" id="notetitle" placeholder="请输入标题内容..." />
		</div>

		<div class="write-box">
			<p>笔记内容</p>
			<textarea class="form-control" id="textarea" placeholder="在此记录你的想法..."></textarea>
			<button class="btn btn-default" id="noteCancer_btn" >取消</button>
			<button class="btn btn-success" id="noteSave_btn" >保存</button>
		</div>';

}

?>




