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
 * Course notice create/edit page.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/forms/notice_form.php');

use local_smartnoticespro\forms\notice_form;
use local_smartnoticespro\local\manager;

$id = optional_param('id', 0, PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);
require_login($course);
$coursecontext = context_course::instance($courseid);
require_capability('local/smartnoticespro:managecoursenotices', $coursecontext);

$notice = null;
if ($id > 0) {
    $notice = manager::get_notice($id);
    if (!$notice) {
        throw new moodle_exception('error:noticemissing', 'local_smartnoticespro');
    }
    if ($notice->scope !== manager::SCOPE_COURSE || (int)$notice->courseid !== $courseid) {
        throw new required_capability_exception($coursecontext, 'local/smartnoticespro:managecoursenotices', 'nopermissions', '');
    }
}

$returnurl = new moodle_url('/local/smartnoticespro/index.php', ['courseid' => $courseid]);
$url = new moodle_url('/local/smartnoticespro/course_edit.php', ['id' => $id, 'courseid' => $courseid]);

$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($id ? get_string('editnotice', 'local_smartnoticespro') : get_string('addnotice', 'local_smartnoticespro'));
$PAGE->set_heading(get_string('coursenotices', 'local_smartnoticespro'));

$mform = new notice_form($url, [
    'id' => $id,
    'courseid' => $courseid,
    'canmanageglobal' => false,
]);

if ($notice) {
    $defaults = [
        'id' => $notice->id,
        'title' => $notice->title,
        'hidetitle' => (int)$notice->hidetitle,
        'confirmenabled' => isset($notice->confirmenabled) ? (int)$notice->confirmenabled : 1,
        'active' => (int)$notice->active,
        'scope' => manager::SCOPE_COURSE,
        'courseid' => (int)$notice->courseid,
        'groupid' => isset($notice->groupid) ? (int)$notice->groupid : 0,
        'targetroles' => manager::ROLE_ALL,
        'timestart' => (int)$notice->timestart,
        'timeend' => (int)$notice->timeend,
        'message_editor' => [
            'text' => $notice->message,
            'format' => FORMAT_HTML,
        ],
    ] + manager::locations_to_form_data($notice->locations);

    $mform->set_data((object)$defaults);
}

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $mform->get_data()) {
    $payload = new stdClass();
    $payload->id = $id;
    $payload->title = $data->title;
    $payload->message = $data->message_editor['text'];
    $payload->hidetitle = !empty($data->hidetitle) ? 1 : 0;
    $payload->confirmenabled = !empty($data->confirmenabled) ? 1 : 0;
    $payload->active = $data->active;
    $payload->scope = manager::SCOPE_COURSE;
    $payload->targetroles = manager::ROLE_ALL;
    $payload->courseid = $courseid;
    $payload->groupid = isset($data->groupid) ? (int)$data->groupid : 0;
    $payload->timestart = !empty($data->timestart) ? (int)$data->timestart : 0;
    $payload->timeend = !empty($data->timeend) ? (int)$data->timeend : 0;
    $payload->locations = manager::normalize_locations([
        manager::LOCATION_LOGIN => 0,
        manager::LOCATION_FRONTPAGE => 0,
        manager::LOCATION_DASHBOARD => 0,
        manager::LOCATION_MYCOURSES => 0,
        manager::LOCATION_COURSE => 1,
    ]);

    if ($id > 0) {
        manager::update_notice($payload);
        redirect($returnurl, get_string('noticeupdated', 'local_smartnoticespro'));
    }

    manager::create_notice($payload);
    redirect($returnurl, get_string('noticecreated', 'local_smartnoticespro'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading($id ? get_string('editnotice', 'local_smartnoticespro') : get_string('addnotice', 'local_smartnoticespro'));
$mform->display();
echo $OUTPUT->footer();
