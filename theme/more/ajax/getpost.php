<?php
require("../../../config.php");
require_once($CFG->dirroot . "/lib/dml/moodle_database.php");
	//根据category id查数据库
	$categoryid=$_POST["categoryid"];
	$categorys = $DB->get_records_sql('select name,id FROM mdl_course_categories where parent='.$categoryid.';');
	//输出二级分类
	echo $categorys;
?>