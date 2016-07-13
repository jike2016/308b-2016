<?php

/**
 * 课程搜索结果页
 */
// Get the HTML for the settings bits.
$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);
// Set default (LTR) layout mark-up for a three column page.
$regionmainbox = 'span9';
$regionmain = 'span8 pull-right';
$sidepre = 'span4 desktop-first-column';
$sidepost = 'span3 pull-right';
// Reset layout mark-up for RTL languages.
if (right_to_left()) {
    $regionmainbox = 'span9 pull-right';
    $regionmain = 'span8';
    $sidepre = 'span4 pull-right';
    $sidepost = 'span3 desktop-first-column';
}
echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>

    <link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /><!-- 全局-->
    <link rel="stylesheet" href="../theme/more/style/coursesearch/classsearch.css" />

    <script src="../theme/more/js/jquery-1.11.3.min.js"></script><!--全局-->
    <script type="text/javascript" src="../theme/more/js/bootstrap.min.js" ></script>

    <script>
        //聊天室 START
        $(document).ready(function(){
            //适配不同大小偏移值
            var winW=$(window).width();
            var winH=$(window).height();
            var leftval = (winW-900)/2;
            var topval = (winH-600)/3;
            $('.chat-box').css({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
            //适配不同大小偏移值 end
            var chatbox=false;
            $('.elevator-weixin').click(function(){
                if(chatbox==false){
                    $('.chat-box1').append('<iframe src="../chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
                    chatbox=true;
                }
                $('.chat-box1').show();
            })
            $('#chat-close').click(function(){
                $('.chat-box1').hide();
                //alert("关闭的top: " +$('.chat-box').offset().top);
            })
            //聊天室 End
            //收藏按钮
            $('#collection-btn').click(function()
            {
                $.ajax({
                    url: "<?php echo $CFG->wwwroot;?>/privatecenter/mycollection/collectionpage.php",
                    data: {mytitle: document.title, myurl: window.location.href },
                    success: function(msg){
                        if(msg=='1'){
                            alert('收藏成功，可去个人中心查看')
                        }
                        else{
                            msg=='2' ? alert('您已经收藏过了，请去个人中心查看收藏结果') :alert('收藏失败');
                        }
                    }
                });
            });
            //点赞按钮
            $('#like-btn').click(function()
            {
                $.ajax({
                    url: "<?php echo $CFG->wwwroot;?>/like/courselike.php",
                    data: {mytitle: document.title, myurl: window.location.href },
                    success: function(msg){
                        // alert(msg);
                        if(msg=='1'){
                            alert('点赞成功')
                        }
                        else{
                            msg=='2' ? alert('你已经点赞了，不能再次点赞') :alert('点赞失败');
                        }
                    }
                });
            });
            //笔记20160314
            var note_personal = false
            $('#mynote-btn').click(function(){
                if(note_personal == false)
                {
                    $('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_personal.php" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
                    note_personal = true;
                }
                $('.chat-box2').show();
            })
            //笔记
            $('#chat-close2').click(function(){
                $('.chat-box2').hide();
            })
        });
    </script>

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

<!--<div style="margin-top:120px; width: 300px;height: 200px;background: red;"></div>-->

<div id="page" class="container-fluid" style="height: 1200px;">

    <?php echo $OUTPUT->full_header(); ?>

    <div id="page-content" class="row-fluid">
        <!--        <section id="region-main" class="span12">-->
        <section id="..." class="span12">
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();//renderer内容
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
    </div>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>

<!--底部导航条-->
<nav class="navstyle-bottom navbar-static-bottom"></nav>
<!--底部导航条 end-->

</body>
</html>