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
 * Form for editing Enrol key instances.
 *
 * @package   block_enrolkey
 * @copyright 2026 Catalyst IT
 * @author    Peter Burnett <peterburnett@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enrolkey_edit_form extends block_edit_form {

    /**
     * Enable general settings
     *
     * @return bool
     */
    protected function has_general_settings() {
        return true;
    }

    protected function specific_definition($mform) {
        parent::specific_definition($mform);

        // Fields for editing Enrolkey block content.
        $mform->addElement('header', 'configheader', get_string('customblocksettings', 'block'));

        $mform->addElement('textarea', 'config_content', get_string('configcontent', 'block_enrolkey'));
        $mform->setType('config_content', PARAM_RAW);
    }

    function set_data($defaults) {
        parent::set_data($defaults);

        if (!empty($this->block->config) && is_object($this->block->config)) {
            $text = $this->block->config->content;
        } else {
            $text = '';
        }

        if (!isset($this->block->config)) {
            $this->block->config = new stdClass();
        }
        $this->block->config->content = $text;
    }
}
