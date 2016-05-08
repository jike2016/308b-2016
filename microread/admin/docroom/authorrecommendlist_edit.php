<?php
/** Start cx 编辑电子书 20160427*/
require_once('../../../config.php');
global $DB;
$authorrecommendid = $_GET['authorrecommendid'];
$authorrecommend = $DB->get_record_sql('select * from mdl_doc_recommend_authorlist_my where id='.$authorrecommendid);

?>
<div class="pageContent">
    <form method="post" action="docroom/authorrecommendlist_post_handler.php?title=edit&authorrecommnedid=<?php echo $authorrecommendid;?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return iframeCallback(this, dialogAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p  style="margin: 20px 20px">
                <label>作者：</label>
                <?php
                /**start zxf 查询所有上传过文档的作者**/
                $users=$DB->get_records_sql('select id,firstname as name from mdl_user where id in (select mdl_doc_my.uploaderid as id from mdl_doc_my group by uploaderid)');
                $selectoption='<select name="userid" class="required combox" class="required"><option value="">请选择</option>';
                foreach($users as $user){
                    if($user->id==$authorrecommend->userid)
                        $selectoption=$selectoption.'<option value="'.$user->id.'" selected="selected">'.$user->name.'</option> ';
                    else
                        $selectoption=$selectoption.'<option value="'.$user->id.'">'.$user->name.'</option>';
                }
                $selectoption=$selectoption.'</select>';
                echo $selectoption;
                /**end zxf 查询所有上传过文档的作者**/
                ?>
            </p>
        </div>
        <div class="formBar">
            <ul>
                <!--<li><a class="buttonActive" href="javascript:;"><span>保存</span></a></li>-->
                <li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
                <li>
                    <div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
                </li>
            </ul>
        </div>
    </form>
</div>
