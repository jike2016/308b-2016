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

        if($relatedtype == '2') // 转发switch($relatedtype)
        {
            $my_result = $DB->get_records_sql('SELECT * FROM mdl_circles_of_learning WHERE id = '.$blogid);
//            url
            $blogurl = '/circlesoflearning/index.php?entryid='.$blogid;
//            标题
            $blogtitle= $my_result[$blogid]->summary;

            $my_originalblogid ='';

            // 获取微博的原始id
            $my_originalblogid = $my_result[$blogid]->originalblogid;

            $newrelated =  new stdClass;

            if($my_originalblogid == '') {
                // 设置微博原始id
                $my_result[$blogid]->originalblogid = $blogid;
                // 设置标题
//                $my_result[$blogid]->subject = '[转发]'. $my_result[$blogid]->subject;
                // 设置内容
                 $my_result[$blogid]->summary = '[转发]'.$my_result[$blogid]->summary.'<br/><a href ='.$CFG->wwwroot .$blogurl.'>原文地址</a>';
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
                    /** Start 修改微博转发的bug 朱子武 20160316*/
                    $DB->insert_record('blog_forwarded_count_my', array('blogid' => $blogid, 'forwardedcount' => 1), true);
                }

            }
            else {
                $myforwardedcount = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = ?', array($my_originalblogid));
                if (count($myforwardedcount)) {
//                    $forwardedcount = '';
                    $forwardedid = '';
                    $my_forwarde = new stdClass;
                    foreach ($myforwardedcount as $forwarde) {
//                        $forwardedcount = $forwarde->forwardedcount;
                        $forwardedid = $forwarde->id;
                        $my_forwarde->forwardedcount = $forwarde->forwardedcount + 1;
                    }
                    $my_forwarde->id = $forwardedid;
                    $my_forwarde->blogid = $my_originalblogid;
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

            $DB->insert_record('circles_of_learning', $my_result[$blogid], true);
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
                $my_result = $DB->get_records_sql('SELECT id, userid, summary FROM mdl_circles_of_learning WHERE id = '.$blogid);

                $blogtitle = $my_result[$blogid]->summary;
                $blogurl = '/circlesoflearning/index.php?entryid='.$blogid;

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
//    搜索学员信息 朱子武 20160315
    case 'searchuser':
        global $OUTPUT;
        $searchtext = $_GET['searchtext'];
        $result = $DB->get_records_sql('SELECT a.*, b.firstname, b.lastname, b.username FROM mdl_blog_concern_me_my a JOIN mdl_user b ON a.concernid = b.id WHERE userid = '.$USER->id.' AND (b.username LIKE "%'.$searchtext.'%" OR b.firstname like "%'.$searchtext.'%" OR b.lastname like "%'.$searchtext.'%") ORDER BY b.lastname');
        $re = Array();

        foreach($result as $value)
        {
            $value->userIcon = getUserIcon($value->concernid);
            $re[] = $value;
        }
        $a = json_encode($re,JSON_UNESCAPED_SLASHES);
        echo json_encode($re,JSON_UNESCAPED_SLASHES);
        break;
    //    评论 朱子武 20160317
    case 'comment':
        /** Start 插入评论 朱子武 20160316*/
        $mycomment = $_GET['mycomment'];
        $blogid = $_GET['blogid'];
        $newcmt = new stdClass;
        $newcmt->commentarea  = 'format_blog';
        $newcmt->itemid       = $blogid;
        $newcmt->component    = 'blog';
        $newcmt->content      = $mycomment;
        $newcmt->format       = '0';
        $newcmt->userid       = $USER->id;
        $newcmt->timecreated  = time();
        $DB->insert_record('learning_comments', $newcmt);
        echo '1';
        /**  START 增加判断添加与我相关  朱子武 20160308*/

        $myresult = $DB->get_records_sql('SELECT id, userid, summary FROM mdl_circles_of_learning WHERE id = '.$newcmt->itemid);

        $blogtitle= $myresult[$newcmt->itemid]->summary;
        $blogurl = '/circlesoflearning/index.php?entryid='.$newcmt->itemid;
        $newrelated =  new stdClass;

        // 保存微博作者
        $newrelated->authorid = $myresult[$newcmt->itemid]->userid;

        $newrelated->userid = $USER->id;

        $newrelated->blogid = $newcmt->itemid;
        $newrelated->blogurl = $blogurl;
        $newrelated->blogtitle = $blogtitle;
        $newrelated->relatedtype = '1';
        $newrelated->relatedtime = $newcmt->timecreated;
        $DB->insert_record('blog_related_me_my', $newrelated, true);

        /**  END 增加判断添加与我相关  朱子武 20160308*/
        break;
    //    删除微博信息 朱子武 201603157
    case 'delete':
        $blogid = $_GET['blogid'];
        global $USER;
        global $DB;
        $blog_result = $DB->get_records_sql('SELECT id, userid, originalblogid FROM mdl_circles_of_learning WHERE id = '.$blogid);
        if(count($blog_result))
        {
            if(($blog_result[$blogid]->userid == $USER->id) || ($USER->id == '2'))
            {
                echo my_deleteBlog($blogid, $blog_result[$blogid]->originalblogid);
            }
            else
            {
                echo '2';
            }
        }
        break;
    default:
        break;
}

/** Star 删除微博数据 朱子武 20160317*/
function my_deleteBlog($blogid, $authorid)
{
    global $DB;
    $deleteid = ($authorid == '')? $blogid: $authorid;
//    $blog_author = $DB->get_records_sql('SELECT id, authorid FROM mdl_circles_of_learning WHERE id = '.$blogid);
//     删除微博
    $DB->delete_records('circles_of_learning',array('id'=>$blogid));
//    删除评论
    $DB->delete_records('learning_comments',array('itemid'=>$blogid));
//    删除与我相关
    $DB->delete_records('blog_related_me_my',array('blogid'=>$blogid));
//                $DB->delete_records('circles_of_learning',array('id'=>$blogid));

//    $mylikecount = '';
//    更新点赞数
    $mylikecount = $DB->get_records_sql('SELECT id, blogid, likecount FROM mdl_blog_like_count_my WHERE blogid = '.$blogid);
    if(count($mylikecount)) {
        $likecount = '';
        $likeid = '';
        foreach ($mylikecount as $like) {
            $likecount = $like->likecount;
            $likeid = $like->id;
        }

        $my_like = new stdClass;
        $my_like->id = $likeid;
        $my_like->blogid = $blogid;
        $my_like->likecount = $likecount - 1;
//        if($my_like->likecount <= 0)
//        {
//            $DB->delete_records('blog_like_count_my',array('id'=>$likeid));
//        }
//        else
//        {
//            $DB->update_record('blog_like_count_my', $my_like);
//        }
        ($my_like->likecount <= 0) ? ($DB->delete_records('blog_like_count_my',array('id'=>$likeid))) : ($DB->update_record('blog_like_count_my', $my_like));
    }
//    更新转发数
    $myforwardedcount = $DB->get_records_sql('SELECT id, blogid, forwardedcount FROM mdl_blog_forwarded_count_my WHERE blogid = '.$deleteid);
    if (count($myforwardedcount)) {
        $forwardedcount = '';
        $forwardedid = '';
        foreach ($myforwardedcount as $forwarde) {
            $forwardedcount = $forwarde->forwardedcount;
            $forwardedid = $forwarde->id;
        }

        $my_forwarde = new stdClass;
        $my_forwarde->id = $forwardedid;
        $my_forwarde->blogid = $deleteid;
        $my_forwarde->forwardedcount = $forwardedcount - 1;
//        $DB->update_record('blog_forwarded_count_my', $my_forwarde);
        ($my_forwarde->forwardedcount <= 0) ? ($DB->delete_records('blog_forwarded_count_my',array('id'=>$forwardedid))) : ($DB->update_record('blog_forwarded_count_my', $my_forwarde));
    }
    return '1';
}
/** End 删除微博数据 朱子武 20160317*/

/** Start 截取用户头像字符串 朱子武 20160316*/
function getUserIcon($concernid)
{
    global $OUTPUT;
    global $DB;
//    $userobject = new stdClass();
//    $userobject->metadata = array();
    $user = $DB->get_record('user', array('id' => $concernid), '*', MUST_EXIST);
    //$str1 = 'hhh/http://localhost/moodle/theme/image.php/more/core/1458032858/u/f2"';
    $str1 = $OUTPUT->user_picture($user,array('link' => false,'visibletoscreenreaders' => false));
//    return str_replace("width=\"35\" height=\"35\"", " ", $userobject->metadata['useravatar']);
//    return str_replace(' ','', $userobject->metadata['useravatar']);
    $str=substr($str1,10);//去除前面
    $n=strpos($str,'"');//寻找位置
    if ($n) $str=substr($str,0,$n);//删除后面
    return $str;
}
/** End 截取用户头像字符串 朱子武 20160316*/