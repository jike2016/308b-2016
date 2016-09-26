<?php
/** 对文档的CURD  */
require_once('../lib/lib.php');
require_once('../../convertlib.php');
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
            delete_doclibrary();
            break;

    }
}

function edit_doclibrary(){

    $newdoclibrary=new stdClass();
    $newdoclibrary->id= $_GET['doclibraryid'];
    $newdoclibrary->name= $_POST['name'];
    $newdoclibrary->categoryid= $_POST['categoryid'];
    $newdoclibrary->summary= $_POST['summary'];
    if(isset($_FILES["pictrueurl"])){
        if($_FILES["pictrueurl"]["error"]>0){
//            failure('上传图片失败');
        }else{
            //判断上传类型是否是图片类型
            $picstr=strrchr($_FILES['pictrueurl']['name'],'.');
            $picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
            if(in_array($picstr,$picmatch)) {
                $currenttime = time();
				$ranknum = rand(100, 200);//随机数
				$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
				$picfilestr=strtolower($picfilestr);//全小写
                move_uploaded_file($_FILES["pictrueurl"]["tmp_name"], "../../../../microread_files/doclibrary/pictrueurl/" .$currenttime.$ranknum.$picfilestr);
                $newdoclibrary->pictrueurl = 'http://' . $_SERVER['HTTP_HOST'] . '/microread_files/doclibrary/pictrueurl/' . $currenttime.$ranknum.$picfilestr;
            }
            else{
                failure('请上传正确格式的图片');
                exit;
            }
         }
    }
    if(isset($_FILES["url"])){
        if($_FILES["url"]["error"]>0){
//            failure('上传文档失败');
        }else{//判断文档上传类型
            $docstr=strrchr($_FILES['url']['name'],'.');
            $docmatch=array('.doc','.docx','.ppt','.pptx','.xls','.xlsx','.pdf','.txt');
            if(in_array($docstr,$docmatch)) {
                $currenttime=time();
				$urlfilestr=strrchr($_FILES['url']['name'],'.');//url后缀名
				$urlfilestr=strtolower($urlfilestr);
                move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/doclibrary/doclibraryurl_fordownload/" . $currenttime.$ranknum.$urlfilestr);
                $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
                $newdoclibrary->suffix= $docstr;
                if(($_FILES["url"]["size"] / 1048576)<=0.1){
                    $newdoclibrary->size='0.1MB';
                }
                else{
                    $newdoclibrary->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
                }
            }
            else{
                failure('请上传正确格式的文档');
                exit;
            }
        }
    }
//    $newdoclibrary->timecreated= time();//不改变其创建时间

    global $DB;
    $newaddtagids=$_POST['tagmy'];
    require_once('doctagmylib.php');
    update_edit_doc_tagmy($newaddtagids,$_GET['doclibraryid']);
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
        else{//判断上传类型是否是图片类型
            $picstr=strrchr($_FILES['pictrueurl']['name'],'.');
            $picmatch=array('.gif','.jpeg','.png','.bmp','.jpg');
            if(in_array($picstr,$picmatch)) {
                $currenttime = time();
				$ranknum = rand(100, 200);//随机数
				$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
				$picfilestr=strtolower($picfilestr);//全小写
                move_uploaded_file($_FILES["pictrueurl"]["tmp_name"], "../../../../microread_files/doclibrary/pictrueurl/" .$currenttime.$ranknum.$picfilestr);
            }
            else{
                failure('请上传正确格式的图片');
                exit;
            }
            if ($_FILES["url"]["error"] > 0){//上传下载的文件
                failure('上传文件失败');
                exit;
            }
            else{
                
				$newdoclibrary=new stdClass();
                $newdoclibrary->name= $_POST['name'];
                $newdoclibrary->categoryid= $_POST['categoryid'];
                $newdoclibrary->summary= $_POST['summary'];
                //判断文档上传类型
                $urlfilestr=strrchr($_FILES['url']['name'],'.');
                $urlfilestr=strtolower($urlfilestr);
                $docmatch=array('.doc','.docx','.ppt','.pptx','.xls','.xlsx','.pdf','.txt');
                if(in_array($urlfilestr,$docmatch)) {
                    $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
                    $newdoclibrary->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/pictrueurl/'.$currenttime.$ranknum.$picfilestr;
                    $newdoclibrary->timecreated= $currenttime;
                    $newdoclibrary->suffix=$urlfilestr;
                    if(($_FILES["url"]["size"] / 1048576)<=0.1){
                        $newdoclibrary->size='0.1MB';
                    }
                    else{
                        $newdoclibrary->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
                    }

					move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/doclibrary/doclibraryurl_fordownload/" . $currenttime.$ranknum.$urlfilestr);
					/**START cx 上传的文档转swf*/
					if(in_array($urlfilestr,array('.doc','.docx','.ppt','.pptx','.txt','.xls','.xlsx'))){
						// word2pdf('http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
						// 'D:/WWW/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
						// 'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
						// word2swf_linux('/var/www/html/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
						// '/var/www/html/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
						// '/var/www/html/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
					}
					elseif(in_array($urlfilestr,array('.pdf'))){
						// pdf2swf('D:/WWW/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
						// 'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
						// pdf2swf_linux('/var/www/html/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
						// '/var/www/html/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
					}
					$newdoclibrary->swfurl='http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
					/**End*/
                }
                else{
                    failure('请上传正确格式的文档');
                    exit;
                }
                global $USER;
                $newdoclibrary->uploaderid= $USER->id;
                global $DB;
                $newid = $DB->insert_record('doc_my',$newdoclibrary,true);
				//处理标签
				$newaddtagids=$_POST['tagmy'];
                require_once('doctagmylib.php');
                update_add_doc_tagmy($newaddtagids,$newid);
                success('添加成功','doclibrary','closeCurrent');
            }
        }
    }
    else{
        // echo "Invalid file";
        failure('没有上传图片');
    }
}

function  delete_doclibrary(){
     global $DB;
    require_once('doctagmylib.php');
    update_delete_tagmy_by_docid($_GET['doclibraryid']);
    //删除该记录相关文件
    //删除文档
    $deletetodoc=$DB->get_record_sql('select *from mdl_doc_my where id='.$_GET['doclibraryid']);
    require_once('../convertpath.php');
    $filepath=convert_url_to_path($deletetodoc->url);
    unlink($filepath);
    //删除文档背景
    $picpath=convert_url_to_path($deletetodoc->pictrueurl);
    if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/doc_default.jpg')
        unlink($picpath);
    $DB->delete_records("doc_my", array("id" =>$_GET['doclibraryid']));
    success('删除成功','doclibrary','');
}
?>

