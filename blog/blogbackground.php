<?php
/** START 朱子武 添加博客点赞关注 20160308*/

require_once(dirname(__FILE__) . '/../config.php');

global $DB;
global $USER;

$type = $_GET["type"];
//$userid = $_GET["userid"];

switch($type)
{
//    与我相关表
    case "related":
        $blogid = $_GET["blogid"];
        $relatedtype = $_GET["relatedtype"];

        if($relatedtype == '2') // 转发
        {
            $my_result = $DB->get_records_sql('SELECT * FROM mdl_post WHERE id = '.$blogid);
//            url
            $blogurl = '/blog/index.php?entryid='.$blogid;
//            标题
            $blogtitle= $my_result[$blogid]->subject;

            $my_originalblogid ='';

            // 获取微博的原始id
            $my_originalblogid = $my_result[$blogid]->originalblogid;

            $newrelated =  new stdClass;

            if($my_originalblogid == '') {
                // 设置微博原始id
                $my_result[$blogid]->originalblogid = $blogid;
                // 设置标题
                $my_result[$blogid]->subject = '[转发]' . $my_result[$blogid]->subject;
                // 设置内容
                $my_result[$blogid]->summary = $my_result[$blogid]->summary . '<br/><p>原文地址：</p><a href = ' . $blogurl . '>' . $blogtitle . '</a>';
                // 插入数据库
//                $DB->insert_record('blog_forwarded_count_my',array('blogid'=>$blogid,'forwardedcount'=>1),true);

                $myforwardedcount = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = ?', array($blogid));
                if (count($myforwardedcount)) {
                    $forwardedcount = '';
                    $forwardedid = '';
                    foreach ($myforwardedcount as $forwarde) {
                        $forwardedcount = $forwarde->forwardedcount;
                        $forwardedid = $forwarde->id;
                    }

                    $my_forwarde = new stdClass;
                    $my_forwarde->id = $forwardedid;
                    $my_forwarde->blogid = $blogid;
                    $my_forwarde->forwardedcount = $forwardedcount + 1;
                    $DB->update_record('blog_forwarded_count_my', $my_forwarde);
                } else {
                    $DB->insert_record('blog_forwarded_count_my', array('blogid' => $my_originalblogid, 'forwardedcount' => 1), true);
                }

            }
            else {
                $myforwardedcount = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = ?', array($my_originalblogid));
                if (count($myforwardedcount)) {
                    $forwardedcount = '';
                    $forwardedid = '';
                    foreach ($myforwardedcount as $forwarde) {
                        $forwardedcount = $forwarde->forwardedcount;
                        $forwardedid = $forwarde->id;
                    }

                    $my_forwarde = new stdClass;
                    $my_forwarde->id = $forwardedid;
                    $my_forwarde->blogid = $my_originalblogid;
                    $my_forwarde->forwardedcount = $forwardedcount + 1;
                    $DB->update_record('blog_forwarded_count_my', $my_forwarde);
                } else {
                    $DB->insert_record('blog_forwarded_count_my', array('blogid' => $my_originalblogid, 'forwardedcount' => 1), true);
                }
            }

            // 保存微博作者
            $newrelated->authorid = $my_result[$blogid]->userid;

            // 设置当前用户转发新作者
            $my_result[$blogid]->userid = $USER->id;
            // 获取时间
            $my_result[$blogid]->created = time();
            // 获取是否是被修改
            $my_result[$blogid]->lastmodified = $my_result[$blogid]->created;

            $DB->insert_record('post', $my_result[$blogid], true);
            echo '1';

            // 添加当前用户
            $newrelated->userid = $USER->id;
            // 获取被转发微博id
            $newrelated->blogid = $blogid;
            // 获取被转发微博url
            $newrelated->blogurl = $blogurl;
            // 标题
            $newrelated->blogtitle = $blogtitle;
            // 类型
            $newrelated->relatedtype = $relatedtype;
            // 时间
            $newrelated->relatedtime = time();
            // 插入数据库
            $DB->insert_record('blog_related_me_my', $newrelated, true);
        }
        else
        {

//            file_put_contents('12223.txt', 'UPDATE mdl_blog_like_count_my SET likecount= likecount + 1 WHERE blogid= '.$blogid);

//            查询是否已经点赞
            $myresult = $DB->get_records_sql('SELECT id FROM mdl_blog_related_me_my WHERE relatedtype = 3 AND blogid = ? AND userid = ?', array($blogid,$USER->id));
            if(count($myresult))
            {
                echo '2';
            }
            else
            {
                $my_result = $DB->get_records_sql('SELECT id, userid, subject FROM mdl_post WHERE id = '.$blogid);

                $blogtitle = $my_result[$blogid]->subject;
                $blogurl = '/blog/index.php?entryid='.$blogid;

                $newrelated =  new stdClass;
                // 保存微博作者
                $newrelated->authorid = $my_result[$blogid]->userid;

                $newrelated->userid = $USER->id;
                $newrelated->blogid = $blogid;
                $newrelated->blogurl = $blogurl;
                $newrelated->blogtitle = $blogtitle;
                $newrelated->relatedtype = $relatedtype;
                $newrelated->relatedtime = time();
                $DB->insert_record('blog_related_me_my', $newrelated, true);
                echo '1';
                if($relatedtype == '3') // 统计点赞数量
                {

                    $mylikecount = '';
                    $mylikecount = $DB->get_records_sql('SELECT id, blogid, likecount FROM mdl_blog_like_count_my WHERE blogid = ?', array($blogid));

                    if(count($mylikecount))
                    {
                        $likecount = '';
                        $likeid = '';
                        foreach($mylikecount as $like)
                        {
                            $likecount = $like->likecount;
                            $likeid = $like->id;
                        }

                        $my_like =  new stdClass;
                        $my_like->id = $likeid;
                        $my_like->blogid = $blogid;
                        $my_like->likecount = $likecount + 1;
//                    $DB->get_records_sql('UPDATE mdl_blog_forwarded_count_my SET forwardedcount = forwardedcount + 1 WHERE blogid= ?', array($blogid));
                        $DB->update_record('blog_like_count_my', $my_like);

//                        $DB->get_records_sql('UPDATE mdl_blog_like_count_my SET likecount= likecount + 1 WHERE blogid= ?', array($blogid));
                    }
                    else
                    {
                        $DB->insert_record('blog_like_count_my',array('blogid'=>$blogid,'likecount'=>1),true);
                    }
                }
            }
        }

        break;
//    我的关注
    case "concern":
        $concernid = $_GET["concernid"];

        $myresult = $DB->get_records_sql('SELECT id FROM mdl_blog_concern_me_my WHERE concernid = ? AND userid = ?', array($concernid,$USER->id));
        if(count($myresult))
        {
			$DB->delete_records('blog_concern_me_my',array('concernid'=>$concernid));
            echo '2';
        }
        else
        {
            $DB->insert_record('blog_concern_me_my',array('concernid'=>$concernid,'userid'=>$USER->id,'concerntime'=>time()),true);
            echo '1';
        }
        break;
    default:
        break;
}