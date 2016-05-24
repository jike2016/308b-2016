<?php

/**
 * 用户评论评分处理文件(书库)
 */

if(!isset($CFG))
{
	require_once '../../config.php';
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
			$sql = 'SELECT id FROM mdl_ebook_score_my WHERE userid = '.$userid.' AND ebookid =  '.$_GET['ebookid'];
			$result = $DB->get_records_sql($sql);
			if(count($result))
			{
				echo '2';
			}
			else
			{
				$ebookscore = new stdClass();
				$ebookscore->userid = $userid;
				$ebookscore->score = $_GET['score'];
				$ebookscore->scoretime = time();
				$ebookscore->ebookid = $_GET['ebookid'];
				$DB->insert_record('ebook_score_my', $ebookscore);

				$scoreresult = $DB->get_records_sql(' SELECT id, score FROM mdl_ebook_score_my WHERE ebookid = '.$_GET['ebookid']);
				$scoresum = 0;
				foreach($scoreresult as $scorevalue)
				{
					$scoresum += $scorevalue->score;
				}

				$avgscore = $scoresum/count($scoreresult);

				$sumsresult = $DB->get_records_sql(' SELECT id FROM mdl_ebook_sumscore_my WHERE ebookid = '.$_GET['ebookid']);

				if(count($sumsresult))
				{

					$sumscore = new stdClass();
					foreach($sumsresult as $sv) {
						$sumscore->id = $sv->id;
					}

					$sumscore->sumscore = number_format($avgscore, 1);
					$sumscore->ebookid = $_GET['ebookid'];
					$DB->update_record('ebook_sumscore_my', $sumscore);
					echo '1';
				}
				else
				{
					$DB->insert_record('ebook_sumscore_my', array('sumscore'=>number_format($avgscore,1), 'ebookid'=>$_GET['ebookid']));
					echo '1';
				}
			}
			break;
		case 'comment':
//			$DB->insert_record('ebook_comment_my', array('ebookid'=>$_GET['ebookid'], 'userid'=>$userid, 'comment'=>$_GET['comment'], 'commenttime'=>time()));
//			echo '1';
			$commentresult = $DB->get_records_sql('SELECT id, comment, commenttime FROM mdl_ebook_comment_my WHERE ebookid = '.$_GET['ebookid'].' AND userid = '.$userid.' ORDER BY commenttime DESC LIMIT 1');
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
					}else
					{
						$DB->insert_record('ebook_comment_my', array('ebookid'=>$_GET['ebookid'], 'userid'=>$userid, 'comment'=>$_GET['comment'], 'commenttime'=>time()));
						echo '1';
					}

				}
			}
			else
			{
				$DB->insert_record('ebook_comment_my', array('ebookid'=>$_GET['ebookid'], 'userid'=>$userid, 'comment'=>$_GET['comment'], 'commenttime'=>time()));
				echo '1';
			}
			break;
		default:
			echo '0';
			break;
	}
}
