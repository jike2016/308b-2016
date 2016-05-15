<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>图库展示页</title>
		<link rel="stylesheet" href="css/bootstrap.css" />
		<link rel="stylesheet" href="css/image-show.css" />

		<script type="text/javascript" src="js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="js/imagesshow.js" ></script>
	</head>
	<body>
		<div class="wrap">
			<!-- 顶部导航 -->
			<nav class="navbar navbar-fixed-top">
				<div class="user-block dropdown">
					<button class="btn dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<a href="#" class="username">王大锤</a>
						<a href="#" class="userimg"><img src="images/user.jpg" /></a>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="#">个人中心</a></li>
					</ul>
				</div>
				<div class="search-box">
					<div class="search-top">
						<img src="images/logo.png" alt="" class="logoimg" />
						<form action="" method="get" id="search-form">
							<input type="text" name="word" id="search-word">
							<input type="submit" value="搜索" id="search-submit">
						</form>
					</div>
					<div class="search-relative">
						<div class="search-relative-left">相关搜索：</div>
						
						<ul class="list-unstyled">
							<li><a href="#">小清新花</a></li>
							<li><a href="#">小清新卡通</a></li>
							<li><a href="#">小清新美女</a></li>
						</ul>
					</div>
					
				</div>
			</nav>

			<!-- 主体 -->
			<div id="imageFlow" class="main">
				<div class="top">
				</div>
				<div class="bank">
					<a rel="images/user.jpg" title="Myselves" href="#">0</a>
					<a rel="images/b1.jpg" title="Myselves" href="#">10</a>
					<a rel="images/1.jpg" title="Myselves" href="#">1</a>
					<a rel="images/2.jpg" title="Discoveries" href="#">2</a>
					<a rel="images/3.jpg" title="Nothing" href="#">3</a>
					<a rel="images/4.jpg" title="New life" href="#">4</a>
					<a rel="images/5.jpg" title="Optimists" href="#">They don&#39;t know all the facts yet</a>
					<a rel="images/6.jpg" title="Empathy" href="#">Emotional intimacy</a>
					<a rel="images/7.jpg" title="Much work" href="#">...remains to be done before we can announce our total failure to make any progress</a>
					<a rel="images/8.jpg" title="System error" href="#">Errare Programma Est</a>
					<a rel="images/9.jpg" title="Nonexistance" href="#">There&#39;s no such thing</a>
					<a rel="images/10.jpg" title="Inside" href="#">I抦 now trapped, without hope of escape or rescue</a>
					<a rel="images/11.jpg" title="E-Slaves" href="#">The World is flat</a>
					<a rel="images/12.jpg" title="l0v3" href="#">1 l0v3 j00 - f0r3v3r</a>
					<a rel="images/13.jpg" title="T minus zero" href="#">111 111 111 x 111 111 111 = 12345678987654321</a>
					<a rel="images/14.jpg" title="The End" href="#">...has not been written yet</a> 
					<a rel="images/14.jpg" title="The End" href="#">...has not been written yet</a> 
					<a rel="images/14.jpg" title="The End" href="#">...has not been written yet</a> 
					<a rel="images/14.jpg" title="The End" href="#">...has not been written yet</a> 

				</div>
				<div class="text">
					<div class="title">Loading</div>
					<div class="legend">Please wait...</div>
				</div>
				<div class="scrollbar">
					<img class="track" src="images/track.jpg" alt="">
					<img class="arrow-left" src="images/sign_out.png" alt="">
					<img class="arrow-right" src="images/sign_in.png" alt="">
					<img class="bar" src="images/bar.jpg" alt=""> 
				</div>
			</div>
			<!-- 主体 end -->
			<!-- 顶部导航 end -->
		</div>
	</body>
</html>
