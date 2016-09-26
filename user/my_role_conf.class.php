<?php
/**
 * Created by PhpStorm.
 * User: fubo_01
 * Date: 2016/8/18
 * Time: 16:53
 *
 * 角色配置：用于适配数据库中角色的改动(对应数据库中的角色id)
 */

class my_role_conf
{
    //超级管理员,注意：超级管理员只设定了一个，用的是 用户id 来判断，而不是用 角色id 判断！！！
//    private $superadmin_role = '';//不要改，这个固定值在其他页面已经被多次使用了！
    //慕课管理员
    private $courseadmin_role = '26';
    //分级管理员
    private $gradingadmin_role = '27';
    //单位角色
    private $unit_role = '14';//不要改，这个固定值在其他页面已经被多次使用了！
    //学生角色
    private $student_role = '5';//不要改，这个固定值在其他页面已经被多次使用了！

//    public function get_superadmin_role(){
//        return $this->superadmin_role;
//    }
    public function get_courseadmin_role(){
        return $this->courseadmin_role;
    }
    public function get_gradingadmin_role(){
        return $this->gradingadmin_role;
    }
    public function get_unit_role(){
        return $this->unit_role;
    }
    public function get_student_role(){
        return $this->student_role;
    }

//    public function set_superadmin_role($id){
//        $this->superadmin_role = $id;
//    }
    public function set_courseadmin_role($id){
        $this->courseadmin_role = $id;
    }
    public function set_gradingadmin_role($id){
        $this->gradingadmin_role = $id;
    }
//    public function set_unit_role($id){
//        $this->unit_role = $id;
//    }
//    public function set_student_role($id){
//        $this->student_role = $id;
//    }

}

