<?php
require_once("../../config.php");

/** 保存课程笔记*/

$title=$_GET['noteTitle'];
$courseid=$_GET['courseId'];
$centent=$_GET['noteText'];

if($title==''  ||  $courseid==0){
    echo '2';//保存失败
}
else{

    global $DB;
    global $USER;

    $userId = $USER->id;
    $notetype = 1;//课程笔记标记
    $time = time();
    $DB->insert_record('note_my',array('userid'=>$userId,'notetype'=>$notetype,'courseid'=>$courseid,'title'=>$title,'content'=>$centent,'time'=>$time),true);
    echo '1';//注意：这个输出返回之前不能有其他的输出，否者会影响接受者的判断
}

?>


