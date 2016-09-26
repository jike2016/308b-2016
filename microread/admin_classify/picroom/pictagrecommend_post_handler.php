<?php
/** Start zxf 处理推荐榜CURD*/
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
		case "edit"://编辑推荐
			$newrecommendtag=new stdClass();
			$newrecommendtag->id= $_GET['pictagrecommendid'];
			$newrecommendtag->name= $_POST['name'];
			$currenttime=time();
			$ranknum = rand(100, 200);//随机数
			if(isset($_FILES["picurl"])){
				if($_FILES["picurl"]["error"]>0){
//            failure('上传图片失败');
				}else{
					//判断上传类型是否是图片类型
					$picstr=strrchr($_FILES['picurl']['name'],'.');
					$picstr=strtolower($picstr);//全小写
					$picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
					if(in_array($picstr,$picmatch)) {
						move_uploaded_file($_FILES["picurl"]["tmp_name"],"../../../../microread_files/picture/recommendpicurl/" . $currenttime.$ranknum.$picstr);
						$newrecommendtag->picurl = '/microread_files/picture/recommendpicurl/' . $currenttime.$ranknum.$picstr;
					}
					else{
						failure('请上传正确格式的图片');
						exit;
					}
				}
			}
			//start zxf 2016/5/11 图片标签推荐榜修改，新图片上传 之前的图片 删除
			require_once('../convertpath.php');
			global $DB;
			$updatepictag=$DB->get_record_sql('select * from mdl_pic_recommended_search where id='.$_GET['pictagrecommendid']);
			$picpath=convert_url_to_path($updatepictag->picurl);
			unlink($picpath);
			//start zxf 2016/5/11 图片标签推荐榜修改，新图片上传 之前的图片 删除
			$DB->update_record('pic_recommended_search', $newrecommendtag);
			success('设置成功','pictagcommend','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}

?>

