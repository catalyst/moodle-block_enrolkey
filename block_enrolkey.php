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
 * Block for allowing enrolment via an enrol key.
 *
 * @package block_enrolkey
 * @copyright  Gleimer Mora <gleimermora@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enrolkey extends block_base {
    /**
     * Initialise the block.
     *
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_enrolkey');
    }

    /**
     * Customise title based on config if set.
     *
     * @return void
     */
    public function specialization() {
        if (isset($this->config->title)) {
            $this->title = format_string($this->config->title, true);
        }
    }

    /**
     * Get the content of the block.
     *
     * @return stdClass|null
     * @throws moodle_exception
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        if ($this->is_auth_plugin_enable() === false) {
            $syscontext = context_system::instance();
            if (has_capability('moodle/site:config', $syscontext) || has_capability('enrol/self:config', $syscontext)) {
                // User should be notified that this won't display as auth_enrol is not enabled.
                $this->content = new stdClass();
                $this->content->text = html_writer::start_div('block_' . $this->name());
                $this->content->text .= html_writer::tag('p', get_string('enrolkeynotenabled', 'block_enrolkey'));
                $this->content->text .= html_writer::end_div();
                return $this->content;
            } else {
                // Regular user so return null.
                return null;
            }
        }

        $this->content = new stdClass();
        $authplugin = get_auth_plugin('enrolkey');
        $form = (new \block_enrolkey\form\enrolkey_form())->set_plugin($authplugin);
        if ($form->get_data()) {
            global $DB, $SESSION;

            $enrolids = $form->get_enrol_ids();
            if (count($enrolids) > 0) {
                // There were successful enrolments, first clear existing notifications.
                if (isset($SESSION->notifications) && is_array($SESSION->notifications)) {
                    $SESSION->notifications = array_filter($SESSION->notifications, function ($n) {
                        if ($n->message === get_string('youenrolledincourse', 'enrol')) {
                            return false;
                        }
                        return true;
                    });
                }

                // Now add new notifications.
                foreach ($enrolids as $enrolid) {
                    $plugin = $DB->get_record('enrol', ['enrol' => 'self', 'id' => $enrolid]);
                    $course = $DB->get_record('course', ['id' => $plugin->courseid]);

                    $coursecontext = context_course::instance($plugin->courseid);
                    $rolenames = role_get_names($coursecontext, ROLENAME_ALIAS, true);

                    $data = new stdClass();
                    $data->course        = $course->fullname;
                    $data->enrolinstance = $plugin->name;
                    $data->role          = $rolenames[$plugin->roleid];
                    $data->startdate     = date('Y-m-d H:i', $plugin->enrolstartdate);
                    $data->enddate       = date('Y-m-d H:i', $plugin->enrolenddate);
                    $data->href          = (new moodle_url('/course/view.php', ['id' => $plugin->courseid]))->out();

                    if ($plugin->enrolstartdate > 0 && $plugin->enrolenddate > 0) {
                        // The course had both a start and end date.
                        $successoutput = get_string('enrolnotification_dates', 'block_enrolkey', $data);
                    } else if ($plugin->enrolstartdate > 0 && $plugin->enrolenddate == 0) {
                        // The course only has a start date set.
                        $successoutput = get_string('enrolnotification_dates_startonly', 'block_enrolkey', $data);
                    } else if ($plugin->enrolstartdate == 0 && $plugin->enrolenddate > 0) {
                        // The course only has a start date set.
                        $successoutput = get_string('enrolnotification_dates_endonly', 'block_enrolkey', $data);
                    } else {
                        // The course has no date restrictions.
                        $successoutput = get_string('enrolnotification', 'block_enrolkey', $data);
                    }

                    \core\notification::add($successoutput, \core\notification::SUCCESS);

                    $this->content = new stdClass();
                    $this->content->text = html_writer::start_div('block_' . $this->name());
                    $this->content->text .= html_writer::tag('p', get_string('formsuccess', 'block_enrolkey'));
                    $this->content->text .= html_writer::end_div();
                    return $this->content;
                }
            } else {
                // There was an error.
                \core\notification::add(get_string('enrolerror', 'block_enrolkey'), \core\notification::ERROR);
            }
        }
        $this->content->text = html_writer::start_div('block_' . $this->name());
        $this->content->text .= $form->render();
        $this->content->text .= html_writer::end_div();

        return $this->content;
    }

    /**
     * Checks whether the auth plugin is enabled.
     *
     * @return bool
     */
    private function is_auth_plugin_enable() {
        // Enrolkey does not need to be the signup method.
        // Self signup does not even need to be enabled.
        // Checking for whether the plugin is enabled is enough to verify it is present and working.
        $authplugins = get_enabled_auth_plugins();
        return in_array('enrolkey', $authplugins);
    }
}
