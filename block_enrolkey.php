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

defined('MOODLE_INTERNAL') || die;

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
     * @return stdClass|stdObject
     * @throws moodle_exception
     */
    public function get_content() {
        if ($this->content !== null || $this->is_auth_plugin_enable() === false) {
            return $this->content;
        }

        $this->content = new stdClass;
        $authplugin = get_auth_plugin('enrolkey');
        $form = (new \block_enrolkey\form\enrolkey_form())->set_plugin($authplugin);
        if ($form->get_data()) {
            $enrolids = $form->get_enrol_ids();
            redirect(new moodle_url("/auth/enrolkey/view.php", ['ids' => implode(',', $enrolids)]));
        }
        $this->content->text = html_writer::start_div('block_' . $this->name());
        $this->content->text .= $form->render();
        $this->content->text .= html_writer::end_div();

        return $this->content;
    }

    /**
     * @return bool|mixed
     */
    private function is_auth_plugin_enable() {
        // Enrolkey does not need to be the signup method.
        // Self signup does not even need to be enabled.
        // Checking for whether the plugin is enabled is enough to verify it is present and working.
        $authplugins = get_enabled_auth_plugins();
        return in_array('enrolkey', $authplugins);
    }
}
