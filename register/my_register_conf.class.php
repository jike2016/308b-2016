<?php
/**
 * Created by PhpStorm.
 * User: fubo_01
 * Date: 2016/8/25
 * Time: 11:01
 *
 * 注册配置
 */

class my_register_conf{

    private $level = 2;//注册中所属单位等级



    public function get_level(){
        return $this->level;
    }
    public function set_level($level){
        $this->level = $level;
    }

}