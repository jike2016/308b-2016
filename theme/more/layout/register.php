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
/** 用户注册 页面 */

require_once($CFG->dirroot .'/config.php');
require_once($CFG->dirroot.'/org/org.class.php');
require_once($CFG->dirroot.'/register/my_register_conf.class.php');

$org = new org();
$register_conf = new my_register_conf();
$tree = $org->show_node_tree_only_unit($register_conf->get_level());//获取前两级的单位组织架构

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
    
    <link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css">	<!--全局-->
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /><!-- 全局-->
	<link rel="stylesheet" href="../theme/more/style/orgselect/orgselect.css" type="text/css">
	<link rel="stylesheet" href="../theme/more/js/zTreeStyle/zTreeStyle.css" type="text/css">
	
	<script type="text/javascript" src="../theme/more/js/jquery-1.11.3.min.js"></script>
<!--	<script type="text/javascript" src="../theme/more/js/bootstrap.min.js"></script>-->
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.exedit.js"></script>
<!--	<script type="text/javascript" src="../../theme/more/js/orgselect/ztreepage.js"></script>-->

	<style>
		nav li{   list-style: none;}
		#page{min-height: 780px}
		nav .center .r-box .a-box,nav .center .r-box .searchbtn,nav .center .r-box .search ,nav .center .r-box .btn-group  {display: none}
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

		function beforeClick(treeId, treeNode, clickFlag) {
			treenode_orgid = treeNode.id;
			treenode_userid = treeNode.userid;
			treenode_name = treeNode.name;
			//return (treeNode.click == true);
		}

		//	添加单位，将选择的单位填充显示,并将该单位的id 传入隐藏的标签元素中
		function addOrgSelect(orgid, orgname)
		{
			$('#id_orgSelect').val(orgname);
			//把选择的单位id传入hidden_org.value
			var hidden_org = document.getElementById("id_hidden_org");
			hidden_org.value = orgid;
		}

		$(document).ready(function(){
			$.fn.zTree.init($("#treeDemo"), setting, zNodes);

			$('#id_add_org').click(function(){   //弹出窗口,‘选择单位’按钮
				$('.reset').slideDown(200);
			});
			$('.close').click(function(){	//关闭弹出窗口
				$('.reset').slideUp(200);
			});
			$('#add').on('click', function(){    //选择单位的‘确认’按钮动作
				//text控件添加内容
				if(treenode_userid == 0 && treenode_orgid != 0)//选的是单位
				{
					addOrgSelect(treenode_orgid,treenode_name);
				}
				$('.reset').slideUp(200);
			});

			/** Start 创建按钮的点击函数 */
			$('#id_submitbutton').click(function(){
				
			});
			/** End */
		});
	</script>
    
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

<!--弹出窗体-->
		    <!--div class="mask"></div-->
			<div class="reset">
				<div class="header">
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
    <?php echo $OUTPUT->full_header(); ?>
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
	

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
<!--底部导航条-->
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->
</body>
</html>
