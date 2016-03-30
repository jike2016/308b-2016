<?php
//require_once($CFG->wwwroot.'/config.php');
require_once('../config.php');

global $CFG;
global $DB;

$picID = $_GET['picID'];

$newid = $DB->update_record('index_picture', array('id'=>$picID, 'Pictureurl'=>null, 'Picturelink'=>null, 'Picturecolor'=>null));
echo '1';

