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
        global $USER;
        if ($this->content !== null) {
          return $this->content;
        }
     
        $this->content =  new stdClass;
        if (!$authplugin = signup_is_enabled()) {
            print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
        }
        $enrolkeystring = $_POST['enrol_key'] ?? '';
        if (!empty($enrolkeystring)) {
            $this->enrol_key($enrolkeystring);
        }

        $this->content->text = '<div>';
        $this->content->text .= '<form method="post" accept-charset="utf-8" id="block_enrolkey_form">';
        $this->content->text .= '<input type="text" name="enrol_key" id="id_enrol_key">';
        $this->content->text .= '<button  type="submit" class="btn btn-secondary" id="enrol_key_submit" title>';
        $this->content->text .= 'Enrol</button>';
        $this->content->text .= '</form>';
        $this->content->text .= '</div>';
        $this->content->footer = '';
     
        return $this->content;
    }

    private function enrol_key(string $enrolkey) {
        global $DB;
        // Password is the Enrolment key that is specified in the Self enrolment instance.
        $enrolplugins = $DB->get_records('enrol', ['enrol' => 'self', 'password' => $enrolkey]);

        $availableenrolids = [];

        /** @var enrol_self_plugin $enrol */
        $enrol = enrol_get_plugin('self');
        foreach ($enrolplugins as $enrolplugin) {
            if ($enrol->can_self_enrol($enrolplugin) === true) {

                $data = new stdClass();
                $data->enrolpassword = $enrolplugin->password;
                $enrol->enrol_self($enrolplugin, $data);
                $availableenrolids[] = $enrolplugin->id;
            }
        }

        // Lookup group enrol keys. Not forgetting that group enrolment key is kept in {group}.enrolmentkey.
        $enrolplugins = $DB->get_records_sql("
                SELECT e.*, g.enrolmentkey
                  FROM {groups} g
                  JOIN {enrol} e ON e.courseid = g.courseid
                                AND e.enrol = 'self'
                                AND e.customint1 = 1
                 WHERE g.enrolmentkey = ?
        ", [$enrolkey]);
        foreach ($enrolplugins as $enrolplugin) {
            if ($enrol->can_self_enrol($enrolplugin) === true) {

                $data = new stdClass();
                // A $data should keep the group enrolment key according to implementation of,
                // Method $enrol_self_plugin->enrol_self.
                $data->enrolpassword = $enrolplugin->enrolmentkey;
                $enrol->enrol_self($enrolplugin, $data);
                $availableenrolids[] = $enrolplugin->id;
            }
        }
    }
}
