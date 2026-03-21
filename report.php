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
 * Notice interaction report.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_smartnoticespro\local\manager;

$noticeid = required_param('noticeid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$export = optional_param('export', '', PARAM_ALPHA);

$notice = manager::get_notice($noticeid);
if (!$notice) {
    throw new moodle_exception('error:noticemissing', 'local_smartnoticespro');
}

if ($notice->scope === manager::SCOPE_COURSE) {
    $courseid = (int)$notice->courseid;
    $course = get_course($courseid);
    require_login($course);
    $coursecontext = context_course::instance($courseid);
    $systemcontext = context_system::instance();
    $canmanagecourse = has_capability('local/smartnoticespro:managecoursenotices', $coursecontext);
    $canmanageglobal = has_capability('local/smartnoticespro:manageglobalnotices', $systemcontext);
    if (!$canmanagecourse && !$canmanageglobal) {
        throw new required_capability_exception($coursecontext, 'local/smartnoticespro:managecoursenotices', 'nopermissions', '');
    }
    $pagecontext = context_system::instance();
} else {
    require_login();
    $courseid = 0;
    $accesscontext = context_system::instance();
    require_capability('local/smartnoticespro:manageglobalnotices', $accesscontext);
    $pagecontext = $accesscontext;
}

$returnurl = new moodle_url('/local/smartnoticespro/index.php', $courseid > 0 ? ['courseid' => $courseid] : []);
$url = new moodle_url('/local/smartnoticespro/report.php', [
    'noticeid' => $noticeid,
] + ($courseid > 0 ? ['courseid' => $courseid] : []));

if ($export === 'csv') {
    require_sesskey();

    $logs = manager::get_notice_logs($noticeid);

    $filename = clean_filename('smartnoticespro_report_' . $noticeid . '_' . date('Ymd_His') . '.csv');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');
    fputcsv($out, [
        get_string('table:id', 'local_smartnoticespro'),
        get_string('title', 'local_smartnoticespro'),
        get_string('report:user', 'local_smartnoticespro'),
        get_string('report:email', 'local_smartnoticespro'),
        get_string('report:action', 'local_smartnoticespro'),
        get_string('report:courseid', 'local_smartnoticespro'),
        get_string('report:pageurl', 'local_smartnoticespro'),
        get_string('report:date', 'local_smartnoticespro'),
    ]);

    $noticecode = 'SN-' . str_pad((string)$notice->id, 6, '0', STR_PAD_LEFT);

    foreach ($logs as $log) {
        $username = trim(fullname($log));
        if ($username === '') {
            $username = '-';
        }
        $pagelabel = manager::format_report_page_label($log->pageurl ?? null, $log->courseid ?? null);

        fputcsv($out, [
            $noticecode,
            format_string($notice->title),
            $username,
            $log->email ?? '-',
            $log->action,
            $log->courseid ?? '-',
            $pagelabel,
            userdate($log->timecreated),
        ]);
    }

    fclose($out);
    exit;
}

$perpage = 50;
$limitfrom = $page * $perpage;
$total = manager::count_notice_logs($noticeid);
$logs = manager::get_notice_logs($noticeid, $limitfrom, $perpage);

$PAGE->set_context($pagecontext);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('report:title', 'local_smartnoticespro'));
$PAGE->set_heading(get_string('report:title', 'local_smartnoticespro'));

$exporturl = new moodle_url('/local/smartnoticespro/report.php', [
    'noticeid' => $noticeid,
    'export' => 'csv',
    'sesskey' => sesskey(),
] + ($courseid > 0 ? ['courseid' => $courseid] : []));

$backicon = $OUTPUT->pix_icon('t/left', '', 'core');
$backbutton = html_writer::link(
    $returnurl,
    $backicon . html_writer::span(get_string('back', 'moodle'), 'smartnotices-addlabel'),
    ['class' => 'btn btn-secondary']
);
$exporticon = $OUTPUT->pix_icon('t/download', '', 'core');
$exportbutton = html_writer::link(
    $exporturl,
    $exporticon . html_writer::span(get_string('report:exportcsv', 'local_smartnoticespro'), 'smartnotices-addlabel'),
    ['class' => 'btn btn-primary']
);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('report:heading', 'local_smartnoticespro', format_string($notice->title)));
echo html_writer::div($exportbutton . $backbutton, 'mb-3 smartnotices-toolbar');

if (empty($logs)) {
    echo $OUTPUT->notification(get_string('report:nodata', 'local_smartnoticespro'), 'info');
    echo $OUTPUT->footer();
    exit;
}

$pagingbar = new paging_bar($total, $page, $perpage, $url);
echo $OUTPUT->render($pagingbar);

$table = new html_table();
$table->attributes['class'] = 'generaltable table table-striped table-hover smartnotices-table';
$table->head = [
    get_string('report:user', 'local_smartnoticespro'),
    get_string('report:email', 'local_smartnoticespro'),
    get_string('report:action', 'local_smartnoticespro'),
    get_string('report:courseid', 'local_smartnoticespro'),
    get_string('report:pageurl', 'local_smartnoticespro'),
    get_string('report:date', 'local_smartnoticespro'),
];

foreach ($logs as $log) {
    $username = trim(fullname($log));
    if ($username === '') {
        $username = '-';
    }
    $pagelabel = manager::format_report_page_label($log->pageurl ?? null, $log->courseid ?? null);

    $table->data[] = [
        s($username),
        s($log->email ?? '-'),
        s($log->action),
        !empty($log->courseid) ? (int)$log->courseid : '-',
        s($pagelabel),
        userdate($log->timecreated),
    ];
}

echo html_writer::table($table);
echo $OUTPUT->render($pagingbar);

echo $OUTPUT->footer();
