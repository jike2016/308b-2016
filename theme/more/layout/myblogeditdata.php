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
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->
	<link rel="stylesheet" href="../theme/more/style/QQface.css" /><!-- 2016.3.25 毛英东 添加表情CSS -->
    <script src="../theme/more/js/jquery-1.11.3.min.js"></script>
    <script src="../theme/more/js/jquery.qqFace.js"></script><!-- 2016.3.25 毛英东 添加表情 -->

	<style>
		html, body {
			background-color: #ffffff;
		}
		.navbar-form {padding: 10px 0px; }
		@media (max-width: 1199px){
			body #region-main .mform:not(.unresponsive) .fitem .fitemtitle {
			    display: block;
			    margin-top: 0px;
			    margin-bottom: 4px;
			    text-align: left;
				 float: left;
			    width: 10%;
			}
			body #region-main .mform:not(.unresponsive) .fitem .felement {
			    margin-left: 0;
			    width: 90%;
			    float: right;
			    padding-left: 0;
			    padding-right: 0;
			    font-size: 16px;
		    }
		    body #region-main .mform:not(.unresponsive) .fitem .fcheckbox > span, body #region-main .mform:not(.unresponsive) .fitem .fradio > span, body #region-main .mform:not(.unresponsive) .fitem .fgroup>span {
			    margin-top: 0px; 
			}
			input[type="radio"], input[type="checkbox"] {
			    margin: 0px; 
			    margin-top: 1px \9;
			    line-height: normal;
			}
			select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
			    display: inline-block;
			    height: 20px;
			    padding: 4px 6px;
			    margin-bottom: 0px;
			    font-size: 14px;
			    line-height: 20px;
			    color: #555;
			    -webkit-border-radius: 4px;
			    -moz-border-radius: 4px;
			    border-radius: 4px;
			    vertical-align: middle;
			}
			body #region-main .mform:not(.unresponsive) .fitem .felement {
			    margin-left: 0;
			    width: 90%;
			    float: right;
			    padding-left: 0;
			    padding-right: 0;
			    padding: 5px 0px 0px 0px;
			}
			/**字体调节**/
			.fitem .fstaticlabel {
			    font-weight:100;
			    font-size: 16px;
			    color: #000;
			}
			#region-main .mform:not(.unresponsive) .fitem .fitemtitle label {
			    font-weight:100;
			    font-size: 16px;
			    color: #000;
			}
			select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
			    display: inline-block;
			    height: 36px;
			    padding: 4px 6px;
			    margin-bottom: 0px;
			    font-size: 14px;
			    line-height: 20px;
			    color: #555;
			    -webkit-border-radius: 4px;
			    -moz-border-radius: 4px;
			    border-radius: 4px;
			    vertical-align: middle;
			}
			
		}
		.mform .fdescription.required {
			    margin-left: 200px;
			    display: none;
		}
	</style>
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

	


        <!--位置 <?php echo $OUTPUT->full_header(); ?>-->
     
           
               
                    <section id="region-main" style="margin-left: auto;margin-right: auto; width: 100%;">
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
                    </section>
                    <?php //echo $OUTPUT->blocks('side-pre', $sidepre); ?>
              
          
            <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
    
    
       
    
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    
  <!-- 2016.3.25 毛英东 添加表情-->
<script>
	$(function(){
		$('.emotion').qqFace({
			id : 'facebox',
			assign:'id_summary_editor',
			path:'../theme/more/img/arclist/'	//表情存放的路径
		});
	});

	$('.content-box .content').each(
	function(){
		var str = $(this).html();
		str = str.replace(/\[(微笑|撇嘴|色|发呆|流泪|害羞|闭嘴|睡|大哭|尴尬|发怒|调皮|呲牙|惊讶|难过|冷汗|抓狂|吐|偷笑|可爱|白眼|傲慢|饥饿|困|惊恐|流汗|憨笑|大兵|奋斗|咒骂|疑问|嘘|晕|折磨|衰|敲打|再见|擦汗|抠鼻|糗大了|坏笑|左哼哼|右哼哼|哈欠|鄙视|快哭了|委屈|阴险|亲亲|吓|可怜|拥抱|月亮|太阳|炸弹|骷髅|菜刀|猪头|西瓜|咖啡|饭|爱心|强|弱|握手|胜利|抱拳|勾引|OK|NO|玫瑰|凋谢|红唇|飞吻|示爱)\]/g, function(w,word){
			return '<img src="../theme/more/img/arclist/'+ em_obj[word] + '.gif" border="0" />';
		});
		$(this).html(str);
	}
);
</script>
<!-- end  2016.3.25 毛英东 添加表情 -->

</body>
</html>
