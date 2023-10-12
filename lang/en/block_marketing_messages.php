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
 * All the configurable strings used throughout the plugin.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

$string['pluginname'] = 'Marketing messages';

// Capabilities.
$string['marketing_messages:addinstance'] = 'Add a new Marketing messages block';
$string['marketing_messages:myaddinstance'] = 'Add a new Marketing messages block to the my Moodle page';
$string['marketing_messages:manageannouncements'] = 'Manage announcements and the relative settings';
$string['marketing_messages:manageownannouncements'] = 'Manage own announcements and the relative settings';

// Block Configuration.
$string['marketing_messages'] = 'Marketing messages';
$string['marketing_messages_class'] = 'Block class:';

// Notifications Table Column Names & Table Related Lang Strings.
$string['marketing_messages_field_id'] = 'ID';
$string['marketing_messages_field_title'] = 'Title';
$string['marketing_messages_field_type'] = 'Type';
$string['marketing_messages_field_enabled'] = 'Enabled';
$string['marketing_messages_field_global'] = 'Global';
$string['marketing_messages_field_audience'] = 'Audience';
$string['marketing_messages_field_aicon'] = 'Icon';
$string['marketing_messages_field_dismissible'] = 'Dismissible';
$string['marketing_messages_field_times'] = 'View Times';
$string['marketing_messages_field_date_from'] = 'From';
$string['marketing_messages_field_date_to'] = 'To';
$string['marketing_messages_field_actions'] = 'Actions';
$string['marketing_messages_edit_label'] = 'Edit';
$string['marketing_messages_delete_label'] = 'Delete';
$string['marketing_messages_restore_label'] = 'Restore';
$string['marketing_messages_table_empty'] = 'No announcements to show!';
$string['marketing_messages_cell_yes'] = 'Yes';
$string['marketing_messages_cell_no'] = 'No';

$string['marketing_messages_restore_table_warning'] = '<strong>Warning!</strong> Deleting announcements from this table will permanently delete it from the database. It is recommended to use the auto-delete features of the plugin...';

// Manage Marketing Messages Lang Strings.
$string['marketing_messages_table_title'] = 'Manage announcements';
$string['marketing_messages_table_title_short'] = 'Manage';
$string['marketing_messages_table_heading'] = 'Marketing messages';

$string['marketing_messages_restore_table_title'] = 'Restore announcements';
$string['marketing_messages_restore_table_title_short'] = 'Restore announcements';
$string['marketing_messages_restore_table_heading'] = 'Marketing messages restore';

// New Announcement Lang Strings.
$string['marketing_messages_enabled'] = 'Enabled?';
$string['marketing_messages_global'] = 'Global announcement?';
$string['add_announcement_global_notice'] = 'This announcement will be displayed globally/site-wide!';
$string['add_notif_local_notice'] = 'This announcement will <em>only</em> be displayed on the page you\'re managing this block from!';
$string['marketing_messages_title'] = 'Title';
$string['marketing_messages_message'] = 'Message';
$string['marketing_messages_limits'] = 'Character limits (Title = <b>{$a->title}</b>, Message = <b>{$a->message}</b>). Only the characters within the limit will be saved!';
$string['marketing_messages_type'] = 'Type';
$string['marketing_messages_times'] = '# of times';
$string['marketing_messages_times_label'] = 'Number of times to display the announcement to a user (0 = forever)';
$string['marketing_messages_audience'] = 'Target audience';
$string['marketing_messages_audience_label'] = 'The audience you want to target with this message';
$string['marketing_messages_aicon'] = 'Icon?';
$string['marketing_messages_dismissible'] = 'Dismissible?';
$string['marketing_messages_date_from'] = 'From:';
$string['marketing_messages_date_to'] = 'To:';
$string['marketing_messages_date_info'] = 'Same date = forever';
$string['marketing_messages_save'] = 'Save';
$string['marketing_messages_update'] = 'Update';
$string['marketing_messages_cancel'] = 'Cancel';
$string['marketing_messages_req'] = 'Required...';
$string['marketing_messages_preview'] = 'Preview';

// Renderer.
$string['marketing_messages_add_heading'] = 'New announcement';
$string['marketing_messages_add_option_info'] = 'Information';
$string['marketing_messages_add_option_success'] = 'Success';
$string['marketing_messages_add_option_warning'] = 'Warning';
$string['marketing_messages_add_option_danger'] = 'Danger';
$string['marketing_messages_add_option_announcement'] = 'Announcement';
$string['marketing_messages_audience_option_all'] = 'All';
$string['marketing_messages_audience_option_student'] = 'Student';
$string['marketing_messages_audience_option_staff'] = 'Staff';
$string['marketing_messages_add_saving'] = 'Saving...';
$string['marketing_messages_add_done'] = 'Done!';

// Admin Settings.
$string['setting/navigation'] = 'Navigation:';
$string['setting/navigation_desc'] = '{$a->left}{$a->right}';

$string['setting/settings'] = 'Settings:';

$string['setting/enable'] = 'Enable:';
$string['setting/enable_desc'] = 'Toggles whether all announcements are enabled/disabled<hr>';
$string['setting/enable_default'] = '';

$string['setting/html'] = 'Allow HTML:';
$string['setting/html_desc'] = 'Toggles whether basic HTML is allowed in announcements\' titles/messages';
$string['setting/html_default'] = '';

$string['setting/multilang'] = 'Multi-lang/Filter support:';
$string['setting/multilang_desc'] = 'Toggles whether the multilang filter (and others) are supported in announcements\' titles/messages.<br>Note - This is for more advanced users & HTML needs to be enabled (above).<hr>';
$string['setting/multilang_default'] = '';

$string['setting/dateformat'] = 'Date format:';
$string['setting/dateformat_desc'] = 'Dates will be shown in the chosen format.<hr>';

$string['setting/auto_delete'] = 'Auto delete:';
$string['setting/auto_delete_desc'] = 'Toggles whether an announcement that goes past the set end-date is automatically deleted - but can be restored again.<br>(Helps with housekeeping/management)';
$string['setting/auto_delete_default'] = '';

$string['setting/auto_perma_delete'] = 'Auto permanent delete:';
$string['setting/auto_perma_delete_desc'] = 'Toggles whether announcements that have been deleted for more than 30 days are automatically permanently deleted from the database.<br>(Helps with housekeeping/management)';
$string['setting/auto_perma_delete_default'] = '';

$string['setting/auto_delete_user_data'] = 'Auto delete user data:';
$string['setting/auto_delete_user_data_desc'] = 'Toggle whether user data (such as whether the user has seen/dismissed announcements that don\'t exist anymore, etc) related to advanced announcements is automatically deleted.<br>(Helps with housekeeping/management)<hr>';
$string['setting/auto_delete_user_data_default'] = '';

$string['setting/carousel'] = 'Enable carousel';
$string['setting/carousel_desc'] = 'Enable a \'carousel mode\' which means messages are displayed in a single row and circle through each active message<hr>';
$string['setting/carousel_default'] = '1';

$string['setting/interval'] = 'Message interval';
$string['setting/interval_desc'] = 'The number of seconds a message is shown before transistioning to the next message (if \'carousel mode\' is enabled';
$string['setting/interval_default'] = '2';

$string['setting/titlelimit'] = 'Message title limit';
$string['setting/titlelimit_desc'] = 'The number of characters that the title should be limited to.';
$string['setting/titlelimit_default'] = '25';

$string['setting/messagelimit'] = 'Message limit';
$string['setting/messagelimit_desc'] = 'The number of characters that the message should be limited to.';
$string['setting/messagelimit_default'] = '100';

$string['setting/audience'] = 'Target by student email';
$string['setting/audience_desc'] = 'Whether to use the \'@stu\' email to differentiate students from staff or use the mod/assign:submit capability.';

// Navigation Links.
$string['marketing_messages_nav_heading'] = 'Notifications:';
$string['marketing_messages_nav_manage'] = 'Manage';
$string['marketing_messages_nav_restore'] = 'Restore';
$string['marketing_messages_nav_settings'] = 'Settings';

// Error Messages.
$string['marketing_messages_err_forbidden'] = 'Forbidden, please login again...';
$string['marketing_messages_err_nojsedit'] = 'Editing only supported with JavaScript enabled. Re-create the desired announcement or enable JavaScript and try again.';
$string['marketing_messages_err_req'] = 'The following fields are required: {$a}';
$string['marketing_messages_err_nocapability'] = 'You don\'t have permission to do that...';

// Cron Messages.
$string['marketing_messages_task_name'] = 'Marketing messages';
$string['marketing_messages_cron_heading'] = 'Cleaning advanced announcements';
$string['marketing_messages_cron_auto_perma_delete'] = 'Permanently delete announcements that have the deleted flag for more than 30 days...';
$string['marketing_messages_cron_auto_delete'] = 'Add deleted flag to announcements that have passed their end-date...';
$string['marketing_messages_cron_auto_delete_udata'] = 'Remove user records that relates to announcements that don\'t exist anymore...';

// Misc.
$string['marketing_messages_join'] = ' & ';
$string['event_announcement_created'] = 'Marketing message created';
$string['event_announcement_deleted'] = 'Marketing message deleted';
$string['event_announcement_updated'] = 'Marketing message updated';

// Privacy API.
$string['privacy:metadata:block_marketing_messages'] = 'Information about announcements the user has been exposed to and recorded interactions.';
$string['privacy:metadata:block_marketing_messages:title'] = 'The title of the announcement.';
$string['privacy:metadata:block_marketing_messages:message'] = 'The body/message of the announcement.';
$string['privacy:metadata:block_marketing_messages:blockid'] = 'The ID of the block from which the announcement was created (if any).';
$string['privacy:metadata:block_marketing_messages:deleted'] = 'Whether the announcement has been deleted from the site (1 = deleted).';
$string['privacy:metadata:block_marketing_messages:deleted_by'] = 'The ID of the user that deleted the announcement (if any).';
$string['privacy:metadata:block_marketing_messages:created_by'] = 'The ID of the user that created the announcements (if any).';
$string['privacy:metadata:block_marketing_messagesdiss'] = 'Information about the user (as consumer)/announcement relationship.';
$string['privacy:metadata:block_marketing_messagesdiss:user_id'] = 'The ID of the user that has seen/dismissed the announcement.';
$string['privacy:metadata:block_marketing_messagesdiss:not_id'] = 'The associated announcement ID.';
$string['privacy:metadata:block_marketing_messagesdiss:dismissed'] = 'Flag of whether the announcement has been dismissed by the user or not (1 = dismissed).';
$string['privacy:metadata:block_marketing_messagesdiss:seen'] = 'A count of how many times the user has seen/been shown the announcement.';
