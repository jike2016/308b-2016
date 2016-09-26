<style type="text/css">
    .username{   width:125px ;}
    .firstname{   width: 60px;}
    .phone1{   width: 100px;}
    .description{  }
    .registertime{    width: 130px;}
    .status{}
    .actions{}
</style>

<?php
/**
 * 分级管理员课程管理页面
 * 主要功能：课程创建、课程属性管理、课程学习内容管理、课程所属管理单位管理、课程可查看单位管理
 */
require('../../config.php');
require_once($CFG->libdir."/formslib.php");
require_once($CFG->dirroot."/user/my_role_conf.class.php");
require_once($CFG->dirroot."/org_classify/org.class.php");

$delete     = optional_param('delete', 0, PARAM_INT);
$check_type = optional_param('check_type', 0, PARAM_INT);//默认显示未审核
$current_page=optional_param('page', 1, PARAM_INT);//当前页码
$returnurl = new moodle_url('/course/course_classify/index_gradingAdmin.php');
require_login();//要求登录
$PAGE->set_pagelayout('registerforadmin');//设置layout

/**start  权限判断 只允许分级管理员 ，要同时是*/
global $USER;
$role =  new my_role_conf();
if(!$DB->record_exists("role_assignments",array('userid'=>$USER->id,'roleid'=>$role->get_gradingadmin_role())) ){
	redirect(new moodle_url('/../moodle/index.php'));
}
if(!$DB->record_exists("org_link_user",array('user_id'=>$USER->id))){//当账号所属的单位被删除时
    echo "<script>alert('您当前的账号未分配所属单位，请联系系统管理员');</script>";
    exit();
}
/**end 权限判断  */

//根据当前分级管理员所在的级别，获取其可管理的下级单位
$org = new org();
$admin_nodeID = $org->get_nodeid_with_userid($USER->id);
$admin_node = $org->get_node($admin_nodeID);
$child_node = $org->get_all_child($admin_node);
$orgIDStr = ''.$admin_nodeID;
if($child_node){
    $child_nodeIDs = array_column($child_node,'id');
    $orgIDStr .= ','.implode(',',$child_nodeIDs);
}

$PAGE->set_url('/course/course_classify/index_gradingAdmin.php');
$PAGE->set_title('课程管理');
$PAGE->set_heading('课程管理');

$eachpage=20;    //设置每页的条目

function my_get_check_count($eachpage,$orgIDStr)    //获取审核数目的页码
{
    global $DB;
    $check_count = $DB->get_records_sql("select co.id from mdl_course_org_my co
                                            join mdl_course c on co.courseid = c.id
                                            where co.manage_org in ($orgIDStr)
                                            order by c.timecreated desc");

    $mycount = count($check_count);
    $mycount = ceil($mycount/$eachpage);
    return ($mycount <= 1 ? 1: $mycount);
}

function my_get_check( $current_page, $eachpage,$orgIDStr) //获取审核条目
{
    $my_page = $current_page * $eachpage;
    global $CFG;
    global $DB;
    global $OUTPUT;
    $table = new html_table();
    $table->attributes['class'] = 'collection';
    $table->head = array(
        '序号',
        '课程名称',
        '所属分类',
        '管理单位(包括上级单位)',
        '可浏览单位(包括下属单位)',
        '创建时间',
        '动作'
    );
    $table->colclasses = array('No', 'course_name','category','org_name','browseable_org','create_time', 'actions');
    $temp_courses = $DB->get_records_sql("select c.id as courseid,c.fullname as course_name,c.category,c.timecreated,co.browseable_org,co.manage_org from mdl_course_org_my co
                                            join mdl_course c on co.courseid = c.id
                                            where co.manage_org in ($orgIDStr)
                                            order by c.timecreated desc
                                            LIMIT $my_page,$eachpage");

    $current_page++;
    $no = 0;
    foreach ($temp_courses as $tag) {
        $actions=null;
        $no++;
        $course_name = '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$tag->courseid.'" target="_blank" >'.$tag->course_name;
        //start 课程所属分类  注意：这里的课程采用多分类，不用mdl_course 中的分类了！
        $coursecategories = $DB->get_records_sql("select cc.id,cc.name as categoryname  from mdl_course_categories as cc where cc.id in
                                              (select cl.mdl_course_categories_id as id from mdl_course_link_categories cl where cl.mdl_course_id = $tag->courseid)");
        $category = '';
        foreach($coursecategories as $value){
            $category .= $value->categoryname.'</br>';
        }
        //end 课程所属分类
        $mannage_org = '';
        if($tag->manage_org){
            $org_node = $DB->get_record_sql("select o.name from mdl_org o where o.id = $tag->manage_org");
            $mannage_org = $org_node->name;
        }
        $browsable_org = '';
        if($tag->browseable_org){
            $org_nodes = $DB->get_records_sql("select o.name from mdl_org o where o.id in ($tag->browseable_org)");
            foreach($org_nodes as $node){
                $browsable_org .= $node->name.'</br>';
            }
        }
        $create_time=$tag->timecreated;
        $date = usergetdate($create_time);
        $create_time = $date['year'].'年'.$date['mon'].'月'.$date['mday'].'日'.$date['hours'].'时'.$date['minutes'].'分';

        //添加动作按钮
        $url = new moodle_url('create_course.php');
        $url->params(array('id'=>$tag->courseid,'returnto'=>'catmanage'));
        $actions .= $OUTPUT->action_icon($url, new pix_icon('t/preferences', '编辑')) . " | ";
//        $url = new moodle_url(qualified_me());
        $url = new moodle_url("delete_my.php");
        $url->params(array('id'=>$tag->courseid,'page'=>$current_page));
        $actions .= $OUTPUT->action_icon($url, new pix_icon('t/delete', '删除')) . " ";

        $row = array($no, $course_name, $category, $mannage_org,$browsable_org,$create_time,$actions);
        $table->data[] = $row;
    }
    return $table;
}

function my_get_check_current_count($count_page)   //输出页码
{
    for($num = 1; $num <= $count_page; $num ++)
    {
        echo'<a href="index_gradingAdmin.php?page='.$num.'">'.$num.'</a>  ';
    }
}

class admin_check_form extends moodleform {
    public function definition() {
        global $CFG;
        $mform = $this->_form;
    }
}

$currenturl = new moodle_url('/course/course_classify/index_gradingAdmin.php');
echo $OUTPUT->header();
echo '<h2>课程管理</h2>';
$tempCategory = $DB->get_record_sql("select MIN(id) as id from mdl_course_categories ");//为创建课程设定默认的所属分类
echo $OUTPUT->single_button(new moodle_url('create_course.php', array('confirm' => 1,'category'=>$tempCategory->id,'returnto'=>'category')), '创建课程');
$mform = new admin_check_form($currenturl);
global $DB;
$count_page=my_get_check_count($eachpage,$orgIDStr);
$table=my_get_check($current_page-1,$eachpage,$orgIDStr);

if ($fromform = $mform->get_data()) {
    redirect(new moodle_url('/course/course_classify/index_gradingAdmin.php'));
}else{
    $mform->display();
    echo $htmltable = html_writer::table($table);
    if ($count_page>1) {
        echo '<div>
                  <a href="index_gradingAdmin.php?page=1">首页</a>
                   <a href="index_gradingAdmin.php?page=' . ($current_page <= 1 ? 1 : $current_page - 1) . '">上一页</a>  ';
        my_get_check_current_count($count_page);

        echo '<a href="index_gradingAdmin.php?page=' . ($current_page < $count_page ? ($current_page + 1) : $count_page) . '">下一页</a>
                <a href="index_gradingAdmin.php?page=' . $count_page . '">尾页</a>
                                 </div>';
    }
}

echo $OUTPUT->footer();


