$(document).ready(function() {
				$('.navbar-nav  .li-normol').click(function() {
					$('.navbar-nav  .li-normol').removeClass('li_active');
					$(this).addClass('li_active');
				});
				$('.direction .course-nav-item').click(function() {
					$('.direction .course-nav-item').removeClass('on');
					$(this).addClass('on');
				});
				$('.kinds .course-nav-item').click(function() {
					$('.kinds .course-nav-item').removeClass('on');
					$(this).addClass('on');
				});
				$('.others .course-nav-item').click(function() {
					$('.others .course-nav-item').removeClass('on');
					$(this).addClass('on');
				});
				$('.sort-item').click(function() {
					$('.sort-item').removeClass('active');
					$(this).addClass('active');
				});
			});