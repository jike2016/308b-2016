<?php
require_once('../lib/lib.php');
/** Start cx 处理分类CURD 20160929*/
if(isset($_GET['title']) && $_GET['title']){
	/** CX 检查权限 */
    require_once('../../../config.php');
	require_once('../../../user/my_role_conf.class.php');//引入角色配置
	require_login();
	global $DB;
	global $USER;
	if($USER->id!=2){//判断是否是超级管理员
		$role_conf = new my_role_conf();
		//判断是否是慕课管理员
		$result = $DB->record_exists('role_assignments', array('roleid' => $role_conf->get_courseadmin_role(),'userid' => $USER->id));
		if(!$result){
			redirect(new moodle_url('/index.php'));
		}
	}
	/** End 检查权限*/
    switch ($_GET['title']){
        case "add"://添加分类
            $newecategory=new stdClass();
            $newecategory->name= $_POST['name'];
            $newecategory->parent= $_POST['parent'];
            global $DB;
            $existid=$DB->get_records_sql('select * from mdl_dokuwiki_categories_my as a where a.name="'.$newecategory->name.'"');
            if($existid)
            {
                failure('分类已存在');
            }
            else
            {
                $DB->insert_record('dokuwiki_categories_my',$newecategory,true);
                success('添加成功','category_manage','closeCurrent');
            }

            break;
        case "edit"://编辑分类
            $newecategory=new stdClass();
            $newecategory->id= $_GET['categoryid'];
            /**strat cx 排除自己查重名**/
            $mycategory=$DB->get_record_sql('select * from mdl_dokuwiki_categories_my where id='.$newecategory->id);
            if($mycategory->name!=$_POST['name'])
            {
                $sql='select * from mdl_dokuwiki_categories_my as a where a.name="'.$_POST['name'].'" and a.name="'.$_POST['name'].'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该类已存在');
                }
                else
                {
                    $newecategory->name= $_POST['name'];
                    $newecategory->parent= $_POST['parent'];
                    $DB->update_record('dokuwiki_categories_my', $newecategory);
                    success('修改成功','category_manage','closeCurrent');
                }
            }
            else
            {
                $newecategory->name= $_POST['name'];
                $newecategory->parent= $_POST['parent'];
                $DB->update_record('dokuwiki_categories_my', $newecategory);
                success('修改成功','category_manage','closeCurrent');
            }



            /**end cx 排除自己查重名**/
            break;
        case "delete"://删除
			$words=$DB->get_records_sql('select * from mdl_dokuwiki_word_my where categoryid='.$_GET['categoryid']);
            foreach($words as $word){
                $newcategory=new stdClass();
                $newcategory->id=$word->id;
                $newcategory->categoryid=0;
                $DB->update_record('dokuwiki_word_my',$newcategory);
            }
            $DB->delete_records("dokuwiki_categories_my", array("id" =>$_GET['categoryid']));
            success('删除成功','category_manage','');
            break;
    }
}
else{
    failure('操作失败');
}


?>

