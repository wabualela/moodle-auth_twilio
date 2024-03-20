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

require_once ('vendor/autoload.php');

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

    /**
     * twilio otp verification
     * @param string $tel
     * @return \Twilio\Rest\Verify\V2\Service\VerificationInstance | \Exception
     */
    public function verifications($tel) {
        global $SESSION;
        try {
            return $this->twilio
                ->verify
                ->v2
                ->services($this->service)
                ->verifications
                ->create($tel, "whatsapp", [ 'locale' => 'ar' ]);
        } catch (\Exception $exception) {
            $SESSION->phone_error_msg = get_string('invalidnumber', 'auth_twilio');
            redirect(new moodle_url('/auth/twilio/login.php'));
        }
    }

    public function verificationChecks($code, $phone) {
        try {
            return $this->twilio
                ->verify
                ->v2
                ->services($this->service)
                ->verificationChecks
                ->create([ 'code' => $code, 'to' => $phone ]);
        } catch (\Exception $exception) {
            redirect(new moodle_url('/auth/twilio/otp.php', [ 'phone' => $phone, 'error' => get_string('invalidverificationcode', 'auth_twilio') ]));
        }
    }

    public function complete_login($data) {
        global $DB;

        $user = new stdClass();
        if (!$user = self::user_exists($data['username'])) {
            $user = self::create_new_confirmed_account($data);
        }

        complete_user_login($user, []);
        redirect(new moodle_url('/'));
    }

    /**
     * Create an account with a linked login that is already confirmed.
     *
     * @param array $userinfo as returned from an oauth client.
     * @return bool
     */
    public static function create_new_confirmed_account($userinfo) {
        global $CFG, $DB;
        require_once ($CFG->dirroot . '/user/profile/lib.php');
        require_once ($CFG->dirroot . '/user/lib.php');

        $user               = new stdClass();
        $user->auth         = 'twilio';
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->secret       = random_string(15);
        $user->password     = '';
        $user->confirmed    = 1;  // Set the user to confirmed.
        $user->firstaccess  = time();
        $user->lastlogin    = time();
        $user->currentlogin = time();
        $user->lastip       = getremoteaddr();
        $user->policyagreed = 0;

        $userinfo['username'] = ltrim($userinfo['username'], '+');
        $user                 = self::save_user($userinfo, $user);

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
            if (isset ($userinfo[ $field ]) && $userinfo[ $field ]) {
                $user->$field = $userinfo[ $field ];

                // Check whether the profile fields exist or not.
                $hasprofilefield = $hasprofilefield || strpos($field, \core_user\fields::PROFILE_FIELD_PREFIX) === 0;
            }
        }

        // Create a new user.
        try {

            $user->id = user_create_user($user, false, true);
        } catch (\Exception $exception) {
            redirect(new moodle_url('/auth/twilio/signup.php', [ 'phone' => $userinfo['phone'] ]), $exception->getMessage());
        }

        profile_load_custom_fields($user);

        $fields['certificatename'] = $userinfo['customfields']['certificatename'];
        $fields['age']             = $userinfo['customfields']['age'];

        profile_save_custom_fields($user->id, $fields);

        return $user;
    }

    public static function user_exists($username) {
        global $DB;
        $username = ltrim($username, '+');
        $sql      = "SELECT * FROM {user} WHERE username LIKE '%$username%'";

        return $DB->get_record_sql($sql);
    }

    /**
     * Remove the error code form the exception message
     * @param mixed $message
     * @return array|string|null
     */
    public static function removeErrorCode($message) {
        $pattern = "/\[\w+\]\s?/";

        $cleanedMessage = preg_replace($pattern, '', $message);

        return $cleanedMessage;
    }
}
