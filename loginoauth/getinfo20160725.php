<?php
//更新列表
$allow_action_list = ["get_user_info","is_alive","get_org_name_by_user_id"];
function get_org_name_by_user_id()
{
	global $DB;
	if(isset($_GET["id"]) &&!empty($_GET["id"]))
	{
		$user_id=$_GET["id"];
		$result=$DB->get_records_sql("select t.name from mdl_org t inner join mdl_org_link_user t1 on t.id=t1.org_id where t1.user_id=$user_id");
		foreach($result as $key=>$value)
			$org_name=$value->name;
		echo $org_name;
	}
}