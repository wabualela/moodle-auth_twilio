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
// die(
//     print_r($code, true)
// );
$firstname = optional_param('firstname', '', PARAM_RAW);
$lastname  = optional_param('lastname', '', PARAM_RAW);

$PAGE->set_url(new moodle_url('/auth/twilio/login.php', []));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

require_sesskey();

if (!\auth_twilio\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

$sid     = "AC660beb04105e094b1da6c41b1529b36b";
$token   = "466db485c9d261b5dd61bf77b4ed090b";
$service = "VA5b73cbb4931de800cef6f52afa72d905";
$twilio  = new Twilio\Rest\Client($sid, $token);

echo $OUTPUT->header();
echo $OUTPUT->heading("WhatsApp");

if ($tel && confirm_sesskey()) {
    try {
        $verification = $twilio->verify
            ->v2
            ->services($service)
            ->verifications
            ->create($tel, "whatsapp");

        if ($verification->status == 'pending') {
            echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
            if (!$exist = $DB->record_exists('user', [ 'phone1' => $tel ])) {
                echo html_writer::tag('input', '', [
                    'placeholder'  => get_string('firstname'),
                    'name'         => 'firstname',
                    'autocomplete' => 'firstname',
                    'class'        => 'form-control',
                ]);
                echo html_writer::tag('input', '', [ 'placeholder' => get_string('lastname'), 'name' => 'lastname', 'autocomplete' => 'lastname', 'class' => 'form-control',]);
            }

            echo html_writer::tag('input', '', [ 'placeholder' => 'Enter your OTP code', 'name' => 'code', 'class' => 'form-control',]);
            echo html_writer::tag('input', '', [ 'value' => $tel, 'name' => 'to', 'type' => "hidden" ]);
            echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
            echo html_writer::tag('button', 'Check', [ 'class' => 'btn btn-primary',]);
            echo html_writer::end_tag('form');
        }
    } catch (Exception $e) {
        redirect(new moodle_url('/login/index.php'), $e->getMessage(), 1000, 'error');
    }

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
        if ($exist = $DB->record_exists('user', [ 'phone1' => $to ])) {
            $user = $DB->get_record('user', [ 'phone1' => $to ]);
            complete_user_login($user, []);
            redirect(new moodle_url('/'));
        } else {

            $user               = new stdClass();
            $user->phone1       = $to;
            $user->username     = $to;
            $user->firstname    = $firstname;
            $user->lastname     = $lastname;
            $user->email        = $to;
            $user->password     = hash('sha256', $to . $firstname . $lastname);
            $user->auth         = 'twilio';
            $user->confirmed    = 1;
            $user->mnethostid   = 1;
            $user->firstaccess  = time();
            $user->lastaccess   = time();
            $user->lastlogin    = time();
            $user->lastlogin    = time();
            $user->currentlogin = time();
            $user->id           = $DB->insert_record('user', $user);
            complete_user_login($user, []);
            redirect(new moodle_url('/'));
        }
    } else {
        redirect(new moodle_url('/login/index.php'), 'Verification code not correct.', 0, 'error');
    }
} else {

    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    echo html_writer::tag('input', '', [ 'placeholder' => 'Enter your phone number', 'name' => 'tel', 'autocomplete' => 'tel', 'class' => 'form-control', 'required' => true, 'autofocus' => true ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Send', [ 'class' => 'btn btn-success mt-2' ]);
    echo html_writer::end_tag('form');

    /*
    <div class="container height-100 d-flex justify-content-center align-items-center">
     <div class="position-relative">
     <div class="card p-2 text-center">
     <h6>Please enter the one time password <br> to verify your account</h6>
     <div>
        <span>A code has been sent to</span>
        <small>*******9897</small>
     </div>
     <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2"> <input class="m-2 text-center form-control rounded" type="text" id="first" maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text" id="second" maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text" id="third" maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text" id="fourth" maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text" id="fifth" maxlength="1" /> <input class="m-2 text-center form-control rounded" type="text" id="sixth" maxlength="1" /> </div> <div class="mt-4"> <button class="btn btn-danger px-4 validate">Validate</button> </div> </div> <div class="card-2"> <div class="content d-flex justify-content-center align-items-center"> <span>Didn't get the code</span> <a href="#" class="text-decoration-none ms-3">Resend(1/3)</a> </div> </div> </div>
</div> */

    // echo html_writer::tag('hr', '');
    // echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    // // if (!$exist = $DB->record_exists('user', [ 'phone1' => $tel, 'confirmed' => true, 'deleted' => false ])) {

    // echo html_writer::start_div('d-flex');
    // echo html_writer::tag('input', '', [ 'placeholder' => get_string('firstname'), 'name' => 'firstname', 'autocomplete' => 'firstname', 'class' => 'form-control rounded m-2' ]);
    // echo html_writer::tag('input', '', [ 'placeholder' => get_string('lastname'), 'name' => 'lastname', 'autocomplete' => 'lastname', 'class' => 'form-control rounded m-2' ]);
    // echo html_writer::end_div();

    // }

    /*  echo html_writer::tag('style', '.inputs input {
         width: 40px;
         height: 40px
     }

     input[type=number]::-webkit-inner-spin-button,
     input[type=number]::-webkit-outer-spin-button {
         -webkit-appearance: none;
         -moz-appearance: none;
         appearance: none;
         margin: 0
     }'); */
    /*  echo html_writer::start_div('d-flex flex-row justify-content-center my-2 inputs');
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::tag('input', '', [ 'name' => 'code[]', 'class' => 'm-2 text-center form-control rounded' ]);
     echo html_writer::end_div(); */

    /* echo html_writer::tag('input', '', [ 'value' => $tel, 'name' => 'to', 'type' => "hidden" ]);
    echo html_writer::tag('input', '', [ 'value' => sesskey(), 'name' => 'sesskey', 'type' => "hidden" ]);
    echo html_writer::tag('button', 'Validate', [ 'class' => 'btn btn-primary' ]);
    echo html_writer::end_tag('form'); */

}

echo $OUTPUT->footer();
