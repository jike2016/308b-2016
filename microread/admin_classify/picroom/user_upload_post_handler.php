<?php 
/** Start CX 处理电子书CURD*/
require_once('../lib/lib.php');
require_once("../../tagmylib.php");
/**获取上传的文件，并转存路径 */
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	require_once('../../../user/my_role_conf.class.php');
	/** 检查权限 */
	require_login();
	global $USER;
	global $DB;
	$role = new my_role_conf();
	if(!$DB->record_exists('role_assignments', array('userid'=>$USER->id,'roleid'=>$role->get_gradingadmin_role())) ){//$role->get_gradingadmin_role()获取分级管理员角色
		redirect(new moodle_url('/index.php'));
	}
	/** End 检查权限*/
	switch ($_GET['title']){
		case "pass"://添加图片
			pass_pic();	
			break;
		case "unpass"://编辑
			unpass_pic();
			break;
	}
}
else{
	failure('操作失败');
}
function pass_pic(){
	global $DB;
	//插入pic_my表
	$user_pic = $DB->get_record_sql('select * from mdl_pic_user_upload_my where id='.$_GET['picid']);
	$newpic=new stdClass();
	$newpic->name= $user_pic->name;
	$newpic->picurl= $user_pic->picurl;
	$newpic->timeuploaded=$user_pic->timecreated;
	$newpic->suffix= $user_pic->suffix;
	$newpic->size= $user_pic->size;
	$newpic->uploaderid= $user_pic->uploaderid;
	$DB->insert_record('pic_my',$newpic,true);
	
	//更新表
	$newpic=new stdClass();
	$newpic->id= $_GET['picid'];
	$newpic->admin_check=1;
	$DB->update_record('pic_user_upload_my', $newpic);
	/**Start cx 审核成功后跳到指定页面,第二个参数不能为空，否则会刷新2次， 20160723*/
//	success('操作成功','picuser_upload','');
	success('操作成功','null_value','forward','picroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}
function unpass_pic(){
	global $DB;
	$newpic=new stdClass();
	$newpic->id= $_GET['picid'];
	$newpic->admin_check=2;
	$DB->update_record('pic_user_upload_my', $newpic);
	/**Start cx 审核成功后跳到指定页面,第二个参数不能为空，否则会刷新2次， 20160723*/
//	success('操作成功','picuser_upload','');
	success('操作成功','null_value','forward','picroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}

?>

