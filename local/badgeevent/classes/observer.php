<?php

defined('MOODLE_INTERNAL') || die();



class local_badgeevent_observer
{

    public static function badge_awarded(\core\event\badge_awarded $event)
    {
        global $DB, $CFG;

        $event_data = $event->get_data();

        $badgeid = $event_data['objectid'];
        $user_issue = $event_data['relateduserid'];

        $user = \core_user::get_user($user_issue);

        /*
        mdl_enrol customint5 0/1 om det finns ett steg två = 1
        customint6 id för badgen
        */
        $ifsteptwo = $DB->get_records_sql('SELECT * FROM {enrol} WHERE customint5 = ? AND customint6 = ?', [1,$badgeid]);
        $badgename = $DB->get_record_sql('SELECT name FROM {badge} WHERE id = ?', [$badgeid]);

       // $DB->update_record()
        if($ifsteptwo){

            $contact='';
            $subject = get_string('reminder','local_badgeevent');
              //  'Påminelse om nästa kurs';
            $message = get_string('message','local_badgeevent') . " " . $badgename->name;
                //'Det finns ett till steg på kursen '. $badgename->name;

            email_to_user($user, $contact, $subject, '', $message);

        }

    }


}


