<script>
	$(document).ready(function(){
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
			var search_type = $("#search_type").text();
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
				default:
					break;
			}

		});

	});

	//回车事件
	document.onkeydown = function (e) {
		var theEvent = window.event || e;
		var code = theEvent.keyCode || theEvent.which;
		if ( $('#search_param').val() != '' && code == 13) {
			$("#search_btn").click();
		}
	}

</script>

<nav class="navstyle navbar-fixed-top">
	<div class="nav-main">
		<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
		<ul class="nav-main-li">
			<a href="<?php echo $CFG->wwwroot;?>">
				<li class="li-normol">首页</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/mod/forum/view.php?id=1">
				<li class="li-normol">微阅</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/course/index.php">
				<li class="li-normol">微课</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/privatecenter/index.php?class=zhibo">
				<li class="li-normol">直播</li>
			</a>
		</ul>
		<div class="usermenu-box">
			<?php echo $OUTPUT->user_menu(); ?>					
		</div>
		
		<div class="search-box">
					<div class="input-group">
				     	<div class="input-group-btn">
				        	<button type="button" id="search_type" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #000000;">课程<span class="caret"></span></button>
				        	<ul class="dropdown-menu">
				          		<li><a href="#">课程</a></li>
				          		<li role="separator" class="divider"></li>
				          		<li><a href="#">书籍</a></li>
				          		<li role="separator" class="divider"></li>
				          		<li><a href="#">文档</a></li>
				          		<li role="separator" class="divider"></li>
				          		<li><a href="#">图片</a></li>
				        	</ul>
				      	</div><!-- /btn-group -->
			      		<input type="text" id="search_param" class="form-control" >
			    	</div><!-- /input-group -->
			    	<button class="btn btn-default searchbtn" id="search_btn"><span class="glyphicon glyphicon-search"></span>&nbsp;搜索</button>
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
              		<div class="fr1"><a href="#">忘记密码?</a></div>
              		<div class="clear"></div>
            	</div>
          	</form>
		</div>
	</div>
</div>