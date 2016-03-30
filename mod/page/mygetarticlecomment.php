<?php
/** 添加文章评论 朱子武 20160315 */
//require_once($CFG->dirroot."\config.php");
require_once("../../config.php");
$mycomment=$_GET['mycomment'];
$articleid=$_GET['articleid'];
global $DB;
global $USER;
$DB->insert_record('comment_article_my',array('articleid'=>$articleid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
echo '1';
