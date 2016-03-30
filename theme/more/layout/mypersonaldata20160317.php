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
    <title>columns3_more<?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <?php echo $OUTPUT->standard_head_html() ?>
    
    <link rel="stylesheet" href="../theme/more/style/bootstrap.css" type="text/css"><!--全局-->
    <link rel="stylesheet" href="../theme/more/style/navstyle.css" /> <!--全局-->

    <script src="../theme/more/js/jquery-1.11.3.min.js"></script>
    
	<style>
		.navbar-form {
		padding: 10px 0px; }
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
    
    
        <footer id="page-footer">
            <div id="course-footer"><?php echo $OUTPUT->course_footer(); ?></div>
    <!--         <p class="helplink"><?php echo $OUTPUT->page_doc_link(); ?></p> -->
            <?php
            echo $html->footnote;
    //        echo $OUTPUT->login_info();
    //        echo $OUTPUT->home_link();
            echo $OUTPUT->standard_footer_html();
            ?>
        </footer>
    
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    
  
</body>
</html>
