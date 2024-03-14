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
 * TODO describe file save
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$firstname       = required_param('firstname', PARAM_RAW);
$lastname        = required_param('lastname', PARAM_RAW);
$email           = required_param('email', PARAM_RAW);
$phone           = required_param('phone', PARAM_RAW);
$certificatename = required_param('certificatename', PARAM_RAW);
$age             = required_param('age', PARAM_RAW);

$url = new moodle_url('/auth/twilio/save.php', []);

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$twilio = new \auth_twilio\api();

if (!$twilio->is_enabled())
    throw new \moodle_exception('notenabled', 'auth_twilio');

$data['username']  = $phone;
$data['email']     = $email;
$data['firstname'] = $firstname;
$data['lastname']  = $lastname;
$data['phone1']    = $phone;
// additional fields
$data['customfields']['certificatename'] = $certificatename;
$data['customfields']['age']             = $age;

$twilio->complete_login($data);


