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

require_once($CFG->dirroot.'/lib/formslib.php');

class local_getkey_key_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
        $mform->addElement('text', 'firstname', get_string('firstname', 'moodle'), 'size="35"');
        $mform->addRule('firstname', null, 'required', null, 'client');
        $mform->setType('firstname', PARAM_TEXT);
        $mform->setDefault('firstname', $this->_customdata['firstname']);
        $mform->addElement('text', 'lastname', get_string('lastname', 'moodle'), 'size="35"');
        $mform->addRule('lastname', null, 'required', null, 'client');
        $mform->setType('lastname', PARAM_TEXT);
        $mform->setDefault('lastname', $this->_customdata['lastname']);

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25" ');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'server');
        // Set default value by using a passed parameter.
        $mform->setDefault('email', $this->_customdata['email']);

        $mform->addElement('text', 'domain', get_string('domain', 'local_getkey'), 'maxlength="100" size="25" ');
        $mform->setType('domain', PARAM_NOTAGS);
        $mform->addRule('domain', get_string('missingdomain', 'local_getkey'), 'required', null, 'server');
        // Set default value by using a passed parameter.
        $mform->setDefault('domain', $this->_customdata['domain']);

        $this->add_action_buttons($cancel = false);
    }
}


class local_getkey_savekey_form extends moodleform {

    function definition() {
        $kform =& $this->_form;
        $kform->addElement('text', 'key', get_string('apikey', 'local_getkey'), 'maxlength="100" size="25" ');
        $kform->setType('key', PARAM_NOTAGS);
        $kform->addRule('key', get_string('missingkey', 'local_getkey'), 'required', null, 'server');
        // Set default value by using a passed parameter.
        //$mform->setDefault('domain', $this->_customdata['domain']);

        $this->add_action_buttons($cancel = true);
    }
}