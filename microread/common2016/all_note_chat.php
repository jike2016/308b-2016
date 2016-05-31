<?php
//微阅》各页面右下角的链接:笔记、聊天、收藏、、、、

?>

<script>
    $(document).ready(function(){
        //聊天室 START 20160314
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
                $('.chat-box1').append('<iframe src="<?php echo $CFG->wwwroot;?>/chat" class="iframestyle" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
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


<!--右下角按钮-->
<?php
    if(isloggedin()){
        echo '
                        <div id="J_GotoTop" class="elevator">
                        <a class="elevator-msg" id="mynote-btn" style="cursor:pointer"></a>
                        <a class="elevator-weixin" style="cursor:pointer"></a>
                        <a class="elevator-app"  id="collection-btn" style="cursor:pointer"></a>
                        <a class="elevator-diaocha" id="like-btn" style="cursor:pointer"></a>
                        <a class="elevator-top" href="#"></a>
                        </div>';
    }
    else{
        echo '
                        <div id="J_GotoTop" class="elevator">
                        <a class="elevator-top" href="#"></a>
                        </div>';
    }
?>
<div class="chat-box chat-box1">
    <div class="chat-head">
        <p>聊天室</p>
        <p id="chat-close" class="close">x</p>
    </div>
</div>
<div class="chat-box chat-box2">
    <div class="chat-head">
        <p>个人笔记</p>
        <p id="chat-close2" class="close">x</p>
    </div>
</div>
<!--右下角按钮 end-->


