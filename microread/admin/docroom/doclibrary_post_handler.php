<?php
/** 对文档的CURD  */
if(isset($_GET['title'])&&$_GET['title']){
    require_once('../../../config.php');
    switch($_GET['title']){
        case "add"://添加
            add_doclirary();
            break;
        case "edit"://编辑
            $newdoclibrary=new stdClass();
            $newdoclibrary->id= $_GET['ebookid'];
            $newdoclibrary->name= $_POST['name'];
            $newdoclibrary->categoryid= $_POST['categoryid'];
            $newdoclibrary->authorid= $_POST['author'];
            $newdoclibrary->summary= $_POST['summary'];
            $newdoclibrary->url= $_POST['url'];
            $newdoclibrary->pictrueurl= $_POST['pictrueurl'];
            $newdoclibrary->timecreated= time();
            $newdoclibrary->wordcount= $_POST['wordcount'];
            $newdoclibrary->suffix= 'txt';
            $newdoclibrary->size= '10MB';
            $DB->update_record('ebook_my', $newdoclibrary);
            success('修改成功','closeCurrent');
            break;
        case "delete"://删除
            $DB->delete_records("doclibrary_my", array("id" =>$_GET['ebookid']));
            success('删除成功','');
            break;

    }
}

function  add_doclirary(){
    if(isset($_FILES['pictrueurl'])){//上传图片
        if ($_FILES["pictrueurl"]["error"] > 0){
            failure('上传图片失败');
            exit;
        }
        else{
            $currenttime=time();
            move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/doclibrary/pictrueurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
            if ($_FILES["url"]["error"] > 0){//上传下载的文件
                failure('上传文件失败');
                exit;
            }
            else{
                global $USER;
                move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/doclibrary/doclibraryurl_fordownload/" . $currenttime.$_FILES["url"]["name"]);
                $newdoclibrary=new stdClass();
                $newdoclibrary->name= $_POST['name'];
                $newdoclibrary->categoryid= $_POST['categoryid'];
                $newdoclibrary->summary= $_POST['summary'];
                $newdoclibrary->authorid= $_POST['author'];
                $newdoclibrary->url= $_FILES["url"]["name"];
                $newdoclibrary->pictrueurl= $_FILES["pictrueurl"]["name"];
                $newdoclibrary->timecreated= $currenttime;
                $newdoclibrary->suffix= $_FILES["url"]["type"];
                $newdoclibrary->size= ($_FILES["url"]["size"] / 1024).'KB';
                $newdoclibrary->uploaderid= $USER->id;
                global $DB;
                $DB->insert_record('doc_my',$newdoclibrary,true);
                success('添加成功','closeCurrent');
            }
        }
    }
    else{
        // echo "Invalid file";
    }
}

function success($message,$callbackType){
    echo '{
		"statusCode":"200",
		"message":"'.$message.'",
		"navTabId":"ebook",
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


