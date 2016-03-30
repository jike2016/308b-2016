<script>

	$('.maininfo-box').load('mycollection/collection_class_record.php');
	$('#collection_class-son').show();
	
	$('.kinds-a ').click(function() {
		$('.kinds-a ').removeClass('a-active');
		$(this).addClass('a-active');
	});
	
	/***我的收藏***/
//	$('#collection_class').click(function() {        //课堂笔记-标签动作
//		$('.kinds-son-abox').hide();
//		$('#collection_class-son').show();
//		$('.maininfo-box').load('mycollection/collection_class_record.php');
//		$('.kinds-son-a ').removeClass('a-active');
//		$('#collection_class_record').addClass('a-active');
//	});
	
		$('#collection_class_record').click(function() {   //课堂笔记的子标签-记录
			$('.kinds-son-a ').removeClass('a-active');
			$(this).addClass('a-active');
			$('.maininfo-box').load('mycollection/collection_class_record.php');
		});	
			
//		$('#collection_class_new').click(function() {   //课堂笔记的子标签-新建
//			$('.kinds-son-a ').removeClass('a-active');
//			$(this).addClass('a-active');
//			$('.maininfo-box').load('mycollection/collection_class_new.php');
//		});
					
	/***我的笔记-课堂笔记 end***/
			
	/***我的笔记-个人笔记***/
//	$('#collection_personal').click(function() {        //个人笔记-标签动作
//		$('.kinds-son-abox').hide();
//		$('#collection_personal-son').show();
//		$('.maininfo-box').load('mycollection/collection_personal_record.php');
//		$('.kinds-son-a ').removeClass('a-active');
//		$('#collection_personal_record').addClass('a-active');
//	});
	
//		$('#collection_personal_record').click(function() {   //个人笔记的子标签-记录
//			$('.kinds-son-a ').removeClass('a-active');
//			$(this).addClass('a-active');
//			$('.maininfo-box').load('mycollection/collection_personal_record.php');
//		});
			
//		$('#collection_personal_new').click(function() {   //个人笔记的子标签-新建
//			$('.kinds-son-a ').removeClass('a-active');
//			$(this).addClass('a-active');
//			$('.maininfo-box').load('mycollection/collection_personal_new.php');
//		});
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
			<a id="collection_class" class="kinds-a a-active" href="javascript:void(0)">我的收藏</a>
<!--			<a id="collection_personal" class="kinds-a" href="javascript:void(0)">个人笔记</a>-->
		</div>
		
		<div class="kinds-son">	
			<div class="left">
				<div id="collection_class-son" class="kinds-son-abox">
					<a id="collection_class_record"  class="kinds-son-a a-active" href="javascript:void(0)">记录</a>
<!--					<a id="collection_class_new"  class="kinds-son-a" href="javascript:void(0)">新建</a>-->
				</div>
<!--				<div id="collection_personal-son" class="kinds-son-abox">-->
<!--					<a id="collection_personal_record" class="kinds-son-a a-active" href="javascript:void(0)">记录</a>l-->
<!--					<a id="collection_personal_new" class="kinds-son-a" href="javascript:void(0)">新建</a>-->
<!--				</div>-->
			</div>						
		</div>
		
		<div class="maininfo-box">
						
		</div>
<!--我的考试页面-end-->
