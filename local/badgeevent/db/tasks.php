<?php

defined('MOODLE_INTERNAL') || die();


$tasks = [

    [
        'classname' => 'local_badgeevent\task\badgereminder_enrolments',
        'blocking' => 0,
        'minute' => '1',
        'hour' => '9',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
