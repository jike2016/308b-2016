<script>
$('.lockpage').hide();
</script>

<?php

/** 单位台账》微阅统计 xdw */

require_once("../../config.php");
$orgid = optional_param('orgid', 0, PARAM_INT);//组织架构id
$start_time = optional_param('start_time', 1, PARAM_TEXT);//开始时间
$end_time = optional_param('end_time', 1, PARAM_TEXT);//结束时间 

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
			where roleid =14
		)
	');

    //单位浏览数
    $orgBrowsNum = browseNum($org->id,$start_time,$end_time) / (count($sumusers));
    $rank_browseNum["$org->name"] = $orgBrowsNum;
    //单位上传数
    $orgUpload = upload($org->id,$start_time,$end_time) / (count($sumusers));
    $rank_upload["$org->name"] = $orgUpload;
    //单位通过审查数
    $orgBrowsNum = passCheck($org->id,$start_time,$end_time) / (count($sumusers));
    $rank_passCheck["$org->name"] = $orgBrowsNum;
}

/** 排序 */
arsort($rank_browseNum);
arsort($rank_upload);
arsort($rank_passCheck);
//var_dump($rank_passCheck);

/** 输出排行榜 */
echo_org_browseNum($orgName,$rank_browseNum);//单位浏览数排行榜
echo_org_upload($orgName,$rank_upload);//单位上传数排行榜
echo_org_passCheck($orgName,$rank_passCheck);//单位通过审查排行榜


/**Start 获取单位浏览数 xdw */
function browseNum($orgid,$start_time,$end_time){
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
                            where roleid =14
                        )
                    )
		");

    return $count->count;
}
/**End 获取单位浏览数 */

/**Start 获取单位上传数 xdw */
function upload($orgid,$start_time,$end_time){
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
                                                    where roleid =14)
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
                                                    where roleid =14)
                                )
                                $sql_ebook
                        )
                    ) as table_new
		");

    return $count->count;
}
/**End 获取单位上传数 */

/**Start 获取单位通过审查数 xdw */
function passCheck($orgid,$start_time,$end_time){
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
                                                    where roleid =14)
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
                                                    where roleid =14)
                                )
                                and e.admin_check = 1
                                $sql_ebook
                        )
                    ) as table_new
		");

    return $count->count;
}
/**End 获取单位通过审查数 */


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
						<td>人均浏览数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_browseNum as $org=>$preCount){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$org.'</td>
				<td>'.$preCount.'</td>
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
						<td>人均上传数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_upload as $org=>$preCount){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$org.'</td>
				<td>'.$preCount.'</td>
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
						<td>人均通过审查数</td>
					</tr>
				</thead>
				<tbody>';
    $n=1;
    foreach($rank_passCheck as $org=>$preCount){
        $output .= '
			<tr>
				<td>'.$n.'</td>
				<td>'.$org.'</td>
				<td>'.$preCount.'</td>
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


