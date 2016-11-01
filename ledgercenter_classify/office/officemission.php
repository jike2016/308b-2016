<script>
$('.lockpage').hide();
</script>
<!--表格排序-->
<script type="text/javascript" src="js/my/sortTable.js"></script>
<!--表格排序-->
<script>
    $(document).ready(function(){
        //查看详细
        $('.detialLink').on('click',function(){
            var orgid = $(this).attr('orgid');
            var missionid = $(this).attr('missionid');
//            console.log(orgid);
//            console.log(missionid);
            //$('.table-box').load('office/officemissiondetial.php?orgid='+orgid+'&missionid='+missionid);
            window.open('office/officemissiondetial.php?orgid='+orgid+'&missionid='+missionid);
        });
    });
</script>

<?php
/** 单位台账》台账任务  徐东威  20160307*/

require_once("../../config.php");
$missionid = optional_param('missionid', 1, PARAM_INT);//任务id
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');

global $DB;
$mission = $DB->get_record_sql("select * from mdl_mission_my m where m.id = $missionid");//获取任务的详细信息
$missionName = $mission->mission_name;//任务名称
$org=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
$orgName = $org->name;

echo_missions_rank_list($orgid,$orgName,$remove_role,$missionid);//输出任务排行榜

exit;//*********************************************


//查询所有下级单位id
$sumorgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$orgid);
$rank_mission = array();//单位任务数组
foreach($sumorgs as $org){
    //分别查下级单位id的所有人,筛掉单位账号
    //    $sumusers = $DB -> get_records_sql('
    //		select a.user_id, b.firstname,b.lastname
    //		from mdl_org_link_user a
    //		join mdl_user b on b.id= a.user_id
    //		where org_id='.$org->id.' and a.user_id not in (
    //			select userid
    //			from mdl_role_assignments
    //			where roleid =14
    //		)
    //	');
    //任务榜处理，处理每个单位中人员的任务记录
    //$orgMission = handle_mission($org,$sumusers,$missionid);
    $orgMission = handle_mission_org($org,$mission,$remove_role);
    $rank_mission[$org->id] = array('name'=>$orgMission->name,'userCount'=>$orgMission->userCount,'requiredAvg'=>$orgMission->requiredAvg,'optionalAvg'=>$orgMission->optionalAvg);//将该单位的数据存入数组
}
$new_rank_mission = my_sort($rank_mission);//排序
echo_mission($new_rank_mission,$orgName,$missionName,$mission);//输出任务完成情况

//*******************************************************************************************



/**
 * 获取用户在某课程的进度
 * @param $UserStr
 * @param $courseid
 * @return array {用户id，课程进度，课程是否完成标志 }
 */
function get_course_criteria_completion_schedule($UserStr,$courseid){
    global $DB;
    $result = array();
    require_once("../lib/my_lib.php");
    $courseCriteria = get_course_module_criteria($courseid,1);//获取课程活动规则
    if(!$courseCriteria){//如果没设置任何活动规则
        $sql = "select ol.user_id as userid,0 as courseschedule,0 as completionflag
                                    from mdl_org_link_user ol
                                    WHERE ol.user_id  in ( $UserStr )";
    }else {
        $countCriteria = count($courseCriteria);//规则的数量
        $courseCriteriaStr = implode(',',$courseCriteria);
        $aggr = get_course_criteria_aggregration($courseid);//规则的组合方式
        if($aggr==1){//如果要完成全部
            $criteria_sqlStr = " select a.userid,(FORMAT((a.count/$countCriteria),2)*100) as courseschedule,IF((FORMAT((a.count/$countCriteria),2)*100)=100,1,0) as completionflag ";
        }elseif($aggr==2){//如果只需完成其一
            $criteria_sqlStr = " select a.userid,(IF(a.count=0,0,100)) as courseschedule,IF((IF(a.count=0,0,100)=100),1,0) as completionflag ";
        }
        $sql = " select temp.userid,MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag  from
                (
                    (   $criteria_sqlStr
                            from
                            (select c.userid,COUNT(1) as count
                                    from mdl_course_completion_crit_compl c
                                    where c.course = $courseid
                                    and c.criteriaid in ($courseCriteriaStr)
                                    and c.userid in ( $UserStr )
                                    GROUP BY c.userid ) as a
                            GROUP BY a.userid
                    )
                    UNION ALL -- 已完成（可能是设定人工设为完成）
                    (select a.userid,100 as courseschedule,1 as completionflag
                        from mdl_course_completions a
                        where a.course = $courseid
                        and a.userid in ( $UserStr )
                        and a.timecompleted != ''
                        GROUP BY a.userid
                     )
                ) as temp
                GROUP BY temp.userid";
    }
    $result = $DB->get_records_sql($sql);

    return $result;
}

/**
 * 获取人员任务课程完成情况
 * @param $UserStr  人员ids
 * @param $courseidStr  课程ids
 * @return array {用户id，必修课（或选修课）课程进度之和，完成的课程数}
 */
function get_course_completion_data2($UserStr,$courseidStr){
    $courseids = explode(',',$courseidStr);
    $result = array();
    foreach($courseids as $courseid){
        $schedule = get_course_criteria_completion_schedule($UserStr,$courseid);//获取单课程的进度
        //合并各课程进度
        foreach($schedule as $key=>$value){
            $result[$key]->userid = $value->userid;
            $result[$key]->sumcourseschedule += $value->courseschedule;
            $result[$key]->completioncount += $value->completionflag;
        }
    }

    return $result;
}


/**
 * 获取人员课程完成情况 （废弃）
 * @param $UserStr
 * @param $courseidStr
 * @return array
 */
function get_course_completion_data($UserStr,$courseidStr){
    global $DB;
    $sql = "select temp2.userid,SUM(temp2.courseschedule) as sumcourseschedule,SUM(temp2.completionflag) as completioncount from (
                    select MAX(temp.courseschedule) as courseschedule,MAX(temp.completionflag) as completionflag,temp.userid,temp.courseid  from
                            (	 -- 正常进度统计
                                (select (FORMAT((a.count/b.count),2)*100) as courseschedule,IF((FORMAT((a.count/b.count),2)*100)=100,1,0) as completionflag,a.userid,a.courseid
                                        from
                                        (select  count(*) as count,cmc.userid,cm.course as courseid
                                            from mdl_course_modules_completion cmc
                                            JOIN mdl_course_modules cm on cmc.coursemoduleid = cm.id
                                            where cm.course in ( $courseidStr )
                                            and cmc.userid in ( $UserStr )
                                            and cm.`completion` in (1,2)
                                            and cmc.completionstate = 1
                                            GROUP BY cmc.userid,cm.course ) as a,
                                        (select count(*) as count,cm.course as courseid
                                            from mdl_course_modules cm
                                            where cm.course in ( $courseidStr )
                                            and cm.`completion` in (1,2)
                                            GROUP BY cm.course) as b
                                        where a.courseid = b.courseid
                                        GROUP BY a.userid,a.courseid
                                )
                                UNION ALL -- 已完成（可能是设定人工设为完成）
                                (select 100 as courseschedule,1 as completionflag,a.userid,a.course as courseid
                                    from mdl_course_completion_crit_compl a
                                    where a.course in ( $courseidStr )
                                    and a.userid in ( $UserStr )
                                    GROUP BY a.userid,a.course
                                 )
                            ) as temp
                    GROUP BY temp.userid,temp.courseid
                    ORDER BY temp.userid
                    ) as temp2
                    GROUP BY temp2.userid";
    $result = $DB->get_records_sql($sql);
    return $result;
}
/**  end（废弃） */

/**Start 台账任务排行榜 xdw */
function echo_missions_rank_list($orgid,$orgname,$remove_role,$missionid){
    global $DB;
    require_once("../lib/my_lib.php");
    $UserStr = get_org_user_array($orgid,$remove_role,$type=1);//获取当前单位下的人员
    $UserStr = (!$UserStr) ? 0 : $UserStr;//判断是否有人员
    $sql = "SELECT m.id,m.mission_name,m.required_course_num,m.required_course_id,m.optional_course_num,m.optional_course_id,m.optional_choice_compeltions
                from mdl_mission_my m WHERE m.id = $missionid";
    $mission = $DB->get_record_sql($sql);

    //获取人员接受任务的情况
    $sql = "SELECT u.id,u.username,u.firstname,u.lastname,a.taskflag,b.orgname,b.orgid
                FROM mdl_user u
                LEFT JOIN -- 是否接受任务
                    (SELECT mum.user_id as userid,1 as taskflag
                    FROM mdl_mission_user_my mum
                    WHERE mum.mission_id = $missionid ) as a
                ON u.id = a.userid
                LEFT JOIN
                    (select o.`name` as orgname,o.id as orgid,ol.user_id as userid
                        from mdl_org_link_user ol
                        left join mdl_org o on o.id = ol.org_id
                        ) as b
                ON u.id = b.userid
                WHERE u.id IN ( $UserStr )";
    $users = $DB -> get_records_sql($sql);

    //获取必修课统计数据
    $requiredcourses = array();
    if($mission->required_course_num){
        $requirsecourseidStr = $mission->required_course_id;
//        $requiredcourses = get_course_completion_data($UserStr,$requirsecourseidStr);
        $requiredcourses = get_course_completion_data2($UserStr,$requirsecourseidStr);
    }

    //获取选修课统计数据
    $opletioncourses = array();
    if($mission->optional_course_num){
        $optioncourseidStr = $mission->optional_course_id;
//        $opletioncourses = get_course_completion_data($UserStr,$optioncourseidStr);
        $opletioncourses = get_course_completion_data2($UserStr,$optioncourseidStr);
    }

    //合并统计数据
    foreach($users as $user){
        if($user->taskflag==1){
            //必修课
            if($requiredcourses[$user->id]){
                $user->requiredcoursecount = $requiredcourses[$user->id]->completioncount;
                $user->requiredcourseschdule = $requiredcourses[$user->id]->sumcourseschedule;
            }

            //选修课
            if($opletioncourses[$user->id]){
                $user->optionalcoursecount = $opletioncourses[$user->id]->completioncount;
                $user->optionalcourseschdule = $opletioncourses[$user->id]->sumcourseschedule;
            }

            //任务进度
            //此处选修课的进度统计不够精确，应该是获取至少需要选修课程数量如：x 的前 x 门选修课最高纪录来求平均
            if($mission->required_course_num){
                $temprequired =  ($user->requiredcourseschdule/$mission->required_course_num) * ($mission->required_course_num/($mission->required_course_num + $mission->optional_choice_compeltions));
            }else{
                $temprequired = 100*($mission->required_course_num / ($mission->required_course_num + $mission->optional_choice_compeltions));;
            }
            if($mission->optional_choice_compeltions){
                if($user->optionalcoursecount<$mission->optional_choice_compeltions) {//选修课没有达到规定数量
                    $tempoptional = ($user->optionalcourseschdule / $mission->optional_course_num) * ($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
                }else{//满足规定数量
                    $tempoptional = 100*($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
                }
            }else{
                $tempoptional = 100*($mission->optional_choice_compeltions / ($mission->required_course_num + $mission->optional_choice_compeltions));
            }

            $tempschedule =  $temprequired+$tempoptional;
            if(!($mission->required_course_num || $mission->optional_choice_compeltions )){//必修课和选修课均为空
                $tempschedule = 100;
            }
            $user->taskschedule = round($tempschedule,0);

        }
    }

    //如果当前单位下有子单位，需要考虑将其二级以下的子单位数据合并到当前所属的二级单位
    if(count(get_second_level_org($orgid))){
        $users = merge_all_user_data_to_sub_org($orgid,$users,1);
    }else{
        $users = merge_all_user_data_to_sub_org($orgid,$users,0);
    }

    //<!-- 表格的排序用js 实现，注意表格加上 id="tblSort" 属性 -->
    $output = '
		<div  class="table_text_center one_table">
		<div class="table_title_lg" >'.$orgname.'：单位 任务排行榜（任务：'.$mission->mission_name.'）</div>
		<table class="table table-striped table-bordered" id="tblSort" >
			<thead>
				<tr>
					<td>排名</td>
					<td>单位</td>
					<td onclick="sortTable(\'tblSort\',2,\'string\');" style="cursor:pointer">接受任务人数</td>
					<td onclick="sortTable(\'tblSort\',3,\'string\');" style="cursor:pointer">人均 已完成/需完成 必修课</td>
					<td onclick="sortTable(\'tblSort\',4,\'string\');" style="cursor:pointer">人均 已完成/需完成 选修课</td>
					<td onclick="sortTable(\'tblSort\',5,\'my_string\');" style="cursor:pointer">任务完成进度</td>
					<td>详细</td>
				</tr>
			</thead>
			<tbody>';
    $n=1;
    foreach($users as $user){
        if(null_to_zero($user->taskflag)==0){
            $taskflagnew = '0';
            $required = '-';
            $optional = '-';
            $schedule = '-';
            $detialLink = '-';
        }else{
            $taskflagnew = $user->taskflag;
            $required = round(null_to_zero($user->requiredcoursecount / $user->taskflag),2).'/'.$mission->required_course_num;
            $optional = round(null_to_zero($user->optionalcoursecount / $user->taskflag),2).'/'.$mission->optional_choice_compeltions;
            $schedule = round(null_to_zero($user->taskschedule / $user->taskflag),2).'%';
//            $schedule = ($schedule==100)? '已完成':$schedule.'%';
            $detialLink = '<a href="#" class="detialLink" orgid="'.$user->id.'"  missionid="'.$missionid.'" >查看</a>';//通过jq获取点击对象
        }

        $output .=  '
			<tr>
				<td>'.$n.'</td>
				<td>'.$user->orgname.'</td>
				<td>'.$taskflagnew.'</td>
				<td>'.$required.'</td>
				<td>'.$optional.'</td>
				<td>'.$schedule.'</td>
				<td>'.$detialLink.'</td>
			</tr>
			';
        $n++;
    }
    $output .='
		</tbody>
	</table>
	</div>
	';
    echo $output;
}
/**end 台账任务排行榜 xdw */


/**
 * 将为空（null）的变量转为 0
 * @param $param 分析的变量
 * @return var|0
 */
function null_to_zero($param){
    return ($param==null)?0:$param;
}

/** start 输出任务完成情况 */
function echo_mission($rank_mission,$orgname,$missionName,$mission){

    $output = '
		<!-- <div style="width:35%;float:right;margin-right:10%;text-align:center;"> -->
		<div style="width:100%;margin-right:10%;text-align:center;">
			<div style="font-weight:600;">'.$orgname.' : '.$missionName.'排行榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>接受任务人数</td>
						<td>人均&nbsp;&nbsp;已完成/需完成&nbsp;&nbsp;必修课</td>
						<td>人均&nbsp;&nbsp;已完成/至少需完成&nbsp;&nbsp;选修课</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_mission as $org){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$org["name"].'</td>
				<td>'.$org["userCount"].'</td>
				<td>'.round($org["requiredAvg"],2).'/'.$mission->required_course_num.'</td>
				<td>'.round($org["optionalAvg"],2).'/'.$mission->optional_choice_compeltions.'</td>
			</tr>';
        $n++;
    }
    $output .= '
			</tbody>
		</table>
		</div>
	';
    echo $output;
}
/** end */

/** start名次排序 按照完成情况排序 */
function my_sort($rank_mission){

//    $count = array();//任务人数
    $requiredArray = array();//必修完成数
    $optionalArray = array();//选修完成数
    $sumArray = array();//必修、选修数之和
    foreach ($rank_mission as $obj) {
//        $count[] = $obj['userCount'];
        $requiredArray[] = $obj['requiredAvg'];
        $optionalArray[] = $obj['optionalAvg'];
        $sumArray[] = $obj['requiredAvg'] + $obj['optionalAvg'];
    }
    array_multisort($sumArray, SORT_DESC,$requiredArray, SORT_DESC, $optionalArray, SORT_DESC, $rank_mission);//排序：按照必修课数和选修课数排

    return $rank_mission;
}
/** end */

/** start 处理每个单位中人员的任务记录 （停用！）
 * @param  $sumusers 单位下的人员
 * @param  $missionid   任务id
 * @return object $missionObj  包含：单位名称 接受任务人数 平均完成必修课 平均完成选修课
 */
function handle_mission($org,$sumusers,$missionid){

    global $DB;

    $mission = $DB->get_record_sql("select * from mdl_mission_my m where m.id = $missionid ");
    $requiresCoursesID = explode(',',$mission->required_course_id);//必修课id
    $optionalCoursesID = explode(',',$mission->optional_course_id);//选修课id

    $users = array();//人员id数组
    foreach($sumusers as $sumuser){
        $users[] = $sumuser->user_id;
    }
    $usersStr = implode(',',$users);//生成例如 ：5,6,3,11 字符串

    //获取该单位下该任务的人员
    $missionUsers = array();
    if($usersStr != ''){
        $missionUsers = $DB->get_records_sql("select m.user_id,m.mission_id from mdl_mission_user_my m where m.mission_id = $missionid and m.user_id in ($usersStr)");
    }

    //计算完成课程的数量
    $requiredCount = 0;//该单位总完成的必修课数量
    $optionalCount = 0;//该单位总完成的选修课数量
    foreach($missionUsers as $missionUser){
        //必修课完成状态的判断
        foreach($requiresCoursesID as $requiresCourseID){
            $state1 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($requiresCourseID) and c.userid = $missionUser->user_id ");
            if($state1){
                $requiredCount++;//必修课完成数量加1
            }
        }
        //选修课完成状态的判断
        foreach($optionalCoursesID as $optionalCourseID){
            $state2 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($optionalCourseID) and c.userid = $missionUser->user_id ");
            if($state2){
                $optionalCount++;//选修课完成数量加1
            }
        }
    }

    //求平均完成数,只算选择该任务的人员
    $count = count($missionUsers);//人数
    if($count){ //人数不为零
        $requiredAvg = $requiredCount / $count;//平均必修课数量
        $optionalAvg = $optionalCount / $count;//平均选修课数量
    }else{
        $requiredAvg = 0;
        $optionalAvg = 0;
    }

    $missionObj = new stdClass();//单位任务对象
    $missionObj->name = $org->name;//单位名
    $missionObj->userCount = $count;//接受任务人数
    $missionObj->requiredAvg = $requiredAvg;//平均完成必修课数
    $missionObj->optionalAvg = $optionalAvg;//平均完成选修课数

    return $missionObj;
}
/** end */

/** start 处理每个子单位中人员的任务记录
 * @param  $org 子单位对象
 * @param  $mission   任务对象
 * @return object $missionObj  包含：单位名称 接受任务人数 平均完成必修课 平均完成选修课
 */
function handle_mission_org($org,$mission,$remove_role){

    global $DB;
    $requiresCoursesID = explode(',',$mission->required_course_id);//必修课id
    $optionalCoursesID = explode(',',$mission->optional_course_id);//选修课id

    //获取该单位下该任务的人员
    $missionUsers = get_missionPeople_org($org,$mission,$remove_role); //获取该单位下》该任务》相关人员

    //计算完成课程的数量
    $requiredCount = 0;//该单位总完成的必修课数量
    $optionalCount = 0;//该单位总完成的选修课数量
    foreach($missionUsers as $missionUser){
        //必修课完成状态的判断
        foreach($requiresCoursesID as $requiresCourseID){
            $state1 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($requiresCourseID) and c.userid = $missionUser->user_id ");
            if($state1){
                $requiredCount++;//必修课完成数量加1
            }
        }
        //选修课完成状态的判断
        foreach($optionalCoursesID as $optionalCourseID){
            $state2 = $DB->get_record_sql("select * from mdl_course_completion_crit_compl c where c.course in ($optionalCourseID) and c.userid = $missionUser->user_id ");
            if($state2){
                $optionalCount++;//选修课完成数量加1
            }
        }
    }

    //求平均完成数,只算选择该任务的人员
    $count = count($missionUsers);//人数
    if($count){ //人数不为零
        $requiredAvg = $requiredCount / $count;//平均必修课数量
        $optionalAvg = $optionalCount / $count;//平均选修课数量
    }else{
        $requiredAvg = 0;
        $optionalAvg = 0;
    }

    $missionObj = new stdClass();//单位任务对象
    $missionObj->name = $org->name;//单位名
    $missionObj->userCount = $count;//接受任务人数
    $missionObj->requiredAvg = $requiredAvg;//平均完成必修课数
    $missionObj->optionalAvg = $optionalAvg;//平均完成选修课数

    return $missionObj;
}
/** end */

/** start 获取子单位中接受该任务的人员
 * @param  $org 子单位对象
 * @param  $mission  任务对象
 * @return array $missionPeople 接受该任务的人员信息
 */
function get_missionPeople_org($org,$mission,$remove_role){

    $missionPeople = array();//该单位下》该任务》相关人员
    global $DB;
    //1、查当前子单位下的人员,筛掉单位账号
    $sumusers = $DB -> get_records_sql('
		select a.user_id, b.firstname,b.lastname
		from mdl_org_link_user a
		join mdl_user b on b.id= a.user_id
		where org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
	');

    $users = array();//人员id数组
    foreach($sumusers as $sumuser){
        $users[] = $sumuser->user_id;
    }
    $usersStr = implode(',',$users);//生成例如 ：5,6,3,11 字符串

    //获取该单位下》该任务》相关人员
    $missionPeople_Org = array();
    if($usersStr != ''){
        $missionPeople_Org = $DB->get_records_sql("select m.user_id,m.mission_id from mdl_mission_user_my m where m.mission_id = $mission->id and m.user_id in ($usersStr)");
    }

    //2、查当前子单位的下级单位
    $missionPeople_SubOrg = array();
    $subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
    foreach($subOrgs as $subOrg){
        $missionPeople_SubOrg_temp = get_missionPeople_org($subOrg,$mission,$remove_role);//递归调用
        $missionPeople_SubOrg = array_merge($missionPeople_SubOrg,$missionPeople_SubOrg_temp);//合并各下级单位数据
    }

    $missionPeople = array_merge($missionPeople_Org,$missionPeople_SubOrg);//合并当前单位 与 当前单位的下级单位数据
    return $missionPeople;
}
/** end */













