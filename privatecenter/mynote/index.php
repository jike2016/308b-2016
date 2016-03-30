<script>

	/** Start 笔记功能显示判断*/
	if(getQueryString('note')==1){//课程笔记：1  个人笔记：2
		//取值
		if(getQueryString('courseid') && getQueryString('noteTitle')){
			// $('.maininfo-box').load('mynote/note_class_new.php?courseid='+getQueryString('courseid')+'&noteTitle='+decodeURI(request('noteTitle')));
			$('.maininfo-box').load('mynote/note_course_new.html?courseid='+getQueryString('courseid')+'&noteTitle='+decodeURI(request('noteTitle')));
		}
		else{
			$('.maininfo-box').load('mynote/note_class_new.php');
		}

		$('#note_class-son').show();
		//修改样式
		$('.kinds-son-a').removeClass('a-active');
		$('#note_class_new').addClass('a-active');
	}
	else if(getQueryString('note')==2){//个人笔记
		//取值
		$('.maininfo-box').load('mynote/note_personal_new.php');
		$('#note_personal-son').show();
		//修改样式
		$('.kinds-a ').removeClass('a-active');
		$('#note_personal').addClass('a-active');
		$('.kinds-son-a').removeClass('a-active');
		$('#note_personal_new').addClass('a-active');
	}
	else{
		$('.maininfo-box').load('mynote/note_class_record.php');
		$('#note_class-son').show();
	}
	/** end 笔记功能显示判断*/

	/** start URL参数乱码转换 */
	function request(paras) {
		var url = location.href;
		var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
		var paraObj = {}
		for (i = 0; j = paraString[i]; i++) {
			paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
		}
		var returnValue = paraObj[paras.toLowerCase()];
		if (typeof (returnValue) == "undefined") {
			return "";
		} else {
			//t=new String(returnValue.getBytes("ISO8859_1"),"UTF-8");
			//return t;
			return returnValue;
		}
	}
	/** end  url 参数乱码转换 */


	
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});
	
	/***我的笔记-课堂笔记***/
	$('#note_class').click(function() {        //课堂笔记-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('#note_class-son').show();
		$('.maininfo-box').load('mynote/note_class_record.php');	
		$('.kinds-son-a ').removeClass('a-active');
		$('#note_class_record').addClass('a-active');
	});
	
		$('#note_class_record').click(function() {   //课堂笔记的子标签-记录
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('mynote/note_class_record.php');
		});	
			
		$('#note_class_new').click(function() {   //课堂笔记的子标签-新建
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			// $('.maininfo-box').load('mynote/note_class_new.php');
			$('.maininfo-box').load('mynote/note_course_new.html');//改为iframe 加载
		});	
					
	/***我的笔记-课堂笔记 end***/
			
	/***我的笔记-个人笔记***/
	$('#note_personal').click(function() {        //个人笔记-标签动作
		$('.lockpage').show();
		$('.kinds-son-abox').hide();
		$('#note_personal-son').show();
		$('.maininfo-box').load('mynote/note_personal_record.php');	
		$('.kinds-son-a ').removeClass('a-active');
		$('#note_personal_record').addClass('a-active');
	});
	
		$('#note_personal_record').click(function() {   //个人笔记的子标签-记录
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('mynote/note_personal_record.php');
		});	
			
		$('#note_personal_new').click(function() {   //个人笔记的子标签-新建
			$('.lockpage').show();
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			// $('.maininfo-box').load('mynote/note_personal_new.php');
			$('.maininfo-box').load('mynote/note_personal_new.html');//改为iframe 加载
		});	
	/***我的笔记-个人笔记 end***/	
</script>

<style>
	.kinds {margin-bottom: 5px;}
	.maininfo-box {background-color: #FFFFFF; height: 560px;}
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
			<a id="note_class" class="kinds-a a-active" href="javascript:void(0)">课堂笔记</a>l
			<a id="note_personal" class="kinds-a" href="javascript:void(0)">个人笔记</a>
		</div>
		
		<div class="kinds-son">	
			<div class="left">
				<div id="note_class-son" class="kinds-son-abox">
					<a id="note_class_record"  class="kinds-son-a a-active" href="javascript:void(0)">记录</a>l
					<a id="note_class_new"  class="kinds-son-a" href="javascript:void(0)">新建</a>
				</div>
				<div id="note_personal-son" class="kinds-son-abox">
					<a id="note_personal_record" class="kinds-son-a a-active" href="javascript:void(0)">记录</a>l
					<a id="note_personal_new" class="kinds-son-a" href="javascript:void(0)">新建</a>
				</div>
			</div>						
		</div>
		
		<div class="maininfo-box">
						
		</div>
<!--我的考试页面-end-->
