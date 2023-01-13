<?php

defined('MOODLE_INTERNAL') || die();


$tasks = [
    [
        'classname' => 'enrol_waitlistext\task\emailenrolmentends_enrolments',
        'blocking' => 0,
        'minute' => '1',
        'hour' => '9',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    [
        'classname' => 'enrol_waitlistext\task\update_enrolmentsext',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '*/3',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
