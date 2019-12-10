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

require_once($CFG->libdir . '/authlib.php');

/**
 * @package block_enrolkey
 */
class block_enrolkey extends block_base {

    /**
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_enrolkey');
    }

    /**
     * @return bool
     */
    function has_config(): bool {
        return true;
    }

    /**
     * @return stdClass|stdObject
     * @throws moodle_exception
     */
    public function get_content() {
        global $USER, $DB;
        if ($this->content !== null) {
          return $this->content;
        }
     
        $this->content =  new stdClass;
        if (!$authplugin = signup_is_enabled()) {
            print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
        }
        $enrolkeystring = $_POST['enrol_key'] ?? '';
        if (!empty($enrolkeystring)) {
            $availableenrolids = $this->enrol_key($DB, $authplugin, $enrolkeystring);
            $authplugin->enrolkey_notify(true, $availableenrolids, $USER->email);
        }
        $this->content->text = '<div>';
        $this->content->text .= '<form method="post" accept-charset="utf-8" id="block_enrolkey_form">';
        $this->content->text .= '<input type="text" name="enrol_key" id="id_enrol_key" autocomplete="off">';
        $this->content->text .= '<button  type="submit" class="btn btn-secondary" id="enrol_key_submit" title>';
        $this->content->text .= get_string('enrolbutton', 'block_enrolkey') . '</button>';
        $this->content->text .= '</form>';
        $this->content->text .= '</div>';
        $this->content->footer = '';
     
        return $this->content;
    }

    /**
     * @param moodle_database $db
     * @param auth_plugin_enrolkey $authplugin
     * @param string $enrolkey
     * @return array
     * @throws dml_exception
     */
    private function enrol_key(moodle_database $db, auth_plugin_enrolkey $authplugin, string $enrolkey): array {
        /** @var enrol_self_plugin $enrol */
        $enrol = enrol_get_plugin('self');

        // Password is the Enrolment key that is specified in the Self enrolment instance.
        $enrolplugins = $authplugin->get_enrol_plugins($db, $enrolkey);
        $availableenrolids = $authplugin->enrol_user($enrol, $enrolplugins);

        // Lookup group enrol keys. Not forgetting that group enrolment key is kept in {group}.enrolmentkey.
        $enrolplugins = $authplugin->get_enrol_plugins($db, $enrolkey, true);
        return array_merge($availableenrolids, $authplugin->enrol_user($enrol, $enrolplugins));
    }
}
