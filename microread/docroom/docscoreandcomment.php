<?php

/**
 * 用户评论评分处理文件(文库)
 */

if(!isset($CFG))
{
	require_once  '../../config.php';
}

$type = $_GET['type'];
global $USER;
if(in_array($type, array('score', 'comment')) && isset($USER->id))
{
	option_score_comment($type, $USER->id);
}else
{
	die('0');
}

function option_score_comment($type, $userid)
{
	global $DB;
	switch ($type)
	{
		case 'score':
			$sql = 'SELECT id FROM mdl_doc_score_my WHERE userid = '.$userid.' AND docid =  '.$_GET['docid'];
			$result = $DB->get_records_sql($sql);
			if(count($result))
			{
				echo '2';
			}
			else
			{
				$docscore = new stdClass();
				$docscore->userid = $userid;
				$docscore->score = $_GET['score'];
				$docscore->scoretime = time();
				$docscore->docid = $_GET['docid'];
				$DB->insert_record('doc_score_my', $docscore);

				$scoreresult = $DB->get_records_sql(' SELECT id, score FROM mdl_doc_score_my WHERE docid = '.$_GET['docid']);
				$scoresum = 0;
				foreach($scoreresult as $scorevalue)
				{
					$scoresum += $scorevalue->score;
				}

				$avgscore = $scoresum/count($scoreresult);

				$sumsresult = $DB->get_records_sql(' SELECT id, scorecount FROM mdl_doc_sumscore_my WHERE docid = '.$_GET['docid']);

				if(count($sumsresult))
				{

					$sumscore = new stdClass();
					foreach($sumsresult as $sv) {
						$sumscore->id = $sv->id;
						$sumscore->scorecount = $sv->scorecount + 1;
					}

					$sumscore->sumscore = number_format($avgscore, 1);
					$sumscore->docid = $_GET['docid'];
					$DB->update_record('doc_sumscore_my', $sumscore);
					echo '1';
				}
				else
				{
					$DB->insert_record('doc_sumscore_my', array('sumscore'=>number_format($avgscore,1), 'docid'=>$_GET['docid'], 'scorecount'=>'1'));
					echo '1';
				}
			}
			break;
		case 'comment':
//			$DB->insert_record('doc_comment_my', array('docid'=>$_GET['docid'], 'userid'=>$userid, 'comment'=>$_GET['comment'], 'commenttime'=>time()));
//			echo '1';
			$commentresult = $DB->get_records_sql('SELECT id, comment, commenttime FROM mdl_doc_comment_my WHERE docid = '.$_GET['docid'].' AND userid = '.$userid.' ORDER BY commenttime DESC LIMIT 1');
			if(count($commentresult))
			{
				foreach($commentresult as $commentresultvalue)
				{
					similar_text($commentresultvalue->comment, $_GET['comment'], $percent);
					if($commentresultvalue->commenttime + 60 > time())
					{
						echo '0';
					}elseif($percent > 60)
					{
						echo '2';
					}else {
						$DB->insert_record('doc_comment_my', array('docid' => $_GET['docid'], 'userid' => $userid, 'comment' => $_GET['comment'], 'commenttime' => time()));
						echo '1';
					}
				}
			}
			else {
				$DB->insert_record('doc_comment_my', array('docid' => $_GET['docid'], 'userid' => $userid, 'comment' => $_GET['comment'], 'commenttime' => time()));
				echo '1';
			}
			break;
		default:
			echo '';
			break;
	}
}
