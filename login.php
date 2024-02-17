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

$tel  = optional_param('tel', '', PARAM_RAW);
$code = optional_param('code', '', PARAM_RAW);
$to   = optional_param('to', '', PARAM_RAW);

$firstname = optional_param('firstname', '', PARAM_RAW);
$lastname  = optional_param('lastname', '', PARAM_RAW);

$PAGE->set_url(new moodle_url('/auth/twilio/login.php', []));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);

require_sesskey();

if (!\auth_twilio\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

$sid     = "AC08e69af0691e2b327dd0702cbf710c77";
$token   = "afe331077bc86d40438859289bddfabe";
$service = "VA4f009bac65c1fd1f586061341e77553f";
$twilio  = new Twilio\Rest\Client($sid, $token);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('login'));

if ($tel && confirm_sesskey()) {

    try {
        $verification = $twilio->verify
            ->v2
            ->services($service)
            ->verifications
            ->create("+256781547101", "whatsapp");

        print("Status: " . $verification->status);
    } catch (Exception $e) {
        echo $OUTPUT->notification($e->getMessage(), 'error');

    }

    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    if (!$exist = $DB->record_exists('user', [ 'phone1' => $tel, 'confirmed' => true, 'deleted' => false ])) {
        echo html_writer::tag('input', '', [ 'placeholder' => get_string('firstname'), 'name' => 'firstname', 'autocomplete' => 'firstname' ]);
        echo html_writer::tag('input', '', [ 'placeholder' => get_string('lastname'), 'name' => 'lastname', 'autocomplete' => 'lastname' ]);
    }
    echo html_writer::tag('input', '', [ 'placeholder' => 'Enter your OTP code', 'name' => 'code' ]);
    echo html_writer::tag('input', '', [ 'value' => $tel, 'name' => 'to', 'type' => "hidden" ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Check', []);
    echo html_writer::end_tag('form');

} else if ($code && $to && confirm_sesskey()) {
    try {
        $verification_check = $twilio
            ->verify
            ->v2
            ->services($service)
            ->verificationChecks
            ->create([ 'code' => $code, 'to' => $to ]);
    } catch (Exception $e) {
        echo $OUTPUT->notification($e->getMessage(), 'error');
    }

    if ($verification_check->status == 'approved') {
        if ($exist = $DB->record_exists('user', [ 'phone1' => $tel, 'confirmed' => true, 'deleted' => false ])) {
            $user = $DB->get_record('user', [ 'phone1' => $tel ]);

        } else {

            $user               = new stdClass();
            $user->phone1       = $to;
            $user->username     = $to;
            $user->firstname    = $firstname;
            $user->lastname     = $lastname;
            $user->email        = $to;
            $user->password     = hash('sha256', $to);
            $user->auth         = 'twilio';
            $user->confirmed    = 1;
            $user->mnethostid   = 1;
            $user->firstaccess  = time();
            $user->lastaccess   = time();
            $user->lastlogin    = time();
            $user->lastlogin    = time();
            $user->currentlogin = time();
            $user->id           = $DB->insert_record('user', $user);
        }
        complete_user_login($user);
        redirect(new moodle_url('/'));
    }
} else {

    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    echo html_writer::tag('input', '', [ 'value' => '+256781547101', 'name' => 'tel', 'autocomplete' => 'tel' ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Send', []);
    echo html_writer::end_tag('form');
}

echo $OUTPUT->footer();
