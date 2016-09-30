<?php
/**
 * 记录网站登录数及当天登录人数
 * Created by PhpStorm.
 * User: fubo_01
 * Date: 2016/9/29
 * Time: 17:44
 */

/**
 * 记录累计登录数，及当日登录人数
 */
function set_login_count(){
    global $DB;
    $login_info = $DB->get_record_sql("select * from mdl_index_login_count");
    if(time() > ($login_info->starttime + 86400)){//第二天开始统计当天登录人数
        $dayTimeStart = strtotime(date('Y-m-d',time()));
        $newid = $DB->update_record("index_login_count",array("id"=>1,"totallogincount"=>$login_info->totallogincount+1,"todaylogincount"=>1,"starttime"=>$dayTimeStart));
    }else{
        $sql = "select count(1) as count from mdl_user u where u.currentlogin > $login_info->starttime ORDER BY u.currentlogin desc";
        $todayCount = $DB->get_record_sql($sql);
        $newid = $DB->update_record("index_login_count",array("id"=>1,"totallogincount"=>$login_info->totallogincount+1,"todaylogincount"=>$todayCount->count));
    }
}

/**
 *获取登录数和今日登录人数
 */
function get_login_count_info(){
    global $DB;
    $login_info = $DB->get_record_sql("select * from mdl_index_login_count");
    return $login_info;
}

