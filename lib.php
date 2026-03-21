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
 * Library callbacks for local_smartnoticespro.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_smartnoticespro\local\manager;
use local_smartnoticespro\output\notice_modal;

/**
 * Shared renderer to avoid duplication across callbacks in the same request.
 *
 * @return string
 */
function local_smartnoticespro_render_notice_modal_html(): string {
    global $PAGE, $OUTPUT, $USER;
    static $alreadyrendered = false;

    if ($alreadyrendered) {
        return '';
    }

    if (during_initial_install()) {
        return '';
    }

    if ((defined('CLI_SCRIPT') && CLI_SCRIPT) || (defined('AJAX_SCRIPT') && AJAX_SCRIPT)) {
        return '';
    }

    $location = manager::detect_current_location($PAGE);
    if ($location === null) {
        return '';
    }

    if (isloggedin() && !isguestuser()) {
        if (!has_capability('local/smartnoticespro:viewnotices', context_system::instance())) {
            return '';
        }
    }

    $notices = manager::get_active_notices_for_location($location, $PAGE);
    if (empty($notices)) {
        return '';
    }

    $userid = (isloggedin() && !isguestuser()) ? (int)$USER->id : null;
    $courseid = !empty($PAGE->course->id) ? (int)$PAGE->course->id : null;
    $pageurl = !empty($PAGE->url) ? $PAGE->url->get_path() : null;
    $modalids = [];
    $html = '';

    foreach ($notices as $notice) {
        manager::increment_metric((int)$notice->id, 'impressions');
        manager::log_notice_event((int)$notice->id, 'impression', $userid, $courseid, $pageurl);

        $modal = new notice_modal($notice);
        $context = $modal->export_for_template($OUTPUT);
        $modalids[] = $context['modalid'];
        $html .= $OUTPUT->render_from_template('local_smartnoticespro/modal', $context);
    }

    $PAGE->requires->js_call_amd('local_smartnoticespro/modal', 'init', [$modalids]);
    $alreadyrendered = true;

    return $html;
}

/**
 * Render modal markup at top of body.
 *
 * @return string
 */
function local_smartnoticespro_before_standard_top_of_body_html(): string {
    return local_smartnoticespro_render_notice_modal_html();
}

/**
 * Render modal markup before footer.
 *
 * @return string
 */
function local_smartnoticespro_before_standard_footer_html(): string {
    return local_smartnoticespro_render_notice_modal_html();
}

/**
 * Add course navigation link for teachers/managers.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 * @return void
 */
function local_smartnoticespro_extend_navigation_course(
    navigation_node $navigation,
    stdClass $course,
    context_course $context
): void {
    if (!isloggedin() || isguestuser()) {
        return;
    }

    if (!has_capability('local/smartnoticespro:managecoursenotices', $context)) {
        return;
    }

    $navigation->add(
        get_string('coursenotices', 'local_smartnoticespro'),
        new moodle_url('/local/smartnoticespro/index.php', ['courseid' => $course->id]),
        navigation_node::TYPE_CUSTOM,
        null,
        'local_smartnoticespro'
    );
}
