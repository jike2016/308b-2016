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
 * The one column layout.
 *
 * @package   theme_clean
 * @copyright 2013 Moodle, moodle.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_clean_get_html_for_settings($OUTPUT, $PAGE);

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $OUTPUT->standard_head_html() ?>
    
<!--    <link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
<!--    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->
<!--    <link rel="stylesheet" href="../theme/more/style/loginpage/loginstyle.css" />-->
    <link rel="stylesheet" href="../theme/more/style/loginpage/style.css" />
<!--    <link rel="stylesheet" href="../theme/more/style/loginpage/reset.css" />-->
    <link rel="stylesheet" href="../theme/more/style/loginpage/supersized.css" />


    <script src="../theme/more/js/jquery-1.11.3.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/supersized.3.2.7.min.js" charset="utf-8" type="text/javascript"></script>
    <script src="js/supersized-init.js" charset="utf-8" type="text/javascript"></script>
</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php require_once("includes/header.php"); ?>
<style>
    .container-fluid {  min-height: 800px; }
    .r-box .searchbtn,.r-box .search ,.r-box .dropdown-toggle{display: none}
    html,body {background-color: transparent;}
    nav {background-color: transparent; height: 80px}
    nav .center,nav .center .r-box, nav .center .l-box{height: 80px}
    nav .center .r-box {  width: 60%;}
    nav .center {  width: 750px;}
    button, #loginbtn {margin: 15px 0px 0px 0px}
    .loginbox {margin-top: 100px}
    .loginbox h2{margin-bottom: -20px}
    .inputbox input {margin-top: 20px}
    .a-box {display: none}
</style>
<div class="bd"></div>

<div id="page" class="container-fluid">

   <!-- <?php echo $OUTPUT->full_header(); ?>-->
    <div id="page-content" class="row-fluid">
        <section>
            <?php
            echo $OUTPUT->course_content_header();
            echo $OUTPUT->main_content();
            echo $OUTPUT->course_content_footer();
            ?>
        </section>
    </div>

<!--    <footer id="page-footer">-->
<!--        <div id="course-footer">--><?php //echo $OUTPUT->course_footer(); ?><!--</div>-->
<!--<!--         <p class="helplink">--><?php //echo $OUTPUT->page_doc_link(); ?><!--</p> -->
<!--        --><?php
//        echo $html->footnote;
//        echo $OUTPUT->login_info();
//        echo $OUTPUT->home_link();
//        echo $OUTPUT->standard_footer_html();
//        ?>
<!--    </footer>-->

    <?php echo $OUTPUT->standard_end_of_body_html() ?>

</div>

</body>
</html>
