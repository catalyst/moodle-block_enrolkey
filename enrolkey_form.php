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

//moodleform is defined in formslib.php
require_once($CFG->libdir . '/formslib.php');

/**
 * Class enrolkey_form
 */
class enrolkey_form extends moodleform {

    /**
     * @var auth_plugin_enrolkey
     */
    private $plugin;

    /**
     * @param auth_plugin_enrolkey $authplugin
     * @return enrolkey_form
     */
    public function set_auth(auth_plugin_enrolkey $authplugin): self {
        $this->plugin = $authplugin;
        return $this;
    }

    /**
     * Add elements to form
     * @throws coding_exception
     */
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'enrolkey', '');
        $mform->setType('enrolkey', PARAM_TEXT);
        $mform->addRule('enrolkey', get_string('emptykey', 'block_enrolkey'), 'required', null, 'client');
        $mform->addElement('submit', 'submitbutton', get_string('enrolbutton', 'block_enrolkey'));
    }

    /**
     * @param array $data
     * @param array $files
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function validation($data, $files) {
        parent::validation($data, $files);
        $availableenrolids = $this->plugin->enrol_user($data['enrolkey']);
        if (empty($availableenrolids)) {
            return ['enrolkey' => get_string('invalidkey', 'block_enrolkey')];
        }
        redirect(new moodle_url("/auth/enrolkey/view.php", array('ids' => implode(',', $availableenrolids))));
    }
}
