<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Moodle's Clean theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_clean
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../../theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="../../theme/more/style/navstyle.css" /><!-- 全局-->
    <link rel="stylesheet" href="../../theme/more/style/lessonvideo/lessonvideo.css" />
	<link rel="stylesheet" href="../../theme/more/style/QQface.css" /><!-- 2016.3.25 毛英东 添加表情CSS -->
    <script src="../../theme/more/js/jquery-1.11.3.min.js"></script>
	<script src="../../theme/more/js/jquery.qqFace.js"></script><!-- 2016.3.25 毛英东 添加表情 -->
    <script>
        $(document).ready(function() {
			//聊天室 START
			//适配不同大小偏移值
			var winW=$(window).width();
			var winH=$(window).height();
			var leftval = (winW-900)/2;	
			var topval = (winH-600)/3;	
			$('.chat-box').offset({top:topval,left:leftval}); //该方法是在控件原有基础上加上定义的值，所以初始属性最好定义为0px
			//适配不同大小偏移值 end	
			
			$('.elevator-weixin').click(function(){
											
				$('.chat-box').show();	
			})
			$('#chat-close').click(function(){
				$('.chat-box').hide();
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
			
            $('#commentBtn').click(function() {
                var mytext =$(this).parent().children('.form-control').val();
				var textmy = mytext;
				textmy = textmy.replace(/[\ |\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,"");
				if(textmy.length <= 10){
					alert('评论内容不能少于10个汉字');
				}
                else{
                    $.ajax({
                        url: "../../mod/lesson/mygetvideocomment.php",
                        data: { mycomment: mytext, modid: getQueryString('id') },
                        success: function(msg){
                            if(msg=='1'){
                                // location.reload();
                                window.location.href=window.location.href+'&page=1';
                            }
							else if(msg=='2')
							{
								alert('评论失败，评论内容重复！')
							}
							else {
								alert('评论失败，一分钟內只能评论一次！')
							}
                        }
                    });
                }
            });
			//笔记20160314
			var class_personal = false
			$('#mynote-btn').click(function(){
				if(class_personal == false)
				{
					var courseid = $('#hiddencourseid').val();
					// alert(courseid);
					var notetitle = $('#hiddennotetitle').val();
					// alert(coursefullname);
					$('.chat-box2').append('<iframe src="<?php echo $CFG->wwwroot;?>/mod/notemy/newnotemy_course.php?courseid='+courseid+'&noteTitle='+notetitle+'" class="iframestylecourse" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="yes"></iframe>');
					class_personal = true;
				}
								
				$('.chat-box2').show();	
				
			})
			//笔记
			$('#chat-close2').click(function(){
				$('.chat-box2').hide();
			})		

        });

		/** START 视频列表弹出 陈振安*/
		$(function(){
			$(".courselist").mouseenter(function(){
				$(this).find('.courselist-content').stop().slideDown();
			}).mouseleave(function(){
				$(this).find('.courselist-content').stop().slideUp();
			});
			$('.ckplayer').parent().nextAll('br,p:empty').remove();
		});
		/** END 视频列表弹出 陈振安*/

        function getQueryString(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) return unescape(r[2]);
            return null;
        }
    </script>
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

    
<div id="page" class="container-fluid">
    <?php echo $OUTPUT->full_header(); ?>
    <div id="page-content" class="row-fluid">
        <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
            <div class="row-fluid">
<!--                <section id="region-main" class="--><?php //echo $regionmain; ?><!--">	<!-- 注释，使得页面居中 -->
               <!--   <section id="region-main"  style="margin-left: auto;margin-right: auto; width: 74.35897436%;">-->
			    <section  style="margin-left: auto;margin-right: auto;  min-height:760px;">
                    <?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    ?>
                </section>
<!--                --><?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?> <!-- 左部板块-->
            </div>
        </div>
        <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
    </div>

<!--    <footer id="page-footer">-->
<!--        <div id="course-footer">--><?php //echo $OUTPUT->course_footer(); ?><!--</div>-->
<!--        <p class="helplink">--><?php //echo $OUTPUT->page_doc_link(); ?><!--</p>-->
<!--        --><?php
//        echo $html->footnote;
//        echo $OUTPUT->login_info();
//        echo $OUTPUT->home_link();
//        echo $OUTPUT->standard_footer_html();
//        ?>
<!--    </footer>-->

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
<!--底部导航条-->
	<nav class="navstyle-bottom navbar-static-bottom"></nav>
	<!--底部导航条 end-->
<!-- 2016.3.25 毛英东 添加表情-->
<script>
	$(function(){
		$('.emotion').qqFace({
			id : 'facebox',
			assign:'comment-text',
			path:'../../theme/more/img/arclist/'	//表情存放的路径
		});
	});
	$('.commentinfo').each(
		function(){
			var str = $(this).html();
			str = str.replace(/\[(微笑|撇嘴|色|发呆|流泪|害羞|闭嘴|睡|大哭|尴尬|发怒|调皮|呲牙|惊讶|难过|冷汗|抓狂|吐|偷笑|可爱|白眼|傲慢|饥饿|困|惊恐|流汗|憨笑|大兵|奋斗|咒骂|疑问|嘘|晕|折磨|衰|敲打|再见|擦汗|抠鼻|糗大了|坏笑|左哼哼|右哼哼|哈欠|鄙视|快哭了|委屈|阴险|亲亲|吓|可怜|拥抱|月亮|太阳|炸弹|骷髅|菜刀|猪头|西瓜|咖啡|饭|爱心|强|弱|握手|胜利|抱拳|勾引|OK|NO|玫瑰|凋谢|红唇|飞吻|示爱)\]/g, function(w,word){
				return '<img src="../../theme/more/img/arclist/'+ em_obj[word] + '.gif" border="0" />';
			});
			$(this).html(str);
		}
	);
</script>
<!-- end  2016.3.25 毛英东 添加表情 -->
</body>
</html>
