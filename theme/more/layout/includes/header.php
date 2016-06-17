<nav class="navstyle navbar-fixed-top">
	<div class="nav-main">
		<img id="logo" src="<?php echo $CFG->wwwroot;?>/theme/more/pix/Home_Logo.png" onMouseOver="this.style.cursor='pointer'" onClick="document.location='<?php echo $CFG->wwwroot;?>';">
		<ul class="nav-main-li">
			<a href="<?php echo $CFG->wwwroot;?>">
				<li class="li-normol">首页</li>
			</a>
			<a href="<?php echo $CFG->wwwroot;?>/microread/">
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