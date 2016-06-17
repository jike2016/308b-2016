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
require_once($CFG->dirroot .'/config.php');
require_once($CFG->dirroot.'/org/org.class.php');

$org = new org();

//添加根节点
if($org->get_root_node_id() === False)
{
	$root_id = $org->add_root_node("root");
} else {
	$root_id = $org->get_root_node_id();
}

$tree = $org->show_node_tree_user_no_office($root_id);

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
	<link rel="stylesheet" href="../theme/more/style/orgselect/orgselect_openmetting.css" type="text/css">
	<link rel="stylesheet" href="../theme/more/js/zTreeStyle/zTreeStyle.css" type="text/css">
	
	<script type="text/javascript" src="../theme/more/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="../theme/more/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.core.js"></script>
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.excheck.js"></script>
	<script type="text/javascript" src="../theme/more/js/orgselect/jquery.ztree.exedit.js"></script>
<!--	<script type="text/javascript" src="../../theme/more/js/orgselect/ztreepage.js"></script>-->


    
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>

<!--弹出窗体-->
		    <!--div class="mask"></div-->
			<div class="reset">
				<div class="header">
					<p>名单</p>
					<a class="close">x</a>
				</div>
					
				<div class="zTreeDemoBackground">
					<ul id="treeDemo" class="ztree"></ul> <!--important 显示文件树的地方-->
				</div>
				
				<div class="button-box">
					<button id="add" class="btn btn-info btn-block">添加</button>
				</div>
			</div>
		    <!--弹出窗体end-->
			
<div style="height:20px; width:100%;"></div>
<div id="page" class="container-fluid">
    <?php echo $OUTPUT->full_header(); ?>
    <div id="page-content" class="row-fluid">
        <div id="region-main-box" class="<?php echo $regionmainbox; ?>">
            <div class="row-fluid">
<!--                <section id="region-main" style="margin-left: auto;margin-right: auto; width: 74.35897436%;">-->
				<section id="region-main" class="<?php echo $regionmain; ?>">
                    <?php
                    echo $OUTPUT->course_content_header();
                    echo $OUTPUT->main_content();
                    echo $OUTPUT->course_content_footer();
                    ?>
                </section>
                <?php echo $OUTPUT->blocks('side-pre', $sidepre); ?>
            </div>
        </div>
        <?php echo $OUTPUT->blocks('side-post', $sidepost); ?>
    </div>

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>
<!--底部导航条-->
	<nav class="navstyle-bottom navbar-static-bottom"></nav>
	<!--底部导航条 end-->
</body>
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

	function beforeClick(treeId, treeNode, clickFlag) {
		treenode_orgid = treeNode.id;
		treenode_userid = treeNode.userid;
		treenode_name = treeNode.name;
		//return (treeNode.click == true);
	}

	//		添加人员
	function adduserSelect(userid, username)
	{
//			var obj = document.getElementById("id_hostname");
//			var valueArray = new Array();
//			$('#id_hostname option').each(function () {
//				var $option = $(this);
//				var value = $option.val();
//				valueArray.push(value);
//			});

		document.getElementById("id_hostname").value = username;

		document.getElementsByName("hostname_id")[0].value = userid;
//			context.Request.Form["hostnameID"] = userid;
//			alert(document.getElementById("id_hostname_id").value);
//			for(var i in valueArray)
//			{
//				if(valueArray[i] == userid) return;
//			}

//			$('#id_hostname').append('<option value='+userid+'>'+username+'</option>');
	}

	//		删除人员
	function deleteuserSelect(userid, username)
	{
		var obj = document.getElementById("id_userSelect");
		var length = obj.options.length - 1;
		for(var i = length; i >= 0; i--){
			if(obj[i].selected == true){
				// 获取选中的options的id，即userid；obj.options[i].value
				// alert(obj.options[i].value);
				obj.options[i] = null;
			}
		}
	}

	$(document).ready(function(){
		$.fn.zTree.init($("#treeDemo"), setting, zNodes);


		$('#id_add_person').click(function(){   //弹出窗口
			$('.reset').slideDown(200);
		});
		$('.close').click(function(){
			$('.reset').slideUp(200);
		});

		$('#add').on('click', function(){      //添加按钮动作
			//select控件添加内容
			if(treenode_userid == 0 && treenode_orgid != 0)
			{
//					$.ajax({
//						url: "../org/orgCRUD.php",
//						dataType:"json",
//						data: { treeNodeid: treenode_orgid, type: 'click_all_user'},
//						success: function(msg){
//							$.each(msg, function(commentIndex, comment){
//								var username = comment['lastname'] + comment['firstname'];
//								var userid = comment['user_id'];
//								adduserSelect(userid,username);
//							});
//						}
//					});
				alert('请添加用户！');
			}
			else if(treenode_orgid == 0 && treenode_userid != 0)
			{
				adduserSelect(treenode_userid,treenode_name);
			}
		});

		$('#id_delete_person').on('click', function(){
			deleteuserSelect();
		});

		/** Start 创建台账按钮的点击函数，将用户多选框里的值取出来放到hidden_users里 岑霄 20160306*/
		$('#id_submitbutton').click(function(){   //弹出窗口
			// alert('3');
			var hidden_users = document.getElementById("id_hidden_user");
			//获取select里所有的用户id，再把id传入hidden_users.value
			var obj = document.getElementById("id_userSelect");
			var length = obj.options.length - 1;
			var userids='';
			for(var i = length; i >= 0; i--){
				if(i!=0){
					userids += obj.options[i].value+',';
				}else{
					userids += obj.options[i].value+'';
				}
			}
			hidden_users.value=userids;

		});
		/** End */
	});
</script>
</html>
