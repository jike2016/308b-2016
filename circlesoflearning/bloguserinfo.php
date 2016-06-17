<?php
/** START 朱子武 获取用户个人资料 20160329*/

require_once(dirname(__FILE__) . '/../config.php');

global $DB;
global $USER;
global $OUTPUT;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $uid = $_POST['uid'];

    $org_value = new stdClass();

    if($USER->id != 2 && $uid == 2)
    {
        $org_value->name = '您没有权限查看此用户';
        echo json_encode($org_value);
    }
    else
    {
        $result = $DB->get_records_sql('SELECT a.id, a.firstname, a.phone1, b.org_id, c.name, c.parent FROM mdl_user AS a JOIN mdl_org_link_user AS b ON a.id = b.user_id JOIN mdl_org AS c ON b.org_id = c.id WHERE a.id = '.$uid);

        foreach($result as $value)
        {
            $org_result_name = array();
            while($value->parent !== '-1')
            {
                $org_result = $DB->get_records_sql('SELECT id, name, parent FROM mdl_org WHERE id = '.$value->parent);
                foreach($org_result as $org_parent)
                {
                    $org_result_name[] = $org_parent->name;
                    $value->parent = $org_parent->parent;
                }
            }
            if($value->phone1 === '')
            {
                $value->phone1 = '此用户没有电话号码';
            }
            $name = '';
            for($i = count($org_result_name) - 1; $i >= 0 ; $i--)
            {
                $name .= '-'.$org_result_name[$i];
            }
            $value->name = $name.'-'.$value->name;
			$value->name = substr($value->name,1);
            $org_value = $value;
        }


        $user = $DB->get_record('user', array('id' => $uid), '*', MUST_EXIST);
        $userIcon = $OUTPUT->user_picture (
            $user,
            array(
                'link' => false,
                'visibletoscreenreaders' => false
            )
        );
        $org_value->usericon = str_replace('width="35" height="35"', " ", $userIcon);


        if(isset($org_value))
        {
            echo str_replace('\\/','/', json_encode($org_value));
        }
    }
}
