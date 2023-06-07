<?php
// This file is part of Moodle - http://moodle.org/
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

namespace local_badgeevent\task;

use coding_exception;
use context_course;
use curl;



defined('MOODLE_INTERNAL') || die();

/**
 * A scheduled task to update waitlist enrolments.
 *
 * @package   enrol_waitlistext
 * @author    Andreas Nehl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class badgereminder_enrolments extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string('task:badgereminder_enrolments', 'local_badgeevent');
    }

    /**
     * Execute the task.
     *
     * @return bool true if everything is fine
     */
    public function execute() {

        $plugin = enrol_get_plugin('local_badgeevent');

        global $CFG, $DB;
        require_once($CFG->libdir.'/filelib.php');
        // Hämtar alla Märken som går ut om exakt 1 månad.
        $badge_issused = $DB->get_records_sql('SELECT badgeid, userid FROM {badge_issued} WHERE FROM_UNIXTIME(dateexpire, "%Y-%m-%d")=FROM_UNIXTIME(UNIX_TIMESTAMP(),"%y-%m-%d")+ INTERVAL 1 MONTH');

        // Vem ska skicka mailet
        $contact = get_admin();
        $subject = get_string('badgeremindersubjectemail', 'local_badgeevent');
        $msgwithuser=get_string('badgeremindermessagetouser_start', 'local_badgeevent');

        foreach ($badge_issused as $badge){

            $badge_name = $DB->get_record_sql('SELECT name FROM {badge} WHERE  id = ?', [$badge->badgeid]);
            $user = \core_user::get_user($badge->userid);
            $msgwithuser .= $badge_name->name;
            $msgwithuser .=get_string('badgeremindermessagetouser_end', 'local_badgeevent');
            email_to_user($user, $contact, $subject, ' ', $msgwithuser);

        }



        if ($plugin === null){
            mtrace("plugin not active returning");
            return true;
        }

        $plugin->cron();

        return true;
    }




}