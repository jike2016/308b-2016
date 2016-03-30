<?php
/** START 朱子武 添加评分 20160226*/
require_once("../../config.php");
$mycomment=$_GET['mycomment'];
$courseid=$_GET['courseid'];
$mystarnum = $_GET['mystarnum'];
global $DB;
global $USER;
$myscore = $DB->get_records_sql('SELECT id FROM mdl_score_course_my WHERE courseid = ? AND userid = ?', array($courseid,$USER->id));
if(count($myscore))
{
    echo '2';
}
else
{
    $DB->insert_record('score_course_my',array('courseid'=>$courseid,'userid'=>$USER->id,'score'=>$mystarnum,'comment'=>$mycomment,'scoretime'=>time()),true);
    echo '1';
    $myscorecount = $DB->get_records_sql('SELECT id, score FROM mdl_score_course_my WHERE courseid = ?', array($courseid));
	$sumscore=0;
	$average=0;
    foreach($myscorecount as $value)
    {
        $sumscore += $value->score;
    }
    $average = $sumscore/count($myscorecount);

//  $DB->update_record('score_course_sum_my',array('courseid'=>$courseid,'sumscore'=>number_format($average,1)));

    $myscorerecord  = $DB->get_records_sql('SELECT id FROM mdl_score_course_sum_my WHERE courseid = ?', array($courseid));
    if(count($myscorerecord))
    {
        /** Star 插入评分数据 朱子武 20160315*/
		$scoreparams = new stdClass();
        foreach($myscorerecord as $score_value)
        {
            $scoreparams->id = $score_value->id;
        }
        $scoreparams->sumscore = number_format($average,1);
        $scoreparams->courseid = $courseid;
		$DB->update_record('score_course_sum_my',$scoreparams);
        /** End 插入评分数据 朱子武 20160315*/
    }
    else
    {
        $DB->insert_record('score_course_sum_my',array('courseid'=>$courseid,'sumscore'=>number_format($average,1)),true);
    }
}
/** ---END---*/
?>