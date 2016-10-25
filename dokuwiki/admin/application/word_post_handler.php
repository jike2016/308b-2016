<?php 
/** Start CX 处理电子书CURD*/
require_once('../lib/lib.php');
/**获取上传的文件，并转存路径 */
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	/** CX 检查权限 */
	require_once('../../../config.php');
	require_once('../../../user/my_role_conf.class.php');//引入角色配置
	require_login();
	global $DB;
	global $USER;
	if($USER->id!=2){//判断是否是超级管理员
		$role_conf = new my_role_conf();
		//判断是否是慕课管理员
		$result = $DB->record_exists('role_assignments', array('roleid' => $role_conf->get_courseadmin_role(),'userid' => $USER->id));
		if(!$result){
			redirect(new moodle_url('/index.php'));
		}
	}
	/** End 检查权限*/
	switch ($_GET['title']){
		case "editcategory"://编辑
			edit_category();
			break;
	}
}
else{
	failure('操作失败');
}

function edit_category(){
	global $DB;
	$editword=new stdClass();
	$editword->id= $_GET['wordid'];
	$editword->categoryid= $_POST['categoryid'];
	if($editword->categoryid==0){
		failure('修改失败，请选择分类！');
		return;
	}
	$DB->update_record('dokuwiki_word_my', $editword);
	success('修改成功','word_manage','closeCurrent');
}

?>

