<?php

/**
 * För utvecklingssamtal mellan chef och medarbetare
 * Form for editing utvsamtal block instances.
 *
 * @package    block_utvsamtal
 * @author     Charlotte Englander
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_adminsettingblock_edit_form extends block_edit_form
{

    protected function specific_definition($mform)
    {
        $options = array(
            'show' => get_string('show'),
            'hide' => get_string('hide'),
        );


        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block_adminsettingblock'));

        $mform->addElement('select', 'config_hiddencourses', get_string('currentsetting', 'block_adminsettingblock'), $options);
        // This will select the colour blue.
        $mform->setDefault('config_hiddencourses', 'show');
        // A sample string variable with a default value.
        //$mform->addElement('text', 'config_text', get_string('blockstring', 'block_adminsettingblock'));
       // $mform->setDefault('config_text', get_string('defaulttext', 'block_adminsettingblock'));
        //$mform->setType('config_text', PARAM_RAW);
    }
}
