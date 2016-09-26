<style type="text/css">
    .username{   width:125px ;}
    .firstname{   width: 60px;}
    .phone1{   width: 100px;}
    .description{  }
    .registertime{    width: 130px;}
    .status{}
    .actions{}

    #id_submitbutton{margin:0px};
</style>

<?php
/**
 * 超好管理员审核用户注册页面
 *叶靖华
 *20130331
 */
require(dirname(__FILE__).'/../config.php');
require_once("$CFG->libdir/formslib.php");

$delete     = optional_param('delete', 0, PARAM_INT);
$pass     = optional_param('pass', 0, PARAM_INT);
$donotpass     = optional_param('donotpass', 0, PARAM_INT);
$check_type = optional_param('check_type', 0, PARAM_INT);//默认显示未审核
$current_page=optional_param('page', 1, PARAM_INT);//当前页码
$returnurl = new moodle_url('/register/admin_check.php');
require_login();//要求登录
global $USER;
if($USER->id!=2){
	redirect(new moodle_url('/../moodle/index.php'));
}
$PAGE->set_url('/register/admin_check.php');
$PAGE->set_title('注册审核');
$PAGE->set_heading('注册审核');
$PAGE->set_pagelayout('registerforadmin');//设置layout

$eachpage=20;    //设置每页的条目

function my_get_check_count($check_type,$eachpage)    //获取审核数目的页码
{
    global $DB;
    if ($check_type==-1){
        $check_count = $DB->get_records_sql('SELECT id as mycount FROM mdl_register_temp_user order by timecreated desc ;');
    }else {
        $check_count = $DB->get_records_sql('SELECT id as mycount FROM mdl_register_temp_user WHERE admin_check=? order by timecreated desc;', array($check_type));
    }
    $mycount = count($check_count);
    $mycount = ceil($mycount/$eachpage);
    return ($mycount <= 1 ? 1: $mycount);
}

function my_get_check($check_type, $current_page, $eachpage) //获取审核条目
{

    $my_page = $current_page * $eachpage;
    global $DB;
    global $OUTPUT;
    $table = new html_table();
    $table->attributes['class'] = 'collection';
    $table->head = array(
        '用户名',
        '姓名',
        '电话',
        '所属单位',
        '自述',
        '申请时间',
        '审核状态',
        '动作'
    );
    $table->colclasses = array('username', 'firstname','phone1','org_name','description','registertime','status', 'actions');

    if ($check_type==-1) {  //从数据库获取条目  按时间戳降序排序
        $temp_users = $DB->get_records_sql('select * FROM mdl_register_temp_user order by timecreated DESC LIMIT '.$my_page.','.$eachpage.';');
    }else{
        $temp_users = $DB->get_records_sql('select * FROM mdl_register_temp_user WHERE admin_check=' . $check_type . ' order by timecreated desc LIMIT '.$my_page.','.$eachpage.';');
    }
    $current_page++;
    foreach ($temp_users as $tag) {
        $actions=null;
        $username = $tag->username;
        $firstname = $tag->firstname;
        $phone=$tag->phone1;
        $description=$tag->description;
        $gettime=$tag->timecreated;
        $date = usergetdate($gettime);
        $registertime=$date['year'].'年'.$date['mon'].'月'.$date['mday'].'日'.$date['hours'].'时'.$date['minutes'].'分';
        $org_name = '';
        if($tag->org_id != 0 ){
            $org_node = $DB->get_record_sql("select o.name from mdl_org o where o.id = $tag->org_id");
            $org_name = $org_node->name;
        }

        //添加动作按钮
        if($tag->admin_check==0||$tag->admin_check==2) {
            $url = new moodle_url(qualified_me());
            $url->params(array('pass'=>$tag->id,'check_type'=>$check_type,'page'=>$current_page));
            $actions .= $OUTPUT->action_icon($url, new pix_icon('t/assignroles', '通过')) . " ";
        }
        if($tag->admin_check==0) {
            $url = new moodle_url(qualified_me());
            $url->params(array('donotpass'=>$tag->id,'check_type'=>$check_type,'page'=>$current_page));
            $actions .= $OUTPUT->action_icon($url, new pix_icon('t/show', '不通过')) . " ";
        }
		if($tag->admin_check==0||$tag->admin_check==2) {
			$url = new moodle_url(qualified_me());
			$url->params(array('delete'=>$tag->id,'check_type'=>$check_type,'page'=>$current_page));
			$actions .= $OUTPUT->action_icon($url, new pix_icon('t/delete', '删除')) . " ";
		}
        $status='';
        if($tag->admin_check==0){
            $status='<span style="color: #00a0f0">未审核</span>';
        }elseif($tag->admin_check==1){
            $status='<span style="color: #00AA00">已通过</span>';
        }elseif($tag->admin_check==2){
            $status='<span style="color: #990000">未通过</span>';
        }
        $row = array($username, $firstname, $phone, $org_name,$description,$registertime,$status,$actions);
        $table->data[] = $row;
    }
    return $table;
}

function my_get_check_current_count($count_page, $check_type)   //输出页码
{
    for($num = 1; $num <= $count_page; $num ++)
    {
        echo'<a href="../register/admin_check.php?check_type='.$check_type.'&page='.$num.'">'.$num.'</a>  ';
    }
}

class admin_check_form extends moodleform {
    public function definition() {
        global $CFG;
        $mform = $this->_form;
        $select=array();
        $select[] =& $mform->createElement('static', null, '','选择类型');
        $check_type = optional_param('check_type', 0, PARAM_INT);
        switch($check_type){
            case 0:
                $select[] =& $mform->createElement('select', 'check_type', '', array('0' => '未审核', '1' => '已通过', '2' => '未通过', '-1' => '全部'));
                break;
            case -1:
                $select[] =& $mform->createElement('select', 'check_type', '', array('-1' => '全部','0' => '未审核', '1' => '已通过', '2' => '未通过'));
                break;
            case 1:
                $select[] =& $mform->createElement('select', 'check_type', '', array( '1' => '已通过','0' => '未审核', '2' => '未通过', '-1' => '全部'));
                break;
            case 2:
                $select[] =& $mform->createElement('select', 'check_type', '', array('2' => '未通过','0' => '未审核', '1' => '已通过', '-1' => '全部'));
                break;
        }
        $select[] =& $mform->createElement('submit', 'submitbutton', '确定');
        $mform->addGroup($select, 'buttonar', '', array(' '), false);

    }
}

if ($delete) {  //删除审核
    $DB->delete_records('register_temp_user', array('id' => $delete));
}else if ($pass) {   //通过审核
    $registerUser = new stdClass();
    $tempUser = $DB->get_record('register_temp_user', array('id' => $pass));
    $registerUser->username = $tempUser->username;
    $registerUser->password = $tempUser->password;
    $registerUser->firstname = $tempUser->firstname;
    $registerUser->phone1 = $tempUser->phone1;
    $registerUser->description = $tempUser->description;

    $registerUser->confirmed = 1;
    $registerUser->mnethostid = 1;
    $registerUser->country = "CN";
    $registerUser->lang = "zh_cn";
    $registerUser->timecreated = time();
    $registerUser->email = date("ymd") . time() . rand(0, 900000) . "@gcmooc.com";

    $newUserID = $DB->insert_record("user", $registerUser);//插入数据库
    $DB->insert_record('org_link_user', array('org_id' => $tempUser->org_id,'user_id'=>$newUserID),true);//编入组织架构
    $DB->update_record('register_temp_user', array('id' => $pass,'admin_check'=>1));

}else if ($donotpass){  //不通过审核
    $DB->update_record('register_temp_user', array('id' => $donotpass,'admin_check'=>2));
}

$currenturl = new moodle_url('/register/admin_check.php');
echo $OUTPUT->header();
echo '<h2>注册审核</h2>';
$mform = new admin_check_form($currenturl);
global $DB;
$count_page=my_get_check_count($check_type,$eachpage);
$table=my_get_check($check_type,$current_page-1,$eachpage);

if ($fromform = $mform->get_data()) {
    redirect(new moodle_url('/register/admin_check.php?check_type='.$fromform->check_type));
}else{
    $mform->display();
    echo $htmltable = html_writer::table($table);
    if ($count_page>1) {
        echo '<div>
                  <a href="../register/admin_check.php?check_type=' . $check_type . '&page=1">首页</a>
                   <a href="../register/admin_check.php?check_type=' . $check_type . '&page=' . ($current_page <= 1 ? 1 : $current_page - 1) . '">上一页</a>  ';
        my_get_check_current_count($count_page,$check_type);

        echo '<a href="../register/admin_check.php?check_type=' . $check_type . '&page=' . ($current_page < $count_page ? ($current_page + 1) : $count_page) . '">下一页</a>
                <a href="../register/admin_check.php?check_type=' . $check_type . '&page=' . $count_page . '">尾页</a>
                                 </div>';
    }
}

echo $OUTPUT->footer();


