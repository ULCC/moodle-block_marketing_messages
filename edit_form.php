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

/**
 * Form displayed when configuring block.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */
class block_marketing_messages_edit_form extends block_edit_form {

    /**
     * Build form.
     *
     * @param object $mform
     */
    protected function specific_definition($mform) {
        global $CFG;

        // Used for navigation links to keep track of blockid (if any).
        $blockid = optional_param('bui_editid', '', PARAM_INT);
        $param = '';
        $xparam = '';

        if (isset($blockid) && $blockid !== '') {
            $param = '?blockid=' . $blockid;
            $xparam = '&blockid=' . $blockid;
        }

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $context = context_system::instance();

        // Only render links to users that are allowed to manage announcements.
        if (has_capability('block/marketing_messages:manageannouncements', $context)) {
            $mform->addElement('html',
                '<div id="marketing_messages_manage" class="manage_announcements">
                                <h3>' .
                get_string('marketing_messages_nav_heading', 'block_marketing_messages') .
                '</h3>
                                <a class="btn btn-secondary instance" href="' . $CFG->wwwroot .
                '/blocks/marketing_messages/pages/announcements.php' . $param . '">' .
                get_string('marketing_messages_nav_manage', 'block_marketing_messages') .
                '</a>&nbsp;&nbsp;
                                <a class="btn btn-secondary" href="' . $CFG->wwwroot .
                '/blocks/marketing_messages/pages/restore.php' . $param . '">' .
                get_string('marketing_messages_nav_restore', 'block_marketing_messages') .
                '</a>&nbsp;&nbsp;
                                <a class="btn btn-secondary" href="' . $CFG->wwwroot .
                '/admin/settings.php?section=blocksettingmarketing_messages' . $xparam . '">' .
                get_string('marketing_messages_nav_settings', 'block_marketing_messages') .
                '</a><br><br>
                            </div>'
            );
        }

        // Allows a custom class to be added to the block for styling purposes.
        $mform->addElement('text', 'config_class', get_string('marketing_messages_class', 'block_marketing_messages'));
        $mform->setDefault('config_class', '');
        $mform->setType('config_class', PARAM_TEXT);
    }
}