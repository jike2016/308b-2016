<?php
/** START 朱子武 添加点赞 20160227*/
require_once("../config.php");
$myurl=$_GET['myurl'];
$mytitle=$_GET['mytitle'];
global $DB;
global $USER;
$myresult = $DB->get_records_sql('SELECT id FROM mdl_course_like_my WHERE url = ? AND userid = ?', array($myurl,$USER->id));
if(count($myresult))
{
    echo '2';
}
else
{
    $DB->insert_record('course_like_my',array('userid'=>$USER->id,'url'=>$myurl,'title'=>$mytitle,'liketime'=>time()),true);
//    $DB->get_record_sql('INSERT INTO mdl_collection_my (userid, url, title, collectiontime) VALUES (?, ?, ?, ?)', array($USER->id, $myurl, $mytitle, time()));
    echo '1';

    $mylikecount = '';
    /** Start更换之前的更新数据库语句 朱子武 20160316*/
    $mylikecount = $DB->get_records_sql('SELECT id, likecount, url FROM mdl_course_like_count_my WHERE url = ?', array($myurl));
    if(count($mylikecount))
    {
        $likeObj = new stdClass();
        foreach($mylikecount as $likeValue)
        {
            $likeObj->id = $likeValue->id;
            $likeObj->likecount = $likeValue->likecount + 1;
        }
        $likeObj->url = $myurl;
        $DB->update_record('course_like_count_my',$likeObj);
//        $DB->get_records_sql('UPDATE mdl_course_like_count_my SET likecount= likecount + 1 WHERE url= ?', array($myurl));
    }
    /** End 更换之前的更新数据库语句 朱子武 20160316*/
    else
    {
        $DB->insert_record('course_like_count_my',array('url'=>$myurl,'likecount'=>1),true);
    }
//    $DB->insert_record('course_like_count_my',array('url'=>$myurl,'likecount'=>$mytitle,'liketime'=>time()),true);

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