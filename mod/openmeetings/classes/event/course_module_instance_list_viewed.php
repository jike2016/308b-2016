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
namespace mod_openmeetings\event;
defined('MOODLE_INTERNAL') || die();
class course_module_instance_list_viewed extends \core\event\course_module_instance_list_viewed {
	/**
	 * Create the event from course record.
	 *
	 * @param \stdClass $course        	
	 * @return course_module_instance_list_viewed
	 */
	public static function create_from_course(\stdClass $course) {
		$params = array ('context' => \context_course::instance($course->id));
		$event = \mod_book\event\course_module_instance_list_viewed::create($params);
		$event->add_record_snapshot('course', $course);
		return $event;
	}
}
