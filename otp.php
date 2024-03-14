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

require('../../config.php');

$phone   = required_param('phone', PARAM_RAW);
$url     = new moodle_url('/auth/twilio/otp.php', [ 'phone' => $phone ]);
$nexturl = new moodle_url('/auth/twilio/check.php', []);

$PAGE->set_url($url);
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

if ($phone) {
    $twilio = new \auth_twilio\api();

    if (!$twilio->is_enabled())
        throw new \moodle_exception('notenabled', 'auth_twilio');

    $verification = $twilio->verifications($phone);
} else {
    redirect(new moodle_url('/auth/twilio/login.php', [ 'error' => get_string('phonemissing', 'auth_twilio') ]));
}

echo $OUTPUT->header();


//     if ($verification->status == 'pending') {
echo $OUTPUT->render_from_template('auth_twilio/otp', [
    'url'   => $nexturl,
    'phone' => $phone,
]);
//     }
// } else {

echo $OUTPUT->footer();