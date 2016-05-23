<?php
/** 对文档的CURD  */
require_once('../lib/lib.php');
require_once('../../convertlib.php');
if(isset($_GET['title'])&&$_GET['title']){
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
    $currenttime = time();
    $ranknum = rand(100, 200);//随机数
    $newdoclibrary=new stdClass();
    $newdoclibrary->id= $_GET['doclibraryid'];
    $newdoclibrary->name= $_POST['name'];
    $newdoclibrary->categoryid= $_POST['categoryid'];
    $newdoclibrary->summary= $_POST['summary'];
	//文档修改 ，根据id获取该文档基本信息，如果有图片上传，则删除之前的封面，如果有文件上传，删除之前的文件(原件，pdf,swf)
    global $DB;
    $updatedoc=$DB->get_record_sql('select *from mdl_doc_my where id='.$_GET['doclibraryid']);
    require_once('../convertpath.php');
    if(isset($_FILES["pictrueurl"])){
        if($_FILES["pictrueurl"]["error"]>0){
//            failure('上传图片失败');
        }else{
            //判断上传类型是否是图片类型
            $picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
			$picfilestr=strtolower($picfilestr);//全小写
            $picmatch=array('.gif','.jpeg','.png','.jpg');
            if(in_array($picfilestr,$picmatch)) {
				$picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
				$picfilestr=strtolower($picfilestr);//全小写
                move_uploaded_file($_FILES["pictrueurl"]["tmp_name"], "../../../../microread_files/doclibrary/pictrueurl/" .$currenttime.$ranknum.$picfilestr);
                 //start zxf 图片加水印
                require_once('../water.php');
                img_water_mark('../../../../microread_files/doclibrary/pictrueurl/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
                //end zxf 图片加水印
                $newdoclibrary->pictrueurl = '/microread_files/doclibrary/pictrueurl/' . $currenttime.$ranknum.$picfilestr;
				//start zxf 2016/5/11 文档修改，有图片上传，删除之前非默认图片
                //删除文档背景
                $picpath=convert_url_to_path($updatedoc->pictrueurl);
                if($picpath!='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/doc_default.jpg')
                    unlink($picpath);
                //end zxf 2016/5/11 文档修改，有图片上传，删除之前非默认图片
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
            $urlfilestr=strrchr($_FILES['url']['name'],'.');//url后缀名
			$urlfilestr=strtolower($urlfilestr);
            $docmatch=array('.doc','.docx','.ppt','.pptx','.xls','.xlsx','.pdf','.txt');
            if(in_array($urlfilestr,$docmatch)) {
                move_uploaded_file($_FILES["url"]["tmp_name"],"../../../../microread_files/doclibrary/doclibraryurl_fordownload/" . $currenttime.$ranknum.$urlfilestr);
                /**START cx 上传的文档转swf*/
                $documentroot = $_SERVER['DOCUMENT_ROOT'];   // 文档的服务器绝对路径
                $filepath=$documentroot.'/microread_files/doclibrary/doclibraryurl_fordownload/'.$currenttime.$ranknum.$urlfilestr;
                $swf_filepath = $documentroot.'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
                $pdf_filepath = $documentroot.'/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf';
                if(in_array($urlfilestr,array('.doc','.docx','.ppt','.pptx','.xls','.xlsx'))){
//					word2pdf('http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
//					'D:/WWW/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
//					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
                    word2swf_linux($filepath,$pdf_filepath ,$swf_filepath);
                }
                elseif(in_array($urlfilestr,array('.pdf'))){
//					pdf2swf('D:/WWW/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
//					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
                    pdf2swf_linux($filepath,$swf_filepath);
                }
                elseif(in_array($urlfilestr,array('.txt'))){
                    $txt_outputpath = $documentroot.'/microread_files/doclibrary/txtfile/'.$currenttime.$ranknum.'.txt';
                    txt2swf_linux($filepath,$txt_outputpath, $pdf_filepath, $swf_filepath);
                }
                $newdoclibrary->swfurl='/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
                /**End*/

                $newdoclibrary->url= '/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
                $newdoclibrary->suffix= $urlfilestr;
                if(($_FILES["url"]["size"] / 1048576)<=0.1){
                    $newdoclibrary->size='0.1MB';
                }
                else{
                    $newdoclibrary->size= number_format(($_FILES["url"]["size"] / 1048576),1).'MB';
                }
				 //start zxf 2016/5/11 有文件上传，删除之前的文件和pdf,swf文件
                //删除swf
                $pdfswf=convert_url_to_path($updatedoc->swfurl);
                unlink($pdfswf);
                //删除pdf
                $pdffile=convert_url_to_path_pdf($updatedoc->swfurl);
                unlink($pdffile);
                $filepath=convert_url_to_path($updatedoc->url);
                unlink($filepath);
                //end zxf 2016/5/11 有文件上传，删除之前的文件和pdf,swf文件
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
    $currenttime = time();
	$ranknum = rand(100, 200);//随机数
    if(isset($_FILES['pictrueurl'])){//上传图片
        if ($_FILES["pictrueurl"]["error"] > 0){
//          failure('上传图片失败');
            $picuploadtag=true;
//          exit;
        }
        else{//判断上传类型是否是图片类型
            $picuploadtag=false;;
            $picfilestr=strrchr($_FILES['pictrueurl']['name'],'.');//pic后缀名
			$picfilestr=strtolower($picfilestr);//全小写
            $picmatch=array('.gif','.jpeg','.png','.jpg');
            if(in_array($picfilestr,$picmatch)) {
                move_uploaded_file($_FILES["pictrueurl"]["tmp_name"], "../../../../microread_files/doclibrary/pictrueurl/" .$currenttime.$ranknum.$picfilestr);
                 //start zxf 图片加水印
                require_once('../water.php');
                img_water_mark('../../../../microread_files/doclibrary/pictrueurl/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
                //end zxf 图片加水印
            }
            else{
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
            $newdoclibrary=new stdClass();
            $newdoclibrary->name= $_POST['name'];
            $newdoclibrary->categoryid= $_POST['categoryid'];
            $newdoclibrary->summary= $_POST['summary'];
            //判断文档上传类型
            $urlfilestr=strrchr($_FILES['url']['name'],'.');
            $urlfilestr=strtolower($urlfilestr);
            $docmatch=array('.doc','.docx','.ppt','.pptx','.xls','.xlsx','.pdf','.txt');
            if(in_array($urlfilestr,$docmatch)) {
                $newdoclibrary->url= '/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
                if($picuploadtag){
                    $newdoclibrary->pictrueurl='/moodle/microread/img/doc_default.jpg';
                }
                else{
                    $newdoclibrary->pictrueurl= '/microread_files/doclibrary/pictrueurl/'.$currenttime.$ranknum.$picfilestr;
                }
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
                $documentroot = $_SERVER['DOCUMENT_ROOT'];   // 文档的服务器绝对路径
                $filepath=$documentroot.'/microread_files/doclibrary/doclibraryurl_fordownload/'.$currenttime.$ranknum.$urlfilestr;
                $swf_filepath = $documentroot.'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
                $pdf_filepath = $documentroot.'/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf';
                if(in_array($urlfilestr,array('.doc','.docx','.ppt','.pptx','.xls','.xlsx'))){
//					word2pdf('http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
//					'D:/WWW/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
//					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
                     word2swf_linux($filepath,$pdf_filepath ,$swf_filepath);
                }
                elseif(in_array($urlfilestr,array('.pdf'))){
//					pdf2swf('D:/WWW/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
//					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
                     pdf2swf_linux($filepath,$swf_filepath);
                }
                elseif(in_array($urlfilestr,array('.txt'))){
                    $txt_outputpath = $documentroot.'/microread_files/doclibrary/txtfile/'.$currenttime.$ranknum.'.txt';
                     txt2swf_linux($filepath,$txt_outputpath, $pdf_filepath, $swf_filepath);
                }
                $newdoclibrary->swfurl='/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
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
    else{
        failure('请上传文档');
        exit;
    }

}
function delete_relate($docid){
    global $DB;
    $DB->delete_records("doc_comment_my", array("docid" =>$docid));
    $DB->delete_records("doc_sumscore_my", array("docid" =>$docid));
    $DB->delete_records("doc_score_my", array("docid" =>$docid));

}
function  delete_doclibrary(){
    global $DB;
    require_once('doctagmylib.php');
    update_delete_tagmy_by_docid($_GET['doclibraryid']);
    //删除该记录相关文件
    delete_relate($_GET['doclibraryid']);
    //删除文档
    $deletetodoc=$DB->get_record_sql('select *from mdl_doc_my where id='.$_GET['doclibraryid']);
    require_once('../convertpath.php');
	//删除swf
    $pdfswf=convert_url_to_path($deletetodoc->swfurl);
    unlink($pdfswf);
    //删除pdf
    $pdffile=convert_url_to_path_pdf($deletetodoc->swfurl);
    unlink($pdffile);
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

