<?php
// This file is part of Moodle - http://moodle.org/

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
