<style>
	/****右下角悬浮按钮*****/
	.elevator {
		position: fixed;
		right: 15px;
		bottom: 10px;
		z-index: 1030;
	}
	.elevator a {
		display: block;
		position: relative;
		margin: 1px 0;
		outline: none;
		height: 52px;
		width: 52px;
		-webkit-transition: background-position 0.15s;
		-moz-transition: background-position 0.15s;
		transition: background-position 0.15s;
		background: url(<?php $CFG->wwwroot?>/moodle/theme/more/pix/elevator.png) no-repeat;
	}
	.elevator .elevator-app {
		background-position: 0 -550px;
	}
	.elevator .elevator-app:hover {
		background-position: 0 -612px;
	}
	.elevator .elevator-weixin {
		background-position: 0 -860px;
	}
	.elevator .elevator-weixin:hover {
		background-position: 0 -922px;
	}
	.elevator .elevator-msg {
		background-position: 0 -426px;
	}
	.elevator .elevator-msg:hover {
		background-position: 0 -488px;
	}
	.elevator .elevator-top {
		background-position: 0 -674px;
	}
	.elevator .elevator-top:hover {
		background-position: 0 -736px;
	}
	.elevator .elevator-diaocha {
		background-position: 0 -798px;
	}
	.elevator .elevator-diaocha:hover {
		background-position: 0 -984px;
	}
	/****右下角悬浮按钮 @end*****/

	/** Start 弹出窗体 **/
	.chat-box .chat-head {  height: 28px; }
	.chat-box { height: 615px;}
	.iframestyle {  height: 575px;}
	/** end 弹出窗体 **/

</style>

<!--要在引用的页面中先引用jq-->
<!--<script src="../moodle/theme/more/js/jquery-1.11.3.min.js"></script>-->

<script>
	$(document).ready(function(){
		//聊天室 START 20160314
		//适配不同大小偏移值
		var winW=$(window).width();
		var winH=$(window).height();
		var leftval = (winW-900)/2;
		var topval = (winH-600)/3;
		$('.chat-box').css({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
		//适配不同大小偏移值 end
		var chatbox=false;
		$('.elevator-weixin').click(function(){
			if(chatbox==false){
				$('.chat-box1').append('<iframe src="<?php echo $CFG->wwwroot;?>/chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
				chatbox=true;
			}
			$('.chat-box1').show();
		})
		$('#chat-close').click(function(){
			$('.chat-box1').hide();
			//alert("关闭的top: " +$('.chat-box').offset().top);
		})
		//聊天室 End
		//收藏按钮
		$('#collection-btn').click(function()
		{
			$.ajax({
				url: "<?php echo $CFG->wwwroot;?>/privatecenter/mycollection/collectionpage.php",
				data: {mytitle: document.title, myurl: window.location.href },
				success: function(msg){
					if(msg=='1'){
						alert('收藏成功，可去个人中心查看')
					}
					else{
						msg=='2' ? alert('您已经收藏过了，请去个人中心查看收藏结果') :alert('收藏失败');
					}
				}
			});
		});
		//end 收藏按钮
		//点赞按钮
		$('#like-btn').click(function()
		{
			$.ajax({
				url: "<?php echo $CFG->wwwroot;?>/like/courselike.php",
				data: {mytitle: document.title, myurl: window.location.href },
				success: function(msg){
					// alert(msg);
					if(msg=='1'){
						alert('点赞成功')
					}
					else{
						msg=='2' ? alert('你已经点赞了，不能再次点赞') :alert('点赞失败');
					}
				}
			});
		});
		//end 点赞按钮

		//笔记
		var note_personal = false //个人笔记
		var class_personal = false //课程笔记
		$('#mynote-btn').click(function(){
			if(class_personal == false && document.getElementById("hiddencourseid") )
			{
				$('#note_name').text( '课程笔记') ;
				var courseid = $('#hiddencourseid').val();
				// alert(courseid);
				var coursefullname = $('#hiddencoursefullname').val();
				// alert(coursefullname);
				$('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_course.php?courseid='+courseid+'&noteTitle='+coursefullname+'" class="iframestylecourse" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
				class_personal = true;
				note_personal = true;//禁止个人笔记
			}
			else if(note_personal == false)
			{
				$('#note_name').text( '个人笔记');
				$('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_personal.php" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
				note_personal = true;
				class_personal = true;//禁止课程笔记
			}

			$('.chat-box2').show();

		});

		$('#chat-close2').click(function(){
			$('.chat-box2').hide();
		})


	});
</script>

<?php
if(isloggedin()){
	echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
					<a class="elevator-weixin" style="cursor:pointer"></a>
					<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
					<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
					<a class="elevator-top" href="#"></a>
					</div>';
}
else{
	echo '
					<div id="J_GotoTop" class="elevator">
					<a class="elevator-top" href="#"></a>
					</div>';
}
?>

<div class="chat-box chat-box1">
	<div class="chat-head">
		<p>聊天室</p>
		<p id="chat-close" class="close">x</p>
	</div>
</div>
<div class="chat-box chat-box2">
	<div class="chat-head">
		<p id="note_name" >笔记</p>
		<p id="chat-close2" class="close">x</p>
	</div>
</div>
<div class="mask"></div>
