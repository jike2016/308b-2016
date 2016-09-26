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
        case "edit"://编辑首页推荐分类文档
            $newcategoryrecommenddoc=new stdClass();
            /**start zxf 判断选中的文档已经在推荐名单中**/
            $exitflag=$DB->get_record_sql('select * from mdl_doc_category_recommend_my where docid='.$_POST['docid']);
            if($exitflag!=null){
                failure('已经存在');
            }
            else{
                $newcategoryrecommenddoc->id=$_GET['categoryrecommenddocid'];
                $newcategoryrecommenddoc->docid=$_POST['docid'];
                $DB->update_record('doc_category_recommend_my',$newcategoryrecommenddoc);
                success('修改成功','categoryrecommendlistdoc','closeCurrent');
            }
            /**end zxf 判断选中的文档已经在推荐名单中**/
            break;
    }
}
else{
    failure('操作失败');
}
?>

