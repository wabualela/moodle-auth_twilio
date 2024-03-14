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
 * English language pack for Twilio
 *
 * @package    auth_twilio
 * @category   string
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Twilio WhatsApp authentication';
$string['notenabled'] = 'Sorry, Twilio (WhatsApp) authentication plugin is not enabled';
// Auth file
$string['whatsapp'] = 'WhatsApp';
$string['dalail'] = 'Dalail Center';
// Settings page
$string['auth_emaildescription']      = '<p>Twilio\'s WhatsApp-based self-registration feature empowers users to effortlessly establish their own accounts through a simple \'Create New Account\' button located on the login page. Upon initiating the process, users promptly receive a WhatsApp message containing a unique code, enabling them to swiftly verify and activate their account. Subsequent login attempts are seamlessly managed by cross-referencing the provided phone number and verification code with the stored data in the Moodle database.</p>';
$string['auth_accountsid']            = 'Account SID';
$string['auth_accountsiddescription'] = 'Twilio Account SID';
$string['auth_token']                 = 'Auth Token';
$string['auth_tokendescription']      = 'Twilio Account Authentication Token';
$string['auth_servicesid']            = 'Service SID';
$string['auth_servicesiddescription'] = 'Twilio Account Service SID';
//  login page
$string['validate']               = 'Validate';
$string['telplaceholder']         = 'Enter your phone number';
$string['telinvalidfeedback']     = 'Tel invalid feedback';
$string['valide']                 = '✓ Valid';
$string['notvalidtel']            = 'Not valid phone number';
$string['fullnameforcertificate'] = 'Full Name for certificate';
// tel errors msg
$string['notcompleted']      = 'operation not completed please try again';
$string['accountincomplete'] = 'Account Creation Incomplete';
// fields
$string['otp']               = 'Enter your OTP code';
$string['otphelp']           = 'One Time Password (OTP) has been sent via WhatsApp to {$a}';
$string['phone']             = 'Phone Number (WhatsApp)';
$string['phonehelp']         = 'Phone Number (WhatsApp)';
$string['phonemissing']      = 'Phone Number missing';
$string['sendcode']          = 'Send Code';
$string['verify']            = 'verify';
$string['singupinstruction'] = 'Please fill in the following fields to create a new account';
$string['certificatename']   = 'Full Name (for certificate)';
$string['age']   = 'Age';



