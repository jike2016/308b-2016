<?php
//文件名convertpath
//作用将要删除的文件网页路径转换成服务器本地绝对路径
//作者 zxf
//时间2015/5/9

//函数名 convert_url_to_path
//作用 将文件url转换成绝对路径
//参数 $url 文件url
//返回值 $path 文件绝对路径

function convert_url_to_path($url){
    //判断冒号第一次出现的位置，出去前缀
    $path=strstr($url,'microread');
    //判断冒号第二次出现的位置，除去前缀
//    $path=strstr($path,':');
    //判断’/‘第三次出现的位置，除去前缀
//    $path=strstr($path,'/');
    $path='D:/WWW/'.$path;
    return $path;
}
function convert_url_to_path_pdf($url){
    //判断冒号第一次出现的位置，出去前缀
    $path=strstr($url,'microread');
    $arrfirst=explode('/',$path);
    $arrsecond=explode('.',$arrfirst[3]);
    $path='D:/WWW/'.$arrfirst[0].'/'.$arrfirst[1].'/pdffile/'.$arrsecond[0].'.pdf';
    return $path;

}
?>