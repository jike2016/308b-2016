<?php


require_once('../../../config.php');
global $DB;
$indexbg=$DB->get_record_sql('select * from mdl_pic_indexbg');
?>


<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="edit" href="picroom/picindexbg_edit.php" target="dialog"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="30%" layoutH="138">
        <thead>
        <tr>
            <th width="80" align="center">序号</th>
            <th width="120" align="center">背景图</th>
        </tr>
        </thead>
        <tbody>
        <?php
       
            echo '
				<tr target="pictureid" rel="'.$indexbg->id.'">
					<td>'.$indexbg->id.'</td>
					<td><img src="'.$indexbg->indexbg_url.'" height="300" width="250" /></td>
				</tr>
				';

        /** End */
        ?>
        </tbody>
    </table>
</div>


