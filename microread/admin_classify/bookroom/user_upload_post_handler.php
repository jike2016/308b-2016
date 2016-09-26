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
		case "pass"://添加电子书
			pass_ebook();	
			break;
		case "unpass"://编辑
			unpass_ebook();
			break;
	}
}
else{
	failure('操作失败');
}
function pass_ebook(){
	global $DB;
	//插入ebook_my表
	$user_ebook = $DB->get_record_sql('select * from mdl_ebook_user_upload_my where id='.$_GET['ebookid']);
	$newebook=new stdClass();
	$newebook->name= $user_ebook->name;
	$newebook->categoryid= -1;
	$newebook->authorid= -1;
	$newebook->summary= $user_ebook->summary;
	$newebook->url= $user_ebook->url;
	$newebook->pictrueurl= $user_ebook->pictrueurl;
	$newebook->timecreated=$user_ebook->timecreated;
	$newebook->wordcount= '';
	$newebook->suffix= $user_ebook->suffix;
	$newebook->size= $user_ebook->size;
	$newebook->uploaderid= $user_ebook->uploaderid;
	$DB->insert_record('ebook_my',$newebook,true);
	
	//更新表
	$newebook=new stdClass();
	$newebook->id= $_GET['ebookid'];
	$newebook->admin_check=1;
	$DB->update_record('ebook_user_upload_my', $newebook);
	/**Start cx 审核成功后跳到指定页面,第二个参数不能为空，否则会刷新2次， 20160723*/
//	success('操作成功','ebookuser_upload','');
	success('操作成功','null_value','forward','bookroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}
function unpass_ebook(){
	global $DB;
	$newebook=new stdClass();
	$newebook->id= $_GET['ebookid'];
	$newebook->admin_check=2;
	$DB->update_record('ebook_user_upload_my', $newebook);
	/**Start cx 审核成功后跳到指定页面 20160723*/
//	success('操作成功','ebookuser_upload','');
	success('操作成功','null_value','forward','bookroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}

?>

