/* eslint no-console: ["error", { allow: ["error"] }], max-nested-callbacks: ["error", 7] */
/**
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

/**
 * @module block_marketing_messages/notif
 */
define(['jquery'], function($) {
    // JQuery is available via $.

    return {
        initialise: function() {
            // Module initialised.
            $(document).ready(function() {
                // USER DISMISSING/CLICKING ON A ANNOUNCEMENT.
                $('.block_marketing_messages').on('click', '.dismissible', function() {

                    var dismiss = $(this).attr('data-dismiss');

                    $(this).slideUp('150', function() {
                        $(this).remove();
                    });

                    // TODO - Move ajax call to Moodle's ajax/webservice call.
                    var senddata = {}; // Data Object.
                    senddata.call = 'ajax';
                    senddata.dismiss = dismiss;

                    var callpath = M.cfg.wwwroot + "/blocks/marketing_messages/pages/process.php?sesskey=" + M.cfg.sesskey;

                    // Update user preferences.
                    $.post(callpath, senddata).fail(function() {
                        console.error("No 'dismiss' response received.");
                    }).done(function() {
                        // User dismissed announcement. Do something maybe...
                        window.location.reload(true);
                    });
                });
            });
        }
    };
});