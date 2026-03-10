<?php
// This file is part of Moodle - http://moodle.org/

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/forms/notice_form.php');

use local_smartnoticespro\forms\notice_form;
use local_smartnoticespro\local\manager;

$id = optional_param('id', 0, PARAM_INT);
$courseidparam = optional_param('courseid', 0, PARAM_INT);

$notice = null;
if ($id > 0) {
    $notice = manager::get_notice($id);
    if (!$notice) {
        throw new moodle_exception('error:noticemissing', 'local_smartnoticespro');
    }
}

if ($notice) {
    if ($notice->scope === manager::SCOPE_COURSE) {
        $courseid = (int)$notice->courseid;
        $course = get_course($courseid);
        require_login($course);
        $accesscontext = context_course::instance($courseid);
        require_capability('local/smartnoticespro:managecoursenotices', $accesscontext);
        $context = context_system::instance();
        $canmanageglobal = has_capability('local/smartnoticespro:manageglobalnotices', context_system::instance());
    } else {
        require_login();
        $courseid = 0;
        $accesscontext = context_system::instance();
        require_capability('local/smartnoticespro:manageglobalnotices', $accesscontext);
        $context = $accesscontext;
        $canmanageglobal = true;
    }
} elseif ($courseidparam > 0) {
    $courseid = $courseidparam;
    $course = get_course($courseid);
    require_login($course);
    $accesscontext = context_course::instance($courseid);
    require_capability('local/smartnoticespro:managecoursenotices', $accesscontext);
    $context = context_system::instance();
    $canmanageglobal = has_capability('local/smartnoticespro:manageglobalnotices', context_system::instance());
} else {
    require_login();
    $courseid = 0;
    $accesscontext = context_system::instance();
    require_capability('local/smartnoticespro:manageglobalnotices', $accesscontext);
    $context = $accesscontext;
    $canmanageglobal = true;
}

$systemcontext = context_system::instance();
$returnurl = new moodle_url('/local/smartnoticespro/index.php');
if (!has_capability('local/smartnoticespro:manageglobalnotices', $systemcontext) && $courseid > 0) {
    $returnurl = new moodle_url('/local/smartnoticespro/index.php', ['courseid' => $courseid]);
}
$url = new moodle_url('/local/smartnoticespro/edit.php', ['id' => $id] + ($courseid > 0 ? ['courseid' => $courseid] : []));

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($id ? get_string('editnotice', 'local_smartnoticespro') : get_string('addnotice', 'local_smartnoticespro'));
$PAGE->set_heading(get_string('managenotices', 'local_smartnoticespro'));

$mform = new notice_form($url, [
    'id' => $id,
    'courseid' => $courseid,
    'canmanageglobal' => $canmanageglobal,
]);

if ($notice) {
    $defaults = [
        'id' => $notice->id,
        'title' => $notice->title,
        'hidetitle' => (int)$notice->hidetitle,
        'confirmenabled' => isset($notice->confirmenabled) ? (int)$notice->confirmenabled : 1,
        'active' => (int)$notice->active,
        'scope' => $notice->scope,
        'courseid' => $notice->courseid,
        'groupid' => isset($notice->groupid) ? (int)$notice->groupid : 0,
        'targetroles' => $notice->targetroles,
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
    $payload->scope = $canmanageglobal ? $data->scope : manager::SCOPE_COURSE;
    $payload->targetroles = $data->targetroles;
    $payload->courseid = $canmanageglobal ? (int)$data->courseid : $courseid;
    $payload->groupid = isset($data->groupid) ? (int)$data->groupid : 0;
    $payload->timestart = !empty($data->timestart) ? (int)$data->timestart : 0;
    $payload->timeend = !empty($data->timeend) ? (int)$data->timeend : 0;

    $rawlocations = [];
    foreach (array_keys(manager::get_location_options()) as $locationkey) {
        $rawlocations[$locationkey] = (int)($data->{'location_' . $locationkey} ?? 0);
    }

    if (!$canmanageglobal) {
        $payload->scope = manager::SCOPE_COURSE;
        $payload->courseid = $courseid;
        $payload->groupid = isset($data->groupid) ? (int)$data->groupid : 0;
        $rawlocations = [
            manager::LOCATION_LOGIN => 0,
            manager::LOCATION_FRONTPAGE => 0,
            manager::LOCATION_DASHBOARD => 0,
            manager::LOCATION_MYCOURSES => 0,
            manager::LOCATION_COURSE => 1,
        ];
        $payload->targetroles = manager::ROLE_ALL;
    } elseif ($payload->scope === manager::SCOPE_COURSE) {
        // Global flow: course-specific notices are always course-page and all-users.
        $payload->targetroles = manager::ROLE_ALL;
        $rawlocations = [
            manager::LOCATION_LOGIN => 0,
            manager::LOCATION_FRONTPAGE => 0,
            manager::LOCATION_DASHBOARD => 0,
            manager::LOCATION_MYCOURSES => 0,
            manager::LOCATION_COURSE => 1,
        ];
    } elseif ($payload->scope === manager::SCOPE_GLOBAL) {
        $payload->courseid = null;
        $payload->groupid = null;
    }

    $payload->locations = manager::normalize_locations($rawlocations);

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
