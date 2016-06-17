<?php
/**
 * Created by PhpStorm.
 * User: gcmooc
 * Date: 16-4-13
 * Time: 下午9:25
 */
function index_rank(){
    global $DB;
    //1. 删除被调走的人员记录
    $DB->execute('delete from mdl_course_index_rank_my where mdl_course_index_rank_my.userid not IN (select id from mdl_user)');

    //2. 从mdl_user同步user id到 mdl_course_index_rank_my
    $DB->execute('insert into mdl_course_index_rank_my (userid) select id from mdl_user where mdl_user.id not in (select userid from mdl_course_index_rank_my)');

    //3. 更新 complete_count
    $DB->execute('update mdl_course_index_rank_my set  mdl_course_index_rank_my.complete_count = (SELECT SUM(mdl_course_complete_rank_my .`complete_count`) FROM mdl_course_complete_rank_my where mdl_course_index_rank_my.userid= mdl_course_complete_rank_my.userid)');

    //4. 更新complete_time
    $DB->execute('update mdl_course_index_rank_my set  mdl_course_index_rank_my.complete_time = (SELECT MAX(mdl_course_complete_rank_my .`complete_time`) FROM mdl_course_complete_rank_my where mdl_course_index_rank_my.userid= mdl_course_complete_rank_my.userid)');

    //echo 'update index study rank done<br/>';

}