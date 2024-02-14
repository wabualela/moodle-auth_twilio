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

require('../../config.php');
require_once('classes/vendor/autoload.php');

$tel  = optional_param('tel', '', PARAM_RAW);
$code = optional_param('code', '', PARAM_RAW);
$to   = optional_param('to', '', PARAM_RAW);

$PAGE->set_url(new moodle_url('/auth/twilio/login.php', []));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);

require_sesskey();

if (!\auth_twilio\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

$sid     = "ACb9c458a9428b1ce16603521ea811af62";
$token   = "05d3420ecc0d88a0ffa2d69ee3bb0147";
$service = "VA1abf832cfc8f432e8b1f4ae113885dc6";
$twilio  = new Twilio\Rest\Client($sid, $token);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('login'));

if ($tel && confirm_sesskey()) {

    $verification = $twilio->verify
        ->v2
        ->services($service)
        ->verifications
        ->create("+256781547101", "whatsapp");

    print("Status: " . $verification->status);

    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    echo html_writer::tag('input', '', [ 'placeholder' => 'Enter your OTP code', 'name' => 'code' ]);
    echo html_writer::tag('input', '', [ 'value' => $tel, 'name' => 'to', 'type' => "hidden" ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Check', []);
    echo html_writer::end_tag('form');

} else if ($code && $to && confirm_sesskey()) {
    $verification_check = $twilio
        ->verify
        ->v2
        ->services($service)
        ->verificationChecks
        ->create([ 'code' => $code, 'to' => $to ]);
    if ($verification_check->status == 'approved') {


    }
} else {

    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    echo html_writer::tag('input', '', [ 'value' => '+256781547101', 'name' => 'tel', 'autocomplete' => true ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Send', []);
    echo html_writer::end_tag('form');
}

echo $OUTPUT->footer();
