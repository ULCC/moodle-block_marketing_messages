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
 * Used to process user actions.
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

// Load in Moodle config.
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');

try {
    require_sesskey();
} catch (EXCEPTION $e) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(array("result" => "Failed",
        "Notification" => get_string('marketing_messages_err_forbidden', 'block_marketing_messages')));
    exit();
}

require_login();

global $USER;

header('HTTP/1.0 200 OK');

// TODO - Check if insertions/updates/deletions were successful, and return appropriate message.

// GET PARAMETERS.
// Check if ajax or other type of call.
$calltype = optional_param('call', null, PARAM_TEXT);

// Announcement details.
$enabled = optional_param('enabled', null, PARAM_TEXT);
$global = optional_param('global', null, PARAM_TEXT);
$blockinstance = optional_param('blockid', -1, PARAM_INT);
if (get_config('block_marketing_messages', 'html')) {
    $title = optional_param('title', null, PARAM_CLEANHTML);
    $message = optional_param('message', null, PARAM_CLEANHTML);
} else {
    $title = optional_param('title', null, PARAM_TEXT);
    $message = optional_param('message', null, PARAM_TEXT);
}
$type = optional_param('type', null, PARAM_TEXT);
$times = optional_param('times', null, PARAM_INT);
$audience = optional_param('audience', null, PARAM_TEXT);
$aicon = optional_param('aicon', null, PARAM_TEXT);
$dismissible = optional_param('dismissible', null, PARAM_TEXT);
$datefrom = optional_param('date_from', null, PARAM_TEXT);
$dateto = optional_param('date_to', null, PARAM_TEXT);

$dismiss = optional_param('dismiss', null, PARAM_TEXT);                 // User dismissed announcement.
$purpose = optional_param('purpose', null, PARAM_TEXT);                 // Purpose of request.
$tableaction = optional_param('tableaction', null, PARAM_TEXT);         // ID of item to action.

// Check if ajax call or not (Progressive Enhancement - yay!).
$ajax = false;

if ($calltype === 'ajax') {
    $ajax = true;
}

// GLOBAL.
// Sort out whether global or instance-based - if the global variable contains anything it is assumed to be global.
if (isset($global) && $global != "") {
    $global = 1;
} else {
    $global = 0;
}

// NEW NOTIFICATION.
// Change the checkbox values to integers for DB - another level of security.
if ($enabled == 'on' || $enabled == '1') {
    $enabled = 1;
} else {
    $enabled = 0;
}
if ($aicon == 'on' || $aicon == '1') {
    $aicon = 1;
} else {
    $aicon = 0;
}
if ($dismissible == 'on' || $dismissible == '1') {
    $dismissible = 1;
} else {
    $dismissible = 0;
}

// TODO: Check if successful?
// Convert dates to epoch for DB. If empty, set to 0 (forever) by default.
$datefrom == "" ? $datefrom = 0 : $datefrom = strtotime($datefrom);
$dateto == "" ? $dateto = 0 : $dateto = strtotime($dateto);

if (isset($dismiss) && $dismiss != '') {
    $announcement = $DB->get_record('block_marketing_messages',
        array('id' => $dismiss)
    );
    $userdissed = $DB->get_record('block_marketing_messagesdiss',
        array('user_id' => $USER->id, 'not_id' => $dismiss)
    );

    // Update if the user has dismissed the announcement.
    if ($userdissed) {
        $DB->set_field('block_marketing_messagesdiss', 'dismissed', 1, array('id' => $userdissed->id));
    }

    if ($ajax) {
        echo json_encode("Di: Successful");
        exit();
    } else {
        exit();
    }
}

$context = context_system::instance();
$allnotifs = has_capability('block/marketing_messages:manageannouncements', $context);
$ownnotifs = false;

// TODO - move to using Moodle's core ajax string retrieval method.
// The 'strings' purpose/action is just getting strings.
if (isset($purpose) && $purpose !== 'strings') {
    if (!$allnotifs) {
        $bcontext = context_block::instance($blockinstance);
        $ownnotifs = has_capability('block/marketing_messages:manageownannouncements', $bcontext);
    }

    if (!$allnotifs && !$ownnotifs) {
        throw new moodle_exception('marketing_messages_err_nocapability', 'block_marketing_messages');
    }
}

// Build redirect url params.
$params = [];
if (isset($blockinstance) && $blockinstance > -1) {
    $params['blockid'] = $blockinstance;
}

// Handle Delete/Edit early as it requires few resources, and then we can quickly exit(),
// this is the new AJAX/JS deletion/editing method.
if (isset($tableaction) && $tableaction != '') {
    if ($purpose == 'edit') {
        $eannouncement = $DB->get_record('block_marketing_messages', array('id' => $tableaction));

        $eannouncement->date_from = date('Y-m-d', $eannouncement->date_from);
        $eannouncement->date_to = date('Y-m-d', $eannouncement->date_to);

        if ($ajax) {
            echo json_encode($eannouncement);
            exit();
        } else {
            redirect(new moodle_url('/blocks/marketing_messages/pages/announcements.php', $params),
                get_string('marketing_messages_err_nojsedit', 'block_marketing_messages'));
        }
    } else if ($purpose == 'delete') {
        $dannouncement = new stdClass();
        $dannouncement->id = $tableaction;
        $dannouncement->deleted = 1;
        $dannouncement->deleted_at = time();
        $dannouncement->deleted_by = $USER->id;

        $old = $DB->get_record('block_marketing_messages', ['id' => $tableaction]);
        $DB->update_record('block_marketing_messages', $dannouncement);

        $params = [
            'objectid' => $dannouncement->id,
            'other' => [
                'old_title' => $old->title,
                'old_message' => $old->message,
                'old_date_from' => $old->date_from,
                'old_date_to' => $old->date_to,
            ]
        ];
        if ($blockinstance > 0) {
            $params['context'] = context_block::instance($blockinstance);
        } else {
            $params['context'] = context_system::instance();
        }
        $event = \block_marketing_messages\event\announcement_deleted::create($params);
        $event->trigger();

        if ($ajax) {
            echo json_encode(array("done" => $tableaction));
            exit();
        } else {
            redirect(new moodle_url('/blocks/marketing_messages/pages/announcements.php',  ['blockid' => $blockinstance]));
        }
    } else if ($purpose == 'restore') {
        $rannouncement = new stdClass();
        $rannouncement->id = $tableaction;
        $rannouncement->deleted = 0;
        $rannouncement->deleted_at = 0;
        $rannouncement->deleted_by = -1;

        $DB->update_record('block_marketing_messages', $rannouncement);

        if ($ajax) {
            echo json_encode(array("done" => $tableaction));
            exit();
        } else {
            redirect(new moodle_url('/blocks/marketing_messages/pages/restore.php', $params));
        }
    } else if ($purpose == 'permdelete') {
        $DB->delete_records('block_marketing_messages', array('id' => $tableaction));

        if ($ajax) {
            echo json_encode(array('done' => $tableaction));
            exit();
        } else {
            redirect(new moodle_url('/blocks/marketing_messages/pages/restore.php', $params));
        }
    }
}

// Get plugin strings so JS can use appropriate locale strings.
if ($purpose == 'strings') {
    if ($ajax) {
        $strings = new stdClass();

        $strings->save = get_string('marketing_messages_save', 'block_marketing_messages');
        $strings->update = get_string('marketing_messages_update', 'block_marketing_messages');
        $strings->req = get_string('marketing_messages_req', 'block_marketing_messages');
        $strings->preview = get_string('marketing_messages_preview', 'block_marketing_messages');
        $strings->title = get_string('marketing_messages_title', 'block_marketing_messages');
        $strings->message = get_string('marketing_messages_message', 'block_marketing_messages');

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($strings);
        exit();
    }
    // Else do nothing... No JS, no JS strings needed...
}
// Get the character limits:
$titlelimit = get_config('block_marketing_messages', 'titlelimit') ?: 999999;
$messagelimit = get_config('block_marketing_messages', 'messagelimit') ?: 999999;
if (strlen($title) > $titlelimit) {
    $title = substr($title, 0, $titlelimit);
}
if (strlen($message) > $messagelimit) {
    $message = substr($message, 0, $messagelimit);
}
// Update existing announcement, instead of inserting a new one.
if ($purpose == 'update') {
    // Only check for id parameter when updating.
    $id = optional_param('id', null, PARAM_INT);

    // Prevent users from making announcements global if they aren't allowed to.
    if (!$allnotifs) {
        $global = 0;
    }

    $old = $DB->get_record('block_marketing_messages', ['id' => $id]);
    // Update an existing announcement.
    $urow = new stdClass();

    $urow->id = $id;
    $urow->title = $title;
    $urow->message = $message;
    $urow->type = $type;
    $urow->aicon = $aicon;
    $urow->enabled = $enabled;
    $urow->global = $global;
    $urow->audience = $audience;
    $urow->blockid = $blockinstance;
    $urow->dismissible = $dismissible;
    $urow->date_from = $datefrom;
    $urow->date_to = $dateto;
    $urow->times = $times;

    $DB->update_record('block_marketing_messages', $urow);

    $params = [
        'context' => context_block::instance($blockinstance),
        'objectid' => $urow->id,
        'other' => [
           'old_title' => $old->title,
           'old_message' => $old->message,
           'old_date_from' => $old->date_from,
           'old_date_to' => $old->date_to,
           'new_title' => $urow->title,
           'new_message' => $urow->message,
           'new_date_from' => $urow->date_from,
           'new_date_to' => $urow->date_to
        ]
    ];
    $event = \block_marketing_messages\event\announcement_updated::create($params);
    $event->trigger();

    if ($ajax) {
        echo json_encode(array("updated" => $title));
        exit();
    } else {
        redirect(new moodle_url('/blocks/marketing_messages/pages/announcements.php',  ['blockid' => $blockinstance]),
            get_string('marketing_messages_err_nojsedit', 'block_marketing_messages'));
    }
}

if ($purpose == "add") {
    // Check for required fields.
    $error = '';
    $fields = [];

    if (!isset($type)) {
        $fields[] = 'type';
        $error .= '"' . get_string('marketing_messages_type', 'block_marketing_messages') . '"';
    }
    if (!isset($times)) {
        $fields[] = 'times';

        if ($error !== '') {
            $error .= get_string('marketing_messages_join', 'block_marketing_messages');
        }

        $error .= '"' . get_string('marketing_messages_times', 'block_marketing_messages') . '"';
    }
    if ($error !== '') {
        if ($ajax) {
            // Return Error.
            // Technically we should never reach this if JS is enabled client-side,
            // but leaving it in case validation slipped past JS.
            header('HTTP/1.1 400 Bad Request Invalid Input');
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(array('error' => $fields));
            exit();
        } else {
            // Redirect with Error.
            redirect(new moodle_url('/blocks/marketing_messages/pages/announcements.php',  ['blockid' => $blockinstance]),
                get_string('marketing_messages_err_req', 'block_marketing_messages', $error));
        }
    }

    // Prevent users from making announcements global if they aren't allowed to.
    if (!$allnotifs) {
        $global = 0;
    }

    // Create a new announcement - Used for both Ajax Calls & NON-JS calls.
    $row = new stdClass();

    $row->title = $title;
    $row->message = $message;
    $row->type = $type;
    $row->aicon = $aicon;
    $row->enabled = $enabled;
    $row->global = $global;
    $row->audience = $audience;
    $row->blockid = $blockinstance;
    $row->dismissible = $dismissible;
    $row->date_from = $datefrom;
    $row->date_to = $dateto;
    $row->times = $times;
    $row->deleted = 0;
    $row->deleted_at = 0;
    $row->deleted_by = -1;
    $row->created_by = $USER->id;

    $id = $DB->insert_record('block_marketing_messages', $row);

    $params = ['objectid' => $id];
    if ($blockinstance > 0) {
        $params['context'] = context_block::instance($blockinstance);
    } else {
        $params['context'] = context_system::instance();
    }
    $event = \block_marketing_messages\event\announcement_created::create($params);
    $event->trigger();

    // Send JSON response if AJAX call was made, otherwise simply redirect to origin page.
    if ($ajax) {
        // Return Successful.
        echo json_encode("I: Successful");
        exit();
    } else {
        
        redirect(new moodle_url('/blocks/marketing_messages/pages/announcements.php', ['blockid' => $blockinstance]));
    }
}
