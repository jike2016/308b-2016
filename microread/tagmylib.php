<?php 
/** START cx 全局标签编辑 20160429*/
//获取全部标签
function gettagmylist(){
	global $DB;
	$alltags = $DB->get_records_sql('select * FROM mdl_tag_my;');
	return $alltags;
}
//获取已经被选取的标签
function gettagmy_selected($table,$tableid){
	global $DB;
	$alltags = $DB->get_records_sql('select tagid from mdl_tag_link where link_name=\''.$table.'\' and link_id='.$tableid.';');
	return $alltags;
}
//更新ebook链接的标签
function update_edit_tagmy($tagmy,$table,$tableid){
	global $DB;
	$DB->delete_records("tag_link", array('link_name'=>$table , 'link_id'=>$tableid));
	for ($x=0; $x<count($tagmy); $x++) {
		$DB->insert_record('tag_link', array('tagid'=>$tagmy[$x], 'link_name'=>$table , 'link_id'=>$tableid));
	} 
	update_tagmy_num();
}
//添加ebook链接的标签
function update_add_tagmy($tagmy,$table,$tableid){
		global $DB;
	for ($x=0; $x<count($tagmy); $x++) {
		$DB->insert_record('tag_link', array('tagid'=>$tagmy[$x], 'link_name'=>$table , 'link_id'=>$tableid));
	} 
	update_tagmy_num();
}

function update_delete_tagmy($table,$tableid){
	global $DB;
	$DB->delete_records("tag_link", array('link_name'=>$table , 'link_id'=>$tableid));
	update_tagmy_num();
}
//更新标签的数量
function update_tagmy_num(){
	global $DB;
	$tagmy_taglinks=$DB->get_records_sql('select * from mdl_tag_my a join mdl_tag_link b where a.id=b.tagid');
	$tagmys=$DB->get_records_sql('select id from mdl_tag_my;');
	foreach ($tagmys as $tagmy){
		$num=0;
		foreach ($tagmy_taglinks as $tagmy_taglink){
			if($tagmy->id==$tagmy_taglink->tagid){
				$num++;
			}
		}
		$DB->update_record('tag_my', array('id'=>$tagmy->id, 'num'=>$num));
	}
}
?>