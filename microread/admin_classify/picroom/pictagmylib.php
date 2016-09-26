<?php 
/** START cx 全局标签编辑 20160429*/
//获取全部标签
require_once('../../../config.php');
function getpictagmylist(){
	global $DB;
	$sql='select * FROM mdl_pic_tag_my ORDER BY CONVERT(name USING gbk)';
	$allpictags = $DB->get_records_sql($sql);
	return $allpictags;
}
//获取已经被选取的标签
function getpictagmy_selected($picid){
	global $DB;
	$alltags = $DB->get_records_sql('select tagid from mdl_pic_tag_link_my where picid='.$picid);
	return $alltags;
}
//更新文档标签
function update_edit_pic_tagmy($tagids,$picid){
	global $DB;
	$DB->delete_records("pic_tag_link_my", array('picid'=>$picid));
	foreach($tagids as $tagid){
		$DB->insert_record('pic_tag_link_my', array('tagid'=>$tagid, 'picid'=>$picid));
	}
}
//添加文档标签
function update_add_pic_tagmy($tagids,$picid){
	global $DB;
	foreach($tagids as $tagid){
		$DB->insert_record('pic_tag_link_my', array('tagid'=>$tagid, 'picid'=>$picid));
	}
}

function update_delete_pic_tagmy_by_picid($picid){
	global $DB;
	$DB->delete_records("pic_tag_link_my", array('picid'=>$picid));
}

function update_delete_pic_tagmy_by_tagid($tagid){
	global $DB;
	$DB->delete_records("pic_tag_link_my", array('tagid'=>$tagid));
}
?>