<?php 
/** START cx 全局标签编辑 20160429*/
//获取全部标签
function gettagmylist(){
	global $DB;
	$alltags = $DB->get_records_sql('select * FROM mdl_doc_tag_my;');
	return $alltags;
}
//获取已经被选取的标签
function gettagmy_selected($docid){
	global $DB;
	$alltags = $DB->get_records_sql('select tagid from mdl_doc_tag_link_my where docid='.$docid);
	return $alltags;
}
//更新文档标签
function update_edit_doc_tagmy($tagids,$docid){
	global $DB;
	$DB->delete_records("doc_tag_link_my", array('docid'=>$docid));
	foreach($tagids as $tagid){
		$DB->insert_record('doc_tag_link_my', array('tagid'=>$tagid, 'docid'=>$docid));
	}
}
//添加文档标签
function update_add_doc_tagmy($tagids,$docid){
	global $DB;
	foreach($tagids as $tagid){
		$DB->insert_record('doc_tag_link_my', array('tagid'=>$tagid, 'docid'=>$docid));
	}
}

function update_delete_tagmy_by_docid($docid){
	global $DB;
	$DB->delete_records("doc_tag_link_my", array('docid'=>$docid));
}

function update_delete_tagmy_by_tagid($tagid){
	global $DB;
	$DB->delete_records("doc_tag_link_my", array('tagid'=>$tagid));
}
?>