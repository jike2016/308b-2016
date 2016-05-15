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
                $newdoclibrary->pictrueurl = 'http://' . $_SERVER['HTTP_HOST'] . '/microread_files/doclibrary/pictrueurl/' . $currenttime.$ranknum.$picfilestr;
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
				if(in_array($urlfilestr,array('.doc','.docx','.ppt','.pptx','.txt','.xls','.xlsx'))){
					word2pdf('http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					'D:/WWW/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
					// word2swf_linux('/var/www/html/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					// '/var/www/html/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
					// '/var/www/html/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
				}
				elseif(in_array($urlfilestr,array('.pdf'))){
					pdf2swf('D:/WWW/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
					// pdf2swf_linux('/var/www/html/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					// '/var/www/html/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
				}
				$newdoclibrary->swfurl='http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf';
				/**End*/
                $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
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
				$ranknum = rand(100, 200);//随机数
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
                $newdoclibrary->url= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr;
                if($picuploadtag){
                    $newdoclibrary->pictrueurl='http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/doc_default.jpg';
                }
                else{
                    $newdoclibrary->pictrueurl= 'http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/pictrueurl/'.$currenttime.$ranknum.$picfilestr;
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
				if(in_array($urlfilestr,array('.doc','.docx','.ppt','.pptx','.txt','.xls','.xlsx'))){
					word2pdf('http://'.$_SERVER['HTTP_HOST'].'/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					'D:/WWW/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
					// word2swf_linux('/var/www/html/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					// '/var/www/html/microread_files/doclibrary/pdffile/'.$currenttime.$ranknum.'.pdf',
					// '/var/www/html/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
				}
				elseif(in_array($urlfilestr,array('.pdf'))){
					pdf2swf('D:/WWW/microread_files/doclibrary/doclibraryurl_fordownload/'. $currenttime.$ranknum.$urlfilestr,
					'D:/WWW/microread_files/doclibrary/swffile/'.$currenttime.$ranknum.'.swf');
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
    else{
        failure('请上传文档');
        exit;
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

