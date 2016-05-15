<?php 
/**
 * url 图片显示详细地址
 * src 缩略图地址
 */
if(!isset($_GET['word'])||!isset($_GET['page'])){
	exit;
}
$page=$_GET['page'];
$pics_perpage = 22;//每页显示图片数
$offset = ($page-1)*$pics_perpage;//序号

$words = wordhandler($_GET['word']);
$pics = searchpics($words,$offset,$pics_perpage);
pics2json($pics);



/**处理关键词 */
function wordhandler($word){
	$word = explode(' ',$word);//拆分关键词
	$words=array();
	foreach($word as $w){
		if($w!=""){
			array_push($words,$w);//去掉空格
		}
	}
	return $words;
}

/**搜索图片 */
function searchpics($words,$offset,$pics_perpage){

	require_once ("../../config.php");
	global $DB;
	if(($wordscount=count($words))>1){//多个关键词
		//搜图片标签
		$sql_part='c.name like \'%'.$words[0].'%\'';
		for($i=1;$i<$wordscount;$i++){
			$sql_part .= 'or c.name like \'%'.$words[$i].'%\'';
		}
		$sql="
			select 
			a.id,
			a.name as picname,
			a.picurl,
			d.firstname as uploadername
			from mdl_pic_my a 
			left join mdl_pic_tag_link_my b on b.picid=a.id 
			left join mdl_pic_tag_my c on c.id=b.tagid
			left join mdl_user d on d.id=a.uploaderid
			where $sql_part
			GROUP BY b.picid
			having count(*)>=$wordscount
			ORDER BY a.timeuploaded DESC
			limit $offset,$pics_perpage";
		$pics = $DB->get_records_sql($sql);
	}
	else{//1个关键词
		//搜：图片简介内容+图片标签
		$sql="
			select 
			a.id,
			a.name as picname,
			a.picurl,
			d.firstname as uploadername
			from mdl_pic_my a
			left join mdl_pic_tag_link_my b on b.picid=a.id
			left join mdl_pic_tag_my c on c.id=b.tagid 
			left join mdl_user d on d.id=a.uploaderid 
			where a.name like '%$words[0]%' 
			or c.name like '%$words[0]%' 
			group by a.id
			ORDER BY a.timeuploaded DESC
			limit $offset,$pics_perpage";
		$pics = $DB->get_records_sql($sql);
	}
	return $pics;
}

/**图片转json格式输出 */
function pics2json($pics){
	$pics_json = '[';
	foreach($pics as $pic){
		$pics_json .= '{"url": "#", "src": "'.$pic->picurl.'", "label": "'.$pic->picname.'", "size": "上传者：'.$pic->uploadername.'"},';
	}
	$pics_json = substr($pics_json,0,mb_strlen($pics_json)-1);
	$pics_json .= ']';
	echo $pics_json;
}
?>