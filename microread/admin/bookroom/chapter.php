<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="bookroom/chapter_add.php?ebookid=<?php echo $_GET['ebookid'] ?>" target="dialog"><span>添加</span></a></li>
            <li><a class="delete" href="bookroom/chapter_post_handler.php?title=delete&chapterid={chapterid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="bookroom/chapter_edit.php?chapterid={chapterid}" target="dialog"><span>修改</span></a></li>
         
           
        </ul>
    </div>
    <table class="table" width="30%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="40">章序号</th>
            <th>章名称</th>
            <th width="65" >节管理</th>
        </tr>
        </thead>
        <tbody>
        <?php /**satrt zxf 查询分类全部基本信息**/?>
        <?php
        require_once("../../../config.php");
        global $DB;
        $chapters=$DB->get_records_sql('select * from mdl_ebook_chapter_my as a where a.ebookid='.$_GET['ebookid'].' order by a.chapterorder');
        foreach($chapters as $chapter){
            echo  '
				<tr target="chapterid" rel="'.$chapter->id.'" align="center">
				<td>'.$chapter->chapterorder.'</td>
				<td>'.$chapter->name.'</td>
				<td><a class="button" href="bookroom/section.php?chapterid='.$chapter->id.'" target="navTab" rel="ebooksection"><span>节管理</span></a></td>
				</tr>';

        }
        ?>

        <?php /**end zxf 查询分类全部基本信息**/?>
        </tbody>
    </table>
</div>
