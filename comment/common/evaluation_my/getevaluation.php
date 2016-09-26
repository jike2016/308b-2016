<?php
/**nlw课程文档阅读评论内容局部刷新*/
require_once("../../../config.php");
$current_page = $_POST['current_page'];
$id=$_POST['id'];
$evaluation_layout =$_POST['evaluation_layout'];
//session_start();

$_SESSION['current_page'] = $current_page;

global $DB;
global $USER;
global $OUTPUT;

require_once($CFG->dirroot."/comment/common/evaluation_my/evaluationsqlforstud.php");
$pl_sql = evaluation_sql_for_stud($evaluation_layout);
$evaluation_sql = $pl_sql[evaluation_sql];
$count_sql = $pl_sql[count_sql];

$evaluationCount = my_get_article_evaluation_count($id,$count_sql);
$page_total = $evaluationCount->ceilcount;


$my_page = ($current_page-1) * 10;
$evaluation = $DB->get_records_sql( $evaluation_sql. $my_page . ',10', array($id));

//$evaluation = $DB->get_records_sql('SELECT a.id, a.userid, a.comment, b.firstname, b.lastname, a.commenttime FROM mdl_comment_article_my a JOIN mdl_user b ON a.userid = b.id WHERE articleid = ? ORDER BY commenttime DESC LIMIT ' . $my_page . ',10', array($articleid));
//$evaluation = $DB->get_records_sql('SELECT a.id, a.userid, a.comment, b.firstname, b.lastname, a.commenttime FROM mdl_comment_article_my a JOIN mdl_user b ON a.userid = b.id WHERE articleid = 122 ORDER BY commenttime DESC LIMIT ' . $my_page . ',10');

$evaluationStr = '';
if(!empty($evaluation)){
    foreach ($evaluation as $value) {
        $userobject = new stdClass();
        $userobject->metadata = array();
        $user = $DB->get_record('user', array('id' => $value->userid), '*', MUST_EXIST);
        $userobject->metadata['useravatar'] = $OUTPUT->user_picture(
            $user,
            array(
                'link' => false,
                'visibletoscreenreaders' => false
            )
        );
        $userobject->metadata['useravatar'] = str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
        $evaluationStr .= '
            <!--评论内容1-->
            <div class="comment container">
                <div class="comment-l">
                    <div class="Learnerimg-box">
                       ' . $userobject->metadata['useravatar'] . '
                    </div>
                </div>
                <div class="comment-r">
                    <p class="name">' . $value->lastname . $value->firstname . '</p>
                    <p class="commentinfo">
                        ' . $value->comment . '
                    </p>
                    <p class="time">时间：' . userdate($value->commenttime, '%Y-%m-%d %H:%M') . '</p>
                </div>
            </div>
            <!--评论内容1 end-->
             ';
    }
    $result = [
        "success"=>true,
        "page_total" => $page_total,
        "data"=>$evaluationStr,
    ];
    echo json_encode($result);
}else{
    $result = [
        "success"=>false,
        "page_total" =>"",
        "data"=>"",
    ];
    echo json_encode($result);
}

/** 获取评价数目页数*/

function my_get_article_evaluation_count($id,$count_sql)
{
    global $DB;
    $evaluation = $DB->get_records_sql($count_sql, array($id));

    $mycount = count($evaluation) < 0 ? 0 : count($evaluation);
    $evaluationCount = new stdClass();
    $evaluationCount->count = $mycount;
    $mycount = ceil($mycount / 10);
    $evaluationCount->ceilcount = ($mycount <= 1 ? 1 : $mycount);
    return $evaluationCount;
}
?>