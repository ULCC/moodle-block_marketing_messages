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
 * Library of functions for the plugin to leverage.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This functions determines which announcements to render and what their attributes should be.
 *
 * @param   mixed $instanceid Block instance id.
 * @return  array Array of announcements' attributes needed for rendering.
 * @throws  dml_exception
 */
function prep_announcements($instanceid) {
    global $DB, $USER;

    $filternotif = false;
    // Check if we should apply filters to title/message or not.
    if (get_config('block_marketing_messages', 'multilang')) {
        $filternotif = true;
    }

    // Notifications to render.
    $rendernotif = [];

    // Get announcements with conditions from above.
    $sql = "SELECT * FROM {block_marketing_messages} WHERE
            deleted = :deleted AND enabled = :enabled
            AND (audience = 'all' OR audience = :audience)";
    $allnotifs = $DB->get_records_sql($sql, [
        'deleted' => 0,
        'enabled' => 1,
        'audience' => mm_get_user_audience()
    ]);

    foreach ($allnotifs as $notif) {
        // Keep track of number of times the user has seen the announcement.
        // Check if a record of the user exists in the dismissed/seen table.
        // TODO: Move DB queries out of loop.
        $userseen = $DB->get_record('block_marketing_messagesdiss',
            array('user_id' => $USER->id, 'not_id' => $notif->id)
        );

        // Get announcement settings to determine whether to render it or not.
        $render = false;

        // Check if forever or in date-range.
        if (($notif->date_from === $notif->date_to) || ($notif->date_from < time() && $notif->date_to > time())) {
            $render = true;
        }

        // Don't render if user has seen it more (or equal) to the times specified or if they've dismissed it.
        if ($userseen !== false) {
            if (($userseen->seen >= $notif->times && $notif->times != 0) || ($userseen->dismissed > 0)) {
                $render = false;
            }
        }

        // Don't render if announcement isn't a global announcement and the instanceid's/blockid's don't match.
        if ($notif->blockid != $instanceid && $notif->global == 0) {
            $render = false;
        }

        if ($render) {
            // Update how many times the user has seen the announcement.
            if ($userseen === false) {
                $seenrecord = new stdClass();
                $seenrecord->user_id = $USER->id;
                $seenrecord->not_id = $notif->id;
                $seenrecord->dismissed = 0;
                $seenrecord->seen = 1;

                $DB->insert_record('block_marketing_messagesdiss', $seenrecord);
            } else {
                $upseenrecord = new stdClass();
                $upseenrecord->id = $userseen->id;
                $upseenrecord->seen = $userseen->seen + 1;

                $DB->update_record('block_marketing_messagesdiss', $upseenrecord);
            }

            // Get type to know which (bootstrap) class to apply.
            $aicon = '';

            // Allows for custom styling and serves as a basic filter if anything unwanted was somehow submitted.
            if ($notif->type == "info" || $notif->type == "success" || $notif->type == "warning" || $notif->type == "danger") {
                $aicon = $notif->type;
            } else {
                $notif->type = ($notif->type == "announcement") ? 'info announcement' : 'info';
                $aicon = 'info';
            }

            // Extra classes to add to the announcement wrapper - at least having the 'type' of alert.
            $extraclasses = ' ' . $notif->type;
            if ($notif->dismissible == 1) {
                $extraclasses .= ' dismissible';
            }
            if ($notif->times > 0) {
                $extraclasses .= ' limitedtimes';
            }
            if ($notif->aicon == 1) {
                $extraclasses .= ' aicon';
            }

            // Construct announcement - also format title/text to support multilang (filtered) strings.
            $rendernotif[] = array('extraclasses' => $extraclasses,                                         // Additional classes.
                'notifid' => $notif->id,                                                                    // Announcement id.
                'alerttype' => $notif->type,                                                                // Alert type (styling).
                'aiconflag' => $notif->aicon,                                                               // Render icon flag.
                'aicon' => $aicon,                                                                          // Which icon to render.
                'title' => $filternotif ? format_text($notif->title, FORMAT_HTML) : $notif->title,          // Title.
                'message' => $filternotif ? format_text($notif->message, FORMAT_HTML) : $notif->message,    // Announcement text.
                'dismissible' => $notif->dismissible);                                                      // Dismissible flag.
        }
    }

    return $rendernotif;
}

/**
 * Get date formats supported by the plugin.
 *
 * @return  array   Array of formats as key and today's date in that format as value.
 */
function mm_get_date_formats() {
    $formats = [];

    // Show formats for 30 January 2023.
    $timestamp = '1675036800';

    // Add supported formats to array.
    $formats['d/m/Y H:i'] = date('d/m/Y H:i', $timestamp);
    $formats['j/n/y H:i'] = date('j/n/y H:i', $timestamp);
    $formats['m-d-Y H:i'] = date('m-d-Y H:i', $timestamp);
    $formats['n-j-y H:i'] = date('n-j-y H:i', $timestamp);
    $formats['Y-m-d H:i'] = date('Y-m-d H:i', $timestamp);
    $formats['j M y H:i'] = date('j M y H:i', $timestamp);
    $formats['j F Y H:i'] = date('j F Y H:i', $timestamp);

    return $formats;
}

/**
 * Get the user audience based on config settings.
 *
 * @return  str   Whether audience is student or staff.
 */
function mm_get_user_audience() {
    global $DB, $USER;

    $conf = get_config('block_marketing_messages', 'audience');
    if ($conf) {
        if (stripos($USER->email, '@stu') !== false) {
            return 'student';
        }
    } else {
        if ($DB->get_records_sql("select 1 from {role_assignments} where userid = ? and roleid in
            (select roleid from {role_capabilities} where capability = ?) limit 1",
                [$USER->id, 'mod/assign:submit'])) {
            return 'student';
        }
    }
    return 'staff';
}