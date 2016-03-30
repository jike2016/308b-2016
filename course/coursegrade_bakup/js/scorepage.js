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
		var starnum;
		var comment;
		starnum = $("#comment-star").children(".star-active").length;
		comment = $(this).parent().children(".form-control").val();
	});
});