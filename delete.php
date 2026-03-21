<?php
// This file is part of Moodle - http://moodle.org/

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
 * Notice deletion confirmation page.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_smartnoticespro\local\manager;

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

$notice = manager::get_notice($id);
if (!$notice) {
    throw new moodle_exception('error:noticemissing', 'local_smartnoticespro');
}

if ($notice->scope === manager::SCOPE_COURSE) {
    $courseid = (int)$notice->courseid;
    $course = get_course($courseid);
    require_login($course);
    $accesscontext = context_course::instance($courseid);
    require_capability('local/smartnoticespro:managecoursenotices', $accesscontext);
    $context = context_system::instance();
} else {
    require_login();
    $courseid = 0;
    $accesscontext = context_system::instance();
    require_capability('local/smartnoticespro:manageglobalnotices', $accesscontext);
    $context = $accesscontext;
}

$returnurl = new moodle_url('/local/smartnoticespro/index.php', $courseid > 0 ? ['courseid' => $courseid] : []);
$deleteurl = new moodle_url('/local/smartnoticespro/delete.php', [
    'id' => $id,
    'courseid' => $courseid,
    'confirm' => 1,
    'sesskey' => sesskey(),
]);

if ($confirm && confirm_sesskey()) {
    manager::delete_notice($id);
    redirect($returnurl, get_string('noticedeleted', 'local_smartnoticespro'));
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/smartnoticespro/delete.php', ['id' => $id, 'courseid' => $courseid]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('deletenotice', 'local_smartnoticespro'));
$PAGE->set_heading(get_string('deletenotice', 'local_smartnoticespro'));

echo $OUTPUT->header();
echo $OUTPUT->confirm(get_string('confirmdelete', 'local_smartnoticespro'), $deleteurl, $returnurl);
echo $OUTPUT->footer();
