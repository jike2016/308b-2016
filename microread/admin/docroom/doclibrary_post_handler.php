<?php
/** 对文档的CURD  */
require_once('../lib/lib.php');
if(isset($_GET['title'])&&$_GET['title']){
    require_once('../../../config.php');
    switch($_GET['title']){
        case "add"://添加
            add_doclibrary();
            break;
        case "edit"://编辑
            edit_doclibrary();
            break;
        case "delete"://删除
            $DB->delete_records("doc_my", array("id" =>$_GET['doclibraryid']));
            success('删除成功','doclibrary','');
            break;

    }
}

//编辑
function edit_doclibrary(){

    $newdoclibrary=new stdClass();
    $newdoclibrary->id= $_GET['doclibraryid'];
    $newdoclibrary->name= $_POST['name'];
    $newdoclibrary->categoryid= $_POST['categoryid'];
    $newdoclibrary->summary= $_POST['summary'];
    $newdoclibrary->authorid= $_POST['authorid'];
    if(isset($_FILES["pictrueurl"])){
        if($_FILES["pictrueurl"]["error"]>0){
//            failure('上传图片失败');
        }else{
            $currenttime=time();
            move_uploaded_file($_FILES["pictrueurl"]["tmp_name"],"../../../../microread_files/doclibrary/pictrueurl/" . $currenttime.$_FILES["pictrueurl"]["name"]);
            $newdoclibrary->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/pictrueurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
        }
    }
    if(isset($_FILES["url"])){
        if($_FILES["url"]["error"]>0){
//            failure('上传文档失败');
        }else{
            $currenttime=time();
            move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/doclibrary/doclibraryurl_fordownload/" . $currenttime.$_FILES["url"]["name"]);
            $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$_FILES["url"]["name"];
            $newdoclibrary->suffix= $_FILES["url"]["type"];
            $newdoclibrary->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
        }
    }
//    $newdoclibrary->timecreated= time();//不改变其创建时间

    global $DB;
    $DB->update_record('doc_my', $newdoclibrary);
    success('修改成功','doclibrary','closeCurrent');
}

//添加
function  add_doclibrary(){
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
                $newdoclibrary->authorid= $_POST['authorid'];
                $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$_FILES["url"]["name"];
                $newdoclibrary->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/pictrueurl/'. $currenttime.$_FILES["pictrueurl"]["name"];
                $newdoclibrary->timecreated= $currenttime;
                $newdoclibrary->suffix= $_FILES["url"]["type"];
                $newdoclibrary->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
                $newdoclibrary->uploaderid= $USER->id;
                global $DB;
                $DB->insert_record('doc_my',$newdoclibrary,true);
                success('添加成功','doclibrary','closeCurrent');
            }
        }
    }
    else{
        // echo "Invalid file";
        failure('没有上传图片');
    }
}




