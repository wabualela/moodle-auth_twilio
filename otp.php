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
 * Twilio login
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require ('../../config.php');

$phone = optional_param('phone', '', PARAM_RAW);

if (empty ($phone)) {
    $SESSION->phone_error_msg = get_string('missingphone', 'auth_twilio');
    redirect(new moodle_url('/auth/twilio/login.php'));
}

$nexturl = new moodle_url('/auth/twilio/check.php');
$backurl = new moodle_url('/auth/twilio/login.php');

$PAGE->set_url(new moodle_url('/auth/twilio/otp.php'));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

if (isset ($SESSION->code_error_msg)) {
    $error = $SESSION->code_error_msg;
    unset($SESSION->code_error_msg);
} else {
    $error = null;
}

if (!$error) { //redirect back with a error no need to send otp again

    $twilio = new \auth_twilio\api();

    if (!$twilio->is_enabled()) {
        throw new \moodle_exception('notenabled', 'auth_twilio');
    }
    $verification = $twilio->verifications($phone);
}

echo $OUTPUT->header();

// Display the OTP form if the otp code is send
// or if you get redirect back with error
if ($error) {
    echo $OUTPUT->render_from_template('auth_twilio/otp', [
        'url'   => $nexturl,
        'phone' => $phone,
        'error' => $error,
    ]);
} else if ($verification->status == 'pending') {
    echo $OUTPUT->render_from_template('auth_twilio/otp', [
        'url'   => $nexturl,
        'phone' => $phone,
    ]);
} else {
    // Use this bag good because when using Moodle redirect
    // it print erros because it request if GET
    echo '<form method="post" action="' . $backurl . '">
        <input type="hidden" name="error" value="' . $verification->status . '" />
    </form>';
}

echo $OUTPUT->footer();