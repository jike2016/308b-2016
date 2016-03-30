<?php
// This file is part of Moodle - http://vidyamantra.org/
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
 * This plugin sends users a welcome message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local
 * @subpackage getkey
 * @author     Pinky Sharma
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Get Key';
$string['domain'] = 'Domain url';
$string['missingdomain'] = 'Domain name missing';
$string['message'] = 'To get API Key for whiteboard/chat you need to submit this form with correct detail. You will get key only for specified Domain.';
$string['keyis'] = 'Your API key is ';
$string['keynotsaved'] = 'API key not saved ';

// Stat lang
$string['graphheading'] = 'Usage Graph of vidya.io API ';
$string['currentmonth'] = 'Current Month ';
$string['previousmonth'] = 'Previous Month ';
$string['year'] = '1 Year ';
$string['today'] = 'Today ';
$string['msggraph'] = 'Message Graph';
$string['usrgraph'] = 'Users Graph';
$string['nodata'] = 'No data available for {$a} graph';
$string['apikey'] = 'Vidya.io API key';
$string['missingkey'] = 'API key missing';
$string['confirmkeydeletion'] = 'Confirm API key deletion';
$string['confirmdelete'] = 'Confirm delete';
$string['havekey'] = "Already have Vidya.io API key? <a href='savekey.php'>click here</a>";
