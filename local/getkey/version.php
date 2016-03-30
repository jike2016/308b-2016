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
 * This plugin gets API key from vidya.io
 * and use it in other plugin (vmchat,virtualclass)
 *
 * @package    local
 * @subpackage getkey
 * @author     Pinky Sharma 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
$plugin->component = 'local_getkey'; 
$plugin->version  = 2015121100;
$plugin->requires = 2012120300;
$plugin->release = '1.3 (Build: 2015121100)';
$plugin->maturity = MATURITY_STABLE;