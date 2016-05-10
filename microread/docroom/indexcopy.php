<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>文库首页</title>
		<link rel="stylesheet" href="../css/bootstrap.css" />
		<link rel="stylesheet" href="../css/docallpage.css" />
		<link rel="stylesheet" href="../css/docindex.css" />

		<script type="text/javascript" src="../js/jquery-1.11.3.min.js" ></script>
		<script type="text/javascript" src="../js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="../js/rank.js" ></script>
		<script>
		var moothrank = new Array(10); //月书单
		moothrank[0] = "1";
		moothrank[1] = "1";
		moothrank[2] = "1";
		moothrank[3] = "1";
		moothrank[4] = "1";
		moothrank[5] = "1";
		moothrank[6] = "1";
		moothrank[7] = "1";
		moothrank[8] = "1";
		moothrank[9] = "1";

		var moothrank_href = new Array(10); //月书单链接
		moothrank_href[0] = "http://www.baidu.com";
		moothrank_href[1] = "http://www.baidu1.com";
		moothrank_href[2] = "http://www.baidu.com";
		moothrank_href[3] = "http://www.baidu.com";
		moothrank_href[4] = "http://www.baidu.com";
		moothrank_href[5] = "http://www.baidu.com";
		moothrank_href[6] = "http://www.baidu.com";
		moothrank_href[7] = "http://www.baidu.com";
		moothrank_href[8] = "http://www.baidu.com";
		moothrank_href[9] = "http://www.baidu.com";

		var weekrank = new Array(10); //周书单
		weekrank[0] = "2";
		weekrank[1] = "2";
		weekrank[2] = "2";
		weekrank[3] = "2";
		weekrank[4] = "2";
		weekrank[5] = "2";
		weekrank[6] = "2";
		weekrank[7] = "2";
		weekrank[8] = "2";
		weekrank[9] = "2";

		var weekrank_href = new Array(10); //周书单链接
		weekrank_href[0] = "http://www.baidu.com";
		weekrank_href[1] = "http://www.baidu.com";
		weekrank_href[2] = "2";
		weekrank_href[3] = "2";
		weekrank_href[4] = "2";
		weekrank_href[5] = "2";
		weekrank_href[6] = "2";
		weekrank_href[7] = "2";
		weekrank_href[8] = "2";
		weekrank_href[9] = "2";


		var totalrank = new Array(10); //总书单
		totalrank[0] = "3";
		totalrank[1] = "3";
		totalrank[2] = "3";
		totalrank[3] = "3";
		totalrank[4] = "3";
		totalrank[5] = "3";
		totalrank[6] = "3";
		totalrank[7] = "3";
		totalrank[8] = "3";
		totalrank[9] = "3";

		var totalrank_href = new Array(10); //总书单
		totalrank_href[0] = "3";
		totalrank_href[1] = "3";
		totalrank_href[2] = "3";
		totalrank_href[3] = "3";
		totalrank_href[4] = "3";
		totalrank_href[5] = "3";
		totalrank_href[6] = "3";
		totalrank_href[7] = "3";
		totalrank_href[8] = "3";
		totalrank_href[9] = "3";
		</script>
	</head>
	<body id="articleindex">
		<!--顶部导航-->
		<div class="header">
			<div class="header-center">
				<div class="a-box">
					<a class="nav-a frist" href="#">首页</a>
					<a class="nav-a" href="#">微阅</a>
					<a class="nav-a" href="#">微课</a>
					<a class="nav-a" href="#">直播</a>
					<!--a class="nav-a login" href="#"><img src="img/denglu.png"</a-->
				</div>

				<div id="usermenu" class="dropdown">
				  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				    <a href="#" class="username">王大锤</a>
					<a href="#" class="userimg"><img src="../img/user.jpg" style="width: 40px;"></a>
				  </button>
				  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				    <li><a href="#">个人中心</a></li>
				    <li role="separator" class="divider"></li>
				    <li><a href="#">台账</a></li>
				    <li role="separator" class="divider"></li>
				    <li><a href="#">Something</a></li>
				     <li role="separator" class="divider"></li>
				    <li><a href="#">Separated</a></li>
				  </ul>
				</div>
			</div>
		</div>
		
		<div class="header-banner">
			<img  src="../img/logo_WenKu.png"/>
			<!--搜索框组-->
			<div class="search-box">
				<div class="input-group">
			     	<div class="input-group-btn">
			        	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">图书&nbsp;<span class="caret"></span></button>
			        	<ul class="dropdown-menu">
			          		<li><a href="#">图书</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a href="#">文献</a></li>
			          		<li role="separator" class="divider"></li>
			          		<li><a href="#">论文</a></li>
			        	</ul>
			      	</div><!-- /btn-group -->
			      	<input type="text" class="form-control" >
			    </div><!-- /input-group -->
			    <button class="btn btn-default searchbtn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
			    
			    <div class="radio">
			    	<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		全部
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1">
			    		DOC
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
			    		PPT
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		TXT
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		PDF
			  		</label>
			  		<label>
			    		<input type="radio" name="optionsRadios" id="optionsRadios3" value="option3">
			    		XLS
			  		</label>
				</div>
			    
			</div>
			<!--搜索框组 end-->
		</div>
		<!--顶部导航 end-->
		
		<!--书本分类-->
		<div class="bookclassified">
			<div class="bookclassified-center">
				
				<!-- 书本分类按钮 -->
				<div class="btn-group" style="float: left;">
				  	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  		<img src="../img/tushuFenlei.png">
				  	</a>
				  	<ul class="dropdown-menu">
				    	<li><a href="#">现代</a></li>
				    	<li role="separator" class="divider"></li>
				    	<li><a href="#">军事</a></li>
				    	<li role="separator" class="divider"></li>
				    	<li><a href="#">战争</a></li>
				    	<li role="separator" class="divider"></li>
				    	<li><a href="#">科技</a></li>
				  	</ul>
				</div>
				<!-- 书本分类按钮 end-->
				
				<div class="line"></div>
				<a href="#" class="kinds">每日新书</a>
				<div class="line"></div>
				<a href="#" class="kinds">7日抢读</a>
				<div class="line"></div>
				<a href="#" ><img src="../img/VIP.png"></a>
				<div class="line"></div>
				<a href="#" class="kinds">客户端下载</a>
			</div>
		</div>
		<!--书本分类 end-->
		
		<!--页面主体-->
		<div class="main">
			<!--权威发布-->
			<div class="authority-release">
				<p class="title">权威发布></p>
					
				<!--第一行-->
				<div class="banner">
					
					<div class="articleblock frist ">
						<img src="../img/1.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我但是</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/2.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/3.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/1.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>	
						
					<div style="clear: both;"></div>
				</div>
				<!--第一行 end-->
				<p class="title">权威发布></p>	
				<!--第二行-->
				<div class="banner">
					<div class="articleblock frist ">
						<img src="../img/1.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我但是</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/2.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/3.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>
					<div class="articleblock">
						<img src="../img/1.jpg" />
						<div>
							<a href="#">公积金拉开发送发送</a>
							<p>但是发送方算法是发送方是算法是发送方色温我</p>
						</div>
					</div>	
				</div>	
				<!--第二行 end-->
				<div style="clear: both;"></div>
			</div>
			<!--权威发布 end-->
			
			<!--贡献作者推荐榜-->
			<div class="contribution-writer">
				<p class="title">贡献作者推荐榜></p>
				<!--第一行-->
				<div class="writerblock frist">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-doc"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-xls"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-ppt"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-pdf"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#"><span class="ic ic-txt"></span>&nbsp;高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				<!--第一行 end-->
				
				<!--第二行-->
				<div class="writerblock frist">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				
				<div class="writerblock">
					<div class="userinfo-box">
						<div class="userimg">
							<img src="../img/user.jpg" />
						</div>
						<div class="userinfo">
							<p class="name">孙敏</p>
							<p class="articlenum">333</p>
							<p class="articlenumw">篇文档</p>
						</div>
					</div>
					
					<div class="articlelist">
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
						<a href="#">
							<p class="pa">
								<a class="ca" href="#">高二是开发商来付款高二是开发商来付款</a>
								<span class="score">4.3分</span>
							</p>
						</a>
					</div>
				</div>
				<!--第二行 end-->
			</div>
			<!--贡献作者推荐榜 end-->
			
			
			<div style="clear: both;"></div>
			
			<!--热门作者榜、评分榜、热门排行榜-->
			<div class="rank-box">
				<!--热门作者榜-->
				<div class="popular-writer">
					<div class="title">热门贡献榜</div>
					<div class="ranklist">
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">1</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">2</a></div>
							<div class="w-name"><a class="writername">张&nbsp;&nbsp;&nbsp;华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top3">3</a></div>
							<div class="w-name"><a class="writername">李子红</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">4</a></div>
							<div class="w-name"><a class="writername">张大炮</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">5</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">6</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">7</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">8</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum">9</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
						<div class="ranklist-block">
							<div class="w-num"><a class="ranknum top10">10</a></div>
							<div class="w-name"><a class="writername">张建华</a></div>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门作者榜 end-->
				
				<!--评分榜-->
				<div class="score">
					<div class="title">评分榜</div>
					<div class="ranklist">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname">辛亥革命在甘肃</a>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--评分榜 end-->
				
				<!--热门排行榜-->
				<div class="top-charts">
					<div class="title hotrank">热门排行榜</div>
					<div class="title week">周</div>
					<div class="title mooth">月</div>					
					<div class="title total">总</div>
					<div style="clear: both;"></div>
					<div class="ranklist final">
						<div class="ranklist-block">
							<a class="ranknum top3">1</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">2</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top3">3</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">4</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">5</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">6</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">7</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">8</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum">9</a>
							<a class="bookname"></a>
						</div>
						<div class="ranklist-block">
							<a class="ranknum top10">10</a>
							<a class="bookname"></a>
						</div>
					</div>
					<div class="more-box"><a class="more" href="#">更多>></a></div>
				</div>
				<!--热门排行榜 end-->
			</div>
			<!--热门作者榜、评分榜、热门排行榜 end-->			
		</div>
		<!--页面主体 end-->
		
		<!--右下角按钮-->
		<div id="J_GotoTop" class="elevator">
			<a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
			<a class="elevator-weixin" style="cursor:pointer"></a>
			<a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
			<a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
			<a class="elevator-top" href="#"></a>
		</div>
		<!--右下角按钮 end-->
		
		<div style="clear: both;"></div>		
		<!--底部导航条-->
		<nav class="bottomnav">
			<div class="whiteline"></div>
			<p>
				<a>电子书友链</a>
				<a>QQ：54250413230</a>
				<a>版权声明</a>
				<a>意见反馈</a>
				<a>客服电话（0771-536780）</a>
			</p>
			<p>
				<a>单位编号：1101081827</a>
				<a>防城慕课网</a>
				<a>桂ICP证：060172号</a>
				<a>网络视听许可证：0110438号</a>
			</p>
			<p>Copyright &nbsp;1999-2012&nbsp;&nbsp;防城慕课</p>
		</nav>
		<!--底部导航条 end-->
	</body>
</html>
