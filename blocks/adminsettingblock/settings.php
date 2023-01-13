<?php
/**
 * För utvecklingssamtal mellan chef och medarbetare
 * Global configuration för admins
 *
 * @package    block_utvsamtal
 * @author     Charlotte Englander
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$settings->add(new admin_setting_heading(
            'headerconfig',
            get_string('headerconfig', 'block_adminsettingblock'),
            get_string('descconfig', 'block_adminsettingblock')
        ));

$settings->add(new admin_setting_configcheckbox(
            'simplehtml/Allow_HTML',
            get_string('labelallowhtml', 'block_adminsettingblock'),
            get_string('descallowhtml', 'block_adminsettingblock'),
            '0'
        ));