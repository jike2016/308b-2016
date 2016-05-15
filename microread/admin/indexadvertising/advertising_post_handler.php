<?php
require_once('../lib/lib.php');
/** Start zxf 处理广告栏CURD*/
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
	switch ($_GET['title']){
		case "delete"://删除广告
			//start zxf 2016/5/11 广告修改，新图片上传 之前的图片 删除
			require_once('../convertpath.php');
			global $DB;
			$updateadvertising=$DB->get_record_sql('select * from mdl_microread_indexad_my where id='.$_GET['advertisingid']);
			$picpath=convert_url_to_path($updateadvertising->picurl);
			unlink($picpath);
			//start zxf 2016/5/11 广告修改，新图片上传 之前的图片 删除
			$newadvertising->id= $_GET['advertisingid'];
			$newadvertising->linkurl='';
			$newadvertising->picurl='';
			$DB->update_record('microread_indexad_my', $newadvertising);
			success('删除成功','advertising','');
			break;
		case "edit"://编辑广告
			$newadvertising=new stdClass();
			$newadvertising->id= $_GET['advertisingid'];
			$newadvertising->linkurl= $_POST['linkurl'];
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
						move_uploaded_file($_FILES["picurl"]["tmp_name"],"../../../../microread_files/index_advertising/" . $currenttime.$ranknum.$picstr);
						$newadvertising->picurl = 'http://' . $_SERVER['HTTP_HOST'] . '/microread_files/index_advertising/' . $currenttime.$ranknum.$picstr;
						//start zxf 2016/5/11 广告修改，新图片上传 之前的图片 删除
						require_once('../convertpath.php');
						global $DB;
						$updateadvertising=$DB->get_record_sql('select * from mdl_microread_indexad_my where id='.$_GET['advertisingid']);
						$picpath=convert_url_to_path($updateadvertising->picurl);
						unlink($picpath);
						//start zxf 2016/5/11 广告修改，新图片上传 之前的图片 删除
					}
					else{
						failure('请上传正确格式的图片');
						exit;
					}
				}
			}
			$DB->update_record('microread_indexad_my', $newadvertising);
			success('修改成功','advertising','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}


?>

