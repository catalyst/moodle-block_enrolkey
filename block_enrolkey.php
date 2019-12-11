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
        global $CFG;
        if ($this->content !== null) {
          return $this->content;
        }
     
        $this->content =  new stdClass;
        if (!$authplugin = signup_is_enabled()) {
            print_error('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
        }

        require_once($CFG->dirroot . '/blocks/enrolkey/enrolkey_form.php');
        $form = (new enrolkey_form())->set_auth($authplugin);
        $form->get_data();
        $this->content->text = $form->render();

        return $this->content;
    }
}
