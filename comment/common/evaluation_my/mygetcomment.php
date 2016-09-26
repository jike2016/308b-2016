<?php
/** 添加文章评论 朱子武 20160315 */
//require_once($CFG->dirroot."\config.php");
require_once("../../../config.php");
$mycomment=$_POST['mycomment'];
$id=$_POST['id'];
$evaluation_layout =$_POST['evaluation_layout'];

global $DB;
global $USER;
$status = "";
$evaluation_latest = "";


require_once($CFG->dirroot."/comment/common/evaluation_my/evaluationsqlforstud.php");
$pl_sql = evaluation_sql_for_stud($evaluation_layout);



$commentresult = $DB->get_records_sql(sprintf($pl_sql[comment_sql].$id.' AND userid = '.$USER->id.' ORDER BY commenttime DESC LIMIT 1'));

$commentallresult = $DB->get_records_sql(sprintf($pl_sql[commentall_sql]));
$similarresult = new stdClass();
if(count($commentallresult))
{
    foreach($commentallresult as $commentallresultvalue)
    {
        similar_text($commentallresultvalue->comment, $mycomment, $percent);
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
            $status = 0;
        }elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
        {
            $status= 2;
        }elseif($similarresult->percent > 90 && $similarresult->articleid == $id)
        {
            $status = 2;
        }else
        {
            $id_latest = $DB->insert_record($pl_sql[evaluation_db],array($pl_sql[pagetype_id]=>$id,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
            $status = 1;
        }
    }

}elseif($similarresult->percent > 90 && $similarresult->userid == $USER->id)
{
    $status = 2;
}elseif($similarresult->percent > 90 && $similarresult->articleid == $id)
{
    $status = 2;
}
else
{
    $id_latest = $DB->insert_record($pl_sql[evaluation_db],array($pl_sql[pagetype_id]=>$id,'userid'=>$USER->id,'comment'=>$mycomment,'commenttime'=>time()),true);
    $status = 1;
}
/**start nlw ajax发表评论*/
if($status != 1){
    $result = [
        "status" => $status,
        "data" => ""
    ];
    echo json_encode($result);
}else{

    $name = $DB->get_records_sql(sprintf('SELECT firstname,lastname FROM mdl_user where id='.$USER->id));
    $content = $DB->get_records_sql(sprintf('SELECT * FROM mdl_'.$pl_sql[evaluation_db].' where id='.$id_latest));
    $value = stdclass;
    if(count($content)  && count($name))
    {
        $value = current($content);
        $value->name = current($name);
        $user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
        $icon = $OUTPUT->user_picture ($user, [ 'link' => false, 'visibletoscreenreaders' => false]);
        $value->icon = str_replace("width=\"35\" height=\"35\"", " ", $icon);
    }

    $evaluation_latest .= '
        <!--评论内容1-->
        <div class="comment container">
            <div class="comment-l">
                <div class="Learnerimg-box">
                ' . $value->icon . '
                </div>
            </div>
            <div class="comment-r">
                <p class="name">' . $value->name->lastname . $value->name->firstname . '</p>
                <p class="commentinfo">
                    ' . $value->comment . '
                </p>
                <p class="time">时间：' . userdate($value->commenttime, '%Y-%m-%d %H:%M') . '</p>
            </div>
        </div>
        <!--评论内容1 end-->
        ';
    $result = [
        "status" => $status,
        "data" => $evaluation_latest
    ];
    echo json_encode($result);

}
/**end ajax发表评论*/
