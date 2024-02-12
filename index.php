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
 * TODO describe file index
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require(__DIR__ . '/vendor/autoload.php');

require_login();

$url = new moodle_url('/auth/twilio/index.php', []);
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$PAGE->set_heading($SITE->fullname);
echo $OUTPUT->header();

$sid    = "ACb9c458a9428b1ce16603521ea811af62";
$token  = "be64f692ed1f906386b81397c6e84587";
$twilio = new Twilio\Rest\Client($sid, $token);

// $verification = $twilio->verify->v2->services("VA1abf832cfc8f432e8b1f4ae113885dc6")
//     ->verifications
//     ->create("+256781547101", "whatsapp");

// print($verification->status);

echo $OUTPUT->footer();
