<?php  // Moodle configuration file

// unset($CFG);
// global $CFG;
// $CFG = new stdClass();

// $CFG->dbtype    = 'mariadb';
// $CFG->dblibrary = 'native';
// $CFG->dbhost    = '10.148.216.166';
// $CFG->dbname    = 'moodle';
// $CFG->dbuser    = 'root';
// $CFG->dbpass    = 'Gcmooc@401';
// $CFG->prefix    = 'mdl_';
// $CFG->dboptions = array (
  // 'dbpersist' => 0,
  // 'dbport' => 3306,
  // 'dbsocket' => '',
// );

// $CFG->wwwroot   = 'http://10.148.216.165/moodle';
// $CFG->dataroot  = '/var/moodledata';
// $CFG->admin     = 'admin';

// $CFG->directorypermissions = 0777;

// require_once(dirname(__FILE__) . '/lib/setup.php');



unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'root';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3306,
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://localhost/moodle';
$CFG->dataroot  = 'D:\\\\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
$MYSERVER=1;
require_once(dirname(__FILE__) . '/lib/setup.php');



// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
