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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

/**
 * Authentication plugin auth_twilio
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_twilio extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'twilio';
        $this->config   = get_config('twilio');
    }

    /**
     * Return a list of identity providers to display on the login page.
     *
     * @param string|
     *  $wantsurl The requested URL.
     * @return array List of arrays with keys url, iconurl and name.
     */
    public function loginpage_idp_list($wantsurl) {
        $result   = [];
        $result[] = [
            'url'     => new moodle_url('/auth/twilio/login.php', [ 'wantsurl' => $wantsurl, 'sesskey' => sesskey()]),
            'iconurl' => 'https://th.bing.com/th/id/OIP.Nf-m41NGgoClnltGcriroAHaHl?rs=1&pid=ImgDetMain',
            'name'    => 'WhatsApp',
        ];
        return $result;

    }

    /**
     * Complete the login process after tel verifications is complete.
     * @param string $tel
     * @return void Either redirects or throws an exception
     */
    public function complete_login($tel): void {
        global $CFG, $SESSION, $PAGE;
    }
}
