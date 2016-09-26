<?php
/** 保存 */
require_once('../config.php');

global $CFG;
global $DB;

$categoryIDs = $_GET['categoryID'];
$i=1;
foreach($categoryIDs as $categoryID){
	$newid = $DB->update_record('index_course_category', array("id"=>$i,"course_category_id"=>$categoryID));
	$i++;
}

echo '1';

