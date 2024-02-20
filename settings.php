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

defined('MOODLE_INTERNAL') || die;

/**
 * Twilio authentication plugin settings
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $settings->add(
        new admin_setting_heading(
            'auth_twilio/pluginname',
            '',
            new lang_string('auth_emaildescription', 'auth_twilio'),
        ),
    );

    $settings->add(
        new admin_setting_configtext(
            'auth_twilio/accountsid',
            new lang_string('auth_accountsid', 'auth_twilio'),
            new lang_string('auth_accountsiddescription', 'auth_twilio'),
            '',
        ),
    );

    $settings->add(
        new admin_setting_configpasswordunmask(
            'auth_twilio/token',
            new lang_string('auth_token', 'auth_twilio'),
            new lang_string('auth_tokendescription', 'auth_twilio'),
            '',
        ),
    );

    $settings->add(
        new admin_setting_configtext(
            'auth_twilio/servicesid',
            new lang_string('auth_servicesid', 'auth_twilio'),
            new lang_string('auth_servicesiddescription', 'auth_twilio'),
            '',
        ),
    );

}
