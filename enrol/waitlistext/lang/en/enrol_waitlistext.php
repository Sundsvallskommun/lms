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
$string['sendcoursewelcomemessageaddtime'] = 'Show time in welcome email';
$string['sendcoursewelcomemessageaddtime_help'] = 'Shows time when a course starts and not just a date in the welcome email';
$string['pluginname_desc'] = 'Extends waitlist plugin with more functionality';
$string['pluginname'] = 'Waitlistext';
$string['messageenrolledends'] = 'Hello {$a->user}, you have been accepted to <a href="{$a->courseurl}">{$a->coursename}</a>, Starting {$a->startdate}';
$string['messagetelenrolledends'] = 'Hello {$a->user}, you have been accepted to {$a->coursename}, Starting {$a->startdate} {$a->courseurl}';
$string['subjectenrolledends'] = 'You have been accepted to ';
$string['task:emailenrolmentends_enrolments'] = 'Update waitlist-enrolments';
$string['task:update_enrolments'] = 'Update waitlist-enrolments';
$string['stepbystep_req'] = 'Require badge to enrol to this course';
$string['stepbystep_badge'] = 'Required badge';
$string['toenrol_pre'] = 'You need badge:';
$string['toenrol_aft'] = 'to join this course';
$string['toenrol_department_pre'] = 'You dont work on this department:';
$string['toenrol_role_pre'] = 'Your have not role:';
$string['stepbystep_use'] = 'Enable step by step as a setting for waitlist enrolment';
$string['stepbystep_use_help'] = 'Enabling this will show the option to have a badge required to join courses';
$string['stepbystep'] = 'Default value for step by step';
$string['stepbystep_help'] = 'Sets the default value this will have inside the waitlist plugin';
$string['department'] = 'Default value for department';
$string['department_help'] = 'Defualt on  department on/off waitlist plugin';
$string['department_req'] = 'Required department on course';
$string['label_department'] = 'Required department';
$string['role'] = 'Default value for role';
$string['role_help'] = 'Sets the default value this will have inside the waitlist plugin';
$string['role_req'] = 'Role required for this course';
$string['label_role'] = 'Required Role';
$string['label_department_chosen'] = 'The required Department';
