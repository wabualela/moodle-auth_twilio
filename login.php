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

$PAGE->set_url(new moodle_url('/auth/twilio/login.php', []));
$PAGE->set_pagelayout('login');
$PAGE->set_context(context_system::instance());

require_sesskey();

if (!\auth_twilio\api::is_enabled()) {
    throw new \moodle_exception('notenabled', 'auth_twilio');
}

try {
    $sid     = get_config('auth_twilio', 'accountsid');
    $token   = get_config('auth_twilio', 'token');
    $service = get_config('auth_twilio', 'servicesid');
    $twilio  = new Twilio\Rest\Client($sid, $token);

} catch (Exception $exception) {
    echo html_writer::tag('span', $exception->getMessage(), [ 'class' => 'alert alert-danger' ]);
}

echo $OUTPUT->header();

echo html_writer::start_div('contianer m-6');

echo html_writer::tag('h2', 'WhatsApp');
if ($tel && confirm_sesskey()) {
    try {
        $verification = $twilio->verify
            ->v2
            ->services($service)
            ->verifications
            ->create($tel, "whatsapp");

        if ($verification->status == 'pending') {
            echo html_writer::start_tag('form', [
                'action' => $PAGE->url,
                'method' => 'post',
            ]);
            if (!$exist = $DB->record_exists('user', [ 'phone1' => $tel ])) {
                echo html_writer::tag('input', '', [
                    'placeholder'  => get_string('firstname'),
                    'name'         => 'firstname',
                    'autocomplete' => 'firstname',
                    'class'        => 'form-control',
                ]);
                echo html_writer::tag('input', '', [
                    'placeholder'  => get_string('lastname'),
                    'name'         => 'lastname',
                    'autocomplete' => 'lastname',
                    'class'        => 'form-control',
                ]);
                echo html_writer::tag('input', '', [
                    'placeholder'  => get_string('fullname'),
                    'name'         => 'fullname',
                    'autocomplete' => 'fullname',
                    'class'        => 'form-control',
                ]);
            }

            echo html_writer::tag('input', '', [
                'placeholder' => 'Enter your OTP code',
                'name'        => 'code',
                'class'       => 'form-control',
            ]);
            echo html_writer::tag('input', '', [
                'value' => $tel,
                'name'  => 'to',
                'type'  => "hidden",
            ]);
            echo html_writer::tag('input', '', [
                'value' => sesskey(),
                'name'  => 'sesskey',
                'type'  => "hidden",
            ]);
            echo html_writer::tag('input', '', [
                'value' => get_string('check'),
                'class' => 'btn btn-primary',
                'type'  => 'submit',
            ]);
            echo html_writer::end_tag('form');
        }
    } catch (Exception $e) {
        echo html_writer::tag('span', $e->getMessage(), [ 'class' => 'alert alert-danger' ]);
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
            $DB->insert_record('user_info_data', [
                'userid'  => $user->id,
                'data'    => $user->firstname . '' . $user->lastname,
                'fieldid' => 1,
            ]);
            complete_user_login($user, []);
            redirect(new moodle_url('/'));
        }
    } else {
        redirect(new moodle_url('/login/index.php'), 'Verification code not correct.', 0, 'error');
    }
} else {
    echo html_writer::start_tag('form', [ 'action' => $PAGE->url, 'method' => 'post' ]);
    echo html_writer::tag('input', '', [
        'id'           => 'tel',
        'name'         => 'tel',
        'class'        => 'form-control',
        'placeholder'  => 'Enter your phone number',
        'autocomplete' => 'tel',
        'required'     => true,
        'autofocus'    => true,
    ]);
    echo html_writer::tag('input', '', [
        'type'  => 'submit',
        'value' => get_string('verify', 'auth_twilio'),
        'class' => 'btn btn-success mx-2',
        'role'  => 'button',
    ]);
    echo html_writer::tag('input', '', [
        'name'  => 'sesskey',
        'value' => sesskey(),
        'type'  => "hidden",
    ]);
    echo html_writer::end_tag('form');

}
echo html_writer::end_div();
echo $OUTPUT->footer();
?>
<script>
    function getIp(callback) {
        fetch('https://ipinfo.io/json?token=a05be40191d88c', { headers: { 'Accept': 'application/json' } })
            .then((resp) => resp.json())
            .catch(() => {
                return {
                    country: 'sa',
                };
            })
            .then((resp) => callback(resp.country));
    }
    const phoneInputField = document.querySelector("#tel");
    const phoneInput = window.intlTelInput(phoneInputField, {
        initialCountry: "auto",
        geoIpLookup: getIp,
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });
</script>