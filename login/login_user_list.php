<?php
/**
 * 今日登录用户显示页面.
 */

require('../config.php');

require_login();//要求登录
$PAGE->set_url('/login/login_user_list.php');
$PAGE->set_title('今日登录人员');
$PAGE->set_heading('今日登录人员');
$PAGE->set_pagelayout('loginuserlist');//设置layout

echo $OUTPUT->header();
echo show_login_users();//显示今日登录的用户
echo $OUTPUT->footer();

//------------------------------------------------
/** 显示登录的用户信息 */
function show_login_users(){

    global $DB;
    global $OUTPUT;
    $dayTimeStart = strtotime(date('Y-m-d',time()));
    $sql = 'select u.id,u.firstname,u.lastname,o.`name` as orgname from mdl_user u
            left join mdl_org_link_user ol on u.id = ol.user_id
            left join mdl_org o on ol.org_id = o.id
            where u.currentlogin >  '.$dayTimeStart.'
            ORDER BY u.currentlogin desc ';
    $loginUsers = $DB->get_records_sql($sql);
    $loginCount = count($loginUsers);

    $html = '<!--主板块-->
        <div class="main">
            <p class="learner_num"><a>今日登录人数：<span class="num">'.$loginCount.'</span></a></p>
            <!--学员列表-->
            <div class="learner_list">';
    $counter = 1;
    foreach($loginUsers as $loginUser){
        $userPic = $OUTPUT->user_picture($loginUser,array('link' => false,'visibletoscreenreaders' => false));
        if( $counter%5 == 1){
            $html .= '<!--行-->
                        <div class="list_row">';
        }
        $html .= '<div class="learner_card">
                            '.$userPic.'
                            <div class="learn_info">
                                <a href="#"><p class="name"> '.$loginUser->firstname.'</p></a>
                                <p class="partment"> '.$loginUser->orgname.'</p>
                            </div>
                        </div>';
        if( $counter%5 == 0){
            $html .= '</div>
                        <!--行-->';
        }
        $counter++;
    }
    if( $loginCount%5 != 0){
        $html .= '</div>
                        <!--行-->';
    }

    $html .= '</div>
            <!--学员列表 end-->
        </div>
        <!--主板块 end-->';

    return $html;
}