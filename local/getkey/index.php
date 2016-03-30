<?php
// This file is part of Moodle - http://vidyamantra.com/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Authentication key
 *
 * @package    local
 * @subpackage get_key
 * @copyright  2014 Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('key_form.php');

$k = optional_param('k', 0, PARAM_NOTAGS);
$e = optional_param('e', null, PARAM_NOTAGS);

require_login();
require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('getkey');

$PAGE->set_url(new moodle_url('/local/getkey/index.php'));

$mform = new local_getkey_key_form(null, array('email' => $USER->email, 'firstname' => $USER->firstname ,
    'lastname' => $USER->lastname , 'domain' => $CFG->wwwroot));

// There should be form submit
// Form submitted throug js and result received in url.
if ($mform->is_cancelled()) {
    // Do nothing.
} else if ($fromform = $mform->get_data()) {
    // Redirect($nexturl).
}
echo $OUTPUT->header();

if ($result = get_config('local_getkey', 'keyvalue')) {
    //echo html_writer::tag('div', get_string('keyis', 'local_getkey').$result, array('class' => 'box generalbox alert'));
    echo html_writer::start_tag('div', array('class' => 'box generalbox alert'));
    echo get_string('keyis', 'local_getkey').$result."\t";
    $url = new moodle_url('/local/getkey/savekey.php', array('action'=>'confirmdelete', 'sesskey'=>sesskey()));
    echo  html_writer::link($url, '<img src = "'.$OUTPUT->pix_url('t/delete').'" class = "iconsmall" alt="'.get_string('delete').'" title = "'.get_string('delete').'" />');
    echo html_writer::end_tag('div');


    // Stat of vidya.io api start------------------------
    $PAGE->requires->js('/local/getkey/stat/d3.v3.min.js');
    $PAGE->requires->js('/local/getkey/stat/underscore-min.js');
    $PAGE->requires->js('/local/getkey/stat/function.js');
    $PAGE->requires->js('/local/getkey/stat/jsonp.js');

    $module = array(
        'name' => 'getkey_stat',
        'fullpath' => '/local/getkey/stat/stat.js',
        'requires' => array('node', 'event'),
        'strings' => array(),
    );
    $PAGE->requires->strings_for_js(array('msggraph', 'usrgraph', 'nodata'), 'local_getkey');
    $PAGE->requires->js_init_call('getkey_stat_init', array($result), false, $module);

    echo html_writer::tag('h3', get_string('graphheading', 'local_getkey'));
    echo html_writer::start_tag('div', array('id' => 'graph', 'class' => 'aGraph'));
        echo html_writer::start_tag('div', array('id' => 'option'));
            echo html_writer::empty_tag('input', array(
            'type' => 'button', 'name' => 'dstat', 'value' => get_string('today', 'local_getkey'), 'id' => 'id_day_stat'));
            echo html_writer::empty_tag('input', array(
            'type' => 'button', 'name' => 'currmstat', 'value' => get_string('currentmonth', 'local_getkey'), 'id' => 'id_currmonth_stat'));
            echo html_writer::empty_tag('input', array(
            'type' => 'button', 'name' => 'premstat', 'value' => get_string('previousmonth', 'local_getkey'), 'id' => 'id_premonth_stat'));
            echo html_writer::empty_tag('input', array(
            'type' => 'button', 'name' => 'ystat', 'value' => get_string('year', 'local_getkey'), 'id' => 'id_year_stat'));
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('id' => 'msggraph', 'class' => 'aGraph'));
        echo html_writer::end_tag('div');
        echo html_writer::start_tag('div', array('id' => 'usergraph', 'class' => 'aGraph'));
        echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
    // Stat of vidya.io api end------------------------

} else if ($k) { // Key received from vidya.io.
    if (!set_config('keyvalue', $k, 'local_getkey')) {
        echo $OUTPUT->error_text(get_string('keynotsaved', 'local_getkey'));
    }
    echo $OUTPUT->heading(get_string('keyis', 'local_getkey').$k, 3, 'box generalbox', 'jpoutput');

} else {
    if($e){
        echo html_writer::tag('div', $e, array('class' => 'alert alert-error'));
    }

    echo html_writer::tag('div', get_string('havekey', 'local_getkey'), array('class' => 'alert alert-notice'));
    // Loading three other YUI modules.
    $jsmodule = array(
                'name' => 'local_getkey',
                'fullpath' => '/local/getkey/module.js',
                'requires' => array('json', 'jsonp', 'jsonp-url', 'io-base', 'node', 'io-form'));
    $PAGE->requires->js_init_call('M.local_getkey.init', null, false, $jsmodule);
    $PAGE->requires->string_for_js('keyis', 'local_getkey');

    echo $OUTPUT->box(get_string('message', 'local_getkey'), "generalbox center clearfix");
    $mform->display();
}

// Create vm token.
if (!$re = get_config('local_getkey', 'tokencode')) {
    $tokencode = substr(  time(), -4).substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" , mt_rand( 0 , 20 ) , 3 ) .
            substr(  time(), 0, 3);// Random string.
    set_config('tokencode', $tokencode, 'local_getkey');
}
echo $OUTPUT->footer();