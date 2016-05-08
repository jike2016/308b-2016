<?php 
/** Start CX 处理电子书CURD*/
require_once('../lib/lib.php');
require_once("../../tagmylib.php");
/**获取上传的文件，并转存路径 */
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "pass"://添加电子书
			pass_doc();	
			break;
		case "unpass"://编辑
			unpass_doc();
			break;
	}
}
else{
	failure('操作失败');
}
function pass_doc(){
	global $DB;
	//插入doc_my表
	$user_doc = $DB->get_record_sql('select * from mdl_doc_user_upload_my where id='.$_GET['docid']);
	$newdoc=new stdClass();
	$newdoc->name= $user_doc->name;
	$newdoc->categoryid= -1;
	$newdoc->summary= $user_doc->summary;
	$newdoc->url= $user_doc->url;
	$newdoc->pictrueurl= $user_doc->pictrueurl;
	$newdoc->timecreated=$user_doc->timecreated;
	$newdoc->suffix= $user_doc->suffix;
	$newdoc->size= $user_doc->size;
	$newdoc->uploaderid= $user_doc->upload_userid;
	$DB->insert_record('doc_my',$newdoc,true);
	
	//更新表
	$newdoc=new stdClass();
	$newdoc->id= $_GET['docid'];
	$newdoc->admin_check=1;
	$DB->update_record('doc_user_upload_my', $newdoc);
	
	success('操作成功','docuser_upload','');
}
function unpass_doc(){
	global $DB;
	$newdoc=new stdClass();
	$newdoc->id= $_GET['docid'];
	$newdoc->admin_check=2;
	$DB->update_record('doc_user_upload_my', $newdoc);
	success('操作成功','docuser_upload','');
}

?>

