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

namespace enrol_waitlistext\task;

use coding_exception;
use context_course;

defined('MOODLE_INTERNAL') || die();

/**
 * A scheduled task to update waitlist enrolments.
 *
 * @package   enrol_waitlistext
 * @author    Andreas Nehl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emailenrolmentends_enrolments extends \core\task\scheduled_task {

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     * @throws coding_exception
     */
    public function get_name() {
        return get_string('task:emailenrolmentends_enrolments', 'enrol_waitlistext');
    }

    /**
     * Execute the task.
     *
     * @return bool true if everything is fine
     */
    public function execute() {

        $plugin = enrol_get_plugin('enrol_waitlistext');

        global $CFG, $DB;
        // Hämta cursid från alla cursers vars väntelista slutar idag
        $curseidenddatewaitlis = $DB->get_records_sql('SELECT courseid FROM {enrol} WHERE FROM_UNIXTIME(UNIX_TIMESTAMP(), "%Y%d%m")=FROM_UNIXTIME(enrolenddate, "%Y%d%m") AND enrol = "waitlistext"');

        // Vem ska skicka mailet
        $contact = get_admin();
        $urltoken="https://api-test.sundsvall.se/token";
//putenv("CLIENTID=2RazzFEBMFofJCZE5ku5zGscOswa");	
	
	//$data=json_encode($data);
$client_id = getenv("CLIENTID");
$client_secret = getenv("CLIENTSECRET");


        $ch = curl_init();
        curl_setopt($ch, constant("CURLOPT_" . 'URL'), $urltoken);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "client_id=". $client_id . "&client_secret=" . $client_secret . "&code=CODE&grant_type=client_credentials");

        $result = curl_exec($ch);
        curl_close($ch);

        $jsonrespons=json_decode($result);
        $token = $jsonrespons->access_token;


// Gå igenom Cursid
        foreach ($curseidenddatewaitlis as $courseid) {

            // Hämta context från cursen utifrån kursid
            // Hämta alla registrerade medarbetare för kursen.
            $coursecontext = context_course::instance($courseid->courseid);
            $enrolledusers = get_enrolled_users($coursecontext);

            // Hämta Kursen från DB.
            $course = get_course($courseid->courseid);
            $courseurl = "$CFG->wwwroot/course/view.php?id=$course->id";
            // skriver ämnesrad och meddelande
            $subject = get_string('subjectenrolledends', 'enrol_waitlistext') . ' ' . $course->fullname;
            $message = get_string('messageenrolledends', 'enrol_waitlistext');
            $message = str_replace('{$a->coursename}', $course->fullname, $message);
            $message = str_replace('{$a->courseurl}', $courseurl, $message);
            $message = str_replace('{$a->startdate}', userdate($course->startdate), $message);
            
            $messagetel = get_string('messagetelenrolledends', 'enrol_waitlistext');
            $messagetel = str_replace('{$a->coursename}', $course->fullname, $messagetel);
            $messagetel = str_replace('{$a->courseurl}', $courseurl, $messagetel);
            $messagetel = str_replace('{$a->startdate}', userdate($course->startdate), $messagetel);  
            // Loppa alla meddarbetare på vardera kurs
            foreach ($enrolledusers as $enrolleduser){
                
                $msgwithuser = str_replace('{$a->user}', $enrolleduser->firstname, $message);
                $msgtelwithuser = str_replace('{$a->user}', $enrolleduser->firstname, $messagetel);
                // directly emailing welcome message rather than using messaging
                email_to_user($enrolleduser, $contact, $subject,' ' ,$msgwithuser);

                $sql = 'SELECT fieldid FROM mdl_user_info_data WHERE userid=' . $enrolleduser->id . ' AND fieldid=2 AND data=1';
		if($DB->record_exists_sql($sql)){
                    $url = 'https://api-i-test.sundsvall.se/messaging/2.3/sms';

                    $med=$msgtelwithuser;
                    $tel=$enrolleduser->phone2;
                    $ch = curl_init();

                    $authorization = "Authorization: Bearer ".$token;
                    $data = array(
                        "sender"=> "Sundsvall",
                        "mobileNumber" => $tel,
                        "message" => $med,
                    );


                    $data=json_encode($data);
		    
		    curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json','Content-Type: application/json'
, $authorization ));
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                    
		    $result = curl_exec($ch);
                    curl_close($ch);
		}


	    }

        }

        if ($plugin === null){
            mtrace("plugin not active returning");
            return true;
        }

        $plugin->cron();

        return true;
    }
    
}
