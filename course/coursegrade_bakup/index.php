<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
		<title>
			课程评分页面
		</title>
		<link rel="stylesheet" href="css/bootstrap.css" type="text/css"/>		
		<link rel="stylesheet" href="css/scorepage.css" type="text/css">
		<link rel="stylesheet" href="css/scorepagebanner.css" type="text/css">		
		<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="js/scorepage.js"></script>
	</head>

	<body>				
		<div id="main">
			<div class="course-infos" >
				<div class="w pr">
					
					<div class="banner-left">
						<img src="img/1.jpg" />
					</div>
					
					<div class="banner-right">
						<div class="path">
							<a href="#">课程</a>
							<i class="path-split">\</i><a href="#">前端开发</a>
							<i class="path-split">\</i><a href="#">HTML/CSS</a>
							<i class="path-split">\</i><span>HTML+CSS基础课程</span>
						</div>
						
						<div class="hd">
							<h2 class="l">HTML+CSS基础课程</h2>
						</div>
						
						<div class="learnernum">						
							<p class="p1">218056</p>
							<p class="p2">学习人数</p>
						</div>
					</div>											
				</div>
			</div>
			
			<div class="course-info-main clearfix w has-progress">
				<div class="content-wrap clearfix">				
					<div class="content">
						<div class="scoreinfo">
							<p>满意度评分</p>
							<p id="comment-star" class="star-box">
								<span id="star1" class="glyphicon glyphicon-star"></span>
								<span id="star2" class="glyphicon glyphicon-star"></span>
								<span id="star3" class="glyphicon glyphicon-star"></span>
								<span id="star4" class="glyphicon glyphicon-star"></span>
								<span id="star5" class="glyphicon glyphicon-star"></span>
							</p>
						</div>
						<div class="evaluation-list" >													
							<div class="mycomment">
								<textarea class="form-control" placeholder="扯淡、吐槽、想说啥说啥..."></textarea>
								<button id="score-btn" class="btn btn-info">发表评论</button>
							</div>
							<!--evaluation-info end-->
							<div class="evaluation">
								<div class="evaluation-con">
									<a href="#" class="img-box">
										<img src="img/533e564d0001308602000200-100-100.jpg" alt="anananan007">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="#" class="username">anananan007</a>
										</div>
										<!--user-info end-->
										<p class="content"></p>
										<div class="info">
											<span class="time">时间：22分钟前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->																					
														
																					
							<div class="evaluation">
								<div class="evaluation-con">
									<a href="#" class="img-box">
										<img src="img/56174ac90001043e01000100-100-100.jpg" alt="_No作NoDie_0">
									</a>
									<div class="content-box">
										<div class="user-info clearfix">
											<a href="#" class="username">_No作NoDie_0</a>
										</div>
										<!--user-info end-->
										<p class="content">就是视频少了一点，其他完美</p>
										<div class="info">
											<span class="time">时间：1天前</span>
										</div>
									</div>
									<!--content end-->
								</div>
								<!--evaluation-con end-->

							</div>
							<!--evaluation end-->
							<div class="paginationbox">
								<ul class="pagination">
									<li>
								      <a href="#">首页</a>
								    </li>
								    <li>
								      <a href="#">上一页</a>
								    </li>
								    <li><a href="#">1</a></li>
								    <li><a href="#">2</a></li>
								    <li><a href="#">3</a></li>
								    <li><a href="#">4</a></li>
								    <li><a href="#">5</a></li>
								    <li>
								      <a href="#">下一页</a>
								    </li>
								    <li>
								      <a href="#">尾页</a>
								    </li>
								</ul>
							</div>
						</div>
						<!--evaluation-list end-->
						
					</div>
					<!--content end-->
				
					<div class="aside r">
						<div class="bd">							
							<div class="box mb40">
								<div class="score-box">
									<p class="score-title">满意度评分</p>
									<p class="score-num" >9.9</p>
									<div class="star-box">
										<p>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
											<span class="glyphicon glyphicon-star"></span>
										</p>
									</div>									
								</div>								
							</div>

						</div>
												
					</div>
				</div>

				<div class="clear"></div>

			</div>

		</div>

		<div id="footer">
			<div class="waper">
				<div class="footerwaper clearfix">
					<div class="followus r">
					</div>
					<div class="footer_intro l">
						<div class="footer_link">
							<ul>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" href="#" target="_blank" id="feedBack"></a>
				<div class="elevator-app-box">
				</div>
			</a>
		</div>

		<div class="mask"></div>

	</body>

</html>