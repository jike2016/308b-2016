<?php
require('../config.php');

$allow_get_list=["username","firstname","lastname","email"];//这里限定可以获取的字段
if (isset($_GET['accesstoken']) && !empty($_GET['accesstoken']) && isset($_GET['field']) && !empty($_GET['field']))//field为获取的字段
{
	global $DB;
	$firstname = '';
	if(in_array($_GET['field'],$allow_get_list))
	{
		$accesstoken=$_GET['accesstoken'];
		$field=$_GET['field'];
		$result=$DB->get_records_sql("select t.`$field` from mdl_user as t inner join mdl_accesstoken as t1 on t.id=t1.user_id where t1.access_token='$accesstoken'");
		foreach($result as $key=>$value)
		{
			$firstname = $value->firstname;
		}
	}
	echo $firstname;
}