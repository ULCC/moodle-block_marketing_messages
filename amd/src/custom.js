/* eslint no-console: ["error", { allow: ["error"] }], max-nested-callbacks: ["error", 7] */
/**
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

/**
 * @module block_marketing_messages/custom
 */
define(['jquery'], function($) {
    // JQuery is available via $.

    return {
        initialise: function() {
            // Module initialised.
            $(document).ready(function() {
                // Commonly (multiple times) used elements.
                var mainregion = $('#region-main');
                var addregion = $('#add_announcement_wrapper_id');
                var strings = {
                    save: 'Save',
                    update: 'Update',
                    req: 'Required...',
                    preview: 'Preview',
                    title: 'Title',
                    message: 'Message'
                };

                // MANAGING ANNOUNEMENTS.
                mainregion.on('click', '.announcements_table tr > td > form > button[type=submit]', function(e) {
                    e.preventDefault();
                    var senddata = {}; // Data Object.
                    senddata.call = 'ajax';
                    senddata.purpose = '';
                    senddata.tableaction = '';
                    senddata.blockid = '';

                    // Check if user wants to edit/delete.
                    var eattr = $(this).closest('form').attr('data-edit');
                    var dattr = $(this).closest('form').attr('data-delete');
                    senddata.blockid = $(this).closest('form').find('[name=blockid]')[0].value;
                    refreshRequired();

                    // Check if anchor element has attribute, retrieved from above.
                    if (typeof eattr !== typeof undefined && eattr !== false) {
                        senddata.purpose = 'edit';
                        senddata.tableaction = eattr;

                        var savebutton = $('#add_announcement_save');
                        savebutton.addClass('update');
                        savebutton.val(strings.update);
                    } else if (typeof dattr !== typeof undefined && dattr !== false) {
                        senddata.purpose = 'delete';
                        senddata.tableaction = dattr;
                    }

                    var callpath = M.cfg.wwwroot + "/blocks/marketing_messages/pages/process.php?sesskey=" + M.cfg.sesskey;

                    // Perform tableaction.
                    $.post(callpath, senddata).fail(function() {
                        console.error("No 'manage' response received.");
                    }).done(function(data) {
                        data = JSON.parse(data);

                        // User deleted/edited announcement.
                        if (parseInt(data.done, 10) > 0) {
                            $('#tr' + senddata.purpose + data.done).closest("tr").fadeOut(250, function() {
                                $(this).remove();
                                clearForm();
                                refreshPreview();
                            });
                        } else if (senddata.purpose === "edit") {
                            for (var i in data) {
                                if (data.hasOwnProperty(i)) {

                                    // Need this for updating.
                                    if (i === "id") {
                                        var form = $('#add_announcement_form');

                                        // Because we're doing a standard submit, we need extra inputs to pass params.
                                        // But first, remove old hidden inputs.
                                        $('#add_announcement_id').remove();
                                        form.prepend(
                                            '<input type="hidden" id="add_announcement_id" name="id" value="' + data[i] + '"/>'
                                        );

                                        $('#add_announcement_purpose').val('update');
                                    }

                                    var affectelement = $('#add_announcement_wrapper_id').find('#add_announcement_' + i);

                                    // Check whether checkboxes should be checked or not.
                                    // We also don't assign a value to checkbox input fields.
                                    if (
                                        (
                                            i === 'enabled' ||
                                            i === 'global' ||
                                            i === 'dismissible' ||
                                            i === 'aicon'
                                        ) && data[i] == 1) {
                                        affectelement.prop('checked', true);
                                    } else if (
                                        (i === 'enabled' ||
                                            i === 'global' ||
                                            i === 'dismissible' ||
                                            i === 'aicon') && data[i] == 0) {
                                        affectelement.prop('checked', false);
                                    } else {
                                        affectelement.val(data[i]);
                                    }
                                }
                            }
                            reloadPreview();
                        }
                    });
                });

                // Restore & Permanently delete announcements.
                mainregion.on('click', '.announcements_restore_table tr > td > form > button[type=submit]', function(e) {

                    e.preventDefault();
                    var senddata = {}; // Data Object.
                    senddata.call = 'ajax';
                    senddata.purpose = '';
                    senddata.tableaction = '';
                    senddata.blockid = '';

                    // Check if user wants to restore/delete.
                    var rattr = $(this).closest('form').attr('data-restore');
                    var pdattr = $(this).closest('form').attr('data-permdelete');
                    senddata.blockid = $(this).closest('form').find('[name=blockid]')[0].value;

                    // Check if anchor element has attribute, retrieved from above.
                    if (typeof rattr !== typeof undefined && rattr !== false) {
                        senddata.purpose = 'restore';
                        senddata.tableaction = rattr;
                    } else if (typeof pdattr !== typeof undefined && pdattr !== false) {
                        senddata.purpose = 'permdelete';
                        senddata.tableaction = pdattr;
                    }

                    var callpath = M.cfg.wwwroot + "/blocks/marketing_messages/pages/process.php?sesskey=" + M.cfg.sesskey;

                    // Perform tableaction.
                    $.post(callpath, senddata).fail(function() {
                        console.error("No 'restore/permdelete' response received.");
                    }).done(function(data) {
                        data = JSON.parse(data);

                        // User deleted/restored announcement.
                        // Object 'done' is returned for both restore & delete.
                        if (parseInt(data.done, 10) > 0) {
                            $('#tr' + senddata.purpose + data.done).closest("tr").fadeOut(250, function() {
                                $(this).remove();
                            });
                        }
                    });
                });

                // Clear form.
                addregion.on('click', '#add_announcement_cancel', function(e) {
                    e.preventDefault();
                    clearForm();
                });

                // Managing more announcements.
                mainregion.on('submit', '#add_announcement_form', function(e) {
                    e.preventDefault();
                    var status = $('#add_announcement_status');
                    var form = $('#add_announcement_form');

                    refreshRequired();
                    if (!checkRequired()) {
                        // Stop if required fields are not supplied.
                        return;
                    }

                    status.show();

                    var senddata = $(this).serialize(); // Data Object.

                    var callpath = M.cfg.wwwroot + "/blocks/marketing_messages/pages/process.php";

                    // Perform tableaction.
                    $.post(callpath, senddata).fail(function(data) {
                        console.error("No 'add' response received.");

                        var error = data.responseJSON.error;

                        for (var i in error) {
                            if (error.hasOwnProperty(i)) {
                                var sfield = form.find('select[name=' + error[i] + ']');
                                sfield.addClass('requiredfield');
                                $(
                                    '<strong class="requiredfield"><em>' + strings.req + '</em></strong>'
                                ).insertAfter(sfield[0].nextSibling);
                            }
                        }

                        status.hide();
                    }).done(function() {
                        // User saved announcement.
                        status.find('.saving').hide();
                        status.find('.done').show();

                        // Clear Form.
                        clearForm();

                        setTimeout(function() {
                            status.fadeOut(function() {
                                status.find('.done').hide();
                                status.find('.saving').show();
                            });
                        }, 1500);

                        $('#marketing_messages_table_wrapper').load('# #marketing_messages_table_wrapper > *');
                    });
                });

                // LIVE PREVIEW.
                // Dynamically update preview alert as user changes textbox content.
                addregion.on('input propertychange paste', '#add_announcement_title, #add_announcement_message', function() {
                    reloadPreview();
                });

                // Dynamically update preview alert type.
                $('#add_announcement_type').on('change', function() {
                    reloadPreview();
                });

                $('#add_announcement_dismissible').on('change', function() {
                    // Checking specifically whether ticked/checked or not to ensure it's displayed correctly (not toggling).
                    reloadPreview();
                });

                $('#add_announcement_aicon').on('change', function() {
                    // Checking specifically whether ticked/checked or not to ensure it's displayed correctly (not toggling).
                    reloadPreview();
                });

                // Check if preview is displaying correct (Update it).
                var reloadPreview = function() {
                    // Update title.
                    var title = addregion.find('#add_announcement_title');
                    if (title.val().length > 0) {
                        addregion.find('.preview-title')[0].innerHTML = title.val();
                    } else {
                        addregion.find('.preview-title')[0].innerHTML = strings.title;
                    }

                    // Update message.
                    var message = addregion.find('#add_announcement_message');
                    if (message.val().length > 0) {
                        addregion.find('.preview-message')[0].innerHTML = message.val();
                    } else {
                        addregion.find('.preview-message')[0].innerHTML = strings.message;
                    }

                    // Check announcement type.
                    var alerttype = $('#add_announcement_type').val();
                    var previewalert = $('#add_announcement_wrapper_id .preview-alert');

                    // Clear existing classes.
                    previewalert.removeClass('alert-info alert-success alert-danger alert-warning announcement');

                    // Special check for announcement type.
                    if (alerttype === 'announcement') {
                        previewalert.addClass(alerttype);
                        alerttype = 'info';
                    }

                    // If anything unexpected, set to info type.
                    if (alerttype !== 'info' && alerttype !== 'success' && alerttype !== 'warning' && alerttype !== 'danger') {
                        alerttype = 'info';
                    }

                    // Add type of alert class.
                    previewalert.addClass('alert-' + alerttype);

                    $('.preview-aicon').find('> img').attr('src', M.util.image_url(alerttype, 'block_marketing_messages'));

                    // Check if dismissable.
                    if (!$('#add_announcement_dismissible')[0].checked) {
                        $('.preview-alert-dismissible').hide();
                        previewalert.removeClass('dismissible');
                    } else {
                        $('.preview-alert-dismissible').show();
                        previewalert.addClass('dismissible');
                    }

                    // Check if icon should be shown.
                    if (!$('#add_announcement_aicon')[0].checked) {
                        $('.preview-aicon').hide();
                        previewalert.removeClass('aicon');
                    } else {
                        $('.preview-aicon').show();
                        previewalert.addClass('aicon');
                    }
                };

                var init = function() {
                    // Get strings.
                    var senddata = {}; // Data Object.
                    senddata.call = 'ajax';
                    senddata.purpose = 'strings';

                    var callpath = M.cfg.wwwroot + "/blocks/marketing_messages/pages/process.php?sesskey=" + M.cfg.sesskey;

                    $.post(callpath, senddata).fail(function() {
                        console.error("No 'strings' response received.");
                    }).done(function(data) {
                        // TODO: ONLY DO THIS IF AJAX SUCCESSFUL - don't render with English first?).
                        // Store strings and update preview.
                        strings = data;
                    }).always(function() {
                        // Always prepend live preview. Use langstrings if AJAX successful, otherwise use strings declared at top.
                        refreshPreview();
                    });

                    // JS is enabled, so we can use AJAX in the new announcement form.
                    $('#add_announcement_form').append(
                        '<input type="hidden" id="add_announcement_call" name="call" value="ajax"/>'
                    );
                };

                // Shiny new and fresh preview.
                var refreshPreview = function() {
                    var previewelem = $('#announcement_preview_wrapper');
                    var previewdom =
                        '<div id="announcement_preview_wrapper">' +
                            '<strong>' + strings.preview + '</strong><br>' +
                            '<div class="alert alert-info preview-alert">' +
                                '<div class="preview-aicon" style="display: none;">' +
                                    '<img src="' + M.util.image_url('info', 'block_marketing_messages') + '" />' +
                                '</div>' +
                                '<strong class="preview-title">' + strings.title + '</strong> ' +
                                '<div class="preview-message">' + strings.message + '</div> ' +
                                '<div class="preview-alert-dismissible" style="display: none;"><strong>&times;</strong></div>' +
                            '</div>' +
                        '</div>';

                    // If it exists already, remove before adding again.
                    if (previewelem.length > 0) {
                        previewelem.remove();
                        // Don't slide in.
                        $(previewdom).prependTo($(addregion));
                    } else {
                        // Slide in.
                        $(previewdom).prependTo($(addregion)).hide().slideDown();
                    }
                };

                var checkRequired = function() {
                    var disselopt = $('#add_announcement_form select option:selected:disabled');

                    for (var opt in disselopt) {
                        if (disselopt.hasOwnProperty(opt)) {
                            if ($(disselopt[opt]).prop('disabled')) {
                                $(disselopt[opt]).closest('select').addClass('requiredfield');
                                $('<strong class="requiredfield"><em>' + strings.req + '</em></strong>')
                                    .insertAfter($(disselopt[opt]).closest('select')[0].nextSibling);

                                return false;
                            }
                        }
                    }
                    return true;
                };

                var refreshRequired = function() {
                    $('select.requiredfield').removeClass('requiredfield');
                    $('strong.requiredfield').remove();
                };

                var clearForm = function() {
                    $('#add_announcement_form')[0].reset();
                    refreshRequired();
                    refreshPreview();

                    // Change save button back to normal.
                    var savebutton = $('#add_announcement_save');
                    savebutton.removeClass('update');
                    $('#add_announcement_id').remove();
                    $('#add_announcement_purpose').val('add');

                    savebutton.val(strings.save);
                };

                init();
            });
        }
    };
});