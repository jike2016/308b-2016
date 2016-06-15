$(document).ready(function() {
	if(getQueryString('note')){
		$('.right-banner').load('mynote/index.php?note='+getQueryString('note'));//课程笔记：1  个人笔记：2
		//修改样式
		$('.menubtn').removeClass('menubtn-active');
		$('#mynote').addClass('menubtn-active');
		
	}
	else if(getQueryString('class')){//如果带的参数是 zhibo
		$('.right-banner').load('myclass/index.php?class='+getQueryString('class'));//class = zhibo: 直播
		//修改样式
		$('.menubtn').removeClass('menubtn-active');
		$('#mycourse').addClass('menubtn-active');
	}
	else{
		$('.right-banner').load('myclass/index.php');
	}
	

	$('.menubtn').click(function() {
		$('.menubtn').removeClass('menubtn-active');
		$(this).addClass('menubtn-active');
	});
	
	/***左边菜单***/
	$('#mycourse').click(function() {   //我的课程
		$('.lockpage').show();
		$('.right-banner').load('myclass/index.php');
	});
	$('#myexam').click(function() {     //我的考试
		$('.lockpage').show();
		$('.right-banner').load('myexam/index.php');
	});
	$('#mynote').click(function() {     //我的笔记
		$('.lockpage').show();
		$('.right-banner').load('mynote/index.php');
	});
	$('#mymedal').click(function() {    //我的勋章
		$('.lockpage').show();
		$('.right-banner').load('mymedal/index.php');
	});
	$('#mymission').click(function() {    //台账任务
		$('.lockpage').show();
		$('.right-banner').load('mymission/index.php');
	});
	$('#learning_circles').click(function() {    //学习圈 2016.3.17郑栩基添加
		$('.right-banner').load('learning_circles/index.php');
	}); 
	$('#mycollection').click(function() {     //我的收藏
		$('.lockpage').show();
		$('.right-banner').load('mycollection/index.php');
	});
	$('#mybookdata').click(function() {    //台账数据
		$('.lockpage').show();
		$('.right-banner').load('mybookdata/index.php');
	});
	$('#microreaddata').click(function() {    //微阅台账
		$('.lockpage').show();
		$('.right-banner').load('microreaddata/index.php');
	});
	$('#personaldata').click(function() {    //个人资料
		$('.lockpage').show();
		$('.right-banner').load('mypersonaldata/personaldata.html');//用iframe 加载页面 、、\moodle\user\my_description.php
	});
	/***左边菜单 end***/

});


function getQueryString(name) { 
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
	var r = window.location.search.substr(1).match(reg); 
	if (r != null) return unescape(r[2]); return null; 
} 