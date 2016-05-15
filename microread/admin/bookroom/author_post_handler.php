<?php
require_once('../lib/lib.php');
/** Start zxf 处理分类CURD*/
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
        case "add"://添加分类
			$newauthor=new stdClass();
			if(isset($_FILES['pictrueurl'])){//上传图片
				if ($_FILES["pictrueurl"]["error"] > 0){
					// failure('上传图片失败');
					// exit;
					$picuploadtag=true;
				}
				else{//判断上传类型是否是图片类型
					$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
					$picfilestr=strtolower($picfilestr);//全小写
					$picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
					if(in_array($picfilestr,$picmatch)){
						$currenttime=time();
						$ranknum = rand(100, 200);//随机数
						
						move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/ebook/authorpicurl/" .$currenttime.$ranknum.$picfilestr);

							}
					else{
						failure('请上传正确格式的图片');
						exit;
					}
				}
			}
			if($picuploadtag){
				$newauthor->pictrueurl='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/user_default.jpg';
			}
			else{
				$newauthor->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/authorpicurl/'.$currenttime.$ranknum.$picfilestr;
			}
            $newauthor->name= $_POST['name'];
			$newauthor->summary= $_POST['summary'];
            global $DB;
            $DB->insert_record('ebook_author_my',$newauthor,true);
            success('添加成功','ebookauthor','closeCurrent');
            break;
        case "edit"://编辑分类
            $newauthor=new stdClass();
			//修改作者，根据id获取该作者的基本信息，如果有新的头像上传，删除之前的非默认头像
			global $DB;
			$updateauthor=$DB->get_record_sql('select *from mdl_ebook_author_my where id='. $_GET['authorid']);
			if(isset($_FILES['pictrueurl'])){//上传图片
				if ($_FILES["pictrueurl"]["error"] > 0){
					// failure('上传图片失败');
					// exit;
				}
				else{//判断上传类型是否是图片类型
					$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
					$picfilestr=strtolower($picfilestr);//全小写
					$picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
					if(in_array($picfilestr,$picmatch)){
						$currenttime=time();
						$ranknum = rand(100, 200);//随机数
						
						move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/ebook/authorpicurl/" .$currenttime.$ranknum.$picfilestr);
						$newauthor->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/authorpicurl/'.$currenttime.$ranknum.$picfilestr;
						// start zxf 2016/5/11 修改作者，有图片上传，删除之前的头像
						require_once('../convertpath.php');
						$picpath=convert_url_to_path($updateauthor->pictrueurl);
						if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/user_default.jpg')
							unlink($picpath);
						// end zxf 2016/5/11 修改作者，有图片上传，删除之前的头像
					}
					else{
						failure('请上传正确格式的图片');
						exit;
					}
				}
			}
			$newauthor->id= $_GET['authorid'];
			$newauthor->name= $_POST['name'];
			$newauthor->summary= $_POST['summary'];
            $DB->update_record('ebook_author_my', $newauthor);
            success('修改成功','ebookauthor','closeCurrent');
            break;
        case "delete"://删除
			$ebooks = $DB->get_records_sql('select id from mdl_ebook_my where authorid='.$_GET['authorid']);
			foreach($ebooks as $ebook){
				$newebook=new stdClass();
				$newebook->id = $ebook->id;
				$newebook->authorid = -1;
				$DB->update_record('ebook_my', $newebook);
			}
			//查询将要删除作者的基本信息
			$deleteauthor=$DB->get_record_sql('select *from mdl_ebook_author_my where id='.$_GET['authorid']);
			require_once('../convertpath.php');
			//删除作者头像
			$picpath=convert_url_to_path($deleteauthor->pictrueurl);
			if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/user_default.jpg')
				unlink($picpath);
            $DB->delete_records("ebook_author_my", array("id" =>$_GET['authorid']));
            success('删除成功','ebookauthor','');
            break;
    }
}
else{
    failure('操作失败');
}


?>

