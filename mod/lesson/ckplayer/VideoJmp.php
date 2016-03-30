<?php
//@author徐东威 2016-01-08 17:40
//require_once($CFG->dirroot."\config.php");
require_once("../../../config.php");


//$test = "http://localhost/moodle/mod/lesson/view.php?id=";
//echo strlen($test);
$URLparam=$_GET['url'];
//$NewURL=substr($URLparam,0,47);//截取URL前部
$indexof = strpos($URLparam,'=');
$VideoID=substr($URLparam,$indexof+1);//截取视屏ID
global $DB;
//select cs.* from mdl_course_sections cs, mdl_course_modules cm where cs.id=cm.section and cm.id=54
$result1=$DB->get_record('course_modules',array('id'=>$VideoID));//注意表名称不要添加mdl_前缀！
$CourseID=$result1->course;//获取课程ID
$sectionID=$result1->section;//获取sectionID
$result2=$DB->get_record('course_sections',array('id'=>$sectionID));//注意表名称不要添加mdl_前缀！
$sequence=$result2->sequence;//获取视屏ID列表
$arraySequence=explode("," , $sequence);//将列表字符串转化为数组
$index=null;//用于记录下一个要跳转的视频id对应的数组脚标
$num=count($arraySequence);
//    var_dump($VideoID);
//    var_dump($arraySequence[1]);
for($i=0;$i<$num;$i++){//从视屏列表中获取跳转视屏的ID
    $res=strcmp($VideoID,$arraySequence[$i]);
    if($res==0){
        $index=$i+1;//让视屏ID号指向下一个
        break;
    }
}

//    var_dump($result2) ;

//视屏跳转页面的控制
if($index==null || $index>=$num){
//        echo "视屏没了，谢谢收看！";
    $NewURL='course/view.php?id=';//从新修改$NewURL,定位到课程首页URL
    $NewURL = $NewURL.$CourseID;//拼接上要跳转的视屏id，如果是视屏的末尾则跳转到课程的首页
//        echo "<script language=\"javascript\">";
//        echo "document.location=\"$NewURL\"";
//        echo "</script>";
}else{
//        echo "请看下一集".$index;
	$moduleName = $DB->get_record_sql('select mm.`name` from mdl_modules mm where mm.`id` = (select mcm.module from mdl_course_modules mcm where mcm.id = '.$arraySequence[$index].' )');
	
    $NewURL='mod/'.$moduleName->name.'/view.php?id=';
    $NewURL = $NewURL.$arraySequence[$index];//拼接上要跳转的视屏id，如果还有下一集则向下跳转

//        echo "<script language=\"javascript\">";
//        echo "document.location=\"$NewURL\"";
//        echo "</script>";
}

$response= $NewURL;
echo $response;

?>



