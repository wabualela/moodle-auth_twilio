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

namespace auth_twilio\form;

/**
 * Class login_form
 *
 * @package    auth_twilio
 * @copyright  2024 Wail Abualela <wailabualela@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class login_form extends \moodleform implements \renderable, \templatable {

    function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'phone', get_string('phone'), 'maxlength="100" size="25"');
        $mform->setType('phone', \core_user::get_property_type('phone1'));
        $mform->addRule('phone', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('phone');

    }
    function definition_after_data() {
    }

    function validation($data, $files) {
    }

    public function export_for_template(\renderer_base $output) {
    }
}
