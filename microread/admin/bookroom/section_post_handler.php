<?php
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
            $newsection=new stdClass();
            $newsection->name= $_POST['name'];
            $newsection->chapterid= $_POST['chapterid'];
            $newsection->sectionorder= $_POST['sectionorder'];
            $newsection->type= $_POST['type'];
            global $DB;
            $sql='select * from mdl_ebook_section_my as a where a.sectionorder='. $_POST['sectionorder'].' and a.chapterid='.$newsection->chapterid;
            $existid=$DB->get_records_sql($sql);
            if($existid!=null) {
                failure('该序号已存在');
            }
            else {
                if($_POST['type']==1) {
                    $newsection->text= $_POST['text'];
                    $newsection->pdfurl='';
                }
                else if($_POST['type']==2){
                    if(isset($_FILES['pdf_url'])){//上传图片
                        if ($_FILES["pdf_url"]["error"] > 0){
                            // failure('上传图片失败');
                            // exit;
                        }
                        else{
                            $newsection->text='';
                            $currenttime=time();
							$ranknum = rand(100, 200);//随机数
							$pdffilestr=strrchr($_FILES['pdf_url']['name'],'.');//pic后缀名
							$pdffilestr=strtolower($pdffilestr);//全小写
                            move_uploaded_file($_FILES["pdf_url"]["tmp_name"],"../../../../microread_files/ebook/onlinereadbook_pdf/" .$currenttime.$ranknum.$pdffilestr);
                            $newsection->pdfurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/onlinereadbook_pdf/'.$currenttime.$ranknum.$pdffilestr;
                        }
                    }
                }

                $DB->insert_record('ebook_section_my',$newsection,true);
                success('添加成功','closeCurrent');
            }

            break;
        case "edit"://编辑分类
            $newsection=new stdClass();
            $newsection->id= $_GET['sectionid'];
            $newsection->name= $_POST['name'];
            $newsection->chapterid= $_POST['chapterid'];
            $newsection->sectionorder= $_POST['sectionorder'];
            $newsection->type= $_POST['type'];
            /**strat zxf 排除自己查重名**/
            $mycategory=$DB->get_record_sql('select * from mdl_ebook_section_my where id='.$newsection->id);
            if($mycategory->sectionorder!=$_POST['sectionorder'])
            {
                $sql='select * from mdl_ebook_section_my as a where a.chapterid="'.$_POST['chapterid'].'" and a.sectionorder="'.$_POST['sectionorder'].'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该序号已存在');
                }
                else
                {
                    if($_POST['type']==1) {
                        $newsection->text= $_POST['text'];
                        $newsection->pdfurl='';
                    }
                    else if($_POST['type']==2){
                        if(isset($_FILES['pdf_url'])){//上传图片
                            if ($_FILES["pdf_url"]["error"] > 0){
                                // failure('上传图片失败');
                                // exit;
                            }
                            else{
                                $newsection->text='';
                                $currenttime=time();
								$ranknum = rand(100, 200);//随机数
								$pdffilestr=strrchr($_FILES['pdf_url']['name'],'.');//pic后缀名
								$pdffilestr=strtolower($pdffilestr);//全小写
                                move_uploaded_file($_FILES["pdf_url"]["tmp_name"],"../../../../microread_files/ebook/onlinereadbook_pdf/" .$currenttime.$ranknum.$pdffilestr);
                                $newsection->pdfurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/onlinereadbook_pdf/'.$currenttime.$ranknum.$pdffilestr;
                            }
                        }
                    }
                    $DB->update_record('ebook_section_my', $newsection);
                    success('修改成功','closeCurrent');
                }
            }
            else
            {
                if($_POST['type']==1) {
                    $newsection->text= $_POST['text'];
                    $newsection->pdfurl='';
                }
                else if($_POST['type']==2){
                    if(isset($_FILES['pdf_url'])){//上传图片
                        if ($_FILES["pdf_url"]["error"] > 0){
                            // failure('上传图片失败');
                            // exit;
                        }
                        else{
                            $newsection->text='';
                            $currenttime=time();
							$ranknum = rand(100, 200);//随机数
							$pdffilestr=strrchr($_FILES['pdf_url']['name'],'.');//pic后缀名
							$pdffilestr=strtolower($pdffilestr);//全小写
                            move_uploaded_file($_FILES["pdf_url"]["tmp_name"],"../../../../microread_files/ebook/onlinereadbook_pdf/" .$currenttime.$ranknum.$pdffilestr);
                            $newsection->pdfurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/ebook/onlinereadbook_pdf/'.$currenttime.$ranknum.$pdffilestr;
                        }
                    }
                    else {
                        $newsection->text='';
                        $newsection->pdfurl=$_POST['temppdfurl'];
                    }
                }
                $DB->update_record('ebook_section_my', $newsection);
                success('修改成功','closeCurrent');
            }



            /**end zxf 排除自己查重名**/
            break;
        case "delete"://删除
            $DB->delete_records("ebook_section_my", array("id" =>$_GET['sectionid']));
            success('删除成功','');
            break;
    }
}
else{
    failure('操作失败');
}
function success($message,$callbackType){
    echo '{
		"statusCode":"200",
		"message":"'.$message.'",
		"navTabId":"ebooksection",
		"rel":"",
		"callbackType":"'.$callbackType.'",
		"forwardUrl":"",
		"confirmMsg":""
	}';
}
function failure($message){
    echo '{
		"statusCode":"300",
		"message":"'.$message.'",
		"navTabId":"",
		"rel":"",
		"callbackType":"",
		"forwardUrl":"",
		"confirmMsg":""
	}';
}

?>

