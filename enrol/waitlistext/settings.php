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
defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // --- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_waitlistext_settings', '', get_string('pluginname_desc', 'enrol_waitlistext')));

    

    $settings->add(new admin_setting_configcheckbox(
        'enrol_waitlistext/addtimetostartdate',
        get_string('sendcoursewelcomemessageaddtime', 'enrol_waitlistext'),
        get_string('sendcoursewelcomemessageaddtime_help', 'enrol_waitlistext'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrol_waitlistext/usestepbystep',
        get_string('stepbystep_use', 'enrol_waitlistext'),
        get_string('stepbystep_use_help', 'enrol_waitlistext'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrol_waitlistext/stepbystep',
        get_string('stepbystep', 'enrol_waitlistext'),
        get_string('stepbystep_help', 'enrol_waitlistext'),
        0
    ));
    $settings->add(new admin_setting_configcheckbox(
        'enrol_waitlistext/department',
        get_string('department', 'enrol_waitlistext'),
        get_string('department_help', 'enrol_waitlistext'),
        0
    ));
  
}

$ADMIN->add('enrolments', new admin_externalpage('enrol_waitlistext', 'Waitlistext enrolment custom fields', $CFG->wwwroot . '/enrol/waitlistext/profile/index.php'));
