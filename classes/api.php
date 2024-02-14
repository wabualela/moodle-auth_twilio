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

namespace auth_twilio;

defined('MOODLE_INTERNAL') || die();

require_once('vendor/autoload.php');

use Twilio\Rest\Client;

/**
 * Class api
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Account SID
     * @var string
     */
    var $sid;
    /**
     * Auth Token
     * @var string
     */
    public string $token;
    /**
     * Service Token
     * @var string
     */
    public string $service;
    /**
     * Plugin configuration
     * @var string
     */
    public string $config;
    /**
     * Plugin configuration
     * @var Client Twilio API Client
     */
    public string $twilio;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->sid     = "ACb9c458a9428b1ce16603521ea811af62";
        $this->token   = "be64f692ed1f906386b81397c6e84587";
        $this->service = "VA1abf832cfc8f432e8b1f4ae113885dc6";
        $this->twilio  = new Client($this->sid, $this->service);
    }

    /**
     * Is the plugin enabled.
     *
     * @return bool
     */
    public static function is_enabled() {
        return is_enabled_auth('twilio');
    }


    public static function send_otp_message($tel) {

        $sid     = "ACb9c458a9428b1ce16603521ea811af62";
        $token   = "05d3420ecc0d88a0ffa2d69ee3bb0147";
        $service = "VA1abf832cfc8f432e8b1f4ae113885dc6";
        $twilio  = new Client($sid, $token);

        return $twilio
            ->verify
            ->v2
            ->services($service)
            ->verifications
            ->create($tel, "whatsapp");
    }

    public static function check($params = []) {
        $sid     = "ACb9c458a9428b1ce16603521ea811af62";
        $token   = "05d3420ecc0d88a0ffa2d69ee3bb0147";
        $service = "VA1abf832cfc8f432e8b1f4ae113885dc6";
        $twilio  = new Client($sid, $token);

        return $twilio->verify->v2->services($service)
            ->verificationChecks
            ->create($params);
    }
}
