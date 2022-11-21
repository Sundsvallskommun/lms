<?php

defined('MOODLE_INTERNAL') || die();


$tasks = [

    [
        'classname' => 'local_authupdateevent\task\updateloginmethod',
        'blocking' => 0,
        'minute' => '1',
        'hour' => '10',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
];
