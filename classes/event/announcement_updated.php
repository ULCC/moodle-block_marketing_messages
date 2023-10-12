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

namespace block_marketing_messages\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The block_marketing_messages announcement updated event class.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */
class announcement_updated extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'block_marketing_messages';
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('event_announcement_updated', 'block_marketing_messages');
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/block/marketing_messages/announcements.php', array('blockid' => $this->contextinstanceid));
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return 'The user with id \'' . $this->userid . '\' updated the announcement with id \'' . $this->objectid . '\'
          for the block with id \'' . $this->contextinstanceid . '\'.
          The title was changed from \'' . $this->other['old_title'] . '\' to \'' . $this->other['new_title'] . '\'.
          The message was changed from \'' . $this->other['old_message'] . '\' to \'' . $this->other['new_message'] . '\'.
          The date from was changed from \'' . userdate($this->other['old_date_from']) . '\' to \'' . userdate($this->other['new_date_from']) . '\'.
          The date to was changed from \'' . userdate($this->other['old_date_to']) . '\' to \'' . userdate($this->other['new_date_to']) . '\'.';
    }
}
