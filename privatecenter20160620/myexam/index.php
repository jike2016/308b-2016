<script>
var currenturl='';//页面Url
var categoryid=0;
	$('.maininfo-box').load('myexam/exam_all_done.php');
	currenturl='myexam/exam_all_done.php';
	$('#exam_all-son').show();
	
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});
	
	/***我的考试-统一考试***/
	$('#exam_all').click(function() {        //统一考试-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('#exam_all-son').show();
		$('.maininfo-box').load('myexam/exam_all_done.php');	
		currenturl='myexam/exam_all_done.php';
		$('.kinds-son-a ').removeClass('a-active');
		$('#exam_all-done').addClass('a-active');
	});
	
		$('#exam_all-done').click(function() {   //统一考试的子标签-已考
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_all_done.php');
			currenturl='myexam/exam_all_done.php';
		});	
			
		$('#exam_all-next').click(function() {   //统一考试的子标签-待考
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_all_next.php');
			currenturl='myexam/exam_all_next.php';
		});	
			
		$('#exam_all-going').click(function() {  //统一考试的子标签-进行中
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_all_going.php');
			currenturl='myexam/exam_all_going.php';
		});	
	/***我的考试-统一考试 end***/
			
	/***我的考试-自主考试***/
	$('#exam_one').click(function() {        //自主考试-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('#exam_one-son').show();
		$('.maininfo-box').load('myexam/exam_one_done.php');
		currenturl='myexam/exam_one_done.php';
		$('.kinds-son-a ').removeClass('a-active');
		$('#exam_one-done').addClass('a-active');
	});
	
		$('#exam_one-done').click(function() {  //自主考试的子标签-已考
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_one_done.php');
			currenturl='myexam/exam_one_done.php';
		});
		
		$('#exam_one-next').click(function() {  //自主考试的子标签-待考
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_one_next.php');
			currenturl='myexam/exam_one_next.php';
		});
	/***我的考试-自主考试 end***/
	
	/***我的考试-在线练习***/
	$('#exam_practice').click(function() {   //在线练习-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('#exam_practice-son').show();
		$('.maininfo-box').load('myexam/exam_practice_record.php');	
		currenturl='myexam/exam_practice_record.php';
		$('.kinds-son-a ').removeClass('a-active');
		$('#exam_practice-record').addClass('a-active');
	});
	
		$('#exam_practice-record').click(function() {  //在线练习的子标签-记录
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_practice_record.php');
			currenturl='myexam/exam_practice_record.php';
		});
		
		$('#exam_practice-new').click(function() {     //在线练习的子标签-新建练习
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('myexam/exam_practice_new.php');
			currenturl='myexam/exam_practice_new.php';
		});	
	/***我的考试-在线练习 end***/
	
	$('#classkindslist li').on('click', function(){ //课程排序下拉菜单
		$(this).parent().parent().parent().children('.classkinds').val($(this).text());
		categoryid=$(this).val();
	});	
	
	$('.search-btn').on('click', function(){ //课程排序搜索按钮
		$('.maininfo-box').load(currenturl+'?categoryid='+categoryid);
	});	
	
</script>

<style>
	.dropdownlist{float: right;width: 150px;height: 36px;margin: 0px 10px;}
	.dropdownlist .dropdown-menu {min-width: 150px;}
	.kinds {margin-bottom: 5px;}
	.classinfo-box {min-height: 510px;}
	.footer-box {background-color: #F0F0F0;  border: 1px solid #ccc; border-top: 0px;}
	.footer { background-color: #F0F0F0; width: 60%; margin: auto; height: 48px; padding: 15px 0px;}
	.footer p {display: inline-block; color: #5E5E5E;}
	.footer .p-14-red {color: #C9302C;}
	.footer .right,.center {float: right;}
	.footer .right{margin-right: 60px;}
	.footer a {margin-right: 40px;}
	
</style>

<!--我的考试-->
	<div class="myclass">
		<div class="kinds">
			<a id="exam_all" class="kinds-a a-active" href="javascript:void(0)">统一考试</a>l
			<a id="exam_one" class="kinds-a" href="javascript:void(0)">自主考试</a>l
			<a id="exam_practice" class="kinds-a" href="javascript:void(0)">在线练习</a>
		</div>
		
		<div class="kinds-son">	
			<div class="left">
				<div id="exam_all-son" class="kinds-son-abox">
					<a id="exam_all-done"  class="kinds-son-a a-active" href="javascript:void(0)">已考</a>l
					<a id="exam_all-next"  class="kinds-son-a" href="javascript:void(0)">待考</a>l
					<a id="exam_all-going" class="kinds-son-a" href="javascript:void(0)">进行中</a>
				</div>
				<div id="exam_one-son" class="kinds-son-abox">
					<a id="exam_one-done" class="kinds-son-a a-active" href="javascript:void(0)">已考</a>l
					<a id="exam_one-next" class="kinds-son-a" href="javascript:void(0)">待考</a>l
				</div>
				<div id="exam_practice-son" class="kinds-son-abox">
					<a id="exam_practice-record" class="kinds-son-a a-active" href="javascript:void(0)">全部</a>
					<!--<a id="exam_practice-new" class="kinds-son-a" href="javascript:void(0)">新建练习</a>l-->
				</div>
			</div>						
						
			<button class="btn btn-info search-btn">搜索</button>
			
			<!--课程类型下拉表单-->
			<div class="dropdownlist">
				<div class="input-group">
					<input  type="text" class="form-control classkinds" value="全部" readOnly="true" style="background-color:#ffffff;cursor:pointer" >
					<div class="input-group-btn">
						<button class="btn  dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul id="classkindslist" class="dropdown-menu dropdown-menu-right" style="cursor:pointer">
						<?php
							require_once("../../config.php");
							global $DB;
							$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where depth=1 and visible=1 ORDER BY sortorder;');
							echo '<li value="0"><a>全部</a></li>';
							foreach ($categorys as $category) {
								echo'<li value="'.$category->id.'"><a>'.$category->name.'</a></li>';
							}
						?>
							

						</ul>
					</div>
				</div>
			</div>
			<!--课程类型下拉表单end-->
		</div>
		<div class="maininfo-box">
		
		</div>
<!--我的考试页面-end-->
