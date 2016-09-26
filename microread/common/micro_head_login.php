<?php
//微阅》首页》登录导航栏
//require_once ("../../config.php");//不能重复引用！
global $USER;

?>

<script>
    $(document).ready(function() {
        $(".mouse").mouseover(function(){
            $(this).addClass("open");
            $(this).children('.nav-a').addClass("active");
        });
        $(".mouse").mouseout(function(){
            $(this).removeClass("open");
            $(this).children('.nav-a').removeClass("active");
        });
        $('.mouse .dropdown-toggle').click(function() {
            window.location.href="<?php echo $CFG->wwwroot; ?>/microread";
        });
    });
</script>
<style>
    .a-box .dropdown-menu {top: 16px;left: 0px; float: left;min-width: 60px;text-align: center; padding: 0px;}
    .a-box .dropdown-menu > li > a { color: #000000; float: inherit;height: 35px;padding-top: 5px; font-size: 16px;}
    .a-box .dropdown-menu > li {height: 35px;}
    .a-box .dropdown-menu .divider {height: 1px; margin: 0px;}
    .mouse {margin-left: 42px;}
    .mouse .nav-a {margin-left: 18px;}
</style>

<div class="header">
    <div class="header-center">
        <div class="a-box">
            <a class="nav-a frist"  href="<?php echo $CFG->wwwroot; ?>">首页</a>
					<span class="mouse dropdown">
						<a class="nav-a dropdown-toggle" href="" id="weiyue" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">微阅&nbsp;<span class="caret"></span></a>
						<ul class="dropdown-menu" aria-labelledby="weiyue">
				   <li><a href="<?php echo $CFG->wwwroot; ?>/microread/docroom/">文库</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo $CFG->wwwroot; ?>/microread/bookroom/">书库</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo $CFG->wwwroot; ?>/microread/picroom/">图库</a></li>
                        </ul>
					</span>
            <a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/course/index.php">微课</a>
            <a class="nav-a" href="<?php echo $CFG->wwwroot; ?>/privatecenter/index.php?class=zhibo">直播</a>
            <?php if($USER->id==0)echo '<a class="nav-a login" href="'.$CFG->wwwroot.'/login/index.php"><img src="'.$CFG->wwwroot.'/microread/img/denglu.png"></a>';?>
        </div>
        <?php

            if($USER->id!=0){
                require_once('user_dropdown_menu.php');

                $user_menu = '<div id="usermenu" class="dropdown">
                                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <a href="#" class="username">'.fullname($USER, true).'</a>
                                        <a href="#" class="userimg">'.$OUTPUT->user_picture($USER,array('link' => false,'visibletoscreenreaders' => false)).'</a>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
                $user_menu .= get_user_dropdown_menu();//获取用户导航条下拉菜单
                $user_menu .= '</ul>
                               </div>';
                echo $user_menu;
            };
        ?>
    </div>
</div>
