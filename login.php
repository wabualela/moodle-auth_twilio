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
 * TODO describe file login
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require ('../../config.php');

$PAGE->set_url(new moodle_url('/auth/twilio/login.php'));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

$twilio = new \auth_twilio\api();

if (!$twilio->is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

if (isset ($SESSION->phone_error_msg)) {
    $error = $SESSION->phone_error_msg;
    unset($SESSION->phone_error_msg);
} else {
    $error = null;
}

$nexturl = new \moodle_url('/auth/twilio/otp.php');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('auth_twilio/tel', [
    'url'   => $nexturl,
    'error' => $error,
]);
echo $OUTPUT->footer();