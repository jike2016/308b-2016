<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="bookroom/section_add.php?chapterid=<?php echo $_GET['chapterid'] ?>" target="navTab"><span>添加</span></a></li>
            <li><a class="delete" href="bookroom/section_post_handler.php?title=delete&sectionid={sectionid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="bookroom/section_edit.php?chapterid=<?php echo $_GET['chapterid'] ?>&sectionid={sectionid}" target="navTab"><span>修改</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="138">
        <thead>
        <tr align="center">
            <th width="20">节序号</th>
            <th width="80">节名称</th>
            <th width="20">类型</th>
            <th width="150">内容</th>
        </tr>
        </thead>
        <tbody>
        <?php /**satrt zxf 查询分类全部基本信息**/?>
        <?php
        require_once("../../../config.php");
        global $DB;
        $sections=$DB->get_records_sql('select * from mdl_ebook_section_my as a where a.chapterid='.$_GET['chapterid'].' order by a.sectionorder');
        foreach($sections as $section){
            $showstr=  '
			    <tr target="sectionid" rel="'.$section->id.'" align="center">
			    <td>'.$section->sectionorder.'</td>
			    <td>'.$section->name.'</td>';
			if($section->type==1)
            {
                $showstr=$showstr.'<td>文本</td>
                <td>'.$section->text.'</td>';
            }
            else
            {
                $showstr=$showstr.'<td>pdf</td>
                <td><a href="'.$section->pdfurl.'">(右键另存为)</a></td>';
				
            }
            $showstr=$showstr.'</tr>';
            echo $showstr;



        }
        ?>

        <?php /**end zxf 查询分类全部基本信息**/?>
        </tbody>
    </table>
</div>
