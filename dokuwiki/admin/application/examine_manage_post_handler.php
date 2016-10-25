<?php 
/** Start CX 处理审核CURD 20160929*/
require_once('../lib/lib.php');
if(isset($_GET['title']) && $_GET['title']){
	/** CX 检查权限 */
    require_once('../../../config.php');
	require_once('../../../user/my_role_conf.class.php');//引入角色配置
	require_login();
	global $DB;
	global $USER;
	if($USER->id!=2){//判断是否是超级管理员
		$role_conf = new my_role_conf();
		//判断是否是慕课管理员
		$result = $DB->record_exists('role_assignments', array('roleid' => $role_conf->get_courseadmin_role(),'userid' => $USER->id));
		if(!$result){
			redirect(new moodle_url('/index.php'));
		}
	}
	/** End 检查权限*/
	switch ($_GET['title']){
		case "pass"://审核通过
			pass_word();	
			break;
		case "unpass"://不通过
			unpass_word();
			break;
	}
}
else{
	failure('操作失败');
}
function pass_word(){
	global $DB;
	$word=new stdClass();
	$word->id= $_GET['wordid'];
	$word->entrystate=1;
	$DB->update_record('dokuwiki_review_my', $word);
	$word= $DB->get_record_sql('select b.id,a.entryid,a.submittime from mdl_dokuwiki_review_my a join mdl_dokuwiki_word_my b where a.id='.$_GET['wordid'].' and a.entryid=b.word_name');
	if($word){//词条是否存在
		/** START cx 20160929 通过审核的词条作为最新词条*/
		revert_my($word->entryid,$word->submittime);
		/**END*/
	}
	else{
		success('操作无效，该词条已被删除，','null_value','forward','application/examine_manage.php?pageNum='.$_GET['pageNum']);
		return;
	}
	success('操作成功','null_value','forward','application/examine_manage.php?pageNum='.$_GET['pageNum']);
}
function unpass_word(){
	global $DB;
	$word=new stdClass();
	$word->id= $_GET['wordid'];
	$word->entrystate=2;
	$DB->update_record('dokuwiki_review_my', $word);
	success('操作成功','null_value','forward','application/examine_manage.php?pageNum='.$_GET['pageNum']);
}

//START CX 将通过审核的词条版本作为最新版本，其实就是回退版本，参考
//D:\WWW\moodle\dokuwiki2\inc\actions.php里函数function act_revert($act)的代码处理20161006
//$id词条，$rev版本号
function revert_my($id,$rev){
	$dir_my = dirname(__FILE__);
	$index = strpos($dir_my, '\\admin\\application');
	$dir_my = substr($dir_my,0,$index);
	if(!defined('DOKU_INC')) define('DOKU_INC', $dir_my.'/');
	require_once(DOKU_INC.'inc/init.php');
	//global $ID;
	//global $REV;
	global $lang;
	/* @var Input $INPUT */
	//global $INPUT;
	// FIXME $INFO['writable'] currently refers to the attic version
	// global $INFO;
	// if (!$INFO['writable']) {
	//     return 'show';
	// }

	// when no revision is given, delete current one
	// FIXME this feature is not exposed in the GUI currently
	$text = '';
	$sum  = $lang['deleted'];
	if($rev){
		$text = rawWiki($id,$rev);
		if(!$text) return 'show'; //something went wrong
		$sum = sprintf($lang['restored'], dformat($rev));
	}

	// spam check

	if (checkwordblock($text)) {
		msg($lang['wordblock'], -1);
		return 'edit';
	}

	saveWikiText($id,$text,$sum,false);
	msg($sum,1);

	//delete any draft
	act_draftdel('revert');
	session_write_close();

	// when done, show current page
	//$INPUT->server->set('REQUEST_METHOD','post'); //should force a redirect
	//$rev = '';
	//return 'show';
}
?>

