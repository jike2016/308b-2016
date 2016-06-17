<?php
/** 添加文章评论 朱子武 20160315 */
//require_once($CFG->dirroot."\config.php");
require_once("../../config.php");
$mycomment=$_GET['mycomment'];
$articleid=$_GET['articleid'];
global $DB;
global $USER;


$commentresult = $DB->get_records_sql(sprintf('SELECT id, comment, commenttime FROM mdl_comment_article_my WHERE articleid = '.$articleid.' AND userid = '.$USER->id.' ORDER BY commenttime DESC LIMIT 1'));

$commentallresult = $DB->get_records_sql(sprintf('SELECT id, comment, articleid, userid FROM mdl_comment_article_my ORDER BY commenttime DESC '));
$similarresult = new stdClass();
if(count($commentallresult))
{
    foreach($commentallresult as $commentallresultvalue)
    {
        similar_text($commentallresultvalue->comment, $_GET['mycomment'], $percent);
        if($percent > $similarresult->percent)
        {
            $similarresult->percent = $percent;
            $similarresult->userid = $commentallresultvalue->userid;
            $similarresult->articleid = $commentallresultvalue->articleid;
        }
    }
}
if(count($commentresult))
{
    foreach($commentresult as $commentresultvalue)
    {
        if($commentresultvalue->commenttime + 60 > time())
        {
            echo '0';
        }elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
        {
            echo '2';
        }elseif($similarresult->percent > 90 && $similarresult->articleid == $articleid)
        {
            echo '2';
        }else
        {
            $DB->insert_record('comment_article_my',array('articleid'=>$articleid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
            echo '1';
        }
    }
}elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
{
    echo '2';
}elseif($similarresult->percent > 90 && $similarresult->articleid == $articleid)
{
    echo '2';
}
else
{
    $DB->insert_record('comment_article_my',array('articleid'=>$articleid,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
    echo '1';
}

