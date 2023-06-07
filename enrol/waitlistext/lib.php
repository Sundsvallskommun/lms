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
/**
 * *************************************************************************
 * *                  Waitlist Enrol                                      **
 * *************************************************************************
 * @copyright   emeneo.com                                                **
 * @link        emeneo.com                                                **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************
 */

require_once($CFG->dirroot . '/enrol/waitlist/lib.php');
//require_once($CFG->dirroot .'/group/lib.php');
//require_once($CFG->dirroot. '/enrol/waitlist/waitlist.php');
//require_once($CFG->dirroot . '/enrol/renderer.php');

defined('MOODLE_INTERNAL') || die();

class enrol_waitlistext_plugin extends enrol_waitlist_plugin
{

    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol($instance) && has_capability("enrol/self:unenrol", $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url, array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        if ($this->allow_manage($instance) && has_capability("enrol/self:manage", $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url, array('class' => 'editenrollink', 'rel' => $ue->id));
        }
        return $actions;
    }

    public function allow_unenrol(stdClass $instance) {
        // users with unenrol cap may unenrol other users manually manually
        return true;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course.
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {
        // $context = get_context_instance(CONTEXT_COURSE, $courseid, MUST_EXIST);
        $context = context_course::instance($courseid);

        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/waitlist:config', $context)) {
            return null;
        }
        // multiple instances supported - different roles with different password
        return new moodle_url('/enrol/waitlistext/edit.php', array('courseid' => $courseid));
    }

    /**
     * Returns edit icons for the page with list of instances
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'waitlistext') {
            throw new coding_exception('invalid enrol instance!');
        }
        // $context = get_context_instance(CONTEXT_COURSE, $instance->courseid);
        $context = context_course::instance($instance->courseid);

        $icons = array();
        
        if (has_capability('enrol/waitlist:config', $context)) {
            $managelink = new moodle_url("/enrol/waitlistext/enroluser.php", array('enrolid' => $instance->id));
               $icons[] = $OUTPUT->action_icon($managelink, new pix_icon('t/enrolusers', get_string('enrolusers', 'enrol_waitlist'), 'core', array('class' => 'iconsmall')));
        }

        if (has_capability('enrol/waitlist:config', $context)) {
            $editlink = new moodle_url("/enrol/waitlistext/edit.php", array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core', array('class' => 'iconsmall')));
        }

        if (has_capability('enrol/waitlist:config', $context)) {
            $editlink = new moodle_url("/enrol/waitlistext/users.php", array('id' => $instance->courseid));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('i/switchrole', get_string('waitlisted_users','enrol_waitlist'), 'core', array('class' => 'iconsmall')));
        }

        return $icons;
    }



    public function add_course_navigation($instancesnode, stdClass $instance) {
        if ($instance->enrol !== 'waitlistext') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/waitlist:config', $context)) {
            $managelink = new moodle_url('/enrol/waitlistext/edit.php', array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }


     /**
     * Add new instance of enrol plugin with default settings.
     * @param object $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {
        $fields = array('customint1'  => $this->get_config('groupkey'),
                        'customint2'  => $this->get_config('longtimenosee'),
                        'customint3'  => $this->get_config('maxenrolled'),
                        'customint4'  => $this->get_config('sendcoursewelcomemessage'),
                        'customchar1'  => $this->get_config('faculty'),
                        'enrolperiod' => $this->get_config('enrolperiod', 0),
                        'status'      => $this->get_config('status'),
                        'roleid'      => $this->get_config('roleid', 0));

        if ($this->get_config('requirepassword')) {
            $fields['password'] = generate_password(20);
        }
        
        return $this->add_instance($course, $fields);
    }

     /**
     * Returns localised name of enrol instance
     *
     * @param object $instance (null is accepted too)
     * @return string
     */
    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id' => $instance->roleid))) {
                // $role = ' (' . role_get_name($role, get_context_instance(CONTEXT_COURSE, $instance->courseid)) . ')';
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    /**
     * Creates course enrol form, checks if form submitted
     * and enrols user if necessary. It can also redirect.
     *
     * @param stdClass $instance
     * @return string html text, usually a form in a text box
     */
    public function enrol_page_hook(stdClass $instance) {
        global $CFG, $OUTPUT, $SESSION, $USER, $DB;

        require_once($CFG->dirroot . '/badges/classes/badge.php');


        if (isguestuser()) {
            // can not enrol guest!!
            return null;
        }
        if ($DB->record_exists('user_enrolments', array('userid' => $USER->id, 'enrolid' => $instance->id))) {
            // TODO: maybe we should tell them they are already enrolled, but can not access the course
            return null;

        }

        if($DB->record_exists('user_enrol_waitlist', array('userid' => $USER->id, 'instanceid' => $instance->id))){
               return $OUTPUT->notification(get_string('waitlistinfo', 'enrol_waitlist'));
        }

        if ($instance->enrolstartdate != 0 and $instance->enrolstartdate > time()) {
            // TODO: inform that we can not enrol yet
            return null;
        }

// kollar om inloggad användare tillhör förvaltning
        if ($instance->customint7 == 1) {

            $departments=explode(",",$instance->customtext2);
            $departmetmatch=false;
            $departmentoutput='';

	    foreach ($departments as $department){
                $sql = 'SELECT * FROM mdl_user where SUBSTRING_INDEX( department, " ", 1 ) ="'. $department .'" AND id=' . $USER->id;
                if($DB->record_exists_sql($sql)){
                    $departmetmatch=true;

                    }
                else{
                    $departmentoutput .= ' ' . $department;
                }
            }
            if(!$departmetmatch){
                return $OUTPUT->notification(get_string('toenrol_department_pre', 'enrol_waitlistext') . ' ' . $departmentoutput);
            }

        }

 if ($instance->customint8 == 1) {
            $sections=explode(",",$instance->customtext3);

            $sectionmatch=false;
            $sectionoutput='';

            foreach ($sections as $section){
                $sql = 'SELECT * FROM mdl_user where SUBSTRING(SUBSTRING( department, LOCATE(" ", department)),2) ="'. $section .'" AND id=' . $USER->id;
                if($DB->record_exists_sql($sql)){
                    $sectionmatch=true;

                    }
                else{
                    $sectionoutput .= ' ' . $section;
                }
            }
            if(!$sectionmatch){
                return $OUTPUT->notification(get_string('toenrol_section_pre', 'enrol_waitlistext') . ' ' . $sectionoutput);
            }

        }
/* När roll implemeteras
	// kollar om inloggad användare hhar roll
        if ($instance->customint8 == 1) {
            $sql = 'SELECT * FROM mdl_user where institution ="'.$instance->customtext3.'" AND id=' . $USER->id;
            if(!$DB->record_exists_sql($sql)){
                return $OUTPUT->notification(get_string('toenrol_role_pre', 'enrol_waitlistext') . ' ' .$instance->customtext3);
            }
        }
 */


	 // kollar om inloggad användare har tilldelat märket
        if ($instance->customint5 == 1) {
            
            if(!$DB->record_exists('badge_issued', array('userid' => $USER->id, 'badgeid' => $instance->customint6))){
                $badge = $DB->get_record_sql("select * from mdl_badge where id=" . $instance->customint6);

                $badges = badges_get_badges(BADGE_TYPE_SITE);

                $badgeObj = array_column($badges, null, 'id')[$badge->id] ?? false;

                $badge_context = $badgeObj->get_context();

                //print_badge_image($badgeObj, $badge_context, 'large');  //  size parameter could be 'small' or 'large'

                return $OUTPUT->notification(get_string('toenrol_pre', 'enrol_waitlistext'). '   ' . print_badge_image($badgeObj, $badge_context, 'small') . '   ' . $badge->name . ',   ' . get_string('toenrol_aft', 'enrol_waitlistext'));
            }
            
            
	}


        

        /*
        if ($instance->enrolenddate != 0 and $instance->enrolenddate < time()) {
            //TODO: inform that enrolment is not possible any more
            return null;
        }

        if ($instance->customint3 > 0) {
            // max enrol limit specified
            $count = $DB->count_records('user_enrolments', array('enrolid'=>$instance->id));
            if ($count >= $instance->customint3) {
                // bad luck, no more waitlist enrolments here
                return $OUTPUT->notification(get_string('maxenrolledreached', 'enrol_waitlist'));
            }
        }
        */
        require_once("$CFG->dirroot/enrol/waitlist/locallib.php");
        require_once("$CFG->dirroot/group/lib.php");
        require_once("$CFG->dirroot/enrol/waitlist/waitlist.php");

        $waitlist = new waitlist();

        /*
        if(!$waitlist->vaildate_wait_list($instance->id,$USER->id)){
        return $OUTPUT->notification(get_string('waitlistinfo', 'enrol_waitlist'));
        }
        */

        $form = new enrol_waitlist_enrol_form(null, $instance);
        $instanceid = optional_param('instance', 0, PARAM_INT);

        if ($instance->id == $instanceid) {
            if ($data = $form->get_data()) {
                $enrol = enrol_get_plugin('waitlist');
                $timestart = time();
                if ($instance->enrolperiod) {
                    $timeend = $timestart + $instance->enrolperiod;
                } else {
                    $timeend = 0;
                }

                // $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);
                $enroledCount = $DB->count_records('user_enrolments', array('enrolid' => $instance->id));

                $canEnrol = false;
                if($instance->customint3 == 0){
                    $canEnrol = true;
                }else if($enroledCount < $instance->customint3){
                    $canEnrol = true;
                    if($instance->enrolenddate){
                        if(time() > $instance->enrolenddate){
                            $canEnrol = false;
                        }
                    }
                }

                if($canEnrol){
                             $this->enrol_user($instance, $USER->id, $instance->roleid, $timestart, $timeend);
                    if ($instance->customint4) {
                        $user = $DB->get_record_sql("select * from ".$CFG->prefix."user where id=".$USER->id);
                        $this->email_welcome_message($instance, $USER);
                    }
                }else{
                             $waitlist->add_wait_list($instance->id, $USER->id, $instance->roleid, $timestart, $timeend);
                }
                // add_to_log($instance->courseid, 'course', 'enrol', '../enrol/users.php?id='.$instance->courseid, $instance->courseid); //there should be userid somewhere!

                if ($instance->password and $instance->customint1 and $data->enrolpassword !== $instance->password) {
                    // it must be a group enrolment, let's assign group too
                    $groups = $DB->get_records('groups', array('courseid' => $instance->courseid), 'id', 'id, enrolmentkey');
                    foreach ($groups as $group) {
                        if (empty($group->enrolmentkey)) {
                            continue;
                        }
                        if ($group->enrolmentkey === $data->enrolpassword) {
                            groups_add_member($group->id, $USER->id);
                            break;
                        }
                    }
                }
                // send welcome
                // if ($instance->customint4) {
                    // $this->email_welcome_message($instance, $USER);
                // }
                redirect("$CFG->wwwroot/course/view.php?id=$instance->courseid");
            }
        }

        ob_start();
        $form->display();
        $output = ob_get_clean();
        return $OUTPUT->box($output);
    }


    /**
     * Send welcome email to specified user
     *
     * @param object $instance
     * @param object $user user record
     * @return void
     */
    protected function email_welcome_message($instance, $user)
    {
        global $CFG, $DB;

        $course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname);
        $a->profileurl = "$CFG->wwwroot/course/view.php?id=$course->id";
        $a->summary = $course->summary;
        $a->startdate = '';
        if ($course->startdate != 0) {
            if (get_config('enrol_waitlistext', 'addtimetostartdate') == 1) {
                $a->startdate = userdate($course->startdate);
            } 
            else {
                $a->startdate = date('y-m-d', $course->startdate);
            }
        }

        if (trim($instance->customtext1) !== '') {
            $message = $instance->customtext1;
            $message = str_replace('{$a->coursename}', $a->coursename, $message);
            $message = str_replace('{$a->profileurl}', $a->profileurl, $message);
            $message = str_replace('{$a->summary}', $a->summary, $message);
            $message = str_replace('{$a->startdate}', $a->startdate, $message);
        } else {
            $message = get_string('welcometocoursetext', 'enrol_waitlist', $a);
        }

        $subject = get_string('welcometocourse', 'enrol_waitlist', format_string($course->fullname));

        // $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $context = context_course::instance($course->id);
        $rusers = array();
        if (!empty($CFG->coursecontact)) {
            $croles = explode(',', $CFG->coursecontact);
            $rusers = get_role_users($croles[0], $context, true, '', 'r.sortorder ASC, u.lastname ASC');
        }
        if ($rusers) {
            $contact = reset($rusers);
        } else {
            $contact = get_admin();
        }

       

        // directly emailing welcome message rather than using messaging
        email_to_user($user, $contact, $subject, '', $message);
    }
}
