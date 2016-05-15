<?php
/** Start zxf 处理分类CURD*/
require_once('../lib/lib.php');
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
        case "edit"://编辑首页推荐分类榜
            /**start zxf 判断选中的分类已经在推荐名单中**/
            $exitflag=$DB->get_record_sql('select * from mdl_doc_recommend_authorlist_my where userid='.$_POST['userid']);
            if($exitflag!=null){
                failure('已经存在');
            }
            else{
                $newauthorrecommned=new stdClass();
                $newauthorrecommned->id=$_GET['authorrecommnedid'];
                $newauthorrecommned->userid=$_POST['userid'];
                $newauthorrecommned->docid1=-1;
                $newauthorrecommned->docid2=-1;
                $newauthorrecommned->docid3=-1;
                $DB->update_record('doc_recommend_authorlist_my', $newauthorrecommned);
                success('修改成功','docauthorrecommendlist','closeCurrent');
            }
            break;
    }
}
else{
    failure('操作失败');
}
?>

