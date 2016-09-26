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
    
    <link rel="stylesheet" href="<?php $CFG->wwwroot?>/moodle/theme/more/style/bootstrap.css" type="text/css"><!--全局-->
    <link rel="stylesheet" href="<?php $CFG->wwwroot?>/moodle/theme/more/style/navstyle.css" /> <!--全局-->

    <script src="<?php $CFG->wwwroot?>/moodle/theme/more/js/jquery-1.11.3.min.js"></script>
    
	<style>
		.navbar-form {padding: 10px 0px; }
		html, body {
			background-color: #ffffff;
		}
		@media (max-width: 1199px){
			body #region-main .mform:not(.unresponsive) .fitem .fitemtitle {
			    display: block;
			    margin-top: 4px;
			    margin-bottom: 4px;
			    text-align: left;
			    width: 100%;
			    float: left;
			    width: 12%;
			}
			body #region-main .mform:not(.unresponsive) .fitem .felement {
			    margin-left: 0;
			    width: 100%;
			    float: left;
			    padding-left: 0;
			    padding-right: 0;
			    float: right;
			    width: 88%;
			}
			select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input {
				height: 40px;
			}
			.editor_atto {
				margin-top: 10px;
			}
			#region-main .mform:not(.unresponsive) .fitem .fitemtitle label {
			    font-weight: 100;
			    font-size: 16px;
			}
			.mform .fdescription.required {
			    margin-left: 0px;
			    text-align: center;
			    margin-bottom: 40px;
				display:none;
			}
			table#form td.submit, .form-buttons, .path-admin .buttons, #fitem_id_submitbutton, .fp-content-center form+div, div.backup-section+form, #fgroup_id_buttonar {
			    padding: 19px 20px 20px;
			    margin-top: 30px;
			}
			table#form td.submit, .form-buttons, .path-admin .buttons, #fitem_id_submitbutton, .fp-content-center form+div, div.backup-section+form, #fgroup_id_buttonar {
			    padding: 19px 0px 0px;
			    margin-top: 30px;
			}
		}
		.mform .fdescription.required {
			    display:none;  }

	</style>

</head>
<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

            <?php //echo $OUTPUT->full_header(); ?>
     
                    <section id="region-main" style="margin-left: auto;margin-right: auto; width: 100%;">
                        <?php
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
                        ?>
                    </section>

<?php echo $OUTPUT->standard_end_of_body_html() ?>
    
  
</body>
</html>
