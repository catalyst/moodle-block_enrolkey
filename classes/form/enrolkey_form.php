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

namespace block_enrolkey\form;

use auth_plugin_enrolkey;
use coding_exception;
use moodle_exception;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * Class enrolkey_form
 */
class enrolkey_form extends \moodleform {

    /**
     * @var \auth_plugin_enrolkey
     */
    private $authplugin;

    /**
     * @var array
     */
    private $enrolids = [];

    /**
     * Add elements to form
     * @throws coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'enrolkey', get_string('enrolmentkey', 'block_enrolkey'));
        $mform->setType('enrolkey', PARAM_TEXT);
        $mform->addRule('enrolkey', get_string('emptykey', 'block_enrolkey'), 'required', null, 'client');
        $mform->addElement('submit', 'submitbutton', get_string('enrolbutton', 'block_enrolkey'));
    }

    /**
     * @return array
     */
    public function get_enrol_ids() : array {
        return $this->enrolids;
    }

    /**
     * @param auth_plugin_enrolkey $authplugin
     * @return enrolkey_form
     */
    public function set_plugin(auth_plugin_enrolkey $authplugin) : self {
        $this->authplugin = $authplugin;
        return $this;
    }

    /**
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function validation($data, $files) : array {
        if (!$this->authplugin) {
            return ['enrolkey' => get_string('pluginerror', 'block_enrolkey')];
        }
        $enrolids = $this->authplugin->enrol_user($data['enrolkey']);
        if (empty($enrolids)) {
            return ['enrolkey' => get_string('invalidkey', 'block_enrolkey')];
        }
        $this->enrolids = $enrolids;
        return [];
    }
}
