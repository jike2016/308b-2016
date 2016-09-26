<script>
$('.lockpage').hide();
</script>

<?php

/** 单位台账》微阅统计 xdw */

require_once("../../config.php");
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id
$start_time = optional_param('start_time', 1, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 1, PARAM_TEXT);//结束时间

//$remove_role = '14,15';//需要移除的角色，14：单位角色 15：分权管理员角色
require_once('../comment_data.php');

global $DB;

$org=$DB -> get_record_sql('select name from mdl_org where id='.$orgid);
$orgName = $org->name;
//查询所有下级单位id
$sumorgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$orgid);

$rank_browseNum = array();//单位浏览数数组
$rank_upload = array();//单位上传数组
$rank_passCheck = array();//单位通过审查数组

foreach($sumorgs as $org){
    //分别查下级单位id的所有人,筛掉单位账号
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

    $totalPeople = get_totalPeople_org($org,$remove_role);//获取子单位的人员数

    //单位浏览数
//    $orgBrowsNum = browseNum($org->id,$start_time,$end_time) / (count($sumusers));
//    $rank_browseNum["$org->name"] = $orgBrowsNum;
    $totalBrows = browseNum_org($org,$start_time,$end_time,$remove_role);//总浏览数
    $orgBrowsNum = round($totalBrows/$totalPeople,2);//人均浏览数
    $rank_browseNum[] = array('orgName'=>$org->name,'totalPeople'=>$totalPeople,'totalBrows'=>$totalBrows,'avgcount'=>$orgBrowsNum);
    //单位上传数
//    $orgUpload = upload($org->id,$start_time,$end_time) / (count($sumusers));
//    $rank_upload["$org->name"] = $orgUpload;
    $totalUpload = upload_org($org,$start_time,$end_time,$remove_role);//总上传数
    $orgUpload =  round($totalUpload/$totalPeople,2);//人均上传数
    $rank_upload[] = array('orgName'=>$org->name,'totalPeople'=>$totalPeople,'totalUpload'=>$totalUpload,'avgcount'=>$orgUpload);
    //单位通过审查数
//    $orgBrowsNum = passCheck($org->id,$start_time,$end_time) / (count($sumusers));
//    $rank_passCheck["$org->name"] = $orgBrowsNum;
    $totalPassCheck = upload_org($org,$start_time,$end_time,$remove_role);//总通过审查数
    $orgPassCheck =  round($totalPassCheck/$totalPeople,2);//人均通过审查数
    $rank_passCheck[] = array('orgName'=>$org->name,'totalPeople'=>$totalPeople,'totalPassCheck'=>$totalPassCheck,'avgcount'=>$orgPassCheck);

}

/** 排序 */
//arsort($rank_browseNum);
//arsort($rank_upload);
//arsort($rank_passCheck);
sroce_multArray($rank_browseNum);
sroce_multArray($rank_upload);
sroce_multArray($rank_passCheck);
//var_dump($rank_browseNum);

/** 输出排行榜 */
echo_org_browseNum($orgName,$rank_browseNum);//单位浏览数排行榜
echo_org_upload($orgName,$rank_upload);//单位上传数排行榜
echo_org_passCheck($orgName,$rank_passCheck);//单位通过审查排行榜



/**Start 输出单位浏览数排行榜 xdw */
function echo_org_browseNum($orgName,$rank_browseNum){
    $output = '
		<div style="width:30%;float:left;margin-left:2%;text-align:center;">
			<div style="font-weight:600;">'.$orgName.':浏览数排行榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>总人数</td>
						<td>总浏览数</td>
						<td>人均浏览数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_browseNum as $item){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$item["orgName"].'</td>
				<td>'.$item["totalPeople"].'</td>
				<td>'.$item["totalBrows"].'</td>
				<td>'.$item["avgcount"].'</td>
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
/**End 输出单位浏览数排行榜  */

/**Start 输出单位上传排行榜 xdw */
function echo_org_upload($orgName,$rank_upload){
    $output = '
		<div style="width:30%;float:left;margin-left:3%;text-align:center;">
			<div style="font-weight:600;">'.$orgName.':上传数排行榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>总人数</td>
						<td>总上传数</td>
						<td>人均上传数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_upload as $item){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$item["orgName"].'</td>
				<td>'.$item["totalPeople"].'</td>
				<td>'.$item["totalUpload"].'</td>
				<td>'.$item["avgcount"].'</td>
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
/**End 输出单位上传数排行榜  */

/**Start 输出单位上传排行榜 xdw */
function echo_org_passCheck($orgName,$rank_passCheck){
    $output = '
		<div style="width:30%;float:left;margin-left:3%;text-align:center;">
			<div style="font-weight:600;">'.$orgName.':通过审查排行榜</div>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<td>排名</td>
						<td>单位名称</td>
						<td>总人数</td>
						<td>总上传数</td>
						<td>人均通过审查数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_passCheck as $item){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$item["orgName"].'</td>
				<td>'.$item["totalPeople"].'</td>
				<td>'.$item["totalPassCheck"].'</td>
				<td>'.$item["avgcount"].'</td>
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
/**End 输出单位上传数排行榜  */

/** Start 多维数组排序 */
function sroce_multArray(&$array){

    $order_array = array();
    foreach($array as $item){
        $order_array = $item['avgcount'];
    }
    array_multisort($order_array, SORT_DESC, $array);
}
/** end */

/** Start 获取当前子单位的人员数
 * @param $org 当前子单位对象
 * @return int $totalPeople 当前子单位的人员数
 */
function get_totalPeople_org($org,$remove_role){

    global $DB;
    $totalPeople = 0;//当前单位下的人员数
    //1、获取当前子单位下的人员数
    $totalPeopleArray = $DB -> get_records_sql('
		select a.user_id, b.firstname,b.lastname
		from mdl_org_link_user a
		join mdl_user b on b.id= a.user_id
		where org_id='.$org->id.' and a.user_id not in (
			select userid
			from mdl_role_assignments
			where roleid in ('.$remove_role.')
		)
	');
    $totalPeople = count($totalPeopleArray);

    //2、当前子单位的下级子单位
    $subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
    $subOrgPeople = 0;//各下级子单位的人数之和
    foreach($subOrgs as $subOrg){
        $subOrgPeople = $subOrgPeople + get_totalPeople_org($subOrg,$remove_role);//这里递归调用
    }

    $totalPeople = $totalPeople + $subOrgPeople;//当前单位下的人数 + 当前单位的下级子单位人数
    return $totalPeople;
}
/** end */

/**Start 获取单位浏览数 xdw */
function browseNum_org($org,$start_time,$end_time,$remove_role){

    global $DB;
    $totalCount = 0;//单位浏览数

    //按照时间段查询
    $sql = 'and m.timecreated > '.$start_time .' and m.timecreated < '.$end_time;
    //1、当前子单位的人员浏览数
    $count = $DB->get_record_sql("
                    select count(1) as count  from mdl_microread_log m
                    where m.action = 'view'
                    and m.target in (1,2,3)
                    $sql
                    and m.userid in (
                        select a.user_id
                        from mdl_org_link_user a
                        join mdl_user b on b.id= a.user_id
                        where org_id= $org->id and a.user_id not in (
                            select userid
                            from mdl_role_assignments
                            where roleid in ('.$remove_role.')
                        )
                    )
		");
    $count_org = $count->count;//当前单位下的人员浏览数

    //2、当前子单位的下级单位浏览数
    $count_subOrg = 0;//下级子单位浏览数
    $subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
    foreach($subOrgs as $subOrg){
        $count_subOrg = $count_subOrg + browseNum_org($subOrg,$start_time,$end_time,$remove_role);//递归调用
    }

    $totalCount = $count_org + $count_subOrg;//当前子单位下的人员浏览数 + 当前子单位的下级单位浏览数
    return $totalCount;
}
/**End 获取单位浏览数 */

/**Start 获取单位上传数 xdw */
function upload_org($org,$start_time,$end_time,$remove_role){

    global $DB;
    $totalCount = 0;//单位上传数

    //按照时间段查询
    $sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
    $sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
    $sql_pic = 'and p.timecreated > '.$start_time .' and p.timecreated < '.$end_time;
    //1、当前子单位的人员上传数
    $count = $DB->get_record_sql("
                    select sum(table_new.count) as count from (
                        (select count(1) as count from mdl_doc_user_upload_my d
                                where d.upload_userid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                $sql_doc
                        )
                        union all
                        (select count(1) as count from mdl_ebook_user_upload_my e
                                where e.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                $sql_ebook
                        )
                        union all
                        (select count(1) as count from mdl_pic_user_upload_my p
                                where p.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                $sql_pic
                        )
                    ) as table_new
		");
    $count_org = $count->count;

    //2、当前子单位的下级单位上传数
    $count_subOrg = 0;//下级子单位上传数
    $subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
    foreach($subOrgs as $subOrg){
        $count_subOrg = $count_subOrg + upload_org($subOrg,$start_time,$end_time,$remove_role);//递归调用
    }

    $totalCount = $count_org + $count_subOrg;//当前子单位下的人员上传数 + 当前子单位的下级单位上传数
    return $totalCount;
}
/**End 获取单位上传数 */

/**Start 获取单位通过审查数 xdw */
function passCheck_org($org,$start_time,$end_time,$remove_role){

    global $DB;
    $totalCount = 0;//单位通过审查数

    //按照时间段查询
    $sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
    $sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
    $sql_pic = 'and p.timecreated > '.$start_time .' and p.timecreated < '.$end_time;
    //1、当前子单位的人员通过审查数
    $count = $DB->get_record_sql("
                    select sum(table_new.count) as count from (
                        (select count(1) as count from mdl_doc_user_upload_my d
                                where d.upload_userid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                and d.admin_check = 1
                                $sql_doc
                        )
                        union all
                        (select count(1) as count from mdl_ebook_user_upload_my e
                                where e.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                and e.admin_check = 1
                                $sql_ebook
                        )
                        union all
                        (select count(1) as count from mdl_pic_user_upload_my p
                                where p.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $org->id
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                and p.admin_check = 1
                                $sql_pic
                        )
                    ) as table_new
		");
    $count_org = $count->count;

    //2、当前子单位的下级单位通过审查数
    $count_subOrg = 0;//下级子单位通过审查数
    $subOrgs = $DB -> get_records_sql('select id,name from mdl_org where parent='.$org->id);
    foreach($subOrgs as $subOrg){
        $count_subOrg = $count_subOrg + passCheck_org($subOrg,$start_time,$end_time,$remove_role);//递归调用
    }

    $totalCount = $count_org + $count_subOrg;//当前子单位下的人员通过审查数 + 当前子单位的下级单位通过审查数
    return $totalCount;
}
/**End 获取单位通过审查数 */

/**Start 获取单位浏览数 xdw （停用！） */
function browseNum($orgid,$start_time,$end_time,$remove_role){
    global $DB;
    //按照时间段查询
    $sql = 'and m.timecreated > '.$start_time .' and m.timecreated < '.$end_time;
    $count = $DB->get_record_sql("
                    select count(1) as count  from mdl_microread_log m
                    where m.action = 'view'
                    and m.target in (1,2)
                    $sql
                    and m.userid in (
                        select a.user_id
                        from mdl_org_link_user a
                        join mdl_user b on b.id= a.user_id
                        where org_id= $orgid and a.user_id not in (
                            select userid
                            from mdl_role_assignments
                            where roleid in ('.$remove_role.')
                        )
                    )
		");

    return $count->count;
}
/**End 获取单位浏览数 */

/**Start 获取单位上传数 xdw （停用！） */
function upload($orgid,$start_time,$end_time,$remove_role){
    global $DB;
    //按照时间段查询
    $sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
    $sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
    $count = $DB->get_record_sql("
                    select sum(table_new.count) as count from (
                        (select count(1) as count from mdl_doc_user_upload_my d
                                where d.upload_userid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $orgid
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                $sql_doc
                        )
                        union
                        (select count(1) as count from mdl_ebook_user_upload_my e
                                where e.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $orgid
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                $sql_ebook
                        )
                    ) as table_new
		");

    return $count->count;
}
/**End 获取单位上传数 */

/**Start 获取单位通过审查数 xdw （停用！） */
function passCheck($orgid,$start_time,$end_time,$remove_role){
    global $DB;
    //按照时间段查询
    $sql_doc = 'and d.timecreated > '.$start_time .' and d.timecreated < '.$end_time;
    $sql_ebook = 'and e.timecreated > '.$start_time .' and e.timecreated < '.$end_time;
    $count = $DB->get_record_sql("
                    select sum(table_new.count) as count from (
                        (select count(1) as count from mdl_doc_user_upload_my d
                                where d.upload_userid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $orgid
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                and d.admin_check = 1
                                $sql_doc
                        )
                        union
                        (select count(1) as count from mdl_ebook_user_upload_my e
                                where e.uploaderid in
                                (
                                            select a.user_id
                                                from mdl_org_link_user a
                                                join mdl_user b on b.id= a.user_id
                                                where org_id= $orgid
                                                and a.user_id not in (
                                                    select userid
                                                    from mdl_role_assignments
                                                    where roleid in ('.$remove_role.') )
                                )
                                and e.admin_check = 1
                                $sql_ebook
                        )
                    ) as table_new
		");

    return $count->count;
}
/**End 获取单位通过审查数 */


