<?php
/** 课程学习排名 徐东威 20160407 */

require_once($CFG->dirroot.'/enrol/locallib.php');

/**
 * Start 分析每门课程的学习排名
 */
function course_rank(){
    global $DB;

    $courses = $DB->get_records_sql("select * from mdl_course where id != 1");

    foreach($courses as $course){

        $rankcomplete = my_get_rank($course);//获取课程中选课学生的课程活动完成数

        $userNow = array();//当前选课的用户
        foreach($rankcomplete as $rank){
            $info = new stdClass();
            $info->userid = $rank['userid'];
            $info->courseid = $course->id;
            $info->complete_count = $rank['completeNum'];
            $info->complete_time = $rank['time'];
            //$info->grede = ;
            $userNow[] = $rank['userid'];

            $record = $DB->get_record_sql("select * from mdl_course_complete_rank_my where userid = $info->userid and courseid = $info->courseid");
            if($record){
                //更新原来的记录
                $index = $DB->update_record('course_complete_rank_my', array('id'=>$record->id,'userid'=>$info->userid, 'courseid'=>$info->courseid,'complete_count'=>$info->complete_count,'complete_time'=>$info->complete_time));
            }else{
                //插入数据
                $index = $DB->insert_record("course_complete_rank_my",$info,true);
            }

        }

        del_record($userNow,$course->id);//删除退课的学生记录

    }
}
/** End */

/**Start 删除退课的学生记录  */
function del_record($userNow,$courseID){

    global $DB;
    if(count($userNow)==0){
        $select = "courseid = $courseID";
        $result = $DB->delete_records_select("course_complete_rank_my",$select);
    }else{
        $user = implode(',',$userNow);
        $select = "courseid = $courseID and userid not in ($user)";
        $result = $DB->delete_records_select("course_complete_rank_my",$select);
    }
}
/**End  */

/** Start 获取课程的学习排名
 * @param $course 课程
 */
function my_get_rank($course){

    $search  = optional_param('search', '', PARAM_RAW);
    $role    = optional_param('role', 0, PARAM_INT);
    $fgroup  = optional_param('filtergroup', 0, PARAM_INT);
    $status  = optional_param('status', -1, PARAM_INT);
    $filter  = optional_param('ifilter', 0, PARAM_INT);

    global $PAGE;
    $manager = new course_enrolment_manager($PAGE, $course, $filter, $role, $search, $fgroup, $status);
    $users = $manager->get_users('lastname', 'ASC', 0, 10000);//获取选课的所有学生
    $rankcomplete = array();//用户的课程完成活动数量、完成时间

    if(count($users)){
        foreach($users as $user){
            $completeResult = my_get_courseCompeleteRate($course->id,$user);//获取用户的课程完成活动情况
            $completeNum = $completeResult->completeNum;//完成数量
            $time = $completeResult->time;//时间
            $rankcomplete[] = array('userid'=>$user->id,'completeNum'=>$completeNum,'time'=>$time);
        }
    }
    return $rankcomplete;

}
/** End */

///**Start 获取用户最近登录该课程的时间 徐东威 20160327     */
//function my_get_loginCourseTime($courseID,$user){
//    global $DB;
//    $logintime = $DB->get_record_sql("select ul.timeaccess from mdl_user_lastaccess ul where ul.userid = $user->id and ul.courseid = $courseID ");
//    return $logintime->timeaccess;
//}
///** End */


/**Start 课程完成数量 徐东威 20160310
 * @param	$courseID 课程
 * @param  $user 学生
 * @return  $completeResult 课程活动完成情况
 */
function my_get_courseCompeleteRate($courseID,$USER){

    global $DB;

    $completeNum = 0;//课程活动完成数量
    $completeTime = 0;//最新活动的完成时间
    //获取课程的进度跟踪启停状态 enablecompletion = 1 为开启状态
    $openState = $DB->get_record_sql("select c.enablecompletion from mdl_course c where c.id = $courseID ");
    //如果课程开启了活动
    if($openState->enablecompletion == 1 ){
        //开启了进度的活动数，其中除去那些不设为进度跟踪的活动（处理方式：如果该课程有活动，但没有一个是设置为进度跟踪的，那么就让其显示为‘无统计’）
        $activeCount = $DB->get_record_sql("select count(*) as count from mdl_course_modules cm  where cm.course = $courseID and cm.`completion` in (1,2) ");
        //如果设置有开启进度的活动，则求完成率
        if($activeCount->count != 0){
            //完成的活动数
            $completeCount = $DB->get_records_sql("select  * from mdl_course_modules_completion cmc
												where cmc.userid = $USER->id
												and cmc.coursemoduleid in (select cm.id from mdl_course_modules cm  where cm.course = $courseID and cm.`completion` in (1,2) )
												and cmc.completionstate = 1
												order by cmc.timemodified desc");

            $completeNum = count($completeCount);//求完成活动数量
            if($completeNum){
                foreach($completeCount as $complete){
                    $completeTime = $complete->timemodified;
                    break;
                }
            }
        }
    }
    //不考虑由管理员手动设置完成的情况
    $completeResult = new stdClass();
    $completeResult->completeNum = $completeNum;
    $completeResult->time = $completeTime;

    return $completeResult;
}
/** End */


