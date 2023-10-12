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
 * Base table that lists announcements to allow visibility and certain actions.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

defined('MOODLE_INTERNAL') || die;

// Load tablelib lib.
require_once($CFG->dirroot .'/lib/tablelib.php');

// The word 'announcements' is used twice, as I'm using the 'pluginname_filename' convention.

/**
 * Sets up the table which lists announcements and allows for management of listed items.
 *
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class marketing_messages_base_table extends table_sql {

    // Lang strings that get re-used below is stored in variables to improve efficiency (Don't have to get strings many times).
    /**
     * @var null|string
     */
    private $yes = null;
    /**
     * @var null|string
     */
    private $no = null;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array(
            'id',
            'title',
            'type',
            'enabled',
            //'global',
            'audience',
            'aicon',
            'dismissible',
            'times',
            'date_from',
            'date_to',
            'actions'
        );
        $this->define_columns($columns);

        // Define the titles of columns to show in header from lang file.               // Examples.
        $headers = array(
            get_string('marketing_messages_field_id', 'block_marketing_messages'),          // Id: 1.
            get_string('marketing_messages_field_title', 'block_marketing_messages'),       // Title: Site Maintenance.
            get_string('marketing_messages_field_type', 'block_marketing_messages'),        // Type: info.
            get_string('marketing_messages_field_enabled', 'block_marketing_messages'),     // Enabled: Yes.
            //get_string('marketing_messages_field_global', 'block_marketing_messages'),      // Global: Yes.
            get_string('marketing_messages_field_audience', 'block_marketing_messages'),      // Audience: Yes.
            get_string('marketing_messages_field_aicon', 'block_marketing_messages'),       // AIcon: Yes.
            get_string('marketing_messages_field_dismissible', 'block_marketing_messages'), // Dismissible: Yes.
            get_string('marketing_messages_field_times', 'block_marketing_messages'),       // Times: 10.
            get_string('marketing_messages_field_date_from', 'block_marketing_messages'),   // Date From: dd/mm/yyyy.
            get_string('marketing_messages_field_date_to', 'block_marketing_messages'),     // Date To: dd/mm/yyyy.
            get_string('marketing_messages_field_actions', 'block_marketing_messages'),     // Actions: Edit | Delete.
        );
        $this->define_headers($headers);

        $this->sortable(true, 'id', SORT_DESC);
        $this->no_sorting('actions');

        // Lang string initialisation.
        $this->yes = get_string('marketing_messages_cell_yes', 'block_marketing_messages'); // Yes.
        $this->no = get_string('marketing_messages_cell_no', 'block_marketing_messages');   // No.
    }

    /**
     * This function is called for each data row to allow processing of the
     * id value.
     *
     * @param object $values Contains object with all the values of record.
     * @return integer Returns announcement ids - easier sorting
     */
    public function col_id($values) {
        return $values->id;
    }

    /**
     * This function is called for each data row to allow processing of the
     * title value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Returns announcement's title - easier sorting
     */
    public function col_title($values) {
        return shorten_text($values->title, 22, true);
    }

    /**
     * This function is called for each data row to allow processing of the
     * type value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return announcement type (for styling purposes)
     */
    public function col_type($values) {
        return get_string('marketing_messages_add_option_' . $values->type, 'block_marketing_messages');
    }

    /**
     * This function is called for each data row to allow processing of the
     * enabled value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return whether announcement is enabled or not
     */
    public function col_enabled($values) {
        return ($values->enabled == 1 ? $this->yes : $this->no);
    }

    /**
     * This function is called for each data row to allow processing of the
     * global value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return whether announcement will be propagated globally/site-wide or not
     */
    public function col_global($values) {
        return ($values->global == 1 ? $this->yes : $this->no);
    }

    /**
     * This function is called for each data row to allow processing of the
     * audience value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return audience restricted to
     */
    public function col_audience($values) {
        return get_string('marketing_messages_audience_option_' . $values->audience, 'block_marketing_messages');
    }

    /**
     * This function is called for each data row to allow processing of the
     * aicon value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return whether announcement is to display an icon or not
     */
    public function col_aicon($values) {
        return ($values->aicon == 1 ? $this->yes : $this->no);
    }

    /**
     * This function is called for each data row to allow processing of the
     * dismissible value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return whether announcement is dismissible or not
     */
    public function col_dismissible($values) {
        return ($values->dismissible == 1 ? $this->yes : $this->no);
    }

    /**
     * This function is called for each data row to allow processing of the
     * times value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return number of times the user should view the announcement
     */
    public function col_times($values) {
        return $values->times;
    }

    /**
     * This function is called for each data row to allow processing of the
     * date_from value.
     *
     * @param object $values Contains object with all the values of record.
     * @return integer Return value from when the announcement should be displayed
     * @throws dml_exception
     */
    public function col_date_from($values) {
        if ($values->date_from <= 0) {
            return '-';
        }

        return date(get_config('block_marketing_messages', 'dateformat'), $values->date_from);
    }

    /**
     * This function is called for each data row to allow processing of the
     * date_to value.
     *
     * @param object $values Contains object with all the values of record.
     * @return integer Return value until when the announcement should be displayed
     * @throws dml_exception
     */
    public function col_date_to($values) {
        if ($values->date_to <= 0 || $values->date_from === $values->date_to) {
            return '-';
        }

        return date(get_config('block_marketing_messages', 'dateformat'), $values->date_to);
    }

    /**
     * This function is not part of the public api.
     */
    public function print_nothing_to_display() {
        $this->print_initials_bar();

        echo '<p class="announcements--empty">' . get_string('marketing_messages_table_empty', 'block_marketing_messages') . '</p>';
    }
}