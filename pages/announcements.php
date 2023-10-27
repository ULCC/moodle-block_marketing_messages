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
 * Announcement page where announcements are created and managed.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

// Load in Moodle config.
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

// Load in Moodle's Tablelib lib.
require_once($CFG->dirroot . '/lib/tablelib.php');
// Call in block's table file.
require_once($CFG->dirroot . '/blocks/marketing_messages/classes/announcements_table.php');

// PARAMS.
$params = array();

// Determines whether or not to download the table.
$download = optional_param('download', '', PARAM_ALPHA);

// Determines whether announcement is global or instance-based.
$blockinstance = optional_param('blockid', '', PARAM_INT);

// Used for navigation links to keep track of blockid (if any).
$param = '';
$xparam = '';

// Build params array (used to build url later).
if (isset($download) && $download !== '') {
    $params['download'] = 1;
}

// TODO: Use 'new moodle_url()' instead?
if (isset($blockinstance) && $blockinstance !== '') {
    $param = '?blockid=' . $blockinstance;
    $xparam = '&blockid=' . $blockinstance;
    $params['blockid'] = $blockinstance;
    $bcontext = context_block::instance($blockinstance);
}

// Force the user to login/create an account to access this page.
require_login();

$context = context_system::instance();
$allnotifs = has_capability('block/marketing_messages:manageannouncements', $context);
$ownnotifs = false;

if (!$allnotifs) {
    if (empty($blockinstance) || !isset($blockinstance) || $blockinstance === -1) {
        throw new moodle_exception('marketing_messages_err_nocapability', 'block_marketing_messages');
    }
    $ownnotifs = has_capability('block/marketing_messages:manageownannouncements', $bcontext);
}

if (!$allnotifs && !$ownnotifs) {
    throw new moodle_exception('marketing_messages_err_nocapability', 'block_marketing_messages');
}

// Set PAGE variables.
$url = new moodle_url($CFG->wwwroot . '/blocks/marketing_messages/pages/announcements.php');
$PAGE->set_context($context);
$PAGE->set_url($url, $params);

// Get the renderer for this page.
$renderer = $PAGE->get_renderer('block_marketing_messages');

$table = new marketing_messages_announcements_table('marketing_messages-list');
$table->is_downloading($download, 'marketing_messages-list', 'Marketing Messages List');

if (!$table->is_downloading()) {
    // Only print headers if not asked to download data.
    // Print the page header.
    $PAGE->set_title(get_string('marketing_messages_table_title', 'block_marketing_messages'));
    $PAGE->set_heading(get_string('marketing_messages_table_heading', 'block_marketing_messages'));
    $PAGE->requires->jquery();
    $PAGE->requires->js_call_amd('block_marketing_messages/custom', 'initialise');
    if (isset($bcontext) && $ccontext = $bcontext->get_course_context(false)) {
        $course = $DB->get_field('course', 'fullname', ['id' => $ccontext->instanceid]);
        $PAGE->navbar->add(format_string($course), new moodle_url('/course/view.php', ['id' => $ccontext->instanceid]));
    }
    $PAGE->navbar->add(get_string('blocks'));
    $PAGE->navbar->add(get_string('pluginname', 'block_marketing_messages'));
    $PAGE->navbar->add(get_string('marketing_messages_table_title_short', 'block_marketing_messages'));

    echo $OUTPUT->header();

    echo '<h1 class="page__title">' . get_string('marketing_messages_table_title', 'block_marketing_messages') . '</h1>';
}

// Configure the table.
$table->define_baseurl($url, $params);

$table->set_attribute('class', 'admin_table general_table announcements_table');
$table->collapsible(false);

$table->is_downloadable(true);
$table->show_download_buttons_at(array(TABLE_P_BOTTOM));

// Set SQL params for table.
$sqlwhere = 'deleted = :deleted';
$sqlparams = array('deleted' => 0);
if ($ownnotifs && !$allnotifs) {
    $sqlwhere .= ' AND created_by = :created_by';
    $sqlparams['created_by'] = $USER->id;
}

$table->set_sql('*', "{block_marketing_messages}", $sqlwhere, $sqlparams);

$navbuttons['left'] = '<a class="btn btn-secondary instance"
                                href="' . $CFG->wwwroot . '/blocks/marketing_messages/pages/restore.php' . $param . '">' .
                                get_string('marketing_messages_nav_restore', 'block_marketing_messages') . '</a>';
$navbuttons['right'] = '';
if ($allnotifs) {
    $navbuttons['right'] = '<a class="btn btn-secondary instance" href="' .
        $CFG->wwwroot . '/admin/settings.php?section=blocksettingmarketing_messages' . $xparam . '">' .
        get_string('marketing_messages_nav_settings', 'block_marketing_messages') .
        '</a>';

    $params['global'] = true;
}

if (!$table->is_downloading()) {
    // Add navigation controls before the table.
    echo '<div id="marketing_messages_manage">' .
            get_string('setting/navigation_desc', 'block_marketing_messages', $navbuttons) .
            '</div><br><br>';

    // Add a wrapper with an id, which makes reloading the table easier (when using ajax).
    echo '<div id="marketing_messages_table_wrapper">';
}

$table->out(20, true);

if (!$table->is_downloading()) {
    echo '</div><hr>';
    echo $renderer->add_announcement($params);
    echo $OUTPUT->footer();
}
