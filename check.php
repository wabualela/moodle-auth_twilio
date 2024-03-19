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

/**
 * TODO describe file code_check
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require ('../../config.php');

$phone = required_param('phone', PARAM_RAW);
$code  = required_param('code', PARAM_RAW);

$url = new moodle_url('/auth/twilio/check.php');

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

if ($code && $phone) {

    $twilio = new \auth_twilio\api();

    if (!$twilio->is_enabled())
        throw new \moodle_exception('notenabled', 'auth_twilio');

    $verification_check = $twilio->verificationChecks($code, $phone);

    if ($verification_check->status == 'approved') {

        \auth_twilio\api::user_exists($phone)
            ? $twilio->complete_login([ 'username' => $phone ])
            : redirect(new moodle_url('/auth/twilio/signup.php', [ 'phone' => $phone ]));

    } else {
        $SESSION->code_error_msg = get_string('invalidverificationcode', 'auth_twilio');
        redirect(new moodle_url('/auth/twilio/otp.php', [ 'phone' => $phone ]));
    }
} else {
    redirect(new moodle_url('/auth/twilio/login.php'), get_string('accountincomplete', 'auth_twilio'));
}