<?php
/** START 朱子武 删除用户收藏页面 20160227*/
require_once("../../config.php");
$myid=$_GET['myid'];
global $DB;
global $USER;

$DB->delete_records('collection_my',array('userid'=>$USER->id,'id'=>$myid),true);

echo '1';
/**---my_collection_delete  END---*/
?>