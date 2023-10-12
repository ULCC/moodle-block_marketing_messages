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
 * Marketing Messages renderer - what gets displayed
 *
 * @package    block_marketing_messages
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Delvon Forrester <delvon.forrester@esparanza.co.uk>
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Renders announcements.
 *
 * @copyright  2023 onwards Cosector {@link https://www.cosector.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_marketing_messages_renderer extends plugin_renderer_base
{
    /**
     * Renders announcement on page.
     *
     * @param   array $announcements Attributes about announcements to render.
     * @return  string Returns HTML to render announcement.
     */
    public function render_announcement($announcements) {
        $html = '';
        $mustachedata = [];
        // Render all the appropriate announcements.
        $interval = get_config('block_marketing_messages', 'interval') ? 
               get_config('block_marketing_messages', 'interval') * 1000 : 2000;
        $carousel = get_config('block_marketing_messages', 'carousel');
        $num = 1;
        $in = 0;
        $mustachedata['multimess'] = count($announcements) > 1 ? 1 : 0;
        foreach ($announcements as $announcement) {
            // Open announcement block.
            $html = $carousel ? '' : $html;
            $html .= '<div class="announcement-block-wrapper' . $announcement['extraclasses'] .
                '" data-dismiss="' . $announcement['notifid'] .
                '"><div class="alert alert-' . $announcement['alerttype'] . '">';

            if (!empty($announcement['aiconflag']) && $announcement['aiconflag'] == 1) {
                $html .= '<img class="announcement_aicon" src="' .
                    $this->image_url($announcement['aicon'], 'block_marketing_messages') . '"/>';
            }
            if (!empty($announcement['title'])) {
                $html .= '<strong>' . $announcement['title'] . '</strong> ';
            }
            if (!empty($announcement['message'])) {
                $html .= $announcement['message'];
            }

            // If dismissible, add close button.
            if ($announcement['dismissible'] == 1) {
                $html .= '<div class="announcement-block-close" title="dismiss"><strong>&times;</strong></div>';
            }

            // Close announcement block.
            $html .= '</div></div>';
            $mustachedata['announcements'][] = [
                'carouseltext' => $html,
                'activ' => $num == 1 ? 'active' : '',
                'interval' => $interval
            ];
            $mustachedata['indicators'][] = [
                'indi' => $in,
                'activ' => $in == 0 ? 'active' : ''
            ];
            $in++;
            $num++;
        }
        return $carousel ? $this->render_from_template(
                'block_marketing_messages/carousel', $mustachedata) : $html;
    }

    /**
     * Render interface to add a announcement.
     *
     * @param   array $params - passes information such whether announcement is new or the block's instance id.
     * @return  string - returns HTML to render (add announcement form HTML).
     * @throws  coding_exception
     */
    public function add_announcement($params) {
        global $CFG;

        $html = '';
        $limits = new stdClass;
        $limits->title = get_config('block_marketing_messages', 'titlelimit') ?: 'None';
        $limits->message = get_config('block_marketing_messages', 'messagelimit') ?: 'None';
        // New Announcement Form.
        $html .= '<div id="add_announcement_wrapper_id" class="add_announcement_wrapper">
                    <div class="add_announcement_header"><h2>' .
                        get_string('marketing_messages_add_heading', 'block_marketing_messages') .
                        '</h2>
                    </div>
                    <div class="add_announcement_form_wrapper">
                        <form id="add_announcement_form" action="' . $CFG->wwwroot .
                            '/blocks/marketing_messages/pages/process.php" method="POST">
                            <div class="form-check">
                                <input type="checkbox" id="add_announcement_enabled" class="form-check-input" name="enabled"/>
                                <label for="add_announcement_enabled" class="form-check-label">' .
                                    get_string('marketing_messages_enabled', 'block_marketing_messages') .
                                '</label>
                            </div>' .
                            ((array_key_exists('blockid', $params) &&
                                array_key_exists('global', $params) &&
                                $params['global'] === true) ?
                            '<div class="form-check">' .
                                /*<input type="hidden" id="add_announcement_global" class="form-check-input" name="global" value="0"/>.
                                <label for="add_announcement_global" class="form-check-label"> .
                                    get_string('marketing_messages_global', 'block_marketing_messages') .
                                </label> .*/
                               '<input type="hidden" id="add_announcement_blockid" name="blockid" value="' . $params['blockid'] .
                                    '"/>
                            </div>' :
                                ((array_key_exists('global', $params) &&
                                    $params['global'] === true) ?
                                    '<div class="form-group">
                                        <strong>
                                            <em>' . get_string('add_announcement_global_notice', 'block_marketing_messages') . '</em>
                                        </strong>
                                        <input type="hidden" id="add_announcement_global" name="global" value="1"/>
                                    </div>' :
                                    '<div class="form-group">
                                        <strong>' . get_string('add_notif_local_notice', 'block_marketing_messages') . '</strong>
                                        <input type="hidden" id="add_announcement_global" name="global" value="0"/>
                                    </div>')) .
                            '<div class="form-group row add_announcement_title">' .
                                    get_string('marketing_messages_limits', 'block_marketing_messages', $limits) .
                                '<input type="text" id="add_announcement_title" class="form-control" name="title" placeholder="' .
                                    get_string('marketing_messages_title', 'block_marketing_messages') . '"/>
                                <textarea id="add_announcement_message" class="form-control" name="message" placeholder="' .
                                    get_string('marketing_messages_message', 'block_marketing_messages') . '"></textarea>
                            </div>
                            <div class="form-group row">
                                <select id="add_announcement_type" class="form-control col-7" name="type" required>
                                    <option selected disabled>' .
                                        get_string('marketing_messages_type', 'block_marketing_messages') .
                                    '</option>
                                    <option value="info">' .
                                        get_string('marketing_messages_add_option_info', 'block_marketing_messages') .
                                    '</option>
                                    <option value="success">' .
                                        get_string('marketing_messages_add_option_success', 'block_marketing_messages') .
                                    '</option>
                                    <option value="warning">' .
                                        get_string('marketing_messages_add_option_warning', 'block_marketing_messages') .
                                    '</option>
                                    <option value="danger">' .
                                        get_string('marketing_messages_add_option_danger', 'block_marketing_messages') .
                                    '</option>' .
                                    /*<option value="announcement"> .
                                        get_string('marketing_messages_add_option_announcement', 'block_marketing_messages') .
                                    '</option>*/
                                '</select>
                                <label for="add_announcement_type" class="col">
                                    <strong class="required">*</strong>
                                </label>
                            </div>
                            <div class="form-group row">
                                <select id="add_announcement_times" class="form-control col-7" name="times" required>
                                    <option selected disabled>' .
                                        get_string('marketing_messages_times', 'block_marketing_messages') . '</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                                <label for="add_announcement_times" class="col">
                                    <strong class="required col">*</strong>
                                </label>
                                <small class="form-text text-muted">' .
                                    get_string('marketing_messages_times_label', 'block_marketing_messages') . '</small>
                            </div>
                            <div class="form-group row">
                                <select id="add_announcement_audience" class="form-control col-8" name="audience" required>
                                    <option selected disabled>' .
                                        get_string('marketing_messages_audience', 'block_marketing_messages') . '</option>
                                    <option value="all">' .
                                        get_string('marketing_messages_audience_option_all', 'block_marketing_messages') .
                                    '</option>
                                    <option value="student">' .
                                        get_string('marketing_messages_audience_option_student', 'block_marketing_messages') .
                                    '</option>
                                    <option value="staff">' .
                                        get_string('marketing_messages_audience_option_staff', 'block_marketing_messages') .
                                    '</option>
                                </select>
                                <label for="add_announcement_audience" class="col">
                                    <strong class="required col">*</strong>
                                </label>
                                <small class="form-text text-muted">' .
                                    get_string('marketing_messages_audience_label', 'block_marketing_messages') . '</small>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" id="add_announcement_aicon" class="form-check-input" name="aicon"/>
                                    <label for="add_announcement_aicon" class="form-check-label">' .
                                        get_string('marketing_messages_aicon', 'block_marketing_messages') . '</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox"
                                        id="add_announcement_dismissible"
                                        class="form-check-input"
                                        name="dismissible"/>
                                    <label for="add_announcement_dismissible" class="form-check-label">' .
                                        get_string('marketing_messages_dismissible', 'block_marketing_messages') . '</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="add_announcement_date_from" class="form-text">' .
                                    get_string('marketing_messages_date_from', 'block_marketing_messages') . '</label>
                                <input type="datetime-local"
                                    id="add_announcement_date_from"
                                    class="form-control"
                                    name="date_from"
                                    placeholder="yyyy-mm-dd"/>
                                <label for="add_announcement_date_to" class="form-text">' .
                                    get_string('marketing_messages_date_to', 'block_marketing_messages') . '</label>
                                <input type="datetime-local"
                                    id="add_announcement_date_to"
                                    class="form-control"
                                    name="date_to"
                                    placeholder="yyyy-mm-dd"/>
                                <small class="form-text text-muted">' .
                                    get_string('marketing_messages_date_info', 'block_marketing_messages') . '</small>
                            </div>
                            <input type="hidden" id="add_announcement_sesskey" name="sesskey" value="' . sesskey() . '"/>
                            <input type="hidden" id="add_announcement_purpose" name="purpose" value="add"/>
                            <input type="hidden" id="add_announcement_blockid" name="blockid" value="' .
                            (array_key_exists('blockid', $params) ?
                                $params['blockid'] :
                                '-1') . '"/>
                            <div class="form-group">
                                <input type="submit"
                                    id="add_announcement_save"
                                    class="btn btn-primary"
                                    role="button"
                                    name="save"
                                    value="' . get_string('marketing_messages_save', 'block_marketing_messages') . '"/>
                                <a href="' . $CFG->wwwroot . '/blocks/marketing_messages/pages/announcements.php"
                                    id="add_announcement_cancel" class="btn btn-danger">' .
                                get_string('marketing_messages_cancel', 'block_marketing_messages') . '</a>
                            </div>
                            <div id="add_announcement_status">
                                <div class="signal"></div>
                                <div class="saving">' .
                                    get_string('marketing_messages_add_saving', 'block_marketing_messages') .
                                '</div>
                                <div class="done" style="display: none;">' .
                                    get_string('marketing_messages_add_done', 'block_marketing_messages') .
                                '</div>
                            </div>
                        </form>
                    </div>
                </div>';

        return $html;
    }
}