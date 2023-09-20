<?php


defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => '\core\event\badge_awarded',
        'callback' => 'local_badgeevent_observer::badge_awarded',
    ),
);