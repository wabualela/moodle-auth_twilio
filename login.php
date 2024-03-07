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
require_once($CFG->libdir . '/authlib.php');
require_once('classes/vendor/autoload.php');

$tel       = optional_param('tel', '', PARAM_RAW);
$code      = optional_param('code', '', PARAM_RAW);
$to        = optional_param('to', '', PARAM_RAW);
$firstname = optional_param('firstname', '', PARAM_RAW);
$lastname  = optional_param('lastname', '', PARAM_RAW);
$fullname  = optional_param('fullname', '', PARAM_RAW);

$PAGE->set_url(new moodle_url('/auth/twilio/login.php', []));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

require_sesskey();

$twilio = new \auth_twilio\api();

if (!$twilio->is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

if ($code && $to && confirm_sesskey()) {

    $verification_check = $twilio->verificationChecks($code, $to);
    if ($verification_check->status == 'approved') {
        $data['firstname'] = $firstname;
        $data['lastname']  = $lastname;
        $data['fullname']  = $fullname;
        $data['phone']     = $to;
        $twilio->complete_login($data);
    }
}

echo $OUTPUT->header();
if ($tel && confirm_sesskey()) {
    $verification = $twilio->verifications($tel);
    if ($verification->status == 'pending') {
        echo $OUTPUT->render_from_template('auth_twilio/otp_form', [
            'url'       => $PAGE->url,
            'exist'     => !$twilio->tel_exist($tel),
            'tel'       => $tel,
            'sesskey'   => sesskey(),
            // 'countries' => $twilio->get_countries_choices(),
        ]);
    }
} else {
    echo $OUTPUT->render_from_template('auth_twilio/tel_form', [
        'url'     => $PAGE->url,
        'sesskey' => sesskey(),
    ]);
}
echo $OUTPUT->footer();