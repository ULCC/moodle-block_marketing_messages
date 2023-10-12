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
 * Marketing Messages block settings
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

defined('MOODLE_INTERNAL') || die;

require_once('locallib.php');

global $CFG;

if ($ADMIN->fulltree) {

    // Used for navigation links to keep track of blockid (if any).
    $blockid = optional_param('blockid', '', PARAM_INT);
    $param = '';

    if (isset($blockid) && $blockid !== '') {
        $param = '?blockid=' . $blockid;
    }

    $navbuttons['left'] = '<a class="btn btn-secondary instance"
                                href="' . $CFG->wwwroot . '/blocks/marketing_messages/pages/announcements.php' . $param . '">' .
                                get_string('marketing_messages_nav_manage', 'block_marketing_messages') . '</a>';
    $navbuttons['right'] = '<a class="btn btn-secondary instance"
                                href="' . $CFG->wwwroot . '/blocks/marketing_messages/pages/restore.php' . $param . '">' .
                                get_string('marketing_messages_nav_restore', 'block_marketing_messages') . '</a>';

    // SETTINGS' NAVIGATIONAL LINKS HEADING & LINKS.
    $settings->add(
        new admin_setting_heading(
            'block_marketing_messages/navigation',                                                            // NAME.
            get_string('setting/navigation', 'block_marketing_messages'),                                     // TITLE.
                        '<div id="marketing_messages_manage">' .
                            get_string('setting/navigation_desc', 'block_marketing_messages', $navbuttons) .
                        '</div><br>'                                                                        // DESCRIPTION.
        )
    );

    // SETTINGS HEADING.
    $settings->add(
        new admin_setting_heading(
            'block_marketing_messages/settings',                                                              // NAME.
            get_string('setting/settings', 'block_marketing_messages'),                                       // TITLE.
            null
        )
    );

    // ENABLE TOGGLE.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/enable',                                                                // NAME.
            get_string('setting/enable', 'block_marketing_messages'),                                         // TITLE.
            get_string('setting/enable_desc', 'block_marketing_messages'),                                    // DESCRIPTION.
            get_string('setting/enable_default', 'block_marketing_messages')                                  // DEFAULT.
        )
    );

    // ALLOW HTML TOGGLE.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/html',                                                                  // NAME.
            get_string('setting/html', 'block_marketing_messages'),                                           // TITLE.
            get_string('setting/html_desc', 'block_marketing_messages'),                                      // DESCRIPTION.
            get_string('setting/html_default', 'block_marketing_messages')                                    // DEFAULT.
        )
    );

    // MULTILANG FILTER(S) SUPPORT TOGGLE.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/multilang',                                                             // NAME.
            get_string('setting/multilang', 'block_marketing_messages'),                                      // TITLE.
            get_string('setting/multilang_desc', 'block_marketing_messages'),                                 // DESCRIPTION.
            get_string('setting/multilang_default', 'block_marketing_messages')                               // DEFAULT.
        )
    );

    // DATE FORMAT.
    $options = mm_get_date_formats();
    $settings->add(
        new admin_setting_configselect(
            'block_marketing_messages/dateformat',                                                            // NAME.
            get_string('setting/dateformat', 'block_marketing_messages'),                                     // TITLE.
            get_string('setting/dateformat_desc', 'block_marketing_messages'),                                // DESCRIPTION.
            array_keys($options)[0],                                                                        // DEFAULT.
            $options                                                                                        // OPTIONS.
        )
    );

    // AUTO-DELETE TOGGLE.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/auto_delete',                                                           // NAME.
            get_string('setting/auto_delete', 'block_marketing_messages'),                                    // TITLE.
            get_string('setting/auto_delete_desc', 'block_marketing_messages'),                               // DESCRIPTION.
            get_string('setting/auto_delete_default', 'block_marketing_messages')                             // DEFAULT.
        )
    );

    // AUTO-PERMADELETE OLD DELETED NOTIFICATIONS.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/auto_perma_delete',                                                     // NAME.
            get_string('setting/auto_perma_delete', 'block_marketing_messages'),                              // TITLE.
            get_string('setting/auto_perma_delete_desc', 'block_marketing_messages'),                         // DESCRIPTION.
            get_string('setting/auto_perma_delete_default', 'block_marketing_messages')                       // DEFAULT.
        )
    );

    // AUTO-DELETE USER DATA TOGGLE.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/auto_delete_user_data',                                                 // NAME.
            get_string('setting/auto_delete_user_data', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/auto_delete_user_data_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            get_string('setting/auto_delete_user_data_default', 'block_marketing_messages')                   // DEFAULT.
        )
    );

    // ENABLE THE MARKETING MESSAGES CAROUSEL.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/carousel',                                                 // NAME.
            get_string('setting/carousel', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/carousel_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            get_string('setting/carousel_default', 'block_marketing_messages')                   // DEFAULT.
        )
    );

    // SET THE MARKETING MESSAGES INTERVAL.
    $settings->add(
        new admin_setting_configtext(
            'block_marketing_messages/interval',                                                 // NAME.
            get_string('setting/interval', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/interval_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            get_string('setting/interval_default', 'block_marketing_messages'),                  // DEFAULT.
            PARAM_INT
        )
    );

    // SET THE MESSAGE TITLE LIMIT.
    $settings->add(
        new admin_setting_configtext(
            'block_marketing_messages/titlelimit',                                                 // NAME.
            get_string('setting/titlelimit', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/titlelimit_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            get_string('setting/titlelimit_default', 'block_marketing_messages'),                  // DEFAULT.
            PARAM_INT
        )
    );

    // SET THE MESSAGE LIMIT.
    $settings->add(
        new admin_setting_configtext(
            'block_marketing_messages/messagelimit',                                                 // NAME.
            get_string('setting/messagelimit', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/messagelimit_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            get_string('setting/messagelimit_default', 'block_marketing_messages'),                  // DEFAULT.
            PARAM_INT
        )
    );

    // DECIDE THE METHOD FOR TARGET GROUPS.
    $settings->add(
        new admin_setting_configcheckbox(
            'block_marketing_messages/audience',                                                 // NAME.
            get_string('setting/audience', 'block_marketing_messages'),                          // TITLE.
            get_string('setting/audience_desc', 'block_marketing_messages'),                     // DESCRIPTION.
            $default = 1, $yes = 1
        )
    );
}