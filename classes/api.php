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

namespace auth_twilio;

use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once('vendor/autoload.php');

use Twilio\Rest\Client;

/**
 * Class api
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {
    /**
     * Service Token
     * @var string
     */
    public string $service;
    /**
     * Plugin configuration
     * @var Client Twilio API Client
     */
    public Client $twilio;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->service = get_config('auth_twilio', 'servicesid');
        $this->twilio  = new Client(get_config('auth_twilio', 'accountsid'), get_config('auth_twilio', 'token'));
    }

    /**
     * Is the plugin enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return is_enabled_auth('twilio');
    }

    public function verifications($tel) {
        return $this->twilio
            ->verify
            ->v2
            ->services($this->service)
            ->verifications
            ->create($tel, "whatsapp");
    }

    public function verificationChecks($code, $tel) {
        return $this->twilio
            ->verify
            ->v2
            ->services($this->service)
            ->verificationChecks
            ->create([ 'code' => $code, 'to' => $tel ]);
    }

    public function complete_login($data) {
        global $DB;
        $user = new stdClass();

        die(var_dump($data));
        if (self::user_exists_phone($data['tel'])) {
            $user = $DB->get_record('user', [ 'username' => $data['tel'] ]);
        } else {
            $user = self::create_new_confirmed_account($data);
        }
        complete_user_login($user, []);
        redirect(new moodle_url('/'));
    }

    /**
     * check is user exist by phone
     * @param string $phone
     * @return bool
     */
    public static function user_exists_phone($phone) {
        global $DB;
        $exists = false;
        if ($DB->record_exists_sql("SELECT id FROM {user} WHERE username LIKE '%$phone%' ")) {
            $exists = true;
        }

        if ($DB->record_exists_sql("SELECT id FROM {user} WHERE phone1 LIKE '%$phone%' ")) {
            $exists = true;
        }

        return $exists;
    }

    public function get_countries_choices() {
        $countries = get_string_manager()->get_list_of_countries();
        $choices   = [];
        foreach ($countries as $key => $value) {
            $choices[] = $value;
        }
        return $choices;
    }

    /**
     * Create an account with a linked login that is already confirmed.
     *
     * @param array $userinfo as returned from an oauth client.
     * @return bool
     */
    public static function create_new_confirmed_account($userinfo) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        $user             = new stdClass();
        $user->auth       = 'twilio';
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->secret     = random_string(15);
        $user->password   = '';
        $user->confirmed  = 1;  // Set the user to confirmed.

        $user = self::save_user($userinfo, $user);

        return $user;
    }

    /**
     * Create a new user & update the profile fields
     *
     * @param array $userinfo
     * @param object $user
     * @return object
     */
    private static function save_user(array $userinfo, object $user): object {
        // Map supplied issuer user info to Moodle user fields.
        $userfieldmapping = new \core\oauth2\user_field_mapping();
        $userfieldlist    = $userfieldmapping->get_internalfields();
        $hasprofilefield  = false;
        foreach ($userfieldlist as $field) {
            if (isset($userinfo[ $field ]) && $userinfo[ $field ]) {
                $user->$field = $userinfo[ $field ];

                // Check whether the profile fields exist or not.
                $hasprofilefield = $hasprofilefield || strpos($field, \core_user\fields::PROFILE_FIELD_PREFIX) === 0;
            }
        }

        // Create a new user.
        $user->id = user_create_user($user, false, true);

        // If profile fields exist then save custom profile fields data.
        if ($hasprofilefield) {
            profile_save_data($user);
        }

        return $user;
    }
}
