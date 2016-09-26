<!DOCTYPE html>

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
		</style>
		
		<script>
			$(document).ready(function(){
				$('#classkindslist li').on('click', function(){ //课程排序下拉菜单
					$(this).parent().parent().parent().children('.classkinds').val($(this).text());
				});
			})
				
		</script>
	</head>
	<body>
<div class="note_class_new_title">
	<p>记笔记</p>
</div>
	
	<div class="note_class_new_info">
		<p>标题</p>
		<input class="form-control title" placeholder="请输入标题内容..." />
	</div>
	
	<div class="note_class_new_info">
		<p>针对</p>
		
		<!--课程类型下拉表单-->
		<div class="dropdownlist">
			<div class="input-group">
				<input  type="text" class="form-control classkinds" value="政治教育">
				<div class="input-group-btn">
					<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
					<ul id="classkindslist" class="dropdown-menu dropdown-menu-right">
						<li value="1"><a>政治教育</a></li>
						<li value="2"><a>思想教育</a></li>
						<li value="3"><a>先进思想</a></li>
					</ul>
				</div>
			</div>
		</div>
		<!--课程类型下拉表单end-->
		
		<p>作笔记</p>
	</div>
	
	<div class="write-box">
		<p>笔记内容</p>
		<textarea class="form-control" placeholder="在此记录你的想法..."></textarea>
		<button class="btn btn-success">保存</button>
	</div>
	</body>

</html>