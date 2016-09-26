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

/** START 获取上传在线阅读 朱子武 20160509*/
function get_office_online_url($page_id = 0)
{
	global $DB;
	unset($CFG);
	global $CFG;
	require_once($CFG->dirroot.'/config.php');
	require_once($CFG->dirroot.'/mod/page/locallib.php');
	require_once($CFG->libdir.'/completionlib.php');

	$p       = optional_param('p', 0, PARAM_INT);  // Page instance ID

	if ($p) {
		if (!$page = $DB->get_record('page', array('id'=>$p))) {
			print_error('invalidaccessparameter');
		}
		$cm = get_coursemodule_from_instance('page', $page->id, $page->course, false, MUST_EXIST);

	} else {
		if (!$cm = get_coursemodule_from_id('page', $page_id)) {
			print_error('invalidcoursemodule');
		}
		$page = $DB->get_record('page', array('id'=>$cm->instance), '*', MUST_EXIST);
	}
	return $page->swfurl;
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>
    
    <link rel="stylesheet" href="../../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
    <link rel="stylesheet" href="../../theme/more/style/navstyle.css" /> <!--全局-->
	<link rel="stylesheet" href="../../theme/more/style/QQface.css" /><!-- 2016.3.29 毛英东 添加表情CSS -->
	<link rel="stylesheet" href="../../theme/more/style/articlecomment/articlecomment.css" /><!-- Start 添加css样式 朱子武 20160315-->
	<link rel="stylesheet" href="../../theme/more/style/flexpaper/flexpaper.css" /><!-- 2016.4.25 岑霄 在线阅读office -->
	<link rel="stylesheet" href="../../theme/more/style/articlecomment/document.css"  type="text/css"/>
	
	<!-- End 添加css样式 朱子武 20160315-->
	<script src="../../theme/more/js/jquery-1.11.3.min.js"></script>
	<script src="../../theme/more/js/jquery.qqFace.js"></script><!-- 2016.3.29 毛英东 添加表情 -->
	
	<script src="../../theme/more/js/flexpaper/flexpaper.js"></script><!-- 2016.4.25 岑霄 在线阅读office -->
	<script src="../../theme/more/js/flexpaper/flexpaper_handlers.js"></script><!-- 2016.4.25 岑霄 在线阅读office -->
    
	<style>
		.navbar-form {
		padding: 10px 0px; }
		.bd {  width: 100%;  height: 40px;  background-color: #F0F0F0;  }
		.row-fluid {min-height: 800px}
		#region-main {background-color: #ffffff;
			border: 0px;
			-webkit-border-radius: 0px;
			-moz-border-radius: 0px;
			border-radius: 0px;
			-webkit-box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0.05);
			-moz-box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0.05);
			 box-shadow: inset 0 0px 0px rgba(0, 0, 0, 0.05);
	</style>

<script>
	$(document).ready(function() {
		//导航条列表样式控制 start
		$('.navRight li').removeClass('active');
		$('.navRight .mod_course').addClass('active');
		//导航条列表样式控制 end
	});
</script>
<script>
$(document).ready(function(){
	/** Start 添加评论按钮点击事件 nlw */
	$('#commentBtn').click(function() {
		var mytext =$(this).parent().children('.form-control').val();
		var textmy = mytext;
		textmy = textmy.replace(/[\ |\~|\`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\-|\_|\+|\=|\||\\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,"");
		var evaluation_layout = "page";
		if(textmy.length <= 10){
			alert('评论内容不能少于10个汉字');
		}
		else{
			$.ajax({
				type:"POST",
				url: "../../comment/common/evaluation_my/mygetcomment.php",
				data: {
					mycomment: mytext,
					evaluation_layout:evaluation_layout,
					id: getQueryString('id')
				},
				dataType:'json',
				success: function(result){
					if(result.status == 1){
						// location.reload();
//							window.location.href=window.location.href+'&page=1';
						num = $('.scoreinfo').children('.title').children('span').text();
						$('.scoreinfo').children('.title').children('span').text(parseInt(num)+1);
						$("#page-evaluation").prepend(result.data);
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
	/** End 添加评论按钮点击事件 nlw */

});
/** Start 获取url中的文章id 朱子武 20160315*/
function getQueryString(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]);
	return null;
}
/** End 获取url中的文章id 朱子武 20160315*/
</script>
<script>
		/**start nlw20160819 文档页面局部刷新，分页重写*/

		var cur_page = 1;//当前页
		var total_num,page_total;//总记录条数，总页数
		function getEvaluation(page){
			var currentpage = parseInt(page);
			var evaluation_layout = "page";
			$.ajax({
				type:"POST",
				url:"../../comment/common/evaluation_my/getevaluation.php",
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
					//js生成分页
					getPageBar();
//					getPageBar1();
				},
				error: function () {
					alert("数据异常，请检查是否json格式");
				}
			});
		}
		function getPageBar(){

			$.ajax({
				type:"GET",
				url:"../../comment/common/evaluation_my/pagebar.php",
				data:{
					cur_page:cur_page,
					page_total:page_total
				},
				dataType:"json",
				success: function (result) {
					$("#page-list").html(result.data);
				},
				error:function () {
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
		/**end nlw文档页面局部刷新，分页重写*/
</script>


</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

	<?php require_once("includes/header.php"); ?>
	<div class="bd"></div>
    
	<div style="height:20px;"></div>

    <div id="page" class="container-fluid">
        <!--位置 <?php echo $OUTPUT->full_header(); ?>-->
        <div id="page-content" class="row-fluid">
            <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
                <div class="row-fluid">
                    <section id="region-main" style="margin-left: auto;margin-right: auto; width: 74.35897436%;">
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
                    </section>
                    <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
                </div>
            </div>
            <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
        </div>


<!--        <footer id="page-footer">-->
<!--            <div id="course-footer">--><?php //echo $OUTPUT->course_footer(); ?><!--</div>-->
<!--    <!--         <p class="helplink">--><?php //echo $OUTPUT->page_doc_link(); ?><!--</p> -->
<!--            --><?php
//            echo $html->footnote;
//    //        echo $OUTPUT->login_info();
//    //        echo $OUTPUT->home_link();
//            echo $OUTPUT->standard_footer_html();
//            ?>
<!--        </footer>-->
    
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
		
    </div>

	<div class="division "></div>
	<!--底部导航条-->
	<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
	<?php require_once("includes/bottom_info.php"); ?>
	<!--底部导航条 end-->

	<!--右下角按钮-->
	<?php require_once("includes/link_button.php"); ?>
	<!--右下角按钮 end-->

<!-- 2016.3.29 毛英东 添加表情 -->
<script>
$(function(){
	$('.emotion').qqFace({
		id : 'facebox',
		assign:'comment-text',
		path:'../../theme/more/img/arclist/'	//表情存放的路径
	});
});
function face() {
	$('.commentinfo').each(
		function () {
			var str = $(this).html();
			str = str.replace(/\[(微笑|撇嘴|色|发呆|流泪|害羞|闭嘴|睡|大哭|尴尬|发怒|调皮|呲牙|惊讶|难过|冷汗|抓狂|吐|偷笑|可爱|白眼|傲慢|饥饿|困|惊恐|流汗|憨笑|大兵|奋斗|咒骂|疑问|嘘|晕|折磨|衰|敲打|再见|擦汗|抠鼻|糗大了|坏笑|左哼哼|右哼哼|哈欠|鄙视|快哭了|委屈|阴险|亲亲|吓|可怜|拥抱|月亮|太阳|炸弹|骷髅|菜刀|猪头|西瓜|咖啡|饭|爱心|强|弱|握手|胜利|抱拳|勾引|OK|NO|玫瑰|凋谢|红唇|飞吻|示爱)\]/g, function (w, word) {
				return '<img src="../../theme/more/img/arclist/' + em_obj[word] + '.gif" border="0" />';
			});
			$(this).html(str);
		}
	);
}

/**Start 显示office 岑霄 */
 $('#documentViewer').FlexPaperViewer(
            { config : {

                SWFFile: '<?php echo get_office_online_url($_GET['id']); ?>',
//				SWFFile: 'http://localhost/document_doc_swf/swf/d.swf',

                Scale : 1,
                ZoomTransition : 'easeOut',
                ZoomTime : 0.5,
                ZoomInterval : 0.2,
                FitPageOnLoad : true,
                FitWidthOnLoad : true,//自适应宽度
                FullScreenAsMaxWindow : false,
                ProgressiveLoading : false,
                MinZoomSize : 0.2,
                MaxZoomSize : 5,
                SearchMatchAll : false,
                InitViewMode : 'Portrait',
                RenderingOrder : 'flash',
                StartAtPage : '',
				//ProgressiveLoading: true,//当设置为true的时候，展示文档时不会加载完整个文档，而是逐步加载，但是需要将文档转化为9以上的flash版本（使用pdf2swf的时候使用-T 9 标签）。
				
                ViewModeToolsVisible : true,
                ZoomToolsVisible : true,
                NavToolsVisible : true,
                CursorToolsVisible : true,
                SearchToolsVisible : true,
                WMode : 'window',
                localeChain: 'zh_CN'
            }}
    );
	 
	/** End */
</script>
<!-- End 2016.3.29 毛英东 添加表情 -->
</body>
</html>
