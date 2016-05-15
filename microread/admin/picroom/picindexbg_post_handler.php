<?php
/** Start cx 处理首页背景图上传*/
require_once('../lib/lib.php');
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
		case "edit"://编辑
			edit_indexbg();
			break;
	}
}
else{
	failure('操作失败');
}
function edit_indexbg(){
	
	$newbd=new stdClass();
	$newbd->id=1;
	$currenttime=time();
	$ranknum = rand(100, 200);//随机数
	if(isset($_FILES["picurl"])){
		if($_FILES["picurl"]["error"]>0){
			failure('上传图片失败');
			exit;
		}else{
			//判断上传类型是否是图片类型
			$picstr=strrchr($_FILES['picurl']['name'],'.');
			$picstr=strtolower($picstr);//全小写
			$picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
			if(in_array($picstr,$picmatch)) {
				move_uploaded_file($_FILES["picurl"]["tmp_name"],"../../../../microread_files/picture/index_ad/" . $currenttime.$ranknum.$picstr);
				//删除原来的文件
				global $DB;
				$oldbd = $DB->get_record_sql('select * from mdl_pic_indexbg where id=1');
				require_once('../convertpath.php');
				$picpath=convert_url_to_path($oldbd->indexbg_url);
				unlink($picpath);
				$newbd->indexbg_url = 'http://' . $_SERVER['HTTP_HOST'] . '/microread_files/picture/index_ad/' . $currenttime.$ranknum.$picstr;
				

			}
			else{
				failure('请上传正确格式的图片');
				exit;
			}
		}
	}
	$DB->update_record('pic_indexbg', $newbd);
	success('设置成功','picindexbg','closeCurrent');
}
?>

