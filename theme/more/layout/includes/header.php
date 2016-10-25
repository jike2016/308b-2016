<link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/theme/more/style/alertstyle.css" type="text/css">	<!--全局-->
<style>
	html,body {font-family: "微软雅黑";font-size: 14px;min-height:900px; background-color: #ffffff;padding-top: 0px}
	p {  margin: 0px; }
	.btn {
		font-family: '微软雅黑', sans-serif;
		display: inline-block;
		padding: 6px 12px;
		margin-bottom: 0;
		font-size: 14px;
		font-weight: normal;
		line-height: 1.42857143;
		text-align: center;
		white-space: nowrap;
		vertical-align: middle;
		-ms-touch-action: manipulation;
		touch-action: manipulation;
		cursor: pointer;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
		background-image: none;
		border-radius: 4px;
	}
	.r-box ul, ol {  padding: 0;  margin: 0px;  }
	.navRight a,.navRight a:hover {  color: #3E3E3E;  text-decoration: none; }
	.r-box .searchbtn,.r-box .search ,.r-box .dropdown-toggle{box-sizing: content-box}
	* {  list-style: none;  }
	button {margin: 0px; }

	/***改变导航条下拉菜单样式***/
	.dropdown-menu .divider {  border-bottom: 0px;  margin: 0px 1px; }
	.dropdown-menu>li>a {  padding: 10px 20px 10px 15px;  }
	/***改变导航条下拉菜单样式 @end***/

	/***改变登录菜单样式***/
	.usermenu .usertext{color: #777 !important;}
	/***改变登录菜单样式 @end***/

	/***导航条***/
	ul, ol {  padding: 0;  margin: 0px;  }
	nav {width: 100%; height: 100px; background-color: #FFFFFF;}
	nav .center {width: 1200px; margin: auto;height: 100px;float: inherit}
	nav .center .l-box{width: 18%;height: 100px;float: left;}
	nav .center .l-box img {margin-top: 12px;}
	nav .center .r-box{width: 82%;height: 100px;float: right;}
	nav .center .r-box .search {width: 200px; height: 26px; float: right; border-radius: 0px; margin-top: 20px;}
	nav .center .r-box .searchbtn {width: 20px; height: 26px; float: right; border-radius: 0px;margin-top: 20px;border-radius: 0px 5px 5px 0px;}
	nav .center .r-box .btn-group{ float: right; margin-top: 20px;}
	nav .center .r-box .dropdown-toggle{background-color: #ffffff;width: 40px; height: 26px; float: right; border-radius: 5px 0px 0px 5px; border-right: 0px;}
	nav .center .r-box .dropdown-toggle:hover {background-color: #F0F0F0}
	nav .center .r-box .dropdown-toggle span {margin-left: 5px;    margin-top: 0px;}
	.caret{ vertical-align: middle;}

	nav .center .r-box .a-box {float: right; width: 160px; height: 40px;padding-top: 20px;}
	nav .center .r-box .a-box a {font-size: 16px; color: #555; }
	/*nav .center .r-box .a-box a:hover {color: #0088FF;}*/
	.moodle-actionmenu[data-enhanced].show .menu>li {  display:inline-block;  }
	.moodle-actionmenu[data-enhanced].show .menu a {box-sizing: border-box; margin-left: 20px}

	nav .navRight {float: left;overflow: hidden; margin-top: 15px;}
	nav .navRight li {float: left;width: 70px;height: 50px;line-height: 50px;text-align: center;position: relative;margin: 0px 10px;}
	nav .navRight li a{font-size: 16px;text-decoration: none;}
	nav .navRight .active{border-bottom: 3px solid #00B0F7;}
	nav .navRight .active a {color: #0066FF;}
	nav .navRight li a:hover{color: #00B0F7;}
	nav .navRight li span {width: 5px;height: 5px;border-bottom: 1px solid #000000;border-left: 1px solid #000000;position: absolute;top: 15px;left: 63px;transform: rotate(-45deg);transition: all 1s;}

	.jsenabled .usermenu .moodle-actionmenu.show {  background-color: #d1dfee; }
	/***导航条 @end***/

	/* start 用户昵称*/
	.usermenu .moodle-actionmenu .toggle-display .userbutton .usertext {
		width: 80px;
		font-size: 14px;
		line-height:1.4em;
		text-align: left;
	}
	.title-p{
		font-size: 10px !important;
		position:relative;
		line-height:1.4em;
		/* 3 times the line-height to show 3 lines */
		height:2.8em;
		overflow:hidden;}
	.title-p::after {
		content:"...";
		font-weight:bold;
		position:absolute;
		bottom:0;
		right:0;
		padding:0 0px 1px 45px;
		background:url(<?php echo $CFG->wwwroot;?>/theme/more/img/ellipsis_bg.png) repeat-y;
	}
	/* end 用户昵称*/
</style>

<!--引入此文件时，要先引入一下文件(注意与以下文件的冲突！)-->
<!--<script src="--><?php //echo $CFG->wwwroot;?><!--/theme/more/js/bootstrap.min.js"></script><!--全局-->

<script>
	$(document).ready(function() {
		//start 导航条用户名称字数控制
		var num = $('.usertext').text();
		if(num.length > 10){
			$('.usertext').addClass('title-p');
		}
		//end

		$('.login ul li a').css("color","#000");
		$('.usertext').css("color","#000");
		$('#searchtype a').click(function() {
			$('#searchtypebtn').text($(this).text());
			$('#searchtypebtn').append('<span class="caret"></span>');
		});
		//start 网站搜索
		$('.dropdown-toggle').click(function(){
			if($('.search-box .dropdown-menu').hasClass('show'))
				$('.search-box .dropdown-menu').removeClass('show');
			else
				$('.search-box .dropdown-menu').addClass('show');
		});
		$('.search-box .dropdown-menu li').click(function(){
			$('.search-box .dropdown-toggle').text($(this).children('a').text());
			$('.search-box .dropdown-toggle').append('<span class="caret"></span>');
			$('.search-box .dropdown-menu').removeClass('show');
		});
		$("#search_btn").click(function(){
			var search_type = $("#searchtypebtn").text();
			var search_param = $("#search_param").val();
			switch(search_type){
				case '课程':
					window.open( "<?php echo $CFG->wwwroot;?>/course/mysearch.php?searchType=课程名&searchParam="+search_param);
					break;
				case '书籍':
					window.open( "<?php echo $CFG->wwwroot;?>/microread/bookroom/searchresult.php?searchType=标题&searchParam="+search_param);
					break;
				case '文档':
					window.open( "<?php echo $CFG->wwwroot;?>/microread/docroom/searchresult.php?searchType=标题&searchParam="+search_param);
					break;
				case '图片':
					window.open( "<?php echo $CFG->wwwroot;?>/microread/picroom/image-search.php?word="+search_param);
					break;
				/**START CX 百科20161019*/
				case '百科':
					window.open( "<?php echo $CFG->wwwroot;?>/dokuwiki/doku.php?do=search&id="+search_param);
					break;
				/**END*/
				default:
					break;
			}
		});
		//end 网站搜索

	});

	//start 回车事件
	document.onkeydown = function (e) {
		var theEvent = window.event || e;
		var code = theEvent.keyCode || theEvent.which;
		if ( $('#search_param').val() != '' && code == 13) {
			$("#search_btn").click();
		}
	}
	//end 回车事件
</script>

<nav>
	<div class="center">
		<div class="l-box">
			<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo1.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
		</div>
		<div class="r-box">
			<ul class="navRight">
				<li><a href="<?php echo $CFG->wwwroot;?>">首页</a></li>
				<li class="mod_course"><a href="<?php echo $CFG->wwwroot;?>/course/index.php">微课</a></li>
				<li class="mod_microread"><a href="<?php echo $CFG->wwwroot;?>/microread/">微阅</a></li>
				<li class="mod_zhibo"><a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">直播</a></li>
<!--				/**START CX 百科20161019*/-->
				<li class="mod_dokuwiki"><a href="<?php echo $CFG->wwwroot;?>/dokuwiki/">百科</a></li>
<!--				<li class="mod_privatecenter"><a href="#"></a></li>-->
<!--				/**END*/-->

			</ul>
			<div class="a-box">
				<?php echo $OUTPUT->user_menu(); ?>
			</div>
			<button class="btn btn-info searchbtn" id="search_btn" ><span class="glyphicon glyphicon-search"></span></button>
			<input class="form-control search" id="search_param" placeholder="请输入关键词..." />

			<!--下拉菜单-->
			<div class="btn-group">
				<button id="searchtypebtn" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">课程<span class="caret"></span></button>
				<ul id="searchtype" class="dropdown-menu">
					<li><a href="#">课程</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">文档</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">图片</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">书籍</a></li>
<!--					/**START CX 百科20161019*/-->
					<li role="separator" class="divider"></li>
					<li><a href="#">百科</a></li>
<!--					/**END*/-->
				</ul>
			</div>
			<!--下拉菜单 end-->
		</div>
	</div>
</nav>


<div class="clear"></div>
<div class="loginmask"></div>

<div id="loginalert"  style="margin-top:100px;">
	<div class="pd20 loginpd">
		<h3 style="padding:20px 0px 5px 0px"><i class="closealert fr"></i>
			<div class="clear"></div>
		</h3>
		<div class="loginwrap">
			<div class="loginh">
				<div class="fl">会员登录</div>
			</div>
			<h3>
				<span class="login_warning">用户名或密码错误</span>
				<div class="clear"></div>
			</h3>
			<div class="clear"></div>
			<form action="<?php echo $CFG->wwwroot;?>/login/index.php" method="post" id="login">
				<div class="logininput">
					<input style="height:50px" type="text" name="username" id="username" class="loginusername" value="用户名" />
					<input style="height:50px" type="text" class="loginuserpasswordt" value="密码" />
					<input style="height:50px; display:none" type="password" name="password" id="password" class="loginuserpasswordp" style="display:none" />
				</div>
				<div class="loginbtn">
					<div class="loginsubmit fl">
						<input type="submit" value="登录" style="margin:auto; height:50px;" />
					</div>
					<div class="fr1"><a href="#">忘记密码?</a><a href="<?php echo $CFG->wwwroot;?>/register/index.php">用户注册</a></div>
					<div class="clear"></div>
				</div>
			</form>
		</div>
	</div>
</div>
