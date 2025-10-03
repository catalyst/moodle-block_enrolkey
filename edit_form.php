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
 * Block edit form class for the block_enrolkey plugin.
 *
 * @package block_enrolkey
 * @copyright  Gleimer Mora <gleimermora@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enrolkey_edit_form extends block_edit_form {
    /**
     * Setup block settings edit form
     *
     * @param MoodleQuickForm $mform
     * @return void
     */
    protected function specific_definition($mform) {
        // Section header title according to language file.
        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_enrolkey'));
        $mform->setDefault('config_title', get_string('configtitle_default', 'block_enrolkey'));
        $mform->setType('config_title', PARAM_TEXT);
    }
}
