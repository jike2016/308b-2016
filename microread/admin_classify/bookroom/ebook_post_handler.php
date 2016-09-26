<?php 
/** Start CX 处理电子书CURD*/
require_once('../lib/lib.php');
require_once("../../tagmylib.php");
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
		case "add"://添加电子书
			add_ebook();	
			break;
		case "edit"://编辑
			edit_ebook();
			break;
		case "delete"://删除
			delete_ebook();
			break;
	}
}
else{
	failure('操作失败');
}
function delete_relate($ebookid){
	global $DB;
	$DB->delete_records("ebook_comment_my", array("ebookid" =>$ebookid));
	$DB->delete_records("ebook_score_my", array("ebookid" =>$ebookid));
	$DB->delete_records("ebook_sumscore_my", array("ebookid" =>$ebookid));
	$DB->delete_records("ebook_user_read_my", array("ebookid" =>$ebookid));
	//删除章节信息
	$chapters=$DB->get_records('ebook_chapter_my',array("ebookid" =>$ebookid));
	foreach ($chapters as $chapter){
		$sections=$DB->get_records('ebook_section_my',array("chapterid" =>$chapter->id));
		foreach ($sections as $section){
			//删除相关pdf文件
			if($section->type==2){
				require_once('../convertpath.php');
				$filepath=convert_url_to_path($section->pdfurl);
				unlink($filepath);
			}
		}
		$DB->delete_records("ebook_section_my", array("chapterid" =>$chapter->id));


	}
	$DB->delete_records("ebook_chapter_my", array("ebookid" =>$ebookid));
}

function delete_ebook(){
	//先删除标签
	global $DB;
	update_delete_tagmy('mdl_ebook_my',$_GET['ebookid']);
	//删除相关数据表记录
	delete_relate($_GET['ebookid']);
	//删除该记录相关文件
	//删除电子书文件
	$deletetoebook=$DB->get_record_sql('select *from mdl_ebook_my where id='.$_GET['ebookid']);
	require_once('../convertpath.php');
	$filepath=convert_url_to_path($deletetoebook->url);
	unlink($filepath);
	//删除电子书封面
	$picpath=convert_url_to_path($deletetoebook->pictrueurl);
	if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/booklogo_default.jpg')
	unlink($picpath);
	$DB->delete_records("ebook_my", array("id" =>$_GET['ebookid']));
	success('删除成功','ebook','');
}
function edit_ebook(){
	$currenttime=time();
	$ranknum = rand(100, 200);//随机数
	$newebook=new stdClass();
	$newebook->id= $_GET['ebookid'];
	$newebook->name= $_POST['name'];
	$newebook->categoryid= $_POST['categoryid'];
	$newebook->authorid= $_POST['authorid'];
	$newebook->summary= $_POST['summary'];
	$newebook->wordcount= $_POST['wordcount'];
	//根据id获取该条记录的相关数据，如果图片更改，则删除之前图片，如果文件更改，则删除之前文件
	global $DB;
	$updateebook=$DB->get_record_sql('select *from mdl_ebook_my where id='.$_GET['ebookid']);
	require_once('../convertpath.php');
	if(isset($_FILES['pictrueurl'])){//上传图片
		if ($_FILES["pictrueurl"]["error"] > 0){
			// failure('上传图片失败');
			// exit;
		}
		else{
			//判断上传类型是否是图片类型
			$picstr=strrchr($_FILES['pictrueurl']['name'],'.');
			$picmatch=array('.gif','.jpeg','.png','.jpg');
			if(in_array($picstr,$picmatch)){
				$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
				$picfilestr=strtolower($picfilestr);//全小写
				move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/ebook/pictrueurl/" . $currenttime.$ranknum.$picfilestr);
				//start zxf 图片加水印
				require_once('../water.php');
				img_water_mark('../../../../microread_files/ebook/pictrueurl/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
				//end zxf 图片加水印
				$newebook->pictrueurl= '/microread_files/ebook/pictrueurl/'. $currenttime.$ranknum.$picfilestr;
			// start zxf 2016/5/11 修改电子书，有图片上传，删除之前的封面
				
				$picpath=convert_url_to_path($updateebook->pictrueurl);
				if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/booklogo_default.jpg')
					unlink($picpath);
				// end zxf 2016/5/11 修改电子书，有图片上传，删除之前的封面
			}
			else{
				failure('请上传正确格式的图片');
				exit;
			}
		}
	}
	if(isset($_FILES['url'])){//上传下载的文件
		if ($_FILES["url"]["error"] > 0){
			// failure('上传文件失败');
			// exit;
		}
		else{//判断上传文件是否为txt,pdf,rar,zip格式
			$urlfilestr=strrchr($_FILES['url']['name'],'.');//url后缀名
			$urlfilestr=strtolower($urlfilestr);
			$ebookmatch=array('.txt','.pdf','.rar','.zip');
			if(in_array($urlfilestr,$ebookmatch)){
				
				move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/ebook/ebookurl_fordownload/" . $currenttime.$ranknum.$urlfilestr);
				$newebook->url= '/microread_files/ebook/ebookurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
				$newebook->suffix= $urlfilestr;
				$newebook->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
			// start zxf 2016/5/11 修改电子书，有新电子书上传，删除之前的电子书
				$filepath=convert_url_to_path($updateebook->url);
				unlink($filepath);
				// end zxf 2016/5/11 修改电子书，有新电子书上传，删除之前的电子书
			}
			else{
				 failure('请上传正确格式的电子书');
				 exit;
			}
		}
	}
	global $DB;
	$DB->update_record('ebook_my', $newebook);
	//处理标签
	if(isset($_POST['tagmy'])){
		update_edit_tagmy($_POST['tagmy'],'mdl_ebook_my',$_GET['ebookid']);
	}
	else{
		update_edit_tagmy(array(),'mdl_ebook_my',$_GET['ebookid']);
	}
	success('修改成功','ebook','closeCurrent');
}

function add_ebook(){
	$currenttime = time();
	if(isset($_FILES['pictrueurl'])){//上传图片
		if ($_FILES["pictrueurl"]["error"] > 0){
//			failure('上传图片失败');
			$picuploadtag=true;
//			exit;
		}
		else {//判断上传类型是否是图片类型
			$picuploadtag=false;
			$picfilestr = strrchr($_FILES['pictrueurl']['name'], '.');//pic后缀名
			$picfilestr = strtolower($picfilestr);//全小写
			$picmatch = array('.gif', '.jpeg', '.png', '.jpg');
			if (in_array($picfilestr, $picmatch)) {
				$ranknum = rand(100, 200);//随机数

				move_uploaded_file($_FILES["pictrueurl"]["tmp_name"], "../../../../microread_files/ebook/pictrueurl/" .$currenttime . $ranknum . $picfilestr);
				//start zxf 图片加水印
				require_once('../water.php');
				img_water_mark('../../../../microread_files/ebook/pictrueurl/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
				//end zxf 图片加水印
			} else {
				failure('请上传正确格式的图片');
				exit;
			}
		}
	}
	else{
		$picuploadtag=true;
	}
	if(isset($_FILES['url'])){
		if ($_FILES["url"]["error"] > 0){//上传下载的文件
			failure('上传文件失败');
			exit;
		}
		else{
			$urlfilestr=strrchr($_FILES['url']['name'],'.');//url后缀名
			$urlfilestr=strtolower($urlfilestr);
			$ebookmatch=array('.txt','.pdf','.rar','.zip');
			if(in_array($urlfilestr,$ebookmatch)){
				$urlfilestr=strrchr($_FILES['url']['name'],'.');//url后缀名
				$urlfilestr=strtolower($urlfilestr);
				move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/ebook/ebookurl_fordownload/" . $currenttime.$ranknum.$urlfilestr);
			}
			else{
				failure('请上传正确格式的电子书');
				exit;
			}
			$newebook=new stdClass();
			$newebook->name= $_POST['name'];
			$newebook->categoryid= $_POST['categoryid'];
			$newebook->authorid= $_POST['authorid'];
			$newebook->summary= $_POST['summary'];
			$newebook->url= '/microread_files/ebook/ebookurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
			//判断图片是否上传 $picuploadtag false-上传 true-没有上传
			if($picuploadtag){
				$newebook->pictrueurl='/moodle/microread/img/booklogo_default.jpg';
			}
			else{
				$newebook->pictrueurl= '/microread_files/ebook/pictrueurl/'. $currenttime . $ranknum.$picfilestr;
			}
			$newebook->timecreated= $currenttime;
			$newebook->wordcount= $_POST['wordcount'];
			$newebook->suffix= $urlfilestr;
			$newebook->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
			global $USER;
			$newebook->uploaderid = $USER->id;
			global $DB;
			$ebookid=$DB->insert_record('ebook_my',$newebook,true);
			//处理标签
			if(isset($_POST['tagmy'])){
				update_add_tagmy($_POST['tagmy'],'mdl_ebook_my',$ebookid);
			}
			success('添加成功','ebook','closeCurrent');
		}
	}
	else{
		failure('请上传电子书');
		exit;
	}

}
?>

