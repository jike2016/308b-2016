<?php



defined('MOODLE_INTERNAL') || die;

function xmldb_missionmy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    // Moodle v2.3.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

   /*  if ($oldversion < 2013021400) {
        // find all courses that contain labels and reset their cache
        $modid = $DB->get_field_sql("SELECT id FROM {modules} WHERE name=?",
                array('wepeng'));
        if ($modid) {
            $courses = $DB->get_fieldset_sql('SELECT DISTINCT course '.
                'FROM {course_modules} WHERE module=?', array($modid));
            foreach ($courses as $courseid) {
                rebuild_course_cache($courseid, true);
            }
        }

        // label savepoint reached
        upgrade_mod_savepoint(true, 2013021400, 'wepeng');
    } */

    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}


