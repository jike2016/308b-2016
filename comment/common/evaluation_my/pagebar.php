<?php
require_once("../../../config.php");
//获取页面传进的参数--当前页码
$cur_page = $_GET['cur_page'];
$page_total = $_GET['page_total'];
//初始化变量
$page_size = 5;
$page_offset = ($page_size-1)/2;
$start = 1;
$end = $page_total;
//处理代码
$page_str = "";
//如果当前页码为第一页
if($cur_page == 1 ){
    $page_str .= "<span><a class='frist-btn a-disabled'>首页</a></span><span><a class='frist-btn a-disabled'>上一页</a></span>";
}else{
    $page_str .= "<span><a href='javascript:void(0)' data-page='1'>首页</a></span><span><a href='javascript:void(0)' data-page='".($cur_page-1)."'>上一页</a></span>";
}
//判断起始页码与结束页码
if($page_total > $page_size){
    //输出首省略符
    if($cur_page > ($page_offset+1)){
        $page_str .= "...";
    }
    //设置起始页码与结束码
    if($cur_page > $page_offset){    //如果当前页码大于偏移量
        $start = $cur_page - $page_offset;
        $end =  ($page_total > ($cur_page + $page_offset))?($cur_page + $page_offset):$page_total;
    }else{
        $start = 1;
        $end = ($page_total > $page_size)?$page_size:$page_total;
    }
    if(($cur_page + $page_offset) > $page_total){//如果页码尺寸加上偏移量大于总页数
        $start = $page_total - $page_size +1;
        $end = $page_total;
    }
}
//输出页码
for($i = $start;$i <= $end;$i++){
    if($i == $cur_page){
        $page_str .= "<a href='javascript:void(0)' class='active' data-page='".$i."'>".$i."</a>";
    }else{
        $page_str .= "<a href='javascript:void(0)' data-page='".$i."'>".$i."</a>";
    }
}
//输出尾省略符
if($page_total > $page_size && $page_total > ($cur_page + $page_offset)){
    $page_str .= "...";
}
//如果当前页为最后一页
if($cur_page == $page_total){
    $page_str .= "<span><a class='frist-btn a-disabled'>下一页</a></span><span><a class='frist-btn a-disabled'>尾页</a></span>";
}else{
    $page_str .= "<span><a href='javascript:void(0)' data-page='".($cur_page + 1)."'>下一页</a></span><span><a href='javascript:void(0)' data-page='".$page_total."'>尾页</a></span>";
}
//输出json
$result = [
    "success" => true,
    "data" =>$page_str,
];
echo json_encode($result);





















