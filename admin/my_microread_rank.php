<?php
/** 微阅日志记录更新、排名榜数据采集 徐东威 20160514 */
//初始化数据库表
//更新书库排行榜相关数据表
//更行文库排行相关数据表
//删除日志表中已经被删除的书籍、文档日志

//cron 函数调用入口
function microread_rank(){

    inittalbe();//初始化书库、文库表
    del_microreadlog();//删除日志表中已经被删除的书籍、文档日志
    ebook();//更新书库排行表
    doc();//更新文库排行表
}

//Start 初始化数据库表
function inittalbe(){

    global $DB;

    //初始化 mdl_ebook_author_rank_my
    $recoedcount = $DB->get_record_sql("select count(1) as count from mdl_ebook_author_rank_my");
    if($recoedcount->count == 0){
        for($i=1;$i<11;$i++){
            $recoed = new stdClass();
            $recoed->id = $i;
            $recoed->authorid = '';
            $recoed->authorname = '';
            $recoed->rankcount = '';
            $newrecode = $DB->insert_record("ebook_author_rank_my",$recoed,true);
        }
    }
    //初始化 mdl_ebook_hot_rank_my
    $recoedcount = $DB->get_record_sql("select count(1) as count from mdl_ebook_hot_rank_my");
    if($recoedcount->count == 0){
        for($i=1;$i<31;$i++){
            $recoed = new stdClass();
            $recoed->id = $i;
            $recoed->contextid = '';
            $recoed->name = '';
            $recoed->rankcount = '';
            if($i<11){
                $recoed->ranktype = 1;
                $newrecode = $DB->insert_record("ebook_hot_rank_my",$recoed,true);
            }
            elseif($i>10 && $i<21){
                $recoed->ranktype = 2;
                $newrecode = $DB->insert_record("ebook_hot_rank_my",$recoed,true);
            }
            elseif($i>20 && $i<31){
                $recoed->ranktype = 3;
                $newrecode = $DB->insert_record("ebook_hot_rank_my",$recoed,true);
            }
        }
    }
    //初始化 mdl_doc_contributor_rank_my
    $recoedcount = $DB->get_record_sql("select count(1) as count from mdl_doc_contributor_rank_my");
    if($recoedcount->count == 0){
        for($i=1;$i<11;$i++){
            $recoed = new stdClass();
            $recoed->id = $i;
            $recoed->uploaderid = '';
            $recoed->uploadusername = '';
            $recoed->rankcount = '';
            $newrecode = $DB->insert_record("doc_contributor_rank_my",$recoed,true);
        }
    }
    //初始化 mdl_doc_hot_rank_my
    $recoedcount = $DB->get_record_sql("select count(1) as count from mdl_doc_hot_rank_my");
    if($recoedcount->count == 0){
        for($i=1;$i<31;$i++){
            $recoed = new stdClass();
            $recoed->id = $i;
            $recoed->contextid = '';
            $recoed->name = '';
            $recoed->suffix = '';
            $recoed->rankcount = '';
            if($i<11){
                $recoed->ranktype = 1;
                $newrecode = $DB->insert_record("doc_hot_rank_my",$recoed,true);
            }
            elseif($i>10 && $i<21){
                $recoed->ranktype = 2;
                $newrecode = $DB->insert_record("doc_hot_rank_my",$recoed,true);
            }
            elseif($i>20 && $i<31){
                $recoed->ranktype = 3;
                $newrecode = $DB->insert_record("doc_hot_rank_my",$recoed,true);
            }
        }
    }
}
//End 初始化数据库表

//Start 更新书库排行榜相关数据表
function ebook(){
    global $DB;
    //更新热门作者
    $authorranks = $DB->get_records_sql("select ea.id,ea.`name` as authorname,count(1) as rank from mdl_ebook_author_my ea
								left join mdl_ebook_my em on ea.id = em.authorid
								left join mdl_microread_log ml on em.id = ml.contextid
								where ml.target = 1
								group by ea.id
								order by rank desc
								limit 0,10");
    if(count($authorranks)<10){
        $i = 10 - count($authorranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->id = '';
            $recode->authorname = '';
            $recode->rankcount = '';
            $authorranks[] = $recode;
        }
    }
    $i = 1;
    foreach($authorranks as $authorrank){
        $result = $DB->update_record("ebook_author_rank_my",array("id"=>$i,"authorid"=>$authorrank->id,"authorname"=>$authorrank->authorname,"rankcount"=>$authorrank->rank));
        $i++;
    }
    //更新热门排行榜
    $weektime = time()-3600*24*7;//一周前
    $monthtime = time()-3600*24*30;//一月前
    //周
    $weekranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_ebook_my e
                                        left join  mdl_microread_log m on m.contextid = e.id
                                        where  m.target = 1 and m.action = 'view' and m.timecreated > $weektime
                                        group by m.contextid
                                        order by rank desc
                                        limit 0,10");
    if(count($weekranks)<10){
        $i = 10 - count($weekranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->name = '';
            $recode->rankcount = '';
            $weekranks[] = $recode;
        }
    }
    $i = 1;
    foreach($weekranks as $weekrank){
        $result = $DB->update_record("ebook_hot_rank_my",array("id"=>$i,"contextid"=>$weekrank->contextid,"name"=>$weekrank->bookname,"rankcount"=>$weekrank->rank));
        $i++;
    }
    //月
    $monthranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_ebook_my e
                                    left join  mdl_microread_log m on m.contextid = e.id
                                    where  m.target = 1 and m.action = 'view' and m.timecreated > $monthtime
                                    group by m.contextid
                                    order by rank desc
                                    limit 0,10");
    if(count($monthranks)<10){
        $i = 10 - count($monthranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->name = '';
            $recode->rankcount = '';
            $monthranks[] = $recode;
        }
    }
    $i = 11;
    foreach($monthranks as $monthrank){
        $result = $DB->update_record("ebook_hot_rank_my",array("id"=>$i,"contextid"=>$monthrank->contextid,"name"=>$monthrank->bookname,"rankcount"=>$monthrank->rank));
        $i++;
    }
    //总
    $totalranks = $DB->get_records_sql("select m.contextid,count(1) as rank ,m.target,e.id as ebookid,e.name as bookname from mdl_ebook_my e
                                    left join  mdl_microread_log m on m.contextid = e.id
                                    where  m.target = 1 and m.action = 'view'
                                    group by m.contextid
                                    order by rank desc
                                    limit 0,10");
    if(count($totalranks)<10){
        $i = 10 - count($totalranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->name = '';
            $recode->rankcount = '';
            $totalranks[] = $recode;
        }
    }
    $i = 21;
    foreach($totalranks as $totalrank){
        $result = $DB->update_record("ebook_hot_rank_my",array("id"=>$i,"contextid"=>$totalrank->contextid,"name"=>$totalrank->bookname,"rankcount"=>$totalrank->rank));
        $i++;
    }


}
//End 更新书库排行榜相关数据表

//Start 更行文库排行相关数据表
function doc(){
    global $DB;

    //更新热门贡献者
    $doccontributorslists = $DB->get_records_sql("select dm.uploaderid,count(1) as rankcount,u.firstname as uploadusername from mdl_user u
												left join  mdl_doc_my dm on u.id = dm.uploaderid
												left join mdl_microread_log ml on dm.id = ml.contextid
												where ml.action = 'view'
												and ml.target = 2
												group by dm.uploaderid
												order by rankcount desc
												limit 0,10");
    if(count($doccontributorslists)<10){
        $i = 10 - count($doccontributorslists);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->uploaderid = '';
            $recode->uploadusername = '';
            $recode->rankcount = '';
            $doccontributorslists[] = $recode;
        }
    }
    $i = 1;
    foreach($doccontributorslists as $doccontributorslist){
        $result = $DB->update_record("doc_contributor_rank_my",array("id"=>$i,"uploaderid"=>$doccontributorslist->uploaderid,"uploadusername"=>$doccontributorslist->uploadusername,"rankcount"=>$doccontributorslist->rankcount));
        $i++;
    }
    //更新热门排行榜
    $weektime = time()-3600*24*7;//一周前
    $monthtime = time()-3600*24*30;//一月前

    //周
    $weekranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_doc_my dm
									left join mdl_microread_log ml on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									and ml.timecreated > $weektime
									group by ml.contextid
									order by rankcount desc
									limit 0,10 ");
    if(count($weekranks)<10){
        $i = 10 - count($weekranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->docname = '';
            $recode->doctype = '';
            $recode->rankcount = '';
            $weekranks[] = $recode;
        }
    }
    $i = 1;
    foreach($weekranks as $weekrank){
        $result = $DB->update_record("doc_hot_rank_my",array("id"=>$i,"contextid"=>$weekrank->contextid,"name"=>$weekrank->docname,"suffix"=>$weekrank->doctype,"rankcount"=>$weekrank->rankcount));
        $i++;
    }

    //月
    $monthranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_doc_my dm
									left join mdl_microread_log ml on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									and ml.timecreated > $monthtime
									group by ml.contextid
									order by rankcount desc
									limit 0,10");
    if(count($monthranks)<10){
        $i = 10 - count($monthranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->docname = '';
            $recode->doctype = '';
            $recode->rankcount = '';
            $monthranks[] = $recode;
        }
    }
    $i = 11;
    foreach($monthranks as $monthrank){
        $result = $DB->update_record("doc_hot_rank_my",array("id"=>$i,"contextid"=>$monthrank->contextid,"name"=>$monthrank->docname,"suffix"=>$monthrank->doctype,"rankcount"=>$monthrank->rankcount));
        $i++;
    }
    //总
    $totalranks = $DB->get_records_sql("select ml.contextid,count(1) as rankcount,dm.`name` as docname,dm.suffix as doctype from mdl_doc_my dm
									left join mdl_microread_log ml on ml.contextid = dm.id
									where ml.action = 'view'
									and ml.target = 2
									group by ml.contextid
									order by rankcount desc
									limit 0,10");
    if(count($totalranks)<10){
        $i = 10 - count($totalranks);
        for($i;$i>0;$i--){
            $recode = new stdClass();
            $recode->contextid = '';
            $recode->docname = '';
            $recode->doctype = '';
            $recode->rankcount = '';
            $totalranks[] = $recode;
        }
    }
    $i = 21;
    foreach($totalranks as $totalrank){
        $result = $DB->update_record("doc_hot_rank_my",array("id"=>$i,"contextid"=>$totalrank->contextid,"name"=>$totalrank->docname,"suffix"=>$totalrank->doctype,"rankcount"=>$totalrank->rankcount));
        $i++;
    }

}
//End 更行文库排行相关数据表

//Start 删除日志表中已经被删除的书籍、文档日志
function del_microreadlog(){
    global $DB;

    //获取已经删除的书籍记录
    $errorbooks = $DB->get_records_sql("select ml.id from mdl_microread_log ml
                                        where ml.action = 'view' and ml.target = 1
                                        and ml.contextid not in (
                                        select em.id from mdl_ebook_my em)");
    foreach($errorbooks as $errorbook){
        $book = $DB->delete_records("microread_log",array("id" => $errorbook->id));
    }
    //获取已经删除的文档记录
    $errordocs = $DB->get_records_sql("select ml.id from mdl_microread_log ml
                                        where ml.action = 'view' and ml.target = 2
                                        and ml.contextid not in (
                                        select em.id from mdl_doc_my em)");
    foreach($errordocs as $errordoc){
        $doc = $DB->delete_records("microread_log",array("id" => $errordoc->id));
    }
}
//End 删除日志表中已经被删除的书籍、文档日志

