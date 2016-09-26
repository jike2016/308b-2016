<?php
/** START 朱子武 添加收藏 20160226*/
require_once("../../config.php");
$myurl=$_GET['myurl'];
$mytitle=$_GET['mytitle'];
global $DB;
global $USER;
$myresult = $DB->get_records_sql('SELECT id FROM mdl_collection_my WHERE url = ? AND userid = ?', array($myurl,$USER->id));
if(count($myresult))
{
    echo '2';
}
else
{
    $DB->insert_record('collection_my',array('userid'=>$USER->id,'url'=>$myurl,'title'=>$mytitle,'collectiontime'=>time()),true);
//    $DB->get_record_sql('INSERT INTO mdl_collection_my (userid, url, title, collectiontime) VALUES (?, ?, ?, ?)', array($USER->id, $myurl, $mytitle, time()));
    echo '1';
//    $myscorecount = $DB->get_records_sql('SELECT id, score FROM mdl_score_course_my WHERE courseid = ?', array($courseid));
//    foreach($myscorecount as $value)
//    {
//        $sumscore += $value->score;
//    }
//    $average = $sumscore/count($myscorecount);
//
////  $DB->update_record('score_course_sum_my',array('courseid'=>$courseid,'sumscore'=>number_format($average,1)));
//
//    $myscorerecord  = $DB->get_records_sql('SELECT id FROM mdl_score_course_sum_my WHERE courseid = ?', array($courseid));
//    if(count($myscorerecord))
//    {
//        $DB->get_records_sql('UPDATE mdl_score_course_sum_my SET sumscore= ? WHERE courseid= ?', array(number_format($average,1),$courseid));
//    }
//    else
//    {
//        $DB->insert_record('score_course_sum_my',array('courseid'=>$courseid,'sumscore'=>number_format($average,1)),true);
//    }
}
/** ---END---*/
?>