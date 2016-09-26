<?php
/** 对图片的CURD  */
require_once('../lib/lib.php');
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
            add_picture();
            break;
        case "edit"://编辑
            edit_picture();
            break;
        case "delete"://删除
            delete_picture();
            break;

    }
}

//编辑
function edit_picture(){
    $newpicture=new stdClass();
    $newpicture->id= $_GET['pictureid'];
    $newpicture->name= $_POST['name'];
    $currenttime=time();
    $ranknum = rand(100, 200);//随机数
    if(isset($_FILES["picurl"])){
        if($_FILES["picurl"]["error"]>0){
//            failure('上传图片失败');
        }else{
            //判断上传类型是否是图片类型
            $picstr=strrchr($_FILES['picurl']['name'],'.');
            $picstr=strtolower($picstr);//全小写
            $picmatch=array('.gif','.jpeg','.png','.jpg');
            if(in_array($picstr,$picmatch)) {
                move_uploaded_file($_FILES["picurl"]["tmp_name"],"../../../../microread_files/picture/picurl/" . $currenttime.$ranknum.$picstr);
                //start zxf 图片加水印
               require_once('../water.php');
                img_water_mark('../../../../microread_files/picture/picurl/'.$currenttime . $ranknum . $picstr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
                //end zxf 图片加水印
                $newpicture->picurl = '/microread_files/picture/picurl/' . $currenttime.$ranknum.$picstr;
				$newpicture->picurl = '/microread_files/picture/picurl/' . $currenttime.$ranknum.$picstr;
				//start zxf 2016/5/11 图片修改，新图片上传 之前的图片 删除
                require_once('../convertpath.php');
                global $DB;
                $picfile=$DB->get_record_sql('select * from mdl_pic_my where id='.$_GET['pictureid']);
                $picpath=convert_url_to_path($picfile->picurl);
                unlink($picpath);
                //start zxf 2016/5/11 图片修改，新图片上传 之前的图片 删除
		   }
            else{
                failure('请上传正确格式的图片');
                exit;
            }
         }
    }
    $newpicture->suffix= $picstr;
    if(($_FILES["picurl"]["size"] / 1024)<=0.1){
        $newpicture->size='0.1KB';
    }
    else {
        $newpicture->size = number_format(($_FILES["url"]["size"] / 1024), 1) . 'KB';
    }
    global $DB;
    $newaddtagids=$_POST['tagmy'];
    require_once('pictagmylib.php');
    update_edit_pic_tagmy($newaddtagids,$_GET['pictureid']);
    $DB->update_record('pic_my', $newpicture);
    success('修改成功','picture','closeCurrent');
}

//添加
function  add_picture(){
    if(isset($_FILES['picurl'])){//上传图片
        if ($_FILES["picurl"]["error"] > 0){
            failure('上传图片失败');
            exit;
        }
        else{//判断上传类型是否是图片类型
            $currenttime=time();
            $ranknum = rand(100, 200);//随机数
            $picstr=strrchr($_FILES['picurl']['name'],'.');
            $picstr=strtolower($picstr);//全小写
            $picmatch=array('.gif','.jpeg','.png','.jpg');
            if(in_array($picstr,$picmatch)) {
                move_uploaded_file($_FILES["picurl"]["tmp_name"],"../../../../microread_files/picture/picurl/" . $currenttime.$ranknum.$picstr);
                //start zxf 图片加水印
               require_once('../water.php');
                img_water_mark('../../../../microread_files/picture/picurl/'.$currenttime . $ranknum . $picstr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
                //end zxf 图片加水印
            }
            else{
                failure('请上传正确格式的图片');
                exit;
            }
                global $USER;
                $newpic=new stdClass();
                $newpic->name= $_POST['name'];
                $newpic->picurl= '/microread_files/picture/picurl/' . $currenttime.$ranknum.$picstr;
                $newpic->timeuploaded= $currenttime;
                $newpic->suffix=$picstr;
                if(($_FILES['picurl']['size']/1024)<=0.1){
                    $newpic->size='0.1KB';
                }
                else{
                    $newpic->size= number_format(($_FILES['picurl']['size']/1024),1).'KB';
                }
                $newpic->uploaderid= $USER->id;
                global $DB;
               $newpicid = $DB->insert_record('pic_my',$newpic,true);
                $newaddtagids=$_POST['tagmy'];
                require_once('pictagmylib.php');
                update_add_pic_tagmy($newaddtagids,$newpicid);
                success('添加成功','picture','closeCurrent');

        }
    }
    else{
        // echo "Invalid file";
        failure('没有上传图片');
    }
}

function  delete_picture(){
    global $DB;
    require_once('pictagmylib.php');
    update_delete_pic_tagmy_by_picid($_GET['pictureid']);
    //删除相关记录图片
    //获取该记录
    $picfile=$DB->get_record_sql('select * from mdl_pic_my where id='.$_GET['pictureid']);
    require_once('../convertpath.php');
    $picpath=convert_url_to_path($picfile->picurl);
    unlink($picpath);
    $DB->delete_records("pic_my", array("id" =>$_GET['pictureid']));
    success('删除成功','picture','');
}
?>

