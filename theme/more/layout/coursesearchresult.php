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
<!--    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /><!-- 全局-->
    <link rel="stylesheet" href="../theme/more/style/coursesearch/classsearch.css" />
    <style>
        * {margin: 0;padding: 0;list-style: none;border: 0;}
        html,body {font-family: "微软雅黑";font-size: 14px;height:1000px;padding-top: 0px; background-color: #ffffff}
        a, a:hover {color: #3E3E3E;text-decoration: none;}
        ui,ol{margin: 0px}
        p {margin: 0px;}
        nav .center .r-box .dropdown-toggle span {  margin-top: 0px;  }
        .searchbtn, nav .center .r-box .dropdown-toggle{box-sizing: content-box}
        nav .center .r-box .search {  height: 22px;  box-sizing: content-box;  }
        .bd {  width: 100%;  height: 40px;  background-color: #10ADF3;  }
    </style>
    <script src="../theme/more/js/jquery-1.11.3.min.js"></script><!--全局-->
<!--    <script type="text/javascript" src="../theme/more/js/bootstrap.min.js" ></script>-->

</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>
<div class="bd"></div>
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
<!--<nav class="navstyle-bottom navbar-static-bottom"></nav>-->
<?php require_once("includes/bottom_info.php"); ?>
<!--底部导航条 end-->

<!--右下角按钮-->
<?php require_once("includes/link_button.php"); ?>
<!--右下角按钮 end-->

</body>
</html>