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
 * *                  authupdateeven                                      **
 * *************************************************************************
 * @copyright   Andreas                                                   **
 * @link        Andreas                                                   **
 * *************************************************************************
 * ************************************************************************
 */
defined('MOODLE_INTERNAL') || die();

$plugin->version  = 2022110400;   // The current module version (Date: YYYYMMDDXX)
$plugin->requires = 2011033005;   // Requires at least this Moodle version

$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'authupdateevents';
$plugin->component = 'local_authupdateevent'; // Full name of the plugin (used for diagnostics)

