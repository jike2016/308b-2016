<?php
/**
 * Created by cx.
 * User: gcmooc
 * Date: 16-5-31
 * Time: 下午9:48
 * 处理节中图片上传
 */
/**获取上传的图片，并转存路径 */
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
    $currenttime = time();
    $ranknum = rand(100, 200);//随机数
    /** End 检查权限*/
    switch ($_GET['title']){
        case "addimg"://添加
            add_img($currenttime,$ranknum);
            break;
    }
}

function add_img($currenttime,$ranknum){
    $msg='';
    $err='';
    if(isset($_FILES['filedata'])){//上传图片
        if ($_FILES["filedata"]["error"] > 0){
            $err='文件上传出错';
        }
        else {//判断上传类型是否是图片类型
            $picfilestr = strrchr($_FILES['filedata']['name'], '.');//pic后缀名
            $picfilestr = strtolower($picfilestr);//全小写
            move_uploaded_file($_FILES["filedata"]["tmp_name"], "../../../../microread_files/ebook/sectionpictrueurl/" .$currenttime . $ranknum . $picfilestr);
            //start zxf 图片加水印
            require_once('../water.php');
            img_water_mark('../../../../microread_files/ebook/sectionpictrueurl/'.$currenttime . $ranknum . $picfilestr,'http://'.$_SERVER['HTTP_HOST'].'/moodle/microread/img/Home_Logo.png');
            //end zxf 图片加水印
            $msg='/microread_files/ebook/sectionpictrueurl/'. $currenttime . $ranknum . $picfilestr;
        }
    }
    echo '{"err":"'.$err.'","msg":"'.$msg.'"}';
}


