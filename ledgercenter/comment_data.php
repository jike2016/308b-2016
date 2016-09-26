<?php
global $CFG;
require_once($CFG->dirroot . '/user/my_role_conf.class.php');

$role = new my_role_conf();
$unit_role = $role->get_unit_role();
$gradingadmin_role = $role->get_gradingadmin_role();
$remove_role = $unit_role.','.$gradingadmin_role;//需要移除的角色，14：单位角色 15：分权管理员角色
