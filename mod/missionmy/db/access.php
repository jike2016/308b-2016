<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    'mod/missionmy:addinstance' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
			// 'guest' => CAP_ALLOW,
            'manager' => CAP_ALLOW
            
        ),
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ),
	'mod/missionmy:read' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'frontpage' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        )
    ),

);