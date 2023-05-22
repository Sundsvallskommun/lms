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
$string['sendcoursewelcomemessageaddtime'] = 'Visa tid i email';
$string['sendcoursewelcomemessageaddtime_help'] = 'Visa tid när en kurs startar och inte bara datum i välkomstmail';
$string['pluginname_desc'] = 'Utöka waitlist plugin med mer fuktionalitet';
$string['pluginname'] = 'Waitlistext';
$string['messageenrolledends'] = 'Hej {$a->user}, du har blivit antagen till  <a href="{$a->courseurl}">{$a->coursename}</a>, med start {$a->startdate}';
$string['messagteleenrolledends'] = 'Hej {$a->user}, du har blivit accepterad till {$a->coursename}, med start {$a->startdate} {$a->courseurl}';
$string['subjectenrolledends'] = 'Du har blivit antagen till ';
$string['task:updateautmethod_enrolments'] = 'Uppdatera waitlist-enrolments';
$string['task:update_enrolments'] = 'Uppdatera waitlist-enrolments';
$string['stepbystep_req'] = 'Märkes krav för denna kurs ';
$string['stepbystep_badge'] = 'Märkes krav';
$string['toenrol_pre'] = 'Du behöver märke:';
$string['toenrol_aft'] = 'för att anmäla dig till kursen';
$string['toenrol_department_pre'] = 'Du tillhör inte avdelningen:';
$string['toenrol_role_pre'] = 'Du har inte rollen:';
$string['toenrol_section_pre'] = 'Du tillhör inte avdelningen:';
$string['stepbystep_use'] = 'Aktivera steg för steg inställningar för waitlist enrolment';
$string['stepbystep_use_help'] = 'Enabling this will show the option to have a badge required to join courses';
$string['stepbystep'] = 'Default värde för steg för steg';
$string['stepbystep_help'] = 'Sätt default värdet inuti waitlistext plugin';
$string['department'] = 'Default värde för förvaltning';
$string['department_help'] = 'Sätt default värde inuti waitlistext plugin';
$string['department_req'] = 'Förvaltning man jobbar på för att anmäla sig till kursen';
$string['label_department'] = 'Förvaltning krav';
$string['section'] = 'Defualt värdet för avdelningen';
$string['section_help'] = 'Sätt defualt värdet för avdelningen on/off waitlist plugin';
$string['section_req'] = 'avdelningen krav för kurs';
$string['label_section'] = 'Krav på avdelningen';
$string['role'] = 'Default värde för roll';
$string['role_help'] = 'Sätt default värde inuti waitlistext plugin';
$string['role_req'] = 'Roll krav för att anmäla sig på kurs';
$string['label_role'] = 'Roll krav';
$string['label_department_chosen'] = 'De valda förvaltning';
