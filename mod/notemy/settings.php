<?php
/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License") +  you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('notemy1', 'notemy1', 'notemy2', "notemy3", PARAM_TEXT));
//如果有参数则跳转
if(count($_GET)!=0)
		{
			//if($_GET["section"]=='modsettingnotemy'){
			if(isset($_GET["section"])&&($_GET["section"]=='modsettingnotemy')){
				header("Location: ../mod/notemy/index.php");
			}
			
		}
//重定向浏览器 
//header("Location: http://www.baidu.com"); 
//确保重定向后，后续代码不会被执行 
//