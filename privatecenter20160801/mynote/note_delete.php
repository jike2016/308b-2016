

<?php
require_once("../../config.php");

/**  删除课程笔记 */
$noteid=$_GET['noteid'];
if($noteid == 0){
    echo '2';//删除失败
}
else{
    global $DB;
    $DB->delete_records('note_my', array('id' => $noteid));
    echo '1';//删除成功
}

?>


