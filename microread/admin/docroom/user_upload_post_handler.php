<?php 
/** Start CX 处理电子书CURD*/
require_once('../lib/lib.php');
require_once("../../tagmylib.php");
require_once('../../convertlib.php');
/**获取上传的文件，并转存路径 */
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	/** CX 检查权限 */
	require_login();
	global $USER;
	if($USER->id!=2){//超级管理员
		global $DB;
		if(!$DB->record_exists('role_assignments', array('userid'=>$USER->id,'roleid'=>11)) ){//没有role=11角色
			redirect(new moodle_url('/index.php'));
		}
	}
	/** End 检查权限*/
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
	$currenttime = time();
	$ranknum = rand(100, 200);//随机数
	/**START cx 上传的文档转swf*/
	$documentroot = $_SERVER['DOCUMENT_ROOT'];   // 文档的服务器绝对路径
	$filename=strrchr($user_doc->url,'/');
	$filename=substr($filename, 1);
	$filepath=$documentroot.'/microread_files/doclibrary/user_upload/docfordownload/'. $filename;
	$pdf_filepath = $documentroot.'/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf';
	$swf_filepath = $documentroot.'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
	if(in_array($user_doc->suffix,array('.doc','.docx','.ppt','.pptx','.xls','.xlsx'))){
		word2swf_linux($filepath,$pdf_filepath ,$swf_filepath);
	}
	elseif(in_array($user_doc->suffix,array('.pdf'))){
		pdf2swf_linux($filepath,$swf_filepath);
	}
	elseif(in_array($user_doc->suffix,array('.txt'))){
		$txt_outputpath = $documentroot.'/microread_files/doclibrary/txtfile/'.$currenttime.$ranknum.'.txt';
		txt2swf_linux($filepath,$txt_outputpath, $pdf_filepath, $swf_filepath);
	}
	$newdoc->swfurl='http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
	/**End*/
	$DB->insert_record('doc_my',$newdoc,true);
	
	//更新表
	$newdoc=new stdClass();
	$newdoc->id= $_GET['docid'];
	$newdoc->admin_check=1;
	$DB->update_record('doc_user_upload_my', $newdoc);
	

	/**Start cx 审核成功后跳到指定页面,第二个参数不能为空，否则会刷新2次， 20160723*/
//	success('操作成功','docuser_upload','');
	success('操作成功','null_value','forward','docroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}
function unpass_doc(){
	global $DB;
	$newdoc=new stdClass();
	$newdoc->id= $_GET['docid'];
	$newdoc->admin_check=2;
	$DB->update_record('doc_user_upload_my', $newdoc);
	/**Start cx 审核成功后跳到指定页面,第二个参数不能为空，否则会刷新2次， 20160723*/
//	success('操作成功','docuser_upload','');
	success('操作成功','null_value','forward','docroom/user_upload.php?pageNum='.$_GET['pageNum']);
	/**End cx 审核成功后跳到指定页面 20160723*/
}

?>

