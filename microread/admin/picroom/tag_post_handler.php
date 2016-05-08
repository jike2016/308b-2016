<?php
/** Start zxf 处理标签CURD*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
    require_once('../../../config.php');
    switch ($_GET['title']){
        case "add"://添加标签
            $newtag=new stdClass();
            $newtag->name= $_POST['name'];
            global $DB;
            $existid=$DB->get_records_sql('select * from mdl_pic_tag_my as a where a.name="'.$newtag->name.'"');
            if($existid)
            {
                failure('该类已存在');
            }
            else
            {
                $DB->insert_record('pic_tag_my',$newtag,true);
                success('添加成功','pictag','closeCurrent');
            }

            break;
        case "edit"://编辑标签
            $newtag=new stdClass();
            $newtag->id= $_GET['tagid'];
            /**strat zxf 排除自己查重名**/
            $mytag=$DB->get_record_sql('select * from mdl_pic_tag_my where id='.$_GET['tagid']);
            if($mytag->name!=$_POST['name'])
            {
                $sql='select * from mdl_pic_tag_my as a where a.name="'.$_POST['name'].'"';
                $existid=$DB->get_records_sql($sql);
                if($existid!=null)
                {
                    failure('该类已存在');
                }
                else
                {
                    $newtag->name= $_POST['name'];
                    $DB->update_record('pic_tag_my', $newtag);
                    success('修改成功','pictag','closeCurrent');
                }
            }
            else
            {
                $newtag->name= $_POST['name'];
                $DB->update_record('pic_tag_my', $newtag);
                success('修改成功','pictag','closeCurrent');
            }



            /**end zxf 排除自己查重名**/
            break;
        case "delete"://删除标签
            global $DB;
            require_once('pictagmylib.php');
            update_delete_pic_tagmy_by_picid($_GET['tagid']);
            $DB->delete_records("pic_tag_my", array("id" =>$_GET['tagid']));
            success('删除成功','pictag','');
            break;
    }
}
else{
    failure('操作失败');
}
?>

