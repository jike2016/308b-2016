<?php

global $CFG;
require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/org/org.class.php');

$allow_action_list = [
    "getchildrenpolice",
    "submitadd"
];//将此行更新到目标文件的首部
if(in_array($_POST['action'],$allow_action_list))
    $_POST['action']();

/** 确认添加 */
function submitadd()
{
    global $DB;
    if(isset($_POST['id']) &&!empty($_POST['id']))
    {
        $data = [];
        foreach ($_POST['arrayObj'] as $value)
        {
            $data[] = [
                'user_id' => $value,
                'police_id' => $_POST['id']
            ];
        }
        $DB->insert_records('police_connection',$data);
//			格式化输出数据
        $res = sprintf_result('');
        echo json_encode($res);
    }
    else
    {
        //	格式化输出数据
        $res = sprintf_result('', 403);
        echo json_encode($res);
    }
}

/** 获取尚未分配警长的干事 */
function getchildrenpolice()
{
    global $DB;
    if(isset($_POST['id']) &&!empty($_POST['id']))
    {
//        $user_id = $_POST['id'];
        $result = $DB->get_records_sql(sprintf('SELECT c.id, a.org_id, c.firstname, c.lastname, b.`data` FROM mdl_org_link_user AS a JOIN mdl_user_info_data AS b ON a.user_id = b.userid JOIN mdl_user AS c ON c.id = b.userid WHERE a.org_id = %s AND b.`data` = "干事" AND a.user_id NOT IN (SELECT d.user_id FROM mdl_police_connection AS d) AND a.user_id NOT IN (SELECT e.userid FROM mdl_role_assignments AS e WHERE e.roleid = 14)', $_POST['id']));
        if(count($result))
        {
//			格式化输出数据
            $res = sprintf_result($result);
            echo json_encode($res);
        }
        else
        {
            //	格式化输出数据
            $res = sprintf_result('', 403);
            echo json_encode($res);
        }
    }
    else
    {
        //	格式化输出数据
        $res = sprintf_result('', 403);
        echo json_encode($res);
    }
}

/** 格式化数据 */
function sprintf_result($data, $status = 200 )
{
    switch ($status)
    {
        case 200:
            $result = [
                'status'=>'200',
                'message'=>'success',
                'data'=>$data
            ];
            break;
        default:
            $result = [
                'status'=>'403',
                'message'=>'error',
                'data'=>$data
            ];
            break;
    }
    return $result;
}