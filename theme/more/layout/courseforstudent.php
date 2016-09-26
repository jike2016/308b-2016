<?php
//用户登录后显示的页面不同于columns3.php
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>

	<link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
	<link rel="stylesheet" href="../theme/more/style/QQface.css" /><!-- 2016.3.25 毛英东 添加表情CSS -->

	<link rel="stylesheet" href="../theme/more/style/courseforstu/courseinfo.css" type="text/css" />

<style>
	.mycomment {width: 100%; height: 160px; margin-bottom: 20px; border-bottom: 1px solid #edf1f2;}
	.mycomment textarea {width: 100%; height:80px;}
	.mycomment button {float: right; width: 100px; height: 36px; margin: 15px 0px;}
	.paginationbox {width: 100%; height: 80px;}
	.paginationbox .pagination{margin:30px 26%;}
	.paginationbox .pagination a {color: #B30000;}
	.pagination > li > a:hover,.pagination > li > span:hover,.pagination > li > a:focus,.pagination > li > span:focus {color: #FFFFFF; background-color: #B30000;}
	#main {background-color:#f0f0f0;padding:0px;}
	.course-info-main {background-color:#ffffff;padding:40px 20px 100px 20px; margin-top:0px}
	.navstyle-bottom {margin-top:0px}
</style>
	<script src="../theme/more/js/jquery-1.11.3.min.js"></script>
	<script src="../theme/more/js/jquery.qqFace.js"></script><!-- 2016.3.25 毛英东 添加表情 -->
<!--	<script src="--><?php //echo $CFG->wwwroot;?><!--/theme/more/js/bootstrap.min.js"></script><!--全局-->
	<script>
		$(document).ready(function() {

			//导航条列表样式控制 start
			$('.navRight li').removeClass('active');
			$('.navRight .mod_course').addClass('active');
			//导航条列表样式控制 end

			$('.navbar-nav  .li-normol').click(function() {
				$('.navbar-nav  .li-normol').removeClass('li_active');
				$(this).addClass('li_active');
			});
				
			$('.chapter').children('h3').click(function() {
				if ($(this).parent('.chapter').hasClass('chapter-active')) 
				{
					$(this).parent('.chapter').removeClass('chapter-active');					
      			}
				else
				{
					$(this).parent('.chapter').addClass('chapter-active');
				}
			});
				
			$('#zhangjie').click(function() {
				$('#zhangjie').addClass('active');
				$('#pingjia').removeClass('active');
				$('.evaluation-list').hide();
				$('.mod-chapters').show();
			});	
			$('#pingjia').click(function() {
				$('#pingjia').addClass('active');
				$('#zhangjie').removeClass('active');
				$('.mod-chapters').hide();
				$('.evaluation-list').show();
			});

			/**start nlw20160824 发表评论修改成无刷新*/
			$('#comment-btn').click(function() {
				var mytext =$(this).parent().children('.form-control').val();
				var textmy = mytext;
				textmy = textmy.replace(/[\ |\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,"");
				var evaluation_layout = "course";
				if(textmy.length <= 10){
					alert('评论内容不能少于10个汉字');
				}
				else{
					$.ajax({
						type:"POST",
						url: "../comment/common/evaluation_my/mygetcomment.php",
						data: {
							mycomment: mytext,
							evaluation_layout:evaluation_layout,
							id: getQueryString('id')
						},
						dataType:'json',
						success: function(result){
							if(result.status == 1){

								$("#page-evaluation").prepend(result.data);
								$("#pingjia span").html(parseInt($("#pingjia span").text())+1);
								face();
								$('#commentBtn').text('发表评论').removeClass('disabled');
								$("#comment-text").val("");
							}
							else if(result.status == 2)
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
			/**end nlw20160824 发表评论修改成无刷新*/
			
		});
		function getQueryString(name) { 
			var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
			var r = window.location.search.substr(1).match(reg); 
			if (r != null) return unescape(r[2]); return null; 
		} 
	</script>
	<script>
		/**start nlw20160823 评论内容局部刷新*/
		var cur_page = 1;//当前页
		var total_num,page_total;//总记录条数，总页数
		function getEvaluation(page,obj){
			var currentpage = parseInt(page);
			var evaluation_layout = "course";
			var obj_1 = obj;
			$.ajax({
				type:"POST",
				url:"../comment/common/evaluation_my/getevaluation.php",
				data:{
					current_page: currentpage,
					evaluation_layout:evaluation_layout,
					id:getQueryString('id')
				},
				dataType:"json",
				success: function (result) {
					//清空占位符数据
					$("#page-evaluation").empty();
					//对总记录条数，每页条数，总页数赋值
					page_total = result.page_total;
					cur_page = currentpage;

					//填充占位符
					$("#page-evaluation").append(result.data);
					face();
				},
				complete: function () {
					getPageBar();//分页，页码条输出
				},
				error: function (result) {
					alert("数据异常，请检查是否json格式");
				}
			});
		}
		//分页
		function getPageBar(){
			$.ajax({
				type:"GET",
				url:"../comment/common/evaluation_my/pagebar.php",
				data:{
					cur_page:cur_page,
					page_total:page_total
				},
				dataType:"json",
				success: function (result) {
					$("#page-list").html(result.data);
				},
				error:function (result) {
					alert("数据异常，请检查是否json格式");
				}
			});
		}

		$(function () {
			getEvaluation(1);//初始化，默认第一页
			$("#page-list ").on('click','a' ,function () {//on向未来的元素添加事件处理器
				var page = $(this).attr("data-page");//获取当前页
				getEvaluation(page);
			});
		});
		/**end 评论内容局部刷新*/
	</script>
<!--</head>-->
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

<!--    --><?php //echo $OUTPUT->full_header(); ?>

				<?php
					//echo $OUTPUT->course_content_header();
					echo $OUTPUT->main_content();
					//echo $OUTPUT->course_content_footer();
				?>
                <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?><!--左边 -->

        <?php //echo $OUTPUT->blocks('side-post', $sidepost); ?><!--右边 -->

<?php echo $OUTPUT->standard_end_of_body_html() ?>

<div style="clear: both;"></div>
<!--底部导航条-->
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->

<!--右下角按钮-->
<?php require_once("includes/link_button.php"); ?>
<!--右下角按钮 end-->

<!--</div>-->
<!--<button id="collection-btn" class="btn btn-info">收藏当前页面</button>
<button id="like-btn" class="btn btn-info">喜欢</button>-->
<!-- 2016.3.25 毛英东 添加表情-->
<script>
	$(function(){
		$('.emotion').qqFace({
			id : 'facebox',
			assign:'comment-text',
			path:'../theme/more/img/arclist/'	//表情存放的路径
		});
	});
	function face() {
		$('.commentinfo').each(
			function () {
				var str = $(this).html();
				str = str.replace(/\[(微笑|撇嘴|色|发呆|流泪|害羞|闭嘴|睡|大哭|尴尬|发怒|调皮|呲牙|惊讶|难过|冷汗|抓狂|吐|偷笑|可爱|白眼|傲慢|饥饿|困|惊恐|流汗|憨笑|大兵|奋斗|咒骂|疑问|嘘|晕|折磨|衰|敲打|再见|擦汗|抠鼻|糗大了|坏笑|左哼哼|右哼哼|哈欠|鄙视|快哭了|委屈|阴险|亲亲|吓|可怜|拥抱|月亮|太阳|炸弹|骷髅|菜刀|猪头|西瓜|咖啡|饭|爱心|强|弱|握手|胜利|抱拳|勾引|OK|NO|玫瑰|凋谢|红唇|飞吻|示爱)\]/g, function (w, word) {
					return '<img src="../theme/more/img/arclist/' + em_obj[word] + '.gif" border="0" />';
				});
				$(this).html(str);
			}
		);
	}
</script>
<!-- end  2016.3.25 毛英东 添加表情 -->
</body>
</html>
