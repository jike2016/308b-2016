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
//echo $CFG->dirroot;exit;
/** 慕课管理员 课程管理 页面 */

require_once($CFG->dirroot .'/config.php');
require_once($CFG->dirroot.'/org_classify/org.class.php');

$org = new org();
$tree = $org->show_all_node_tree_only_unit();//获取单位组织架构(只含单位，不含人员)

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
    
    <link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/theme/more/style/navstyle.css" /><!-- 全局-->
	<link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/theme/more/style/orgselect/orgselect.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $CFG->wwwroot;?>/theme/more/js/zTreeStyle/zTreeStyle.css" type="text/css">
	
	<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/theme/more/js/jquery-1.11.3.min.js"></script>
<!--	<script type="text/javascript" src="--><?php //echo $CFG->wwwroot;?><!--/theme/more/js/bootstrap.min.js"></script>-->
	<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/theme/more/js/orgselect/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/theme/more/js/orgselect/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="<?php echo $CFG->wwwroot;?>/theme/more/js/orgselect/jquery.ztree.exedit.js"></script>
<!--	<script type="text/javascript" src="../../theme/more/js/orgselect/ztreepage.js"></script>-->
	<style>
		.header {width: auto;height:auto; background-color:#fff;border-radius: 0px 0px 0px 0px;}
		nav li{    list-style: none;}
		.collection .No {width:5% }
		.collection .course_name {width: 19%}
		.collection .category {width:10% }
		.collection .org_name {width:24% }
		.collection .browseable_org {width:24% }
		.collection .create_time {width:13% }
		.collection .actions {width:5% }

		#page{min-height: 750px}
	</style>

	<script>

		var setting = {
			data: {
				simpleData: {
					enable: true
				}
			},
			callback: {
				beforeClick: beforeClick
			}
		};

		var zNodes =[
			<?php echo $tree['tree'];?>
		];

		var treenode_orgid;    //全局变量存储树节点id
		var treenode_userid;    //全局变量存储树节点userid
		var treenode_name;  //全局变量存储树节点name
		var remove_role = <?php echo "'".$remove_role."'"; ?>;		//移除的账号角色
		var treeObj;//用于存储整棵树的对象
		var browse_orgids = new Array();//存储选择的可浏览此课程的单位

		//在弹出的组织架构框中，每点击一次结构树，便会执行一次
		function beforeClick(treeId, treeNode, clickFlag) {
			treenode_orgid = treeNode.id;
			treenode_userid = treeNode.userid;
			treenode_name = treeNode.name;
			//return (treeNode.click == true);
		}

		var courseid;//当前编辑的课程id
		var html_obj;//当前编辑的课程 html 对象
		var option_type;//设置类型：设置管理单位 1；设置查看单位 2
		$(document).ready(function(){
			treeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);

			$('.manage_org').click(function(){   //设置‘管理单位’按钮
				$('.reset-header p').text('选择所属管理单位');
				courseid = $(this).attr("data-courseid");//获取当前编辑的记录
				html_obj = $(this);//当前编辑的对象
				option_type = 1;//管理单位
				$('.reset').slideDown(200);
			});
			$('.view_org').click(function(){   //设置‘查看单位’按钮
				$('.reset-header p').text('选择可浏览单位');
				courseid = $(this).attr("data-courseid");//获取当前编辑的记录
				html_obj = $(this);//当前编辑的对象
				option_type = 2;//查看单位
				$('.reset').slideDown(200);
			});

			//---以下是弹出框（组织架构）------------------------------
			$('.close').click(function(){	//关闭弹出窗口
				$('.reset').slideUp(200);
			});
			$('#add').on('click', function(){    //选择单位的‘确认’按钮动作
				if(treenode_userid == 0 && treenode_orgid != 0)//选的是单位
				{
//					alert(treenode_orgid+'--'+treenode_name);
					if(option_type == 1){//设置管理单位
						$.ajax({
							type:"POST",
							url:"../course_classify/course_classifyManage_ajax.php",
							data:{
								courseid:courseid,
								orgid:treenode_orgid,
								option_type:option_type
							},
							dataType:"json",
							success: function (result) {
								//alert("success");
								html_obj.parents('td').children('.manage_org_name').html(treenode_name);
							},
							error:function (result) {
								alert("设置失败");
							}
						});
					}
					else if(option_type == 2){//设置查看单位
						browse_orgids = treeObj.getSelectedNodes();//获取被选中的多个节点
						$.ajax({
							type:"POST",
							url:"../course_classify/course_classifyManage_ajax.php",
							data:{
								courseid:courseid,
								orgid:browse_orgids,
								option_type:option_type
							},
							dataType:"json",
							success: function (result) {
								var select_org = '';
								for(var i=0;i<browse_orgids.length;i++){
									select_org += browse_orgids[i].name+'</br>';
								}
								//alert(select_org);
								html_obj.parents('td').children('.view_org_name').html(select_org);
							},
							error:function (result) {
								alert("设置失败");
							}
						});
					}

				}
				$('.reset').slideUp(200);
			});

		});
	</script>
    
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

<!--弹出窗体-->
		    <!--div class="mask"></div-->
			<div class="reset">
				<div class="reset-header">
					<p>选择所属单位</p>
					<a class="close">x</a>
				</div>
					
				<div class="zTreeDemoBackground">
					<ul id="treeDemo" class="ztree"></ul> <!--important 显示文件树的地方-->
				</div>
				
				<div class="button-box">
					<button id="add" class="btn btn-info btn-block">确定</button>
				</div>
			</div>
<!--弹出窗体end-->
			
<div style="height:20px; width:100%;"></div>
<div id="page" class="container-fluid">
<!--	--><?php //echo $OUTPUT->full_header(); ?>
	<section id="region-main" class="">
		<?php
		echo $OUTPUT->course_content_header();
		echo $OUTPUT->main_content();
		echo $OUTPUT->course_content_footer();
		?>
	</section>
	<?php echo $OUTPUT->standard_end_of_body_html() ?>
</div>

<!--底部导航条-->
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->
</body>
</html>
