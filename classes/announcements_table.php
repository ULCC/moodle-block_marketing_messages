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
 * Table that lists announcements.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

defined('MOODLE_INTERNAL') || die;

// Load parent (& tablelib lib).
require_once(dirname(__FILE__) . '/base_table.php');

// The word 'announcements' is used twice, as I'm using the 'pluginname_filename' convention.

/**
 * Sets up the table which lists announcements and allows for management of listed items.
 *
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marketing_messages_announcements_table extends marketing_messages_base_table {

    /**
     * This function is called for each data row to allow processing of the
     * actions value.
     *
     * @param   object $values Contains object with all the values of record.
     * @return  string Return url to view the individual transaction
     * @throws  coding_exception
     */
    public function col_actions($values) {
        global $CFG;

        if ($this->is_downloading()) {
            return get_string('marketing_messages_edit_label', 'block_marketing_messages') . ' | ' .
                    get_string('marketing_messages_delete_label', 'block_marketing_messages');
        } else {
            return '<form id="tredit'.$values->id.'" data-edit="' . $values->id . '" method="POST" action="' . $CFG->wwwroot .
                '/blocks/marketing_messages/pages/process.php">
                    <input type="hidden" class="edit_announcement_sesskey" name="sesskey" value="' . sesskey() . '">
                    <input type="hidden" class="edit_announcement_purpose" name="purpose" value="edit">
                    <input type="hidden" class="edit_announcement_tableaction" name="tableaction" value="' . $values->id . '">
                    <input type="hidden" class="edit_announcement_blockid" name="blockid" value="' . $values->blockid . '">
                    <button type="submit" class="edit_announcement_edit icon fa fa-pencil-square-o fa-fw" name="edit"
                        title="' . get_string('marketing_messages_edit_label', 'block_marketing_messages') . '"></button>
                </form>
                <form id="trdelete'.$values->id.'" data-delete="' . $values->id . '" method="POST" action="' . $CFG->wwwroot .
                '/blocks/marketing_messages/pages/process.php">
                    <input type="hidden" class="delete_announcement_sesskey" name="sesskey" value="' . sesskey() . '">
                    <input type="hidden" class="delete_announcement_purpose" name="purpose" value="delete">
                    <input type="hidden" class="delete_announcement_tableaction" name="tableaction" value="' . $values->id . '">
                    <input type="hidden" class="delete_announcement_blockid" name="blockid" value="' . $values->blockid . '">
                    <button type="submit" class="delete_announcement_delete icon fa fa-trash-o fa-fw" name="delete"
                        title="' . get_string('marketing_messages_delete_label', 'block_marketing_messages') . '"></button>
                </form>';
        }
    }
}