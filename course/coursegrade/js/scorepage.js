$(document).ready(function() {
	$('.navbar-nav  .li-normol').click(function() {
		$('.navbar-nav  .li-normol').removeClass('li_active');
		$(this).addClass('li_active');
	});

	$('#star1').mouseover(function(event) {
		$('#star1').addClass('star-active');
		$('#star2').removeClass('star-active');
		$('#star3').removeClass('star-active');
		$('#star4').removeClass('star-active');
		$('#star5').removeClass('star-active');
	});

	$('#star2').mouseover(function(event) {
		$('#star1').addClass('star-active');
		$('#star2').addClass('star-active');
		$('#star3').removeClass('star-active');
		$('#star4').removeClass('star-active');
		$('#star5').removeClass('star-active');
	});
	$('#star3').mouseover(function(event) {
		$('#star1').addClass('star-active');
		$('#star2').addClass('star-active');
		$('#star3').addClass('star-active');
		$('#star4').removeClass('star-active');
		$('#star5').removeClass('star-active');
	});
	$('#star4').mouseover(function(event) {
		$('#star1').addClass('star-active');
		$('#star2').addClass('star-active');
		$('#star3').addClass('star-active');
		$('#star4').addClass('star-active');
		$('#star5').removeClass('star-active');
	});
	$('#star5').mouseover(function(event) {
		$('#star1').addClass('star-active');
		$('#star2').addClass('star-active');
		$('#star3').addClass('star-active');
		$('#star4').addClass('star-active');
		$('#star5').addClass('star-active');
	});
	
	$('#score-btn').click(function() {
		var mystarnum = 0.0;
		var mycomment;
		mystarnum = $("#comment-star").children(".star-active").length;
		mystarnum > 0 ? (mystarnum = mystarnum * 2.0) : 10.0;
		mycomment = $(this).parent().children(".form-control").val();
		if(mycomment == ""){
			alert('请输入评论内容');
		}
		else{
			$.ajax({
				url: "../../course/coursegrade/myscorecoursecomment.php",
				data: { mycomment: mycomment, mystarnum: mystarnum, courseid: getQueryString('id') },
				success: function(msg){
					if(msg=='1'){
						location.reload();
					}
					else{
						// alert(msg);
						msg=='2'?alert('评论失败,一个用户只能对一门课程评分一次'):alert('评论失败');
					}
				}
			});
		}
	});
});

function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return null;
}