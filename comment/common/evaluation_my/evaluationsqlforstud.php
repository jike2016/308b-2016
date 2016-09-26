<?php
require_once("../../../config.php");
/**
 * 根据评论页面选择相应的sql语句（查询评论信息的SQL语句）
 * @author nlw 20160823
 * @param
 * @return $sql
 */
function evaluation_sql_for_stud($evaluation_layout){
    $evaluation = array(
        "page" => array(
            "evaluation_db" =>'comment_article_my',
            "pagetype_id" => 'articleid',
            "evaluation_sql" => 'SELECT a.id, a.userid, a.comment, b.firstname, b.lastname, a.commenttime FROM mdl_comment_article_my a JOIN mdl_user b ON a.userid = b.id WHERE articleid = ? ORDER BY commenttime DESC LIMIT ',
            "count_sql" => 'SELECT id as mycount FROM mdl_comment_article_my WHERE articleid = ? ',
            "comment_sql"=> 'SELECT id, comment, commenttime FROM mdl_comment_article_my WHERE articleid = ',
            "commentall_sql" => 'SELECT id, comment, articleid, userid FROM mdl_comment_article_my ORDER BY commenttime DESC '
        ),
        "lesson" => array(
            "evaluation_db" => 'comment_video_my',
            "pagetype_id" => 'modid',
            "evaluation_sql" =>'SELECT a.id, a.userid, a.comment, b.firstname, b.lastname, a.commenttime FROM mdl_comment_video_my a JOIN mdl_user b ON a.userid = b.id WHERE modid = ? ORDER BY commenttime DESC LIMIT ',
            "count_sql"=>'SELECT id as mycount FROM mdl_comment_video_my WHERE modid = ? ',
            "comment_sql"=> 'SELECT id, comment, commenttime FROM mdl_comment_video_my WHERE modid = ',
            "commentall_sql" => 'SELECT id, comment, userid, modid FROM mdl_comment_video_my ORDER BY commenttime DESC'
        ),
        "course" =>array(
            "evaluation_db" => 'comment_course_my',
            "pagetype_id" => 'courseid',
            "evaluation_sql" => 'SELECT a.id, userid, comment, b.firstname, b.lastname, commenttime FROM mdl_comment_course_my a JOIN mdl_user b ON a.userid = b.id WHERE courseid = ? ORDER BY commenttime DESC LIMIT ',
            "count_sql" => 'SELECT id as mycount FROM mdl_comment_course_my WHERE courseid = ? ',
            "comment_sql" => 'SELECT id, comment, commenttime FROM mdl_comment_course_my WHERE courseid = ',
            "commentall_sql" => 'SELECT id, comment, courseid, userid FROM mdl_comment_course_my ORDER BY commenttime DESC'
        )
    );
    foreach($evaluation as $key => $value){
        if($key == $evaluation_layout){
            $evaluation_sql = $value;
            return $evaluation_sql;
            break;
        }
    }
}

















