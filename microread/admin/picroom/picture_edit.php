<?php
require_once('../../../config.php');
$pictureid = $_GET['pictureid'];
$picture = $DB->get_record_sql('select * from mdl_pic_my where id='.$pictureid.';');
?>

<div class="pageContent">
    <form method="post" enctype="multipart/form-data" action="picroom/picture_post_handler.php?title=edit&pictureid=<?php echo $pictureid;?>" class="pageForm required-validate" onsubmit="return iframeCallback(this, navTabAjaxDone);"">
        <div layoutH="56">
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片名称：</label>
                <input name="name" type="text" size="30" value="<?php echo $picture->name; ?>" class="required"/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>图片上传：(jpg ,png ,bmp ,gif)</label>
                <input name="picurl" type="file" class=""/>
            </p>
            <p class="pageFormContent" style="margin: 20px 20px">
                <label>标签选择：</label>
                <?php
                require_once("pictagmylib.php");
                $alltags = getpictagmylist();
                $tag_selecteds = getpictagmy_selected($pictureid);
                foreach($alltags as $tagmy){
                    $n=false;
                    foreach($tag_selecteds as $tag_selected){
                        if($tagmy->id==$tag_selected->tagid){
                            echo '<label><input type="checkbox" name="tagmy[]" value="'.$tagmy->id.'" checked="checked" s/>'.$tagmy->name.'</label>';
                            $n=true;
                        }
                    }
                    if($n==false){
                        echo '<label><input type="checkbox" name="tagmy[]" value="'.$tagmy->id.'" />'.$tagmy->name.'</label>';
                    }
                }
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
