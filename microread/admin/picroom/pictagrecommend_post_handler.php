<?php
/** Start zxf 处理推荐榜CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
	require_once('../../../config.php');
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
						$newrecommendtag->picurl = 'http://' . $_SERVER['HTTP_HOST'] . '/microread_files/picture/recommendpicurl/' . $currenttime.$ranknum.$picstr;
					}
					else{
						failure('请上传正确格式的图片');
						exit;
					}
				}
			}
			$DB->update_record('pic_recommended_search', $newrecommendtag);
			success('设置成功','pictagcommend','closeCurrent');
			break;
	}
}
else{
	failure('操作失败');
}

?>

